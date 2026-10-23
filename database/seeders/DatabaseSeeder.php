<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            UserSeeder::class,
            MarketplaceSeeder::class,
        ]);

        if (app()->environment('local')) {
            $this->call(DemoListingSeeder::class);
        }
    }
}
