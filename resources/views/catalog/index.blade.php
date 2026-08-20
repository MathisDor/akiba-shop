@php
    $pageTitle = $currentCategory?->name ?? match($currentGender) { 'femme' => 'Collection Femme', 'homme' => 'Collection Homme', default => 'Toute la collection' };
    $navActive = request('sort') === 'nouveautes' ? 'nouveautes' : ($currentGender ?? 'catalog');
@endphp

<x-layouts.app
    :active="$navActive"
    :title="$pageTitle.' — AkibaShop'"
    :description="'Découvrez '.$pageTitle.' sur AkibaShop : mode homme et femme, livraison à Libreville, paiement Airtel Money et Moov Money.'"
>

    <div class="px-margin-mobile md:px-margin-desktop py-6 md:py-10 max-w-container-max mx-auto">
        <h1 class="font-headline-xl text-headline-xl text-primary mb-2">{{ $pageTitle }}</h1>
        <p class="font-body-lg text-body-lg text-on-surface-variant">Découvrez notre sélection exclusive.</p>
    </div>

    <form method="GET" id="filter-form" class="sticky top-[72px] md:top-[88px] z-40 bg-surface/95 backdrop-blur-md border-b border-surface-container-highest pb-4">
        <input type="hidden" name="sort" value="{{ request('sort') }}" />

        <div class="flex overflow-x-auto hide-scrollbar px-margin-mobile md:px-margin-desktop gap-2 py-2 max-w-container-max mx-auto">
            <a href="{{ route('catalog.index') }}" class="whitespace-nowrap px-4 py-2 rounded-full border font-label-sm text-label-sm uppercase transition-colors {{ !$currentGender ? 'border-primary bg-primary text-on-primary' : 'border-outline-variant text-on-surface hover:border-primary' }}">Genre: Tous</a>
            <a href="{{ route('catalog.gender', 'homme') }}" class="whitespace-nowrap px-4 py-2 rounded-full border font-label-sm text-label-sm uppercase transition-colors {{ $currentGender === 'homme' ? 'border-primary bg-primary text-on-primary' : 'border-outline-variant text-on-surface hover:border-primary' }}">Homme</a>
            <a href="{{ route('catalog.gender', 'femme') }}" class="whitespace-nowrap px-4 py-2 rounded-full border font-label-sm text-label-sm uppercase transition-colors {{ $currentGender === 'femme' ? 'border-primary bg-primary text-on-primary' : 'border-outline-variant text-on-surface hover:border-primary' }}">Femme</a>

            <select name="category" onchange="document.getElementById('filter-form').submit()" class="whitespace-nowrap px-4 py-2 rounded-full border border-outline-variant text-on-surface font-label-sm text-label-sm uppercase bg-surface">
                <option value="">Catégorie</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->slug }}" @selected(request('category') === $cat->slug)>{{ $cat->name }}</option>
                @endforeach
            </select>

            <select name="size" onchange="document.getElementById('filter-form').submit()" class="whitespace-nowrap px-4 py-2 rounded-full border border-outline-variant text-on-surface font-label-sm text-label-sm uppercase bg-surface">
                <option value="">Taille</option>
                @foreach($sizes as $size)
                    <option value="{{ $size->name }}" @selected(request('size') === $size->name)>{{ $size->name }}</option>
                @endforeach
            </select>

            <select name="price_max" onchange="document.getElementById('filter-form').submit()" class="whitespace-nowrap px-4 py-2 rounded-full border border-outline-variant text-on-surface font-label-sm text-label-sm uppercase bg-surface">
                <option value="">Prix</option>
                <option value="25000" @selected(request('price_max') === '25000')>Moins de 25 000 FCFA</option>
                <option value="50000" @selected(request('price_max') === '50000')>25 000 – 50 000 FCFA</option>
                <option value="100000" @selected(request('price_max') === '100000')>50 000 – 100 000 FCFA</option>
            </select>

            <select name="availability" onchange="document.getElementById('filter-form').submit()" class="whitespace-nowrap px-4 py-2 rounded-full border border-outline-variant text-on-surface font-label-sm text-label-sm uppercase bg-surface">
                <option value="">Disponibilité</option>
                <option value="in_stock" @selected(request('availability') === 'in_stock')>Disponible</option>
                <option value="out_of_stock" @selected(request('availability') === 'out_of_stock')>En rupture</option>
            </select>

            <label class="whitespace-nowrap px-4 py-2 rounded-full border border-outline-variant text-on-surface font-label-sm text-label-sm uppercase flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="promo" value="1" onchange="document.getElementById('filter-form').submit()" @checked(request('promo')) class="rounded-sm" />
                Promotions
            </label>
        </div>

        <div class="px-margin-mobile md:px-margin-desktop pt-4 flex justify-between items-center max-w-container-max mx-auto">
            <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-widest">{{ $products->total() }} Articles</span>
            <select name="sort" onchange="document.getElementById('filter-form').submit()" class="font-label-sm text-label-sm text-primary uppercase bg-transparent border-none">
                <option value="" @selected(!request('sort'))>Trier par: Pertinence</option>
                <option value="nouveautes" @selected(request('sort') === 'nouveautes')>Nouveautés</option>
                <option value="prix_asc" @selected(request('sort') === 'prix_asc')>Prix croissant</option>
                <option value="prix_desc" @selected(request('sort') === 'prix_desc')>Prix décroissant</option>
            </select>
        </div>
    </form>

    <div class="max-w-container-max mx-auto">
        @if($products->isEmpty())
            <div class="flex flex-col items-center justify-center text-center py-24 px-4">
                <div class="w-24 h-24 rounded-full bg-surface-container-high flex items-center justify-center mb-6">
<x-icon name="search_off" class="text-4xl text-on-surface-variant" />
                </div>
                <h2 class="font-headline-md text-headline-md text-primary mb-2">Aucun article trouvé</h2>
                <p class="font-body-md text-body-md text-on-surface-variant mb-8 max-w-sm">Essayez d'ajuster vos filtres ou parcourez toute la collection.</p>
                <a href="{{ route('catalog.index') }}" class="px-8 h-12 border border-primary text-primary font-label-sm text-label-sm rounded-full uppercase tracking-wider hover:bg-primary hover:text-on-primary transition-colors flex items-center">Voir toute la collection</a>
            </div>
        @else
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-4 gap-y-10 md:gap-x-6 md:gap-y-16 px-margin-mobile md:px-margin-desktop py-8">
                @foreach($products as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>

            <div class="flex justify-center py-8 mb-8 md:mb-16">
                {{ $products->onEachSide(1)->links('pagination.akiba') }}
            </div>
        @endif
    </div>
</x-layouts.app>
