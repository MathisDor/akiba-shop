@props(['markOnly' => false, 'inverse' => false])

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-2 select-none']) }}>
    {{-- Mark: a price tag on the national green, the tag's eyelet in yellow --}}
    <svg viewBox="0 0 40 40" class="h-9 w-9 shrink-0" aria-hidden="true">
        <rect width="40" height="40" rx="11" fill="#009639" />
        <path d="M20.586 10.586A2 2 0 0 0 19.172 10H12a2 2 0 0 0-2 2v7.172a2 2 0 0 0 .586 1.414l8.704 8.704a2.426 2.426 0 0 0 3.42 0l6.58-6.58a2.426 2.426 0 0 0 0-3.42z" fill="#ffffff" />
        <circle cx="15.5" cy="15.5" r="2.4" fill="#fcd116" />
        <path d="M17 31.5h11" stroke="#3a75c4" stroke-width="2.5" stroke-linecap="round" />
    </svg>
    @unless($markOnly)
        <span class="text-[21px] leading-none font-extrabold tracking-tight {{ $inverse ? 'text-inverse-on-surface' : 'text-on-surface' }}">akiba<span class="{{ $inverse ? 'text-inverse-primary' : 'text-primary' }}">shop</span></span>
    @endunless
</span>
