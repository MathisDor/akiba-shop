<?php

namespace App\Actions;

use App\Enums\OrderStatus;
use App\Models\Address;
use App\Models\Coupon;
use App\Models\Delivery;
use App\Models\DeliveryZone;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Services\CartService;
use App\Services\OrderNumberGenerator;
use App\Services\Payments\PaymentGatewayInterface;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PlaceOrderAction
{
    public function __construct(
        private CartService $cartService,
        private OrderNumberGenerator $orderNumbers,
        private PaymentGatewayInterface $paymentGateway,
        private UpdateOrderStatusAction $updateOrderStatus,
    ) {}

    public function execute(
        User $user,
        Address $address,
        string $deliveryMethod,
        ?int $deliveryZoneId,
        string $paymentMethod,
        ?string $couponCode = null,
        ?string $paymentPhone = null,
    ): Order {
        $cart = $this->cartService->current($user);
        $cart->load('items.variant.product');

        if ($cart->items->isEmpty()) {
            throw new RuntimeException('Votre panier est vide.');
        }

        // Server-side recalculation — the client never dictates price, stock or totals.
        $subtotal = 0;
        foreach ($cart->items as $item) {
            $variant = $item->variant()->lockForUpdate()->first();

            if (! $variant || $variant->stock < $item->quantity) {
                throw new RuntimeException("Stock insuffisant pour {$item->variant->product->name}.");
            }

            $subtotal += $variant->price * $item->quantity;
        }

        $discount = 0;
        $coupon = null;
        if ($couponCode) {
            $coupon = Coupon::where('code', strtoupper($couponCode))->first();
            if ($coupon && $coupon->isValidFor($subtotal)) {
                $discount = $coupon->discountFor($subtotal);
            } else {
                $coupon = null;
            }
        }

        $deliveryFee = 0;
        if ($deliveryMethod === 'domicile' && $deliveryZoneId) {
            $zone = DeliveryZone::where('is_active', true)->findOrFail($deliveryZoneId);
            $deliveryFee = $zone->fee;
        }

        $total = max(0, $subtotal - $discount) + $deliveryFee;

        return DB::transaction(function () use (
            $user, $address, $deliveryMethod, $deliveryZoneId, $paymentMethod, $paymentPhone,
            $cart, $subtotal, $discount, $deliveryFee, $total, $coupon
        ) {
            $order = Order::create([
                'order_number' => $this->orderNumbers->generate(),
                'user_id' => $user->id,
                'status' => OrderStatus::EnAttentePaiement->value,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'delivery_fee' => $deliveryFee,
                'total' => $total,
                'coupon_id' => $coupon?->id,
                'address_id' => $address->id,
                'address_snapshot' => $address->only([
                    'label', 'full_name', 'phone', 'province', 'city', 'commune', 'detailed_address', 'landmark',
                ]),
                'delivery_method' => $deliveryMethod,
                'payment_method' => $paymentMethod,
            ]);

            foreach ($cart->items as $item) {
                $order->items()->create([
                    'product_variant_id' => $item->variant->id,
                    'product_name' => $item->variant->product->name,
                    'product_sku' => $item->variant->sku,
                    'color_name' => $item->variant->color?->name,
                    'size_name' => $item->variant->size?->name,
                    'unit_price' => $item->variant->price,
                    'quantity' => $item->quantity,
                ]);
            }

            $order->statusHistories()->create([
                'status' => OrderStatus::EnAttentePaiement->value,
                'note' => 'Commande créée.',
                'created_by' => $user->id,
            ]);

            Delivery::create([
                'order_id' => $order->id,
                'delivery_zone_id' => $deliveryZoneId,
                'method' => $deliveryMethod,
            ]);

            if ($coupon) {
                $coupon->increment('used_count');
            }

            $result = $this->paymentGateway->charge($order->fresh('items.variant'), $paymentPhone);

            $paymentStatus = $result->pending ? 'pending' : ($result->success ? 'paid' : 'failed');

            Payment::create([
                'order_id' => $order->id,
                'provider' => $paymentMethod,
                'status' => $paymentStatus,
                'amount' => $total,
                'reference' => $result->reference,
                'raw_payload' => $paymentPhone ? [...$result->raw, 'phone' => $paymentPhone] : $result->raw,
            ]);

            if ($result->pending) {
                $order->statusHistories()->create([
                    'status' => OrderStatus::EnAttentePaiement->value,
                    'note' => 'Paiement mobile money initié, en attente de confirmation de l\'opérateur.',
                    'created_by' => $user->id,
                ]);
            } else {
                $this->updateOrderStatus->execute(
                    $order,
                    $result->success ? OrderStatus::Payee : OrderStatus::EchecPaiement,
                    $result->success ? 'Paiement confirmé.' : 'Échec du paiement.'
                );
            }

            $cart->items()->delete();
            session()->forget(['cart.coupon_code', 'checkout']);

            return $order->fresh(['items', 'delivery', 'payments', 'statusHistories']);
        });
    }
}
