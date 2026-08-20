<x-layouts.app active="favorites" title="Mes Favoris — AkibaShop">
    <div class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop py-8">
        <h1 class="font-headline-xl text-headline-xl text-primary mb-8">Mes Favoris</h1>

        @if($products->isEmpty())
            <div class="flex flex-col items-center justify-center text-center py-20">
                <div class="w-24 h-24 rounded-full bg-surface-container-high flex items-center justify-center mb-6">
                    <x-icon name="favorite_border" class="text-4xl text-on-surface-variant" />
                </div>
                <h2 class="font-headline-md text-headline-md text-primary mb-2">Aucun favori pour le moment</h2>
                <p class="font-body-md text-body-md text-on-surface-variant mb-8 max-w-sm">Ajoutez des articles à vos favoris pour les retrouver facilement.</p>
                <a href="{{ route('catalog.index') }}" class="px-8 h-12 border border-primary text-primary font-label-sm text-label-sm rounded-full uppercase tracking-wider hover:bg-primary hover:text-on-primary transition-colors flex items-center">Découvrir la collection</a>
            </div>
        @else
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-4 gap-y-10 md:gap-x-6 md:gap-y-16">
                @foreach($products as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        @endif

        @if($recommended->isNotEmpty())
            <section class="mt-16 pt-12 border-t border-outline-variant">
                <h2 class="font-headline-md text-headline-md text-on-surface mb-8">Vous pourriez aussi aimer</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
                    @foreach($recommended as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-layouts.app>
