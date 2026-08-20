<x-layouts.guest title="Créer un compte — AkibaShop">
    <h1 class="font-headline-lg text-headline-lg text-primary mb-2 text-center">Créer un compte</h1>
    <p class="font-body-md text-body-md text-on-surface-variant mb-8 text-center">Rejoignez AkibaShop pour commander et suivre vos livraisons.</p>

    <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-6">
        @csrf

        <div class="relative-input-group">
            <input type="text" name="name" id="name" class="input-underline font-body-md text-body-md text-on-surface h-10" placeholder=" " value="{{ old('name') }}" required autofocus />
            <label for="name" class="font-body-md">Nom complet</label>
        </div>
        @error('name')
            <p class="-mt-4 text-error font-label-sm text-label-sm">{{ $message }}</p>
        @enderror

        <div class="relative-input-group">
            <input type="email" name="email" id="email" class="input-underline font-body-md text-body-md text-on-surface h-10" placeholder=" " value="{{ old('email') }}" required />
            <label for="email" class="font-body-md">Adresse email</label>
        </div>
        @error('email')
            <p class="-mt-4 text-error font-label-sm text-label-sm">{{ $message }}</p>
        @enderror

        <div class="relative-input-group flex items-end">
            <div class="flex items-center border-b border-outline-variant pb-2 mr-4 text-on-surface-variant font-body-md h-10">
                <span class="mr-1 mt-1">🇬🇦</span>
                <span>+241</span>
            </div>
            <div class="flex-grow relative">
                <input type="tel" name="phone" id="phone" class="input-underline font-body-md text-body-md text-on-surface h-10" placeholder=" " value="{{ old('phone') }}" />
                <label for="phone" class="font-body-md">Numéro de téléphone</label>
            </div>
        </div>

        <div class="relative-input-group">
            <input type="password" name="password" id="password" class="input-underline font-body-md text-body-md text-on-surface h-10" placeholder=" " required />
            <label for="password" class="font-body-md">Mot de passe</label>
        </div>
        @error('password')
            <p class="-mt-4 text-error font-label-sm text-label-sm">{{ $message }}</p>
        @enderror

        <div class="relative-input-group">
            <input type="password" name="password_confirmation" id="password_confirmation" class="input-underline font-body-md text-body-md text-on-surface h-10" placeholder=" " required />
            <label for="password_confirmation" class="font-body-md">Confirmer le mot de passe</label>
        </div>

        <button type="submit" class="w-full bg-primary text-on-primary h-12 rounded-full font-label-lg text-label-lg uppercase tracking-widest hover:bg-primary-container transition-colors">
            Créer mon compte
        </button>
    </form>

    <p class="mt-8 text-center font-body-md text-body-md text-on-surface-variant">
        Déjà client ?
        <a href="{{ route('login') }}" class="text-primary font-bold hover:underline">Se connecter</a>
    </p>
</x-layouts.guest>
