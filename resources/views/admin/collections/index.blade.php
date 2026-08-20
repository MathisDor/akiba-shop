<x-layouts.admin active="collections" title="Collections">
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-6">
        <p class="font-body-md text-body-md text-on-surface-variant">{{ $collections->count() }} collections</p>
        <a href="{{ route('admin.collections.create') }}" class="bg-primary text-on-primary px-6 py-3 rounded-full font-label-sm text-label-sm uppercase hover:opacity-90 transition-opacity flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">add</span> Nouvelle collection
        </a>
    </div>

    @session('status')
        <div class="mb-6 rounded-lg bg-primary-container/10 text-primary px-4 py-3 font-body-md text-body-md">{{ $value }}</div>
    @endsession

    {{-- Mobile: card list --}}
    <div class="md:hidden flex flex-col gap-3">
        @forelse($collections as $collection)
            <div class="bg-surface-container-lowest rounded-xl p-4 shadow-[0_4px_20px_rgba(0,0,0,0.03)]">
                <div class="flex items-start justify-between gap-2">
                    <p class="font-body-md text-body-md text-on-surface truncate">{{ $collection->name }}</p>
                    <span class="px-2 py-0.5 rounded-full font-label-sm text-[10px] shrink-0 {{ $collection->is_active ? 'bg-primary/10 text-primary' : 'bg-surface-variant text-on-surface-variant' }}">
                        {{ $collection->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <p class="font-label-sm text-[11px] text-on-surface-variant mt-1">
                    {{ $collection->products_count }} produit(s) · {{ $collection->starts_at?->format('d/m/Y') ?? '—' }} → {{ $collection->ends_at?->format('d/m/Y') ?? '—' }}
                </p>
                <div class="flex items-center gap-4 mt-3 pt-3 border-t border-surface-variant">
                    <a href="{{ route('admin.collections.edit', $collection) }}" class="text-primary font-label-sm text-label-sm">Modifier</a>
                    <form method="POST" action="{{ route('admin.collections.destroy', $collection) }}" onsubmit="return confirm('Supprimer cette collection ?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-error font-label-sm text-label-sm">Supprimer</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="font-body-md text-body-md text-on-surface-variant text-center py-8">Aucune collection.</p>
        @endforelse
    </div>

    {{-- Desktop: table --}}
    <div class="hidden md:block bg-surface-container-lowest rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.03)] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
            <thead class="bg-surface-container-low">
                <tr class="font-label-sm text-label-sm uppercase text-on-surface-variant">
                    <th class="px-4 py-3 md:px-6 md:py-4">Nom</th>
                    <th class="px-4 py-3 md:px-6 md:py-4">Produits</th>
                    <th class="px-4 py-3 md:px-6 md:py-4">Période</th>
                    <th class="px-4 py-3 md:px-6 md:py-4">Statut</th>
                    <th class="px-4 py-3 md:px-6 md:py-4"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-variant">
                @foreach($collections as $collection)
                    <tr class="font-body-md text-body-md text-on-surface">
                        <td class="px-4 py-3 md:px-6 md:py-4">{{ $collection->name }}</td>
                        <td class="px-4 py-3 md:px-6 md:py-4">{{ $collection->products_count }}</td>
                        <td class="px-4 py-3 md:px-6 md:py-4 font-label-sm text-label-sm text-on-surface-variant">
                            {{ $collection->starts_at?->format('d/m/Y') ?? '—' }} → {{ $collection->ends_at?->format('d/m/Y') ?? '—' }}
                        </td>
                        <td class="px-4 py-3 md:px-6 md:py-4">
                            <span class="px-3 py-1 rounded-full font-label-sm text-[11px] {{ $collection->is_active ? 'bg-primary/10 text-primary' : 'bg-surface-variant text-on-surface-variant' }}">
                                {{ $collection->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 md:px-6 md:py-4 text-right">
                            <a href="{{ route('admin.collections.edit', $collection) }}" class="text-primary hover:underline font-label-sm text-label-sm">Modifier</a>
                            <form method="POST" action="{{ route('admin.collections.destroy', $collection) }}" class="inline" onsubmit="return confirm('Supprimer cette collection ?')">
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
