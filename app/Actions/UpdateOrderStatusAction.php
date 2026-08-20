<?php

namespace App\Actions;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\StockMovement;
use App\Services\TelegramNotifier;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UpdateOrderStatusAction
{
    public function __construct(private TelegramNotifier $telegram) {}

    public function execute(Order $order, OrderStatus $status, ?string $note = null): Order
    {
        $justValidated = false;

        DB::transaction(function () use ($order, $status, $note, &$justValidated) {
            $previous = $order->statusEnum();

            $order->update(['status' => $status->value]);

            $order->statusHistories()->create([
                'status' => $status->value,
                'note' => $note,
                'created_by' => Auth::guard('admin')->id() ?? Auth::guard('web')->id(),
            ]);

            if ($previous !== OrderStatus::stockDeductingTrigger() && $status === OrderStatus::stockDeductingTrigger()) {
                $this->adjustStock($order, -1, 'order');
            }

            if (! in_array($previous, OrderStatus::restockingStatuses(), true) && in_array($status, OrderStatus::restockingStatuses(), true)) {
                $this->adjustStock($order, 1, $status === OrderStatus::Annulee ? 'cancel' : 'return');
            }

            $justValidated = $previous !== OrderStatus::Payee && $status === OrderStatus::Payee;
        });

        $order = $order->fresh();

        if ($justValidated) {
            // Runs after the HTTP response is sent, so a slow/blocked outbound
            // connection to Telegram can never delay or break the checkout flow.
            dispatch(function () use ($order) {
                $this->telegram->sendOrderSummary($order);
            })->afterResponse();
        }

        return $order;
    }

    private function adjustStock(Order $order, int $direction, string $type): void
    {
        foreach ($order->items as $item) {
            if (! $item->product_variant_id) {
                continue;
            }

            $variant = $item->variant;

            if (! $variant) {
                continue;
            }

            $delta = $direction * $item->quantity;
            $variant->increment('stock', $delta);

            StockMovement::create([
                'product_variant_id' => $variant->id,
                'type' => $type,
                'quantity' => $delta,
                'reason' => "Commande {$order->order_number}",
                'created_by' => Auth::guard('admin')->id(),
            ]);
        }
    }
}
