<x-layouts.app active="account" title="Ma boutique — AkibaShop">
    <form
        method="POST"
        action="{{ route('account.shop.update') }}"
        enctype="multipart/form-data"
        class="max-w-2xl mx-auto px-margin-mobile md:px-margin-desktop pt-4 md:pt-8 flex flex-col gap-4"
        x-data="{
            cover: @js($user->coverUrl()),
            avatar: @js($user->avatarUrl()),
            removeCover: false,
            removeAvatar: false,
            delivers: @js((bool) old('delivers', $user->delivers)),
            preview(event, key) {
                const file = event.target.files[0];
                if (file) { this[key] = URL.createObjectURL(file); this[key === 'cover' ? 'removeCover' : 'removeAvatar'] = false; }
            },
        }"
    >
        @csrf
        @method('PUT')

        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-2 min-w-0">
                <a href="{{ route('account.profile') }}" class="w-9 h-9 shrink-0 flex items-center justify-center rounded-full hover:bg-surface-container-low" aria-label="Retour au compte"><x-icon name="arrow_back" class="text-[20px]" /></a>
                <h1 class="text-[20px] leading-7 md:text-headline-lg font-extrabold text-on-surface truncate">Ma boutique</h1>
            </div>
            <a href="{{ route('sellers.show', $user) }}" class="shrink-0 text-[13px] font-semibold text-secondary hover:underline">Voir ma boutique</a>
        </div>

        @if($errors->any())
            <div role="alert" class="rounded-xl bg-error-container text-on-error-container px-4 py-3 text-[14px]">Certains champs sont à corriger.</div>
        @endif

        {{-- Cover + profile photo, previewed before saving --}}
        <section class="card overflow-hidden">
            <div class="relative aspect-[3/1] bg-gradient-to-br from-primary to-primary-container">
                <img x-show="cover && !removeCover" :src="cover" alt="" class="absolute inset-0 w-full h-full object-cover" x-cloak />
                <div class="absolute right-3 bottom-3 flex gap-2">
                    <label class="btn btn-sm bg-black/55 text-white hover:bg-black/70 cursor-pointer">
                        <x-icon name="camera" class="text-[16px]" /> <span x-text="cover && !removeCover ? 'Changer la couverture' : 'Ajouter une couverture'">Ajouter une couverture</span>
                        <input type="file" name="cover" accept="image/*" class="sr-only" @change="preview($event, 'cover')" />
                    </label>
                    <button type="button" x-show="cover && !removeCover" x-cloak @click="removeCover = true" class="btn btn-sm bg-black/55 text-white hover:bg-black/70 !px-2.5" aria-label="Retirer la couverture"><x-icon name="delete" class="text-[16px]" /></button>
                </div>
                <input type="hidden" name="remove_cover" :value="removeCover ? 1 : 0" />
            </div>

            <div class="relative z-10 px-4 pb-4 -mt-10 flex items-end gap-3">
                <div class="relative">
                    <div class="w-20 h-20 rounded-full ring-4 ring-surface-container-lowest bg-primary-fixed text-on-primary-fixed-variant flex items-center justify-center text-[30px] font-extrabold overflow-hidden">
                        <img x-show="avatar && !removeAvatar" :src="avatar" alt="" class="w-full h-full object-cover" x-cloak />
                        <span x-show="!avatar || removeAvatar">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span>
                    </div>
                    <label class="absolute -right-1 -bottom-1 w-8 h-8 rounded-full bg-primary text-on-primary flex items-center justify-center ring-2 ring-surface-container-lowest cursor-pointer" aria-label="Changer la photo de profil">
                        <x-icon name="camera" class="text-[15px]" />
                        <input type="file" name="avatar" accept="image/*" class="sr-only" @change="preview($event, 'avatar')" />
                    </label>
                    <input type="hidden" name="remove_avatar" :value="removeAvatar ? 1 : 0" />
                </div>
                <div class="pb-1 min-w-0">
                    <p class="text-[13px] font-semibold text-on-surface">Photo de profil</p>
                    <button type="button" x-show="avatar && !removeAvatar" x-cloak @click="removeAvatar = true" class="text-[12px] font-semibold text-error hover:underline">Retirer la photo</button>
                    <p x-show="!avatar || removeAvatar" class="text-[12px] text-on-surface-variant">Votre logo ou une photo de vous</p>
                </div>
            </div>
            @error('cover') <p class="field-error px-4 pb-3">{{ $message }}</p> @enderror
            @error('avatar') <p class="field-error px-4 pb-3">{{ $message }}</p> @enderror
        </section>

        {{-- Identity --}}
        <section class="card p-4 flex flex-col gap-4">
            <div>
                <label for="name" class="field-label">Nom de la boutique</label>
                <input id="name" name="name" type="text" maxlength="80" required value="{{ old('name', $user->name) }}" class="field" @error('name') aria-invalid="true" @enderror />
                @error('name') <p class="field-error">{{ $message }}</p> @enderror
            </div>
            <div x-data="{ length: @js(mb_strlen((string) old('bio', $user->bio))) }">
                <label for="bio" class="field-label">À propos</label>
                <textarea id="bio" name="bio" rows="4" maxlength="1000" @input="length = $el.value.length" placeholder="Ce que vous vendez, depuis quand, ce qui vous distingue…" class="field">{{ old('bio', $user->bio) }}</textarea>
                <p class="mt-1 text-right text-[11px] text-on-surface-variant"><span x-text="length"></span>/1000</p>
                @error('bio') <p class="field-error">{{ $message }}</p> @enderror
            </div>
        </section>

        {{-- Where --}}
        <section class="card p-4 flex flex-col gap-4">
            <h2 class="text-[13px] font-bold uppercase tracking-wide text-on-surface-variant">Localisation</h2>
            <div class="grid grid-cols-2 gap-3">
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
                    <input id="city" name="city" type="text" maxlength="100" value="{{ old('city', $user->city) }}" placeholder="Libreville" class="field" />
                </div>
            </div>
            <div>
                <label for="address" class="field-label">Adresse ou quartier <span class="font-normal text-on-surface-variant">(facultatif)</span></label>
                <input id="address" name="address" type="text" maxlength="150" value="{{ old('address', $user->address) }}" placeholder="Ex. : Louis, face à la pharmacie" class="field" />
                @error('address') <p class="field-error">{{ $message }}</p> @enderror
            </div>
        </section>

        {{-- Delivery & hours --}}
        <section class="card p-4 flex flex-col gap-4">
            <h2 class="text-[13px] font-bold uppercase tracking-wide text-on-surface-variant">Livraison & horaires</h2>
            <div class="flex items-center justify-between gap-3">
                <span>
                    <span id="delivers-label" class="block text-[15px] font-semibold text-on-surface">Je propose la livraison</span>
                    <span class="block text-[12px] text-on-surface-variant">Sinon, remise en main propre uniquement</span>
                </span>
                <input type="hidden" name="delivers" :value="delivers ? 1 : 0" />
                <button type="button" role="switch" aria-labelledby="delivers-label" :aria-checked="delivers.toString()" @click="delivers = !delivers" class="relative w-11 h-6 shrink-0 rounded-full transition-colors" :class="delivers ? 'bg-primary' : 'bg-surface-container-highest'">
                    <span class="absolute top-0.5 left-0.5 w-5 h-5 rounded-full bg-white shadow transition-transform" :class="delivers && 'translate-x-5'"></span>
                </button>
            </div>
            <div x-show="delivers" x-collapse x-cloak>
                <label for="delivery_note" class="field-label">Détails de livraison</label>
                <input id="delivery_note" name="delivery_note" type="text" maxlength="200" value="{{ old('delivery_note', $user->delivery_note) }}" placeholder="Ex. : Libreville et Owendo sous 24 h, 2 000 FCFA" class="field" />
                @error('delivery_note') <p class="field-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="opening_hours" class="field-label">Horaires <span class="font-normal text-on-surface-variant">(facultatif)</span></label>
                <input id="opening_hours" name="opening_hours" type="text" maxlength="120" value="{{ old('opening_hours', $user->opening_hours) }}" placeholder="Ex. : Lun – Sam, 8 h – 19 h" class="field" />
                @error('opening_hours') <p class="field-error">{{ $message }}</p> @enderror
            </div>
        </section>

        <div class="sticky bottom-16 md:bottom-0 -mx-margin-mobile md:mx-0 px-margin-mobile md:px-0 py-3 bg-background/95 backdrop-blur">
            <button type="submit" class="btn btn-primary w-full h-12 text-[15px]">Enregistrer ma boutique</button>
        </div>
    </form>
</x-layouts.app>
