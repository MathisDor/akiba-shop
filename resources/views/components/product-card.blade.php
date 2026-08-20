@props(['product'])

@php
    $primaryImage = $product->images->firstWhere('is_primary', true) ?? $product->images->first();
    $isNew = $product->published_at && $product->published_at->gt(now()->subDays(21));
    $hasPromo = $product->discount_percent !== null;
    $maxColorsShown = 4;
    $extraColorsCount = max(0, $product->colors->count() - $maxColorsShown);
@endphp

<div class="group cursor-pointer">
    <a href="{{ route('products.show', $product) }}" class="block">
        <div class="relative aspect-[3/4] mb-3 overflow-hidden rounded-xl bg-surface-container-low">
            @if($primaryImage)
                <img
                    src="{{ Illuminate\Support\Facades\Storage::disk('public')->url($primaryImage->path) }}"
                    alt="{{ $product->name }}"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out"
                    loading="lazy"
                />
            @endif

            <div class="absolute top-3 left-3 flex flex-col gap-1">
                @if($isNew)
                    <span class="bg-primary text-on-primary px-3 py-1 rounded-sm font-label-sm text-[10px] uppercase tracking-wider">Nouveau</span>
                @endif
                @if($hasPromo)
                    <span class="bg-error text-on-error px-3 py-1 rounded-sm font-label-sm text-[10px] uppercase tracking-wider">-{{ $product->discount_percent }}%</span>
                @endif
            </div>

            <button
                type="button"
                @auth
                    onclick="event.preventDefault(); document.getElementById('fav-form-{{ $product->id }}').requestSubmit();"
                @else
                    onclick="event.preventDefault(); window.location.href='{{ route('login') }}';"
                @endauth
                class="absolute top-3 right-3 w-9 h-9 flex items-center justify-center rounded-full bg-white/80 backdrop-blur-sm text-on-surface hover:text-error hover:bg-white transition-colors"
                aria-label="Ajouter aux favoris"
            >
                <x-icon name="favorite_border" class="{{ auth()->check() && $product->favoritedBy->contains(auth()->id()) ? 'text-error' : '' }}" :filled="auth()->check() && $product->favoritedBy->contains(auth()->id())" />
            </button>
        </div>
    </a>

    @auth
        <form id="fav-form-{{ $product->id }}" method="POST" action="{{ route('account.favorites.toggle', $product) }}" class="hidden">
            @csrf
        </form>
    @endauth

    <a href="{{ route('products.show', $product) }}" class="flex flex-col gap-1 min-w-0">
        <h3 class="font-body-md text-body-md text-on-surface truncate">{{ $product->name }}</h3>

        <div class="flex items-center gap-2 mt-1">
            <p class="font-label-sm text-label-sm font-bold text-primary">{{ number_format($product->effective_price, 0, ',', ' ') }} FCFA</p>
            @if($hasPromo)
                <p class="font-label-sm text-[11px] text-outline line-through">{{ number_format($product->base_price, 0, ',', ' ') }} FCFA</p>
            @endif
        </div>

        @if($product->colors->isNotEmpty())
            <div class="flex items-center gap-1 mt-1">
                @foreach($product->colors->take($maxColorsShown) as $color)
                    <span class="w-3.5 h-3.5 rounded-full border border-outline-variant shrink-0" style="background-color: {{ $color->hex }}"></span>
                @endforeach
                @if($extraColorsCount > 0)
                    <span class="font-label-sm text-[10px] text-on-surface-variant ml-0.5">+{{ $extraColorsCount }}</span>
                @endif
            </div>
        @endif
    </a>
</div>
