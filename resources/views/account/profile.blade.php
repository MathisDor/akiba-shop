@php($phone = old('phone', $user->phone ? \App\Support\GabonPhone::format($user->phone) : ''))

<x-layouts.app active="account" title="Mon compte — AkibaShop">
    <div class="max-w-4xl mx-auto px-margin-mobile md:px-margin-desktop pt-6">
        <div class="flex items-center gap-4 mb-5">
            <span class="w-14 h-14 rounded-full bg-primary-fixed text-on-primary-fixed-variant flex items-center justify-center text-[22px] font-extrabold">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span>
            <div class="min-w-0">
                <h1 class="text-headline-md md:text-headline-lg text-on-surface truncate">{{ $user->name }}</h1>
                <a href="{{ route('sellers.show', $user) }}" class="text-[13px] font-semibold text-secondary hover:underline">Voir mon profil public</a>
            </div>
        </div>

        @include('account.partials.nav', ['current' => 'profile'])

        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
            @foreach([
                ['En ligne', $stats['online'], route('account.listings')],
                ['En validation', $stats['pending'], route('account.listings')],
                ['Vues totales', $stats['views'], null],
                ['Favoris', $stats['favorites'], route('account.favorites')],
            ] as [$label, $value, $link])
                @if($link)
                    <a href="{{ $link }}" class="card p-4 hover:border-primary transition-colors">
                        <p class="text-[24px] font-extrabold text-on-surface">{{ number_format($value, 0, ',', ' ') }}</p>
                        <p class="text-[13px] text-on-surface-variant">{{ $label }}</p>
                    </a>
                @else
                    <div class="card p-4">
                        <p class="text-[24px] font-extrabold text-on-surface">{{ number_format($value, 0, ',', ' ') }}</p>
                        <p class="text-[13px] text-on-surface-variant">{{ $label }}</p>
                    </div>
                @endif
            @endforeach
        </div>

        <section class="card p-5 md:p-6">
            <h2 class="text-[17px] font-extrabold text-on-surface mb-4">Mes informations</h2>
            <form method="POST" action="{{ route('account.profile.update') }}" class="grid sm:grid-cols-2 gap-4">
                @csrf
                @method('PUT')
                <div>
                    <label for="name" class="field-label">Nom affiché</label>
                    <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required class="field" />
                    @error('name') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="email" class="field-label">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required class="field" />
                    @error('email') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="phone" class="field-label">Téléphone (+241)</label>
                    <input id="phone" name="phone" type="tel" inputmode="tel" value="{{ $phone }}" placeholder="077 12 34 56" required class="field" />
                    @error('phone') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="city_id" class="field-label">Ville</label>
                    <select id="city_id" name="city_id" class="field">
                        <option value="">—</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" @selected((int) old('city_id', $user->city_id) === $city->id)>{{ $city->name }}</option>
                        @endforeach
                    </select>
                </div>
                <label class="sm:col-span-2 flex items-center gap-3 text-[14px] text-on-surface">
                    <input type="checkbox" name="is_pro" value="1" @checked(old('is_pro', $user->is_pro)) class="w-5 h-5 rounded accent-[var(--color-primary)]" />
                    Je vends en tant que professionnel (boutique, garage, agence…)
                </label>
                <div class="sm:col-span-2 flex flex-wrap gap-3 justify-between items-center pt-2">
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </section>

        <form method="POST" action="{{ route('logout') }}" class="mt-6">
            @csrf
            <button type="submit" class="btn btn-outline text-error"><x-icon name="logout" class="text-[18px]" /> Se déconnecter</button>
        </form>
    </div>
</x-layouts.app>
