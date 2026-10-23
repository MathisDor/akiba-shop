<?php

use Database\Seeders\MarketplaceSeeder;
use Database\Seeders\PermissionSeeder;
use Illuminate\Database\Migrations\Migration;

/**
 * Cities, districts, categories and back-office permissions are required for the
 * marketplace to work at all, so they ship with the schema (deploys only run
 * `migrate`). Both seeders are idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        (new MarketplaceSeeder)->run();
        (new PermissionSeeder)->run();
    }

    public function down(): void
    {
        // Reference data is removed with its tables.
    }
};
