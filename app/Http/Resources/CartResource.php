<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'items' => $this->items->map(fn ($item) => [
                'id' => $item->id,
                'quantity' => $item->quantity,
                'unit_price' => $item->variant->price,
                'line_total' => $item->lineTotal(),
                'variant' => [
                    'id' => $item->variant->id,
                    'stock' => $item->variant->stock,
                    'color' => $item->variant->color?->name,
                    'size' => $item->variant->size?->name,
                ],
                'product' => [
                    'id' => $item->variant->product->id,
                    'name' => $item->variant->product->name,
                    'slug' => $item->variant->product->slug,
                    'image' => optional($item->variant->product->images->first())->path
                        ? Storage::disk('public')->url($item->variant->product->images->first()->path)
                        : null,
                ],
            ]),
            'subtotal' => $this->subtotal(),
            'total_quantity' => $this->totalQuantity(),
        ];
    }
}
