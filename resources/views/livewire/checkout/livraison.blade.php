<div class="pt-4 w-full">
    <div class="grid grid-cols-1 md:grid-cols-12 gap-gutter">
        <div class="md:col-span-12 flex flex-col gap-10">
            <section>
                <h2 class="font-headline-md text-headline-md text-primary mb-6">Mode de livraison</h2>
                <div class="flex flex-col gap-4">
                    <label class="flex items-start gap-4 p-4 border rounded-lg cursor-pointer bg-surface hover:bg-surface-container-low transition-colors {{ $deliveryMethod === 'domicile' ? 'border-primary' : 'border-outline-variant' }}">
                        <input wire:model.live="deliveryMethod" value="domicile" type="radio" class="mt-1 text-primary focus:ring-primary" />
                        <div class="flex-1">
                            <div class="flex justify-between items-center mb-1">
                                <span class="font-body-md text-body-md font-semibold text-primary">Livraison à domicile</span>
                                <span class="font-label-sm text-label-sm font-bold text-primary">Selon zone</span>
                            </div>
                            <p class="font-label-sm text-label-sm text-on-surface-variant">Livraison standard en 7 à 14 jours.</p>
                        </div>
                    </label>
                    <label class="flex items-start gap-4 p-4 border rounded-lg cursor-pointer bg-surface hover:bg-surface-container-low transition-colors {{ $deliveryMethod === 'point_relais' ? 'border-primary' : 'border-outline-variant' }}">
                        <input wire:model.live="deliveryMethod" value="point_relais" type="radio" class="mt-1 text-primary focus:ring-primary" />
                        <div class="flex-1">
                            <div class="flex justify-between items-center mb-1">
                                <span class="font-body-md text-body-md font-semibold text-primary">Point Relais</span>
                                <span class="font-label-sm text-label-sm font-bold text-primary">Gratuit</span>
                            </div>
                            <p class="font-label-sm text-label-sm text-on-surface-variant">Retirez votre colis dans un de nos points partenaires.</p>
                        </div>
                    </label>
                    <label class="flex items-start gap-4 p-4 border rounded-lg cursor-pointer bg-surface hover:bg-surface-container-low transition-colors {{ $deliveryMethod === 'boutique' ? 'border-primary' : 'border-outline-variant' }}">
                        <input wire:model.live="deliveryMethod" value="boutique" type="radio" class="mt-1 text-primary focus:ring-primary" />
                        <div class="flex-1">
                            <div class="flex justify-between items-center mb-1">
                                <span class="font-body-md text-body-md font-semibold text-primary">Retrait en boutique</span>
                                <span class="font-label-sm text-label-sm font-bold text-primary">Gratuit</span>
                            </div>
                            <p class="font-label-sm text-label-sm text-on-surface-variant">Disponible sous 2h à la boutique de Libreville Centre.</p>
                        </div>
                    </label>
                </div>

                @if($deliveryMethod === 'domicile')
                    <div class="mt-4">
                        <label class="font-label-sm text-label-sm text-on-surface-variant">Zone de livraison</label>
                        <select wire:model.live="deliveryZoneId" class="w-full bg-surface border-b border-outline-variant py-2 px-0 text-primary focus:border-primary focus:ring-0 font-body-md text-body-md">
                            <option value="">Sélectionnez votre zone</option>
                            @foreach($this->deliveryZones as $zone)
                                <option value="{{ $zone->id }}">{{ $zone->zone_name }} — {{ number_format($zone->fee, 0, ',', ' ') }} FCFA</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                @if($error)
                    <p class="mt-3 text-error font-label-sm text-label-sm">{{ $error }}</p>
                @endif
            </section>

            <section>
                <h2 class="font-headline-md text-headline-md text-primary mb-6">Adresse de livraison</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="flex flex-col gap-1 md:col-span-2">
                        <label class="font-label-sm text-label-sm text-on-surface-variant">Nom complet</label>
                        <input wire:model="fullName" type="text" class="w-full bg-surface border-b border-outline-variant py-2 px-0 text-primary focus:border-primary focus:ring-0 font-body-md text-body-md" />
                        @error('fullName') <p class="text-error font-label-sm text-label-sm">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="font-label-sm text-label-sm text-on-surface-variant">Téléphone</label>
                        <input wire:model="phone" type="text" class="w-full bg-surface border-b border-outline-variant py-2 px-0 text-primary focus:border-primary focus:ring-0 font-body-md text-body-md" />
                        @error('phone') <p class="text-error font-label-sm text-label-sm">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="font-label-sm text-label-sm text-on-surface-variant">Province</label>
                        <select wire:model="province" class="w-full bg-surface border-b border-outline-variant py-2 px-0 text-primary focus:border-primary focus:ring-0 font-body-md text-body-md">
                            <option>Estuaire</option>
                            <option>Haut-Ogooué</option>
                            <option>Moyen-Ogooué</option>
                        </select>
                        @error('province') <p class="text-error font-label-sm text-label-sm">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="font-label-sm text-label-sm text-on-surface-variant">Ville</label>
                        <input wire:model="city" type="text" class="w-full bg-surface border-b border-outline-variant py-2 px-0 text-primary focus:border-primary focus:ring-0 font-body-md text-body-md" />
                        @error('city') <p class="text-error font-label-sm text-label-sm">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="font-label-sm text-label-sm text-on-surface-variant">Quartier</label>
                        <input wire:model="commune" type="text" placeholder="Ex: Louis, Batterie IV..." class="w-full bg-surface border-b border-outline-variant py-2 px-0 text-primary focus:border-primary focus:ring-0 font-body-md text-body-md" />
                    </div>
                    <div class="flex flex-col gap-1 md:col-span-2">
                        <label class="font-label-sm text-label-sm text-on-surface-variant">Adresse détaillée</label>
                        <input wire:model="detailedAddress" type="text" placeholder="Ex: Immeuble, rue, numéro..." class="w-full bg-surface border-b border-outline-variant py-2 px-0 text-primary focus:border-primary focus:ring-0 font-body-md text-body-md" />
                        @error('detailedAddress') <p class="text-error font-label-sm text-label-sm">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex flex-col gap-1 md:col-span-2">
                        <label class="font-label-sm text-label-sm text-on-surface-variant">Point de repère / Précisions</label>
                        <textarea wire:model="landmark" rows="2" placeholder="Ex: Portail noir à côté de la pharmacie..." class="w-full bg-surface border-b border-outline-variant py-2 px-0 text-primary focus:border-primary focus:ring-0 font-body-md text-body-md resize-none"></textarea>
                    </div>
                </div>
            </section>
        </div>

        <div class="md:col-span-12 mt-4">
            <div class="bg-surface-container-low rounded-xl p-6 shadow-[0_10px_40px_rgba(0,0,0,0.03)]">
                <h3 class="font-headline-md text-headline-md text-primary mb-4 border-b border-outline-variant pb-4">Résumé</h3>
                <div class="flex flex-col gap-2 pt-2 mb-6 font-label-sm text-label-sm">
                    <div class="flex justify-between text-on-surface-variant">
                        <span>Sous-total</span>
                        <span>{{ number_format($subtotal, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="flex justify-between text-on-surface-variant">
                        <span>Livraison</span>
                        <span class="text-primary">{{ $this->deliveryFee > 0 ? number_format($this->deliveryFee, 0, ',', ' ').' FCFA' : 'Gratuit' }}</span>
                    </div>
                </div>
                <div class="flex justify-between items-end mb-8">
                    <span class="font-headline-md text-headline-md text-primary font-bold">Total</span>
                    <span class="font-headline-md text-headline-md text-primary font-bold">{{ number_format($subtotal + $this->deliveryFee, 0, ',', ' ') }} FCFA</span>
                </div>
                <button type="button" wire:click="continue" class="w-full bg-primary text-on-primary py-4 rounded-full font-label-sm text-label-sm uppercase tracking-wider hover:opacity-90 transition-opacity h-[48px] flex items-center justify-center">
                    Continuer vers le paiement
                </button>
            </div>
        </div>
    </div>
</div>
