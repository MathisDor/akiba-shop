<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['parent_id', 'name', 'slug', 'icon', 'sort_order', 'is_active'])]
class ListingCategory extends Model
{
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ListingCategory::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ListingCategory::class, 'parent_id')->orderBy('sort_order')->orderBy('name');
    }

    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class);
    }

    public function scopeRoots(Builder $query): void
    {
        $query->whereNull('parent_id')->orderBy('sort_order')->orderBy('name');
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /** This category plus its sub-categories — what a listing filter on it should match. */
    public function selfAndChildrenIds(): array
    {
        return [$this->id, ...$this->children()->pluck('id')->all()];
    }

    public function rootSlug(): string
    {
        return $this->parent?->slug ?? $this->slug;
    }

    /** Category-specific listing fields (see config/marketplace.php). */
    public function fields(): array
    {
        return config('marketplace.category_fields.'.$this->rootSlug(), []);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
