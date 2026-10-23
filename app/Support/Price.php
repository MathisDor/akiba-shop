<?php

namespace App\Support;

/** Formats whole amounts in the site currency (config marketplace.currency). */
class Price
{
    public static function format(int $amount): string
    {
        $currency = config('marketplace.currency');
        $number = number_format($amount, 0, ',', ' ');

        return $currency['symbol_first'] ? $currency['symbol'].' '.$number : $number.' '.$currency['symbol'];
    }

    public static function symbol(): string
    {
        return config('marketplace.currency.symbol');
    }
}
