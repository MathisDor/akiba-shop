<div>
    <main class="flex-grow pb-32 px-margin-mobile md:px-margin-desktop max-w-container-max mx-auto w-full">
        <div class="mb-8">
            <h1 class="font-headline-md text-headline-md md:font-headline-lg md:text-headline-lg text-primary">
                Mon Panier <span class="text-on-surface-variant font-body-md text-body-md ml-2">({{ $cart->items->count() }} article{{ $cart->items->count() > 1 ? 's' : '' }})</span>
            </h1>
        </div>

        @if($cart->items->isEmpty())
            <div class="flex flex-col items-center justify-center text-center py-20 px-4">
                <div class="w-24 h-24 rounded-full bg-surface-container-high flex items-center justify-center mb-6">
<x-icon name="shopping_basket" class="text-4xl text-on-surface-variant" />
                </div>
                <h2 class="font-headline-md text-headline-md text-primary mb-2">Votre panier est vide</h2>
                <p class="font-body-md text-body-md text-on-surface-variant mb-8 max-w-sm">Découvrez nos nouvelles collections et trouvez les pièces parfaites pour votre style.</p>
                <a href="{{ route('catalog.index') }}" class="px-8 h-12 border border-primary text-primary font-label-sm text-label-sm rounded-full uppercase tracking-wider hover:bg-primary hover:text-on-primary transition-colors flex items-center">
                    Continuer mes achats
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter lg:gap-8">
                <div class="lg:col-span-8 flex flex-col gap-6">
                    @foreach($cart->items as $item)
                        <div wire:key="cart-item-{{ $item->id }}" class="flex gap-4 p-4 bg-surface-container-lowest rounded-xl shadow-[0px_4px_20px_rgba(0,0,0,0.04)] relative">
                            <div class="w-24 h-32 md:w-32 md:h-40 flex-shrink-0 rounded-lg overflow-hidden bg-surface-container">
                                @php($img = $item->variant->product->images->first())
                                @if($img)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($img->path) }}" alt="{{ $item->variant->product->name }}" class="w-full h-full object-cover" />
                                @endif
                            </div>
                            <div class="flex-grow flex flex-col justify-between">
                                <div>
                                    <a href="{{ route('products.show', $item->variant->product) }}" class="font-headline-md text-headline-md text-primary mb-1 line-clamp-1 hover:underline">{{ $item->variant->product->name }}</a>
                                    <p class="font-body-md text-body-md text-on-surface-variant mb-2">
                                        Taille: {{ $item->variant->size->name ?? 'Unique' }} | Couleur: {{ $item->variant->color->name ?? '—' }}
                                    </p>
                                    <p class="font-headline-md text-headline-md font-bold text-primary">{{ number_format($item->variant->price, 0, ',', ' ') }} FCFA</p>
                                </div>
                                <div class="flex items-center justify-between mt-4">
                                    <div class="flex items-center border border-outline-variant rounded-full px-2 py-1 bg-surface">
                                        <button type="button" wire:click="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})" class="w-8 h-8 flex items-center justify-center text-on-surface-variant hover:text-primary transition-colors">
<x-icon name="remove" class="text-[20px]" />
                                        </button>
                                        <span class="font-label-sm text-label-sm px-4">{{ $item->quantity }}</span>
                                        <button type="button" wire:click="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})" @disabled($item->quantity >= $item->variant->stock) class="w-8 h-8 flex items-center justify-center text-on-surface-variant hover:text-primary transition-colors disabled:opacity-40">
<x-icon name="add" class="text-[20px]" />
                                        </button>
                                    </div>
                                    <button type="button" wire:click="removeItem({{ $item->id }})" class="text-error hover:opacity-70 p-2">
<x-icon name="delete" class="text-[20px]" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="lg:col-span-4 mt-8 lg:mt-0">
                    <div class="bg-surface-container-lowest rounded-xl p-6 shadow-[0px_4px_20px_rgba(0,0,0,0.04)] sticky top-28">
                        <h3 class="font-headline-md text-headline-md text-primary mb-6 border-b border-outline-variant pb-4">Résumé de la commande</h3>
                        <div class="flex flex-col gap-4 mb-6">
                            <div class="flex justify-between font-body-md text-body-md text-on-surface-variant">
                                <span>Sous-total</span>
                                <span>{{ number_format($subtotal, 0, ',', ' ') }} FCFA</span>
                            </div>
                            @if($discount > 0)
                                <div class="flex justify-between font-body-md text-body-md text-primary">
                                    <span>Réduction ({{ session('cart.coupon_code') }})</span>
                                    <span>-{{ number_format($discount, 0, ',', ' ') }} FCFA</span>
                                </div>
                            @endif
                            <div class="flex justify-between font-body-md text-body-md text-on-surface-variant">
                                <span>Livraison</span>
                                <span>Calculée à l'étape suivante</span>
                            </div>
                        </div>

                        <div class="mb-6 pb-6 border-b border-outline-variant">
                            <label class="block font-label-sm text-label-sm text-on-surface-variant mb-2">Code promo</label>
                            <div class="flex gap-2">
                                <input wire:model="couponCode" type="text" placeholder="Entrez votre code" class="flex-grow bg-transparent border-b border-outline-variant focus:border-primary focus:ring-0 px-0 py-2 font-body-md text-primary placeholder-outline" />
                                <button type="button" wire:click="applyCoupon" class="font-label-sm text-label-sm text-primary hover:text-primary-container transition-colors uppercase px-2">Appliquer</button>
                            </div>
                            @if($couponError)
                                <p class="mt-2 font-label-sm text-label-sm text-error">{{ $couponError }}</p>
                            @endif
                        </div>

                        <div class="flex justify-between items-end mb-8">
                            <span class="font-headline-md text-headline-md text-primary">Total</span>
                            <span class="font-headline-md text-headline-md font-bold text-primary">{{ number_format($total, 0, ',', ' ') }} FCFA</span>
                        </div>

                        <button type="button" wire:click="checkout" class="hidden md:flex w-full bg-primary text-on-primary font-label-sm text-label-sm h-12 rounded-full items-center justify-center uppercase tracking-wider hover:bg-primary-container transition-colors shadow-sm">
                            Passer la commande
                        </button>
                    </div>
                </div>
            </div>

            <div class="md:hidden fixed bottom-[80px] left-0 w-full bg-surface/95 backdrop-blur-lg border-t border-outline-variant p-4 z-40 shadow-[0_-4px_20px_rgba(0,0,0,0.05)]">
                <div class="flex justify-between items-center mb-3">
                    <span class="font-body-md text-body-md text-on-surface-variant">Total</span>
                    <span class="font-headline-md text-headline-md font-bold text-primary">{{ number_format($total, 0, ',', ' ') }} FCFA</span>
                </div>
                <button type="button" wire:click="checkout" class="w-full bg-primary text-on-primary font-label-sm text-label-sm h-12 rounded-full flex items-center justify-center uppercase tracking-wider hover:bg-primary-container transition-colors shadow-md">
                    Passer la commande
                </button>
            </div>
        @endif

        @if($recommended->isNotEmpty())
            <section class="mt-16 pt-12 border-t border-outline-variant">
                <h2 class="font-headline-md text-headline-md text-on-surface mb-8">Vous pourriez aussi aimer</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
                    @foreach($recommended as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>
            </section>
        @endif
    </main>
</div>
