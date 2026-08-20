<?php

namespace App\Livewire;

use App\Models\Coupon;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Cart extends Component
{
    public string $couponCode = '';

    public ?string $couponError = null;

    public function mount(): void
    {
        $this->couponCode = session('cart.coupon_code', '');
    }

    public function updateQuantity(int $itemId, int $quantity): void
    {
        $cart = app(CartService::class)->current();
        $item = $cart->items()->where('id', $itemId)->first();

        if (! $item) {
            return;
        }

        if ($quantity < 1) {
            $item->delete();

            return;
        }

        $item->update(['quantity' => min($quantity, $item->variant->stock)]);
    }

    public function removeItem(int $itemId): void
    {
        app(CartService::class)->current()->items()->where('id', $itemId)->delete();
    }

    public function applyCoupon(): void
    {
        $this->couponError = null;
        $code = strtoupper(trim($this->couponCode));

        if ($code === '') {
            session()->forget('cart.coupon_code');

            return;
        }

        $coupon = Coupon::where('code', $code)->first();
        $subtotal = app(CartService::class)->current()->subtotal();

        if (! $coupon || ! $coupon->isValidFor($subtotal)) {
            $this->couponError = 'Code promo invalide ou expiré.';
            session()->forget('cart.coupon_code');

            return;
        }

        session()->put('cart.coupon_code', $code);
    }

    public function checkout()
    {
        if (! Auth::guard('web')->check()) {
            return redirect()->route('login')->with('status', 'Connectez-vous pour finaliser votre commande.');
        }

        return redirect()->route('checkout.informations');
    }

    public function render()
    {
        $cart = app(CartService::class)->current();
        $cart->load('items.variant.product.images', 'items.variant.product.promotions', 'items.variant.color', 'items.variant.size');

        $subtotal = $cart->subtotal();
        $coupon = session('cart.coupon_code') ? Coupon::where('code', session('cart.coupon_code'))->first() : null;
        $discount = ($coupon && $coupon->isValidFor($subtotal)) ? $coupon->discountFor($subtotal) : 0;

        $cartProducts = $cart->items->pluck('variant.product');
        $recommended = Product::recommended(
            excludeIds: $cartProducts->pluck('id'),
            categoryIds: $cartProducts->pluck('category_id'),
        );

        return view('livewire.cart', [
            'cart' => $cart,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => max(0, $subtotal - $discount),
            'recommended' => $recommended,
        ])->layout('layouts.app', ['active' => 'cart', 'title' => 'Mon Panier — AkibaShop']);
    }
}
