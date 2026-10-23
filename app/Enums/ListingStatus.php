<?php

namespace App\Enums;

enum ListingStatus: string
{
    case EnAttente = 'en_attente';
    case Publiee = 'publiee';
    case Refusee = 'refusee';
    case Vendue = 'vendue';

    public function label(): string
    {
        return match ($this) {
            self::EnAttente => 'En attente de validation',
            self::Publiee => 'En ligne',
            self::Refusee => 'Refusée',
            self::Vendue => 'Vendue',
        };
    }

    /** Tailwind classes for the status pill (account + admin lists). */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::EnAttente => 'bg-tertiary-container text-on-tertiary-container',
            self::Publiee => 'bg-primary-fixed text-on-primary-fixed-variant',
            self::Refusee => 'bg-error-container text-on-error-container',
            self::Vendue => 'bg-secondary-container text-on-secondary-container',
        };
    }
}
