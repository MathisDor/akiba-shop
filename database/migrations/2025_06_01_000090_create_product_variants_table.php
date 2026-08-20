<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('color_id')->nullable()->constrained('colors')->nullOnDelete();
            $table->foreignId('size_id')->nullable()->constrained('sizes')->nullOnDelete();
            $table->string('sku')->unique();
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedInteger('price_override')->nullable();
            $table->timestamps();

            $table->unique(['product_id', 'color_id', 'size_id'], 'product_variant_combo_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
