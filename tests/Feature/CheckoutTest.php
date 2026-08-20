<?php

namespace Tests\Feature;

use App\Actions\AddToCartAction;
use App\Actions\PlaceOrderAction;
use App\Enums\OrderStatus;
use App\Models\Promotion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\SeedsShopData;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase, SeedsShopData;

    public function test_placing_an_order_recalculates_total_server_side_and_deducts_stock(): void
    {
        $user = $this->makeClient();
        $variant = $this->makeProductWithVariant(basePrice: 50000, stock: 10);

        // Attach an active 20% promotion — the client-visible price should be ignored,
        // only the server-computed effective price must end up on the order.
        $promotion = Promotion::create([
            'name' => 'Test promo', 'type' => 'percentage', 'value' => 20,
            'starts_at' => now()->subDay(), 'ends_at' => now()->addDay(), 'is_active' => true,
        ]);
        $promotion->products()->attach($variant->product_id);

        app(AddToCartAction::class)->execute($variant->id, 2, $user);

        $address = $user->addresses()->create([
            'label' => 'Domicile', 'full_name' => $user->name, 'phone' => '077000000',
            'province' => 'Estuaire', 'city' => 'Libreville', 'detailed_address' => 'Villa 1',
        ]);

        $order = app(PlaceOrderAction::class)->execute(
            user: $user,
            address: $address,
            deliveryMethod: 'point_relais',
            deliveryZoneId: null,
            paymentMethod: 'airtel',
        );

        // 50 000 * 0.8 = 40 000 per unit, x2 = 80 000 — never the raw base price of 100 000.
        $this->assertSame(80000, $order->subtotal);
        $this->assertSame(80000, $order->total);
        $this->assertSame(OrderStatus::Payee->value, $order->status);

        $variant->refresh();
        $this->assertSame(8, $variant->stock, 'Stock must be decremented by the ordered quantity once payment is confirmed.');
    }
}
