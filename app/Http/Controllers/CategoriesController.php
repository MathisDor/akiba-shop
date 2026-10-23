<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\ListingCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoriesController extends Controller
{
    public function index(): View
    {
        $categories = ListingCategory::active()->roots()->with('children')->get();

        $counts = Listing::visible()
            ->selectRaw('listing_category_id, COUNT(*) as total')
            ->groupBy('listing_category_id')
            ->pluck('total', 'listing_category_id');

        return view('categories.index', compact('categories', 'counts'));
    }

    public function show(Request $request, ListingCategory $category): View
    {
        abort_unless($category->is_active, 404);

        return app(ListingController::class)->browse($request, $category);
    }
}
