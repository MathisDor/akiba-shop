<?php

namespace App\Livewire\Checkout;

use App\Actions\PlaceOrderAction;
use App\Models\Address;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Paiement extends Component
{
    public string $paymentMethod = 'airtel';

    public string $mobileMoneyPhone = '';

    public string $error = '';

    public bool $processing = false;

    private const MOBILE_MONEY_PREFIXES = [
        'airtel' => ['074', '076', '077'],
        'moov' => ['065', '066'],
    ];

    public function mount(): void
    {
        if (! session()->has('checkout.livraison')) {
            redirect()->route('checkout.livraison');
        }
    }

    public function updatedMobileMoneyPhone(string $value): void
    {
        $this->mobileMoneyPhone = substr(preg_replace('/\D/', '', $value), 0, 9);
    }

    public function updatedPaymentMethod(): void
    {
        $this->resetErrorBag('mobileMoneyPhone');
    }

    public function confirmAndPay(PlaceOrderAction $placeOrder)
    {
        if (array_key_exists($this->paymentMethod, self::MOBILE_MONEY_PREFIXES)) {
            $prefixes = implode('|', self::MOBILE_MONEY_PREFIXES[$this->paymentMethod]);
            $label = $this->paymentMethod === 'airtel' ? 'Airtel Money' : 'Moov Money';

            $this->validate([
                'mobileMoneyPhone' => ['required', 'regex:/^('.$prefixes.')\d{6}$/'],
            ], [
                'mobileMoneyPhone.required' => "Veuillez saisir votre numéro {$label}.",
                'mobileMoneyPhone.regex' => 'Numéro invalide. Format attendu : '.implode(', ', self::MOBILE_MONEY_PREFIXES[$this->paymentMethod]).' suivi de 6 chiffres.',
            ]);
        }

        $this->processing = true;
        $livraison = session('checkout.livraison');

        if (! $livraison) {
            return redirect()->route('checkout.livraison');
        }

        $user = Auth::guard('web')->user();
        $address = Address::where('user_id', $user->id)->findOrFail($livraison['address_id']);

        try {
            $order = $placeOrder->execute(
                user: $user,
                address: $address,
                deliveryMethod: $livraison['delivery_method'],
                deliveryZoneId: $livraison['delivery_zone_id'],
                paymentMethod: $this->paymentMethod,
                couponCode: session('cart.coupon_code'),
                paymentPhone: array_key_exists($this->paymentMethod, self::MOBILE_MONEY_PREFIXES) ? $this->mobileMoneyPhone : null,
            );
        } catch (\RuntimeException $e) {
            $this->processing = false;
            $this->error = $e->getMessage();

            return null;
        }

        return redirect()->route('checkout.confirmation', $order);
    }

    public function render()
    {
        $cart = app(CartService::class)->current();
        $cart->load('items.variant.product.images', 'items.variant.product.promotions');

        $subtotal = $cart->subtotal();
        $livraison = session('checkout.livraison', []);
        $deliveryFee = 0;

        if (($livraison['delivery_method'] ?? null) === 'domicile' && ! empty($livraison['delivery_zone_id'])) {
            $zone = \App\Models\DeliveryZone::find($livraison['delivery_zone_id']);
            $deliveryFee = $zone?->fee ?? 0;
        }

        $coupon = session('cart.coupon_code') ? \App\Models\Coupon::where('code', session('cart.coupon_code'))->first() : null;
        $discount = ($coupon && $coupon->isValidFor($subtotal)) ? $coupon->discountFor($subtotal) : 0;

        return view('livewire.checkout.paiement', [
            'cart' => $cart,
            'subtotal' => $subtotal,
            'deliveryFee' => $deliveryFee,
            'discount' => $discount,
            'total' => max(0, $subtotal - $discount) + $deliveryFee,
        ])->layout('layouts.checkout', ['title' => 'Paiement — AkibaShop', 'step' => 'paiement', 'backUrl' => route('checkout.livraison')]);
    }
}
