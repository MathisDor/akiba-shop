<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Listings are located by Gabonese province again, and members can chat
 * about a listing (one conversation per listing and buyer).
 */
return new class extends Migration
{
    /** Main towns of each province, used to backfill existing rows. */
    private const TOWNS = [
        'estuaire' => ['libreville', 'owendo', 'akanda', 'ntoum', 'kango', 'cocobeach'],
        'haut-ogooue' => ['franceville', 'moanda', 'mounana', 'okondja', 'akieni', 'leconi'],
        'moyen-ogooue' => ['lambarene', 'ndjole'],
        'ngounie' => ['mouila', 'ndende', 'fougamou', 'mimongo', 'mbigou'],
        'nyanga' => ['tchibanga', 'mayumba'],
        'ogooue-ivindo' => ['makokou', 'booue', 'mekambo', 'ovan'],
        'ogooue-lolo' => ['koulamoutou', 'lastoursville', 'pana'],
        'ogooue-maritime' => ['port-gentil', 'omboue', 'gamba'],
        'woleu-ntem' => ['oyem', 'bitam', 'mitzic', 'minvoul', 'medouneu'],
    ];

    public function up(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->string('province', 40)->nullable()->after('country_code')->index();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('province', 40)->nullable()->after('country_code');
        });

        foreach (['listings', 'users'] as $table) {
            DB::table($table)->whereNotNull('city')->orderBy('id')->each(function ($row) use ($table) {
                if ($province = $this->provinceOf((string) $row->city)) {
                    DB::table($table)->where('id', $row->id)->update(['province' => $province]);
                }
            });
        }

        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('last_message_at')->nullable()->index();
            $table->timestamps();

            $table->unique(['listing_id', 'buyer_id']);
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->text('body');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['conversation_id', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
        Schema::dropIfExists('conversations');

        Schema::table('users', fn (Blueprint $table) => $table->dropColumn('province'));
        Schema::table('listings', fn (Blueprint $table) => $table->dropColumn('province'));
    }

    private function provinceOf(string $city): ?string
    {
        $slug = Str::slug($city);

        foreach (self::TOWNS as $province => $towns) {
            if (in_array($slug, $towns, true)) {
                return $province;
            }
        }

        return null;
    }
};
