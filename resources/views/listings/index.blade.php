@php
    use App\Support\Price;
    use App\Support\Provinces;

    $heading = $currentCategory?->name ?? (request('q') ? 'Résultats pour « '.request('q').' »' : 'Toutes les annonces');
    $place = collect([$currentCity, Provinces::name($currentProvince)])->filter()->implode(', ');
    $where = $place !== '' ? ' — '.$place : '';
    $pageTitle = ($currentCategory?->name ?? 'Petites annonces').$where.' — AkibaShop';
    $rootCategory = $currentCategory?->parent ?? $currentCategory;
    $active = $rootCategory ? 'cat-'.$rootCategory->slug : 'listings';

    // Category links keep the visitor's other filters (search, place, price…).
    $keep = fn () => array_filter(request()->except(['page', 'categorie']), fn ($v) => is_string($v) && $v !== '');
    $categoryUrl = fn ($category = null) => ($category ? route('categories.show', $category) : route('listings.index')).($keep() ? '?'.http_build_query($keep()) : '');

    // Removable chips summarising the active filters.
    $without = fn (string ...$keys) => url()->current().(($query = array_filter(request()->except(['page', ...$keys]), fn ($v) => is_string($v) && $v !== '')) ? '?'.http_build_query($query) : '');
    $priceLabel = match (true) {
        request()->filled('prix_min') && request()->filled('prix_max') => Price::format((int) request('prix_min')).' – '.Price::format((int) request('prix_max')),
        request()->filled('prix_min') => 'Dès '.Price::format((int) request('prix_min')),
        request()->filled('prix_max') => 'Jusqu’à '.Price::format((int) request('prix_max')),
        default => null,
    };
    $activeChips = array_filter([
        request('q') ? ['« '.request('q').' »', $without('q')] : null,
        $currentProvince ? [Provinces::name($currentProvince), $without('province')] : null,
        $currentCity ? [$currentCity, $without('ville')] : null,
        $priceLabel ? [$priceLabel, $without('prix_min', 'prix_max')] : null,
        in_array(request('etat'), ['neuf', 'occasion'], true) ? [ucfirst(request('etat')), $without('etat')] : null,
    ]);
    $resetUrl = $currentCategory ? route('categories.show', $currentCategory) : route('listings.index');
@endphp

