<x-layouts.app active="account" title="Mon compte — AkibaShop">
    <div class="max-w-2xl mx-auto px-margin-mobile md:px-margin-desktop pt-4 md:pt-8 flex flex-col gap-4">

        {{-- Identity --}}
        <section class="card p-4 md:p-5">
            <div class="flex items-center gap-4">
                <x-avatar :user="$user" size="w-16 h-16 text-[26px]" />
                <div class="min-w-0 flex-1">
                    <h1 class="text-[20px] leading-7 font-extrabold text-on-surface truncate">{{ $user->name }}</h1>
                    <p class="text-[13px] text-on-surface-variant truncate">
                        {{ $user->is_pro ? 'Professionnel' : 'Particulier' }}
                        @if($user->location()) · {{ $user->location() }} @endif
                    </p>
                    <div class="flex flex-wrap items-center gap-1.5 mt-1">
                        @if($user->is_verified)
                            <span class="inline-flex items-center gap-1 rounded-md bg-secondary-container text-on-secondary-container px-1.5 py-0.5 text-[11px] font-bold"><x-icon name="verified" class="text-[12px]" /> Vérifié</span>
                        @endif
                        <span class="text-[12px] text-on-surface-variant">Membre depuis {{ $user->created_at->translatedFormat('F Y') }}</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-2 mt-4">
                @foreach([['En ligne', $stats['online']], ['En validation', $stats['pending']], ['Vues', $stats['views']]] as [$label, $value])
                    <div class="rounded-xl bg-surface-container-low px-3 py-2 text-center">
                        <p class="text-[18px] leading-6 font-extrabold text-on-surface">{{ number_format($value, 0, ',', ' ') }}</p>
                        <p class="text-[11px] text-on-surface-variant">{{ $label }}</p>
                    </div>
                @endforeach
            </div>

            <div class="grid grid-cols-2 gap-2 mt-3">
                <a href="{{ route('account.shop.edit') }}" class="btn btn-primary btn-sm"><x-icon name="edit" class="text-[15px]" /> Modifier ma boutique</a>
                <a href="{{ route('sellers.show', $user) }}" class="btn btn-outline btn-sm">Voir ma boutique</a>
            </div>
        </section>

        {{-- Activity --}}
        <nav aria-label="Mon activité" class="card divide-y divide-surface-variant overflow-hidden">
            @foreach([
                [route('account.shop.edit'), 'storefront', 'Ma boutique', $user->avatar_path && $user->bio ? null : 'À compléter', null],
                [route('account.listings'), 'listings', 'Mes annonces', $stats['online'] ? $stats['online'].' en ligne' : null, null],
                [route('account.conversations.index'), 'chat', 'Messages', null, $stats['unread']],
                [route('account.favorites'), 'favorite', 'Mes favoris', $stats['favorites'] ?: null, null],
                [route('listings.create'), 'add_circle', 'Déposer une annonce', null, null],
            ] as [$href, $icon, $label, $hint, $badge])
                <a href="{{ $href }}" class="flex items-center gap-3 px-4 min-h-[56px] hover:bg-surface-container-low active:bg-surface-container-low transition-colors">
                    <span class="w-9 h-9 shrink-0 rounded-xl bg-primary-fixed text-primary flex items-center justify-center"><x-icon :name="$icon" class="text-[18px]" /></span>
                    <span class="flex-1 text-[15px] font-semibold text-on-surface">{{ $label }}</span>
                    @if($badge)
                        <span class="min-w-5 h-5 px-1.5 rounded-full bg-error text-on-error text-[11px] font-bold flex items-center justify-center">{{ $badge }}</span>
                    @elseif($hint)
                        <span class="text-[13px] text-on-surface-variant">{{ $hint }}</span>
                    @endif
                    <x-icon name="chevron_right" class="text-[18px] text-outline" />
                </a>
            @endforeach
        </nav>

        {{-- Personal details, folded away until needed --}}
        <section class="card overflow-hidden" x-data="{ open: @js($errors->any()) }">
            <button type="button" @click="open = !open" :aria-expanded="open.toString()" aria-controls="profile-form" class="w-full flex items-center gap-3 px-4 min-h-[56px] text-left hover:bg-surface-container-low">
                <span class="w-9 h-9 shrink-0 rounded-xl bg-surface-container-low text-on-surface flex items-center justify-center"><x-icon name="person" class="text-[18px]" /></span>
                <span class="flex-1 min-w-0">
                    <span class="block text-[15px] font-semibold text-on-surface">Mes informations</span>
                    <span class="block text-[12px] text-on-surface-variant truncate">{{ $user->email }}@if($user->phone) · {{ \App\Support\Phone::format($user->phone) }}@endif</span>
                </span>
                <x-icon name="expand_more" class="text-[20px] text-outline transition-transform" x-bind:class="open && 'rotate-180'" />
            </button>

            <form id="profile-form" x-show="open" x-collapse x-cloak method="POST" action="{{ route('account.profile.update') }}" class="border-t border-surface-variant p-4 grid sm:grid-cols-2 gap-4">
                @csrf
                @method('PUT')
                <div class="sm:col-span-2">
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
                    <label for="phone" class="field-label">Téléphone</label>
                    <x-phone-input name="phone" country-name="phone_country" :value="$user->phone" :invalid="$errors->has('phone')" />
                    @error('phone') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="province" class="field-label">Province</label>
                    <select id="province" name="province" class="field">
                        <option value="">—</option>
                        @foreach($provinces as $slug => $provinceName)
                            <option value="{{ $slug }}" @selected(old('province', $user->province) === $slug)>{{ $provinceName }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="city" class="field-label">Ville</label>
                    <input id="city" name="city" type="text" maxlength="100" value="{{ old('city', $user->city) }}" class="field" />
                </div>
                <label class="sm:col-span-2 flex items-center gap-3 text-[14px] text-on-surface">
                    <input type="checkbox" name="is_pro" value="1" @checked(old('is_pro', $user->is_pro)) class="w-5 h-5 rounded accent-[var(--color-primary)]" />
                    Je vends en tant que professionnel (boutique, garage, agence…)
                </label>
                <button type="submit" class="btn btn-primary sm:col-span-2">Enregistrer</button>
            </form>
        </section>

        {{-- Help --}}
        <section class="card divide-y divide-surface-variant overflow-hidden">
            @foreach([
                [route('pages.safety'), 'warning', 'Conseils de sécurité'],
                [route('pages.contact'), 'mail', 'Aide & contact'],
                [route('pages.terms'), 'info', 'Conditions d’utilisation'],
                [route('pages.privacy'), 'lock', 'Confidentialité'],
            ] as [$href, $icon, $label])
                <a href="{{ $href }}" class="flex items-center gap-3 px-4 min-h-[52px] hover:bg-surface-container-low transition-colors">
                    <span class="w-9 h-9 shrink-0 rounded-xl bg-surface-container-low text-on-surface flex items-center justify-center"><x-icon :name="$icon" class="text-[18px]" /></span>
                    <span class="flex-1 text-[15px] text-on-surface">{{ $label }}</span>
                    <x-icon name="chevron_right" class="text-[18px] text-outline" />
                </a>
            @endforeach
        </section>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-outline w-full !text-error"><x-icon name="logout" class="text-[18px]" /> Se déconnecter</button>
        </form>
    </div>
</x-layouts.app>
