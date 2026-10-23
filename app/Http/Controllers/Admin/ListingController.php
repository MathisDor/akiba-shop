<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ListingStatus;
use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Services\ListingImageStore;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ListingController extends Controller
{
    public function index(Request $request): View
    {
        $status = ListingStatus::tryFrom((string) $request->query('statut', ListingStatus::EnAttente->value));

        $query = Listing::with(['images', 'user', 'category'])->withCount('reports');

        if ($status) {
            $query->where('status', $status->value);
        }

        if ($request->filled('q')) {
            $like = '%'.addcslashes($request->query('q'), '%_\\').'%';
            $query->where(fn ($q) => $q->where('title', 'like', $like)
                ->orWhereHas('user', fn ($u) => $u->where('name', 'like', $like)->orWhere('phone', 'like', $like)));
        }

        // Pending listings are reviewed oldest first, everything else newest first.
        $status === ListingStatus::EnAttente ? $query->oldest() : $query->latest();

        $counts = Listing::selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');

        return view('admin.listings.index', [
            'listings' => $query->paginate(20)->withQueryString(),
            'currentStatus' => $status,
            'counts' => $counts,
        ]);
    }

    public function show(Listing $listing): View
    {
        $listing->load(['images', 'category.parent', 'reports.user', 'user' => fn ($q) => $q->withCount('listings')]);

        return view('admin.listings.show', compact('listing'));
    }

    public function approve(Listing $listing): RedirectResponse
    {
        $expired = $listing->expires_at === null || $listing->expires_at->isPast();

        $listing->update([
            'status' => ListingStatus::Publiee,
            'rejection_reason' => null,
            'published_at' => $listing->published_at ?? now(),
            'expires_at' => $expired ? now()->addDays(config('marketplace.listing_lifetime_days')) : $listing->expires_at,
        ]);

        $this->log($listing, 'Annonce approuvée');

        return $this->backToQueue('Annonce approuvée et mise en ligne.');
    }

    public function reject(Request $request, Listing $listing): RedirectResponse
    {
        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:255'],
        ], [
            'rejection_reason.required' => 'Indiquez au vendeur pourquoi l’annonce est refusée.',
        ]);

        $listing->update(['status' => ListingStatus::Refusee, ...$validated]);

        $this->log($listing, 'Annonce refusée : '.$validated['rejection_reason']);

        return $this->backToQueue('Annonce refusée.');
    }

    public function boost(Listing $listing): RedirectResponse
    {
        $boosting = ! $listing->isBoosted();
        $listing->update(['boosted_until' => $boosting ? now()->addDays(config('marketplace.boost_days')) : null]);

        $this->log($listing, $boosting ? 'Annonce mise en avant' : 'Mise en avant retirée');

        return back()->with('status', $boosting
            ? 'Annonce mise en avant pendant '.config('marketplace.boost_days').' jours.'
            : 'Mise en avant retirée.');
    }

    public function destroy(Listing $listing, ListingImageStore $images): RedirectResponse
    {
        $this->log($listing, 'Annonce supprimée : '.$listing->title);

        $listing->images->each(fn ($image) => $images->delete($image));
        $listing->delete();

        return redirect()->route('admin.listings.index')->with('status', 'Annonce supprimée.');
    }

    /** After a moderation decision, go straight to the next listing waiting for review. */
    private function backToQueue(string $message): RedirectResponse
    {
        $next = Listing::where('status', ListingStatus::EnAttente->value)->oldest()->first();

        return ($next ? redirect()->route('admin.listings.show', $next) : redirect()->route('admin.listings.index'))
            ->with('status', $message);
    }

    private function log(Listing $listing, string $description): void
    {
        activity('listing')->performedOn($listing)->causedBy(auth('admin')->user())->log($description);
    }
}
