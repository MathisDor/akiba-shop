<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramNotifier
{
    public function sendOrderSummary(Order $order): void
    {
        $token = config('services.telegram.bot_token');
        $chatId = config('services.telegram.chat_id');

        if (! $token || ! $chatId) {
            return;
        }

        $order->loadMissing('items', 'user');

        try {
            Http::timeout(5)->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $this->buildMessage($order),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Telegram order notification failed.', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function buildMessage(Order $order): string
    {
        $lines = [
            "🛍️ Nouvelle commande validée #{$order->order_number}",
            '',
            '👤 '.($order->user->name ?? 'Client').' — '.($order->address_snapshot['phone'] ?? $order->user->phone ?? '—'),
            '',
        ];

        foreach ($order->items as $item) {
            $variant = trim(implode(' / ', array_filter([$item->color_name, $item->size_name])));
            $lines[] = "• {$item->quantity}× {$item->product_name}".($variant ? " ({$variant})" : '')
                .' — '.number_format($item->lineTotal(), 0, ',', ' ').' FCFA';
        }

        $lines[] = '';
        $lines[] = '💰 Total : '.number_format($order->total, 0, ',', ' ').' FCFA';
        $lines[] = '💳 Paiement : '.str_replace('_', ' ', $order->payment_method);
        $lines[] = '🚚 Livraison : '.str_replace('_', ' ', $order->delivery_method);

        $city = $order->address_snapshot['city'] ?? null;
        $province = $order->address_snapshot['province'] ?? null;
        if ($city || $province) {
            $lines[] = '📍 '.trim(implode(', ', array_filter([$city, $province])));
        }

        return implode("\n", $lines);
    }
}
