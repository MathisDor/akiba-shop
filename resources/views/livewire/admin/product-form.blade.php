<div class="flex flex-col gap-6 max-w-4xl">
    @if($feedback)
        <div class="rounded-lg bg-error-container text-on-error-container px-4 py-3 font-body-md text-body-md">{{ $feedback }}</div>
    @endif
    @session('status')
        <div class="rounded-lg bg-primary-container/10 text-primary px-4 py-3 font-body-md text-body-md">{{ $value }}</div>
    @endsession

    @if($product)
        <div class="rounded-lg bg-surface-container-low px-4 py-3 flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-4 font-label-sm text-label-sm text-on-surface-variant">
            <span class="flex items-center gap-1">
                <span class="material-symbols-outlined text-[16px]">person</span>
                Ajouté par <span class="font-semibold text-on-surface">{{ $product->creator?->name ?? 'Inconnu' }}</span>
                le {{ $product->created_at->translatedFormat('d M Y à H:i') }}
            </span>
            @if($product->editor && $product->updated_at->ne($product->created_at))
                <span class="flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">edit</span>
                    Dernière modification par <span class="font-semibold text-on-surface">{{ $product->editor->name }}</span>
                    le {{ $product->updated_at->translatedFormat('d M Y à H:i') }}
                </span>
            @endif
        </div>
    @endif

    {{-- Stepper --}}
    <div class="overflow-x-auto hide-scrollbar -mx-4 px-4 sm:mx-0 sm:px-0">
        <div class="flex items-center min-w-max sm:min-w-0">
            @foreach([1 => 'Général', 2 => 'Prix', 3 => 'Images', 4 => 'Variantes', 5 => 'Récap'] as $n => $label)
                <button
                    type="button"
                    wire:click="goToStep({{ $n }})"
                    @disabled($n > $maxStepReached)
                    class="flex items-center gap-2 shrink-0 disabled:cursor-not-allowed"
                >
                    <span class="w-9 h-9 sm:w-8 sm:h-8 rounded-full flex items-center justify-center font-label-sm text-label-sm shrink-0 {{ $step === $n ? 'bg-primary text-on-primary' : ($n <= $maxStepReached ? 'bg-primary/10 text-primary' : 'bg-surface-container-highest text-on-surface-variant') }}">
                        {{ $n }}
                    </span>
                    <span class="hidden sm:block font-label-sm text-label-sm {{ $step === $n ? 'text-primary font-bold' : 'text-on-surface-variant' }}">{{ $label }}</span>
                </button>
                @unless($loop->last)
                    <span class="w-6 sm:flex-1 h-px bg-surface-variant mx-1 sm:mx-2 shrink-0 sm:shrink"></span>
                @endunless
            @endforeach
        </div>
    </div>

    <div class="flex flex-col gap-8">
        {{-- Step 1: Informations générales --}}
        @if($step === 1)
            <section class="bg-surface-container-lowest rounded-xl p-4 sm:p-6 shadow-[0_4px_20px_rgba(0,0,0,0.03)]">
                <h2 class="font-headline-md text-headline-md text-on-surface mb-6">Informations générales</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="font-label-sm text-label-sm text-on-surface-variant">Nom du produit</label>
                        <input wire:model="name" type="text" class="w-full bg-surface border-b border-outline-variant py-2 focus:border-primary focus:ring-0 font-body-md text-body-md" />
                        @error('name') <p class="text-error font-label-sm text-label-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="font-label-sm text-label-sm text-on-surface-variant">Catégorie</label>
                        <select wire:model="categoryId" class="w-full bg-surface border-b border-outline-variant py-2 focus:border-primary focus:ring-0 font-body-md text-body-md">
                            <option value="">Sélectionner...</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }} ({{ $category->gender }})</option>
                            @endforeach
                        </select>
                        @error('categoryId') <p class="text-error font-label-sm text-label-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="font-label-sm text-label-sm text-on-surface-variant">Collection (optionnel)</label>
                        <select wire:model="collectionId" class="w-full bg-surface border-b border-outline-variant py-2 focus:border-primary focus:ring-0 font-body-md text-body-md">
                            <option value="">Aucune</option>
                            @foreach($collections as $collection)
                                <option value="{{ $collection->id }}">{{ $collection->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="font-label-sm text-label-sm text-on-surface-variant">Genre</label>
                        <select wire:model="gender" class="w-full bg-surface border-b border-outline-variant py-2 focus:border-primary focus:ring-0 font-body-md text-body-md">
                            <option value="femme">Femme</option>
                            <option value="homme">Homme</option>
                            <option value="unisexe">Unisexe</option>
                        </select>
                    </div>
                    <div>
                        <label class="font-label-sm text-label-sm text-on-surface-variant">Marque</label>
                        <input wire:model="brand" type="text" class="w-full bg-surface border-b border-outline-variant py-2 focus:border-primary focus:ring-0 font-body-md text-body-md" />
                    </div>
                    <div class="md:col-span-2">
                        <label class="font-label-sm text-label-sm text-on-surface-variant">Description</label>
                        <textarea wire:model="description" rows="4" class="w-full bg-surface border-b border-outline-variant py-2 focus:border-primary focus:ring-0 font-body-md text-body-md resize-none"></textarea>
                    </div>
                </div>
            </section>
        @endif

        {{-- Step 2: Prix --}}
        @if($step === 2)
            <section class="bg-surface-container-lowest rounded-xl p-4 sm:p-6 shadow-[0_4px_20px_rgba(0,0,0,0.03)]">
                <h2 class="font-headline-md text-headline-md text-on-surface mb-6">Prix</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="font-label-sm text-label-sm text-on-surface-variant">Prix de base (FCFA)</label>
                        <input wire:model="basePrice" type="number" min="0" class="w-full bg-surface border-b border-outline-variant py-2 focus:border-primary focus:ring-0 font-body-md text-body-md" />
                        @error('basePrice') <p class="text-error font-label-sm text-label-sm mt-1">{{ $message }}</p> @enderror
                        <p class="font-label-sm text-[11px] text-on-surface-variant mt-1">Les promotions (prix barré) se gèrent depuis la page « Promotions ».</p>
                    </div>
                    <label class="flex items-center gap-2 mt-6">
                        <input wire:model="isActive" type="checkbox" class="rounded-sm" />
                        <span class="font-body-md text-body-md text-on-surface-variant">Produit actif (visible sur la boutique)</span>
                    </label>
                </div>
            </section>
        @endif

        {{-- Step 3: Images --}}
        @if($step === 3)
            <section class="bg-surface-container-lowest rounded-xl p-4 sm:p-6 shadow-[0_4px_20px_rgba(0,0,0,0.03)]">
                <h2 class="font-headline-md text-headline-md text-on-surface mb-2">Images</h2>
                <p class="font-label-sm text-label-sm text-on-surface-variant mb-6">La première image (ou celle marquée "Principale") s'affiche sur les listes produits. Vous pourrez assigner une photo par couleur à l'étape Variantes.</p>

                @if(count($existingImages) > 0)
                    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3 sm:gap-4 mb-6">
                        @foreach($existingImages as $i => $img)
                            <div class="relative group">
                                <div class="aspect-[3/4] rounded-lg overflow-hidden bg-surface-variant border-2 {{ $img['is_primary'] ? 'border-primary' : 'border-transparent' }}">
                                    <img src="{{ $img['url'] }}" class="w-full h-full object-cover" alt="" />
                                </div>
                                @if($img['is_primary'])
                                    <span class="absolute top-1 left-1 bg-primary text-on-primary text-[9px] px-2 py-0.5 rounded-full uppercase">Principale</span>
                                @endif
                                <div class="flex items-center justify-between mt-1">
                                    <button type="button" wire:click="moveImage({{ $i }}, -1)" class="p-1.5 -m-1.5 text-on-surface-variant hover:text-primary"><span class="material-symbols-outlined text-[16px]">chevron_left</span></button>
                                    <button type="button" wire:click="setPrimaryImage({{ $img['id'] }})" class="p-1.5 -m-1.5 text-[10px] uppercase text-on-surface-variant hover:text-primary">Définir</button>
                                    <button type="button" wire:click="moveImage({{ $i }}, 1)" class="p-1.5 -m-1.5 text-on-surface-variant hover:text-primary"><span class="material-symbols-outlined text-[16px]">chevron_right</span></button>
                                </div>
                                <button type="button" wire:click="removeExistingImage({{ $img['id'] }})" class="absolute top-1 right-1 w-7 h-7 rounded-full bg-black/60 text-white flex items-center justify-center opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity">
                                    <span class="material-symbols-outlined text-[14px]">close</span>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div>
                    <label class="flex flex-col items-center justify-center gap-2 border-2 border-dashed border-outline-variant rounded-xl py-8 px-4 cursor-pointer hover:border-primary active:border-primary transition-colors">
                        <span class="material-symbols-outlined text-primary text-[28px]">add_photo_alternate</span>
                        <span class="font-label-sm text-label-sm text-on-surface-variant text-center">Ajouter des images</span>
                        <input wire:model="newImages" type="file" multiple accept="image/*" class="hidden" />
                    </label>
                    <div wire:loading wire:target="newImages" class="font-label-sm text-label-sm text-primary mt-2">Envoi en cours…</div>
                    @error('newImages.*') <p class="text-error font-label-sm text-label-sm mt-1">{{ $message }}</p> @enderror

                    @if(count($newImages) > 0)
                        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3 sm:gap-4 mt-4">
                            @foreach($newImages as $file)
                                <img src="{{ $file->temporaryUrl() }}" class="w-full aspect-[3/4] object-cover rounded-lg" alt="" />
                            @endforeach
                        </div>
                    @endif
                </div>
            </section>
        @endif

        {{-- Step 4: Variantes --}}
        @if($step === 4)
            <section class="bg-surface-container-lowest rounded-xl p-4 sm:p-6 shadow-[0_4px_20px_rgba(0,0,0,0.03)]">
                <h2 class="font-headline-md text-headline-md text-on-surface mb-2">Variantes</h2>
                <p class="font-label-sm text-label-sm text-on-surface-variant mb-6">Sélectionnez les couleurs et tailles disponibles pour générer la matrice de stock.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <p class="font-label-sm text-label-sm text-on-surface-variant mb-2">Couleurs</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($colors as $color)
                                <label class="flex items-center gap-2 px-3 py-2 rounded-full border border-outline-variant cursor-pointer has-[:checked]:border-primary has-[:checked]:bg-primary/5">
                                    <input type="checkbox" wire:model.live="selectedColorIds" value="{{ $color->id }}" class="rounded-sm" />
                                    <span class="w-4 h-4 rounded-full border border-outline-variant" style="background-color: {{ $color->hex }}"></span>
                                    <span class="font-label-sm text-label-sm">{{ $color->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <p class="font-label-sm text-label-sm text-on-surface-variant mb-2">Tailles</p>
                        <label class="flex items-center gap-2 mb-3">
                            <input type="checkbox" wire:model.live="hasNoSizes" class="rounded-sm" />
                            <span class="font-label-sm text-label-sm">Article à taille unique (accessoires)</span>
                        </label>
                        @unless($hasNoSizes)
                            <div class="flex flex-wrap gap-2">
                                @foreach($sizes as $size)
                                    <label class="flex items-center gap-2 px-3 py-2 rounded-full border border-outline-variant cursor-pointer has-[:checked]:border-primary has-[:checked]:bg-primary/5">
                                        <input type="checkbox" wire:model.live="selectedSizeIds" value="{{ $size->id }}" class="rounded-sm" />
                                        <span class="font-label-sm text-label-sm">{{ $size->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        @endunless
                    </div>
                </div>

                @if(count($selectedColorIds) > 0)
                    <div class="mb-6">
                        <p class="font-label-sm text-label-sm text-on-surface-variant mb-3">Photos par couleur (optionnel — plusieurs possibles, affichées sur la fiche produit quand cette couleur est choisie)</p>
                        <div class="flex flex-col gap-4">
                            @foreach($selectedColorIds as $colorId)
                                @php($colorObj = $colors->firstWhere('id', (int) $colorId))
                                @php($selectedForColor = $colorImages[(int) $colorId] ?? [])
                                <div class="flex flex-col sm:flex-row sm:items-start gap-2 sm:gap-3">
                                    <span class="flex items-center gap-2 sm:w-28 shrink-0 sm:pt-3">
                                        <span class="w-4 h-4 rounded-full border border-outline-variant shrink-0" style="background-color: {{ $colorObj?->hex }}"></span>
                                        <span class="font-label-sm text-label-sm truncate">{{ $colorObj?->name }}</span>
                                        @if(count($selectedForColor) > 0)
                                            <span class="font-label-sm text-[10px] text-primary shrink-0">({{ count($selectedForColor) }})</span>
                                        @endif
                                    </span>
                                    <div class="flex gap-2 flex-wrap">
                                        @foreach($existingImages as $img)
                                            @php($isSelected = in_array('existing:'.$img['id'], $selectedForColor, true))
                                            <button type="button" wire:click="toggleColorImage({{ (int) $colorId }}, 'existing:{{ $img['id'] }}')" class="relative w-14 h-16 rounded-lg overflow-hidden border-2 shrink-0 {{ $isSelected ? 'border-primary' : 'border-transparent' }}">
                                                <img src="{{ $img['url'] }}" class="w-full h-full object-cover" alt="" />
                                                @if($isSelected)
                                                    <span class="absolute top-0.5 right-0.5 w-4 h-4 rounded-full bg-primary text-on-primary flex items-center justify-center">
                                                        <span class="material-symbols-outlined text-[12px]">check</span>
                                                    </span>
                                                @endif
                                            </button>
                                        @endforeach
                                        @foreach($newImages as $i => $file)
                                            @php($isSelected = in_array('new:'.$i, $selectedForColor, true))
                                            <button type="button" wire:click="toggleColorImage({{ (int) $colorId }}, 'new:{{ $i }}')" class="relative w-14 h-16 rounded-lg overflow-hidden border-2 shrink-0 {{ $isSelected ? 'border-primary' : 'border-transparent' }}">
                                                <img src="{{ $file->temporaryUrl() }}" class="w-full h-full object-cover" alt="" />
                                                @if($isSelected)
                                                    <span class="absolute top-0.5 right-0.5 w-4 h-4 rounded-full bg-primary text-on-primary flex items-center justify-center">
                                                        <span class="material-symbols-outlined text-[12px]">check</span>
                                                    </span>
                                                @endif
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if(count($variantMatrix) > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="font-label-sm text-label-sm uppercase text-on-surface-variant border-b border-surface-variant">
                                    <th class="py-2 pr-4">Couleur</th>
                                    <th class="py-2 pr-4">Taille</th>
                                    <th class="py-2 pr-4">SKU</th>
                                    <th class="py-2 pr-4">Stock</th>
                                    <th class="py-2 pr-4">Prix spécifique (optionnel)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($selectedColorIds as $colorId)
                                    @foreach(($hasNoSizes ? [null] : $selectedSizeIds) as $sizeId)
                                        @php($key = ($colorId ?? 'none').'_'.($sizeId ?? 'none'))
                                        @php($colorName = $colors->firstWhere('id', (int) $colorId)?->name)
                                        @php($sizeName = $sizeId ? $sizes->firstWhere('id', (int) $sizeId)?->name : null)
                                        <tr class="border-b border-surface-variant/50">
                                            <td class="py-2 pr-4 font-body-md text-body-md">{{ $colorName }}</td>
                                            <td class="py-2 pr-4 font-body-md text-body-md">{{ $sizeId ? $sizeName : 'Unique' }}</td>
                                            <td class="py-2 pr-4 font-body-md text-body-md text-on-surface-variant whitespace-nowrap">
                                                {{ $this->displaySku((int) $colorId, $sizeId ? (int) $sizeId : null, $colorName, $sizeName) }}
                                            </td>
                                            <td class="py-2 pr-4">
                                                <input type="number" min="0" wire:model="variantMatrix.{{ $key }}.stock" class="w-24 bg-surface border-b border-outline-variant py-1 focus:border-primary focus:ring-0 font-body-md text-body-md" />
                                            </td>
                                            <td class="py-2 pr-4">
                                                <input type="number" min="0" wire:model="variantMatrix.{{ $key }}.price_override" placeholder="{{ $basePrice }}" class="w-28 bg-surface border-b border-outline-variant py-1 focus:border-primary focus:ring-0 font-body-md text-body-md" />
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="font-body-md text-body-md text-on-surface-variant">Sélectionnez au moins une couleur et une taille pour générer les variantes.</p>
                @endif
            </section>
        @endif

        {{-- Step 5: Détails complémentaires & récapitulatif --}}
        @if($step === 5)
            <section class="bg-surface-container-lowest rounded-xl p-4 sm:p-6 shadow-[0_4px_20px_rgba(0,0,0,0.03)]">
                <h2 class="font-headline-md text-headline-md text-on-surface mb-6">Informations complémentaires</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="font-label-sm text-label-sm text-on-surface-variant">Matière</label>
                        <input wire:model="material" type="text" class="w-full bg-surface border-b border-outline-variant py-2 focus:border-primary focus:ring-0 font-body-md text-body-md" />
                    </div>
                    <div>
                        <label class="font-label-sm text-label-sm text-on-surface-variant">Coupe</label>
                        <input wire:model="fit" type="text" class="w-full bg-surface border-b border-outline-variant py-2 focus:border-primary focus:ring-0 font-body-md text-body-md" />
                    </div>
                    <div>
                        <label class="font-label-sm text-label-sm text-on-surface-variant">Saison</label>
                        <input wire:model="season" type="text" class="w-full bg-surface border-b border-outline-variant py-2 focus:border-primary focus:ring-0 font-body-md text-body-md" />
                    </div>
                    <div>
                        <label class="font-label-sm text-label-sm text-on-surface-variant">Instructions d'entretien</label>
                        <input wire:model="careInstructions" type="text" class="w-full bg-surface border-b border-outline-variant py-2 focus:border-primary focus:ring-0 font-body-md text-body-md" />
                    </div>
                </div>
            </section>

            <section class="bg-surface-container-lowest rounded-xl p-4 sm:p-6 shadow-[0_4px_20px_rgba(0,0,0,0.03)]">
                <h2 class="font-headline-md text-headline-md text-on-surface mb-6">Récapitulatif</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex items-start gap-3 p-4 rounded-lg bg-surface">
                        @php($thumb = collect($existingImages)->first())
                        @if($thumb)
                            <img src="{{ $thumb['url'] }}" class="w-14 h-16 object-cover rounded-lg shrink-0" alt="" />
                        @endif
                        <div>
                            <p class="font-label-lg text-label-lg text-on-surface">{{ $name ?: 'Sans nom' }}</p>
                            <p class="font-label-sm text-label-sm text-on-surface-variant">{{ $sku }} · {{ $categories->firstWhere('id', $categoryId)?->name ?? 'Sans catégorie' }}</p>
                        </div>
                    </div>
                    <div class="p-4 rounded-lg bg-surface">
                        <p class="font-label-sm text-label-sm text-on-surface-variant">Prix de base</p>
                        <p class="font-label-lg text-label-lg text-on-surface">{{ number_format($basePrice, 0, ',', ' ') }} FCFA</p>
                        <p class="font-label-sm text-[11px] text-on-surface-variant mt-1">{{ $isActive ? 'Produit actif' : 'Produit inactif' }}</p>
                    </div>
                    <div class="p-4 rounded-lg bg-surface">
                        <p class="font-label-sm text-label-sm text-on-surface-variant">Images</p>
                        <p class="font-label-lg text-label-lg text-on-surface">{{ count($existingImages) + count($newImages) }} photo(s)</p>
                    </div>
                    <div class="p-4 rounded-lg bg-surface">
                        <p class="font-label-sm text-label-sm text-on-surface-variant">Variantes</p>
                        <p class="font-label-lg text-label-lg text-on-surface">{{ count($variantMatrix) }} variante(s) · {{ collect($variantMatrix)->sum('stock') }} en stock</p>
                    </div>
                </div>
            </section>
        @endif

        {{-- Navigation --}}
        <div class="sticky sm:static bottom-0 -mx-4 sm:mx-0 px-4 sm:px-0 py-3 sm:py-0 bg-surface-container-low/95 sm:bg-transparent backdrop-blur-sm sm:backdrop-blur-none border-t sm:border-t-0 border-surface-variant flex flex-col sm:flex-row gap-3">
            @if($step < 5)
                <button type="button" wire:click="nextStep" class="w-full sm:w-auto order-1 bg-primary text-on-primary px-8 py-3 rounded-full font-label-sm text-label-sm uppercase tracking-widest text-center">Suivant</button>
            @else
                <button type="button" wire:click="save" wire:loading.attr="disabled" class="w-full sm:w-auto order-1 bg-primary text-on-primary px-8 py-3 rounded-full font-label-sm text-label-sm uppercase tracking-widest text-center disabled:opacity-60">
                    <span wire:loading.remove wire:target="save">Enregistrer le produit</span>
                    <span wire:loading wire:target="save">Enregistrement…</span>
                </button>
            @endif

            @if($step > 1)
                <button type="button" wire:click="prevStep" class="w-full sm:w-auto order-2 px-8 py-3 rounded-full border border-outline text-on-surface-variant font-label-sm text-label-sm uppercase tracking-widest text-center">Précédent</button>
            @endif

            <a href="{{ route('admin.products.index') }}" class="w-full sm:w-auto order-3 px-8 py-3 rounded-full border border-outline text-on-surface-variant font-label-sm text-label-sm uppercase tracking-widest text-center">Annuler</a>
        </div>
    </div>
</div>
