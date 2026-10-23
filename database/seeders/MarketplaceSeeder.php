<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\ListingCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Reference data for the classifieds marketplace (cities, districts, categories).
 * Idempotent — safe to re-run in production.
 */
class MarketplaceSeeder extends Seeder
{
    private const CITIES = [
        'Libreville' => [
            'Akanda', 'Akébé', 'Alibandeng', 'Ancienne Sobraga', 'Angondjé', 'Atong Abè', 'Awendjé',
            'Batterie IV', 'Belle-Vue', 'Centre-ville', 'Charbonnages', 'Cocotiers', 'Glass', 'Kalikak',
            'Lalala', 'Louis', 'Mont-Bouët', 'Nkembo', 'Nombakélé', 'Nzeng-Ayong', 'Okala', 'Oloumi',
            'Owendo', 'PK5 – PK12', 'Plaine Orety', 'Quaben', 'Sablière', 'Sotega', 'Venez-Voir',
        ],
        'Port-Gentil' => [],
        'Franceville' => [],
        'Oyem' => [],
        'Moanda' => [],
        'Lambaréné' => [],
        'Mouila' => [],
        'Tchibanga' => [],
        'Koulamoutou' => [],
        'Makokou' => [],
    ];

    private const CATEGORIES = [
        ['Véhicules', 'vehicules', 'car', ['Voitures', 'Motos & scooters', 'Pièces & accessoires auto', 'Camions & engins']],
        ['Immobilier', 'immobilier', 'house', ['Locations', 'Ventes de maisons & appartements', 'Terrains', 'Bureaux & commerces', 'Chambres & studios']],
        ['Téléphones & Électronique', 'telephones-electronique', 'smartphone', ['Téléphones', 'Ordinateurs', 'TV & audio', 'Consoles & jeux vidéo', 'Accessoires']],
        ['Maison & Décoration', 'maison-decoration', 'sofa', ['Meubles', 'Électroménager', 'Décoration', 'Cuisine & vaisselle']],
        ['Mode & Beauté', 'mode-beaute', 'shirt', ['Vêtements femme', 'Vêtements homme', 'Pagnes & tissus', 'Chaussures', 'Montres & bijoux', 'Cosmétiques & parfums']],
        ['Emploi & Services', 'emploi-services', 'briefcase', ['Offres d\'emploi', 'Demandes d\'emploi', 'Prestations & freelance', 'Cours & formations']],
        ['Loisirs & Divertissement', 'loisirs', 'gamepad', ['Sport', 'Instruments de musique', 'Livres', 'Billetterie & événements']],
        ['Animaux & Agriculture', 'animaux-agriculture', 'paw', ['Volailles', 'Bétail', 'Animaux de compagnie', 'Produits vivriers', 'Matériel agricole']],
        ['Bébé & Enfant', 'bebe-enfant', 'baby', ['Vêtements enfant', 'Poussettes & équipement', 'Jouets']],
        ['Autres', 'autres', 'package', []],
    ];

    public function run(): void
    {
        $order = 0;
        foreach (self::CITIES as $name => $districts) {
            $city = City::updateOrCreate(['slug' => Str::slug($name)], ['name' => $name, 'sort_order' => $order++]);

            foreach ($districts as $district) {
                $city->districts()->firstOrCreate(['name' => $district]);
            }
        }

        foreach (self::CATEGORIES as $i => [$name, $slug, $icon, $children]) {
            $root = ListingCategory::updateOrCreate(
                ['slug' => $slug],
                ['name' => $name, 'icon' => $icon, 'sort_order' => $i, 'parent_id' => null],
            );

            foreach ($children as $j => $childName) {
                ListingCategory::updateOrCreate(
                    ['slug' => Str::slug($childName)],
                    ['name' => $childName, 'parent_id' => $root->id, 'sort_order' => $j],
                );
            }
        }
    }
}
