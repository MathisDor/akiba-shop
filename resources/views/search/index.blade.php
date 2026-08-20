<x-layouts.app title="Recherche — AkibaShop">
    <div class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop py-8">
        <form method="GET" action="{{ route('search') }}" class="mb-8">
            <div class="relative max-w-xl">
                <x-icon name="search" class="absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant" />
                <input
                    type="search"
                    name="q"
                    value="{{ $term }}"
                    placeholder="Rechercher une robe, une couleur, une référence…"
                    class="w-full rounded-full border border-outline-variant bg-surface-container-lowest pl-12 pr-4 py-3 font-body-md text-body-md text-on-surface focus:border-primary focus:ring-0"
                    autofocus
                />
            </div>
        </form>

        @if($term === '')
            <p class="font-body-md text-body-md text-on-surface-variant">Recherchez par nom de produit, catégorie, couleur ou référence.</p>
        @elseif($products->isEmpty())
            <div class="flex flex-col items-center justify-center text-center py-24 px-4">
                <div class="w-24 h-24 rounded-full bg-surface-container-high flex items-center justify-center mb-6">
                    <x-icon name="search_off" class="text-4xl text-on-surface-variant" />
                </div>
                <h2 class="font-headline-md text-headline-md text-primary mb-2">Aucun résultat pour « {{ $term }} »</h2>
                <p class="font-body-md text-body-md text-on-surface-variant mb-8 max-w-sm">Essayez un autre mot-clé ou parcourez la collection complète.</p>
                <a href="{{ route('catalog.index') }}" class="px-8 h-12 border border-primary text-primary font-label-sm text-label-sm rounded-full uppercase tracking-wider hover:bg-primary hover:text-on-primary transition-colors flex items-center">Voir toute la collection</a>
            </div>
        @else
            <p class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-widest mb-6">{{ $products->total() }} résultat(s) pour « {{ $term }} »</p>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-4 gap-y-10 md:gap-x-6 md:gap-y-16">
                @foreach($products as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
            <div class="flex justify-center py-8">
                {{ $products->onEachSide(1)->links('pagination.akiba') }}
            </div>
        @endif
    </div>
</x-layouts.app>
