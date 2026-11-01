<x-layouts.app active="listings" title="Toutes les catégories — AkibaShop" description="Toutes les catégories de petites annonces : véhicules, immobilier, téléphones, maison, mode, emploi, animaux et plus.">
    <div class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop pt-4 md:pt-8">
        <h1 class="text-headline-md md:text-headline-lg text-on-surface">Catégories</h1>
        <p class="text-[14px] text-on-surface-variant mt-1 mb-4 md:mb-6">Choisissez une catégorie, puis affinez par sous-catégorie.</p>

        {{-- Mobile: one tappable row per category, sub-categories unfold in place --}}
        <ul class="md:hidden card divide-y divide-surface-variant overflow-hidden" x-data="{ open: null }">
            @foreach($categories as $category)
                @php($total = $counts->only([$category->id, ...$category->children->pluck('id')])->sum())
                <li>
                    @if($category->children->isEmpty())
                        <a href="{{ route('categories.show', $category) }}" class="flex items-center gap-3 px-4 min-h-[64px] active:bg-surface-container-low">
                            <span class="w-10 h-10 shrink-0 rounded-xl bg-primary-fixed text-primary flex items-center justify-center"><x-icon :name="$category->icon ?? 'package'" class="text-[20px]" /></span>
                            <span class="flex-1 min-w-0">
                                <span class="block text-[15px] font-bold text-on-surface">{{ $category->name }}</span>
                                <span class="block text-[12px] text-on-surface-variant">{{ number_format($total, 0, ',', ' ') }} annonce{{ $total > 1 ? 's' : '' }}</span>
                            </span>
                            <x-icon name="chevron_right" class="text-[18px] text-outline" />
                        </a>
                    @else
                        <button
                            type="button"
                            @click="open = open === {{ $category->id }} ? null : {{ $category->id }}"
                            :aria-expanded="(open === {{ $category->id }}).toString()"
                            aria-controls="cat-{{ $category->id }}"
                            class="w-full flex items-center gap-3 px-4 min-h-[64px] text-left active:bg-surface-container-low"
                        >
                            <span class="w-10 h-10 shrink-0 rounded-xl bg-primary-fixed text-primary flex items-center justify-center"><x-icon :name="$category->icon ?? 'package'" class="text-[20px]" /></span>
                            <span class="flex-1 min-w-0">
                                <span class="block text-[15px] font-bold text-on-surface">{{ $category->name }}</span>
                                <span class="block text-[12px] text-on-surface-variant">{{ number_format($total, 0, ',', ' ') }} annonce{{ $total > 1 ? 's' : '' }} · {{ $category->children->count() }} sous-catégories</span>
                            </span>
                            <x-icon name="expand_more" class="text-[20px] text-outline transition-transform" x-bind:class="open === {{ $category->id }} && 'rotate-180'" />
                        </button>
                        <div id="cat-{{ $category->id }}" x-show="open === {{ $category->id }}" x-collapse x-cloak class="bg-surface-container-low">
                            <a href="{{ route('categories.show', $category) }}" class="flex items-center justify-between gap-3 pl-[68px] pr-4 min-h-[48px] text-[14px] font-bold text-primary border-t border-surface-variant">
                                Tout « {{ $category->name }} »
                                <x-icon name="arrow_forward" class="text-[16px]" />
                            </a>
                            @foreach($category->children as $child)
                                <a href="{{ route('categories.show', $child) }}" class="flex items-center justify-between gap-3 pl-[68px] pr-4 min-h-[48px] text-[14px] text-on-surface border-t border-surface-variant active:bg-surface-container">
                                    <span class="truncate">{{ $child->name }}</span>
                                    <span class="shrink-0 text-[12px] text-on-surface-variant">{{ $counts[$child->id] ?? 0 }}</span>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </li>
            @endforeach
        </ul>

        {{-- Tablet & desktop: cards with every sub-category visible --}}
        <div class="hidden md:grid md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($categories as $category)
                @php($total = $counts->only([$category->id, ...$category->children->pluck('id')])->sum())
                <section class="card p-5 flex flex-col gap-3">
                    <a href="{{ route('categories.show', $category) }}" class="flex items-center gap-3 group">
                        <span class="w-12 h-12 rounded-2xl bg-primary-fixed text-primary flex items-center justify-center group-hover:bg-primary group-hover:text-on-primary transition-colors">
                            <x-icon :name="$category->icon ?? 'package'" class="text-[24px]" />
                        </span>
                        <span>
                            <span class="block text-[17px] font-extrabold text-on-surface group-hover:text-primary">{{ $category->name }}</span>
                            <span class="block text-[13px] text-on-surface-variant">{{ number_format($total, 0, ',', ' ') }} annonce{{ $total > 1 ? 's' : '' }}</span>
                        </span>
                    </a>
                    @if($category->children->isNotEmpty())
                        <ul class="flex flex-wrap gap-2">
                            @foreach($category->children as $child)
                                <li>
                                    <a href="{{ route('categories.show', $child) }}" class="inline-flex items-center gap-1 rounded-full bg-surface-container-low px-3 py-1.5 text-[13px] font-medium text-on-surface hover:bg-primary-fixed hover:text-on-primary-fixed-variant transition-colors">
                                        {{ $child->name }}
                                        @if($counts[$child->id] ?? 0)
                                            <span class="text-on-surface-variant">({{ $counts[$child->id] }})</span>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </section>
            @endforeach
        </div>
    </div>
</x-layouts.app>
