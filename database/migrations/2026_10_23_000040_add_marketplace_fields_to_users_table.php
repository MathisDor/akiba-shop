<?php

use App\Support\Phone;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('city_id')->nullable()->after('phone')->constrained()->nullOnDelete();
            $table->boolean('is_pro')->default(false)->after('city_id');
            $table->boolean('is_verified')->default(false)->after('is_pro');
        });

        // Phones are stored normalised so members can sign in with them.
        DB::table('users')->whereNotNull('phone')->orderBy('id')->each(function ($user) {
            $normalized = Phone::normalize($user->phone, 'GA');
            if ($normalized !== null && $normalized !== $user->phone) {
                DB::table('users')->where('id', $user->id)->update(['phone' => $normalized]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('city_id');
            $table->dropColumn(['is_pro', 'is_verified']);
        });
    }
};
