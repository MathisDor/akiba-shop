<?php

namespace App\Enums;

enum ListingCondition: string
{
    case Neuf = 'neuf';
    case Occasion = 'occasion';

    public function label(): string
    {
        return match ($this) {
            self::Neuf => 'Neuf',
            self::Occasion => 'Occasion',
        };
    }
}
