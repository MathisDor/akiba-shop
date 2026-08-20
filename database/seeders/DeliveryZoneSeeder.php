<?php

namespace Database\Seeders;

use App\Models\DeliveryZone;
use Illuminate\Database\Seeder;

class DeliveryZoneSeeder extends Seeder
{
    public function run(): void
    {
        $zones = [
            'Centre-ville' => 2000,
            'Akanda' => 3000,
            'Owendo' => 3000,
            'Nzeng-Ayong' => 2500,
            'Lalala' => 2500,
            'Charbonnages' => 3000,
            'PK5 - PK12' => 3500,
        ];

        foreach ($zones as $zone => $fee) {
            DeliveryZone::updateOrCreate(
                ['province' => 'Estuaire', 'city' => 'Libreville', 'zone_name' => $zone],
                ['fee' => $fee, 'is_active' => true]
            );
        }
    }
}
