<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FavoriteController extends Controller
{
    public function index(): View
    {
        $listings = Auth::guard('web')->user()
            ->favoriteListings()
            ->with(['images', 'city', 'district', 'user', 'favoritedBy'])
            ->latest('listing_favorites.created_at')
            ->get();

        return view('account.favorites', compact('listings'));
    }

    public function toggle(Request $request, Listing $listing): RedirectResponse|JsonResponse
    {
        $result = Auth::guard('web')->user()->favoriteListings()->toggle($listing->id);

        if ($request->wantsJson()) {
            return response()->json(['favorited' => $result['attached'] !== []]);
        }

        return back();
    }
}
