<?php

namespace App\Services\Payments;

use App\Models\Order;

interface PaymentGatewayInterface
{
    /**
     * Charge the order's total through the provider and return the outcome.
     * Real gateways (Airtel Money, Moov Money, card) implement this against
     * their respective SDK/API using server-side credentials from config —
     * never client-supplied keys.
     */
    public function charge(Order $order): PaymentResult;
}
