<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Public storefront settings for sellers: photos, description, address, delivery and hours. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar_path')->nullable()->after('city');
            $table->string('cover_path')->nullable()->after('avatar_path');
            $table->text('bio')->nullable()->after('cover_path');
            $table->string('address', 150)->nullable()->after('bio');
            $table->boolean('delivers')->default(false)->after('address');
            $table->string('delivery_note', 200)->nullable()->after('delivers');
            $table->string('opening_hours', 120)->nullable()->after('delivery_note');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar_path', 'cover_path', 'bio', 'address', 'delivers', 'delivery_note', 'opening_hours']);
        });
    }
};
