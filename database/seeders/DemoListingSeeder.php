<?php

namespace Database\Seeders;

use App\Enums\ListingStatus;
use App\Models\City;
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
    private const SELLERS = [
        ['Aurélie Mba', 'aurelie.mba@example.com', '074112233', false],
        ['Garage Nzeng-Ayong Auto', 'garage.nzeng@example.com', '066445566', true],
        ['Patrick Ondo', 'patrick.ondo@example.com', '077889900', false],
        ['Immo Estuaire', 'immo.estuaire@example.com', '062334455', true],
        ['Sandrine Moussavou', 'sandrine.m@example.com', '065778899', false],
    ];

    /** [seller index, category slug, title, price, condition, city slug, district, specs, description] */
    private const LISTINGS = [
        [1, 'voitures', 'Toyota Corolla 2014 très propre', 4500000, 'occasion', 'libreville', 'Nzeng-Ayong', ['marque' => 'Toyota', 'modele' => 'Corolla', 'annee' => 2014, 'kilometrage' => 132000, 'carburant' => 'Essence', 'boite' => 'Automatique'], 'Véhicule dédouané, papiers à jour. Climatisation fonctionnelle, pneus neufs. Visible à Nzeng-Ayong.'],
        [1, 'voitures', 'Hyundai Tucson 2017 diesel', 8900000, 'occasion', 'libreville', 'Owendo', ['marque' => 'Hyundai', 'modele' => 'Tucson', 'annee' => 2017, 'kilometrage' => 98000, 'carburant' => 'Diesel', 'boite' => 'Manuelle'], 'SUV en excellent état, entretien suivi au garage. Idéal pour les routes de l’intérieur.'],
        [2, 'motos-scooters', 'Moto Haojue 125 cc', 650000, 'occasion', 'franceville', null, ['marque' => 'Haojue', 'annee' => 2021, 'kilometrage' => 15000, 'carburant' => 'Essence'], 'Moto économique, parfaite pour les déplacements en ville.'],
        [3, 'locations', 'Appartement 3 pièces meublé à Glass', 450000, null, 'libreville', 'Glass', ['surface' => 85, 'pieces' => 3, 'meuble' => 'Oui'], 'Appartement climatisé, 2 chambres, salon, cuisine équipée. Gardiennage 24h/24, groupe électrogène. Loyer mensuel, caution 3 mois.'],
        [3, 'terrains', 'Terrain 500 m² titré à Angondjé', 15000000, null, 'libreville', 'Angondjé', ['surface' => 500], 'Terrain plat avec titre foncier, accès route goudronnée, eau et électricité à proximité.'],
        [3, 'chambres-studios', 'Studio moderne à Louis', 180000, null, 'libreville', 'Louis', ['surface' => 30, 'pieces' => 1, 'meuble' => 'Non'], 'Studio avec douche interne et cuisine américaine. Proche des commerces et des transports.'],
        [0, 'telephones', 'iPhone 13 128 Go comme neuf', 380000, 'occasion', 'libreville', 'Akébé', ['marque' => 'Apple', 'modele' => 'iPhone 13'], 'Batterie à 89 %, aucune rayure, vendu avec boîte et chargeur. Débloqué tous opérateurs.'],
        [4, 'telephones', 'Samsung Galaxy A54 neuf sous blister', 260000, 'neuf', 'port-gentil', null, ['marque' => 'Samsung', 'modele' => 'Galaxy A54'], 'Téléphone neuf, jamais ouvert, garantie 1 an. Livraison possible à Port-Gentil.'],
        [2, 'ordinateurs', 'HP EliteBook i5 8e génération', 230000, 'occasion', 'libreville', 'Centre-ville', ['marque' => 'HP', 'modele' => 'EliteBook 840 G5'], '16 Go de RAM, SSD 256 Go, Windows 11. Parfait pour le bureau et les études.'],
        [4, 'tv-audio', 'Télévision LG 55 pouces 4K', 320000, 'occasion', 'libreville', 'Okala', [], 'Smart TV en très bon état, télécommande d’origine. Cause départ.'],
        [0, 'meubles', 'Salon complet 7 places', 550000, 'occasion', 'libreville', 'Lalala', [], 'Canapés en tissu gris avec table basse. Très bon état, à récupérer sur place.'],
        [4, 'electromenager', 'Congélateur coffre 300 L', 210000, 'neuf', 'oyem', null, [], 'Congélateur neuf, idéal pour commerce ou famille. Faible consommation.'],
        [0, 'pagnes-tissus', 'Lot de 6 pagnes wax', 45000, 'neuf', 'libreville', 'Mont-Bouët', ['taille' => '6 yards'], 'Pagnes de qualité, motifs variés. Prix pour le lot complet, vente au détail possible.'],
        [0, 'vetements-femme', 'Robes de soirée tailles 38 à 44', 25000, 'neuf', 'libreville', 'Nombakélé', ['taille' => '38 – 44'], 'Nouvel arrivage de robes de soirée, plusieurs coloris disponibles.'],
        [4, 'cosmetiques-parfums', 'Parfums originaux 100 ml', 35000, 'neuf', 'lambarene', null, [], 'Parfums originaux homme et femme, plusieurs marques. Livraison possible.'],
        [2, 'prestations-freelance', 'Électricien bâtiment disponible', null, null, 'libreville', 'PK5 – PK12', ['type_contrat' => 'Prestation / freelance'], 'Installation, dépannage, mise aux normes. Devis gratuit, intervention rapide sur Libreville et Owendo.'],
        [3, 'offres-demploi', 'Recrutement commerciaux terrain', null, null, 'libreville', 'Batterie IV', ['type_contrat' => 'CDD'], 'Entreprise de distribution recrute 5 commerciaux motivés. Permis B apprécié. Envoyez votre CV par WhatsApp.'],
        [2, 'volailles', 'Poulets de chair prêts à vendre', 5000, null, 'moanda', null, [], 'Poulets de 45 jours, nourris au grain. Prix à l’unité, réduction à partir de 20 poulets.'],
        [0, 'produits-vivriers', 'Régimes de banane plantain', 7000, null, 'mouila', null, [], 'Plantain frais de nos plantations, envoi possible vers Libreville par lot.'],
        [4, 'poussettes-equipement', 'Poussette + siège auto', 85000, 'occasion', 'libreville', 'Sablière', [], 'Poussette légère et pliable avec siège auto adapté. Très peu servi.'],
    ];

    public function run(): void
    {
        $clientRole = Role::where('slug', 'client')->firstOrFail();
        $libreville = City::where('slug', 'libreville')->firstOrFail();

        $sellers = collect(self::SELLERS)->map(function (array $seller) use ($clientRole, $libreville) {
            [$name, $email, $phone, $isPro] = $seller;

            $user = User::updateOrCreate(['email' => $email], [
                'name' => $name,
                'password' => 'password',
                'role_id' => $clientRole->id,
                'phone' => $phone,
                'city_id' => $libreville->id,
                'is_pro' => $isPro,
                'email_verified_at' => now(),
            ]);
            $user->forceFill(['is_verified' => $isPro])->save();

            return $user;
        });

        foreach (self::LISTINGS as $i => [$seller, $categorySlug, $title, $price, $condition, $citySlug, $districtName, $specs, $description]) {
            $city = City::where('slug', $citySlug)->firstOrFail();
            $publishedAt = now()->subHours($i * 7 + 1);

            Listing::updateOrCreate(['title' => $title, 'user_id' => $sellers[$seller]->id], [
                'listing_category_id' => ListingCategory::where('slug', $categorySlug)->firstOrFail()->id,
                'slug' => Listing::makeSlug($title),
                'description' => $description,
                'price' => $price,
                'is_negotiable' => $price !== null && $i % 3 === 0,
                'condition' => $condition,
                'specs' => $specs,
                'city_id' => $city->id,
                'district_id' => $districtName ? $city->districts()->where('name', $districtName)->value('id') : null,
                'contact_phone' => $sellers[$seller]->phone,
                'accepts_whatsapp' => true,
                'status' => ListingStatus::Publiee,
                'published_at' => $publishedAt,
                'expires_at' => $publishedAt->copy()->addDays(config('marketplace.listing_lifetime_days')),
                'boosted_until' => $i < 2 ? now()->addDays(3) : null,
            ]);
        }
    }
}
