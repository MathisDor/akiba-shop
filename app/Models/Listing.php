<?php

namespace App\Models;

use App\Enums\ListingCondition;
use App\Enums\ListingStatus;
use App\Support\GabonPhone;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable([
    'user_id', 'listing_category_id', 'title', 'slug', 'description', 'price', 'is_negotiable',
    'condition', 'specs', 'city_id', 'district_id', 'contact_phone', 'accepts_whatsapp',
    'status', 'rejection_reason', 'boosted_until', 'published_at', 'expires_at',
])]
class Listing extends Model
{
    protected $attributes = [
        'status' => 'en_attente',
        'views' => 0,
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'is_negotiable' => 'boolean',
            'accepts_whatsapp' => 'boolean',
            'condition' => ListingCondition::class,
            'status' => ListingStatus::class,
            'specs' => 'array',
            'boosted_until' => 'datetime',
            'published_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public static function makeSlug(string $title): string
    {
        return Str::limit(Str::slug($title), 70, '').'-'.Str::lower(Str::random(6));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ListingCategory::class, 'listing_category_id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ListingImage::class)->orderBy('sort_order');
    }

    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'listing_favorites')->withTimestamps();
    }

    public function reports(): HasMany
    {
        return $this->hasMany(ListingReport::class);
    }

    /** Approved, not sold, not expired and from a non-suspended seller — what visitors may see. */
    public function scopeVisible(Builder $query): void
    {
        $query->where('status', ListingStatus::Publiee->value)
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->whereHas('user', fn ($q) => $q->where('is_active', true));
    }

    /** Boosted listings first, then most recent. */
    public function scopeBoostedFirst(Builder $query): void
    {
        $query->orderByRaw('CASE WHEN boosted_until IS NOT NULL AND boosted_until > ? THEN 0 ELSE 1 END', [now()])
            ->latest('published_at');
    }

    public function isVisible(): bool
    {
        return $this->status === ListingStatus::Publiee && ! $this->isExpired() && $this->user?->is_active;
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function isBoosted(): bool
    {
        return $this->boosted_until !== null && $this->boosted_until->isFuture();
    }

    public function isRenewable(): bool
    {
        return $this->status === ListingStatus::Publiee
            && $this->expires_at !== null
            && $this->expires_at->lte(now()->addDays(config('marketplace.renewable_within_days')));
    }

    public function formattedPrice(): string
    {
        if ($this->price === null) {
            return 'Prix à débattre';
        }

        if ($this->price === 0) {
            return 'Gratuit';
        }

        return number_format($this->price, 0, ',', ' ').' FCFA';
    }

    public function location(): string
    {
        return collect([$this->district?->name, $this->city?->name])->filter()->implode(', ');
    }

    public function coverImage(): ?ListingImage
    {
        return $this->images->first();
    }

    public function telUrl(): string
    {
        return GabonPhone::telUrl($this->contact_phone);
    }

    public function whatsappUrl(): string
    {
        $message = "Bonjour, je suis intéressé(e) par votre annonce « {$this->title} » sur AkibaShop : ".route('listings.show', $this);

        return GabonPhone::whatsappUrl($this->contact_phone, $message);
    }

    public function formattedPhone(): string
    {
        return GabonPhone::format($this->contact_phone);
    }

    /**
     * Category-specific details as label => display value, in the order the
     * category defines them; empty values are skipped.
     *
     * @return array<string, string>
     */
    public function specList(): array
    {
        $specs = $this->specs ?? [];

        return collect($this->category?->fields() ?? [])
            ->filter(fn (array $field) => filled($specs[$field['key']] ?? null))
            ->mapWithKeys(function (array $field) use ($specs) {
                $value = $specs[$field['key']];
                if ($field['type'] === 'number') {
                    $value = number_format((int) $value, 0, ',', ' ');
                }

                return [$field['label'] => trim($value.' '.($field['suffix'] ?? ''))];
            })
            ->all();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
