<x-layouts.guest title="Connexion — AkibaShop">
    <h1 class="text-headline-lg text-on-surface text-center">Bon retour !</h1>
    <p class="text-body-md text-on-surface-variant mt-1 mb-6 text-center">Connectez-vous pour publier et gérer vos annonces.</p>

    @session('status')
        <div class="mb-5 rounded-xl bg-primary-fixed text-on-primary-fixed px-4 py-3 text-[14px]">{{ $value }}</div>
    @endsession

    <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-4">
        @csrf

        <div>
            <label for="login" class="field-label">Email ou numéro de téléphone</label>
            <input type="text" name="login" id="login" value="{{ old('login') }}" autocomplete="username" placeholder="vous@exemple.com ou 077 12 34 56" required autofocus class="field" @error('login') aria-invalid="true" @enderror />
            @error('login') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password" class="field-label">Mot de passe</label>
            <input type="password" name="password" id="password" autocomplete="current-password" required class="field" />
        </div>

        <label class="flex items-center gap-2 text-[14px] text-on-surface-variant">
            <input type="checkbox" name="remember" class="w-4 h-4 rounded accent-[var(--color-primary)]" />
            Rester connecté
        </label>

        <button type="submit" class="btn btn-primary w-full h-12">Se connecter</button>
    </form>

    <p class="mt-6 text-center text-[14px] text-on-surface-variant">
        Pas encore de compte ?
        <a href="{{ route('register') }}" class="font-bold text-primary hover:underline">Créer un compte gratuit</a>
    </p>
</x-layouts.guest>
