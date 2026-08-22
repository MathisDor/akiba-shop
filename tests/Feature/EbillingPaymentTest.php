<?php

namespace Tests\Feature;

use App\Actions\AddToCartAction;
use App\Actions\PlaceOrderAction;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\Feature\Concerns\SeedsShopData;
use Tests\TestCase;

class EbillingPaymentTest extends TestCase
{
    use RefreshDatabase, SeedsShopData;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.ebilling.username' => 'test-user',
            'services.ebilling.shared_key' => 'test-key',
            'services.ebilling.base_url' => 'https://lab.billing-easy.net/api/v1/merchant/',
        ]);
    }

    private function placeAirtelOrder(User $user): Order
    {
        $variant = $this->makeProductWithVariant(basePrice: 40000, stock: 10);
        app(AddToCartAction::class)->execute($variant->id, 2, $user);

        $address = $user->addresses()->create([
            'label' => 'Domicile', 'full_name' => $user->name, 'phone' => '077000000',
            'province' => 'Estuaire', 'city' => 'Libreville', 'detailed_address' => 'Villa 1',
        ]);

        return app(PlaceOrderAction::class)->execute(
            user: $user,
            address: $address,
            deliveryMethod: 'point_relais',
            deliveryZoneId: null,
            paymentMethod: 'airtel',
            paymentPhone: '077123456',
        );
    }

    public function test_placing_an_airtel_order_creates_a_bill_and_triggers_ussd_push_and_leaves_order_pending(): void
    {
        Http::fake([
            'lab.billing-easy.net/api/v1/merchant/e_bills' => Http::response([
                'e_bill' => ['bill_id' => '5550051928', 'amount' => 80000, 'state' => 'ready'],
            ], 201),
            'lab.billing-easy.net/api/v1/merchant/e_bills/5550051928/ussd_push' => Http::response([
                'message' => 'Accepted', 'url' => null,
            ], 200),
        ]);

        $order = $this->placeAirtelOrder($this->makeClient());

        $this->assertSame(OrderStatus::EnAttentePaiement->value, $order->status);
        $this->assertSame('pending', $order->payments->first()->status);
        $this->assertSame('5550051928', $order->payments->first()->reference);

        Http::assertSent(fn ($request) => str_contains($request->url(), 'e_bills/5550051928/ussd_push')
            && $request['payment_system_name'] === 'airtelmoney'
            && $request['payer_msisdn'] === '077123456');
    }

    public function test_a_failed_bill_creation_leaves_no_order_behind(): void
    {
        Http::fake([
            'lab.billing-easy.net/api/v1/merchant/e_bills' => Http::response([
                'message' => 'Invalid payer_msisdn',
            ], 401),
        ]);

        $this->expectException(\RuntimeException::class);

        $this->placeAirtelOrder($this->makeClient());
    }

    public function test_ebilling_callback_confirms_payment_and_decrements_stock(): void
    {
        Http::fake([
            'lab.billing-easy.net/*' => Http::response(['e_bill' => ['bill_id' => '5550051928']], 201),
        ]);

        $order = $this->placeAirtelOrder($this->makeClient());

        $response = $this->postJson('/api/webhooks/ebilling/callback', [
            'reference' => $order->order_number,
            'amount' => $order->total,
            'transactionid' => 'TX123',
            'paymentsystem' => 'airtelmoney',
            'billingid' => '5550051928',
        ]);

        $response->assertOk()->assertJson(['success' => true]);

        $order->refresh();
        $this->assertSame(OrderStatus::Payee->value, $order->status);
        $this->assertSame('paid', $order->payments->first()->status);
        $this->assertSame('TX123', $order->payments->first()->raw_payload['callback']['transactionid']);
    }

    public function test_ebilling_callback_rejects_unknown_reference(): void
    {
        $this->postJson('/api/webhooks/ebilling/callback', [
            'reference' => 'DOES-NOT-EXIST',
            'amount' => 1000,
        ])->assertStatus(404);
    }

    public function test_ebilling_callback_rejects_amount_mismatch(): void
    {
        Http::fake([
            'lab.billing-easy.net/*' => Http::response(['e_bill' => ['bill_id' => '5550051928']], 201),
        ]);

        $order = $this->placeAirtelOrder($this->makeClient());

        $this->postJson('/api/webhooks/ebilling/callback', [
            'reference' => $order->order_number,
            'amount' => 1,
        ])->assertStatus(400);

        $this->assertSame(OrderStatus::EnAttentePaiement->value, $order->fresh()->status);
    }
}
