<div
    x-show="drawerOpen"
    x-transition:enter="transition-opacity ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click="drawerOpen = false"
    class="fixed inset-0 bg-inverse-surface/40 backdrop-blur-sm z-[55]"
    style="display: none;"
></div>

<aside
    x-show="drawerOpen"
    x-transition:enter="transition-transform ease-in-out duration-300"
    x-transition:enter-start="-translate-x-full"
    x-transition:enter-end="translate-x-0"
    x-transition:leave="transition-transform ease-in-out duration-300"
    x-transition:leave-start="translate-x-0"
    x-transition:leave-end="-translate-x-full"
    @click.outside="drawerOpen = false"
    class="fixed top-0 left-0 h-full w-80 max-w-[85vw] bg-surface-container-lowest shadow-2xl z-[60] flex flex-col py-8 px-6"
    style="display: none; background-color: var(--color-surface-container-lowest);"
>
    <div class="mb-10 flex items-center justify-between">
        <a href="{{ route('home') }}" class="flex items-center gap-2" @click="drawerOpen = false">
            <img src="{{ asset('logo/logo1.PNG') }}" alt="AkibaShop" class="h-9 w-auto object-contain rounded" />
            <span class="font-headline-lg text-headline-lg text-primary">Menu</span>
        </a>
        <button @click="drawerOpen = false" aria-label="Fermer" class="text-on-surface-variant hover:text-primary transition-colors">
            <x-icon name="close" />
        </button>
    </div>

    <nav class="flex-1 flex flex-col space-y-2">
        <a href="{{ route('categories.index') }}" class="flex items-center space-x-4 p-4 rounded-lg text-on-surface-variant hover:bg-surface-variant/50 transition-colors">
            <x-icon name="grid_view" />
            <span class="font-body-md text-body-md">Toutes les catégories</span>
        </a>
        <a href="{{ route('catalog.gender', 'femme') }}" class="flex items-center space-x-4 p-4 rounded-lg text-primary-container bg-primary-container/10 transition-colors">
            <x-icon name="female" />
            <span class="font-body-md text-body-md">Femme</span>
        </a>
        <a href="{{ route('catalog.gender', 'homme') }}" class="flex items-center space-x-4 p-4 rounded-lg text-on-surface-variant hover:bg-surface-variant/50 transition-colors">
            <x-icon name="male" />
            <span class="font-body-md text-body-md">Homme</span>
        </a>
        <a href="{{ route('catalog.index', ['category' => 'accessoires']) }}" class="flex items-center space-x-4 p-4 rounded-lg text-on-surface-variant hover:bg-surface-variant/50 transition-colors">
            <x-icon name="watch" />
            <span class="font-body-md text-body-md">Accessoires</span>
        </a>
        <a href="{{ route('catalog.index', ['promo' => 1]) }}" class="flex items-center space-x-4 p-4 rounded-lg text-on-surface-variant hover:bg-surface-variant/50 transition-colors">
            <x-icon name="local_offer" />
            <span class="font-body-md text-body-md">Promotions</span>
        </a>
        <a href="{{ route('search') }}" class="flex items-center space-x-4 p-4 rounded-lg text-on-surface-variant hover:bg-surface-variant/50 transition-colors">
            <x-icon name="search" />
            <span class="font-body-md text-body-md">Recherche</span>
        </a>
    </nav>

    <div class="mt-auto border-t border-outline-variant/30 pt-4 flex flex-col gap-2">
        @auth
            <a href="{{ route('account.profile') }}" class="flex items-center space-x-4 p-4 rounded-lg text-on-surface-variant hover:bg-surface-variant/50 transition-colors">
                <x-icon name="person" />
                <span class="font-body-md text-body-md">Mon compte</span>
            </a>
        @else
            <a href="{{ route('login') }}" class="flex items-center space-x-4 p-4 rounded-lg text-on-surface-variant hover:bg-surface-variant/50 transition-colors">
                <x-icon name="login" />
                <span class="font-body-md text-body-md">Se connecter</span>
            </a>
        @endauth
    </div>
</aside>
