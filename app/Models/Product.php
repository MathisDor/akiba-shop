<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

#[Fillable([
    'name', 'slug', 'sku', 'description', 'category_id', 'collection_id',
    'brand', 'material', 'fit', 'season', 'care_instructions', 'gender',
    'base_price', 'is_active', 'published_at',
])]
class Product extends Model
{
    use LogsActivity, SoftDeletes;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'published_at' => 'datetime',
            'base_price' => 'integer',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'base_price', 'is_active', 'category_id'])
            ->logOnlyDirty()
            ->useLogName('product');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function primaryImage(): HasMany
    {
        return $this->images()->where('is_primary', true);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function colors(): BelongsToMany
    {
        return $this->belongsToMany(Color::class, 'product_variants')->distinct();
    }

    public function sizes(): BelongsToMany
    {
        return $this->belongsToMany(Size::class, 'product_variants')->distinct();
    }

    public function promotions(): BelongsToMany
    {
        return $this->belongsToMany(Promotion::class, 'promotion_product');
    }

    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class)->where('is_approved', true);
    }

    public function totalStock(): int
    {
        return $this->variants->sum('stock');
    }

    public function activePromotion(): ?Promotion
    {
        $now = Carbon::now();

        return $this->promotions
            ->first(fn (Promotion $promotion) => $promotion->is_active
                && (! $promotion->starts_at || $promotion->starts_at->lte($now))
                && (! $promotion->ends_at || $promotion->ends_at->gte($now)));
    }

    public function getEffectivePriceAttribute(): int
    {
        $promotion = $this->activePromotion();

        if (! $promotion) {
            return $this->base_price;
        }

        return $promotion->type === 'percentage'
            ? (int) round($this->base_price * (100 - $promotion->value) / 100)
            : max(0, $this->base_price - $promotion->value);
    }

    public function getDiscountPercentAttribute(): ?int
    {
        if ($this->effective_price >= $this->base_price) {
            return null;
        }

        return (int) round((($this->base_price - $this->effective_price) / $this->base_price) * 100);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Active products in the same categories as $categoryIds, excluding $excludeIds,
     * topped up with the latest active products if the category match falls short of $limit.
     */
    public static function recommended(iterable $excludeIds, iterable $categoryIds, int $limit = 4): \Illuminate\Support\Collection
    {
        $excludeIds = collect($excludeIds)->filter()->values();
        $categoryIds = collect($categoryIds)->filter()->unique()->values();

        $withRelations = ['images', 'variants', 'colors', 'promotions', 'favoritedBy'];

        $recommended = static::query()
            ->with($withRelations)
            ->where('is_active', true)
            ->whereNotIn('id', $excludeIds)
            ->when($categoryIds->isNotEmpty(), fn ($q) => $q->whereIn('category_id', $categoryIds))
            ->inRandomOrder()
            ->take($limit)
            ->get();

        if ($recommended->count() < $limit) {
            $moreExcludeIds = $excludeIds->merge($recommended->pluck('id'));

            $more = static::query()
                ->with($withRelations)
                ->where('is_active', true)
                ->whereNotIn('id', $moreExcludeIds)
                ->latest('published_at')
                ->take($limit - $recommended->count())
                ->get();

            $recommended = $recommended->concat($more);
        }

        return $recommended;
    }
}
