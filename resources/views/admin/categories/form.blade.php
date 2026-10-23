@php($editing = $category->exists)

<x-layouts.admin active="categories" :title="$editing ? 'Modifier la catégorie' : 'Nouvelle catégorie'">
    <div class="max-w-xl card p-5 md:p-8">
        <form method="POST" action="{{ $editing ? route('admin.categories.update', $category) : route('admin.categories.store') }}" class="flex flex-col gap-5">
            @csrf
            @if($editing) @method('PUT') @endif

            <div>
                <label for="name" class="field-label">Nom</label>
                <input id="name" type="text" name="name" value="{{ old('name', $category->name) }}" class="field" required />
                @error('name') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="parent_id" class="field-label">Catégorie parente</label>
                <select id="parent_id" name="parent_id" class="field">
                    <option value="">Aucune (catégorie principale)</option>
                    @foreach($parents as $parent)
                        <option value="{{ $parent->id }}" @selected((int) old('parent_id', $category->parent_id) === $parent->id)>{{ $parent->name }}</option>
                    @endforeach
                </select>
                @error('parent_id') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="icon" class="field-label">Icône</label>
                    <input id="icon" type="text" name="icon" value="{{ old('icon', $category->icon) }}" placeholder="car, house, smartphone…" class="field" />
                </div>
                <div>
                    <label for="sort_order" class="field-label">Ordre</label>
                    <input id="sort_order" type="number" min="0" name="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}" class="field" />
                </div>
            </div>

            <label class="flex items-center gap-2 text-[14px]">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->is_active ?? true)) class="w-4 h-4 rounded accent-[var(--color-primary)]" />
                Catégorie active (visible sur le site)
            </label>

            @if($editing)
                <p class="text-[12px] text-on-surface-variant">Adresse : /categories/{{ $category->slug }} (inchangée pour ne pas casser les liens).</p>
            @endif

            <div class="flex flex-wrap gap-3 items-center">
                <button type="submit" class="btn btn-primary">Enregistrer</button>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline">Annuler</a>
            </div>
        </form>

        @if($editing)
            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Supprimer cette catégorie ?')" class="mt-6 pt-4 border-t border-surface-variant">
                @csrf @method('DELETE')
                <button class="text-[13px] font-semibold text-error hover:underline">Supprimer la catégorie</button>
            </form>
        @endif
    </div>
</x-layouts.admin>
