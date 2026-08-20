<?php

namespace Tests\Feature;

use App\Actions\AddToCartAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\Feature\Concerns\SeedsShopData;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase, SeedsShopData;

    public function test_adding_to_cart_persists_the_correct_quantity_and_price(): void
    {
        $user = $this->makeClient();
        $variant = $this->makeProductWithVariant(basePrice: 15000, stock: 5);

        $this->actingAs($user);

        $cart = app(AddToCartAction::class)->execute($variant->id, 2, $user);

        $this->assertSame(1, $cart->items->count());
        $this->assertSame(2, $cart->items->first()->quantity);
        $this->assertSame(30000, $cart->subtotal());
    }

    public function test_adding_more_than_available_stock_is_rejected(): void
    {
        $user = $this->makeClient();
        $variant = $this->makeProductWithVariant(basePrice: 10000, stock: 2);

        $this->actingAs($user);

        $this->expectException(RuntimeException::class);

        app(AddToCartAction::class)->execute($variant->id, 3, $user);
    }
}
