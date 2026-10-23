<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['slug' => 'listings.moderate', 'name' => 'Modération des annonces', 'group' => 'Annonces'],
            ['slug' => 'reports.manage', 'name' => 'Signalements', 'group' => 'Annonces'],
            ['slug' => 'categories.manage', 'name' => 'Catégories', 'group' => 'Annonces'],
            ['slug' => 'users.manage', 'name' => 'Utilisateurs', 'group' => 'Communauté'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(['slug' => $permission['slug']], $permission);
        }

        // Permissions of the former fashion shop back-office, retired with the marketplace pivot.
        Permission::whereIn('slug', [
            'products.manage', 'collections.manage', 'orders.manage', 'customers.view',
            'promotions.manage', 'coupons.manage', 'delivery.manage', 'reviews.moderate',
        ])->delete();
    }
}
