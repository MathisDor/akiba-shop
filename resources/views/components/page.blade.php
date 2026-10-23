@props(['title', 'description' => null, 'intro' => null])

<x-layouts.app :title="$title.' — AkibaShop'" :description="$description">
    <article class="max-w-3xl mx-auto px-margin-mobile md:px-margin-desktop pt-6 md:pt-10">
        <h1 class="text-headline-lg md:text-headline-xl text-on-surface">{{ $title }}</h1>
        @if($intro)
            <p class="mt-3 text-body-lg text-on-surface-variant">{{ $intro }}</p>
        @endif
        <div class="mt-6 card p-5 md:p-8 flex flex-col gap-4 text-body-md text-on-surface [&_h2]:text-[18px] [&_h2]:font-extrabold [&_h2]:mt-3 [&_ul]:list-disc [&_ul]:pl-5 [&_ul]:flex [&_ul]:flex-col [&_ul]:gap-1.5 [&_a]:text-secondary [&_a]:font-semibold [&_a]:underline">
            {{ $slot }}
        </div>
    </article>
</x-layouts.app>
