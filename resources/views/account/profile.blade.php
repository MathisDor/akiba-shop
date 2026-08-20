@php($user = auth()->user())

<x-layouts.app active="account" title="Mon Compte — AkibaShop">
    <div class="px-margin-mobile md:px-margin-desktop pt-8 pb-16 max-w-container-max mx-auto w-full flex flex-col gap-16" x-data="{ editing: false }">

        @session('status')
            <div class="rounded-lg bg-primary-container/10 text-primary px-4 py-3 font-body-md text-body-md text-center">
                {{ $value }}
            </div>
        @endsession

        <section class="flex flex-col items-center justify-center text-center gap-6">
            <div class="w-32 h-32 md:w-40 md:h-40 rounded-full bg-primary text-on-primary flex items-center justify-center text-4xl font-headline-lg font-headline-lg shadow-[0_8px_30px_rgba(0,0,0,0.06)] ring-4 ring-surface-container-lowest">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>

            <div class="flex flex-col gap-2 items-center" x-show="!editing">
                <h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight">{{ $user->name }}</h1>
                <p class="font-body-md text-body-md text-on-surface-variant">{{ $user->email }}</p>
                @if($user->phone)
                    <p class="font-body-md text-body-md text-on-surface-variant">{{ $user->phone }}</p>
                @endif
            </div>

            <button @click="editing = !editing" type="button" x-show="!editing" class="mt-2 px-8 py-3 rounded-full bg-primary text-on-primary font-label-sm text-label-sm uppercase tracking-widest hover:bg-primary-container hover:text-on-primary-container transition-colors duration-300 min-h-[48px] shadow-sm">
                Éditer le profil
            </button>

            <form method="POST" action="{{ route('account.profile.update') }}" x-show="editing" x-cloak class="w-full max-w-md flex flex-col gap-4 text-left mt-2">
                @csrf
                @method('PUT')
                <div>
                    <label class="font-label-sm text-label-sm text-on-surface-variant">Nom complet</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full bg-surface border-b border-outline-variant py-2 focus:border-primary focus:ring-0 font-body-md text-body-md" />
                </div>
                <div>
                    <label class="font-label-sm text-label-sm text-on-surface-variant">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full bg-surface border-b border-outline-variant py-2 focus:border-primary focus:ring-0 font-body-md text-body-md" />
                </div>
                <div>
                    <label class="font-label-sm text-label-sm text-on-surface-variant">Téléphone</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full bg-surface border-b border-outline-variant py-2 focus:border-primary focus:ring-0 font-body-md text-body-md" />
                </div>
                <div class="flex gap-3 mt-2 justify-center">
                    <button type="submit" class="px-8 py-3 rounded-full bg-primary text-on-primary font-label-sm text-label-sm uppercase tracking-widest">Enregistrer</button>
                    <button type="button" @click="editing = false" class="px-8 py-3 rounded-full border border-outline text-on-surface-variant font-label-sm text-label-sm uppercase tracking-widest">Annuler</button>
                </div>
            </form>
        </section>

        <section class="grid grid-cols-1 md:grid-cols-2 gap-gutter max-w-4xl w-full mx-auto">
            <a href="{{ route('account.orders') }}" class="flex items-center gap-6 p-6 md:p-8 bg-surface-container-lowest rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.02)] hover:shadow-[0_12px_40px_rgba(0,0,0,0.06)] hover:-translate-y-1 transition-all duration-300 group">
                <div class="w-12 h-12 rounded-full bg-surface-container-low flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-on-primary transition-colors">
                    <x-icon name="local_shipping" />
                </div>
                <div class="flex-grow">
                    <h3 class="font-headline-md text-headline-md text-on-surface mb-1">Mes Commandes</h3>
                    <p class="font-body-md text-body-md text-on-surface-variant text-sm">Suivre et gérer vos achats</p>
                </div>
                <x-icon name="chevron_right" class="text-outline group-hover:text-primary transition-colors" />
            </a>
            <a href="{{ route('account.addresses') }}" class="flex items-center gap-6 p-6 md:p-8 bg-surface-container-lowest rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.02)] hover:shadow-[0_12px_40px_rgba(0,0,0,0.06)] hover:-translate-y-1 transition-all duration-300 group">
                <div class="w-12 h-12 rounded-full bg-surface-container-low flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-on-primary transition-colors">
                    <x-icon name="location_on" />
                </div>
                <div class="flex-grow">
                    <h3 class="font-headline-md text-headline-md text-on-surface mb-1">Mes Adresses</h3>
                    <p class="font-body-md text-body-md text-on-surface-variant text-sm">Carnet d'adresses de livraison</p>
                </div>
                <x-icon name="chevron_right" class="text-outline group-hover:text-primary transition-colors" />
            </a>
            <a href="{{ route('account.favorites') }}" class="flex items-center gap-6 p-6 md:p-8 bg-surface-container-lowest rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.02)] hover:shadow-[0_12px_40px_rgba(0,0,0,0.06)] hover:-translate-y-1 transition-all duration-300 group">
                <div class="w-12 h-12 rounded-full bg-surface-container-low flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-on-primary transition-colors">
                    <x-icon name="favorite_border" />
                </div>
                <div class="flex-grow">
                    <h3 class="font-headline-md text-headline-md text-on-surface mb-1">Favoris</h3>
                    <p class="font-body-md text-body-md text-on-surface-variant text-sm">Vos articles sauvegardés</p>
                </div>
                <x-icon name="chevron_right" class="text-outline group-hover:text-primary transition-colors" />
            </a>
            <div class="flex items-center gap-6 p-6 md:p-8 bg-surface-container-lowest rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.02)] opacity-60 cursor-not-allowed">
                <div class="w-12 h-12 rounded-full bg-surface-container-low flex items-center justify-center text-primary">
                    <x-icon name="settings" />
                </div>
                <div class="flex-grow">
                    <h3 class="font-headline-md text-headline-md text-on-surface mb-1">Paramètres</h3>
                    <p class="font-body-md text-body-md text-on-surface-variant text-sm">Bientôt disponible</p>
                </div>
            </div>
        </section>

        <section class="flex justify-center max-w-4xl w-full mx-auto -mt-8">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center gap-3 px-8 py-4 rounded-xl text-error hover:bg-error-container/50 transition-colors duration-300 w-full md:w-auto justify-center min-h-[48px]">
                    <x-icon name="logout" />
                    <span class="font-label-sm text-label-sm uppercase tracking-widest">Se déconnecter</span>
                </button>
            </form>
        </section>
    </div>
</x-layouts.app>
