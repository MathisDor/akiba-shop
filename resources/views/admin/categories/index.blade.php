<x-layouts.admin active="categories" title="Catégories">
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-6">
        <p class="font-body-md text-body-md text-on-surface-variant">{{ $categories->count() }} catégories</p>
        <a href="{{ route('admin.categories.create') }}" class="bg-primary text-on-primary px-6 py-3 rounded-full font-label-sm text-label-sm uppercase hover:opacity-90 transition-opacity flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">add</span> Nouvelle catégorie
        </a>
    </div>

    @session('status')
        <div class="mb-6 rounded-lg bg-primary-container/10 text-primary px-4 py-3 font-body-md text-body-md">{{ $value }}</div>
    @endsession
    @session('error')
        <div class="mb-6 rounded-lg bg-error-container text-on-error-container px-4 py-3 font-body-md text-body-md">{{ $value }}</div>
    @endsession

    {{-- Mobile: card list --}}
    <div class="md:hidden flex flex-col gap-3">
        @foreach($categories as $category)
            <div class="bg-surface-container-lowest rounded-xl p-4 shadow-[0_4px_20px_rgba(0,0,0,0.03)]">
                <div class="flex items-start justify-between gap-2">
                    <p class="font-body-md text-body-md text-on-surface">{{ $category->name }}</p>
                    <span class="px-2 py-0.5 rounded-full font-label-sm text-[10px] shrink-0 {{ $category->is_active ? 'bg-primary/10 text-primary' : 'bg-surface-variant text-on-surface-variant' }}">
                        {{ $category->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <p class="font-label-sm text-[11px] text-on-surface-variant mt-0.5">
                    <span class="capitalize">{{ $category->gender }}</span>
                    @if($category->parent) · {{ $category->parent->name }} @endif
                    · {{ $category->products_count }} produit{{ $category->products_count > 1 ? 's' : '' }}
                </p>
                <div class="flex items-center gap-4 mt-3 pt-3 border-t border-surface-variant">
                    <a href="{{ route('admin.categories.edit', $category) }}" class="text-primary font-label-sm text-label-sm">Modifier</a>
                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Supprimer cette catégorie ?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-error font-label-sm text-label-sm">Supprimer</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Desktop: table --}}
    <div class="hidden md:block bg-surface-container-lowest rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.03)] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-surface-container-low">
                    <tr class="font-label-sm text-label-sm uppercase text-on-surface-variant">
                        <th class="px-6 py-4">Nom</th>
                        <th class="px-6 py-4">Genre</th>
                        <th class="px-6 py-4">Parent</th>
                        <th class="px-6 py-4">Produits</th>
                        <th class="px-6 py-4">Statut</th>
                        <th class="px-6 py-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-variant">
                    @foreach($categories as $category)
                        <tr class="font-body-md text-body-md text-on-surface">
                            <td class="px-6 py-4">{{ $category->name }}</td>
                            <td class="px-6 py-4 capitalize">{{ $category->gender }}</td>
                            <td class="px-6 py-4">{{ $category->parent?->name ?? '—' }}</td>
                            <td class="px-6 py-4">{{ $category->products_count }}</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full font-label-sm text-[11px] {{ $category->is_active ? 'bg-primary/10 text-primary' : 'bg-surface-variant text-on-surface-variant' }}">
                                    {{ $category->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="text-primary hover:underline font-label-sm text-label-sm">Modifier</a>
                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="inline" onsubmit="return confirm('Supprimer cette catégorie ?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-error hover:underline font-label-sm text-label-sm ml-4">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
