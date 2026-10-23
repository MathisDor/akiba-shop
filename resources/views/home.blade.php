<x-layouts.app active="home">

    {{-- Hero: one job — get people searching or publishing --}}
    <section class="bg-surface-container-lowest border-b border-surface-variant">
        <div class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop py-6 md:py-12 grid md:grid-cols-[1.2fr_1fr] gap-6 md:gap-12 items-center">
            <div class="flex flex-col gap-4">
                <p class="inline-flex self-start items-center gap-2 rounded-full bg-primary-fixed text-on-primary-fixed-variant px-3 py-1 text-[12px] font-bold">
                    <span class="w-2 h-2 rounded-full bg-gabon-green"></span>
                    @if($listingCount > 0)
                        {{ number_format($listingCount, 0, ',', ' ') }} annonce{{ $listingCount > 1 ? 's' : '' }} en ligne au Gabon
                    @else
                        Petites annonces gratuites au Gabon
                    @endif
                </p>
                <h1 class="text-display-lg-mobile md:text-display-lg text-on-surface">
                    Achetez et vendez <span class="text-primary">près de chez vous</span>.
                </h1>
                <p class="text-body-lg text-on-surface-variant max-w-xl">Voitures, logements, téléphones, meubles, emplois… Publiez gratuitement et discutez directement avec les vendeurs sur WhatsApp.</p>

                <form method="GET" action="{{ route('listings.index') }}" role="search" class="md:hidden flex flex-col gap-2 mt-1">
                    <label for="hero-city" class="sr-only">Ville</label>
                    <select id="hero-city" name="ville" class="field">
                        <option value="">Tout le Gabon</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->slug }}">{{ $city->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary">
                        <x-icon name="search" class="text-[20px]" /> Voir les annonces
                    </button>
                </form>

                <div class="hidden md:flex items-center gap-3 mt-2">
                    <a href="{{ route('listings.create') }}" class="btn btn-accent h-12 px-6 text-[16px]">
                        <x-icon name="add_circle" class="text-[22px]" /> Déposer une annonce gratuite
                    </a>
                    <a href="{{ route('listings.index') }}" class="btn btn-outline h-12 px-6 text-[16px]">Parcourir les annonces</a>
                </div>
            </div>

            {{-- Trust points, the Gabonese way of buying: WhatsApp, meet in person, pay on hand-over --}}
            <ul class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-1 gap-3">
                @foreach([
                    ['whatsapp', 'Contact direct sur WhatsApp', 'Écrivez ou appelez le vendeur en un clic, sans intermédiaire.'],
                    ['verified', 'Annonces vérifiées', 'Chaque annonce est relue par notre équipe avant publication.'],
                    ['location_on', 'Rencontre en main propre', 'Voyez l’article, puis payez à la remise — en espèces ou Mobile Money.'],
                ] as [$icon, $title, $text])
                    <li class="flex items-start gap-3 rounded-2xl bg-surface-container-low p-4">
                        <span class="w-10 h-10 shrink-0 rounded-xl flex items-center justify-center {{ $icon === 'whatsapp' ? 'bg-whatsapp text-on-whatsapp' : 'bg-primary text-on-primary' }}">
                            <x-icon :name="$icon" class="text-[20px]" />
                        </span>
                        <div>
                            <p class="text-[15px] font-bold text-on-surface">{{ $title }}</p>
                            <p class="text-[13px] leading-5 text-on-surface-variant">{{ $text }}</p>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    {{-- Categories --}}
    <section class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop pt-8 md:pt-12">
        <div class="flex items-end justify-between mb-4">
            <h2 class="text-headline-md text-on-surface">Catégories</h2>
            <a href="{{ route('categories.index') }}" class="text-[14px] font-semibold text-secondary hover:underline">Tout voir</a>
        </div>
        <div class="grid grid-cols-4 sm:grid-cols-5 lg:grid-cols-10 gap-2 md:gap-3">
            @foreach($categories as $category)
                <a href="{{ route('categories.show', $category) }}" class="group flex flex-col items-center gap-2 rounded-2xl p-2 md:p-3 text-center hover:bg-surface-container-lowest transition-colors">
                    <span class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-surface-container-lowest border border-surface-variant flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-on-primary group-hover:border-primary transition-colors">
                        <x-icon :name="$category->icon ?? 'package'" class="text-[26px]" />
                    </span>
                    <span class="text-[12px] leading-4 font-semibold text-on-surface line-clamp-2">{{ $category->name }}</span>
                </a>
            @endforeach
        </div>
    </section>

    @if($featured->isNotEmpty())
        <section class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop pt-8 md:pt-12">
            <div class="flex items-end justify-between mb-4">
                <h2 class="text-headline-md text-on-surface flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-tertiary text-on-tertiary flex items-center justify-center"><x-icon name="boost" class="text-[18px]" :filled="true" /></span>
                    À la une
                </h2>
            </div>
            <div class="flex gap-3 overflow-x-auto hide-scrollbar -mx-margin-mobile px-margin-mobile md:mx-0 md:px-0 pb-2 snap-x">
                @foreach($featured as $listing)
                    <div class="w-[46%] sm:w-[30%] md:w-[calc(25%-9px)] shrink-0 snap-start">
                        <x-listing-card :listing="$listing" />
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <section class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop pt-8 md:pt-12">
        <div class="flex items-end justify-between mb-4">
            <h2 class="text-headline-md text-on-surface">Annonces récentes</h2>
            <a href="{{ route('listings.index') }}" class="text-[14px] font-semibold text-secondary hover:underline">Voir tout</a>
        </div>

        @if($latest->isEmpty())
            <x-empty-state icon="megaphone" title="Soyez le premier à publier !" text="AkibaShop vient d’ouvrir ses petites annonces. Déposez la vôtre gratuitement, elle sera vue par tout le Gabon.">
                <a href="{{ route('listings.create') }}" class="btn btn-accent"><x-icon name="add_circle" class="text-[20px]" /> Déposer une annonce</a>
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

    {{-- Sell CTA --}}
    <section class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop pt-10 md:pt-16">
        <div class="relative overflow-hidden rounded-3xl bg-primary text-on-primary p-6 md:p-10 grid md:grid-cols-[1fr_auto] gap-6 items-center">
            <div class="absolute inset-y-0 right-0 w-2 md:w-3 flex flex-col" aria-hidden="true">
                <span class="flex-1 bg-gabon-green"></span><span class="flex-1 bg-gabon-yellow"></span><span class="flex-1 bg-gabon-blue"></span>
            </div>
            <div>
                <h2 class="text-headline-lg">Vous avez quelque chose à vendre ?</h2>
                <p class="mt-2 text-body-lg text-on-primary/85 max-w-2xl">Prenez quelques photos avec votre téléphone, fixez votre prix en FCFA et publiez en 2 minutes. C’est gratuit.</p>
            </div>
            <a href="{{ route('listings.create') }}" class="btn btn-accent h-12 px-6 text-[16px] justify-self-start md:justify-self-end">Publier mon annonce</a>
        </div>
    </section>

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
