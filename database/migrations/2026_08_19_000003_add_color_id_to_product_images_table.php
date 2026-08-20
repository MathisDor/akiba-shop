<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_images', function (Blueprint $table) {
            $table->foreignId('color_id')->nullable()->after('product_id')
                ->constrained('colors')->nullOnDelete();
        });

        // Carry over the old "one image per color" assignment (via
        // product_variants.product_image_id) onto the image itself, now
        // that an image can belong to a color directly.
        DB::table('product_variants')
            ->whereNotNull('product_image_id')
            ->whereNotNull('color_id')
            ->select('product_image_id', 'color_id')
            ->distinct()
            ->orderBy('product_image_id')
            ->get()
            ->each(function ($row) {
                DB::table('product_images')
                    ->where('id', $row->product_image_id)
                    ->update(['color_id' => $row->color_id]);
            });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product_image_id');
        });
    }

    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->foreignId('product_image_id')->nullable()->after('size_id')
                ->constrained('product_images')->nullOnDelete();
        });

        Schema::table('product_images', function (Blueprint $table) {
            $table->dropConstrainedForeignId('color_id');
        });
    }
};
