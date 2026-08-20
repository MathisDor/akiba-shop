<x-layouts.admin active="customers" title="Clients">
    <form method="GET" class="mb-6">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher un client (nom, email)" class="w-full max-w-md bg-surface-container-lowest border border-outline-variant rounded-full px-4 py-2 font-body-md text-body-md focus:border-primary focus:ring-0" />
    </form>

    {{-- Mobile: card list --}}
    <div class="md:hidden flex flex-col gap-3">
        @forelse($customers as $customer)
            <a href="{{ route('admin.customers.show', $customer) }}" class="block bg-surface-container-lowest rounded-xl p-4 shadow-[0_4px_20px_rgba(0,0,0,0.03)]">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <p class="font-body-md text-body-md text-on-surface truncate">{{ $customer->name }}</p>
                        <p class="font-label-sm text-[11px] text-on-surface-variant truncate">{{ $customer->email }}</p>
                    </div>
                    <span class="font-label-sm text-label-sm text-on-surface-variant shrink-0">{{ $customer->phone }}</span>
                </div>
                <div class="flex items-center gap-3 mt-2 pt-2 border-t border-surface-variant font-label-sm text-label-sm text-on-surface-variant">
                    <span>{{ $customer->orders_count }} commande(s)</span>
                    <span class="font-bold text-on-surface">{{ number_format($customer->total_spent ?? 0, 0, ',', ' ') }} FCFA</span>
                    <span class="ml-auto">{{ $customer->created_at->format('d/m/Y') }}</span>
                </div>
            </a>
        @empty
            <p class="font-body-md text-body-md text-on-surface-variant text-center py-8">Aucun client.</p>
        @endforelse
    </div>

    {{-- Desktop: table --}}
    <div class="hidden md:block bg-surface-container-lowest rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.03)] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
            <thead class="bg-surface-container-low">
                <tr class="font-label-sm text-label-sm uppercase text-on-surface-variant">
                    <th class="px-4 py-3 md:px-6 md:py-4">Client</th>
                    <th class="px-4 py-3 md:px-6 md:py-4">Téléphone</th>
                    <th class="px-4 py-3 md:px-6 md:py-4">Commandes</th>
                    <th class="px-4 py-3 md:px-6 md:py-4">Total dépensé</th>
                    <th class="px-4 py-3 md:px-6 md:py-4">Inscrit le</th>
                    <th class="px-4 py-3 md:px-6 md:py-4"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-variant">
                @foreach($customers as $customer)
                    <tr class="font-body-md text-body-md text-on-surface">
                        <td class="px-4 py-3 md:px-6 md:py-4">
                            <p>{{ $customer->name }}</p>
                            <p class="font-label-sm text-label-sm text-on-surface-variant">{{ $customer->email }}</p>
                        </td>
                        <td class="px-4 py-3 md:px-6 md:py-4">{{ $customer->phone }}</td>
                        <td class="px-4 py-3 md:px-6 md:py-4">{{ $customer->orders_count }}</td>
                        <td class="px-4 py-3 md:px-6 md:py-4">{{ number_format($customer->total_spent ?? 0, 0, ',', ' ') }} FCFA</td>
                        <td class="px-4 py-3 md:px-6 md:py-4 font-label-sm text-label-sm text-on-surface-variant">{{ $customer->created_at->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 md:px-6 md:py-4 text-right">
                            <a href="{{ route('admin.customers.show', $customer) }}" class="text-primary hover:underline font-label-sm text-label-sm">Voir</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>

    <div class="mt-6">{{ $customers->onEachSide(1)->links('pagination.akiba') }}</div>
</x-layouts.admin>
