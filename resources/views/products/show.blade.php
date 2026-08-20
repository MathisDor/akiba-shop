@php
    $primaryImage = $product->images->firstWhere('is_primary', true) ?? $product->images->first();
    $imageUrl = fn($img) => \Illuminate\Support\Facades\Storage::disk('public')->url($img->path);
    // Keep the color shown here in sync with the image shown on product
    // listing cards (which use this same primary image), so clicking
    // through from a card doesn't land on a different color.
    $initialColorId = $primaryImage?->color_id ?? $product->variants->first()?->color_id;
    // Lead the swatch row with whichever color is selected by default, so
    // the highlighted swatch is always the first one shown.
    $sortedColors = $product->colors->sortByDesc(fn ($color) => $color->id === $initialColorId)->values();
    $galleryImages = $product->images->map(fn ($img) => [
        'id' => $img->id,
        'url' => $imageUrl($img),
        'colorId' => $img->color_id,
    ])->values();
@endphp

<x-layouts.app
    :title="$product->name.' — AkibaShop'"
    :description="\Illuminate\Support\Str::limit(strip_tags($product->description), 155)"
    :og-image="$primaryImage ? $imageUrl($primaryImage) : null"
>
    @push('head')
        <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org/',
            '@type' => 'Product',
            'name' => $product->name,
            'image' => $product->images->map($imageUrl)->values(),
            'description' => strip_tags($product->description),
            'sku' => $product->sku,
            'brand' => ['@type' => 'Brand', 'name' => $product->brand ?? 'AkibaShop'],
            'offers' => [
                '@type' => 'Offer',
                'priceCurrency' => 'XAF',
                'price' => $product->effective_price,
                'availability' => $product->totalStock() > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                'url' => route('products.show', $product),
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
        </script>
        <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org/',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Accueil', 'item' => route('home')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => ucfirst($product->gender), 'item' => route('catalog.gender', $product->gender)],
                ['@type' => 'ListItem', 'position' => 3, 'name' => $product->category->name, 'item' => route('catalog.category', [$product->gender, $product->category->slug])],
                ['@type' => 'ListItem', 'position' => 4, 'name' => $product->name, 'item' => route('products.show', $product)],
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
        </script>
    @endpush

    <div class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop mt-4">
        <nav class="hidden md:flex mb-6 text-on-surface-variant font-label-sm text-label-sm items-center space-x-2">
            <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Accueil</a>
<x-icon name="chevron_right" class="text-[14px]" />
            <a href="{{ route('catalog.gender', $product->gender) }}" class="hover:text-primary transition-colors capitalize">{{ $product->gender }}</a>
<x-icon name="chevron_right" class="text-[14px]" />
            <a href="{{ route('catalog.category', [$product->gender, $product->category->slug]) }}" class="hover:text-primary transition-colors">{{ $product->category->name }}</a>
