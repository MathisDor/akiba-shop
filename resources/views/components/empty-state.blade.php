@props(['icon' => 'search_off', 'title', 'text' => null])

<div {{ $attributes->merge(['class' => 'card flex flex-col items-center text-center gap-3 px-6 py-12']) }}>
    <span class="w-14 h-14 rounded-2xl bg-primary-fixed text-primary flex items-center justify-center">
        <x-icon :name="$icon" class="text-[28px]" />
    </span>
    <h2 class="text-headline-md text-on-surface">{{ $title }}</h2>
    @if($text)
        <p class="text-body-md text-on-surface-variant max-w-md">{{ $text }}</p>
    @endif
    @if($slot->isNotEmpty())
        <div class="mt-2 flex flex-wrap justify-center gap-2">{{ $slot }}</div>
    @endif
</div>
