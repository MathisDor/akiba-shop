<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * The marketplace goes international: a country + free-text city replace the
 * Gabon-only city/district tables, and phone numbers move to E.164.
 * Existing rows were all Gabonese, so they are backfilled as such.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->char('country_code', 2)->default('FR')->after('specs')->index();
            $table->string('city', 100)->default('')->after('country_code');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->char('country_code', 2)->nullable()->after('phone');
            $table->string('city', 100)->nullable()->after('country_code');
        });

        DB::table('listings')->orderBy('id')->each(function ($listing) {
            DB::table('listings')->where('id', $listing->id)->update([
                'country_code' => 'GA',
                'city' => DB::table('cities')->where('id', $listing->city_id)->value('name') ?? '',
                'contact_phone' => $this->toE164($listing->contact_phone),
            ]);
        });

        DB::table('users')->orderBy('id')->each(function ($user) {
            DB::table('users')->where('id', $user->id)->update([
                'country_code' => $user->city_id ? 'GA' : null,
                'city' => $user->city_id ? DB::table('cities')->where('id', $user->city_id)->value('name') : null,
                'phone' => $user->phone ? $this->toE164($user->phone) : null,
            ]);
        });

        Schema::table('listings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('district_id');
            $table->dropConstrainedForeignId('city_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('city_id');
        });

        Schema::dropIfExists('districts');
        Schema::dropIfExists('cities');

        DB::table('listing_categories')->where('slug', 'pagnes-tissus')
            ->update(['slug' => 'tissus-mercerie', 'name' => 'Tissus & mercerie']);
        DB::table('listing_categories')->where('slug', 'produits-vivriers')
            ->update(['slug' => 'produits-agricoles', 'name' => 'Produits agricoles']);
    }

    public function down(): void
    {
        // One-way: the Gabon-only city reference data is not restored.
    }

    /** Gabonese national numbers ("077123456") were stored without their dialling code. */
    private function toE164(string $phone): string
    {
        return preg_match('/^0\d{7,8}$/', $phone) ? '+241'.$phone : $phone;
    }
};
