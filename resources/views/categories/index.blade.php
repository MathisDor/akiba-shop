@php
    $sections = [
        'femme' => ['label' => 'Femme', 'image' => asset('img/hero-femme.jpeg'), 'categories' => $femme],
        'homme' => ['label' => 'Homme', 'image' => asset('img/hero-homme.jpeg'), 'categories' => $homme],
    ];
    $initialTab = in_array(request('gender'), ['femme', 'homme'], true) ? request('gender') : 'femme';
@endphp

<x-layouts.app
    active="catalog"
    title="Catégories — AkibaShop"
    description="Parcourez les catégories AkibaShop pour Homme et Femme : robes, chemises, jeans, accessoires et plus encore."
>
    <div class="px-margin-mobile md:px-margin-desktop py-6 md:py-8 max-w-container-max mx-auto">
        <h1 class="font-headline-xl text-headline-xl text-primary mb-2">Catégories</h1>
        <p class="font-body-lg text-body-lg text-on-surface-variant">Trouvez votre style, par genre et par catégorie.</p>
    </div>

    <div x-data="{ tab: '{{ $initialTab }}' }" class="max-w-container-max mx-auto">
        {{-- Tabs --}}
        <div class="sticky top-[72px] md:top-[88px] z-30 bg-surface/95 backdrop-blur-md px-margin-mobile md:px-margin-desktop py-3 border-b border-surface-container-highest">
            <div class="grid grid-cols-2 gap-2 md:inline-grid md:w-auto rounded-full bg-surface-container-low p-1">
                <button
                    type="button"
                    @click="tab = 'femme'"
                    :class="tab === 'femme' ? 'bg-primary text-on-primary shadow-sm' : 'text-on-surface-variant hover:text-primary'"
                    class="px-6 py-3 rounded-full font-label-lg text-label-lg uppercase tracking-wide transition-colors duration-200"
                >
                    Femme
                </button>
                <button
                    type="button"
                    @click="tab = 'homme'"
                    :class="tab === 'homme' ? 'bg-primary text-on-primary shadow-sm' : 'text-on-surface-variant hover:text-primary'"
                    class="px-6 py-3 rounded-full font-label-lg text-label-lg uppercase tracking-wide transition-colors duration-200"
                >
                    Homme
                </button>
            </div>
        </div>

        <div class="px-margin-mobile md:px-margin-desktop py-8">
            @foreach($sections as $gender => $section)
                <section
                    x-show="tab === '{{ $gender }}'"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-cloak
                >
                    {{-- Gender hero tile --}}
                    <a
                        href="{{ route('catalog.gender', $gender) }}"
                        class="group relative block w-full h-[220px] md:h-[320px] rounded-xl overflow-hidden bg-surface-container-highest mb-6 hover-lift"
                    >
                        <div class="absolute inset-0 bg-cover bg-center transition-transform duration-700 group-hover:scale-105" style="background-image: url('{{ $section['image'] }}')"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/10 to-transparent"></div>
                        <div class="absolute bottom-0 left-0 p-6 md:p-10 flex items-end justify-between w-full">
                            <h2 class="font-display-lg-mobile md:font-headline-xl text-display-lg-mobile md:text-headline-xl text-on-primary">{{ $section['label'] }}</h2>
                            <span class="hidden md:flex items-center gap-2 font-label-lg text-label-lg uppercase text-on-primary border border-on-primary/60 rounded-full px-6 py-3 group-hover:bg-on-primary group-hover:text-primary transition-colors">
                                Découvrir
<x-icon name="arrow_forward" class="text-[18px]" />
                            </span>
                        </div>
                    </a>

                    {{-- Sub-categories grid --}}
                    @if($section['categories']->isNotEmpty())
                        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3 md:gap-4">
                            @foreach($section['categories'] as $category)
                                <a
                                    href="{{ route('catalog.category', [$gender, $category->slug]) }}"
                                    class="flex flex-col items-center justify-center text-center gap-1 p-4 md:p-6 rounded-xl bg-surface-container-lowest border border-surface-variant hover:border-primary hover:shadow-[0_10px_30px_rgba(0,0,0,0.06)] transition-all duration-300"
                                >
                                    <span class="font-body-md text-body-md text-on-surface">{{ $category->name }}</span>
                                    <span class="font-label-sm text-[11px] text-on-surface-variant">{{ $category->products_count }} article{{ $category->products_count > 1 ? 's' : '' }}</span>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="font-body-md text-body-md text-on-surface-variant">Aucune catégorie disponible pour le moment.</p>
                    @endif
                </section>
            @endforeach
        </div>
    </div>
</x-layouts.app>
