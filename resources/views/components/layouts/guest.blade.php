@props(['title' => null])
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title ?? 'AkibaShop' }}</title>
    <meta name="theme-color" content="#007a2f" />
    <meta name="robots" content="noindex" />
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml" />
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="32x32" />
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400..800&display=swap" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background text-on-surface antialiased min-h-screen flex flex-col">
    <div class="flag-band"></div>
    <header class="w-full flex justify-center items-center py-6 md:py-8">
        <a href="{{ route('home') }}" aria-label="AkibaShop — accueil"><x-logo /></a>
    </header>

    <main class="flex-grow flex items-start justify-center px-margin-mobile pb-12">
        <div class="w-full max-w-md card p-6 md:p-8">
            {{ $slot }}
        </div>
    </main>

    <footer class="py-6 text-center text-[12px] text-on-surface-variant">
        © {{ date('Y') }} AkibaShop ·
        <a href="{{ route('pages.terms') }}" class="hover:text-primary">Conditions d’utilisation</a> ·
        <a href="{{ route('pages.privacy') }}" class="hover:text-primary">Confidentialité</a>
    </footer>
</body>
</html>
