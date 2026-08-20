<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'description' => $this->description,
            'gender' => $this->gender,
            'brand' => $this->brand,
            'category' => $this->whenLoaded('category', fn () => [
                'id' => $this->category->id,
                'name' => $this->category->name,
                'slug' => $this->category->slug,
            ]),
            'base_price' => $this->base_price,
            'effective_price' => $this->effective_price,
            'discount_percent' => $this->discount_percent,
            'is_active' => $this->is_active,
            'images' => $this->whenLoaded('images', fn () => $this->images->map(fn ($img) => [
                'id' => $img->id,
                'url' => Storage::disk('public')->url($img->path),
                'is_primary' => $img->is_primary,
            ])),
            'colors' => $this->whenLoaded('colors', fn () => $this->colors->map(fn ($c) => ['id' => $c->id, 'name' => $c->name, 'hex' => $c->hex])),
            'sizes' => $this->whenLoaded('sizes', fn () => $this->sizes->sortBy('sort_order')->values()->map(fn ($s) => ['id' => $s->id, 'name' => $s->name])),
            'variants' => $this->whenLoaded('variants', fn () => $this->variants->map(fn ($v) => [
                'id' => $v->id,
                'color_id' => $v->color_id,
                'size_id' => $v->size_id,
                'stock' => $v->stock,
                'price' => $v->price,
            ])),
        ];
    }
}
