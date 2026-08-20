<div>
    @if($product->sizes->isNotEmpty())
        <div class="mb-8">
            <div class="flex justify-between mb-3">
                <span class="font-body-md text-body-md font-bold text-on-surface">Taille:</span>
                <span class="font-label-sm text-label-sm text-on-surface-variant">Guide des tailles</span>
            </div>
            <div class="grid grid-cols-5 gap-2">
                @foreach($product->sizes->sortBy('sort_order') as $size)
                    @php
                        $variant = $product->variants->first(fn($v) => $v->color_id === $selectedColorId && $v->size_id === $size->id);
                        $available = $variant && $variant->stock > 0;
                    @endphp
                    <button
                        type="button"
                        @if($available) wire:click="selectSize({{ $size->id }})" @endif
                        @disabled(!$available)
                        class="py-3 border rounded font-body-md text-body-md transition-colors
                            {{ $selectedSizeId === $size->id ? 'border-primary bg-primary text-on-primary font-bold' : 'border-outline-variant text-on-surface hover:border-primary' }}
                            {{ !$available ? 'text-outline-variant cursor-not-allowed opacity-50 hover:border-outline-variant' : '' }}"
                    >
                        {{ $size->name }}
                    </button>
                @endforeach
            </div>

            @if($this->currentVariant)
                <p class="mt-3 font-label-sm text-label-sm {{ $this->currentVariant->stock <= 5 ? 'text-error' : 'text-secondary' }} flex items-center gap-1">
<x-icon name="info" class="text-[16px]" />
                    @if($this->currentVariant->stock > 0)
                        En stock - {{ $this->currentVariant->stock }} restant{{ $this->currentVariant->stock > 1 ? 's' : '' }}
                    @else
                        Rupture de stock
                    @endif
                </p>
            @endif
        </div>
    @endif

    @if($feedback)
        <p class="mb-4 font-label-sm text-label-sm {{ $success ? 'text-primary' : 'text-error' }}">{{ $feedback }}</p>
    @endif

    <div class="flex flex-col gap-3 mb-10">
        <button
            type="button"
            wire:click="addToCart"
            wire:loading.attr="disabled"
            class="w-full bg-primary text-on-primary py-4 rounded-lg font-body-lg text-body-lg font-bold hover:bg-on-surface-variant transition-colors flex justify-center items-center gap-2 h-14 disabled:opacity-60"
        >
            <span wire:loading.remove wire:target="addToCart">Ajouter au panier</span>
            <span wire:loading wire:target="addToCart">Ajout en cours…</span>
        </button>
        <button
            type="button"
            wire:click="addToCart('buy_now')"
            class="w-full bg-transparent border-2 border-primary text-primary py-4 rounded-lg font-body-lg text-body-lg font-bold hover:bg-surface-variant transition-colors h-14"
        >
            Acheter maintenant
        </button>
    </div>
</div>