<x-icon name="chevron_right" class="text-[14px]" />
            <span class="text-primary font-bold">{{ $product->name }}</span>
        </nav>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 md:gap-16">
            {{-- Gallery --}}
            <section
                class="md:col-span-7 flex flex-col md:flex-row gap-4"
                x-data="{
                    images: {{ Illuminate\Support\Js::from($galleryImages) }},
                    activeColorId: {{ $initialColorId ?? 'null' }},
                    active: 0,
                    touchStartX: 0,
                    get visible() {
                        if (this.activeColorId === null) return this.images;
                        const forColor = this.images.filter(i => i.colorId === this.activeColorId);
                        return forColor.length ? forColor : this.images.filter(i => i.colorId === null);
                    },
                    next() { if (this.visible.length) this.active = (this.active + 1) % this.visible.length; },
                    prev() { if (this.visible.length) this.active = this.active === 0 ? this.visible.length - 1 : this.active - 1; },
                    onTouchStart(e) { this.touchStartX = e.changedTouches[0].clientX; },
                    onTouchEnd(e) {
                        const dx = e.changedTouches[0].clientX - this.touchStartX;
                        if (Math.abs(dx) > 40) { dx < 0 ? this.next() : this.prev(); }
                    },
                }"
                @color-picked.window="activeColorId = $event.detail.colorId; active = 0"
            >
                <div class="hidden md:flex flex-col gap-4 w-24 shrink-0">
                    <template x-for="(image, i) in visible" :key="image.id">
                        <button @click="active = i" class="w-full aspect-[3/4] rounded-lg overflow-hidden border-2 transition-colors" :class="active === i ? 'border-primary' : 'border-transparent hover:border-outline-variant'">
                            <img :src="image.url" alt="{{ $product->name }}" class="w-full h-full object-cover" />
                        </button>
                    </template>
                </div>

                <div class="flex-1 flex flex-col gap-3">
                    <div
                        class="group relative w-full rounded-xl overflow-hidden aspect-[3/4] md:aspect-[4/5] bg-surface-container shadow-[0_20px_40px_rgba(0,0,0,0.04)]"
                        @touchstart.passive="onTouchStart($event)"
                        @touchend.passive="onTouchEnd($event)"
                    >
                        <template x-for="(image, i) in visible" :key="image.id">
                            <img
                                x-show="active === i"
                                :src="image.url"
                                alt="{{ $product->name }}"
                                class="w-full h-full object-cover transition-transform duration-500 md:group-hover:scale-110 select-none"
                                draggable="false"
                            />
                        </template>

                        @if($product->discount_percent)
                            <div class="absolute top-4 left-4">
                                <span class="bg-error text-on-error font-label-sm text-label-sm px-4 py-2 rounded-full uppercase tracking-wider shadow-sm">-{{ $product->discount_percent }}%</span>
                            </div>
                        @endif

                        <template x-if="visible.length > 1">
                            <div>
                                <button @click="prev" class="hidden md:flex absolute left-3 top-1/2 -translate-y-1/2 w-10 h-10 items-center justify-center rounded-full bg-surface/90 text-on-surface shadow-md opacity-0 group-hover:opacity-100 transition-opacity" aria-label="Image précédente">
                                    <x-icon name="chevron_left" />
                                </button>
                                <button @click="next" class="hidden md:flex absolute right-3 top-1/2 -translate-y-1/2 w-10 h-10 items-center justify-center rounded-full bg-surface/90 text-on-surface shadow-md opacity-0 group-hover:opacity-100 transition-opacity" aria-label="Image suivante">
                                    <x-icon name="chevron_right" />
                                </button>

                                <span class="absolute bottom-3 right-3 bg-inverse-surface/70 text-inverse-on-surface font-label-sm text-[11px] px-2.5 py-1 rounded-full">
                                    <span x-text="active + 1"></span>/<span x-text="visible.length"></span>
                                </span>
                            </div>
                        </template>
                    </div>

                    <template x-if="visible.length > 1">
                        <div class="flex md:hidden overflow-x-auto hide-scrollbar gap-2">
                            <template x-for="(image, i) in visible" :key="image.id">
                                <button @click="active = i" class="w-16 h-16 shrink-0 rounded-lg overflow-hidden border-2 transition-colors" :class="active === i ? 'border-primary' : 'border-transparent'">
                                    <img :src="image.url" alt="{{ $product->name }}" class="w-full h-full object-cover" />
                                </button>
                            </template>
                        </div>
                    </template>
                </div>
            </section>

            {{-- Details --}}
            <section class="md:col-span-5 flex flex-col pt-4 md:pt-10">
                @if($product->colors->isNotEmpty())
                    <div class="mb-3" x-data="{ activeColorId: {{ $initialColorId ?? 'null' }} }">
                        <div class="flex items-center gap-2 flex-wrap">
                            @foreach($sortedColors as $color)
                                <button
                                    type="button"
                                    @click="activeColorId = {{ $color->id }}; $dispatch('color-picked', { colorId: {{ $color->id }} })"
                                    class="w-8 h-8 rounded-full border transition-all shrink-0"
                                    :class="activeColorId === {{ $color->id }} ? 'ring-2 ring-primary ring-offset-2 ring-offset-background border-transparent' : 'border-outline-variant hover:ring-2 hover:ring-outline hover:ring-offset-2 hover:ring-offset-background'"
                                    style="background-color: {{ $color->hex }}"
                                    aria-label="{{ $color->name }}"
                                ></button>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="mb-6">
                    <h1 class="font-headline-md text-headline-md md:font-headline-lg md:text-headline-lg text-on-surface {{ $product->brand ? 'mb-1' : 'mb-3' }}">{{ $product->name }}</h1>

                    @if($product->brand)
                        <p class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider mb-3">{{ $product->brand }}</p>
                    @endif

                    @if($avgRating)
                        <a href="#avis" class="flex items-center gap-2 mb-4 w-fit">
                            <span class="flex gap-0.5">
                                @for($i = 1; $i <= 5; $i++)
                                    <x-icon name="star" class="text-[18px] {{ $i <= round($avgRating) ? 'text-primary' : 'text-outline-variant' }}" :filled="$i <= round($avgRating)" />
                                @endfor
                            </span>
                            <span class="font-label-lg text-label-lg text-on-surface">{{ $avgRating }}</span>
                            <span class="font-label-sm text-label-sm text-on-surface-variant underline underline-offset-2">{{ $reviewCount }} avis</span>
                        </a>
                    @endif

                    <div class="flex items-center gap-3">
                        <span class="font-body-lg text-body-lg font-bold text-on-surface">{{ number_format($product->effective_price, 0, ',', ' ') }} FCFA</span>
                        @if($product->discount_percent)
                            <span class="font-body-md text-body-md text-outline line-through">{{ number_format($product->base_price, 0, ',', ' ') }} FCFA</span>
                        @endif
                    </div>
                </div>
                <hr class="border-outline-variant mb-6" />

                <livewire:product-variant-selector :product="$product" :initial-color-id="$initialColorId" />

                <div class="grid grid-cols-3 gap-2 mb-8 -mt-4">
                    <div class="flex flex-col items-center text-center gap-1.5 p-3 rounded-lg bg-surface-container-low">
                        <x-icon name="local_shipping" class="text-primary text-[22px]" />
                        <span class="font-label-sm text-[10px] text-on-surface-variant leading-tight">Livraison à Libreville</span>
                    </div>
                    <div class="flex flex-col items-center text-center gap-1.5 p-3 rounded-lg bg-surface-container-low">
                        <x-icon name="verified_user" class="text-primary text-[22px]" />
                        <span class="font-label-sm text-[10px] text-on-surface-variant leading-tight">Paiement sécurisé</span>
                    </div>
                    <div class="flex flex-col items-center text-center gap-1.5 p-3 rounded-lg bg-surface-container-low">
                        <x-icon name="replay" class="text-primary text-[22px]" />
                        <span class="font-label-sm text-[10px] text-on-surface-variant leading-tight">Retour sous 14 jours</span>
                    </div>
                </div>

                <div class="border-t border-outline-variant">
                    @if($product->description)
                        <details class="group py-4 border-b border-outline-variant cursor-pointer" open>
                            <summary class="flex justify-between items-center font-headline-md text-headline-md text-on-surface list-none">
                                Description
                                <x-icon name="expand_more" class="group-open:rotate-180 transition-transform" />
                            </summary>
                            <div class="pt-4 font-body-md text-body-md text-on-surface-variant leading-relaxed">
                                {{ $product->description }}
                            </div>
                        </details>
                    @endif

                    @if($product->material || $product->fit || $product->season || $product->care_instructions)
                        <details class="group py-4 border-b border-outline-variant cursor-pointer">
                            <summary class="flex justify-between items-center font-headline-md text-headline-md text-on-surface list-none">
                                Caractéristiques
                                <x-icon name="expand_more" class="group-open:rotate-180 transition-transform" />
                            </summary>
                            <dl class="pt-4 grid grid-cols-2 gap-y-3 font-body-md text-body-md">
                                @if($product->material)
                                    <dt class="text-on-surface-variant">Matière</dt>
                                    <dd class="text-on-surface">{{ $product->material }}</dd>
                                @endif
                                @if($product->fit)
                                    <dt class="text-on-surface-variant">Coupe</dt>
                                    <dd class="text-on-surface">{{ $product->fit }}</dd>
                                @endif
                                @if($product->season)
                                    <dt class="text-on-surface-variant">Saison</dt>
                                    <dd class="text-on-surface">{{ $product->season }}</dd>
                                @endif
                                @if($product->brand)
                                    <dt class="text-on-surface-variant">Marque</dt>
                                    <dd class="text-on-surface">{{ $product->brand }}</dd>
                                @endif
                                @if($product->care_instructions)
                                    <dt class="text-on-surface-variant col-span-2">Entretien</dt>
                                    <dd class="text-on-surface col-span-2">{{ $product->care_instructions }}</dd>
                                @endif
                            </dl>
                        </details>
                    @endif

                    <details class="group py-4 border-b border-outline-variant cursor-pointer">
                        <summary class="flex justify-between items-center font-headline-md text-headline-md text-on-surface list-none">
                            Livraison et Retours
                            <x-icon name="expand_more" class="group-open:rotate-180 transition-transform" />
                        </summary>
                        <div class="pt-4 font-body-md text-body-md text-on-surface-variant leading-relaxed">
                            Livraison à domicile, en point relais ou retrait en boutique à Libreville. Retours acceptés sous 14 jours.
                        </div>
                    </details>
                </div>
            </section>
        </div>

        {{-- Reviews --}}
        @if($reviewCount > 0)
        <section id="avis" class="mt-16 pt-12 border-t border-outline-variant scroll-mt-24">
            <h3 class="font-headline-md text-headline-md text-on-surface mb-8">Avis clients</h3>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-8 md:gap-16 mb-10">
                    <div class="md:col-span-4 flex flex-col items-start gap-2">
                        <span class="font-display-lg-mobile text-display-lg-mobile text-on-surface">{{ $avgRating }}</span>
                        <span class="flex gap-0.5">
                            @for($i = 1; $i <= 5; $i++)
                                <x-icon name="star" class="text-[20px] {{ $i <= round($avgRating) ? 'text-primary' : 'text-outline-variant' }}" :filled="$i <= round($avgRating)" />
                            @endfor
                        </span>
                        <span class="font-body-md text-body-md text-on-surface-variant">Basé sur {{ $reviewCount }} avis</span>
                    </div>
                    <div class="md:col-span-8 flex flex-col gap-2 justify-center">
                        @foreach($ratingBreakdown as $star => $percent)
                            <div class="flex items-center gap-3">
                                <span class="font-label-sm text-label-sm text-on-surface-variant w-10 shrink-0">{{ $star }} étoiles</span>
                                <div class="flex-1 h-2 rounded-full bg-surface-container-highest overflow-hidden">
                                    <div class="h-full bg-primary rounded-full" style="width: {{ $percent }}%"></div>
                                </div>
                                <span class="font-label-sm text-label-sm text-on-surface-variant w-10 shrink-0 text-right">{{ $percent }}%</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div x-data="{ showAll: false }" class="flex flex-col gap-4">
                    @foreach($product->reviews as $i => $review)
                        <div class="p-5 md:p-6 rounded-xl bg-surface-container-lowest border border-surface-variant" @if($i >= 4) x-show="showAll" x-cloak @endif>
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex gap-0.5">
                                    @for($i2 = 1; $i2 <= 5; $i2++)
                                        <x-icon name="star" class="text-[16px] {{ $i2 <= $review->rating ? 'text-primary' : 'text-outline-variant' }}" :filled="$i2 <= $review->rating" />
                                    @endfor
                                </div>
                                <span class="font-label-sm text-[11px] text-on-surface-variant">{{ $review->created_at->format('d/m/Y') }}</span>
                            </div>
                            <p class="font-body-md text-body-md text-on-surface leading-relaxed">{{ $review->comment }}</p>
                            @if($review->photo_path)
                                <img src="{{ Illuminate\Support\Facades\Storage::disk('public')->url($review->photo_path) }}" alt="Photo avis" class="mt-3 w-20 h-20 object-cover rounded-lg" />
                            @endif
                            <p class="font-label-sm text-label-sm text-on-surface-variant mt-3">
                                {{ \Illuminate\Support\Str::before($review->user?->name ?? 'Client AkibaShop', ' ') }}
                                {{ \Illuminate\Support\Str::substr(\Illuminate\Support\Str::after($review->user?->name ?? '', ' '), 0, 1) }}.
                            </p>
                        </div>
                    @endforeach

                    @if($reviewCount > 4)
                        <button type="button" @click="showAll = !showAll" class="self-center mt-2 px-8 py-3 rounded-full border border-outline-variant text-on-surface font-label-lg text-label-lg uppercase hover:border-primary hover:text-primary transition-colors">
                            <span x-show="!showAll">Voir tous les avis</span>
                            <span x-show="showAll" x-cloak>Réduire</span>
                        </button>
                    @endif
                </div>
        </section>
        @endif

        @if($related->isNotEmpty())
            <section class="mt-16 mb-12">
                <h3 class="font-headline-md text-headline-md text-on-surface mb-8 text-center md:text-left">Vous pourriez aussi aimer</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
                    @foreach($related as $item)
                        <x-product-card :product="$item" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-layouts.app>
