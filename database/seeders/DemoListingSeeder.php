<?php

namespace Database\Seeders;

use App\Enums\ListingStatus;
use App\Models\Listing;
use App\Models\ListingCategory;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Local demo content only — never run in production (fake listings would mislead real visitors).
 */
class DemoListingSeeder extends Seeder
{
    /** [name, email, E.164 phone, country, city, pro] */
    private const SELLERS = [
        ['Claire Martin', 'claire.martin@example.com', '+33612345678', 'FR', 'Lyon', false],
        ['Garage Central Auto', 'garage.central@example.com', '+32470123456', 'BE', 'Bruxelles', true],
        ['Moussa Diallo', 'moussa.diallo@example.com', '+221771234567', 'SN', 'Dakar', false],
        ['Agence Horizon Immo', 'horizon.immo@example.com', '+15145550123', 'CA', 'Montréal', true],
        ['Sofia Benali', 'sofia.benali@example.com', '+212612345678', 'MA', 'Casablanca', false],
    ];

    /** [seller index, category slug, title, price, condition, specs, description] — located at the seller's city */
    private const LISTINGS = [
        [1, 'voitures', 'Toyota Corolla 2014 très propre', 8500, 'occasion', ['marque' => 'Toyota', 'modele' => 'Corolla', 'annee' => 2014, 'kilometrage' => 132000, 'carburant' => 'Essence', 'boite' => 'Automatique'], 'Contrôle technique OK, carnet d’entretien à jour. Climatisation fonctionnelle, pneus neufs.'],
        [1, 'voitures', 'Hyundai Tucson 2017 diesel', 14900, 'occasion', ['marque' => 'Hyundai', 'modele' => 'Tucson', 'annee' => 2017, 'kilometrage' => 98000, 'carburant' => 'Diesel', 'boite' => 'Manuelle'], 'SUV en excellent état, entretien suivi au garage. Deuxième main.'],
        [2, 'motos-scooters', 'Scooter 125 cc peu servi', 1200, 'occasion', ['marque' => 'Yamaha', 'annee' => 2021, 'kilometrage' => 15000, 'carburant' => 'Essence'], 'Scooter économique, parfait pour les déplacements en ville.'],
        [3, 'locations', 'Appartement 3 pièces meublé', 1450, null, ['surface' => 85, 'pieces' => 3, 'meuble' => 'Oui'], 'Appartement lumineux, 2 chambres, salon, cuisine équipée. Proche transports. Loyer mensuel.'],
        [3, 'terrains', 'Terrain constructible 500 m²', 65000, null, ['surface' => 500], 'Terrain plat viabilisé, accès route goudronnée, eau et électricité à proximité.'],
        [3, 'chambres-studios', 'Studio moderne centre-ville', 690, null, ['surface' => 30, 'pieces' => 1, 'meuble' => 'Non'], 'Studio avec salle d’eau et cuisine américaine. Proche des commerces.'],
        [0, 'telephones', 'iPhone 13 128 Go comme neuf', 420, 'occasion', ['marque' => 'Apple', 'modele' => 'iPhone 13'], 'Batterie à 89 %, aucune rayure, vendu avec boîte et chargeur. Débloqué tous opérateurs.'],
        [4, 'telephones', 'Samsung Galaxy A54 neuf sous blister', 290, 'neuf', ['marque' => 'Samsung', 'modele' => 'Galaxy A54'], 'Téléphone neuf, jamais ouvert, garantie 1 an.'],
        [2, 'ordinateurs', 'HP EliteBook i5 8e génération', 330, 'occasion', ['marque' => 'HP', 'modele' => 'EliteBook 840 G5'], '16 Go de RAM, SSD 256 Go, Windows 11. Parfait pour le bureau et les études.'],
        [4, 'tv-audio', 'Télévision LG 55 pouces 4K', 380, 'occasion', [], 'Smart TV en très bon état, télécommande d’origine. Cause déménagement.'],
        [0, 'meubles', 'Canapé d’angle 5 places', 450, 'occasion', [], 'Canapé en tissu gris avec table basse. Très bon état, à récupérer sur place.'],
        [4, 'electromenager', 'Congélateur coffre 300 L', 260, 'neuf', [], 'Congélateur neuf, idéal pour commerce ou famille. Faible consommation.'],
        [0, 'tissus-mercerie', 'Lot de tissus imprimés', 60, 'neuf', ['taille' => '6 m'], 'Tissus de qualité, motifs variés. Prix pour le lot complet.'],
        [0, 'vetements-femme', 'Robes de soirée tailles 38 à 44', 35, 'neuf', ['taille' => '38 – 44'], 'Nouvel arrivage de robes de soirée, plusieurs coloris disponibles.'],
        [4, 'cosmetiques-parfums', 'Parfums originaux 100 ml', 55, 'neuf', [], 'Parfums originaux homme et femme, plusieurs marques.'],
        [2, 'prestations-freelance', 'Électricien bâtiment disponible', null, null, ['type_contrat' => 'Prestation / freelance'], 'Installation, dépannage, mise aux normes. Devis gratuit, intervention rapide.'],
        [3, 'offres-demploi', 'Recrutement commerciaux terrain', null, null, ['type_contrat' => 'CDD'], 'Entreprise de distribution recrute 5 commerciaux motivés. Permis B apprécié.'],
        [2, 'volailles', 'Poulets fermiers', 9, null, [], 'Poulets élevés en plein air, nourris au grain. Prix à l’unité.'],
        [0, 'produits-agricoles', 'Paniers de légumes de saison', 15, null, [], 'Légumes frais de notre exploitation, récoltés le jour même.'],
        [4, 'poussettes-equipement', 'Poussette + siège auto', 120, 'occasion', [], 'Poussette légère et pliable avec siège auto adapté. Très peu servi.'],
    ];

    public function run(): void
    {
        $clientRole = Role::where('slug', 'client')->firstOrFail();

        $sellers = collect(self::SELLERS)->map(function (array $seller) use ($clientRole) {
            [$name, $email, $phone, $country, $city, $isPro] = $seller;

            $user = User::updateOrCreate(['email' => $email], [
                'name' => $name,
                'password' => 'password',
                'role_id' => $clientRole->id,
                'phone' => $phone,
                'country_code' => $country,
                'city' => $city,
                'is_pro' => $isPro,
                'email_verified_at' => now(),
            ]);
            $user->forceFill(['is_verified' => $isPro])->save();

            return $user;
        });

        foreach (self::LISTINGS as $i => [$seller, $categorySlug, $title, $price, $condition, $specs, $description]) {
            $owner = $sellers[$seller];
            $publishedAt = now()->subHours($i * 7 + 1);

            Listing::updateOrCreate(['title' => $title, 'user_id' => $owner->id], [
                'listing_category_id' => ListingCategory::where('slug', $categorySlug)->firstOrFail()->id,
                'slug' => Listing::makeSlug($title),
                'description' => $description,
                'price' => $price,
                'is_negotiable' => $price !== null && $i % 3 === 0,
                'condition' => $condition,
                'specs' => $specs,
                'country_code' => $owner->country_code,
                'city' => $owner->city,
                'contact_phone' => $owner->phone,
                'accepts_whatsapp' => true,
                'status' => ListingStatus::Publiee,
                'published_at' => $publishedAt,
                'expires_at' => $publishedAt->copy()->addDays(config('marketplace.listing_lifetime_days')),
                'boosted_until' => $i < 2 ? now()->addDays(3) : null,
            ]);
        }
    }
}
