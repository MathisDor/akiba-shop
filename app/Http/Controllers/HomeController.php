<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\ListingCategory;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $baseQuery = fn () => Listing::visible()->with(['images', 'user', 'favoritedBy']);

        $featured = $baseQuery()->where('boosted_until', '>', now())->inRandomOrder()->take(8)->get();
        $latest = $baseQuery()->latest('published_at')->take(16)->get();

        $categories = ListingCategory::active()->roots()->get();
        $listingCount = Listing::visible()->count();

        return view('home', compact('featured', 'latest', 'categories', 'listingCount'));
    }
}
