@php
    $editing = $listing->exists;
    $selectedCategoryId = (int) old('listing_category_id', $listing->listing_category_id);
    $selectedCityId = (int) old('city_id', $listing->city_id);

    $categoryTree = $categories->map(fn ($root) => [
        'id' => $root->id,
        'slug' => $root->slug,
        'name' => $root->name,
        'children' => $root->children->map(fn ($child) => ['id' => $child->id, 'name' => $child->name])->values(),
    ])->values();

    $districtsByCity = $cities->mapWithKeys(fn ($city) => [
        $city->id => $city->districts->map(fn ($d) => ['id' => $d->id, 'name' => $d->name])->values(),
    ]);

    // Re-open the step holding the first validation error.
    $stepFields = [
        1 => ['listing_category_id'],
        2 => ['title', 'description', 'price', 'condition', 'specs'],
        3 => ['photos', 'remove_images', 'city_id', 'district_id', 'contact_phone'],
    ];
    $initialStep = 1;
    if ($errors->any()) {
        foreach ($stepFields as $step => $fields) {
            if (collect($errors->keys())->contains(fn ($key) => in_array(\Illuminate\Support\Str::before($key, '.'), $fields, true))) {
                $initialStep = $step;
                break;
            }
        }
    } elseif ($editing) {
        $initialStep = 2;
    }

    $maxImages = config('marketplace.max_images');
    $existingImages = $editing ? $listing->images : collect();
    $phone = old('contact_phone', $listing->contact_phone ? \App\Support\GabonPhone::format($listing->contact_phone) : '');
@endphp

