<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Support\Countries;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\Traits\CausesActivity;

#[Fillable(['name', 'email', 'password', 'phone', 'role_id', 'country_code', 'city', 'is_pro'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use CausesActivity, HasApiTokens, HasFactory, Notifiable;

    /**
     * PHP-side defaults for freshly instantiated models. The `is_active`
     * column also defaults to true at the DB level, but a model built via
     * `new User()`/factories never re-reads that default until fetched
     * from the database — leaving `is_active` as an unset (NULL/falsy)
     * attribute in memory otherwise, which silently fails `is_active`
     * checks (e.g. in EnsureUserIsAdmin) for admins created this session.
     */
    protected $attributes = [
        'is_super_admin' => false,
        'is_active' => true,
        'is_pro' => false,
        'is_verified' => false,
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_super_admin' => 'boolean',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'is_pro' => 'boolean',
            'is_verified' => 'boolean',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function isAdmin(): bool
    {
        return $this->role?->slug === 'admin';
    }

    public function isSuperAdmin(): bool
    {
        return $this->isAdmin() && $this->is_super_admin;
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_user');
    }

    public function hasPermission(string $slug): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->relationLoaded('permissions')
            ? $this->permissions->contains('slug', $slug)
            : $this->permissions()->where('slug', $slug)->exists();
    }

    /** "Lyon, France" — empty when the member gave no location. */
    public function location(): string
    {
        return collect([$this->city, Countries::name($this->country_code)])->filter()->implode(', ');
    }

    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class);
    }

    public function favoriteListings(): BelongsToMany
    {
        return $this->belongsToMany(Listing::class, 'listing_favorites')->withTimestamps();
    }

    public function hasFavorited(Listing $listing): bool
    {
        return $listing->relationLoaded('favoritedBy')
            ? $listing->favoritedBy->contains($this->id)
            : $this->favoriteListings()->whereKey($listing->id)->exists();
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function defaultAddress(): HasOne
    {
        return $this->hasOne(Address::class)->where('is_default', true);
    }

    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function favoriteProducts()
    {
        return $this->belongsToMany(Product::class, 'favorites');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
