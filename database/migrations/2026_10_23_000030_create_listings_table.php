<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('listing_category_id')->constrained()->restrictOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            // Whole FCFA (XAF has no minor unit). NULL means "price on request".
            $table->unsignedBigInteger('price')->nullable();
            $table->boolean('is_negotiable')->default(false);
            $table->string('condition')->nullable();
            $table->json('specs')->nullable();
            $table->foreignId('city_id')->constrained()->restrictOnDelete();
            $table->foreignId('district_id')->nullable()->constrained()->nullOnDelete();
            $table->string('contact_phone', 20);
            $table->boolean('accepts_whatsapp')->default(true);
            $table->string('status')->default('en_attente')->index();
            $table->string('rejection_reason')->nullable();
            $table->unsignedInteger('views')->default(0);
            $table->timestamp('boosted_until')->nullable()->index();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamps();

            $table->index(['status', 'published_at']);
        });

        Schema::create('listing_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('listing_favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'listing_id']);
        });

        Schema::create('listing_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('reason');
            $table->text('details')->nullable();
            $table->string('status')->default('ouvert')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listing_reports');
        Schema::dropIfExists('listing_favorites');
        Schema::dropIfExists('listing_images');
        Schema::dropIfExists('listings');
    }
};
