<?php

namespace Database\Seeders;

use App\Models\Color;
use Illuminate\Database\Seeder;

class ColorSeeder extends Seeder
{
    public function run(): void
    {
        $colors = [
            'Noir' => '#000000',
            'Blanc' => '#FFFFFF',
            'Rouge' => '#8B0000',
            'Bleu Marine' => '#1E3A8A',
            'Beige' => '#D8C3A5',
            'Émeraude' => '#046337',
            'Or' => '#D4AF37',
            'Terracotta' => '#A63B00',
            'Gris' => '#6B7280',
            'Sable' => '#C2A878',
        ];

        foreach ($colors as $name => $hex) {
            Color::updateOrCreate(['name' => $name], ['hex' => $hex]);
        }
    }
}
