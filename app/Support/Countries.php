<?php

namespace App\Support;

/** Country list from config/countries.php, as code => French name. */
class Countries
{
    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(config('countries'))->map(fn (array $country) => $country[0])->all();
    }

    public static function name(?string $code): ?string
    {
        return $code ? config('countries.'.strtoupper($code).'.0') : null;
    }

    public static function exists(?string $code): bool
    {
        return $code !== null && config('countries.'.strtoupper($code)) !== null;
    }
}