<x-layouts.app :active="$active" :title="$pageTitle" :description="'Découvrez '.number_format($listings->total(), 0, ',', ' ').' annonces '.mb_strtolower($currentCategory?->name ?? 'de particuliers et de professionnels').$where.' sur AkibaShop. Contact direct par WhatsApp.'">
    <div
        class="max-w-container-max mx-auto pt-3 md:pt-6"
        x-data="{ filtersOpen: window.location.hash === '#filtres' }"
        x-effect="document.body.classList.toggle('overflow-hidden', filtersOpen && window.innerWidth < 768)"
        @open-filters.window="filtersOpen = true"
        @keydown.escape.window="filtersOpen = false"
        data-filters
    >
        {{-- Category rail: one tap to switch category, other filters are kept --}}
        <nav aria-label="Catégories" class="flex gap-1 md:gap-2 overflow-x-auto hide-scrollbar snap-x px-margin-mobile md:px-margin-desktop pb-1">
            @foreach([null, ...$categories] as $category)
                @php($isCurrent = $category ? $rootCategory?->is($category) : $rootCategory === null)
                <a
                    href="{{ $categoryUrl($category) }}"
                    class="group snap-start shrink-0 w-[74px] md:w-[88px] flex flex-col items-center gap-1.5 rounded-2xl px-1 py-2 text-center transition-colors {{ $isCurrent ? '' : 'hover:bg-surface-container-lowest' }}"
                    @if($isCurrent) aria-current="page" @endif
                >
                    <span class="w-12 h-12 md:w-14 md:h-14 rounded-2xl flex items-center justify-center transition-colors {{ $isCurrent ? 'bg-primary text-on-primary shadow-[0_6px_16px_-6px_rgba(0,122,47,0.6)]' : 'bg-surface-container-lowest border border-surface-variant text-primary group-hover:border-primary' }}">
                        <x-icon :name="$category?->icon ?? 'grid_view'" class="text-[22px] md:text-[24px]" />
                    </span>
                    <span class="text-[11px] md:text-[12px] leading-[14px] line-clamp-2 {{ $isCurrent ? 'font-bold text-primary' : 'font-semibold text-on-surface' }}">{{ $category?->name ?? 'Toutes' }}</span>
                </a>
            @endforeach
        </nav>

        <div class="px-margin-mobile md:px-margin-desktop">
            <div class="flex items-end justify-between gap-3 mt-3 md:mt-5">
                <h1 class="text-[20px] leading-7 md:text-headline-lg font-extrabold text-on-surface min-w-0">
                    {{ $heading }}<span class="text-on-surface-variant font-semibold">{{ $where }}</span>
                </h1>
                <p class="shrink-0 text-[13px] text-on-surface-variant pb-0.5">{{ number_format($listings->total(), 0, ',', ' ') }} annonce{{ $listings->total() > 1 ? 's' : '' }}</p>
            </div>

            {{-- Sub-categories of the current category --}}
            @php($chips = $rootCategory?->children ?? collect())
            @if($chips->isNotEmpty())
                <div class="flex gap-2 overflow-x-auto hide-scrollbar -mx-margin-mobile px-margin-mobile md:mx-0 md:px-0 mt-3">
                    @foreach([$rootCategory, ...$chips] as $chip)
                        @php($isCurrent = $currentCategory?->is($chip))
                        <a href="{{ $categoryUrl($chip) }}" class="shrink-0 inline-flex items-center gap-1.5 h-9 rounded-full px-3.5 text-[13px] font-semibold border transition-colors {{ $isCurrent ? 'bg-on-surface text-surface border-on-surface' : 'bg-surface-container-lowest border-outline-variant text-on-surface hover:border-primary' }}">
                            @if($isCurrent)<x-icon name="check" class="text-[14px]" />@endif
                            {{ $chip->is($rootCategory) ? 'Tout' : $chip->name }}
                        </a>
                    @endforeach
                </div>
            @endif

            {{-- Active filters --}}
            @if($activeChips !== [])
                <div class="flex items-center gap-2 overflow-x-auto hide-scrollbar -mx-margin-mobile px-margin-mobile md:mx-0 md:px-0 mt-3">
                    @foreach($activeChips as [$label, $removeUrl])
                        <a href="{{ $removeUrl }}" class="shrink-0 inline-flex items-center gap-1 h-8 rounded-full pl-3 pr-2 bg-primary-fixed text-on-primary-fixed-variant text-[12px] font-bold hover:bg-primary-fixed-dim transition-colors" aria-label="Retirer le filtre {{ $label }}">
                            {{ $label }} <x-icon name="close" class="text-[14px]" />
                        </a>
                    @endforeach
                    <a href="{{ $resetUrl }}" class="shrink-0 text-[12px] font-semibold text-secondary hover:underline px-1">Tout effacer</a>
                </div>
            @endif

            <div class="grid md:grid-cols-[272px_1fr] gap-6 items-start mt-4">
                {{-- Filters: a static sidebar on desktop, a slide-up sheet on phones --}}
                <div
                    class="fixed inset-0 z-50 pointer-events-none md:sticky md:left-auto md:right-auto md:bottom-auto md:top-24 md:z-auto md:pointer-events-auto"
                    :class="filtersOpen && '!pointer-events-auto'"
                >
                    <div
                        class="md:hidden absolute inset-0 bg-black/45 opacity-0 transition-opacity duration-300"
                        :class="filtersOpen && '!opacity-100'"
                        @click="filtersOpen = false"
                        aria-hidden="true"
                    ></div>

                    <form
                        method="GET"
                        action="{{ route('listings.index') }}"
                        role="dialog"
                        aria-labelledby="filters-title"
                        class="absolute inset-x-0 bottom-0 max-h-[88dvh] flex flex-col bg-surface-container-lowest rounded-t-3xl shadow-[0_-12px_40px_rgba(0,0,0,0.18)] translate-y-full transition-transform duration-300 ease-out md:static md:max-h-none md:translate-y-0 md:rounded-2xl md:shadow-none md:border md:border-surface-variant"
                        :class="filtersOpen && '!translate-y-0'"
                    >
                        {{-- Sheet header --}}
                        <div class="shrink-0 px-5 pt-2.5 pb-3 md:pt-4 border-b border-surface-variant">
                            <div class="md:hidden mx-auto mb-2.5 w-10 h-1.5 rounded-full bg-surface-container-highest" aria-hidden="true"></div>
                            <div class="flex items-center justify-between gap-3">
                                <h2 id="filters-title" class="text-[17px] font-extrabold text-on-surface">Filtres</h2>
                                <div class="flex items-center gap-1">
                                    <a href="{{ $resetUrl }}" class="text-[13px] font-semibold text-secondary hover:underline px-2 py-1">Réinitialiser</a>
                                    <button type="button" class="md:hidden w-9 h-9 flex items-center justify-center rounded-full bg-surface-container-low hover:bg-surface-container" @click="filtersOpen = false" aria-label="Fermer les filtres">
                                        <x-icon name="close" class="text-[18px]" />
                                    </button>
                                </div>
                            </div>
                        </div>

                        @if(request('q'))
                            <input type="hidden" name="q" value="{{ request('q') }}" />
                        @endif

                        {{-- Sheet body --}}
                        <div class="flex-1 overflow-y-auto overscroll-contain px-5 divide-y divide-surface-variant" x-data="{ min: @js(request('prix_min', '')), max: @js(request('prix_max', '')) }">
                            <div role="group" aria-labelledby="fg-1" class="py-4">
                                <p id="fg-1" class="text-[13px] font-bold uppercase tracking-wide text-on-surface-variant mb-2.5">Trier par</p>
                                <div class="grid grid-cols-3 gap-1 p-1 rounded-xl bg-surface-container-low">
                                    @foreach(['recent' => 'Récentes', 'prix_asc' => 'Moins cher', 'prix_desc' => 'Plus cher'] as $value => $label)
                                        <label class="cursor-pointer">
                                            <input type="radio" name="tri" value="{{ $value }}" class="peer sr-only" @checked(request('tri', 'recent') === $value) />
                                            <span class="flex items-center justify-center text-center h-9 px-1 rounded-lg text-[12px] font-semibold text-on-surface-variant peer-checked:bg-surface-container-lowest peer-checked:text-on-surface peer-checked:shadow-sm peer-focus-visible:ring-2 peer-focus-visible:ring-primary transition">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="py-4">
                                <label for="f-category" class="block text-[13px] font-bold uppercase tracking-wide text-on-surface-variant mb-2.5">Catégorie</label>
                                <select id="f-category" name="categorie" class="field">
                                    <option value="">Toutes les catégories</option>
                                    @foreach($categories as $category)
                                        <optgroup label="{{ $category->name }}">
                                            <option value="{{ $category->slug }}" @selected($currentCategory?->is($category))>Tout « {{ $category->name }} »</option>
                                            @foreach($category->children as $child)
                                                <option value="{{ $child->slug }}" @selected($currentCategory?->is($child))>{{ $child->name }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>

                            <div role="group" aria-labelledby="fg-2" class="py-4">
                                <p id="fg-2" class="text-[13px] font-bold uppercase tracking-wide text-on-surface-variant mb-2.5">Localisation</p>
                                <div class="grid grid-cols-2 md:grid-cols-1 gap-2">
                                    <div>
                                        <label for="f-province" class="sr-only">Province</label>
                                        <select id="f-province" name="province" class="field">
                                            <option value="">Toutes les provinces</option>
                                            @foreach($navProvinces as $slug => $provinceName)
                                                <option value="{{ $slug }}" @selected($currentProvince === $slug)>{{ $provinceName }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="relative">
                                        <label for="f-city" class="sr-only">Ville</label>
                                        <x-icon name="location_on" class="absolute left-3 top-1/2 -translate-y-1/2 text-[16px] text-on-surface-variant pointer-events-none" />
                                        <input id="f-city" type="text" name="ville" value="{{ $currentCity }}" maxlength="100" placeholder="Ville" class="field !pl-9" />
                                    </div>
                                </div>
                            </div>

                            <div role="group" aria-labelledby="fg-3" class="py-4">
                                <p id="fg-3" class="text-[13px] font-bold uppercase tracking-wide text-on-surface-variant mb-2.5">Prix</p>
                                <div class="grid grid-cols-2 md:grid-cols-1 gap-2">
                                    @foreach(['min' => ['f-min', 'prix_min', 'Minimum'], 'max' => ['f-max', 'prix_max', 'Maximum']] as $bound => [$id, $name, $label])
                                        <div class="relative">
                                            <label for="{{ $id }}" class="sr-only">Prix {{ mb_strtolower($label) }}</label>
                                            <input id="{{ $id }}" type="number" inputmode="numeric" min="0" step="500" name="{{ $name }}" x-model="{{ $bound }}" placeholder="{{ $label }}" class="field !pr-14" />
                                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[12px] font-bold text-on-surface-variant pointer-events-none">{{ Price::symbol() }}</span>
                                        </div>
                                    @endforeach
                                </div>
                                {{-- Typical budgets, one tap --}}
                                <div class="flex flex-wrap gap-1.5 mt-2.5">
                                    @foreach([['', 10000, 'Moins de 10 000'], [10000, 50000, '10 000 – 50 000'], [50000, 200000, '50 000 – 200 000'], [200000, '', 'Plus de 200 000']] as [$presetMin, $presetMax, $presetLabel])
                                        <button
                                            type="button"
                                            @click="min = @js((string) $presetMin); max = @js((string) $presetMax)"
                                            class="h-8 px-3 rounded-full border text-[12px] font-semibold transition-colors"
                                            :class="String(min) === @js((string) $presetMin) && String(max) === @js((string) $presetMax) ? 'border-primary bg-primary-fixed text-on-primary-fixed-variant' : 'border-outline-variant text-on-surface hover:border-primary'"
                                        >{{ $presetLabel }}</button>
                                    @endforeach
                                </div>
                            </div>

                            <div role="group" aria-labelledby="fg-4" class="py-4">
                                <p id="fg-4" class="text-[13px] font-bold uppercase tracking-wide text-on-surface-variant mb-2.5">État</p>
                                <div class="grid grid-cols-3 gap-1 p-1 rounded-xl bg-surface-container-low">
                                    @foreach(['' => 'Tous', 'neuf' => 'Neuf', 'occasion' => 'Occasion'] as $value => $label)
                                        <label class="cursor-pointer">
                                            <input type="radio" name="etat" value="{{ $value }}" class="peer sr-only" @checked(request('etat', '') === $value) />
                                            <span class="flex items-center justify-center h-9 rounded-lg text-[13px] font-semibold text-on-surface-variant peer-checked:bg-surface-container-lowest peer-checked:text-on-surface peer-checked:shadow-sm peer-focus-visible:ring-2 peer-focus-visible:ring-primary transition">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Sheet footer --}}
                        <div class="shrink-0 px-5 pt-3 border-t border-surface-variant bg-surface-container-lowest rounded-b-2xl" style="padding-bottom: max(0.75rem, env(safe-area-inset-bottom));">
                            <button type="submit" class="btn btn-primary w-full h-12 text-[15px]">
                                <x-icon name="search" class="text-[18px]" /> Afficher les annonces
                            </button>
                        </div>
                    </form>
                </div>

                <div>
                    @if($listings->isEmpty())
                        <x-empty-state icon="search_off" title="Aucune annonce trouvée" text="Essayez d’élargir votre recherche : une autre province, une fourchette de prix plus large ou moins de mots-clés.">
                            <a href="{{ $resetUrl }}" class="btn btn-outline">Effacer les filtres</a>
                            <a href="{{ route('listings.create') }}" class="btn btn-accent">Déposer une annonce</a>
                        </x-empty-state>
                    @else
                        <div class="grid grid-cols-2 lg:grid-cols-3 gap-2.5 md:gap-4">
                            @foreach($listings as $listing)
                                <x-listing-card :listing="$listing" />
                            @endforeach
                        </div>

                        <div class="mt-8 flex justify-center">{{ $listings->onEachSide(1)->links('pagination.akiba') }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
