@php($current = $current ?? null)

<nav aria-label="Mon compte" class="flex gap-2 overflow-x-auto hide-scrollbar -mx-margin-mobile px-margin-mobile md:mx-0 md:px-0 mb-6">
    @foreach([
        ['account.profile', 'profile', 'person', 'Mon profil'],
        ['account.listings', 'listings', 'listings', 'Mes annonces'],
        ['account.favorites', 'favorites', 'favorite', 'Favoris'],
    ] as [$route, $key, $icon, $label])
        <a href="{{ route($route) }}" class="shrink-0 inline-flex items-center gap-2 rounded-full px-4 py-2 text-[14px] font-semibold border transition-colors {{ $current === $key ? 'bg-on-surface text-surface border-on-surface' : 'bg-surface-container-lowest border-outline-variant text-on-surface hover:border-primary' }}" @if($current === $key) aria-current="page" @endif>
            <x-icon :name="$icon" class="text-[18px]" /> {{ $label }}
        </a>
    @endforeach
</nav>
