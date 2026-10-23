@props(['active' => null, 'title' => null, 'description' => null, 'ogImage' => null, 'hideFooter' => false, 'canonical' => null])
@php
    $pageTitle = $title ?? 'AkibaShop — Petites annonces gratuites';
    $pageDescription = $description ?? 'AkibaShop, les petites annonces gratuites : véhicules, immobilier, téléphones, maison, mode, emploi… Achetez et vendez près de chez vous, entre particuliers et professionnels.';
@endphp
<!DOCTYPE html>
<html lang="fr" class="{{ request()->cookie('eco') === '1' ? 'eco' : '' }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}" />
    <meta name="theme-color" content="#007a2f" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="canonical" href="{{ $canonical ?? url()->current() }}" />
    <meta property="og:site_name" content="AkibaShop" />
    <meta property="og:locale" content="fr_FR" />
    <meta property="og:title" content="{{ $pageTitle }}" />
    <meta property="og:description" content="{{ $pageDescription }}" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ url()->current() }}" />
    @if($ogImage)
        <meta property="og:image" content="{{ $ogImage }}" />
    @endif
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml" />
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="32x32" />
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}" />
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}" />
    @stack('head')

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400..800&display=swap" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="antialiased min-h-screen flex flex-col bg-background text-on-background">
    @include('layouts.partials.header', ['active' => $active])

    <main class="flex-1 pb-24 md:pb-0">
        @session('status')
            <div class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop pt-4">
                <div role="status" class="flex items-start gap-3 rounded-xl bg-primary-fixed text-on-primary-fixed px-4 py-3 text-[14px] leading-5">
                    <x-icon name="check_circle" class="text-[20px] text-primary" />
                    <p>{{ $value }}</p>
                </div>
            </div>
        @endsession

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
