@php($active = $active ?? null)

<nav
    class="fixed left-3 right-3 z-50 mx-auto max-w-[420px] md:hidden rounded-full bg-surface/75 backdrop-blur-xl border border-outline-variant/40 shadow-[0_10px_30px_rgba(0,0,0,0.15)]"
    style="bottom: max(0.75rem, env(safe-area-inset-bottom));"
>
    <div class="flex items-center justify-around gap-0.5 px-1.5 py-2">
        <a href="{{ route('home') }}" class="flex flex-1 flex-col items-center justify-center gap-0.5 py-1.5 rounded-full transition-all duration-200 {{ $active === 'home' ? 'text-primary font-bold' : 'text-on-surface-variant opacity-70 hover:opacity-100' }}">
            <x-icon name="home" class="text-[22px]" :filled="$active === 'home'" />
            <span class="font-label-sm text-[10px] leading-none">Accueil</span>
        </a>
        <a href="{{ route('categories.index') }}" class="flex flex-1 flex-col items-center justify-center gap-0.5 py-1.5 rounded-full transition-all duration-200 {{ $active === 'catalog' ? 'text-primary font-bold' : 'text-on-surface-variant opacity-70 hover:opacity-100' }}">
            <x-icon name="grid_view" class="text-[22px]" :filled="$active === 'catalog'" />
            <span class="font-label-sm text-[10px] leading-none">Catégories</span>
        </a>
        <a href="{{ route('catalog.index', ['sort' => 'nouveautes']) }}" class="flex flex-1 flex-col items-center justify-center gap-0.5 py-1.5 rounded-full transition-all duration-200 {{ $active === 'nouveautes' ? 'text-primary font-bold' : 'text-on-surface-variant opacity-70 hover:opacity-100' }}">
            <x-icon name="auto_awesome" class="text-[22px]" :filled="$active === 'nouveautes'" />
            <span class="font-label-sm text-[10px] leading-none">Nouveautés</span>
        </a>
        <a href="{{ route('account.favorites') }}" class="flex flex-1 flex-col items-center justify-center gap-0.5 py-1.5 rounded-full transition-all duration-200 {{ $active === 'favorites' ? 'text-primary font-bold' : 'text-on-surface-variant opacity-70 hover:opacity-100' }}">
            <x-icon name="favorite" class="text-[22px]" :filled="$active === 'favorites'" />
            <span class="font-label-sm text-[10px] leading-none">Favoris</span>
        </a>
        <a href="{{ auth()->check() ? route('account.profile') : route('login') }}" class="flex flex-1 flex-col items-center justify-center gap-0.5 py-1.5 rounded-full transition-all duration-200 {{ $active === 'account' ? 'text-primary font-bold' : 'text-on-surface-variant opacity-70 hover:opacity-100' }}">
            <x-icon name="person" class="text-[22px]" :filled="$active === 'account'" />
            <span class="font-label-sm text-[10px] leading-none">Compte</span>
        </a>
    </div>
</nav>
