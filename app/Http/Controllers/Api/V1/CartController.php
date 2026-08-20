<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\AddToCartAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    private function cartFor(Request $request): Cart
    {
        $cart = Cart::firstOrCreate(['user_id' => $request->user()->id]);
        $cart->load('items.variant.color', 'items.variant.size', 'items.variant.product.images', 'items.variant.product.promotions');

        return $cart;
    }

    public function index(Request $request): CartResource
    {
        return new CartResource($this->cartFor($request));
    }

    public function store(Request $request, AddToCartAction $action): CartResource|JsonResponse
    {
        $validated = $request->validate([
            'variant_id' => ['required', 'integer', 'exists:product_variants,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        try {
            $action->execute($validated['variant_id'], $validated['quantity'], $request->user());
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return new CartResource($this->cartFor($request));
    }

    public function update(Request $request, int $itemId): CartResource|JsonResponse
    {
        $validated = $request->validate(['quantity' => ['required', 'integer', 'min:0']]);
        $cart = $this->cartFor($request);
        $item = $cart->items()->where('id', $itemId)->first();

        if (! $item) {
            return response()->json(['message' => 'Article introuvable.'], 404);
        }

        if ($validated['quantity'] === 0) {
            $item->delete();
        } else {
            $item->update(['quantity' => min($validated['quantity'], $item->variant->stock)]);
        }

        return new CartResource($this->cartFor($request));
    }

    public function destroy(Request $request, int $itemId): CartResource
    {
        $this->cartFor($request)->items()->where('id', $itemId)->delete();

        return new CartResource($this->cartFor($request));
    }
}
