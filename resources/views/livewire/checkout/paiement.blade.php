<div class="w-full">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-16">
        <div class="lg:col-span-7 xl:col-span-8 flex flex-col gap-6">
            <div class="mb-4">
                <h2 class="font-headline-md text-headline-md text-primary mb-2">Moyen de paiement</h2>
                <p class="font-body-md text-body-md text-on-surface-variant">Toutes les transactions sont sécurisées et cryptées.</p>
            </div>

            <div class="flex flex-col gap-4">
                <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-surface-container-lowest transition-colors shadow-sm {{ $paymentMethod === 'airtel' ? 'border-primary bg-surface-container-lowest' : 'border-outline-variant' }}">
                    <input wire:model.live="paymentMethod" value="airtel" type="radio" class="w-5 h-5 text-primary bg-surface border-outline-variant focus:ring-primary focus:ring-2 ml-2" />
                    <div class="flex items-center flex-1 ml-4">
                        <div class="w-12 h-8 rounded bg-surface flex items-center justify-center overflow-hidden border border-surface-variant mr-4">
                            <img src="{{ asset('img/mobilemoney-airtelmoney.png') }}" alt="Airtel Money" class="w-full h-full object-contain" />
                        </div>
                        <span class="font-body-lg text-body-lg text-primary font-medium">Airtel Money</span>
                    </div>
                </label>

                <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-surface-container-lowest transition-colors shadow-sm {{ $paymentMethod === 'moov' ? 'border-primary bg-surface-container-lowest' : 'border-outline-variant' }}">
                    <input wire:model.live="paymentMethod" value="moov" type="radio" class="w-5 h-5 text-primary bg-surface border-outline-variant focus:ring-primary focus:ring-2 ml-2" />
                    <div class="flex items-center flex-1 ml-4">
                        <div class="w-12 h-8 rounded bg-surface flex items-center justify-center overflow-hidden border border-surface-variant mr-4">
                            <img src="{{ asset('img/mobilemoneyymoov-mooney.png') }}" alt="Moov Money" class="w-full h-full object-contain" />
                        </div>
                        <span class="font-body-lg text-body-lg text-primary font-medium">Moov Money</span>
                    </div>
                </label>

                <div class="border rounded-lg overflow-hidden shadow-sm transition-colors {{ $paymentMethod === 'card' ? 'border-primary' : 'border-outline-variant' }}">
                    <label class="relative flex items-center p-4 cursor-pointer hover:bg-surface-container-lowest bg-surface">
                        <input wire:model.live="paymentMethod" value="card" type="radio" class="w-5 h-5 text-primary bg-surface border-outline-variant focus:ring-primary focus:ring-2 ml-2" />
                        <div class="flex items-center justify-between flex-1 ml-4">
                            <span class="font-body-lg text-body-lg text-primary font-medium">Carte Bancaire</span>
                            <img src="{{ asset('img/visa-mastercard.jpg') }}" alt="Visa / Mastercard" class="h-6 w-auto object-contain rounded" />
                        </div>
                    </label>
                    @if($paymentMethod === 'card')
                        <div class="p-6 bg-surface-container-lowest border-t border-outline-variant flex flex-col gap-4">
                            <div class="relative">
                                <input placeholder="Numéro de carte" type="text" class="block w-full px-0 py-3 border-0 border-b border-outline-variant bg-transparent text-primary placeholder-on-surface-variant focus:ring-0 focus:border-secondary font-body-md text-body-md" />
<x-icon name="lock" class="absolute right-0 top-3 text-primary text-[20px]" />
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <input placeholder="MM / AA" type="text" class="block w-full px-0 py-3 border-0 border-b border-outline-variant bg-transparent text-primary placeholder-on-surface-variant focus:ring-0 focus:border-secondary font-body-md text-body-md" />
                                <input placeholder="Code CVC" type="text" class="block w-full px-0 py-3 border-0 border-b border-outline-variant bg-transparent text-primary placeholder-on-surface-variant focus:ring-0 focus:border-secondary font-body-md text-body-md" />
                            </div>
                            <input placeholder="Nom sur la carte" type="text" class="block w-full px-0 py-3 border-0 border-b border-outline-variant bg-transparent text-primary placeholder-on-surface-variant focus:ring-0 focus:border-secondary font-body-md text-body-md" />
                        </div>
                    @endif
                </div>
            </div>

            @if($error)
                <p class="text-error font-label-sm text-label-sm">{{ $error }}</p>
            @endif
        </div>

        <div class="lg:col-span-5 xl:col-span-4 relative">
            <div class="bg-surface-container-lowest rounded-xl shadow-atmospheric p-6 md:p-8 flex flex-col gap-6">
                <h3 class="font-headline-md text-headline-md text-primary border-b border-surface-variant pb-4">Résumé de la commande</h3>
                <div class="flex flex-col gap-3 font-body-md text-body-md">
                    <div class="flex justify-between text-on-surface-variant">
                        <span>Sous-total ({{ $cart->items->count() }} article{{ $cart->items->count() > 1 ? 's' : '' }})</span>
                        <span class="text-primary">{{ number_format($subtotal, 0, ',', ' ') }} FCFA</span>
                    </div>
                    @if($discount > 0)
                        <div class="flex justify-between text-on-surface-variant">
                            <span>Réduction</span>
                            <span class="text-primary">-{{ number_format($discount, 0, ',', ' ') }} FCFA</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-on-surface-variant">
                        <span>Livraison</span>
                        <span class="text-primary">{{ $deliveryFee > 0 ? number_format($deliveryFee, 0, ',', ' ').' FCFA' : 'Gratuit' }}</span>
                    </div>
                </div>
                <div class="border-t border-surface-variant pt-4 flex justify-between items-end">
                    <span class="font-body-lg text-body-lg text-primary">Total</span>
                    <span class="font-headline-lg text-headline-lg text-primary">{{ number_format($total, 0, ',', ' ') }} FCFA</span>
                </div>
                <button
                    type="button"
                    wire:click="confirmAndPay"
                    wire:loading.attr="disabled"
                    class="w-full bg-primary text-on-primary py-4 rounded font-label-sm text-label-sm uppercase tracking-wider hover:opacity-90 active:scale-[0.98] transition-all flex items-center justify-center gap-2 mt-4 min-h-[48px] disabled:opacity-60"
                >
<x-icon name="lock" class="text-[20px]" />
                    <span wire:loading.remove wire:target="confirmAndPay">Confirmer et payer</span>
                    <span wire:loading wire:target="confirmAndPay">Traitement en cours…</span>
                </button>
                <div class="flex flex-col items-center justify-center mt-6 gap-2 opacity-80">
                    <div class="flex items-center gap-2 text-on-surface-variant">
<x-icon name="verified_user" class="text-[18px] text-primary" />
                        <span class="font-label-sm text-[10px] uppercase">Paiement 100% Sécurisé</span>
                    </div>
                    <p class="text-[11px] text-center text-outline leading-tight max-w-[240px]">
                        Vos données de paiement sont cryptées par un certificat SSL 256-bits de niveau bancaire.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
