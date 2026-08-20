<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Collection;
use App\Models\Product;
use App\Models\Review;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $baseQuery = fn () => Product::query()
            ->with(['images', 'variants', 'colors', 'promotions', 'favoritedBy'])
            ->where('is_active', true);

        $newArrivals = $baseQuery()->latest('published_at')->take(8)->get();
        $femme = $baseQuery()->where('gender', 'femme')->latest('published_at')->take(8)->get();
        $homme = $baseQuery()->where('gender', 'homme')->latest('published_at')->take(8)->get();

        $promotions = $baseQuery()
            ->whereHas('promotions', fn ($q) => $q->where('is_active', true)
                ->where(fn ($q2) => $q2->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
                ->where(fn ($q2) => $q2->whereNull('ends_at')->orWhere('ends_at', '>=', now())))
            ->take(8)->get();

        $trending = $baseQuery()->inRandomOrder()->take(8)->get();

        $collections = Collection::where('is_active', true)->whereHas('products')->withCount('products')->take(6)->get();

        $reviews = Review::with('user', 'product')
            ->where('is_approved', true)
            ->where('rating', '>=', 4)
            ->whereNotNull('comment')
            ->latest()
            ->take(6)
            ->get();

        return view('home', compact('newArrivals', 'femme', 'homme', 'promotions', 'trending', 'collections', 'reviews'));
    }
}
