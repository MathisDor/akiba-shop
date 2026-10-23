<x-layouts.app active="listings" title="Toutes les catégories — AkibaShop" description="Toutes les catégories de petites annonces : véhicules, immobilier, téléphones, maison, mode, emploi, animaux et plus.">
    <div class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop pt-6">
        <h1 class="text-headline-lg text-on-surface mb-6">Toutes les catégories</h1>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
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
