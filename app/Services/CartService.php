<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CartService
{
    /**
     * Resolve the current cart. Pass $user explicitly for guard-agnostic
     * contexts (e.g. Sanctum-authenticated API requests); otherwise falls
     * back to the web session guard for Blade/Livewire requests.
     */
    public function current(?User $user = null): Cart
    {
        $user ??= Auth::guard('web')->user();

        if ($user) {
            return Cart::firstOrCreate(['user_id' => $user->id]);
        }

        $sessionId = session()->get('cart_session_id');

        if (! $sessionId) {
            $sessionId = (string) Str::uuid();
            session()->put('cart_session_id', $sessionId);
        }

        return Cart::firstOrCreate(['session_id' => $sessionId, 'user_id' => null]);
    }

    public function mergeGuestCartIntoUser(int $userId): void
    {
        $sessionId = session()->get('cart_session_id');

        if (! $sessionId) {
            return;
        }

        $guestCart = Cart::where('session_id', $sessionId)->whereNull('user_id')->first();

        if (! $guestCart) {
            return;
        }

        $userCart = Cart::firstOrCreate(['user_id' => $userId]);

        foreach ($guestCart->items as $item) {
            $existing = $userCart->items()->where('product_variant_id', $item->product_variant_id)->first();

            if ($existing) {
                $existing->increment('quantity', $item->quantity);
            } else {
                $item->update(['cart_id' => $userCart->id]);
            }
        }

        $guestCart->delete();
        session()->forget('cart_session_id');
    }

    public function itemCount(): int
    {
        return $this->current()->items()->sum('quantity');
    }
}
