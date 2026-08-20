@props(['active' => null, 'title' => null])
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title ? $title.' — Admin AkibaShop' : 'Dashboard — Admin AkibaShop' }}</title>
    <meta name="robots" content="noindex" />
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any" />
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}" />
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,opsz,wght@0,6..96,400..900;1,6..96,400..900&family=Manrope:wght@200..800&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="antialiased bg-surface-container-low text-on-background" x-data="{ sidebarOpen: false }">
    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <aside
            class="fixed md:static inset-y-0 left-0 z-40 w-72 min-h-screen bg-inverse-surface text-inverse-on-surface flex flex-col py-8 px-6 transition-transform duration-300 md:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 mb-10">
                <img src="{{ asset('logo/logo1.PNG') }}" alt="AkibaShop" class="h-9 w-9 rounded object-cover" />
                <span class="font-headline-md text-headline-md text-inverse-on-surface">AkibaShop</span>
            </a>

            <nav class="flex-1 flex flex-col gap-1 overflow-y-auto">
                @php
                    $currentAdmin = auth('admin')->user();
                    $links = [
                        ['route' => 'admin.dashboard', 'key' => 'dashboard', 'icon' => 'space_dashboard', 'label' => 'Tableau de bord', 'permission' => null],
                        ['route' => 'admin.products.index', 'key' => 'products', 'icon' => 'apparel', 'label' => 'Produits', 'permission' => 'products.manage'],
                        ['route' => 'admin.categories.index', 'key' => 'categories', 'icon' => 'category', 'label' => 'Catégories', 'permission' => 'categories.manage'],
                        ['route' => 'admin.collections.index', 'key' => 'collections', 'icon' => 'style', 'label' => 'Collections', 'permission' => 'collections.manage'],
                        ['route' => 'admin.orders.index', 'key' => 'orders', 'icon' => 'shopping_bag', 'label' => 'Commandes', 'permission' => 'orders.manage'],
                        ['route' => 'admin.customers.index', 'key' => 'customers', 'icon' => 'group', 'label' => 'Clients', 'permission' => 'customers.view'],
                        ['route' => 'admin.promotions.index', 'key' => 'promotions', 'icon' => 'sell', 'label' => 'Promotions', 'permission' => 'promotions.manage'],
                        ['route' => 'admin.coupons.index', 'key' => 'coupons', 'icon' => 'confirmation_number', 'label' => 'Coupons', 'permission' => 'coupons.manage'],
                        ['route' => 'admin.delivery-zones.index', 'key' => 'delivery', 'icon' => 'local_shipping', 'label' => 'Livraison', 'permission' => 'delivery.manage'],
                        ['route' => 'admin.reviews.index', 'key' => 'reviews', 'icon' => 'star', 'label' => 'Avis clients', 'permission' => 'reviews.moderate'],
                    ];
                @endphp

                @foreach($links as $link)
                    @continue($link['permission'] && ! $currentAdmin?->hasPermission($link['permission']))
                    <a
                        href="{{ route($link['route']) }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-lg font-body-md text-body-md transition-colors {{ $active === $link['key'] ? 'bg-primary text-on-primary' : 'text-inverse-on-surface/70 hover:bg-inverse-on-surface/10 hover:text-inverse-on-surface' }}"
                    >
                        <x-icon :name="$link['icon']" class="text-[20px]" />
                        {{ $link['label'] }}
                    </a>
                @endforeach

                @if($currentAdmin?->isSuperAdmin())
                    <div class="mt-4 pt-4 border-t border-inverse-on-surface/10">
                        <p class="px-4 mb-2 font-label-sm text-[11px] uppercase tracking-widest text-inverse-on-surface/40">Super admin</p>
                        <a href="{{ route('admin.admins.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg font-body-md text-body-md transition-colors {{ $active === 'admins' ? 'bg-primary text-on-primary' : 'text-inverse-on-surface/70 hover:bg-inverse-on-surface/10 hover:text-inverse-on-surface' }}">
                            <x-icon name="admin_panel_settings" class="text-[20px]" />
                            Administrateurs
                        </a>
                        <a href="{{ route('admin.activity-log.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg font-body-md text-body-md transition-colors {{ $active === 'activity-log' ? 'bg-primary text-on-primary' : 'text-inverse-on-surface/70 hover:bg-inverse-on-surface/10 hover:text-inverse-on-surface' }}">
                            <x-icon name="history" class="text-[20px]" />
                            Journal d'activité
                        </a>
                    </div>
                @endif
            </nav>

            <div class="mt-auto pt-4 border-t border-inverse-on-surface/10 flex flex-col gap-2">
                <a href="{{ route('home') }}" target="_blank" class="flex items-center gap-3 px-4 py-3 rounded-lg font-body-md text-body-md text-inverse-on-surface/70 hover:bg-inverse-on-surface/10 hover:text-inverse-on-surface transition-colors">
                    <x-icon name="storefront" class="text-[20px]" />
                    Voir la boutique
                </a>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg font-body-md text-body-md text-error hover:bg-error-container/20 transition-colors">
                        <x-icon name="logout" class="text-[20px]" />
                        Se déconnecter
                    </button>
                </form>
            </div>
        </aside>

        <div
            x-show="sidebarOpen"
            @click="sidebarOpen = false"
            class="fixed inset-0 bg-inverse-surface/40 z-30 md:hidden"
            style="display: none;"
        ></div>

        {{-- Main --}}
        <div class="flex-1 flex flex-col min-w-0">
            <header class="sticky top-0 z-20 bg-surface-container-lowest border-b border-surface-variant px-4 md:px-6 py-3 md:py-4 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 md:gap-4 min-w-0">
                    <button @click="sidebarOpen = true" class="md:hidden text-on-surface-variant shrink-0">
                        <x-icon name="menu" class="text-[24px]" />
                    </button>
                    <h1 class="font-headline-md text-headline-md text-on-surface truncate">{{ $title ?? 'Tableau de bord' }}</h1>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    <div class="w-9 h-9 rounded-full bg-primary text-on-primary flex items-center justify-center font-label-sm text-label-sm font-bold">
                        {{ strtoupper(substr(auth('admin')->user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <span class="font-body-md text-body-md text-on-surface hidden sm:inline">{{ auth('admin')->user()->name ?? '' }}</span>
                </div>
            </header>

            <main class="flex-1 p-4 md:p-6">
                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
</body>
</html>
