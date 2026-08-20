<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function __invoke(Request $request): View
    {
        $term = trim((string) $request->query('q', ''));

        $products = collect();

        if ($term !== '') {
            $products = Product::query()
                ->with(['images', 'variants', 'colors', 'promotions', 'favoritedBy', 'category'])
                ->where('is_active', true)
                ->where(function ($query) use ($term) {
                    $query->where('name', 'like', "%{$term}%")
                        ->orWhere('sku', 'like', "%{$term}%")
                        ->orWhereHas('category', fn ($q) => $q->where('name', 'like', "%{$term}%"))
                        ->orWhereHas('variants.color', fn ($q) => $q->where('name', 'like', "%{$term}%"));
                })
                ->latest('published_at')
                ->paginate(12)
                ->withQueryString();
        }

        return view('search.index', compact('products', 'term'));
    }
}
