<?php

namespace Database\Seeders;

use App\Models\ListingCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Reference data for the classifieds marketplace (categories).
 * Idempotent — safe to re-run in production.
 */
class MarketplaceSeeder extends Seeder
{
    private const CATEGORIES = [
        ['Véhicules', 'vehicules', 'car', ['Voitures', 'Motos & scooters', 'Pièces & accessoires auto', 'Camions & engins']],
        ['Immobilier', 'immobilier', 'house', ['Locations', 'Ventes de maisons & appartements', 'Terrains', 'Bureaux & commerces', 'Chambres & studios']],
        ['Téléphones & Électronique', 'telephones-electronique', 'smartphone', ['Téléphones', 'Ordinateurs', 'TV & audio', 'Consoles & jeux vidéo', 'Accessoires']],
        ['Maison & Décoration', 'maison-decoration', 'sofa', ['Meubles', 'Électroménager', 'Décoration', 'Cuisine & vaisselle']],
        ['Mode & Beauté', 'mode-beaute', 'shirt', ['Vêtements femme', 'Vêtements homme', 'Tissus & mercerie', 'Chaussures', 'Montres & bijoux', 'Cosmétiques & parfums']],
        ['Emploi & Services', 'emploi-services', 'briefcase', ['Offres d\'emploi', 'Demandes d\'emploi', 'Prestations & freelance', 'Cours & formations']],
        ['Loisirs & Divertissement', 'loisirs', 'gamepad', ['Sport', 'Instruments de musique', 'Livres', 'Billetterie & événements']],
        ['Animaux & Agriculture', 'animaux-agriculture', 'paw', ['Volailles', 'Bétail', 'Animaux de compagnie', 'Produits agricoles', 'Matériel agricole']],
        ['Bébé & Enfant', 'bebe-enfant', 'baby', ['Vêtements enfant', 'Poussettes & équipement', 'Jouets']],
        ['Autres', 'autres', 'package', []],
    ];

    public function run(): void
    {
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
