<x-layouts.admin active="orders" title="Commandes">
    <form method="GET" class="flex flex-col sm:flex-row flex-wrap gap-3 mb-6">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher (n° commande, client)" class="flex-1 sm:min-w-[240px] bg-surface-container-lowest border border-outline-variant rounded-full px-4 py-2 font-body-md text-body-md focus:border-primary focus:ring-0" />
        <select name="status" onchange="this.form.submit()" class="w-full sm:w-auto bg-surface-container-lowest border border-outline-variant rounded-full px-4 py-2 font-body-md text-body-md">
            <option value="">Tous les statuts</option>
            @foreach($statuses as $status)
                <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>
        <button type="submit" class="w-full sm:w-auto bg-primary text-on-primary px-6 py-2 rounded-full font-label-sm text-label-sm uppercase">Filtrer</button>
    </form>

    @session('status')
        <div class="mb-6 rounded-lg bg-primary-container/10 text-primary px-4 py-3 font-body-md text-body-md">{{ $value }}</div>
    @endsession

    {{-- Mobile: card list --}}
    <div class="md:hidden flex flex-col gap-3">
        @forelse($orders as $order)
            <a href="{{ route('admin.orders.show', $order) }}" class="block bg-surface-container-lowest rounded-xl p-4 shadow-[0_4px_20px_rgba(0,0,0,0.03)]">
                <div class="flex items-start justify-between gap-2">
                    <p class="font-body-md text-body-md text-on-surface font-bold">#{{ $order->order_number }}</p>
                    <span class="px-2 py-0.5 rounded-full font-label-sm text-[10px] shrink-0 bg-primary/10 text-primary">{{ $order->statusLabel() }}</span>
                </div>
                <p class="font-label-sm text-[11px] text-on-surface-variant mt-1 truncate">{{ $order->user->name }} · {{ $order->created_at->format('d/m/Y H:i') }}</p>
                <p class="font-body-md text-body-md text-on-surface font-bold mt-1.5">{{ number_format($order->total, 0, ',', ' ') }} FCFA</p>
            </a>
        @empty
            <p class="font-body-md text-body-md text-on-surface-variant text-center py-8">Aucune commande.</p>
        @endforelse
    </div>

    {{-- Desktop: table --}}
    <div class="hidden md:block bg-surface-container-lowest rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.03)] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
            <thead class="bg-surface-container-low">
                <tr class="font-label-sm text-label-sm uppercase text-on-surface-variant">
                    <th class="px-4 py-3 md:px-6 md:py-4">Commande</th>
                    <th class="px-4 py-3 md:px-6 md:py-4">Client</th>
                    <th class="px-4 py-3 md:px-6 md:py-4">Date</th>
                    <th class="px-4 py-3 md:px-6 md:py-4">Statut</th>
                    <th class="px-4 py-3 md:px-6 md:py-4">Total</th>
                    <th class="px-4 py-3 md:px-6 md:py-4"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-variant">
                @forelse($orders as $order)
                    <tr class="font-body-md text-body-md text-on-surface">
                        <td class="px-4 py-3 md:px-6 md:py-4 font-bold">#{{ $order->order_number }}</td>
                        <td class="px-4 py-3 md:px-6 md:py-4">{{ $order->user->name }}</td>
                        <td class="px-4 py-3 md:px-6 md:py-4 font-label-sm text-label-sm text-on-surface-variant">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 md:px-6 md:py-4">
                            <span class="px-3 py-1 rounded-full font-label-sm text-[11px] bg-primary/10 text-primary">{{ $order->statusLabel() }}</span>
                        </td>
                        <td class="px-4 py-3 md:px-6 md:py-4">{{ number_format($order->total, 0, ',', ' ') }} FCFA</td>
                        <td class="px-4 py-3 md:px-6 md:py-4 text-right">
                            <a href="{{ route('admin.orders.show', $order) }}" class="text-primary hover:underline font-label-sm text-label-sm">Voir</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-8 text-center font-body-md text-body-md text-on-surface-variant">Aucune commande.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    <div class="mt-6">{{ $orders->onEachSide(1)->links('pagination.akiba') }}</div>
</x-layouts.admin>
