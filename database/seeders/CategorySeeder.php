<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $femme = [
            'Robes', 'Ensembles', 'Tops', 'Chemises', 'T-shirts', 'Jeans', 'Pantalons',
            'Jupes', 'Shorts', 'Vestes', 'Manteaux', 'Lingerie', 'Accessoires',
        ];

        $homme = [
            'T-shirts', 'Chemises', 'Polos', 'Jeans', 'Pantalons', 'Shorts', 'Ensembles',
            'Vestes', 'Sweats', 'Pulls', 'Sous-vêtements', 'Accessoires',
        ];

        foreach ($femme as $name) {
            Category::updateOrCreate(
                ['slug' => Str::slug($name).'-femme'],
                ['name' => $name, 'gender' => 'femme', 'is_active' => true]
            );
        }

        foreach ($homme as $name) {
            Category::updateOrCreate(
                ['slug' => Str::slug($name).'-homme'],
                ['name' => $name, 'gender' => 'homme', 'is_active' => true]
            );
        }
    }
}
