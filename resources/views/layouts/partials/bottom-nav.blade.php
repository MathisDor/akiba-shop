@php
    $active = $active ?? null;
    $items = [
        ['route' => route('home'), 'key' => 'home', 'icon' => 'home', 'label' => 'Accueil'],
        ['route' => route('listings.index'), 'key' => 'listings', 'icon' => 'search', 'label' => 'Annonces'],
        null, // "Publier" slot
        ['route' => route('account.favorites'), 'key' => 'favorites', 'icon' => 'favorite', 'label' => 'Favoris'],
        ['route' => auth()->check() ? route('account.profile') : route('login'), 'key' => 'account', 'icon' => 'person', 'label' => 'Compte'],
    ];
@endphp

<nav
    aria-label="Navigation principale"
    class="fixed inset-x-0 bottom-0 z-40 md:hidden bg-surface-container-lowest border-t border-surface-variant"
    style="padding-bottom: env(safe-area-inset-bottom);"
>
    <div class="grid grid-cols-5 h-16">
        @foreach($items as $item)
            @if($item === null)
                <a href="{{ route('listings.create') }}" class="flex flex-col items-center justify-center gap-0.5 -mt-5" aria-label="Déposer une annonce">
                    <span class="w-14 h-14 rounded-2xl bg-tertiary text-on-tertiary flex items-center justify-center shadow-[0_6px_16px_rgba(0,0,0,0.18)] ring-4 ring-surface-container-lowest">
                        <x-icon name="add" class="text-[28px]" :stroke-width="2.5" />
                    </span>
                    <span class="text-[11px] font-bold leading-none text-on-surface">Publier</span>
                </a>
            @else
                @php($isActive = $active === $item['key'])
                <a href="{{ $item['route'] }}" class="flex flex-col items-center justify-center gap-1 {{ $isActive ? 'text-primary' : 'text-on-surface-variant' }}" @if($isActive) aria-current="page" @endif>
                    <x-icon :name="$item['icon']" class="text-[22px]" :filled="$isActive && $item['icon'] !== 'search'" />
                    <span class="text-[11px] leading-none {{ $isActive ? 'font-bold' : 'font-medium' }}">{{ $item['label'] }}</span>
                </a>
            @endif
        @endforeach
    </div>
</nav>
