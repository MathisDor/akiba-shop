<x-layouts.guest title="Créer un compte — AkibaShop">
    <h1 class="text-headline-lg text-on-surface text-center">Créer un compte</h1>
    <p class="text-body-md text-on-surface-variant mt-1 mb-6 text-center">Gratuit. Publiez vos annonces et gardez vos favoris.</p>

    <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-4">
        @csrf

        <div>
            <label for="name" class="field-label">Nom ou nom de la boutique</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" autocomplete="name" required autofocus class="field" @error('name') aria-invalid="true" @enderror />
            @error('name') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="phone" class="field-label">Téléphone</label>
            <x-phone-input name="phone" country-name="phone_country" :invalid="$errors->has('phone')" />
            @error('phone') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="email" class="field-label">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" autocomplete="email" required class="field" @error('email') aria-invalid="true" @enderror />
            @error('email') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label for="country_code" class="field-label">Pays</label>
                <select id="country_code" name="country_code" class="field" required>
                    @foreach($countries as $code => $countryName)
                        <option value="{{ $code }}" @selected(old('country_code', config('marketplace.default_country')) === $code)>{{ $countryName }}</option>
                    @endforeach
                </select>
                @error('country_code') <p class="field-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="city" class="field-label">Ville <span class="font-normal text-on-surface-variant">(facultatif)</span></label>
                <input id="city" name="city" type="text" maxlength="100" value="{{ old('city') }}" autocomplete="address-level2" class="field" />
            </div>
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label for="password" class="field-label">Mot de passe</label>
                <input type="password" name="password" id="password" autocomplete="new-password" required class="field" @error('password') aria-invalid="true" @enderror />
            </div>
            <div>
                <label for="password_confirmation" class="field-label">Confirmation</label>
                <input type="password" name="password_confirmation" id="password_confirmation" autocomplete="new-password" required class="field" />
            </div>
        </div>
        @error('password') <p class="field-error -mt-2">{{ $message }}</p> @enderror

        <label class="flex items-center gap-2 text-[14px] text-on-surface">
            <input type="checkbox" name="is_pro" value="1" @checked(old('is_pro')) class="w-4 h-4 rounded accent-[var(--color-primary)]" />
            Je suis un professionnel (boutique, garage, agence…)
        </label>

        <label class="flex items-start gap-2 text-[14px] text-on-surface">
            <input type="checkbox" name="terms" value="1" @checked(old('terms')) required class="mt-0.5 w-4 h-4 rounded accent-[var(--color-primary)]" />
            <span>J’accepte les <a href="{{ route('pages.terms') }}" target="_blank" class="font-semibold text-secondary underline">conditions d’utilisation</a> et la <a href="{{ route('pages.privacy') }}" target="_blank" class="font-semibold text-secondary underline">politique de confidentialité</a>.</span>
        </label>
        @error('terms') <p class="field-error -mt-2">{{ $message }}</p> @enderror

        <button type="submit" class="btn btn-primary w-full h-12">Créer mon compte</button>
    </form>

    <p class="mt-6 text-center text-[14px] text-on-surface-variant">
        Déjà inscrit ?
        <a href="{{ route('login') }}" class="font-bold text-primary hover:underline">Se connecter</a>
    </p>
</x-layouts.guest>
