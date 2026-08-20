@props(['title' => null, 'step' => 'informations', 'backUrl' => null])
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title ?? 'AkibaShop — Checkout' }}</title>
    <meta name="robots" content="noindex" />
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any" />
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}" />
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,opsz,wght@0,6..96,400..900;1,6..96,400..900&family=Manrope:wght@200..800&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-background text-on-background font-body-md antialiased min-h-screen flex flex-col">
    <header class="w-full flex justify-between items-center px-margin-mobile md:px-margin-desktop py-4 bg-surface z-50 sticky top-0 border-b border-surface-variant/60">
        <a href="{{ $backUrl ?? route('cart.index') }}" aria-label="Retour" class="text-primary hover:opacity-70 transition-opacity flex items-center justify-center w-10 h-10 -ml-2">
            <x-icon name="arrow_back_ios_new" :stroke-width="1.5" />
        </a>
        <a href="{{ route('home') }}" class="h-8 md:h-10">
            <img src="{{ asset('logo/logo1.PNG') }}" alt="AkibaShop" class="h-full w-auto object-contain rounded" />
        </a>
        <div class="w-10 h-10 flex items-center justify-center text-outline">
            <x-icon name="lock" :stroke-width="1.5" />
        </div>
    </header>

    <nav class="flex items-center justify-center w-full py-6">
        <div class="flex items-center space-x-2 md:space-x-4">
            @foreach(['informations' => 'Infos', 'livraison' => 'Livraison', 'paiement' => 'Paiement'] as $key => $label)
                <div class="flex items-center {{ $step === $key ? 'text-primary' : 'text-outline opacity-50' }}">
                    <span class="font-label-sm text-label-sm uppercase tracking-widest">{{ $label }}</span>
                </div>
                @if(!$loop->last)
                    <div class="w-8 h-px bg-outline-variant"></div>
                @endif
            @endforeach
        </div>
    </nav>

    <main class="flex-grow flex flex-col w-full max-w-2xl mx-auto px-margin-mobile md:px-0 pb-32">
        {{ $slot }}
    </main>

    <footer class="border-t border-surface-variant py-6 mt-section-gap flex flex-col items-center gap-2 text-center px-margin-mobile">
        <p class="font-label-sm text-[11px] text-outline uppercase tracking-widest">© {{ date('Y') }} AkibaShop Libreville. Tous droits réservés.</p>
        <p class="font-label-sm text-[11px] text-outline">
            Développé par
            <a href="https://dibundiweb.netlify.app/" target="_blank" rel="noopener noreferrer" class="text-primary hover:underline font-semibold">DibundiWeb</a>
        </p>
    </footer>

    @livewireScripts
</body>
</html>
