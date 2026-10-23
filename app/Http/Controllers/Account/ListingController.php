<?php

namespace App\Http\Controllers\Account;

use App\Enums\ListingCondition;
use App\Enums\ListingStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\ListingRequest;
use App\Models\Listing;
use App\Models\ListingCategory;
use App\Services\ListingImageStore;
use App\Support\Countries;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ListingController extends Controller
{
    public function __construct(private ListingImageStore $images) {}

    public function index(): View
    {
        $listings = Auth::guard('web')->user()
            ->listings()
            ->with(['images', 'category'])
            ->latest()
            ->paginate(20);

        return view('account.listings', compact('listings'));
    }

    public function create(): View
    {
        $user = Auth::guard('web')->user();

        return $this->form(new Listing([
            'country_code' => $user->country_code ?? config('marketplace.default_country'),
            'city' => $user->city,
            'contact_phone' => $user->phone,
            'accepts_whatsapp' => true,
        ]));
    }

    public function store(ListingRequest $request): RedirectResponse
    {
        $user = Auth::guard('web')->user();
        abort_unless($user->is_active, 403);

        $listing = DB::transaction(function () use ($request, $user) {
            $listing = $user->listings()->create([
                ...$request->listingAttributes(),
                'slug' => Listing::makeSlug($request->validated('title')),
                'status' => ListingStatus::EnAttente,
            ]);

            foreach (array_values($request->file('photos', [])) as $i => $photo) {
                $this->images->store($photo, $listing, $i);
            }

            return $listing;
        });

        return redirect()->route('account.listings')
            ->with('status', "Votre annonce « {$listing->title} » a bien été envoyée. Elle sera en ligne dès sa validation par notre équipe.");
    }

    public function edit(Listing $listing): View
    {
        $this->authorizeOwner($listing);
        abort_if($listing->status === ListingStatus::Vendue, 403);

        return $this->form($listing->load('images'));
    }

    public function update(ListingRequest $request, Listing $listing): RedirectResponse
    {
        $this->authorizeOwner($listing);
        abort_if($listing->status === ListingStatus::Vendue, 403);

        DB::transaction(function () use ($request, $listing) {
            $listing->fill($request->listingAttributes());

            // Any content change goes back through moderation.
            if ($listing->isDirty() || $request->hasFile('photos') || $request->filled('remove_images')) {
                $listing->status = ListingStatus::EnAttente;
                $listing->rejection_reason = null;
            }
            $listing->save();

            $listing->images()->whereIn('id', (array) $request->validated('remove_images', []))->get()
                ->each(fn ($image) => $this->images->delete($image));

            $nextOrder = (int) $listing->images()->max('sort_order') + 1;
            foreach (array_values($request->file('photos', [])) as $i => $photo) {
                $this->images->store($photo, $listing, $nextOrder + $i);
            }
        });

        $message = $listing->status === ListingStatus::EnAttente
            ? 'Annonce modifiée. Elle sera de nouveau visible après validation.'
            : 'Annonce mise à jour.';

        return redirect()->route('account.listings')->with('status', $message);
    }

    public function destroy(Listing $listing): RedirectResponse
    {
        $this->authorizeOwner($listing);

        $listing->images->each(fn ($image) => $this->images->delete($image));
        $listing->delete();

        return redirect()->route('account.listings')->with('status', 'Annonce supprimée.');
    }

    public function renew(Listing $listing): RedirectResponse
    {
        $this->authorizeOwner($listing);
        abort_unless($listing->isRenewable(), 403);

        $listing->update([
            'published_at' => now(),
            'expires_at' => now()->addDays(config('marketplace.listing_lifetime_days')),
        ]);

        return back()->with('status', 'Annonce renouvelée pour '.config('marketplace.listing_lifetime_days').' jours et remontée en tête de liste.');
    }

    public function markSold(Listing $listing): RedirectResponse
    {
        $this->authorizeOwner($listing);

        $listing->update(['status' => ListingStatus::Vendue]);

        return back()->with('status', 'Bravo ! L’annonce est marquée comme vendue et n’apparaît plus dans les résultats.');
    }

    private function form(Listing $listing): View
    {
        return view('listings.form', [
            'listing' => $listing,
            'categories' => ListingCategory::active()->roots()->with(['children' => fn ($q) => $q->active()])->get(),
            'countries' => Countries::options(),
            'conditions' => ListingCondition::cases(),
            'categoryFields' => config('marketplace.category_fields'),
        ]);
    }

    private function authorizeOwner(Listing $listing): void
    {
        abort_unless($listing->user_id === Auth::guard('web')->id(), 404);
    }
}
