<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        Coupon::updateOrCreate(
            ['code' => 'AKIBA10'],
            [
                'type' => 'percentage',
                'value' => 10,
                'min_purchase' => 20000,
                'starts_at' => now()->subDay(),
                'ends_at' => now()->addMonths(6),
                'usage_limit' => null,
                'used_count' => 0,
                'is_active' => true,
            ]
        );
    }
}
