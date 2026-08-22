<?php

namespace App\Http\Controllers\Webhooks;

use App\Actions\UpdateOrderStatusAction;
use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * E-Billing calls this endpoint asynchronously once the operator confirms
 * (or the customer's USSD prompt times out) a mobile money payment. See
 * App\Services\Payments\EbillingPaymentGateway for the initiating side.
 *
 * Must always return HTTP 2xx once the notification is processed, and 4xx
 * on any validation failure — E-Billing uses the code to mark the callback
 * "Processed" and to decide whether to retry.
 */
class EbillingCallbackController extends Controller
{
    public function __invoke(Request $request, UpdateOrderStatusAction $updateOrderStatus): JsonResponse
    {
        $reference = $request->input('reference');
        $amount = $request->input('amount');

        if (! $reference) {
            return response()->json(['error' => 'missing reference'], 400);
        }

        $order = Order::where('order_number', $reference)->first();

        if (! $order) {
            return response()->json(['error' => 'order not found'], 404);
        }

        if ($amount !== null && (int) $amount !== (int) $order->total) {
            return response()->json(['error' => 'amount mismatch'], 400);
        }

        $payment = $order->payments()->latest()->first();

        if ($payment) {
            $payment->update([
                'status' => 'paid',
                'reference' => $request->input('billingid') ?? $payment->reference,
                'raw_payload' => [
                    ...$payment->raw_payload,
                    'callback' => $request->only(['transactionid', 'paymentsystem', 'amount', 'billingid']),
                    'confirmed_at' => now()->toIso8601String(),
                ],
            ]);
        }

        if ($order->status !== OrderStatus::Payee->value) {
            $updateOrderStatus->execute($order, OrderStatus::Payee, 'Paiement confirmé via callback E-Billing.');
        }

        return response()->json(['success' => true], 200);
    }
}
