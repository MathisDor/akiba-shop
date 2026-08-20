@php($editing = $promotion->exists)
@php($selectedIds = old('products', $promotion->products->pluck('id')->all() ?? []))

<x-layouts.admin active="promotions" :title="$editing ? 'Modifier la promotion' : 'Nouvelle promotion'">
    <div class="max-w-2xl bg-surface-container-lowest rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.03)] p-4 sm:p-8">
        <form method="POST" action="{{ $editing ? route('admin.promotions.update', $promotion) : route('admin.promotions.store') }}" class="flex flex-col gap-6">
            @csrf
            @if($editing) @method('PUT') @endif

            <div>
                <label class="font-label-sm text-label-sm text-on-surface-variant">Nom de la campagne</label>
                <input type="text" name="name" value="{{ old('name', $promotion->name) }}" class="w-full bg-surface border-b border-outline-variant py-2 focus:border-primary focus:ring-0 font-body-md text-body-md" required />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="font-label-sm text-label-sm text-on-surface-variant">Type</label>
                    <select name="type" class="w-full bg-surface border-b border-outline-variant py-2 focus:border-primary focus:ring-0 font-body-md text-body-md">
                        <option value="percentage" @selected(old('type', $promotion->type) === 'percentage')>Pourcentage (%)</option>
                        <option value="fixed" @selected(old('type', $promotion->type) === 'fixed')>Montant fixe (FCFA)</option>
                    </select>
                </div>
                <div>
                    <label class="font-label-sm text-label-sm text-on-surface-variant">Valeur</label>
                    <input type="number" name="value" min="1" value="{{ old('value', $promotion->value) }}" class="w-full bg-surface border-b border-outline-variant py-2 focus:border-primary focus:ring-0 font-body-md text-body-md" required />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="font-label-sm text-label-sm text-on-surface-variant">Début</label>
                    <input type="date" name="starts_at" value="{{ old('starts_at', $promotion->starts_at?->format('Y-m-d')) }}" class="w-full bg-surface border-b border-outline-variant py-2 focus:border-primary focus:ring-0 font-body-md text-body-md" />
                </div>
                <div>
                    <label class="font-label-sm text-label-sm text-on-surface-variant">Fin</label>
                    <input type="date" name="ends_at" value="{{ old('ends_at', $promotion->ends_at?->format('Y-m-d')) }}" class="w-full bg-surface border-b border-outline-variant py-2 focus:border-primary focus:ring-0 font-body-md text-body-md" />
                </div>
            </div>

            <div>
                <label class="font-label-sm text-label-sm text-on-surface-variant">Achat minimum (optionnel, FCFA)</label>
                <input type="number" name="min_purchase" min="0" value="{{ old('min_purchase', $promotion->min_purchase) }}" class="w-full bg-surface border-b border-outline-variant py-2 focus:border-primary focus:ring-0 font-body-md text-body-md" />
            </div>

            <div>
                <label class="font-label-sm text-label-sm text-on-surface-variant mb-2 block">Produits concernés</label>
                <div class="max-h-64 overflow-y-auto border border-outline-variant rounded-lg p-4 flex flex-col gap-2">
                    @foreach($products as $product)
                        <label class="flex items-center gap-2 font-body-md text-body-md">
                            <input type="checkbox" name="products[]" value="{{ $product->id }}" @checked(in_array($product->id, $selectedIds)) class="rounded-sm" />
                            {{ $product->name }} <span class="text-on-surface-variant font-label-sm text-label-sm">({{ number_format($product->base_price, 0, ',', ' ') }} FCFA)</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $promotion->is_active ?? true)) class="rounded-sm" />
                <span class="font-body-md text-body-md text-on-surface-variant">Promotion active</span>
            </label>

            <div class="flex flex-col sm:flex-row gap-3">
                <button type="submit" class="w-full sm:w-auto bg-primary text-on-primary px-8 py-3 rounded-full font-label-sm text-label-sm uppercase tracking-widest text-center">Enregistrer</button>
                <a href="{{ route('admin.promotions.index') }}" class="w-full sm:w-auto px-8 py-3 rounded-full border border-outline text-on-surface-variant font-label-sm text-label-sm uppercase tracking-widest text-center">Annuler</a>
            </div>
        </form>
    </div>
</x-layouts.admin>
