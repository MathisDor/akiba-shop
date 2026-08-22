<x-layouts.app active="home" title="AkibaShop — La mode qui vous ressemble" description="Découvrez notre sélection de vêtements tendance pour hommes et femmes au Gabon. Livraison à Libreville, paiement Airtel Money et Moov Money.">

    {{-- Hero --}}
    <section class="relative w-full h-[440px] sm:h-[520px] md:h-[720px] bg-surface-container-highest overflow-hidden">
        <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ asset('img/hero-home.jpeg') }}')"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
        <div class="absolute bottom-0 left-0 w-full px-margin-mobile md:px-margin-desktop pb-8 sm:pb-12 md:pb-24 flex flex-col items-start">
            <h1 class="font-display-lg-mobile md:font-display-lg text-display-lg-mobile md:text-display-lg text-on-primary mb-3 md:mb-4 max-w-2xl drop-shadow-lg">La mode qui vous ressemble</h1>
            <p class="font-body-lg text-body-lg text-surface-container-low mb-5 md:mb-8 max-w-xl">Découvrez notre sélection de vêtements tendance pour hommes et femmes.</p>
            <div class="flex flex-col sm:flex-row gap-3 md:gap-4 w-full sm:w-auto">
                <a href="{{ route('catalog.index') }}" class="bg-primary-container text-on-primary-container font-label-lg text-label-lg uppercase py-4 px-8 rounded-full hover:bg-surface-tint hover:text-on-primary transition-colors w-full sm:w-auto text-center min-h-[48px] flex items-center justify-center">
                    Découvrir la collection
                </a>
                <a href="{{ route('catalog.index', ['sort' => 'nouveautes']) }}" class="bg-transparent border border-surface-container-low text-on-primary font-label-lg text-label-lg uppercase py-4 px-8 rounded-full hover:bg-white/10 transition-colors w-full sm:w-auto text-center min-h-[48px] backdrop-blur-sm flex items-center justify-center">
                    Voir les nouveautés
                </a>
            </div>
        </div>
    </section>

    @php
        $sections = [
            ['title' => 'Nouveautés', 'items' => $newArrivals, 'link' => route('catalog.index', ['sort' => 'nouveautes'])],
            ['title' => 'Femme', 'items' => $femme, 'link' => route('catalog.gender', 'femme')],
            ['title' => 'Homme', 'items' => $homme, 'link' => route('catalog.gender', 'homme')],
            ['title' => 'Tendances', 'items' => $trending, 'link' => route('catalog.index')],
            ['title' => 'Promotions', 'items' => $promotions, 'link' => route('catalog.index', ['promo' => 1]), 'compact' => true],
        ];
    @endphp

    @foreach($sections as $section)
        @if($section['items']->isNotEmpty())
            <section class="py-8 md:py-14 px-margin-mobile md:px-margin-desktop">
                <div class="flex justify-between items-end mb-4 md:mb-8">
                    <h2 class="font-headline-md text-headline-md text-on-background">{{ $section['title'] }}</h2>
                    <a class="font-label-sm text-label-sm text-primary hover:underline uppercase" href="{{ $section['link'] }}">Tout voir</a>
                </div>
                <div class="flex overflow-x-auto hide-scrollbar gap-3 md:gap-gutter pb-4 -mx-margin-mobile px-margin-mobile md:mx-0 md:px-0">
                    @foreach($section['items'] as $product)
                        <div class="{{ $section['compact'] ?? false ? 'min-w-[110px] md:min-w-[150px]' : 'min-w-[220px] md:min-w-[280px]' }}">
                            <x-product-card :product="$product" />
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
    @endforeach

    @if($collections->isNotEmpty())
        @php
            $collectionImages = [
                'ete' => 'collection-été.jpeg',
                'soiree' => 'collection-soirée.jpeg',
                'streetwear' => 'collection-streetwear.jpeg',
            ];
            $collectionImageFallbacks = array_values($collectionImages);
        @endphp
        <section class="py-8 md:py-14 px-margin-mobile md:px-margin-desktop bg-surface-container-low">
            <div class="flex justify-between items-end mb-4 md:mb-8">
                <h2 class="font-headline-md text-headline-md text-on-background">Collections</h2>
                <a class="font-label-sm text-label-sm text-primary hover:underline uppercase" href="{{ route('catalog.index') }}">Tout voir</a>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3 md:gap-6">
                @foreach($collections as $i => $collection)
                    @php
                        $image = $collectionImages[$collection->slug] ?? $collectionImageFallbacks[$i % count($collectionImageFallbacks)];
                    @endphp
                    <a href="{{ route('catalog.index', ['collection' => $collection->slug]) }}" class="group relative aspect-[4/3] rounded-xl overflow-hidden bg-surface-container-highest flex items-end p-6 hover-lift">
                        <div class="absolute inset-0 bg-cover bg-center transition-transform duration-700 group-hover:scale-105" style="background-image: url('{{ asset('img/'.$image) }}')"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/10 to-transparent"></div>
                        <h3 class="relative font-headline-md text-headline-md text-on-primary z-10">{{ $collection->name }}</h3>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Testimonials --}}
    @if($reviews->isNotEmpty())
        <section class="py-8 md:py-14 px-margin-mobile md:px-margin-desktop">
            <h2 class="font-headline-md text-headline-md text-on-background mb-4 md:mb-8">Ce que disent nos clients</h2>
            <div class="flex overflow-x-auto hide-scrollbar gap-3 md:gap-gutter pb-4 -mx-margin-mobile px-margin-mobile md:mx-0 md:px-0">
                @foreach($reviews as $review)
                    <div class="min-w-[260px] md:min-w-[320px] flex flex-col gap-3 p-6 rounded-xl bg-surface-container-lowest border border-surface-variant">
                        <div class="flex gap-0.5">
                            @for($i = 1; $i <= 5; $i++)
