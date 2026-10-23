<x-layouts.app :title="$seller->name.' — annonces sur AkibaShop'" :description="'Toutes les annonces de '.$seller->name.' sur AkibaShop.'">
    <div class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop pt-6">
        <section class="card p-5 md:p-6 flex flex-col sm:flex-row sm:items-center gap-4 mb-6">
            <span class="w-16 h-16 rounded-full bg-primary-fixed text-on-primary-fixed-variant flex items-center justify-center text-[24px] font-extrabold shrink-0">{{ mb_strtoupper(mb_substr($seller->name, 0, 1)) }}</span>
            <div class="flex-1 min-w-0">
                <h1 class="text-headline-md md:text-headline-lg text-on-surface flex items-center gap-2 flex-wrap">
                    {{ $seller->name }}
                    @if($seller->is_verified)
                        <span class="inline-flex items-center gap-1 rounded-md bg-secondary-container text-on-secondary-container px-2 py-0.5 text-[12px] font-bold"><x-icon name="verified" class="text-[14px]" /> Vérifié</span>
                    @endif
                    @if($seller->is_pro)
                        <span class="rounded-md bg-secondary-container text-on-secondary-container px-2 py-0.5 text-[12px] font-bold">Pro</span>
                    @endif
                </h1>
                <p class="text-[14px] text-on-surface-variant mt-1">
                    {{ $seller->is_pro ? 'Professionnel' : 'Particulier' }}
                    @if($seller->location()) · {{ $seller->location() }} @endif
                    · membre depuis {{ $seller->created_at->translatedFormat('F Y') }}
                </p>
            </div>
            <p class="text-[14px] font-semibold text-on-surface">{{ $listings->total() }} annonce{{ $listings->total() > 1 ? 's' : '' }} en ligne</p>
        </section>

        @if($listings->isEmpty())
            <x-empty-state icon="listings" title="Aucune annonce en ligne" text="Ce vendeur n’a pas d’annonce visible pour le moment." />
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 md:gap-4">
                @foreach($listings as $listing)
                    <x-listing-card :listing="$listing" />
                @endforeach
            </div>
            <div class="mt-8 flex justify-center">{{ $listings->onEachSide(1)->links('pagination.akiba') }}</div>
        @endif
    </div>
</x-layouts.app>
