@php
    $heading = $currentCategory?->name ?? (request('q') ? 'Résultats pour « '.request('q').' »' : 'Toutes les annonces');
    $place = collect([$currentCity, \App\Support\Countries::name($currentCountry)])->filter()->implode(', ');
    $where = $place !== '' ? ' — '.$place : '';
    $pageTitle = ($currentCategory?->name ?? 'Petites annonces').$where.' — AkibaShop';
    $rootCategory = $currentCategory?->parent ?? $currentCategory;
    $active = $rootCategory ? 'cat-'.$rootCategory->slug : 'listings';
@endphp

<x-layouts.app :active="$active" :title="$pageTitle" :description="'Découvrez '.number_format($listings->total(), 0, ',', ' ').' annonces '.mb_strtolower($currentCategory?->name ?? 'de particuliers et de professionnels').$where.' sur AkibaShop. Contact direct par WhatsApp.'">
    <div class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop pt-4 md:pt-6" x-data="{ filtersOpen: false }">

        {{-- Breadcrumb --}}
        <nav aria-label="Fil d’Ariane" class="text-[13px] text-on-surface-variant mb-2">
            <ol class="flex flex-wrap items-center gap-1">
                <li><a href="{{ route('home') }}" class="hover:text-primary">Accueil</a></li>
                <li aria-hidden="true">›</li>
                @if($currentCategory?->parent)
                    <li><a href="{{ route('categories.show', $currentCategory->parent) }}" class="hover:text-primary">{{ $currentCategory->parent->name }}</a></li>
                    <li aria-hidden="true">›</li>
                @endif
                <li class="text-on-surface font-semibold" aria-current="page">{{ $currentCategory?->name ?? 'Annonces' }}</li>
            </ol>
        </nav>

        <div class="flex flex-wrap items-end justify-between gap-2 mb-4">
            <h1 class="text-headline-md md:text-headline-lg text-on-surface">{{ $heading }}<span class="text-on-surface-variant font-semibold">{{ $where }}</span></h1>
            <p class="text-[14px] text-on-surface-variant">{{ number_format($listings->total(), 0, ',', ' ') }} annonce{{ $listings->total() > 1 ? 's' : '' }}</p>
        </div>

        {{-- Sub-categories --}}
        @php($chips = $rootCategory?->children ?? collect())
        @if($chips->isNotEmpty())
            <div class="flex gap-2 overflow-x-auto hide-scrollbar -mx-margin-mobile px-margin-mobile md:mx-0 md:px-0 mb-4">
                <a href="{{ route('categories.show', $rootCategory) }}?{{ http_build_query(request()->except(['page', 'categorie'])) }}" class="shrink-0 rounded-full px-4 py-2 text-[13px] font-semibold border transition-colors {{ $currentCategory?->is($rootCategory) ? 'bg-on-surface text-surface border-on-surface' : 'bg-surface-container-lowest border-outline-variant text-on-surface hover:border-primary' }}">Tout</a>
                @foreach($chips as $child)
                    <a href="{{ route('categories.show', $child) }}?{{ http_build_query(request()->except(['page', 'categorie'])) }}" class="shrink-0 rounded-full px-4 py-2 text-[13px] font-semibold border transition-colors {{ $currentCategory?->is($child) ? 'bg-on-surface text-surface border-on-surface' : 'bg-surface-container-lowest border-outline-variant text-on-surface hover:border-primary' }}">{{ $child->name }}</a>
                @endforeach
            </div>
        @endif

        {{-- Mobile sticky toolbar --}}
        <div class="md:hidden sticky top-[121px] z-30 -mx-margin-mobile px-margin-mobile py-2 bg-background/95 backdrop-blur flex gap-2 mb-3">
            <button type="button" @click="filtersOpen = true" class="btn btn-outline btn-sm flex-1">
                <x-icon name="filters" class="text-[18px]" /> Filtres
                @if($activeFilterCount)
                    <span class="ml-1 min-w-5 h-5 px-1.5 rounded-full bg-primary text-on-primary text-[11px] flex items-center justify-center">{{ $activeFilterCount }}</span>
                @endif
            </button>
            <form method="GET" action="{{ url()->current() }}" class="flex-1">
                @foreach(request()->except(['tri', 'page']) as $key => $value)
                    @if(is_string($value))
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}" />
                    @endif
                @endforeach
                <label for="sort-mobile" class="sr-only">Trier</label>
                <select id="sort-mobile" name="tri" onchange="this.form.submit()" class="field !min-h-[38px] !py-1.5 !rounded-[10px] text-[14px] font-semibold">
                    @foreach($sorts as $value => $label)
                        <option value="{{ $value }}" @selected(request('tri', 'recent') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="grid md:grid-cols-[260px_1fr] gap-6 items-start">
            {{-- Filters: sidebar on desktop, bottom sheet on mobile --}}
            <div
                class="hidden md:block md:sticky md:top-[136px]"
                :class="{ '!block fixed inset-0 z-50 md:static': filtersOpen }"
                @keydown.escape.window="filtersOpen = false"
            >
                <div class="md:hidden absolute inset-0 bg-black/40" @click="filtersOpen = false"></div>
                <form
                    method="GET"
                    action="{{ route('listings.index') }}"
                    class="absolute md:static inset-x-0 bottom-0 max-h-[85vh] overflow-y-auto md:overflow-visible card rounded-b-none md:rounded-b-2xl p-5 flex flex-col gap-4"
                    style="padding-bottom: max(1.25rem, env(safe-area-inset-bottom));"
                >
                    <div class="flex items-center justify-between">
                        <h2 class="text-[17px] font-extrabold text-on-surface">Filtres</h2>
                        <button type="button" class="md:hidden w-9 h-9 flex items-center justify-center rounded-full hover:bg-surface-container-low" @click="filtersOpen = false" aria-label="Fermer les filtres">
                            <x-icon name="close" class="text-[20px]" />
                        </button>
                    </div>

                    @if(request('q'))
                        <input type="hidden" name="q" value="{{ request('q') }}" />
                    @endif

                    <div>
                        <label for="f-category" class="field-label">Catégorie</label>
                        <select id="f-category" name="categorie" class="field">
                            <option value="">Toutes les catégories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->slug }}" @selected($currentCategory?->is($category)) class="font-bold">{{ $category->name }}</option>
                                @foreach($category->children as $child)
                                    <option value="{{ $child->slug }}" @selected($currentCategory?->is($child))>&nbsp;&nbsp;– {{ $child->name }}</option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="f-country" class="field-label">Pays</label>
                        <select id="f-country" name="pays" class="field">
                            <option value="">Tous les pays</option>
                            @foreach($navCountries as $code => $name)
                                <option value="{{ $code }}" @selected($currentCountry === $code)>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="f-city" class="field-label">Ville</label>
                        <input id="f-city" type="text" name="ville" value="{{ $currentCity }}" maxlength="100" placeholder="Toutes les villes" class="field" />
                    </div>

                    <fieldset>
                        <legend class="field-label">Prix ({{ \App\Support\Price::symbol() }})</legend>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="sr-only" for="f-min">Prix minimum</label>
                            <input id="f-min" type="number" inputmode="numeric" min="0" step="1000" name="prix_min" value="{{ request('prix_min') }}" placeholder="Min" class="field" />
                            <label class="sr-only" for="f-max">Prix maximum</label>
                            <input id="f-max" type="number" inputmode="numeric" min="0" step="1000" name="prix_max" value="{{ request('prix_max') }}" placeholder="Max" class="field" />
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend class="field-label">État</legend>
                        <div class="grid grid-cols-3 gap-2">
                            @foreach(['' => 'Tous', 'neuf' => 'Neuf', 'occasion' => 'Occasion'] as $value => $label)
                                <label class="cursor-pointer">
                                    <input type="radio" name="etat" value="{{ $value }}" class="peer sr-only" @checked(request('etat', '') === $value) />
                                    <span class="flex items-center justify-center h-10 rounded-[10px] border border-outline-variant text-[13px] font-semibold text-on-surface peer-checked:border-primary peer-checked:bg-primary-fixed peer-checked:text-on-primary-fixed-variant peer-focus-visible:ring-2 peer-focus-visible:ring-primary">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>

                    <div class="hidden md:block">
                        <label for="f-sort" class="field-label">Trier par</label>
                        <select id="f-sort" name="tri" class="field">
                            @foreach($sorts as $value => $label)
                                <option value="{{ $value }}" @selected(request('tri', 'recent') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex gap-2 pt-1">
                        <a href="{{ $currentCategory ? route('categories.show', $currentCategory) : route('listings.index') }}" class="btn btn-outline flex-1">Effacer</a>
                        <button type="submit" class="btn btn-primary flex-[2]">Afficher</button>
                    </div>
                </form>
            </div>

            <div>
                @if($listings->isEmpty())
                    <x-empty-state icon="search_off" title="Aucune annonce trouvée" text="Essayez d’élargir votre recherche : une autre ville, une fourchette de prix plus large ou moins de mots-clés.">
                        <a href="{{ route('listings.index') }}" class="btn btn-outline">Voir toutes les annonces</a>
                        <a href="{{ route('listings.create') }}" class="btn btn-accent">Déposer une annonce</a>
                    </x-empty-state>
                @else
                    <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4">
                        @foreach($listings as $listing)
                            <x-listing-card :listing="$listing" />
                        @endforeach
                    </div>

                    <div class="mt-8 flex justify-center">{{ $listings->onEachSide(1)->links('pagination.akiba') }}</div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
