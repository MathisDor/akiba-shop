<?php

namespace App\Support;

/**
 * Gabonese phone numbers have no trunk prefix: the leading 0 is part of the
 * number (077 12 34 56 → +241 077 12 34 56). We store the national digits only
 * and derive tel:/wa.me links from them.
 */
class GabonPhone
{
    public const COUNTRY_CODE = '241';

    /** National digits (8 or 9, starting with 0), or null when not a valid Gabonese number. */
    public static function normalize(?string $input): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $input);

        if (str_starts_with($digits, '00'.self::COUNTRY_CODE)) {
            $digits = substr($digits, 5);
        } elseif (str_starts_with($digits, self::COUNTRY_CODE) && strlen($digits) > 9) {
            $digits = substr($digits, 3);
        }

        // Numbers typed without their leading 0 (e.g. "77 12 34 56").
        if (strlen($digits) === 8 && ! str_starts_with($digits, '0')) {
            $digits = '0'.$digits;
        }

        return preg_match('/^0\d{7,8}$/', $digits) ? $digits : null;
    }

    public static function international(string $national): string
    {
        return self::COUNTRY_CODE.$national;
    }

    /** "077123456" → "077 12 34 56" */
    public static function format(string $national): string
    {
        $head = substr($national, 0, strlen($national) - 6);

        return trim($head.' '.implode(' ', str_split(substr($national, -6), 2)));
    }

    public static function telUrl(string $national): string
    {
        return 'tel:+'.self::international($national);
    }

    public static function whatsappUrl(string $national, string $message = ''): string
    {
        $url = 'https://wa.me/'.self::international($national);

        return $message === '' ? $url : $url.'?text='.rawurlencode($message);
    }
}
