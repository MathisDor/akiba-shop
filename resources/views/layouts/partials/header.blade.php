@php($active = $active ?? null)

<div x-data="{ drawerOpen: false, searchOpen: false }" @open-drawer.window="drawerOpen = true">
    <header class="fixed top-0 w-full z-50 bg-surface/90 backdrop-blur-md">
        <div class="flex items-center justify-between px-margin-mobile md:px-margin-desktop py-4 max-w-container-max mx-auto">
            <div class="flex items-center gap-4 md:hidden">
                <button @click="drawerOpen = true" aria-label="Menu" class="text-primary hover:opacity-70 transition-opacity">
                    <x-icon name="menu" />
                </button>
            </div>

            <a href="{{ route('home') }}" class="hidden md:flex items-center">
                <img src="{{ asset('logo/logo1.PNG') }}" alt="AkibaShop" class="h-10 w-auto object-contain rounded" />
            </a>

            <a href="{{ route('home') }}" class="md:hidden flex items-center justify-center flex-1">
                <span class="font-headline-md text-headline-md text-primary tracking-wide leading-none">AKIBASHOP</span>
            </a>

            <nav class="hidden md:flex items-center gap-8">
                <a href="{{ route('home') }}" class="font-label-lg text-label-lg uppercase {{ $active === 'home' ? 'text-primary font-bold' : 'text-on-surface-variant hover:text-primary' }} transition-colors">Accueil</a>
                <a href="{{ route('catalog.gender', 'femme') }}" class="font-label-lg text-label-lg uppercase {{ $active === 'femme' ? 'text-primary font-bold' : 'text-on-surface-variant hover:text-primary' }} transition-colors">Femme</a>
                <a href="{{ route('catalog.gender', 'homme') }}" class="font-label-lg text-label-lg uppercase {{ $active === 'homme' ? 'text-primary font-bold' : 'text-on-surface-variant hover:text-primary' }} transition-colors">Homme</a>
                <a href="{{ route('catalog.index', ['sort' => 'nouveautes']) }}" class="font-label-lg text-label-lg uppercase {{ $active === 'nouveautes' ? 'text-primary font-bold' : 'text-on-surface-variant hover:text-primary' }} transition-colors">Nouveautés</a>
                <a href="{{ route('catalog.index', ['promo' => 1]) }}" class="font-label-lg text-label-lg uppercase {{ $active === 'promotions' ? 'text-primary font-bold' : 'text-on-surface-variant hover:text-primary' }} transition-colors">Promotions</a>
            </nav>

            <div class="flex items-center gap-4">
                <a href="{{ route('search') }}" class="text-primary hover:opacity-70 transition-opacity hidden md:block" aria-label="Recherche">
                    <x-icon name="search" />
                </a>
                <button @click="searchOpen = !searchOpen" type="button" class="text-primary hover:opacity-70 transition-opacity md:hidden" :aria-expanded="searchOpen.toString()" aria-label="Recherche">
                    <x-icon name="search" />
                </button>
                <a href="{{ route('account.favorites') }}" class="text-primary hover:opacity-70 transition-opacity hidden md:block" aria-label="Favoris">
                    <x-icon name="favorite" />
                </a>
                <a href="{{ auth()->check() ? route('account.profile') : route('login') }}" class="text-primary hover:opacity-70 transition-opacity hidden md:block" aria-label="Compte">
                    <x-icon name="person" />
                </a>
                @livewire('cart-count')
            </div>
        </div>

        <div
            x-show="searchOpen"
            x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            class="md:hidden border-t border-surface-variant px-margin-mobile py-3"
        >
            <form method="GET" action="{{ route('search') }}">
                <div class="relative">
                    <x-icon name="search" class="absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant" />
                    <input
                        type="search"
                        name="q"
                        placeholder="Rechercher un produit…"
                        class="w-full rounded-full border border-outline-variant bg-surface-container-lowest pl-12 pr-4 py-3 font-body-md text-body-md text-on-surface focus:border-primary focus:ring-0"
                        x-ref="mobileSearchInput"
                        x-init="$watch('searchOpen', value => { if (value) $nextTick(() => $refs.mobileSearchInput.focus()) })"
                    />
                </div>
            </form>
        </div>
    </header>

    @include('layouts.partials.drawer')
</div>
