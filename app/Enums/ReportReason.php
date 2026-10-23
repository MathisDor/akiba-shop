<?php

namespace App\Enums;

enum ReportReason: string
{
    case Arnaque = 'arnaque';
    case Spam = 'spam';
    case Interdit = 'interdit';
    case MauvaiseCategorie = 'mauvaise_categorie';
    case DejaVendu = 'deja_vendu';
    case Autre = 'autre';

    public function label(): string
    {
        return match ($this) {
            self::Arnaque => 'Arnaque ou fraude',
            self::Spam => 'Spam ou doublon',
            self::Interdit => 'Produit ou service interdit',
            self::MauvaiseCategorie => 'Mauvaise catégorie',
            self::DejaVendu => 'Déjà vendu / plus disponible',
            self::Autre => 'Autre raison',
        };
    }
}
