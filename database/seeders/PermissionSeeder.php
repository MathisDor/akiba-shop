<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['slug' => 'products.manage', 'name' => 'Produits', 'group' => 'Boutique'],
            ['slug' => 'categories.manage', 'name' => 'Catégories', 'group' => 'Boutique'],
            ['slug' => 'collections.manage', 'name' => 'Collections', 'group' => 'Boutique'],
            ['slug' => 'orders.manage', 'name' => 'Commandes', 'group' => 'Ventes'],
            ['slug' => 'customers.view', 'name' => 'Clients', 'group' => 'Ventes'],
            ['slug' => 'promotions.manage', 'name' => 'Promotions', 'group' => 'Ventes'],
            ['slug' => 'coupons.manage', 'name' => 'Coupons', 'group' => 'Ventes'],
            ['slug' => 'delivery.manage', 'name' => 'Livraison', 'group' => 'Logistique'],
            ['slug' => 'reviews.moderate', 'name' => 'Avis clients', 'group' => 'Contenu'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(['slug' => $permission['slug']], $permission);
        }
    }
}
