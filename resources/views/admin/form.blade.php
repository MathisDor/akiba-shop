@php($editing = $collection->exists)

<x-layouts.admin active="collections" :title="$editing ? 'Modifier la collection' : 'Nouvelle collection'">
    <div class="max-w-xl bg-surface-container-lowest rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.03)] p-4 sm:p-8">
        <form method="POST" action="{{ $editing ? route('admin.collections.update', $collection) : route('admin.collections.store') }}" class="flex flex-col gap-6">
            @csrf
            @if($editing) @method('PUT') @endif

            <div>
                <label class="font-label-sm text-label-sm text-on-surface-variant">Nom</label>
                <input type="text" name="name" value="{{ old('name', $collection->name) }}" class="w-full bg-surface border-b border-outline-variant py-2 focus:border-primary focus:ring-0 font-body-md text-body-md" required />
                @error('name') <p class="text-error font-label-sm text-label-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="font-label-sm text-label-sm text-on-surface-variant">Description</label>
                <textarea name="description" rows="3" class="w-full bg-surface border-b border-outline-variant py-2 focus:border-primary focus:ring-0 font-body-md text-body-md resize-none">{{ old('description', $collection->description) }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="font-label-sm text-label-sm text-on-surface-variant">Début</label>
                    <input type="date" name="starts_at" value="{{ old('starts_at', $collection->starts_at?->format('Y-m-d')) }}" class="w-full bg-surface border-b border-outline-variant py-2 focus:border-primary focus:ring-0 font-body-md text-body-md" />
                </div>
                <div>
                    <label class="font-label-sm text-label-sm text-on-surface-variant">Fin</label>
                    <input type="date" name="ends_at" value="{{ old('ends_at', $collection->ends_at?->format('Y-m-d')) }}" class="w-full bg-surface border-b border-outline-variant py-2 focus:border-primary focus:ring-0 font-body-md text-body-md" />
                </div>
            </div>

            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $collection->is_active ?? true)) class="rounded-sm" />
                <span class="font-body-md text-body-md text-on-surface-variant">Collection active</span>
            </label>

            <div class="flex flex-col sm:flex-row gap-3">
                <button type="submit" class="w-full sm:w-auto bg-primary text-on-primary px-8 py-3 rounded-full font-label-sm text-label-sm uppercase tracking-widest text-center">Enregistrer</button>
                <a href="{{ route('admin.collections.index') }}" class="w-full sm:w-auto px-8 py-3 rounded-full border border-outline text-on-surface-variant font-label-sm text-label-sm uppercase tracking-widest text-center">Annuler</a>
            </div>
        </form>
    </div>
</x-layouts.admin>