<x-layouts.app :title="($editing ? 'Modifier mon annonce' : 'Déposer une annonce').' — AkibaShop'" hide-footer>
    <div class="max-w-3xl mx-auto px-margin-mobile md:px-margin-desktop pt-4 md:pt-8 pb-12"
        x-data="listingForm({
            step: {{ $initialStep }},
            tree: @js($categoryTree),
            categoryId: {{ $selectedCategoryId ?: 'null' }},
            cityId: {{ $selectedCityId ?: 'null' }},
            districtId: @js(old('district_id', $listing->district_id)),
            districts: @js($districtsByCity),
            existingCount: {{ $existingImages->count() }},
            maxImages: {{ $maxImages }},
            price: @js(old('price', $listing->price !== null ? number_format($listing->price, 0, ',', ' ') : '')),
        })"
    >
        <h1 class="text-headline-lg text-on-surface">{{ $editing ? 'Modifier mon annonce' : 'Déposer une annonce' }}</h1>
        <p class="text-body-md text-on-surface-variant mt-1">
            @if($editing)
                Après modification, l’annonce repasse en validation avant d’être de nouveau visible.
            @else
                Gratuit · validée par notre équipe avant publication · visible {{ config('marketplace.listing_lifetime_days') }} jours.
            @endif
        </p>

        {{-- Stepper --}}
        <ol class="mt-6 mb-5 grid grid-cols-3 gap-2" aria-label="Étapes">
            @foreach([1 => 'Catégorie', 2 => 'Détails', 3 => 'Photos & contact'] as $n => $label)
                <li>
                    <button type="button" @click="goTo({{ $n }})" class="w-full text-left" :aria-current="step === {{ $n }} ? 'step' : null">
                        <span class="block h-1.5 rounded-full transition-colors" :class="step >= {{ $n }} ? 'bg-primary' : 'bg-surface-container-highest'"></span>
                        <span class="mt-2 block text-[12px] md:text-[13px] font-semibold" :class="step === {{ $n }} ? 'text-on-surface' : 'text-on-surface-variant'">{{ $n }}. {{ $label }}</span>
                    </button>
                </li>
            @endforeach
        </ol>

        @if($errors->any())
            <div role="alert" class="mb-4 rounded-xl bg-error-container text-on-error-container px-4 py-3 text-[14px]">
                Certains champs sont à corriger avant de publier.
            </div>
        @endif

        <form
            method="POST"
            action="{{ $editing ? route('account.listings.update', $listing) : route('listings.store') }}"
            enctype="multipart/form-data"
            novalidate
            x-ref="form"
            @submit.prevent="submit()"
        >
            @csrf
            @if($editing) @method('PUT') @endif
            <input type="hidden" name="listing_category_id" :value="categoryId ?? ''" />

            {{-- Step 1: category --}}
            <section x-show="step === 1" data-step="1" class="card p-4 md:p-6 flex flex-col gap-4">
                <h2 class="text-[17px] font-extrabold text-on-surface">Que proposez-vous ?</h2>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                    @foreach($categories as $root)
                        <button
                            type="button"
                            @click="pickRoot({{ $root->id }})"
                            class="flex items-center gap-3 rounded-xl border p-3 text-left transition-colors"
                            :class="rootId === {{ $root->id }} ? 'border-primary bg-primary-fixed' : 'border-outline-variant bg-surface-container-lowest hover:border-primary'"
                        >
                            <span class="w-9 h-9 rounded-lg bg-surface-container-low text-primary flex items-center justify-center shrink-0"><x-icon :name="$root->icon ?? 'package'" class="text-[20px]" /></span>
                            <span class="text-[13px] font-semibold leading-4 text-on-surface">{{ $root->name }}</span>
                        </button>
                    @endforeach
                </div>

                <template x-if="root && root.children.length">
                    <div>
                        <label for="subcategory" class="field-label">Sous-catégorie</label>
                        <select id="subcategory" class="field" x-model.number="categoryId" required data-message="Choisissez une sous-catégorie.">
                            <option value="">Choisissez…</option>
                            <template x-for="child in root.children" :key="child.id">
                                <option :value="child.id" x-text="child.name" :selected="child.id === categoryId"></option>
                            </template>
                        </select>
                    </div>
                </template>

                @error('listing_category_id') <p class="field-error">{{ $message }}</p> @enderror
                <p class="field-error" x-show="stepError === 1" x-cloak>Choisissez une catégorie pour continuer.</p>

                <div class="flex justify-end">
                    <button type="button" @click="next()" class="btn btn-primary">Continuer <x-icon name="arrow_forward" class="text-[18px]" /></button>
                </div>
            </section>

            {{-- Step 2: details --}}
            <section x-show="step === 2" x-cloak data-step="2" class="card p-4 md:p-6 flex flex-col gap-5">
                <div class="flex items-center justify-between gap-2">
                    <h2 class="text-[17px] font-extrabold text-on-surface">Décrivez votre annonce</h2>
                    <button type="button" @click="goTo(1)" class="text-[13px] font-semibold text-secondary hover:underline" x-text="categoryLabel()"></button>
                </div>

                <div>
                    <label for="title" class="field-label">Titre de l’annonce</label>
                    <input id="title" name="title" type="text" required minlength="5" maxlength="100" value="{{ old('title', $listing->title) }}" placeholder="Ex. : Toyota RAV4 2015 automatique" class="field" @error('title') aria-invalid="true" @enderror />
                    @error('title') <p class="field-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="description" class="field-label">Description</label>
                    <textarea id="description" name="description" rows="6" required minlength="20" maxlength="5000" placeholder="État, ancienneté, raison de la vente, ce qui est inclus…" class="field" @error('description') aria-invalid="true" @enderror>{{ old('description', $listing->description) }}</textarea>
                    @error('description') <p class="field-error">{{ $message }}</p> @enderror
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label for="price" class="field-label">Prix</label>
                        <div class="relative">
                            <input id="price" name="price" type="text" inputmode="numeric" x-model="price" @input="price = formatPrice($event.target.value)" placeholder="Laisser vide si à débattre" class="field pr-16" @error('price') aria-invalid="true" @enderror />
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-[14px] font-bold text-on-surface-variant">FCFA</span>
                        </div>
                        @error('price') <p class="field-error">{{ $message }}</p> @enderror
                        <label class="mt-2 flex items-center gap-2 text-[14px] text-on-surface" x-show="price !== ''">
                            <input type="checkbox" name="is_negotiable" value="1" @checked(old('is_negotiable', $listing->is_negotiable)) class="w-4 h-4 rounded accent-[var(--color-primary)]" />
                            Prix négociable
                        </label>
                    </div>

                    <div>
                        <span class="field-label" id="condition-label">État</span>
                        <div class="grid grid-cols-3 gap-2" role="radiogroup" aria-labelledby="condition-label">
                            @foreach(['' => 'Non précisé', ...collect($conditions)->mapWithKeys(fn ($c) => [$c->value => $c->label()])->all()] as $value => $label)
                                <label class="cursor-pointer">
                                    <input type="radio" name="condition" value="{{ $value }}" class="peer sr-only" @checked((string) old('condition', $listing->condition?->value ?? '') === (string) $value) />
                                    <span class="flex items-center justify-center text-center min-h-[46px] px-1 rounded-xl border border-outline-variant text-[13px] font-semibold text-on-surface peer-checked:border-primary peer-checked:bg-primary-fixed peer-checked:text-on-primary-fixed-variant peer-focus-visible:ring-2 peer-focus-visible:ring-primary">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Category-specific fields --}}
                @foreach($categoryFields as $rootSlug => $fields)
                    <fieldset x-show="root && root.slug === @js($rootSlug)" x-cloak class="grid sm:grid-cols-2 gap-4 rounded-xl bg-surface-container-low p-4">
                        <legend class="sr-only">Caractéristiques</legend>
                        @foreach($fields as $field)
                            @php($value = old('specs.'.$field['key'], $listing->specs[$field['key']] ?? ''))
                            <div>
                                <label for="spec-{{ $rootSlug }}-{{ $field['key'] }}" class="field-label">{{ $field['label'] }} <span class="font-normal text-on-surface-variant">(facultatif)</span></label>
                                @if($field['type'] === 'select')
                                    <select id="spec-{{ $rootSlug }}-{{ $field['key'] }}" name="specs[{{ $field['key'] }}]" class="field" :disabled="!root || root.slug !== @js($rootSlug)">
                                        <option value="">—</option>
                                        @foreach($field['options'] as $option)
                                            <option value="{{ $option }}" @selected($value === $option)>{{ $option }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <div class="relative">
                                        <input
                                            id="spec-{{ $rootSlug }}-{{ $field['key'] }}"
                                            name="specs[{{ $field['key'] }}]"
                                            type="{{ $field['type'] === 'number' ? 'number' : 'text' }}"
                                            @if($field['type'] === 'number') inputmode="numeric" min="{{ $field['min'] ?? 0 }}" max="{{ $field['max'] ?? '' }}" @endif
                                            value="{{ $value }}"
                                            placeholder="{{ $field['placeholder'] ?? '' }}"
                                            maxlength="100"
                                            class="field {{ isset($field['suffix']) ? 'pr-12' : '' }}"
                                            :disabled="!root || root.slug !== @js($rootSlug)"
                                        />
                                        @isset($field['suffix'])
                                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-[13px] font-semibold text-on-surface-variant">{{ $field['suffix'] }}</span>
                                        @endisset
                                    </div>
                                @endif
                                @error('specs.'.$field['key']) <p class="field-error">{{ $message }}</p> @enderror
                            </div>
                        @endforeach
                    </fieldset>
                @endforeach

                <div class="flex justify-between">
                    <button type="button" @click="goTo(1)" class="btn btn-outline"><x-icon name="arrow_back" class="text-[18px]" /> Retour</button>
                    <button type="button" @click="next()" class="btn btn-primary">Continuer <x-icon name="arrow_forward" class="text-[18px]" /></button>
                </div>
            </section>

            {{-- Step 3: photos, location, contact --}}
            <section x-show="step === 3" x-cloak data-step="3" class="card p-4 md:p-6 flex flex-col gap-5">
                <div>
                    <h2 class="text-[17px] font-extrabold text-on-surface">Photos</h2>
                    <p class="text-[13px] text-on-surface-variant mt-0.5">Jusqu’à {{ $maxImages }} photos. Les annonces avec photos sont beaucoup plus consultées. Elles sont compressées automatiquement.</p>
                </div>

                <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                    @foreach($existingImages as $image)
                        <label class="relative aspect-square rounded-xl overflow-hidden bg-surface-container-low group" x-data="{ removed: false }">
                            <img src="{{ $image->url() }}" alt="" class="w-full h-full object-cover transition-opacity" :class="removed && 'opacity-30'" />
                            <input type="checkbox" name="remove_images[]" value="{{ $image->id }}" class="sr-only" x-model="removed" @change="existingCount += removed ? -1 : 1" />
                            <span class="absolute top-1.5 right-1.5 w-7 h-7 rounded-full bg-white/95 text-on-surface flex items-center justify-center shadow" :class="removed && '!bg-primary !text-on-primary'">
                                <x-icon name="close" class="text-[16px]" x-show="!removed" />
                                <x-icon name="replay" class="text-[14px]" x-show="removed" x-cloak />
                            </span>
                            <span class="sr-only">Retirer cette photo</span>
                        </label>
                    @endforeach

                    <template x-for="(photo, i) in photos" :key="photo.url">
                        <div class="relative aspect-square rounded-xl overflow-hidden bg-surface-container-low">
                            <img :src="photo.url" alt="" class="w-full h-full object-cover" />
                            <button type="button" @click="removePhoto(i)" class="absolute top-1.5 right-1.5 w-7 h-7 rounded-full bg-white/95 text-on-surface flex items-center justify-center shadow" aria-label="Retirer la photo">
                                <x-icon name="close" class="text-[16px]" />
                            </button>
                            <span x-show="i === 0 && existingCount === 0" class="absolute bottom-1.5 left-1.5 rounded bg-black/60 text-white px-1.5 py-0.5 text-[10px] font-bold">Principale</span>
                        </div>
                    </template>

                    <label x-show="remaining() > 0" class="aspect-square rounded-xl border-2 border-dashed border-outline-variant hover:border-primary text-on-surface-variant hover:text-primary flex flex-col items-center justify-center gap-1 cursor-pointer transition-colors">
                        <template x-if="!busy">
                            <span class="flex flex-col items-center gap-1">
                                <x-icon name="camera" class="text-[26px]" />
                                <span class="text-[12px] font-semibold">Ajouter</span>
                            </span>
                        </template>
                        <template x-if="busy">
                            <x-icon name="progress_activity" class="text-[26px] animate-spin" />
                        </template>
                        <input type="file" accept="image/*" multiple class="sr-only" @change="addPhotos($event)" />
                    </label>
                </div>
                <input type="file" name="photos[]" multiple class="hidden" x-ref="photosInput" />
                @error('photos') <p class="field-error">{{ $message }}</p> @enderror
                @error('photos.*') <p class="field-error">{{ $message }}</p> @enderror

                <div class="grid sm:grid-cols-2 gap-4 border-t border-surface-variant pt-5">
                    <div>
                        <label for="city_id" class="field-label">Ville</label>
                        <select id="city_id" name="city_id" class="field" required x-model.number="cityId" @change="districtId = ''" data-message="Choisissez une ville." @error('city_id') aria-invalid="true" @enderror>
                            <option value="">Choisissez…</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" @selected($selectedCityId === $city->id)>{{ $city->name }}</option>
                            @endforeach
                        </select>
                        @error('city_id') <p class="field-error">{{ $message }}</p> @enderror
                    </div>
                    <div x-show="cityDistricts().length">
                        <label for="district_id" class="field-label">Quartier <span class="font-normal text-on-surface-variant">(facultatif)</span></label>
                        <select id="district_id" name="district_id" class="field" x-model="districtId" :disabled="!cityDistricts().length">
                            <option value="">—</option>
                            <template x-for="district in cityDistricts()" :key="district.id">
                                <option :value="district.id" x-text="district.name" :selected="String(district.id) === String(districtId)"></option>
                            </template>
                        </select>
                        @error('district_id') <p class="field-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label for="contact_phone" class="field-label">Téléphone de contact</label>
                        <div class="flex items-stretch rounded-xl border border-outline-variant bg-surface-container-lowest focus-within:border-primary focus-within:ring-3 focus-within:ring-primary/15 @error('contact_phone') !border-error @enderror">
                            <span class="flex items-center gap-1.5 px-3 border-r border-outline-variant text-[14px] font-semibold text-on-surface-variant">🇬🇦 +241</span>
                            <input id="contact_phone" name="contact_phone" type="tel" inputmode="tel" autocomplete="tel-national" required value="{{ $phone }}" placeholder="077 12 34 56" class="flex-1 min-w-0 bg-transparent border-0 px-3 py-2.5 text-[15px] focus:outline-none focus:ring-0" data-message="Indiquez un numéro de téléphone." />
                        </div>
                        @error('contact_phone') <p class="field-error">{{ $message }}</p> @enderror
                    </div>
                    <label class="flex items-start gap-3 rounded-xl bg-surface-container-low p-3 cursor-pointer sm:self-end">
                        <input type="checkbox" name="accepts_whatsapp" value="1" @checked(old('accepts_whatsapp', $listing->accepts_whatsapp ?? true)) class="mt-0.5 w-5 h-5 rounded accent-[var(--color-primary)]" />
                        <span>
                            <span class="flex items-center gap-1.5 text-[14px] font-bold text-on-surface"><x-icon name="whatsapp" class="text-[16px] text-[#128c4b]" /> Joignable sur WhatsApp</span>
                            <span class="block text-[12px] text-on-surface-variant">Affiche le bouton « Contacter sur WhatsApp »</span>
                        </span>
                    </label>
                </div>

                <p class="text-[12px] text-on-surface-variant">
                    En publiant, vous acceptez nos <a href="{{ route('pages.terms') }}" target="_blank" class="font-semibold text-secondary underline">conditions d’utilisation</a>. Les annonces illégales, trompeuses ou en double sont refusées.
                </p>

                <div class="flex justify-between gap-2">
                    <button type="button" @click="goTo(2)" class="btn btn-outline"><x-icon name="arrow_back" class="text-[18px]" /> Retour</button>
                    <button type="submit" class="btn btn-accent" :disabled="submitting || busy">
                        <x-icon name="progress_activity" class="text-[18px] animate-spin" x-show="submitting" x-cloak />
                        {{ $editing ? 'Enregistrer les modifications' : 'Publier mon annonce' }}
                    </button>
                </div>
            </section>
        </form>
    </div>

    {{-- In <head> so the factory exists whenever Alpine boots, whatever the script order. --}}
    @push('head')
        <script>
            function listingForm(config) {
                return {
                    ...config,
                    _rootId: null,
                    photos: [],
                    busy: false,
                    submitting: false,
                    stepError: null,

                    get rootId() {
                        return this.rootFor(this.categoryId)?.id ?? this._rootId ?? null;
                    },
                    set rootId(value) {
                        this._rootId = value;
                    },
                    get root() {
                        return this.tree.find((r) => r.id === this.rootId) ?? null;
                    },
                    rootFor(id) {
                        return this.tree.find((r) => r.id === id || r.children.some((c) => c.id === id)) ?? null;
                    },
                    pickRoot(id) {
                        this.rootId = id;
                        const root = this.tree.find((r) => r.id === id);
                        // Leaf roots ("Autres") are selectable themselves; others need a sub-category.
                        this.categoryId = root.children.length ? (root.children.some((c) => c.id === this.categoryId) ? this.categoryId : null) : root.id;
                        this.stepError = null;
                    },
                    categoryLabel() {
                        const root = this.root;
                        if (!root) return '';
                        const child = root.children.find((c) => c.id === this.categoryId);
                        return (child ? root.name + ' › ' + child.name : root.name) + ' · modifier';
                    },
                    cityDistricts() {
                        return this.districts[this.cityId] ?? [];
                    },
                    formatPrice(value) {
                        const digits = value.replace(/\D/g, '').replace(/^0+(?=\d)/, '');
                        return digits.replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
                    },
                    remaining() {
                        return this.maxImages - this.existingCount - this.photos.length;
                    },
                    async addPhotos(event) {
                        const files = Array.from(event.target.files).slice(0, this.remaining());
                        event.target.value = '';
                        this.busy = true;
                        for (const file of files) {
                            const compressed = await window.compressImage(file);
                            this.photos.push({ file: compressed, url: URL.createObjectURL(compressed) });
                        }
                        this.busy = false;
                    },
                    removePhoto(index) {
                        URL.revokeObjectURL(this.photos[index].url);
                        this.photos.splice(index, 1);
                    },
                    stepIsValid(step) {
                        if (step === 1 && !this.categoryId) {
                            this.stepError = 1;
                            return false;
                        }
                        const section = this.$refs.form.querySelector(`[data-step="${step}"]`);
                        for (const field of section.querySelectorAll('input, select, textarea')) {
                            if (!field.disabled && !field.checkValidity()) {
                                this.step = step;
                                this.$nextTick(() => field.reportValidity());
                                return false;
                            }
                        }
                        this.stepError = null;
                        return true;
                    },
                    goTo(step) {
                        // Moving forward requires the steps in between to be complete.
                        for (let s = 1; s < step; s++) {
                            if (!this.stepIsValid(s)) {
                                this.step = s;
                                return;
                            }
                        }
                        this.step = step;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    },
                    next() {
                        this.goTo(this.step + 1);
                    },
                    submit() {
                        for (const step of [1, 2, 3]) {
                            if (!this.stepIsValid(step)) {
                                this.step = step;
                                return;
                            }
                        }
                        const transfer = new DataTransfer();
                        this.photos.forEach((photo) => transfer.items.add(photo.file));
                        this.$refs.photosInput.files = transfer.files;
                        this.submitting = true;
                        this.$refs.form.submit();
                    },
                };
            }
        </script>
    @endpush
</x-layouts.app>
