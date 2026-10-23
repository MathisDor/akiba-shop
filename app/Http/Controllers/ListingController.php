<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Listing;
use App\Models\ListingCategory;
use App\Support\ListingFilters;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ListingController extends Controller
{
    public function index(Request $request): View
    {
        return $this->browse($request, (new ListingFilters($request))->category());
    }

    /** Shared by /annonces and /categories/{slug}. */
    public function browse(Request $request, ?ListingCategory $category): View
    {
        $filters = new ListingFilters($request);

        return view('listings.index', [
            'listings' => $filters->paginate($category),
            'currentCategory' => $category?->loadMissing('parent', 'children'),
            'currentCity' => $filters->city()?->load('districts'),
            'activeFilterCount' => $filters->activeCount(),
            'categories' => ListingCategory::active()->roots()->with('children')->get(),
            'cities' => City::orderBy('sort_order')->get(),
            'sorts' => ListingFilters::SORTS,
        ]);
    }

    public function show(Request $request, Listing $listing): View
    {
        $isOwner = Auth::guard('web')->id() === $listing->user_id;
        abort_unless($listing->isVisible() || $isOwner, 404);

        $listing->load(['images', 'category.parent', 'city', 'district', 'favoritedBy', 'user' => fn ($q) => $q->withCount([
            'listings as visible_listings_count' => fn ($q) => $q->visible(),
        ])]);

        // One view per visitor session; owners browsing their own listing don't count.
        $viewed = $request->session()->get('viewed_listings', []);
        if (! $isOwner && ! in_array($listing->id, $viewed, true)) {
            Listing::whereKey($listing->id)->toBase()->increment('views');
            $request->session()->put('viewed_listings', [...array_slice($viewed, -199), $listing->id]);
        }

        $similar = Listing::visible()
            ->with(['images', 'city', 'district', 'user', 'favoritedBy'])
            ->where('listing_category_id', $listing->listing_category_id)
            ->whereKeyNot($listing->id)
            ->boostedFirst()
            ->take(8)
            ->get();

        return view('listings.show', compact('listing', 'similar', 'isOwner'));
    }
}
