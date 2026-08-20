<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __invoke(Product $product): View
    {
        abort_unless($product->is_active, 404);

        $product->load([
            'images',
            'variants.color',
            'variants.size',
            'category',
            'promotions',
            'favoritedBy',
            'colors',
            'sizes',
            'reviews' => fn ($q) => $q->with('user')->latest(),
        ]);

        $related = Product::query()
            ->with(['images', 'variants', 'colors', 'promotions', 'favoritedBy'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->take(4)
            ->get();

        $variantMatrix = $product->variants->map(fn ($v) => [
            'color_id' => $v->color_id,
            'size_id' => $v->size_id,
            'stock' => $v->stock,
            'variant_id' => $v->id,
        ]);

        $reviewCount = $product->reviews->count();
        $avgRating = $reviewCount > 0 ? round($product->reviews->avg('rating'), 1) : null;
        $ratingBreakdown = collect(range(5, 1))->mapWithKeys(fn ($star) => [
            $star => $reviewCount > 0 ? round($product->reviews->where('rating', $star)->count() / $reviewCount * 100) : 0,
        ]);

        return view('products.show', compact('product', 'related', 'variantMatrix', 'reviewCount', 'avgRating', 'ratingBreakdown'));
    }
}
