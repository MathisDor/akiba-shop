<?php

namespace App\Services\Payments;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * PAYIN integration with E-Billing (Digitech Africa) — the Gabonese mobile
 * money aggregator for Airtel Money and Moov Money. Payment is inherently
 * asynchronous: this only creates the bill and triggers a USSD push: the
 * customer must approve the prompt on their phone, and E-Billing confirms
 * the actual payment later via the /api/webhooks/ebilling/callback webhook
 * (see App\Http\Controllers\Webhooks\EbillingCallbackController).
 */
class EbillingPaymentGateway implements PaymentGatewayInterface
{
    private const PAYMENT_SYSTEMS = [
        'airtel' => 'airtelmoney',
        'moov' => 'moovmoney4',
    ];

    public function charge(Order $order, ?string $payerPhone = null): PaymentResult
    {
        $paymentSystem = self::PAYMENT_SYSTEMS[$order->payment_method] ?? null;

        if (! $paymentSystem) {
            throw new RuntimeException('Ce moyen de paiement n\'est pas encore disponible. Merci de choisir Airtel Money ou Moov Money.');
        }

        if (! $payerPhone) {
            throw new RuntimeException('Numéro de téléphone mobile money manquant.');
        }

        $bill = $this->createBill($order, $payerPhone);
        $billId = $bill['bill_id'];

        $push = $this->triggerUssdPush($billId, $payerPhone, $paymentSystem);

        return new PaymentResult(
            success: false,
            reference: $billId,
            raw: ['e_bill' => $bill, 'ussd_push' => $push],
            pending: true,
        );
    }

    private function createBill(Order $order, string $payerPhone): array
    {
        $response = $this->client()->post($this->url('e_bills'), [
            'amount' => (int) $order->total,
            'short_description' => "Commande {$order->order_number}",
            'payer_email' => $order->user->email,
            'payer_msisdn' => $payerPhone,
            'payer_name' => $order->address_snapshot['full_name'] ?? $order->user->name,
            'external_reference' => $order->order_number,
            'expiry_period' => (int) config('services.ebilling.expiry_minutes', 60),
        ]);

        if ($response->status() !== 201) {
            throw new RuntimeException($this->errorMessage($response, 'Échec de la création de la facture E-Billing.'));
        }

        $billId = $response->json('e_bill.bill_id');

        if (! $billId) {
            throw new RuntimeException('Réponse E-Billing invalide : bill_id manquant.');
        }

        return $response->json('e_bill');
    }

    private function triggerUssdPush(string $billId, string $payerPhone, string $paymentSystem): array
    {
        $response = $this->client()->post($this->url("e_bills/{$billId}/ussd_push"), [
            'payer_msisdn' => $payerPhone,
            'payment_system_name' => $paymentSystem,
        ]);

        if (! $response->successful()) {
            throw new RuntimeException($this->errorMessage($response, 'Échec de l\'envoi de la demande de paiement sur votre téléphone.'));
        }

        return $response->json() ?? [];
    }

    private function client()
    {
        return Http::withBasicAuth(
            (string) config('services.ebilling.username'),
            (string) config('services.ebilling.shared_key'),
        )->acceptJson()->timeout(20);
    }

    private function url(string $path): string
    {
        return rtrim((string) config('services.ebilling.base_url'), '/').'/'.ltrim($path, '/');
    }

    private function errorMessage($response, string $fallback): string
    {
        $message = $response->json('message') ?? $response->json('error');

        return is_string($message) && $message !== '' ? $message : $fallback;
    }
}
