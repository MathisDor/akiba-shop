<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Promotion;
use App\Models\Size;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /** @var array<int, string> */
    private array $sourceImages = [];

    private int $imageCursor = 0;

    private ?int $adminId = null;

    public function run(): void
    {
        $this->adminId = User::where('email', 'admin@akibashop.ga')->value('id');

        $this->sourceImages = collect(File::files(public_path('produits')))
            ->map(fn ($file) => $file->getPathname())
            ->values()
            ->all();

        $products = [
            // Femme
            ['name' => 'Robe satinée élégante', 'sku' => 'AKB-9402', 'category' => 'robes-femme', 'gender' => 'femme', 'price' => 95000, 'brand' => 'AkibaShop', 'colors' => ['Émeraude', 'Noir', 'Rouge'], 'sizes' => ['XS', 'S', 'M', 'L', 'XL'], 'images' => 3, 'description' => 'Sublimez vos soirées avec cette robe élégante en satin. Sa coupe fluide et son drapé délicat épousent parfaitement la silhouette tout en offrant un confort absolu. Parfaite pour les grandes occasions ou un dîner chic.', 'promo_fixed' => 16000],
            ['name' => 'Blazer Tailleur Noir', 'sku' => 'AKB-1001', 'category' => 'ensembles-femme', 'gender' => 'femme', 'price' => 45000, 'brand' => 'AkibaShop', 'colors' => ['Noir'], 'sizes' => ['S', 'M', 'L'], 'images' => 2],
            ['name' => 'Blouse en soie perlée', 'sku' => 'AKB-1010', 'category' => 'tops-femme', 'gender' => 'femme', 'price' => 45000, 'brand' => 'AkibaShop', 'colors' => ['Blanc', 'Beige'], 'sizes' => ['XS', 'S', 'M', 'L'], 'images' => 2],
            ['name' => 'Pantalon large tailleur', 'sku' => 'AKB-1020', 'category' => 'pantalons-femme', 'gender' => 'femme', 'price' => 62000, 'brand' => 'AkibaShop', 'colors' => ['Noir', 'Beige'], 'sizes' => ['S', 'M', 'L', 'XL'], 'images' => 2],
            ['name' => 'Jupe plissée sable', 'sku' => 'AKB-1030', 'category' => 'jupes-femme', 'gender' => 'femme', 'price' => 38000, 'brand' => 'AkibaShop', 'colors' => ['Sable', 'Noir'], 'sizes' => ['XS', 'S', 'M', 'L'], 'images' => 2],
            ['name' => 'Manteau structuré camel', 'sku' => 'AKB-1040', 'category' => 'manteaux-femme', 'gender' => 'femme', 'price' => 98000, 'brand' => 'AkibaShop', 'colors' => ['Beige', 'Noir'], 'sizes' => ['S', 'M', 'L'], 'images' => 2],
            ['name' => 'Ensemble tailleur beige', 'sku' => 'AKB-1050', 'category' => 'ensembles-femme', 'gender' => 'femme', 'price' => 72000, 'brand' => 'AkibaShop', 'colors' => ['Beige'], 'sizes' => ['S', 'M', 'L'], 'images' => 2],

            // Homme
            ['name' => 'Chemise Lin Blanc', 'sku' => 'AKB-2001', 'category' => 'chemises-homme', 'gender' => 'homme', 'price' => 32000, 'brand' => 'AkibaShop', 'colors' => ['Blanc'], 'sizes' => ['S', 'M', 'L', 'XL'], 'images' => 2],
            ['name' => 'Veste en lin sable', 'sku' => 'AKB-2010', 'category' => 'vestes-homme', 'gender' => 'homme', 'price' => 85000, 'brand' => 'Atelier Lbv', 'colors' => ['Sable'], 'sizes' => ['M', 'L', 'XL'], 'images' => 2],
            ['name' => 'T-shirt Essentiel Noir', 'sku' => 'AKB-2020', 'category' => 't-shirts-homme', 'gender' => 'homme', 'price' => 18000, 'brand' => 'AkibaShop', 'colors' => ['Noir', 'Blanc', 'Gris'], 'sizes' => ['S', 'M', 'L', 'XL'], 'images' => 2],
            ['name' => 'Polo Piqué Marine', 'sku' => 'AKB-2030', 'category' => 'polos-homme', 'gender' => 'homme', 'price' => 25000, 'brand' => 'AkibaShop', 'colors' => ['Bleu Marine', 'Blanc'], 'sizes' => ['S', 'M', 'L', 'XL'], 'images' => 2],
            ['name' => 'Jean Slim Brut', 'sku' => 'AKB-2040', 'category' => 'jeans-homme', 'gender' => 'homme', 'price' => 35000, 'brand' => 'AkibaShop', 'colors' => ['Bleu Marine'], 'sizes' => ['S', 'M', 'L', 'XL'], 'images' => 2],
            ['name' => 'Sweat Oversize Gris', 'sku' => 'AKB-2050', 'category' => 'sweats-homme', 'gender' => 'homme', 'price' => 28000, 'brand' => 'AkibaShop', 'colors' => ['Gris', 'Noir'], 'sizes' => ['S', 'M', 'L', 'XL'], 'images' => 2],
            ['name' => 'Pull Col Rond Camel', 'sku' => 'AKB-2060', 'category' => 'pulls-homme', 'gender' => 'homme', 'price' => 30000, 'brand' => 'AkibaShop', 'colors' => ['Beige', 'Gris'], 'sizes' => ['S', 'M', 'L'], 'images' => 2],

            // Accessoires
            ['name' => 'Sac Minimaliste Structuré', 'sku' => 'AKB-3001', 'category' => 'accessoires-femme', 'gender' => 'femme', 'price' => 85000, 'brand' => 'AkibaShop', 'colors' => ['Noir', 'Sable'], 'sizes' => [], 'images' => 2],
            ['name' => 'Sac en cuir structuré', 'sku' => 'AKB-3010', 'category' => 'accessoires-femme', 'gender' => 'femme', 'price' => 125000, 'brand' => 'Maison Ndong', 'colors' => ['Noir'], 'sizes' => [], 'images' => 2],
            ['name' => 'Sac à Main Élégance Noir', 'sku' => 'AKB-3020', 'category' => 'accessoires-femme', 'gender' => 'femme', 'price' => 45000, 'brand' => 'AkibaShop', 'colors' => ['Noir'], 'sizes' => [], 'images' => 1],
            ['name' => 'Sac mini cuir texturé', 'sku' => 'AKB-3030', 'category' => 'accessoires-femme', 'gender' => 'femme', 'price' => 120000, 'brand' => 'AkibaShop', 'colors' => ['Terracotta'], 'sizes' => [], 'images' => 1],
            ['name' => 'Collier Minimaliste Or', 'sku' => 'AKB-3040', 'category' => 'accessoires-femme', 'gender' => 'femme', 'price' => 15500, 'brand' => 'Bijoux Komo', 'colors' => ['Or'], 'sizes' => [], 'images' => 1],
            ['name' => "Boucles d'oreilles Géométriques Or", 'sku' => 'AKB-3050', 'category' => 'accessoires-femme', 'gender' => 'femme', 'price' => 12500, 'brand' => 'Bijoux Komo', 'colors' => ['Or'], 'sizes' => [], 'images' => 1],
            ['name' => "Boucles d'oreilles perles", 'sku' => 'AKB-3060', 'category' => 'accessoires-femme', 'gender' => 'femme', 'price' => 18500, 'brand' => 'Bijoux Komo', 'colors' => ['Or'], 'sizes' => [], 'images' => 1],
            ['name' => 'Bracelet Tressé Or', 'sku' => 'AKB-3070', 'category' => 'accessoires-femme', 'gender' => 'femme', 'price' => 150000, 'brand' => 'Bijoux Komo', 'colors' => ['Or'], 'sizes' => [], 'images' => 1],
            ['name' => 'Sneakers Urbaines', 'sku' => 'AKB-3080', 'category' => 'accessoires-homme', 'gender' => 'homme', 'price' => 65000, 'brand' => 'Ogooué Kicks', 'colors' => ['Blanc', 'Terracotta'], 'sizes' => [], 'images' => 2],
        ];

        foreach ($products as $data) {
            $this->createProduct($data);
        }
    }

    private function createProduct(array $data): void
    {
        $category = Category::where('slug', $data['category'])->firstOrFail();

        $product = Product::updateOrCreate(
            ['sku' => $data['sku']],
            [
                'name' => $data['name'],
                'slug' => Str::slug($data['name']),
                'description' => $data['description'] ?? "Pièce {$data['name']} de la collection AkibaShop, pensée pour sublimer votre style au quotidien.",
                'category_id' => $category->id,
                'brand' => $data['brand'],
                'material' => 'Voir fiche produit',
                'gender' => $data['gender'],
                'base_price' => $data['price'],
                'is_active' => true,
                'published_at' => now(),
            ]
        );

        $product->forceFill(['created_by' => $this->adminId, 'updated_by' => $this->adminId])->save();

        $product->images()->delete();
        for ($i = 0; $i < $data['images']; $i++) {
            $storedPath = $this->storeNextImage();
            ProductImage::create([
                'product_id' => $product->id,
                'path' => $storedPath,
                'sort_order' => $i,
                'is_primary' => $i === 0,
            ]);
        }

        $product->variants()->delete();
        $colors = Color::whereIn('name', $data['colors'])->get();
        $sizes = empty($data['sizes']) ? [null] : Size::whereIn('name', $data['sizes'])->get()->all();

        $variantIndex = 0;
        foreach ($colors as $color) {
            foreach ($sizes as $size) {
                $variantIndex++;
                ProductVariant::create([
                    'product_id' => $product->id,
                    'color_id' => $color->id,
                    'size_id' => $size?->id,
                    'sku' => $data['sku'].'-'.$variantIndex,
                    'stock' => random_int(0, 1) === 0 ? random_int(4, 18) : random_int(0, 3),
                ]);
            }
        }

        if (! empty($data['promo_fixed'])) {
            $promotion = Promotion::updateOrCreate(
                ['name' => "Promo {$product->name}"],
                [
                    'type' => 'fixed',
                    'value' => $data['promo_fixed'],
                    'starts_at' => now()->subDay(),
                    'ends_at' => now()->addMonth(),
                    'is_active' => true,
                ]
            );
            $promotion->products()->syncWithoutDetaching([$product->id]);
        }
    }

    private function storeNextImage(): string
    {
        if (empty($this->sourceImages)) {
            return '';
        }

        $source = $this->sourceImages[$this->imageCursor % count($this->sourceImages)];
        $this->imageCursor++;

        $filename = 'products/'.Str::random(20).'.jpeg';
        Storage::disk('public')->put($filename, File::get($source));

        return $filename;
    }
}
