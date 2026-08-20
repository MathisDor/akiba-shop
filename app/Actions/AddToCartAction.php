<?php

namespace App\Actions;

use App\Models\Cart;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\CartService;
use RuntimeException;

class AddToCartAction
{
    public function __construct(private CartService $cartService) {}

    public function execute(int $variantId, int $quantity = 1, ?User $user = null): Cart
    {
        $variant = ProductVariant::findOrFail($variantId);

        if ($quantity < 1) {
            throw new RuntimeException('Quantité invalide.');
        }

        $cart = $this->cartService->current($user);
        $item = $cart->items()->where('product_variant_id', $variant->id)->first();

        $desiredQuantity = $quantity + ($item?->quantity ?? 0);

        if ($desiredQuantity > $variant->stock) {
            throw new RuntimeException('Stock insuffisant pour cette variante.');
        }

        if ($item) {
            $item->update(['quantity' => $desiredQuantity]);
        } else {
            $cart->items()->create([
                'product_variant_id' => $variant->id,
                'quantity' => $quantity,
            ]);
        }

        return $cart->fresh('items.variant.product', 'items.variant.color', 'items.variant.size');
    }
}
