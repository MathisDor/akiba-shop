<?php

namespace App\Services\Payments;

use App\Models\Order;
use Illuminate\Support\Str;

/**
 * Placeholder gateway used until real Airtel Money / Moov Money / card
 * integrations are wired up (Phase 2). It immediately reports success so
 * the order lifecycle (stock decrement, notifications, tracking) can be
 * built and tested end-to-end. Swap the binding in AppServiceProvider per
 * provider once real merchant credentials are available — nothing in the
 * checkout flow needs to change.
 */
class ManualPaymentGateway implements PaymentGatewayInterface
{
    public function charge(Order $order): PaymentResult
    {
        return new PaymentResult(
            success: true,
            reference: 'MANUAL-'.Str::upper(Str::random(10)),
            raw: ['simulated' => true, 'provider' => $order->payment_method],
        );
    }
}
