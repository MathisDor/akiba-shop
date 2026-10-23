<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ListingStatus;
use App\Enums\ReportStatus;
use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\ListingCategory;
use App\Models\ListingReport;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $today = Carbon::today();
        $members = fn () => User::whereHas('role', fn ($q) => $q->where('slug', 'client'));

        $stats = [
            'pending' => Listing::where('status', ListingStatus::EnAttente->value)->count(),
            'online' => Listing::visible()->count(),
            'openReports' => ListingReport::where('status', ReportStatus::Ouvert->value)->count(),
            'members' => $members()->count(),
            'newMembers' => $members()->where('created_at', '>=', $today->copy()->subDays(6))->count(),
            'views' => (int) Listing::sum('views'),
            'boosted' => Listing::visible()->where('boosted_until', '>', now())->count(),
            'sold' => Listing::where('status', ListingStatus::Vendue->value)->count(),
        ];

        $newListingsByDay = Listing::selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->where('created_at', '>=', $today->copy()->subDays(13))
            ->groupBy('day')
            ->pluck('total', 'day');

        $days = collect(range(13, 0))->map(fn ($ago) => $today->copy()->subDays($ago))
            ->map(fn (Carbon $day) => ['label' => $day->format('d/m'), 'total' => (int) ($newListingsByDay[$day->toDateString()] ?? 0)]);

        $topCategories = ListingCategory::roots()->with('children')->get()
            ->map(fn (ListingCategory $category) => [
                'name' => $category->name,
                'total' => Listing::visible()->whereIn('listing_category_id', $category->selfAndChildrenIds())->count(),
            ])
            ->sortByDesc('total')
            ->take(6)
            ->values();

        $pendingListings = Listing::with(['images', 'user', 'category'])
            ->where('status', ListingStatus::EnAttente->value)
            ->oldest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'days', 'topCategories', 'pendingListings'));
    }
}
