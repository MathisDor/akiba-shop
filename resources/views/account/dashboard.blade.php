<x-layouts.app active="dashboard" title="Tableau de bord — AkibaShop">
    <div class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop pt-4 md:pt-6">

        <div class="flex items-center justify-between gap-3">
            <div class="min-w-0">
                <p class="text-[13px] text-on-surface-variant">Bonjour,</p>
                <h1 class="text-headline-md md:text-headline-lg text-on-surface truncate">{{ \Illuminate\Support\Str::before($user->name, ' ') ?: $user->name }} 👋</h1>
            </div>
            <a href="{{ route('listings.create') }}" class="btn btn-accent btn-sm shrink-0"><x-icon name="add" class="text-[18px]" /> Publier</a>
        </div>

        {{-- Activity --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 md:gap-3 mt-4">
            @foreach([
                ['En ligne', $stats['online'], 'check_circle', route('account.listings'), false],
                ['En validation', $stats['pending'], 'clock', route('account.listings'), false],
                ['Messages non lus', $stats['unread'], 'chat', route('account.conversations.index'), $stats['unread'] > 0],
                ['Favoris', $stats['favorites'], 'favorite', route('account.favorites'), false],
            ] as [$label, $value, $icon, $link, $highlight])
                <a href="{{ $link }}" class="card p-3 md:p-4 flex items-center gap-3 hover:border-primary transition-colors {{ $highlight ? '!bg-primary-fixed !border-primary-fixed-dim' : '' }}">
                    <span class="w-10 h-10 shrink-0 rounded-xl flex items-center justify-center {{ $highlight ? 'bg-primary text-on-primary' : 'bg-surface-container-low text-primary' }}">
                        <x-icon :name="$icon" class="text-[20px]" />
                    </span>
                    <span class="min-w-0">
                        <span class="block text-[20px] leading-6 font-extrabold text-on-surface">{{ number_format($value, 0, ',', ' ') }}</span>
                        <span class="block text-[12px] leading-4 text-on-surface-variant truncate">{{ $label }}</span>
                    </span>
                </a>
            @endforeach
        </div>

        {{-- Search --}}
        <form method="GET" action="{{ route('listings.index') }}" role="search" class="mt-4 card p-3 flex flex-col sm:flex-row gap-2">
            <label for="dash-q" class="sr-only">Que recherchez-vous ?</label>
            <input id="dash-q" type="search" name="q" placeholder="Que recherchez-vous ?" class="field flex-1" />
            <label for="dash-province" class="sr-only">Province</label>
            <select id="dash-province" name="province" class="field sm:w-56">
                <option value="">Toutes les provinces</option>
                @foreach($navProvinces as $slug => $provinceName)
                    <option value="{{ $slug }}" @selected($user->province === $slug)>{{ $provinceName }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary"><x-icon name="search" class="text-[18px]" /> Rechercher</button>
        </form>

        {{-- Categories --}}
        <div class="flex gap-2 overflow-x-auto hide-scrollbar -mx-margin-mobile px-margin-mobile md:mx-0 md:px-0 mt-4">
            @foreach($categories as $category)
                <a href="{{ route('categories.show', $category) }}" class="shrink-0 inline-flex items-center gap-2 rounded-full bg-surface-container-lowest border border-outline-variant px-3 py-2 text-[13px] font-semibold text-on-surface hover:border-primary transition-colors">
                    <x-icon :name="$category->icon ?? 'package'" class="text-[16px] text-primary" /> {{ $category->name }}
                </a>
            @endforeach
        </div>

        <section class="mt-6">
            <div class="flex items-end justify-between mb-3">
                <h2 class="text-headline-md text-on-surface">
                    Annonces récentes
                    @if($user->province)
                        <span class="block text-[13px] font-medium text-on-surface-variant">{{ \App\Support\Provinces::name($user->province) }} en premier</span>
                    @endif
                </h2>
                <a href="{{ route('listings.index') }}" class="text-[14px] font-semibold text-secondary hover:underline">Tout voir</a>
            </div>

            @if($latest->isEmpty())
                <x-empty-state icon="megaphone" title="Pas encore d’annonces" text="Soyez le premier à publier dans votre région.">
                    <a href="{{ route('listings.create') }}" class="btn btn-accent">Déposer une annonce</a>
                </x-empty-state>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 md:gap-4">
                    @foreach($latest as $listing)
                        <x-listing-card :listing="$listing" />
                    @endforeach
                </div>
                <div class="flex justify-center mt-6">
                    <a href="{{ route('listings.index') }}" class="btn btn-outline">Voir toutes les annonces <x-icon name="arrow_forward" class="text-[18px]" /></a>
                </div>
            @endif
        </section>
    </div>
</x-layouts.app>
