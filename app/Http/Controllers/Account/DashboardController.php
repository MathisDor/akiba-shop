<?php

namespace App\Http\Controllers\Account;

use App\Enums\ListingStatus;
use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\ListingCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/** Members' landing page after sign-in: their activity at a glance, then the latest listings. */
class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = Auth::guard('web')->user();

        $stats = [
            'online' => $user->listings()->visible()->count(),
            'pending' => $user->listings()->where('status', ListingStatus::EnAttente->value)->count(),
            'unread' => $user->unreadMessagesCount(),
            'favorites' => $user->favoriteListings()->count(),
        ];

        // Listings near the member first when we know their province.
        $latest = Listing::visible()
            ->with(['images', 'user', 'favoritedBy'])
            ->where('user_id', '!=', $user->id)
            ->when($user->province, fn ($q) => $q->orderByRaw('CASE WHEN province = ? THEN 0 ELSE 1 END', [$user->province]))
            ->boostedFirst()
            ->take(24)
            ->get();

        return view('account.dashboard', [
            'user' => $user,
            'stats' => $stats,
            'latest' => $latest,
            'categories' => ListingCategory::active()->roots()->get(),
        ]);
    }
}
