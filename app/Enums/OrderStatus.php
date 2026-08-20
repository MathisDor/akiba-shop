<?php

namespace App\Enums;

enum OrderStatus: string
{
    case EnAttentePaiement = 'en_attente_paiement';
    case Payee = 'payee';
    case Preparation = 'preparation';
    case PreteAExpedier = 'prete_a_expedier';
    case EnLivraison = 'en_livraison';
    case Livree = 'livree';
    case Terminee = 'terminee';
    case Annulee = 'annulee';
    case EchecPaiement = 'echec_paiement';
    case Retour = 'retour';
    case Remboursee = 'remboursee';

    public function label(): string
    {
        return match ($this) {
            self::EnAttentePaiement => 'En attente de paiement',
            self::Payee => 'Payée',
            self::Preparation => 'En préparation',
            self::PreteAExpedier => 'Prête à être expédiée',
            self::EnLivraison => 'En livraison',
            self::Livree => 'Livrée',
            self::Terminee => 'Terminée',
            self::Annulee => 'Annulée',
            self::EchecPaiement => 'Échec du paiement',
            self::Retour => 'Retour',
            self::Remboursee => 'Remboursée',
        };
    }

    /** Statuses that count as "in progress" for the customer order filters. */
    public static function inProgress(): array
    {
        return [
            self::EnAttentePaiement, self::Payee, self::Preparation,
            self::PreteAExpedier, self::EnLivraison,
        ];
    }

    /** Statuses that decrement stock when reached (payment confirmed). */
    public static function stockDeductingTrigger(): self
    {
        return self::Payee;
    }

    /** Statuses that restock (order cancelled after payment). */
    public static function restockingStatuses(): array
    {
        return [self::Annulee, self::Retour, self::Remboursee];
    }
}
