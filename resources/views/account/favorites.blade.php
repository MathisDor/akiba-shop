<x-layouts.app active="favorites" title="Mes favoris — AkibaShop">
    <div class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop pt-6">
        <h1 class="text-headline-lg text-on-surface mb-4">Mes favoris</h1>

        @include('account.partials.nav', ['current' => 'favorites'])

        @if($listings->isEmpty())
            <x-empty-state icon="favorite" title="Aucun favori pour l’instant" text="Touchez le cœur sur une annonce pour la retrouver ici.">
                <a href="{{ route('listings.index') }}" class="btn btn-primary">Parcourir les annonces</a>
            </x-empty-state>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 md:gap-4">
                @foreach($listings as $listing)
                    <div class="relative {{ $listing->isVisible() ? '' : 'opacity-60' }}">
                        <x-listing-card :listing="$listing" />
                        @unless($listing->isVisible())
                            <span class="absolute top-2 left-2 z-10 rounded-md bg-inverse-surface text-inverse-on-surface px-2 py-1 text-[11px] font-bold">Plus disponible</span>
                        @endunless
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts.app>
