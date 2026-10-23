<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Connexion Admin — AkibaShop</title>
    <meta name="robots" content="noindex" />
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml" />
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="32x32" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400..800&display=swap" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-inverse-surface text-inverse-on-surface antialiased min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-md card text-on-surface p-8 md:p-10">
        <div class="flex flex-col items-center mb-8">
            <x-logo class="mb-4" />
            <h1 class="text-headline-md text-on-surface">Espace administrateur</h1>
            <p class="text-body-md text-on-surface-variant mt-1 text-center">Accès réservé à l'équipe AkibaShop.</p>
        </div>

        <form method="POST" action="{{ route('admin.login.store') }}" class="flex flex-col gap-4">
            @csrf

            <div>
                <label for="email" class="field-label">Adresse email</label>
                <input type="email" name="email" id="email" class="field" value="{{ old('email') }}" required autofocus />
                @error('email') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password" class="field-label">Mot de passe</label>
                <input type="password" name="password" id="password" class="field" required />
            </div>

            <button type="submit" class="btn btn-primary w-full h-12">Accéder au back-office</button>
        </form>
    </div>
</body>
</html>
