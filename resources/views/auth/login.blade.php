<x-layouts.guest title="Connexion — AkibaShop">
    <h1 class="font-headline-lg text-headline-lg text-primary mb-2 text-center">Bon retour</h1>
    <p class="font-body-md text-body-md text-on-surface-variant mb-8 text-center">Connectez-vous pour retrouver votre panier et vos commandes.</p>

    @session('status')
        <div class="mb-6 rounded-lg bg-primary-container/10 text-primary px-4 py-3 font-body-md text-body-md">
            {{ $value }}
        </div>
    @endsession

    <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
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

        <label class="flex items-center gap-2">
            <input type="checkbox" name="remember" class="w-4 h-4 rounded-sm border-outline-variant text-primary focus:ring-primary" />
            <span class="font-body-md text-body-md text-on-surface-variant">Se souvenir de moi</span>
        </label>

        <button type="submit" class="w-full bg-primary text-on-primary h-12 rounded-full font-label-lg text-label-lg uppercase tracking-widest hover:bg-primary-container transition-colors">
            Se connecter
        </button>
    </form>

    <p class="mt-8 text-center font-body-md text-body-md text-on-surface-variant">
        Pas encore de compte ?
        <a href="{{ route('register') }}" class="text-primary font-bold hover:underline">Créer un compte</a>
    </p>
</x-layouts.guest>
