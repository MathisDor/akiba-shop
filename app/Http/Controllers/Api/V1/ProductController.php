<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Product::query()
            ->with(['images', 'variants', 'colors', 'sizes', 'promotions', 'category'])
            ->where('is_active', true);

        if ($request->filled('gender')) {
            $query->where('gender', $request->query('gender'));
        }

        if ($request->filled('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->query('category')));
        }

        if ($request->filled('q')) {
            $term = $request->query('q');
            $query->where('name', 'like', "%{$term}%");
        }

        $products = $query->paginate($request->integer('per_page', 12));

        return ProductResource::collection($products);
    }

    public function show(Product $product): ProductResource
    {
        abort_unless($product->is_active, 404);

        $product->load(['images', 'variants.color', 'variants.size', 'colors', 'sizes', 'promotions', 'category']);

        return new ProductResource($product);
    }
}
