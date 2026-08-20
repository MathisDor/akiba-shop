@php($editing = $category->exists)

<x-layouts.admin active="categories" :title="$editing ? 'Modifier la catégorie' : 'Nouvelle catégorie'">
    <div class="max-w-xl bg-surface-container-lowest rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.03)] p-5 md:p-8">
        <form method="POST" action="{{ $editing ? route('admin.categories.update', $category) : route('admin.categories.store') }}" class="flex flex-col gap-6">
            @csrf
            @if($editing) @method('PUT') @endif

            <div>
                <label class="font-label-sm text-label-sm text-on-surface-variant">Nom</label>
                <input type="text" name="name" value="{{ old('name', $category->name) }}" class="w-full bg-surface border-b border-outline-variant py-2 focus:border-primary focus:ring-0 font-body-md text-body-md" required />
                @error('name') <p class="text-error font-label-sm text-label-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="font-label-sm text-label-sm text-on-surface-variant">Genre</label>
                <select name="gender" class="w-full bg-surface border-b border-outline-variant py-2 focus:border-primary focus:ring-0 font-body-md text-body-md">
                    @foreach(['femme' => 'Femme', 'homme' => 'Homme', 'unisexe' => 'Unisexe'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('gender', $category->gender) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="font-label-sm text-label-sm text-on-surface-variant">Catégorie parente (optionnel)</label>
                <select name="parent_id" class="w-full bg-surface border-b border-outline-variant py-2 focus:border-primary focus:ring-0 font-body-md text-body-md">
                    <option value="">Aucune</option>
                    @foreach(\App\Models\Category::where('id', '!=', $category->id)->orderBy('name')->get() as $parent)
                        <option value="{{ $parent->id }}" @selected(old('parent_id', $category->parent_id) == $parent->id)>{{ $parent->name }} ({{ $parent->gender }})</option>
                    @endforeach
                </select>
            </div>

            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->is_active ?? true)) class="rounded-sm" />
                <span class="font-body-md text-body-md text-on-surface-variant">Catégorie active</span>
            </label>

            <div class="flex gap-3">
                <button type="submit" class="bg-primary text-on-primary px-8 py-3 rounded-full font-label-sm text-label-sm uppercase tracking-widest">Enregistrer</button>
                <a href="{{ route('admin.categories.index') }}" class="px-8 py-3 rounded-full border border-outline text-on-surface-variant font-label-sm text-label-sm uppercase tracking-widest">Annuler</a>
            </div>
        </form>
    </div>
</x-layouts.admin>
