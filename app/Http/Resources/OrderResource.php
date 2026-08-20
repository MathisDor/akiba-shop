<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'status' => $this->status,
            'status_label' => $this->statusLabel(),
            'subtotal' => $this->subtotal,
            'discount' => $this->discount,
            'delivery_fee' => $this->delivery_fee,
            'total' => $this->total,
            'delivery_method' => $this->delivery_method,
            'payment_method' => $this->payment_method,
            'created_at' => $this->created_at->toIso8601String(),
            'items' => $this->whenLoaded('items', fn () => $this->items->map(fn ($item) => [
                'product_name' => $item->product_name,
                'color_name' => $item->color_name,
                'size_name' => $item->size_name,
                'unit_price' => $item->unit_price,
                'quantity' => $item->quantity,
                'line_total' => $item->lineTotal(),
            ])),
            'status_history' => $this->whenLoaded('statusHistories', fn () => $this->statusHistories->map(fn ($h) => [
                'status' => $h->status,
                'label' => \App\Enums\OrderStatus::from($h->status)->label(),
                'note' => $h->note,
                'created_at' => $h->created_at->toIso8601String(),
            ])),
        ];
    }
}
