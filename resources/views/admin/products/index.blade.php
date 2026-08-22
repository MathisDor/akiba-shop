<x-layouts.admin active="products" title="Produits">
    <div x-data="{ selected: [], pageIds: {{ $products->pluck('id')->toJson() }} }">
        <div class="flex flex-wrap justify-between items-center gap-4 mb-6">
            <form method="GET" class="flex-1 min-w-[240px]">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher un produit (nom, SKU)" class="w-full max-w-md bg-surface-container-lowest border border-outline-variant rounded-full px-4 py-2 font-body-md text-body-md focus:border-primary focus:ring-0" />
            </form>
            <a href="{{ route('admin.products.create') }}" class="bg-primary text-on-primary px-6 py-3 rounded-full font-label-sm text-label-sm uppercase hover:opacity-90 transition-opacity flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">add</span> Ajouter un produit
            </a>
        </div>

        @session('status')
            <div class="mb-6 rounded-lg bg-primary-container/10 text-primary px-4 py-3 font-body-md text-body-md">{{ $value }}</div>
        @endsession

        {{-- Bulk actions bar --}}
        <div x-show="selected.length > 0" x-cloak x-transition class="mb-4 flex items-center justify-between gap-4 bg-error-container/10 border border-error/20 rounded-xl px-4 py-3">
            <span class="font-body-md text-body-md text-on-surface" x-text="selected.length + ' produit(s) sélectionné(s)'"></span>
            <div class="flex items-center gap-4">
                <button type="button" class="font-label-sm text-label-sm text-on-surface-variant hover:text-on-surface" @click="selected = []">Annuler</button>
                <button type="button" class="bg-error text-on-error px-4 py-2 rounded-full font-label-sm text-label-sm uppercase hover:opacity-90 transition-opacity" @click="if (confirm('Supprimer les ' + selected.length + ' produit(s) sélectionné(s) ?')) $refs.bulkDeleteForm.submit()">
                    Supprimer la sélection
                </button>
            </div>
        </div>

        <form x-ref="bulkDeleteForm" method="POST" action="{{ route('admin.products.bulk-destroy') }}" class="hidden">
            @csrf
            @method('DELETE')
            <template x-for="id in selected" :key="id">
                <input type="hidden" name="ids[]" :value="id" />
            </template>
        </form>

        {{-- Mobile: card list --}}
        <div class="md:hidden flex flex-col gap-3">
            @foreach($products as $product)
                <div class="bg-surface-container-lowest rounded-xl p-4 shadow-[0_4px_20px_rgba(0,0,0,0.03)] flex gap-3">
                    <input type="checkbox" value="{{ $product->id }}" x-model.number="selected" class="mt-1 w-4 h-4 rounded border-outline-variant text-primary focus:ring-primary flex-shrink-0" />
                    <div class="w-14 h-16 rounded overflow-hidden bg-surface-variant flex-shrink-0">
                    @if($product->images->first())
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($product->images->first()->path) }}" class="w-full h-full object-cover" alt="{{ $product->name }}" />
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2">
                        <p class="font-body-md text-body-md text-on-surface truncate">{{ $product->name }}</p>
                        <span class="px-2 py-0.5 rounded-full font-label-sm text-[10px] shrink-0 {{ $product->is_active ? 'bg-primary/10 text-primary' : 'bg-surface-variant text-on-surface-variant' }}">
                            {{ $product->is_active ? 'Actif' : 'Inactif' }}
                        </span>
                    </div>
                    <p class="font-label-sm text-[11px] text-on-surface-variant truncate">{{ $product->sku }} · {{ $product->category->name }}</p>
                    <div class="flex items-center gap-3 mt-1.5">
                        <span class="font-body-md text-body-md text-on-surface font-bold">{{ number_format($product->base_price, 0, ',', ' ') }} FCFA</span>
                        <span class="font-label-sm text-label-sm {{ $product->stock <= 5 ? 'text-error font-bold' : 'text-on-surface-variant' }}">Stock: {{ $product->stock ?? 0 }}</span>
                    </div>
                    <div class="flex items-center gap-4 mt-3 pt-3 border-t border-surface-variant">
                        <a href="{{ route('products.show', $product) }}" target="_blank" rel="noopener noreferrer" class="text-on-surface-variant hover:text-primary font-label-sm text-label-sm">Voir</a>
                        <a href="{{ route('admin.products.edit', $product) }}" class="text-primary font-label-sm text-label-sm">Modifier</a>
                        <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Supprimer ce produit ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-error font-label-sm text-label-sm">Supprimer</button>
                        </form>
                    </div>
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
                        <th class="px-6 py-4 w-10">
                            <input type="checkbox" class="w-4 h-4 rounded border-outline-variant text-primary focus:ring-primary" :checked="pageIds.length > 0 && pageIds.every(id => selected.includes(id))" @change="selected = $event.target.checked ? [...new Set([...selected, ...pageIds])] : selected.filter(id => !pageIds.includes(id))" />
                        </th>
                        <th class="px-6 py-4">Produit</th>
                        <th class="px-6 py-4">SKU</th>
                        <th class="px-6 py-4">Catégorie</th>
                        <th class="px-6 py-4">Prix</th>
                        <th class="px-6 py-4">Stock</th>
                        <th class="px-6 py-4">Statut</th>
                        <th class="px-6 py-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-variant">
                    @foreach($products as $product)
                        <tr class="font-body-md text-body-md text-on-surface">
                            <td class="px-6 py-4">
                                <input type="checkbox" value="{{ $product->id }}" x-model.number="selected" class="w-4 h-4 rounded border-outline-variant text-primary focus:ring-primary" />
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-12 rounded overflow-hidden bg-surface-variant flex-shrink-0">
                                        @if($product->images->first())
                                            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($product->images->first()->path) }}" class="w-full h-full object-cover" alt="{{ $product->name }}" />
                                        @endif
                                    </div>
                                    {{ $product->name }}
                                </div>
                            </td>
                            <td class="px-6 py-4 font-label-sm text-label-sm">{{ $product->sku }}</td>
                            <td class="px-6 py-4">{{ $product->category->name }}</td>
                            <td class="px-6 py-4">{{ number_format($product->base_price, 0, ',', ' ') }} FCFA</td>
                            <td class="px-6 py-4">
                                <span class="{{ $product->stock <= 5 ? 'text-error font-bold' : '' }}">{{ $product->stock ?? 0 }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full font-label-sm text-[11px] {{ $product->is_active ? 'bg-primary/10 text-primary' : 'bg-surface-variant text-on-surface-variant' }}">
                                    {{ $product->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <a href="{{ route('products.show', $product) }}" target="_blank" rel="noopener noreferrer" class="text-on-surface-variant hover:text-primary hover:underline font-label-sm text-label-sm">Voir</a>
                                <a href="{{ route('admin.products.edit', $product) }}" class="text-primary hover:underline font-label-sm text-label-sm ml-4">Modifier</a>
                                <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="inline" onsubmit="return confirm('Supprimer ce produit ?')">
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

        <div class="mt-6">{{ $products->onEachSide(1)->links('pagination.akiba') }}</div>
    </div>
</x-layouts.admin>
