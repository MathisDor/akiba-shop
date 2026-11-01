<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/** A private thread between a buyer and the seller about one listing. */
#[Fillable(['listing_id', 'buyer_id', 'seller_id', 'last_message_at'])]
class Conversation extends Model
{
    protected function casts(): array
    {
        return [
            'last_message_at' => 'datetime',
        ];
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('id');
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    public function scopeInvolving(Builder $query, User $user): void
    {
        $query->where(fn ($q) => $q->where('buyer_id', $user->id)->orWhere('seller_id', $user->id));
    }

    public function involves(User $user): bool
    {
        return in_array($user->id, [$this->buyer_id, $this->seller_id], true);
    }

    /** The participant who isn't $user. */
    public function otherParticipant(User $user): ?User
    {
        return $user->id === $this->buyer_id ? $this->seller : $this->buyer;
    }

    /** Marks the messages $user received as read. */
    public function markReadFor(User $user): void
    {
        $this->messages()->toBase()
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }
}
