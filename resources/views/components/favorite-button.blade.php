@props(['listing', 'active' => false, 'size' => 'md'])

@php($dimensions = $size === 'sm' ? 'w-9 h-9' : 'w-11 h-11')

@auth
    <button
        type="button"
        x-data="{ active: @js((bool) $active), busy: false }"
        @click.prevent="
            if (busy) return;
            busy = true;
            active = !active;
            fetch(@js(route('listings.favorite', $listing)), {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
            })
                .then(r => r.ok ? r.json() : Promise.reject())
                .then(data => active = data.favorited)
                .catch(() => active = !active)
                .finally(() => busy = false)
        "
        :aria-pressed="active.toString()"
        aria-label="Ajouter aux favoris"
        {{ $attributes->merge(['class' => $dimensions.' flex items-center justify-center rounded-full bg-white/90 backdrop-blur shadow-sm text-on-surface hover:text-error transition-colors']) }}
    >
        <svg class="icon text-[18px]" :class="active ? 'text-error' : ''" viewBox="0 0 24 24" :fill="active ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 9.5a5.5 5.5 0 0 1 9.591-3.676.56.56 0 0 0 .818 0A5.49 5.49 0 0 1 22 9.5c0 2.29-1.5 4-3 5.5l-5.492 5.313a2 2 0 0 1-3 .019L5 15c-1.5-1.5-3-3.2-3-5.5" /></svg>
    </button>
@else
    <a
        href="{{ route('login') }}"
        aria-label="Connectez-vous pour ajouter aux favoris"
        {{ $attributes->merge(['class' => $dimensions.' flex items-center justify-center rounded-full bg-white/90 backdrop-blur shadow-sm text-on-surface hover:text-error transition-colors']) }}
    >
        <x-icon name="favorite_border" class="text-[18px]" />
    </a>
@endauth
