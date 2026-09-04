<?php

namespace Tests\Feature\Api;

use App\Actions\AddToCartAction;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Feature\Concerns\SeedsShopData;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    use RefreshDatabase, SeedsShopData;

    private function clientWithFilledCart(): array
    {
        $user = $this->makeClient();
        $variant = $this->makeProductWithVariant(basePrice: 30000, stock: 10);
        app(AddToCartAction::class)->execute($variant->id, 1, $user);

        $address = $user->addresses()->create([
            'label' => 'Domicile', 'full_name' => $user->name, 'phone' => '077000000',
            'province' => 'Estuaire', 'city' => 'Libreville', 'detailed_address' => 'Villa 1',
        ]);

        return [$user, $address];
    }

    public function test_mobile_money_checkout_requires_a_payer_phone(): void
    {
        [$user, $address] = $this->clientWithFilledCart();
        Sanctum::actingAs($user);

        $this->postJson('/api/v1/orders', [
            'address_id' => $address->id,
            'delivery_method' => 'point_relais',
            'payment_method' => 'airtel',
        ])->assertStatus(422)->assertJsonValidationErrors('payment_phone');
    }

    public function test_mobile_money_checkout_rejects_a_wrong_operator_prefix(): void
    {
        [$user, $address] = $this->clientWithFilledCart();
        Sanctum::actingAs($user);

        $this->postJson('/api/v1/orders', [
            'address_id' => $address->id,
            'delivery_method' => 'point_relais',
            'payment_method' => 'airtel',
            'payment_phone' => '065123456', // a Moov prefix, not Airtel
        ])->assertStatus(422)->assertJsonValidationErrors('payment_phone');
    }

    public function test_mobile_money_checkout_forwards_the_payer_phone_to_the_gateway(): void
    {
        [$user, $address] = $this->clientWithFilledCart();
        Sanctum::actingAs($user);

        $this->postJson('/api/v1/orders', [
            'address_id' => $address->id,
            'delivery_method' => 'point_relais',
            'payment_method' => 'airtel',
            'payment_phone' => '077123456',
        ])->assertOk();

        $payment = Payment::latest('id')->first();
        $this->assertSame('077123456', $payment->raw_payload['phone']);
    }

    public function test_card_checkout_does_not_require_a_phone(): void
    {
        [$user, $address] = $this->clientWithFilledCart();
        Sanctum::actingAs($user);

        $this->postJson('/api/v1/orders', [
            'address_id' => $address->id,
            'delivery_method' => 'point_relais',
            'payment_method' => 'card',
        ])->assertOk();
    }

    public function test_a_user_cannot_read_another_users_order(): void
    {
        [$owner, $address] = $this->clientWithFilledCart();
        Sanctum::actingAs($owner);

        $orderId = $this->postJson('/api/v1/orders', [
            'address_id' => $address->id,
            'delivery_method' => 'point_relais',
            'payment_method' => 'card',
        ])->json('data.id');

        Sanctum::actingAs($this->makeClient());

        $this->getJson("/api/v1/orders/{$orderId}")->assertStatus(403);
    }
}
