<x-layouts.app active="account" :title="'Commande #'.$order->order_number.' — AkibaShop'">
    <div class="max-w-3xl mx-auto px-margin-mobile md:px-margin-desktop py-10">
        <a href="{{ route('account.orders') }}" class="inline-flex items-center gap-2 font-label-sm text-label-sm text-on-surface-variant hover:text-primary transition-colors mb-6">
            <x-icon name="arrow_back" class="text-[18px]" /> Retour aux commandes
        </a>

        <div class="flex flex-wrap justify-between items-start gap-4 mb-10">
            <div>
                <h1 class="font-headline-lg text-headline-lg text-primary mb-1">Commande #{{ $order->order_number }}</h1>
                <p class="font-body-md text-body-md text-on-surface-variant">Passée le {{ $order->created_at->translatedFormat('d F Y à H:i') }}</p>
            </div>
            <span class="px-4 py-2 rounded-full bg-primary/10 text-primary font-label-sm text-label-sm uppercase tracking-wider">{{ $order->statusLabel() }}</span>
        </div>

        <section class="mb-12">
            <h2 class="font-headline-md text-headline-md text-primary mb-6">Suivi de la commande</h2>
            <div class="relative border-l border-outline-variant ml-2 space-y-6">
                @foreach($order->statusHistories as $history)
                    <div class="relative pl-8">
                        <div class="absolute left-[-9px] top-1 w-4 h-4 rounded-full {{ $loop->last ? 'bg-primary' : 'bg-surface-container-high border-2 border-outline-variant' }}"></div>
                        <h3 class="font-label-sm text-label-sm text-primary uppercase tracking-widest mb-1">{{ \App\Enums\OrderStatus::from($history->status)->label() }}</h3>
                        <p class="font-body-md text-body-md text-on-surface-variant">{{ $history->created_at->translatedFormat('d F Y à H:i') }}</p>
                        @if($history->note)
                            <p class="font-body-md text-body-md text-on-surface-variant">{{ $history->note }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>

        <section class="mb-12">
            <h2 class="font-headline-md text-headline-md text-primary mb-6">Articles</h2>
            <div class="flex flex-col gap-4">
                @foreach($order->items as $item)
                    @php($img = $item->variant?->product?->images->first())
                    <div class="flex gap-4 p-4 bg-surface-container-lowest rounded-xl">
                        <div class="w-20 h-24 rounded-lg overflow-hidden bg-surface-variant flex-shrink-0">
                            @if($img)
                                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($img->path) }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover" />
                            @endif
                        </div>
                        <div class="flex-grow flex flex-col justify-center">
                            <h3 class="font-body-lg text-body-lg text-primary">{{ $item->product_name }}</h3>
                            <p class="font-body-md text-body-md text-on-surface-variant">
                                @if($item->size_name) Taille: {{ $item->size_name }} @endif
                                @if($item->color_name) | Couleur: {{ $item->color_name }} @endif
                                | Qté: {{ $item->quantity }}
                            </p>
                            <p class="font-body-md text-body-md font-bold text-primary mt-1">{{ number_format($item->lineTotal(), 0, ',', ' ') }} FCFA</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
            <section>
                <h2 class="font-headline-md text-headline-md text-primary mb-4">Livraison</h2>
                <div class="bg-surface-container-low rounded-xl p-6 font-body-md text-body-md text-on-surface-variant">
                    <p class="text-on-surface font-semibold mb-1">{{ $order->address_snapshot['full_name'] ?? '' }}</p>
                    <p>{{ $order->address_snapshot['detailed_address'] ?? '' }}@if(!empty($order->address_snapshot['commune'])), {{ $order->address_snapshot['commune'] }}@endif</p>
                    <p>{{ $order->address_snapshot['city'] ?? '' }}, {{ $order->address_snapshot['province'] ?? '' }}</p>
                    <p class="mt-2">{{ $order->address_snapshot['phone'] ?? '' }}</p>
                    <p class="mt-2 capitalize">Mode: {{ str_replace('_', ' ', $order->delivery_method) }}</p>
                </div>
            </section>
            <section>
                <h2 class="font-headline-md text-headline-md text-primary mb-4">Paiement</h2>
                <div class="bg-surface-container-low rounded-xl p-6 font-body-md text-body-md text-on-surface-variant flex flex-col gap-2">
                    <div class="flex justify-between"><span>Sous-total</span><span>{{ number_format($order->subtotal, 0, ',', ' ') }} FCFA</span></div>
                    @if($order->discount > 0)
                        <div class="flex justify-between text-primary"><span>Réduction</span><span>-{{ number_format($order->discount, 0, ',', ' ') }} FCFA</span></div>
                    @endif
                    <div class="flex justify-between"><span>Livraison</span><span>{{ number_format($order->delivery_fee, 0, ',', ' ') }} FCFA</span></div>
                    <div class="flex justify-between font-bold text-on-surface border-t border-outline-variant pt-2 mt-2"><span>Total</span><span>{{ number_format($order->total, 0, ',', ' ') }} FCFA</span></div>
                    <p class="mt-2 uppercase text-[11px] tracking-widest">{{ $order->payment_method }}</p>
                </div>
            </section>
        </div>
    </div>
</x-layouts.app>
