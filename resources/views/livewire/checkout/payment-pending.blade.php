@php
    $payment = $order->payments()->latest()->first();
    $operatorLabel = $order->payment_method === 'airtel' ? 'Airtel Money' : 'Moov Money';
@endphp

<div class="pt-4 flex items-center justify-center" wire:poll.4s="poll">
    <div class="w-full max-w-lg bg-surface-container-lowest rounded-xl shadow-[0_20px_60px_-15px_rgba(0,0,0,0.05)] p-8 md:p-12 flex flex-col items-center text-center">
        <div class="w-20 h-20 rounded-full bg-surface-container-high flex items-center justify-center mb-6">
            <svg class="animate-spin h-9 w-9 text-primary" viewBox="0 0 24 24" fill="none">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
        </div>

        <h1 class="font-headline-md text-headline-md md:font-headline-lg md:text-headline-lg text-primary mb-3">
            En attente de confirmation
        </h1>
        <p class="font-body-md text-body-md text-on-surface-variant mb-2">
            Une demande de paiement {{ $operatorLabel }} a été envoyée sur votre téléphone.
        </p>
        <p class="font-body-md text-body-md text-on-surface-variant mb-8">
            Validez le code USSD reçu pour confirmer votre paiement de
            <span class="font-bold text-primary">{{ number_format($order->total, 0, ',', ' ') }} FCFA</span>.
        </p>

        <div class="w-full bg-surface-container-low rounded-lg p-4 mb-8 text-left">
            <div class="flex justify-between font-label-sm text-label-sm text-on-surface-variant mb-1">
                <span>Commande</span>
                <span class="text-on-surface font-bold">#{{ $order->order_number }}</span>
            </div>
            <div class="flex justify-between font-label-sm text-label-sm text-on-surface-variant">
                <span>Statut</span>
                <span class="text-primary">En attente de paiement</span>
            </div>
        </div>

        <button type="button" wire:click="poll" wire:loading.attr="disabled" class="w-full bg-primary text-on-primary h-12 rounded-full font-label-sm text-label-sm uppercase tracking-widest hover:opacity-90 transition-opacity flex items-center justify-center">
            <span wire:loading.remove wire:target="poll">J'ai validé, vérifier</span>
            <span wire:loading wire:target="poll">Vérification…</span>
        </button>

        <p class="font-label-sm text-[11px] text-on-surface-variant mt-6">
            Cette page se met à jour automatiquement dès que le paiement est confirmé.
        </p>
    </div>
</div>
