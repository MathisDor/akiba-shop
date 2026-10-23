<x-layouts.admin active="dashboard" title="Tableau de bord">
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 mb-6">
        @foreach([
            ['À valider', $stats['pending'], 'listings', route('admin.listings.index'), $stats['pending'] > 0],
            ['Signalements ouverts', $stats['openReports'], 'report', route('admin.reports.index'), $stats['openReports'] > 0],
            ['Annonces en ligne', $stats['online'], 'check_circle', route('admin.listings.index', ['statut' => 'publiee']), false],
            ['Membres', $stats['members'], 'group', route('admin.users.index'), false],
        ] as [$label, $value, $icon, $link, $highlight])
            <a href="{{ $link }}" class="card p-4 md:p-5 flex flex-col gap-2 hover:border-primary transition-colors {{ $highlight ? '!bg-tertiary-container !border-tertiary' : '' }}">
                <x-icon :name="$icon" class="text-[22px] {{ $highlight ? 'text-on-tertiary-container' : 'text-primary' }}" />
                <p class="text-[28px] leading-none font-extrabold text-on-surface">{{ number_format($value, 0, ',', ' ') }}</p>
                <p class="text-[13px] text-on-surface-variant">{{ $label }}</p>
            </a>
        @endforeach
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 mb-6 text-[14px]">
        <div class="card p-4"><span class="font-extrabold text-on-surface">{{ $stats['newMembers'] }}</span> <span class="text-on-surface-variant">inscriptions (7 j)</span></div>
        <div class="card p-4"><span class="font-extrabold text-on-surface">{{ number_format($stats['views'], 0, ',', ' ') }}</span> <span class="text-on-surface-variant">vues d’annonces</span></div>
        <div class="card p-4"><span class="font-extrabold text-on-surface">{{ $stats['boosted'] }}</span> <span class="text-on-surface-variant">à la une</span></div>
        <div class="card p-4"><span class="font-extrabold text-on-surface">{{ $stats['sold'] }}</span> <span class="text-on-surface-variant">marquées vendues</span></div>
    </div>

    <div class="grid lg:grid-cols-[1.4fr_1fr] gap-4 md:gap-6">
        <section class="card p-5">
            <h2 class="text-[16px] font-extrabold text-on-surface mb-4">Nouvelles annonces (14 jours)</h2>
            @php($max = max(1, $days->max('total')))
            <div class="flex items-end gap-1.5 h-40" role="img" aria-label="Nombre d’annonces déposées par jour sur 14 jours">
                @foreach($days as $day)
                    <div class="flex-1 flex flex-col items-center gap-1 h-full justify-end" title="{{ $day['label'] }} : {{ $day['total'] }}">
                        <span class="text-[10px] font-semibold text-on-surface-variant">{{ $day['total'] ?: '' }}</span>
                        <div class="w-full rounded-t-md bg-primary-container" style="height: {{ max(2, $day['total'] / $max * 100) }}%"></div>
                        <span class="text-[9px] text-on-surface-variant whitespace-nowrap">{{ $day['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="card p-5">
            <h2 class="text-[16px] font-extrabold text-on-surface mb-4">Catégories les plus actives</h2>
            @php($maxCat = max(1, $topCategories->max('total')))
            <ul class="flex flex-col gap-3">
                @foreach($topCategories as $category)
                    <li>
                        <div class="flex justify-between text-[13px] mb-1"><span class="text-on-surface">{{ $category['name'] }}</span><span class="font-bold text-on-surface">{{ $category['total'] }}</span></div>
                        <div class="h-2 rounded-full bg-surface-container"><div class="h-2 rounded-full bg-secondary" style="width: {{ $category['total'] / $maxCat * 100 }}%"></div></div>
                    </li>
                @endforeach
            </ul>
        </section>
    </div>

    <section class="card p-5 mt-4 md:mt-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-[16px] font-extrabold text-on-surface">En attente de validation</h2>
            <a href="{{ route('admin.listings.index') }}" class="text-[13px] font-semibold text-secondary hover:underline">Tout voir</a>
        </div>
        @forelse($pendingListings as $listing)
            <a href="{{ route('admin.listings.show', $listing) }}" class="flex items-center gap-3 py-2.5 border-t border-surface-variant first:border-t-0 hover:bg-surface-container-low -mx-2 px-2 rounded-lg">
                <span class="w-12 h-12 rounded-lg overflow-hidden bg-surface-container-low shrink-0 flex items-center justify-center">
                    @if($cover = $listing->coverImage())
                        <img src="{{ $cover->url() }}" alt="" class="w-full h-full object-cover" />
                    @else
                        <x-icon name="image" class="text-[20px] text-outline" />
                    @endif
                </span>
                <span class="flex-1 min-w-0">
                    <span class="block text-[14px] font-semibold text-on-surface truncate">{{ $listing->title }}</span>
                    <span class="block text-[12px] text-on-surface-variant">{{ $listing->category->name }} · {{ $listing->user->name }} · {{ $listing->created_at->diffForHumans() }}</span>
                </span>
                <x-icon name="chevron_right" class="text-[18px] text-outline" />
            </a>
        @empty
            <p class="text-[14px] text-on-surface-variant">Rien à valider. 🎉</p>
        @endforelse
    </section>
</x-layouts.admin>
