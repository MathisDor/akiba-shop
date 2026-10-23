<?php

namespace App\Enums;

enum ReportStatus: string
{
    case Ouvert = 'ouvert';
    case Traite = 'traite';
    case Rejete = 'rejete';

    public function label(): string
    {
        return match ($this) {
            self::Ouvert => 'À traiter',
            self::Traite => 'Traité',
            self::Rejete => 'Classé sans suite',
        };
    }
}