<x-icon name="star" class="text-[16px] {{ $i <= $review->rating ? 'text-primary' : 'text-outline-variant' }}" :filled="$i <= $review->rating" />
                            @endfor
                        </div>
                        <p class="font-body-md text-body-md text-on-surface line-clamp-4">« {{ $review->comment }} »</p>
                        <div class="mt-auto pt-2 border-t border-surface-variant">
                            <p class="font-label-lg text-label-lg text-on-surface">
                                {{ \Illuminate\Support\Str::before($review->user?->name ?? 'Client AkibaShop', ' ') }}
                                {{ \Illuminate\Support\Str::substr(\Illuminate\Support\Str::after($review->user?->name ?? '', ' '), 0, 1) }}.
                            </p>
                            <p class="font-label-sm text-[11px] text-on-surface-variant">{{ $review->product?->name }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- FAQ --}}
    <section class="py-8 md:py-14 px-margin-mobile md:px-margin-desktop bg-surface-container-low">
        <div class="max-w-2xl mx-auto">
            <h2 class="font-headline-md text-headline-md text-on-background mb-4 md:mb-8 text-center">Questions fréquentes</h2>
            <div x-data="{ open: 0 }" class="flex flex-col gap-2">
                @php
                    $faqs = [
                        ['q' => 'Quels sont les délais de livraison à Libreville ?', 'a' => 'Vos commandes sont livrées sous 7 à 14 jours à Libreville et ses environs. Un SMS vous informe dès que le livreur est en route.'],
                        ['q' => 'Quels moyens de paiement acceptez-vous ?', 'a' => 'Nous acceptons Airtel Money, Moov Money et les cartes bancaires (Visa/Mastercard) via un paiement 100% sécurisé.'],
                        ['q' => 'Puis-je retourner ou échanger un article ?', 'a' => 'Oui, les articles non portés et avec étiquette peuvent être échangés ou retournés sous 7 jours après réception.'],
                        ['q' => 'Comment suivre ma commande ?', 'a' => 'Rendez-vous dans "Mon compte > Mes commandes" pour suivre le statut de votre commande en temps réel.'],
                        ['q' => 'Comment choisir ma taille ?', 'a' => 'Chaque fiche produit indique les mensurations disponibles. En cas de doute, contactez-nous avant de commander.'],
                    ];
                @endphp
                @foreach($faqs as $index => $faq)
                    <div class="rounded-xl bg-surface-container-lowest border border-surface-variant overflow-hidden">
                        <button
                            type="button"
                            @click="open = open === {{ $index }} ? null : {{ $index }}"
                            class="w-full flex items-center justify-between gap-4 p-4 md:p-5 text-left"
                        >
                            <span class="font-label-lg text-label-lg normal-case text-on-surface">{{ $faq['q'] }}</span>
<x-icon name="expand_more" class="text-primary transition-transform duration-300 shrink-0" x-bind:class="open === {{ $index }} ? 'rotate-180' : ''" />
                        </button>
                        <div
                            x-show="open === {{ $index }}"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-cloak
                            class="px-4 md:px-5 pb-4 md:pb-5"
                        >
                            <p class="font-body-md text-body-md text-on-surface-variant">{{ $faq['a'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

</x-layouts.app>
