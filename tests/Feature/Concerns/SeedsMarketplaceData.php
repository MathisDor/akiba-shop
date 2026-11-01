<?php

namespace Tests\Feature\Concerns;

use App\Enums\ListingStatus;
use App\Models\Listing;
use App\Models\ListingCategory;
use App\Models\User;

/** Categories come from the reference-data migration; this adds listings. */
trait SeedsMarketplaceData
{
    protected function makeListing(User $seller, array $attributes = []): Listing
    {
        return Listing::create(array_merge([
            'user_id' => $seller->id,
            'listing_category_id' => ListingCategory::where('slug', 'voitures')->value('id'),
            'title' => 'Toyota Corolla 2014',
            'slug' => Listing::makeSlug('Toyota Corolla 2014'),
            'description' => 'Très bon état, papiers à jour, climatisation fonctionnelle.',
            'price' => 4500000,
            'country_code' => 'GA',
            'province' => 'estuaire',
            'city' => 'Libreville',
            'contact_phone' => '+241077123456',
            'accepts_whatsapp' => true,
            'status' => ListingStatus::Publiee,
            'published_at' => now(),
            'expires_at' => now()->addDays(60),
        ], $attributes));
    }

    protected function listingPayload(array $overrides = []): array
    {
        return array_merge([
            'listing_category_id' => ListingCategory::where('slug', 'voitures')->value('id'),
            'title' => 'Hyundai Tucson 2017 diesel',
            'description' => 'SUV en excellent état, entretien suivi, visible sur rendez-vous.',
            'price' => '8 900 000',
            'condition' => 'occasion',
            'province' => 'ogooue-maritime',
            'city' => '  Port-Gentil ',
            'contact_phone_country' => 'GA',
            'contact_phone' => '066 44 55 66',
            'accepts_whatsapp' => '1',
            'specs' => ['marque' => 'Hyundai', 'annee' => '2017', 'surface' => '999'],
        ], $overrides);
    }
}
