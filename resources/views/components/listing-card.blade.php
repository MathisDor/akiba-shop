@props(['listing'])

@php
    $cover = $listing->coverImage();
    $photoCount = $listing->images->count();
    $isFavorite = auth()->check() && $listing->favoritedBy->contains(auth()->id());
    $dataSaver = request()->cookie('eco') === '1';
@endphp

<article class="group relative flex flex-col h-full card overflow-hidden hover:border-outline-variant hover:shadow-[0_8px_24px_-12px_rgba(20,38,27,0.25)] transition-shadow">
    <div class="relative aspect-[4/3] bg-surface-container-low overflow-hidden">
        @if($cover && ! $dataSaver)
            <img
                src="{{ $cover->url() }}"
                alt=""
                loading="lazy"
                decoding="async"
                class="absolute inset-0 w-full h-full object-cover skeleton"
            />
        @else
            <div class="absolute inset-0 flex flex-col items-center justify-center gap-1 text-outline">
                <x-icon :name="$cover ? 'data_saver' : 'image'" class="text-[28px]" />
                @if($cover)
                    <span class="text-[11px] font-medium">Photo masquée</span>
                @endif
            </div>
        @endif

        @if($listing->isBoosted())
            <span class="absolute top-2 left-2 inline-flex items-center gap-1 rounded-md bg-tertiary text-on-tertiary px-2 py-1 text-[11px] font-bold">
                <x-icon name="boost" class="text-[12px]" :filled="true" /> À la une
            </span>
        @endif

        @if($photoCount > 1)
            <span class="absolute bottom-2 left-2 inline-flex items-center gap-1 rounded-md bg-black/60 text-white px-1.5 py-0.5 text-[11px] font-semibold">
                <x-icon name="camera" class="text-[12px]" /> {{ $photoCount }}
            </span>
        @endif
    </div>

    <div class="flex flex-col gap-1 p-3 flex-1">
        <div class="flex items-start justify-between gap-2">
            <p class="text-[16px] leading-6 font-extrabold text-on-surface">{{ $listing->formattedPrice() }}</p>
            @if($listing->user?->is_pro)
                <span class="mt-0.5 shrink-0 rounded bg-secondary-container text-on-secondary-container px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wide">Pro</span>
            @endif
        </div>
        <h3 class="text-[14px] leading-5 text-on-surface line-clamp-2">
            {{-- Stretched link: the whole card is clickable, the favourite button sits above it --}}
            <a href="{{ route('listings.show', $listing) }}" class="after:absolute after:inset-0 focus:outline-none focus-visible:after:ring-2 focus-visible:after:ring-primary focus-visible:after:rounded-2xl">{{ $listing->title }}</a>
        </h3>
        <div class="mt-auto pt-2 flex items-center gap-1.5 text-[12px] leading-4 text-on-surface-variant min-w-0">
            <x-icon name="location_on" class="text-[13px] shrink-0" />
            <span class="truncate">{{ $listing->location() }}</span>
            <span aria-hidden="true">·</span>
            <time datetime="{{ $listing->published_at?->toIso8601String() }}" class="shrink-0">{{ $listing->published_at?->diffForHumans(short: true) }}</time>
        </div>
    </div>

    <div class="absolute top-2 right-2 z-10">
        <x-favorite-button :listing="$listing" :active="$isFavorite" size="sm" />
    </div>
</article>
