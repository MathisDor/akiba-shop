@props(['active' => null, 'title' => null, 'description' => null, 'ogImage' => null, 'hideFooter' => false])
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title ?? 'AkibaShop — La mode qui vous ressemble' }}</title>
    <meta name="description" content="{{ $description ?? "AkibaShop, la boutique en ligne de mode pour hommes et femmes au Gabon. Nouveautés, promotions et livraison à Libreville." }}" />
    <meta property="og:title" content="{{ $title ?? 'AkibaShop' }}" />
    <meta property="og:description" content="{{ $description ?? 'La mode qui vous ressemble — AkibaShop' }}" />
    <meta property="og:type" content="website" />
    @if($ogImage)
        <meta property="og:image" content="{{ $ogImage }}" />
    @endif
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any" />
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}" />
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}" />
    @stack('head')

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,opsz,wght@0,6..96,400..900;1,6..96,400..900&family=Manrope:wght@200..800&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="antialiased bg-background text-on-background pb-24 md:pb-0">
    @include('layouts.partials.header', ['active' => $active])

    <main class="pt-[72px] md:pt-[88px]">
        {{ $slot }}
    </main>

    @unless($hideFooter)
        @include('layouts.partials.footer')
    @endunless

    @include('layouts.partials.bottom-nav', ['active' => $active])

    @livewireScripts
    @stack('scripts')
</body>
</html>
