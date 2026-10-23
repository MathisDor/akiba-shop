<?php

namespace App\Support;

use App\Enums\ListingCondition;
use App\Models\City;
use App\Models\Listing;
use App\Models\ListingCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

/**
 * Search + filters shared by /annonces, /categories/{slug} and the seller page.
 * Query-string keys are French since they show up in shared URLs.
 */
class ListingFilters
{
    public const SORTS = [
        'recent' => 'Plus récentes',
        'prix_asc' => 'Prix croissant',
        'prix_desc' => 'Prix décroissant',
    ];

    public function __construct(private Request $request) {}

    public function category(): ?ListingCategory
    {
        $slug = $this->request->query('categorie');

        return is_string($slug) && $slug !== ''
            ? ListingCategory::active()->where('slug', $slug)->first()
            : null;
    }

    public function city(): ?City
    {
        $slug = $this->request->query('ville');

        return is_string($slug) && $slug !== '' ? City::where('slug', $slug)->first() : null;
    }

    public function paginate(?ListingCategory $category = null, ?int $userId = null): LengthAwarePaginator
    {
        $category ??= $this->category();
        $query = Listing::query()
            ->visible()
            ->with(['images', 'city', 'district', 'user', 'favoritedBy']);

        if ($userId !== null) {
            $query->where('user_id', $userId);
        }

        if ($category) {
            $query->whereIn('listing_category_id', $category->selfAndChildrenIds());
        }

        if ($city = $this->city()) {
            $query->where('city_id', $city->id);

            if ($this->request->filled('quartier')) {
                $query->where('district_id', $this->request->integer('quartier'));
            }
        }

        $term = trim((string) $this->request->query('q', ''));
        if ($term !== '') {
            $like = '%'.addcslashes($term, '%_\\').'%';
            $query->where(fn ($q) => $q->where('title', 'like', $like)->orWhere('description', 'like', $like));
        }

        if ($this->request->filled('prix_min')) {
            $query->where('price', '>=', $this->request->integer('prix_min'));
        }
        if ($this->request->filled('prix_max')) {
            $query->where('price', '<=', $this->request->integer('prix_max'));
        }

        if ($condition = ListingCondition::tryFrom((string) $this->request->query('etat'))) {
            $query->where('condition', $condition->value);
        }

        match ($this->request->query('tri')) {
            'prix_asc' => $query->whereNotNull('price')->orderBy('price'),
            'prix_desc' => $query->whereNotNull('price')->orderByDesc('price'),
            default => $query->boostedFirst(),
        };

        return $query->paginate(config('marketplace.per_page'))->withQueryString();
    }

    /** Number of filters the visitor applied (badge on the mobile "Filtres" button). */
    public function activeCount(): int
    {
        return collect(['ville', 'prix_min', 'prix_max', 'etat'])
            ->filter(fn ($key) => $this->request->filled($key))
            ->count();
    }
}
