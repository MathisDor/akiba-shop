<?php

namespace App\Services\Payments;

use App\Models\Order;

interface PaymentGatewayInterface
{
    /**
     * Charge the order's total through the provider and return the outcome.
     * Real gateways (Airtel Money, Moov Money, card) implement this against
     * their respective SDK/API using server-side credentials from config —
     * never client-supplied keys. $payerPhone is the mobile money MSISDN
     * collected at checkout, required for USSD-push-based providers.
     */
    public function charge(Order $order, ?string $payerPhone = null): PaymentResult;
}
