@php($editing = $deliveryZone->exists)

<x-layouts.admin active="delivery" :title="$editing ? 'Modifier la zone' : 'Nouvelle zone'">
    <div class="max-w-xl bg-surface-container-lowest rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.03)] p-4 sm:p-8">
        <form method="POST" action="{{ $editing ? route('admin.delivery-zones.update', $deliveryZone) : route('admin.delivery-zones.store') }}" class="flex flex-col gap-6">
            @csrf
            @if($editing) @method('PUT') @endif

            <div>
                <label class="font-label-sm text-label-sm text-on-surface-variant">Nom de la zone</label>
                <input type="text" name="zone_name" value="{{ old('zone_name', $deliveryZone->zone_name) }}" placeholder="Ex: Akanda" class="w-full bg-surface border-b border-outline-variant py-2 focus:border-primary focus:ring-0 font-body-md text-body-md" required />
                @error('zone_name') <p class="text-error font-label-sm text-label-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="font-label-sm text-label-sm text-on-surface-variant">Ville</label>
                    <input type="text" name="city" value="{{ old('city', $deliveryZone->city) }}" class="w-full bg-surface border-b border-outline-variant py-2 focus:border-primary focus:ring-0 font-body-md text-body-md" required />
                </div>
                <div>
                    <label class="font-label-sm text-label-sm text-on-surface-variant">Province</label>
                    <input type="text" name="province" value="{{ old('province', $deliveryZone->province) }}" class="w-full bg-surface border-b border-outline-variant py-2 focus:border-primary focus:ring-0 font-body-md text-body-md" required />
                </div>
            </div>

            <div>
                <label class="font-label-sm text-label-sm text-on-surface-variant">Tarif (FCFA)</label>
                <input type="number" name="fee" min="0" value="{{ old('fee', $deliveryZone->fee) }}" class="w-full bg-surface border-b border-outline-variant py-2 focus:border-primary focus:ring-0 font-body-md text-body-md" required />
                @error('fee') <p class="text-error font-label-sm text-label-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $deliveryZone->is_active ?? true)) class="rounded-sm" />
                <span class="font-body-md text-body-md text-on-surface-variant">Zone active</span>
            </label>

            <div class="flex flex-col sm:flex-row gap-3">
                <button type="submit" class="w-full sm:w-auto bg-primary text-on-primary px-8 py-3 rounded-full font-label-sm text-label-sm uppercase tracking-widest text-center">Enregistrer</button>
                <a href="{{ route('admin.delivery-zones.index') }}" class="w-full sm:w-auto px-8 py-3 rounded-full border border-outline text-on-surface-variant font-label-sm text-label-sm uppercase tracking-widest text-center">Annuler</a>
            </div>
        </form>
    </div>
</x-layouts.admin>
