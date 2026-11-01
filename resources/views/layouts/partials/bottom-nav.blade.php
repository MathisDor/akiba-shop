@php
    // Members only: visitors browse the public home page without an app-style tab bar.
    $active = $active ?? null;
    $items = [
        ['route' => route('listings.index'), 'keys' => ['listings'], 'icon' => 'listings', 'label' => 'Annonces'],
        ['route' => route('account.conversations.index'), 'keys' => ['chat'], 'icon' => 'chat', 'label' => 'Chat', 'badge' => $unreadMessages],
        null, // "Publier" slot
        ['route' => route('account.favorites'), 'keys' => ['favorites'], 'icon' => 'favorite', 'label' => 'Favoris'],
        ['route' => route('account.profile'), 'keys' => ['account'], 'icon' => 'person', 'label' => 'Compte'],
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
                {{-- Category pages (active "cat-…") are listing pages too --}}
                @php($isActive = in_array($active, $item['keys'], true) || ($item['keys'] === ['listings'] && str_starts_with((string) $active, 'cat-')))
                <a href="{{ $item['route'] }}" class="relative flex flex-col items-center justify-center gap-1 {{ $isActive ? 'text-primary' : 'text-on-surface-variant' }}" @if($isActive) aria-current="page" @endif>
                    <x-icon :name="$item['icon']" class="text-[22px]" :filled="$isActive && in_array($item['icon'], ['favorite', 'person'], true)" />
                    <span class="text-[11px] leading-none {{ $isActive ? 'font-bold' : 'font-medium' }}">{{ $item['label'] }}</span>
                    @if(($item['badge'] ?? 0) > 0)
                        <span class="absolute top-1.5 left-1/2 ml-2 min-w-[18px] h-[18px] px-1 rounded-full bg-error text-on-error text-[10px] font-bold flex items-center justify-center" aria-label="{{ $item['badge'] }} non lu(s)">{{ $item['badge'] > 99 ? '99+' : $item['badge'] }}</span>
                    @endif
                </a>
            @endif
        @endforeach
    </div>
</nav>
