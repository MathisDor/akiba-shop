@props(['title' => null])
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title ?? 'AkibaShop' }}</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any" />
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}" />
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,opsz,wght@0,6..96,400..900;1,6..96,400..900&family=Manrope:wght@200..800&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-surface text-on-surface font-body-md antialiased min-h-screen flex flex-col">
    <header class="w-full flex justify-center items-center py-8">
        <a href="{{ route('home') }}">
            <img src="{{ asset('logo/logo1.PNG') }}" alt="AkibaShop" class="h-12 w-auto object-contain rounded" />
        </a>
    </header>

    <main class="flex-grow flex items-start justify-center px-margin-mobile pb-16">
        <div class="w-full max-w-md bg-surface-container-lowest rounded-xl shadow-[0_20px_60px_-15px_rgba(0,0,0,0.08)] p-8 md:p-10">
            {{ $slot }}
        </div>
    </main>

    <footer class="py-6 flex flex-col items-center gap-2 text-center">
        <p class="font-label-sm text-[11px] text-outline uppercase tracking-widest">© {{ date('Y') }} AkibaShop Libreville. Tous droits réservés.</p>
        <p class="font-label-sm text-[11px] text-outline">
            Développé par
            <a href="https://dibundiweb.netlify.app/" target="_blank" rel="noopener noreferrer" class="text-primary hover:underline font-semibold">DibundiWeb</a>
        </p>
    </footer>
</body>
</html>
