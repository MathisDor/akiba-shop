<x-layouts.app active="home">

    {{-- Hero: search first on phones, then the pitch --}}
    <section class="bg-surface-container-lowest border-b border-surface-variant">
        <div class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop pt-5 pb-6 md:py-14 grid md:grid-cols-[1.2fr_1fr] gap-6 md:gap-12 items-center">
            <div class="flex flex-col gap-3 md:gap-4 min-w-0">
                {{-- The listing counter only shows once the number is worth showing off --}}
                @if($listingCount > 500)
                    <p class="inline-flex self-start items-center gap-2 rounded-full bg-primary-fixed text-on-primary-fixed-variant px-3 py-1 text-[12px] font-bold">
                        <span class="w-2 h-2 rounded-full bg-brand"></span>
                        {{ number_format($listingCount, 0, ',', ' ') }} annonces en ligne
                    </p>
                @endif
                <h1 class="text-[28px] leading-[34px] font-extrabold tracking-tight sm:text-display-lg-mobile sm:leading-[38px] md:text-display-lg md:leading-[56px] text-on-surface">
                    Achetez et vendez <span class="text-primary">près de chez vous</span>.
                </h1>
                <p class="text-[15px] leading-6 md:text-body-lg text-on-surface-variant max-w-xl">Voitures, logements, téléphones, meubles, emplois… Publiez gratuitement et discutez directement avec les vendeurs.</p>

                <form method="GET" action="{{ route('listings.index') }}" role="search" class="mt-1 flex flex-col sm:flex-row gap-2 rounded-2xl bg-surface-container-low p-2 md:max-w-xl">
                    <label for="hero-q" class="sr-only">Que recherchez-vous ?</label>
                    <div class="relative flex-1 min-w-0">
                        <x-icon name="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-[18px] text-on-surface-variant pointer-events-none" />
                        <input id="hero-q" type="search" name="q" placeholder="Que recherchez-vous ?" enterkeyhint="search" class="field !pl-10" />
                    </div>
                    <label for="hero-province" class="sr-only">Province</label>
                    <select id="hero-province" name="province" class="field sm:w-52 sm:shrink-0">
                        <option value="">Toutes les provinces</option>
                        @foreach($navProvinces as $slug => $provinceName)
                            <option value="{{ $slug }}">{{ $provinceName }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary">Rechercher</button>
                </form>

                <div class="hidden md:flex items-center gap-3 mt-1">
                    <a href="{{ route('listings.create') }}" class="btn btn-accent h-12 px-6 text-[16px]">
                        <x-icon name="add_circle" class="text-[22px]" /> Déposer une annonce gratuite
                    </a>
                    <a href="{{ route('listings.index') }}" class="btn btn-outline h-12 px-6 text-[16px]">Parcourir les annonces</a>
                </div>
            </div>

            {{-- Trust points: three compact columns on phones, cards on larger screens --}}
            <ul class="grid grid-cols-3 md:grid-cols-1 gap-2 md:gap-3">
                @foreach([
                    ['whatsapp', 'Contact direct', 'Écrivez ou appelez le vendeur en un clic, sans intermédiaire.'],
                    ['verified', 'Annonces vérifiées', 'Chaque annonce est relue par notre équipe avant publication.'],
                    ['location_on', 'En main propre', 'Voyez l’article, puis payez à la remise.'],
                ] as [$icon, $title, $text])
                    <li class="flex flex-col md:flex-row items-center md:items-start text-center md:text-left gap-2 md:gap-3 rounded-2xl bg-surface-container-low p-3 md:p-4">
                        <span class="w-9 h-9 md:w-10 md:h-10 shrink-0 rounded-xl flex items-center justify-center {{ $icon === 'whatsapp' ? 'bg-whatsapp text-on-whatsapp' : 'bg-primary text-on-primary' }}">
                            <x-icon :name="$icon" class="text-[18px] md:text-[20px]" />
                        </span>
                        <div class="min-w-0">
                            <p class="text-[12px] leading-4 md:text-[15px] md:leading-5 font-bold text-on-surface">{{ $title }}</p>
                            <p class="hidden md:block text-[13px] leading-5 text-on-surface-variant">{{ $text }}</p>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    {{-- Categories: a swipeable row on phones, a grid on larger screens --}}
    <section class="max-w-container-max mx-auto pt-6 md:pt-12">
        <div class="flex items-end justify-between mb-3 md:mb-4 px-margin-mobile md:px-margin-desktop">
            <h2 class="text-[20px] leading-7 font-extrabold md:text-headline-md text-on-surface">Catégories</h2>
            <a href="{{ route('categories.index') }}" class="text-[14px] font-semibold text-secondary hover:underline">Tout voir</a>
        </div>
        <div class="flex md:grid md:grid-cols-5 lg:grid-cols-10 gap-1 md:gap-3 overflow-x-auto md:overflow-visible hide-scrollbar snap-x px-margin-mobile md:px-margin-desktop pb-1">
            @foreach($categories as $category)
                <a href="{{ route('categories.show', $category) }}" class="group snap-start shrink-0 w-[78px] md:w-auto flex flex-col items-center gap-2 rounded-2xl p-1.5 md:p-3 text-center hover:bg-surface-container-lowest transition-colors">
                    <span class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-surface-container-lowest border border-surface-variant flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-on-primary group-hover:border-primary transition-colors">
                        <x-icon :name="$category->icon ?? 'package'" class="text-[24px] md:text-[26px]" />
                    </span>
                    <span class="text-[11px] md:text-[12px] leading-[14px] md:leading-4 font-semibold text-on-surface line-clamp-2">{{ $category->name }}</span>
                </a>
            @endforeach
        </div>
    </section>

    @if($featured->isNotEmpty())
        <section class="max-w-container-max mx-auto pt-6 md:pt-12">
            <h2 class="flex items-center gap-2 mb-3 md:mb-4 px-margin-mobile md:px-margin-desktop text-[20px] leading-7 font-extrabold md:text-headline-md text-on-surface">
                <span class="w-7 h-7 md:w-8 md:h-8 rounded-lg bg-tertiary text-on-tertiary flex items-center justify-center"><x-icon name="boost" class="text-[16px] md:text-[18px]" :filled="true" /></span>
                À la une
            </h2>
            <div class="flex gap-3 overflow-x-auto hide-scrollbar snap-x px-margin-mobile md:px-margin-desktop pb-2">
                @foreach($featured as $listing)
                    <div class="w-[44%] sm:w-[30%] md:w-[calc(25%-9px)] shrink-0 snap-start">
                        <x-listing-card :listing="$listing" />
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <section class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop pt-6 md:pt-12">
        <div class="flex items-end justify-between mb-3 md:mb-4">
            <h2 class="text-[20px] leading-7 font-extrabold md:text-headline-md text-on-surface">Annonces récentes</h2>
            <a href="{{ route('listings.index') }}" class="text-[14px] font-semibold text-secondary hover:underline">Voir tout</a>
        </div>

        @if($latest->isEmpty())
            <x-empty-state icon="megaphone" title="Soyez le premier à publier !" text="Déposez votre annonce gratuitement, elle sera vue par des milliers de visiteurs.">
                <a href="{{ route('listings.create') }}" class="btn btn-accent"><x-icon name="add_circle" class="text-[20px]" /> Déposer une annonce</a>
            </x-empty-state>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2.5 md:gap-4">
                @foreach($latest as $i => $listing)
                    {{-- Phones get the first 8 to keep the page short; the rest is one tap away --}}
                    <div class="{{ $i >= 8 ? 'hidden sm:block' : '' }}">
                        <x-listing-card :listing="$listing" />
                    </div>
                @endforeach
            </div>
            <a href="{{ route('listings.index') }}" class="btn btn-outline w-full sm:w-auto sm:mx-auto sm:flex mt-5">Voir toutes les annonces <x-icon name="arrow_forward" class="text-[18px]" /></a>
        @endif
    </section>

    {{-- How it works --}}
    <section class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop pt-8 md:pt-16">
        <h2 class="text-[20px] leading-7 font-extrabold md:text-headline-md text-on-surface mb-3 md:mb-6">Comment ça marche ?</h2>
        <ol class="grid gap-2 md:grid-cols-3 md:gap-4">
            @foreach([
                ['camera', 'Publiez en 2 minutes', 'Quelques photos, un prix, votre province : c’est gratuit.'],
                ['chat', 'Discutez', 'Les acheteurs vous écrivent sur AkibaShop ou par WhatsApp.'],
                ['check_circle', 'Concluez en main propre', 'Rencontrez-vous dans un lieu public et payez à la remise.'],
            ] as $i => [$icon, $title, $text])
                <li class="card flex items-start gap-3 p-4">
                    <span class="relative w-10 h-10 shrink-0 rounded-xl bg-primary-fixed text-primary flex items-center justify-center">
                        <x-icon :name="$icon" class="text-[20px]" />
                        <span class="absolute -top-1.5 -left-1.5 w-5 h-5 rounded-full bg-primary text-on-primary text-[11px] font-bold flex items-center justify-center">{{ $i + 1 }}</span>
                    </span>
                    <div>
                        <p class="text-[15px] font-bold text-on-surface">{{ $title }}</p>
                        <p class="text-[13px] leading-5 text-on-surface-variant">{{ $text }}</p>
                    </div>
                </li>
            @endforeach
        </ol>
    </section>

    {{-- Sell CTA --}}
    <section class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop pt-8 md:pt-16">
        <div class="rounded-3xl bg-primary text-on-primary p-5 md:p-10 grid md:grid-cols-[1fr_auto] gap-4 md:gap-6 items-center">
            <div>
                <h2 class="text-[22px] leading-7 font-extrabold md:text-headline-lg">Vous avez quelque chose à vendre ?</h2>
                <p class="mt-2 text-[15px] leading-6 md:text-body-lg text-on-primary/85 max-w-2xl">Prenez quelques photos avec votre téléphone, fixez votre prix et publiez en 2 minutes. C’est gratuit.</p>
            </div>
            <a href="{{ route('listings.create') }}" class="btn btn-accent w-full md:w-auto h-12 px-6 text-[16px]">Publier mon annonce</a>
        </div>
    </section>

    @guest
        {{-- Visitors have no bottom bar: keep the two key actions within thumb reach --}}
        <div class="md:hidden fixed inset-x-0 bottom-0 z-30 bg-surface-container-lowest/95 backdrop-blur border-t border-surface-variant px-margin-mobile pt-2 flex gap-2" style="padding-bottom: max(0.5rem, env(safe-area-inset-bottom));">
            <a href="{{ route('login') }}" class="btn btn-outline flex-1">Se connecter</a>
            <a href="{{ route('listings.create') }}" class="btn btn-accent flex-[1.4]"><x-icon name="add_circle" class="text-[18px]" /> Publier</a>
        </div>
        <div class="md:hidden h-16" aria-hidden="true"></div>
    @endguest

    @push('head')
        <script type="application/ld+json">{!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => 'AkibaShop',
            'url' => route('home'),
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => route('listings.index').'?q={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endpush
</x-layouts.app>
