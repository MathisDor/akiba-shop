@php($editing = $coupon->exists)

<x-layouts.admin active="coupons" :title="$editing ? 'Modifier le coupon' : 'Nouveau coupon'">
    <div class="max-w-xl bg-surface-container-lowest rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.03)] p-4 sm:p-8">
        <form method="POST" action="{{ $editing ? route('admin.coupons.update', $coupon) : route('admin.coupons.store') }}" class="flex flex-col gap-6">
            @csrf
            @if($editing) @method('PUT') @endif

            <div>
                <label class="font-label-sm text-label-sm text-on-surface-variant">Code</label>
                <input type="text" name="code" value="{{ old('code', $coupon->code) }}" placeholder="AKIBA10" class="w-full bg-surface border-b border-outline-variant py-2 focus:border-primary focus:ring-0 font-body-md text-body-md uppercase" required />
                @error('code') <p class="text-error font-label-sm text-label-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="font-label-sm text-label-sm text-on-surface-variant">Type</label>
                    <select name="type" class="w-full bg-surface border-b border-outline-variant py-2 focus:border-primary focus:ring-0 font-body-md text-body-md">
                        <option value="percentage" @selected(old('type', $coupon->type) === 'percentage')>Pourcentage (%)</option>
                        <option value="fixed" @selected(old('type', $coupon->type) === 'fixed')>Montant fixe (FCFA)</option>
                    </select>
                </div>
                <div>
                    <label class="font-label-sm text-label-sm text-on-surface-variant">Valeur</label>
                    <input type="number" name="value" min="1" value="{{ old('value', $coupon->value) }}" class="w-full bg-surface border-b border-outline-variant py-2 focus:border-primary focus:ring-0 font-body-md text-body-md" required />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="font-label-sm text-label-sm text-on-surface-variant">Achat minimum (FCFA)</label>
                    <input type="number" name="min_purchase" min="0" value="{{ old('min_purchase', $coupon->min_purchase) }}" class="w-full bg-surface border-b border-outline-variant py-2 focus:border-primary focus:ring-0 font-body-md text-body-md" />
                </div>
                <div>
                    <label class="font-label-sm text-label-sm text-on-surface-variant">Limite d'utilisation</label>
                    <input type="number" name="usage_limit" min="1" value="{{ old('usage_limit', $coupon->usage_limit) }}" class="w-full bg-surface border-b border-outline-variant py-2 focus:border-primary focus:ring-0 font-body-md text-body-md" />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="font-label-sm text-label-sm text-on-surface-variant">Début</label>
                    <input type="date" name="starts_at" value="{{ old('starts_at', $coupon->starts_at?->format('Y-m-d')) }}" class="w-full bg-surface border-b border-outline-variant py-2 focus:border-primary focus:ring-0 font-body-md text-body-md" />
                </div>
                <div>
                    <label class="font-label-sm text-label-sm text-on-surface-variant">Fin</label>
                    <input type="date" name="ends_at" value="{{ old('ends_at', $coupon->ends_at?->format('Y-m-d')) }}" class="w-full bg-surface border-b border-outline-variant py-2 focus:border-primary focus:ring-0 font-body-md text-body-md" />
                </div>
            </div>

            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $coupon->is_active ?? true)) class="rounded-sm" />
                <span class="font-body-md text-body-md text-on-surface-variant">Coupon actif</span>
            </label>

            <div class="flex flex-col sm:flex-row gap-3">
                <button type="submit" class="w-full sm:w-auto bg-primary text-on-primary px-8 py-3 rounded-full font-label-sm text-label-sm uppercase tracking-widest text-center">Enregistrer</button>
                <a href="{{ route('admin.coupons.index') }}" class="w-full sm:w-auto px-8 py-3 rounded-full border border-outline text-on-surface-variant font-label-sm text-label-sm uppercase tracking-widest text-center">Annuler</a>
            </div>
        </form>
    </div>
</x-layouts.admin>
