<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Connexion Admin — AkibaShop</title>
    <meta name="robots" content="noindex" />
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any" />
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}" />
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-inverse-surface text-inverse-on-surface font-body-md antialiased min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-md bg-surface-container-lowest text-on-surface rounded-xl shadow-2xl p-8 md:p-10">
        <div class="flex flex-col items-center mb-8">
            <img src="{{ asset('logo/logo1.PNG') }}" alt="AkibaShop" class="h-12 w-auto object-contain rounded mb-4" />
            <h1 class="font-headline-lg text-headline-lg text-primary">Espace administrateur</h1>
            <p class="font-body-md text-body-md text-on-surface-variant mt-2 text-center">Accès réservé à l'équipe AkibaShop.</p>
        </div>

        <form method="POST" action="{{ route('admin.login.store') }}" class="flex flex-col gap-6">
            @csrf

            <div class="relative-input-group">
                <input type="email" name="email" id="email" class="input-underline font-body-md text-body-md text-on-surface h-10" placeholder=" " value="{{ old('email') }}" required autofocus />
                <label for="email" class="font-body-md">Adresse email</label>
            </div>
            @error('email')
                <p class="-mt-4 text-error font-label-sm text-label-sm">{{ $message }}</p>
            @enderror

            <div class="relative-input-group">
                <input type="password" name="password" id="password" class="input-underline font-body-md text-body-md text-on-surface h-10" placeholder=" " required />
                <label for="password" class="font-body-md">Mot de passe</label>
            </div>

            <button type="submit" class="w-full bg-primary text-on-primary h-12 rounded-full font-label-lg text-label-lg uppercase tracking-widest hover:bg-primary-container transition-colors">
                Accéder au dashboard
            </button>
        </form>
    </div>
</body>
</html>
