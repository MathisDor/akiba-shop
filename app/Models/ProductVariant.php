<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['product_id', 'color_id', 'size_id', 'sku', 'stock', 'price_override'])]
class ProductVariant extends Model
{
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class);
    }

    public function size(): BelongsTo
    {
        return $this->belongsTo(Size::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function getPriceAttribute(): int
    {
        return $this->price_override ?? $this->product->effective_price;
    }

    public function isInStock(): bool
    {
        return $this->stock > 0;
    }

    public function isLowStock(int $threshold = 5): bool
    {
        return $this->stock > 0 && $this->stock <= $threshold;
    }
}
