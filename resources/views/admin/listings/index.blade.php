@php use App\Enums\ListingStatus; @endphp

<x-layouts.admin active="listings" title="Annonces">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 mb-5">
        <div class="flex gap-2 overflow-x-auto hide-scrollbar">
            @foreach(ListingStatus::cases() as $status)
                <a href="{{ route('admin.listings.index', ['statut' => $status->value]) }}" class="shrink-0 inline-flex items-center gap-2 px-4 py-2 rounded-full text-[13px] font-semibold border {{ $currentStatus === $status ? 'bg-on-surface text-surface border-on-surface' : 'bg-surface-container-lowest border-outline-variant text-on-surface' }}">
                    {{ $status->label() }}
                    <span class="text-[12px] opacity-70">{{ $counts[$status->value] ?? 0 }}</span>
                </a>
            @endforeach
        </div>
        <form method="GET" class="flex gap-2">
            <input type="hidden" name="statut" value="{{ $currentStatus?->value }}" />
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Titre, vendeur, téléphone…" class="field !min-h-[40px] !py-2 w-full md:w-72" />
            <button class="btn btn-primary btn-sm">Rechercher</button>
        </form>
    </div>

    @session('status')
        <div class="mb-4 rounded-xl bg-primary-fixed text-on-primary-fixed px-4 py-3 text-[14px]">{{ $value }}</div>
    @endsession

    @if($listings->isEmpty())
        <div class="card p-10 text-center text-on-surface-variant">Aucune annonce dans cette liste.</div>
    @else
        <div class="flex flex-col gap-2">
            @foreach($listings as $listing)
                <a href="{{ route('admin.listings.show', $listing) }}" class="card p-3 flex items-center gap-3 hover:border-primary transition-colors">
                    <span class="w-16 h-16 rounded-lg overflow-hidden bg-surface-container-low shrink-0 flex items-center justify-center">
                        @if($cover = $listing->coverImage())
                            <img src="{{ $cover->url() }}" alt="" loading="lazy" class="w-full h-full object-cover" />
                        @else
                            <x-icon name="image" class="text-[22px] text-outline" />
                        @endif
                    </span>
                    <span class="flex-1 min-w-0">
                        <span class="flex items-center gap-2">
                            <span class="text-[15px] font-bold text-on-surface truncate">{{ $listing->title }}</span>
                            @if($listing->reports_count)
                                <span class="shrink-0 rounded-md bg-error-container text-on-error-container px-1.5 py-0.5 text-[11px] font-bold">{{ $listing->reports_count }} signalement{{ $listing->reports_count > 1 ? 's' : '' }}</span>
                            @endif
                            @if($listing->isBoosted())
                                <span class="shrink-0 rounded-md bg-tertiary text-on-tertiary px-1.5 py-0.5 text-[11px] font-bold">À la une</span>
                            @endif
                        </span>
                        <span class="block text-[13px] font-semibold text-on-surface">{{ $listing->formattedPrice() }}</span>
                        <span class="block text-[12px] text-on-surface-variant truncate">{{ $listing->category->name }} · {{ $listing->city->name }} · {{ $listing->user->name }} · {{ $listing->created_at->diffForHumans() }}</span>
                    </span>
                    <x-icon name="chevron_right" class="text-[18px] text-outline" />
                </a>
            @endforeach
        </div>
        <div class="mt-6">{{ $listings->onEachSide(1)->links('pagination.akiba') }}</div>
    @endif
</x-layouts.admin>
