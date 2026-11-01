<?php

namespace App\Support;

/** Gabonese provinces from config/marketplace.php, as slug => name. */
class Provinces
{
    /** @return array<string, string> */
    public static function options(): array
    {
        return config('marketplace.provinces');
    }

    public static function name(?string $slug): ?string
    {
        return $slug ? (config('marketplace.provinces')[$slug] ?? null) : null;
    }

    public static function exists(?string $slug): bool
    {
        return $slug !== null && array_key_exists($slug, config('marketplace.provinces'));
    }
}
