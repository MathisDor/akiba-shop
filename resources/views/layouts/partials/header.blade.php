@php($active = $active ?? null)

<header class="sticky top-0 z-40 bg-surface-container-lowest/95 backdrop-blur border-b border-surface-variant">
    <div class="flag-band"></div>

    <div class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop">
        <div class="flex items-center gap-3 md:gap-6 h-[60px] md:h-[72px]">
            <a href="{{ route('home') }}" aria-label="AkibaShop — accueil" class="shrink-0">
                <x-logo />
            </a>

            {{-- Desktop search: what + where --}}
            <form method="GET" action="{{ route('listings.index') }}" role="search" class="hidden md:flex flex-1 items-stretch max-w-2xl h-12 rounded-xl border border-outline-variant bg-surface-container-lowest focus-within:border-primary focus-within:ring-3 focus-within:ring-primary/15 transition-shadow">
                <label for="header-q" class="sr-only">Que recherchez-vous ?</label>
                <div class="flex flex-1 items-center gap-2 pl-4 min-w-0">
                    <x-icon name="search" class="text-[20px] text-on-surface-variant" />
                    <input id="header-q" type="search" name="q" value="{{ request('q') }}" placeholder="Que recherchez-vous ? Voiture, iPhone, studio…" class="w-full min-w-0 bg-transparent border-0 p-0 text-[15px] focus:outline-none focus:ring-0 placeholder:text-outline" />
                </div>
                <label for="header-city" class="sr-only">Ville</label>
                <div class="flex items-center gap-1 border-l border-outline-variant pl-3 pr-1">
                    <x-icon name="location_on" class="text-[18px] text-on-surface-variant" />
                    <select id="header-city" name="ville" class="bg-transparent border-0 py-0 pl-1 pr-6 text-[14px] font-semibold text-on-surface focus:outline-none focus:ring-0 cursor-pointer">
                        <option value="">Tout le Gabon</option>
                        @foreach($navCities as $city)
                            <option value="{{ $city->slug }}" @selected(request('ville') === $city->slug)>{{ $city->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="m-1 px-5 rounded-lg bg-primary text-on-primary font-bold text-[14px] hover:bg-on-primary-fixed-variant transition-colors">
                    Rechercher
                </button>
            </form>

            <div class="flex items-center gap-1 md:gap-2 ml-auto">
                <a href="{{ route('listings.create') }}" class="btn btn-accent hidden md:inline-flex">
                    <x-icon name="add_circle" class="text-[20px]" />
                    Déposer une annonce
                </a>
                <a href="{{ route('account.favorites') }}" class="hidden md:flex w-11 h-11 items-center justify-center rounded-xl text-on-surface-variant hover:bg-surface-container-low hover:text-primary transition-colors {{ $active === 'favorites' ? 'text-primary' : '' }}" aria-label="Mes favoris">
                    <x-icon name="favorite" class="text-[22px]" />
                </a>
                @auth
                    <a href="{{ route('account.profile') }}" class="flex items-center gap-2 h-11 pl-1 pr-1 md:pr-3 rounded-xl hover:bg-surface-container-low transition-colors" aria-label="Mon compte">
                        <span class="w-9 h-9 rounded-full bg-primary-fixed text-on-primary-fixed-variant flex items-center justify-center font-bold text-[14px]">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
                        <span class="hidden lg:inline text-[14px] font-semibold text-on-surface max-w-[120px] truncate">{{ \Illuminate\Support\Str::before(auth()->user()->name, ' ') }}</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="flex items-center gap-2 h-11 px-2 md:px-3 rounded-xl text-on-surface hover:bg-surface-container-low transition-colors">
                        <x-icon name="person" class="text-[22px]" />
                        <span class="text-[14px] font-semibold">Se connecter</span>
                    </a>
                @endauth
            </div>
        </div>

        {{-- Mobile search --}}
        <form method="GET" action="{{ route('listings.index') }}" role="search" class="md:hidden pb-3">
            <label for="header-q-mobile" class="sr-only">Que recherchez-vous ?</label>
            <div class="flex items-center gap-2 h-11 px-3 rounded-xl bg-surface-container-low border border-transparent focus-within:border-primary focus-within:bg-surface-container-lowest transition-colors">
                <x-icon name="search" class="text-[20px] text-on-surface-variant" />
                <input id="header-q-mobile" type="search" name="q" value="{{ request('q') }}" placeholder="Rechercher sur AkibaShop" class="w-full min-w-0 bg-transparent border-0 p-0 text-[15px] focus:outline-none focus:ring-0 placeholder:text-outline" />
                @if(request('ville'))
                    <input type="hidden" name="ville" value="{{ request('ville') }}" />
                @endif
            </div>
        </form>
    </div>

    {{-- Category strip (desktop) --}}
    <nav aria-label="Catégories" class="hidden md:block border-t border-surface-variant">
        <div class="max-w-container-max mx-auto px-margin-desktop flex items-center gap-1 overflow-x-auto hide-scrollbar h-11">
            @foreach($navCategories as $category)
                <a href="{{ route('categories.show', $category) }}" class="shrink-0 px-3 py-1.5 rounded-lg text-[13px] font-semibold whitespace-nowrap transition-colors {{ $active === 'cat-'.$category->slug ? 'bg-primary-fixed text-on-primary-fixed-variant' : 'text-on-surface-variant hover:text-primary hover:bg-surface-container-low' }}">
                    {{ $category->name }}
                </a>
            @endforeach
        </div>
    </nav>
</header>
