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
            SizeSeeder::class,
            ColorSeeder::class,
            CategorySeeder::class,
            DeliveryZoneSeeder::class,
            UserSeeder::class,
            CollectionSeeder::class,
            CouponSeeder::class,
            ProductSeeder::class,
        ]);
    }
}
