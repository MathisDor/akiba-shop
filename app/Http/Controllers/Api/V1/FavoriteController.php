<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FavoriteController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $products = $request->user()->favoriteProducts()->with(['images', 'variants', 'promotions'])->get();

        return ProductResource::collection($products);
    }

    public function toggle(Request $request, Product $product): JsonResponse
    {
        $user = $request->user();
        $isFavorite = $user->favoriteProducts()->where('product_id', $product->id)->exists();

        if ($isFavorite) {
            $user->favoriteProducts()->detach($product->id);
        } else {
            $user->favoriteProducts()->attach($product->id);
        }

        return response()->json(['is_favorite' => ! $isFavorite]);
    }
}
