<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

#[Fillable(['code', 'type', 'value', 'min_purchase', 'starts_at', 'ends_at', 'usage_limit', 'used_count', 'is_active'])]
class Coupon extends Model
{
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function isValidFor(int $subtotal): bool
    {
        $now = Carbon::now();

        if (! $this->is_active) {
            return false;
        }

        if ($this->starts_at && $this->starts_at->gt($now)) {
            return false;
        }

        if ($this->ends_at && $this->ends_at->lt($now)) {
            return false;
        }

        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return false;
        }

        if ($this->min_purchase && $subtotal < $this->min_purchase) {
            return false;
        }

        return true;
    }

    public function discountFor(int $subtotal): int
    {
        return $this->type === 'percentage'
            ? (int) round($subtotal * $this->value / 100)
            : min($this->value, $subtotal);
    }
}
