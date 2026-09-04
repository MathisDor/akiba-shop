<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(Request $request): View
    {
        return $this->render($request);
    }

    public function gender(Request $request, string $gender): View
    {
        return $this->render($request, $gender);
    }

    public function category(Request $request, string $gender, string $category): View
    {
        $categoryModel = Category::where('slug', $category)->where('gender', $gender)->firstOrFail();

        return $this->render($request, $gender, $categoryModel);
    }

    private function render(Request $request, ?string $gender = null, ?Category $category = null): View
    {
        $query = Product::query()
            ->with(['images', 'variants', 'colors', 'sizes', 'promotions', 'favoritedBy', 'category'])
            ->where('is_active', true);

        $gender = $gender ?? $request->query('gender');
        if ($gender && in_array($gender, ['femme', 'homme'], true)) {
            $query->where('gender', $gender);
        }

        if ($category) {
            $query->where('category_id', $category->id);
        } elseif ($request->filled('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', 'like', $request->query('category').'%'));
        }

        if ($request->filled('size')) {
            $query->whereHas('variants.size', fn ($q) => $q->where('name', $request->query('size')));
        }

        if ($request->filled('color')) {
            $query->whereHas('variants.color', fn ($q) => $q->where('name', $request->query('color')));
        }

        if ($request->filled('price_min')) {
            $query->where('base_price', '>=', (int) $request->query('price_min'));
        }
        if ($request->filled('price_max')) {
            $query->where('base_price', '<=', (int) $request->query('price_max'));
        }

        if ($request->query('availability') === 'in_stock') {
            $query->whereHas('variants', fn ($q) => $q->where('stock', '>', 0));
        } elseif ($request->query('availability') === 'out_of_stock') {
            $query->whereDoesntHave('variants', fn ($q) => $q->where('stock', '>', 0));
        }

        if ($request->boolean('promo')) {
            $query->whereHas('promotions', fn ($q) => $q->where('is_active', true)
                ->where(fn ($q2) => $q2->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
                ->where(fn ($q2) => $q2->whereNull('ends_at')->orWhere('ends_at', '>=', now())));
        }

        if ($request->filled('collection')) {
            $query->whereHas('collection', fn ($q) => $q->where('slug', $request->query('collection')));
        }

        match ($request->query('sort')) {
            'nouveautes' => $query->latest('published_at'),
            'prix_asc' => $query->orderBy('base_price'),
            'prix_desc' => $query->orderByDesc('base_price'),
            default => $query->latest('published_at'),
        };

        $products = $query->paginate(12)->withQueryString();

        $categories = Category::when($gender, fn ($q) => $q->where('gender', $gender))
            ->orderBy('name')
            ->get();

        $sizes = Size::orderBy('sort_order')->get();

        return view('catalog.index', [
            'products' => $products,
            'categories' => $categories,
            'sizes' => $sizes,
            'currentGender' => $gender,
            'currentCategory' => $category,
        ]);
    }
}
