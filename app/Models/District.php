<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['city_id', 'name'])]
class District extends Model
{
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}
