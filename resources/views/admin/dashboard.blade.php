@php
    $tiles = [
        ['label' => 'Chiffre d\'affaires', 'value' => number_format($revenue, 0, ',', ' ').' FCFA', 'icon' => 'payments'],
        ['label' => 'Commandes du jour', 'value' => $ordersToday, 'icon' => 'today'],
        ['label' => 'En attente de paiement', 'value' => $ordersPending, 'icon' => 'hourglass_empty'],
        ['label' => 'Commandes livrées', 'value' => $ordersDelivered, 'icon' => 'local_shipping'],
        ['label' => 'Produits vendus', 'value' => $productsSold, 'icon' => 'inventory_2'],
        ['label' => 'Clients', 'value' => $customersCount, 'icon' => 'group'],
    ];

    $maxSale = $salesByDay->max('total') ?: 1;
    $days = collect(range(6, 0))->map(fn ($i) => \Illuminate\Support\Carbon::today()->subDays($i));
@endphp

<x-layouts.admin active="dashboard" title="Tableau de bord">
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3 md:gap-4 mb-6 md:mb-8">
        @foreach($tiles as $tile)
            <div class="bg-surface-container-lowest rounded-xl p-4 md:p-5 shadow-[0_4px_20px_rgba(0,0,0,0.03)]">
                <div class="w-9 h-9 md:w-10 md:h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center mb-2 md:mb-3">
                    <span class="material-symbols-outlined text-[18px] md:text-[20px]">{{ $tile['icon'] }}</span>
                </div>
                <p class="font-headline-md text-headline-md text-on-surface truncate">{{ $tile['value'] }}</p>
                <p class="font-label-sm text-[10px] md:text-label-sm text-on-surface-variant uppercase tracking-wide mt-1">{{ $tile['label'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6">
        <div class="lg:col-span-2 bg-surface-container-lowest rounded-xl p-4 md:p-6 shadow-[0_4px_20px_rgba(0,0,0,0.03)]">
            <h2 class="font-headline-md text-headline-md text-on-surface mb-1">Ventes des 7 derniers jours</h2>
            <p class="font-label-sm text-label-sm text-on-surface-variant mb-4 md:mb-6">Chiffre d'affaires (FCFA), commandes payées et au-delà</p>

            <div class="flex items-end gap-1.5 md:gap-3 h-40 md:h-48" role="img" aria-label="Graphique des ventes des 7 derniers jours">
                @foreach($days as $day)
                    @php
                        $amount = (int) ($salesByDay->firstWhere('day', $day->toDateString())->total ?? 0);
                        $heightPct = max(4, round(($amount / $maxSale) * 100));
                    @endphp
                    <div class="flex-1 flex flex-col items-center justify-end h-full gap-1.5 md:gap-2 group">
                        <span class="font-label-sm text-[9px] md:text-[10px] text-on-surface-variant opacity-0 group-hover:opacity-100 transition-opacity hidden sm:block">{{ number_format($amount, 0, ',', ' ') }}</span>
                        <div class="w-full bg-primary/15 rounded-t-md relative" style="height: {{ $heightPct }}%">
                            <div class="absolute inset-0 bg-primary rounded-t-md" style="opacity: 0.85"></div>
                        </div>
                        <span class="font-label-sm text-[9px] md:text-[10px] text-on-surface-variant uppercase">{{ $day->translatedFormat('D') }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-surface-container-lowest rounded-xl p-4 md:p-6 shadow-[0_4px_20px_rgba(0,0,0,0.03)]">
            <h2 class="font-headline-md text-headline-md text-on-surface mb-4">Stock faible</h2>
            @if($lowStock->isEmpty())
                <p class="font-body-md text-body-md text-on-surface-variant">Aucune alerte de stock pour le moment.</p>
            @else
                <ul class="flex flex-col gap-3">
                    @foreach($lowStock as $variant)
                        <li class="flex items-center justify-between font-body-md text-body-md gap-2">
                            <span class="text-on-surface truncate">{{ $variant->product->name }} — {{ $variant->color?->name }} / {{ $variant->size?->name ?? 'Unique' }}</span>
                            <span class="text-error font-bold shrink-0">{{ $variant->stock }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    <div class="bg-surface-container-lowest rounded-xl p-4 md:p-6 shadow-[0_4px_20px_rgba(0,0,0,0.03)] mt-4 md:mt-6">
        <h2 class="font-headline-md text-headline-md text-on-surface mb-4">Produits les plus vendus</h2>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3 md:gap-4">
            @forelse($bestSellers as $product)
                <div class="text-center">
                    <div class="aspect-square rounded-lg overflow-hidden bg-surface-container mb-2">
                        @if($product->images->first())
                            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($product->images->first()->path) }}" class="w-full h-full object-cover" alt="{{ $product->name }}" />
                        @endif
                    </div>
                    <p class="font-label-sm text-label-sm text-on-surface truncate">{{ $product->name }}</p>
                    <p class="font-label-sm text-[11px] text-on-surface-variant">{{ (int) $product->sold }} vendus</p>
                </div>
            @empty
                <p class="font-body-md text-body-md text-on-surface-variant col-span-full">Pas encore de ventes.</p>
            @endforelse
        </div>
    </div>
</x-layouts.admin>
