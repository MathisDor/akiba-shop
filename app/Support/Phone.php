<?php

namespace App\Support;

/**
 * International phone numbers, stored in E.164 ("+33612345678") and turned
 * into tel:/wa.me links. Deliberately light (no libphonenumber): it handles
 * the trunk "0" and the dialling code, and checks the overall length.
 */
class Phone
{
    /** E.164 number, or null when the input can't be a valid phone number. */
    public static function normalize(?string $input, ?string $countryCode = null): ?string
    {
        $input = trim((string) $input);
        $digits = preg_replace('/\D+/', '', $input);

        if (str_starts_with($input, '+')) {
            // Already international.
        } elseif (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        } else {
            $country = config('countries.'.strtoupper((string) ($countryCode ?: config('marketplace.default_country'))));
            if (! $country) {
                return null;
            }
            [, $dial, $keepsZero] = $country;
            $national = $keepsZero ? $digits : preg_replace('/^0/', '', $digits);
            $digits = $dial.$national;
        }

        return preg_match('/^[1-9]\d{7,14}$/', $digits) ? '+'.$digits : null;
    }

    /** ISO code of the country whose dialling code prefixes this number (longest match). */
    public static function countryOf(string $e164): ?string
    {
        $digits = ltrim($e164, '+');
        $match = null;
        $matchLength = 0;

        foreach (config('countries') as $code => [, $dial]) {
            if (str_starts_with($digits, $dial) && strlen($dial) > $matchLength) {
                [$match, $matchLength] = [$code, strlen($dial)];
            }
        }

        return $match;
    }

    /** "+33612345678" → "+33 6 12 34 56 78" */
    public static function format(string $e164): string
    {
        $country = self::countryOf($e164);
        if (! $country) {
            return $e164;
        }

        $dial = config("countries.$country.1");
        $rest = substr(ltrim($e164, '+'), strlen($dial));
        $head = strlen($rest) % 2 === 1 ? substr($rest, 0, 1) : '';
        $pairs = str_split(substr($rest, strlen($head)), 2);

        return trim('+'.$dial.' '.$head.' '.implode(' ', $pairs));
    }

    /** The number as the member would type it locally, for pre-filling forms. */
    public static function national(string $e164): string
    {
        $country = self::countryOf($e164);
        if (! $country) {
            return $e164;
        }

        [, $dial, $keepsZero] = config("countries.$country");
        $rest = substr(ltrim($e164, '+'), strlen($dial));

        return $keepsZero ? $rest : '0'.$rest;
    }

    public static function telUrl(string $e164): string
    {
        return 'tel:'.$e164;
    }

    public static function whatsappUrl(string $e164, string $message = ''): string
    {
        $url = 'https://wa.me/'.ltrim($e164, '+');

        return $message === '' ? $url : $url.'?text='.rawurlencode($message);
    }
}
