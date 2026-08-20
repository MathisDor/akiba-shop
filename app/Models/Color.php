<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'hex'])]
class Color extends Model
{
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }
}
