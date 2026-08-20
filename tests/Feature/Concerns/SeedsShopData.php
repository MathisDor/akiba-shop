<?php

namespace Tests\Feature\Concerns;

use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Role;
use App\Models\Size;
use App\Models\User;

trait SeedsShopData
{
    protected function makeClient(array $attributes = []): User
    {
        $role = Role::firstOrCreate(['slug' => 'client'], ['name' => 'Client']);

        return User::factory()->create(array_merge(['role_id' => $role->id], $attributes));
    }

    protected function makeAdmin(array $attributes = []): User
    {
        $role = Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Administrateur']);

        return User::factory()->create(array_merge(['role_id' => $role->id], $attributes));
    }

    protected function makeProductWithVariant(int $basePrice = 20000, int $stock = 5): ProductVariant
    {
        $category = Category::create([
            'name' => 'T-shirts', 'slug' => 't-shirts-test-'.uniqid(), 'gender' => 'homme', 'is_active' => true,
        ]);
        $color = Color::create(['name' => 'Noir', 'hex' => '#000000']);
        $size = Size::create(['name' => 'M', 'sort_order' => 1]);

        $product = Product::create([
            'name' => 'T-shirt Test',
            'slug' => 't-shirt-test-'.uniqid(),
            'sku' => 'TEST-'.uniqid(),
            'description' => 'Produit de test.',
            'category_id' => $category->id,
            'gender' => 'homme',
            'base_price' => $basePrice,
            'is_active' => true,
            'published_at' => now(),
        ]);

        return ProductVariant::create([
            'product_id' => $product->id,
            'color_id' => $color->id,
            'size_id' => $size->id,
            'sku' => $product->sku.'-1',
            'stock' => $stock,
        ]);
    }
}
