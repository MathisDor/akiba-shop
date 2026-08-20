<x-layouts.app active="account" title="Mes Commandes — AkibaShop">
    <div class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop py-8">
        <div class="mb-section-gap flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div>
                <h1 class="font-headline-xl text-headline-xl text-primary mb-2">Historique des commandes</h1>
                <p class="font-body-lg text-body-lg text-on-surface-variant">Suivez et gérez vos achats récents.</p>
            </div>
            <div class="flex gap-3 overflow-x-auto pb-2 hide-scrollbar">
                <a href="{{ route('account.orders') }}" class="px-6 py-2 rounded-full font-label-sm text-label-sm whitespace-nowrap transition-colors {{ !request('filter') ? 'bg-primary text-on-primary' : 'border border-outline-variant text-on-surface hover:bg-surface-variant' }}">Toutes ({{ $counts['all'] }})</a>
                <a href="{{ route('account.orders', ['filter' => 'in_progress']) }}" class="px-6 py-2 rounded-full font-label-sm text-label-sm whitespace-nowrap transition-colors {{ request('filter') === 'in_progress' ? 'bg-primary text-on-primary' : 'border border-outline-variant text-on-surface hover:bg-surface-variant' }}">En cours ({{ $counts['in_progress'] }})</a>
                <a href="{{ route('account.orders', ['filter' => 'delivered']) }}" class="px-6 py-2 rounded-full font-label-sm text-label-sm whitespace-nowrap transition-colors {{ request('filter') === 'delivered' ? 'bg-primary text-on-primary' : 'border border-outline-variant text-on-surface hover:bg-surface-variant' }}">Livrées ({{ $counts['delivered'] }})</a>
            </div>
        </div>

        @if($orders->isEmpty())
            <div class="flex flex-col items-center justify-center text-center py-20">
                <div class="w-24 h-24 rounded-full bg-surface-container-high flex items-center justify-center mb-6">
                    <x-icon name="receipt_long" class="text-4xl text-on-surface-variant" />
                </div>
                <h2 class="font-headline-md text-headline-md text-primary mb-2">Aucune commande</h2>
                <p class="font-body-md text-body-md text-on-surface-variant mb-8 max-w-sm">Vos commandes apparaîtront ici une fois votre première commande passée.</p>
                <a href="{{ route('catalog.index') }}" class="px-8 h-12 border border-primary text-primary font-label-sm text-label-sm rounded-full uppercase tracking-wider hover:bg-primary hover:text-on-primary transition-colors flex items-center">Découvrir la collection</a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($orders as $order)
                    <a href="{{ route('account.orders.show', $order) }}" class="group block p-6 rounded-xl bg-surface-container-lowest shadow-[0_4px_30px_rgba(0,0,0,0.04)] hover:shadow-[0_8px_40px_rgba(0,0,0,0.08)] transition-all duration-300 hover:-translate-y-1">
                        <div class="flex justify-between items-start mb-6">
                            <div>
                                <div class="font-label-sm text-label-sm text-on-surface-variant mb-1 uppercase">{{ $order->created_at->translatedFormat('d F Y') }}</div>
                                <div class="font-headline-md text-headline-md text-primary">#{{ $order->order_number }}</div>
                            </div>
                            <span class="px-3 py-1 rounded-full font-label-sm text-label-sm {{ $order->status === 'livree' ? 'bg-surface-variant text-on-surface-variant' : 'bg-primary/10 text-primary' }} flex items-center gap-1">
                                @if($order->status === 'livree')
                                    <x-icon name="check_circle" class="text-[14px]" />
                                @endif
                                {{ $order->statusLabel() }}
                            </span>
                        </div>

                        <div class="flex gap-4 mb-6">
                            @foreach($order->items->take(2) as $item)
                                @php($img = $item->variant?->product?->images->first())
                                <div class="w-16 h-20 rounded-lg overflow-hidden bg-surface-variant flex-shrink-0 relative">
                                    @if($img)
                                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($img->path) }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover" />
                                    @endif
                                    @if($loop->last && $order->items->count() > 2)
                                        <div class="absolute inset-0 bg-black/40 flex items-center justify-center font-label-sm text-label-sm text-white">+{{ $order->items->count() - 2 }}</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <div class="flex justify-between items-end border-t border-surface-variant pt-4">
                            <div class="font-body-md text-body-md text-on-surface-variant">{{ $order->items->count() }} Article{{ $order->items->count() > 1 ? 's' : '' }}</div>
                            <div class="font-headline-md text-headline-md text-primary">{{ number_format($order->total, 0, ',', ' ') }} FCFA</div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="flex justify-center py-8">
                {{ $orders->onEachSide(1)->links('pagination.akiba') }}
            </div>
        @endif
    </div>
</x-layouts.app>
