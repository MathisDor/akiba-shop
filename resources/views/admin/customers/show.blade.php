<x-layouts.admin active="customers" :title="$customer->name">
    <a href="{{ route('admin.customers.index') }}" class="font-label-sm text-label-sm text-on-surface-variant hover:text-primary flex items-center gap-1 mb-6">
        <span class="material-symbols-outlined text-[18px]">arrow_back</span> Retour aux clients
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-surface-container-lowest rounded-xl p-4 sm:p-6 shadow-[0_4px_20px_rgba(0,0,0,0.03)]">
            <h2 class="font-headline-md text-headline-md text-on-surface mb-3">Profil</h2>
            <p class="font-body-md text-body-md text-on-surface">{{ $customer->name }}</p>
            <p class="font-body-md text-body-md text-on-surface-variant break-words">{{ $customer->email }}</p>
            <p class="font-body-md text-body-md text-on-surface-variant">{{ $customer->phone }}</p>
            <p class="font-label-sm text-label-sm text-on-surface-variant mt-3">Client depuis le {{ $customer->created_at->format('d/m/Y') }}</p>

            <h3 class="font-label-sm text-label-sm uppercase text-on-surface-variant mt-6 mb-2">Adresses</h3>
            @forelse($customer->addresses as $address)
                <p class="font-body-md text-body-md text-on-surface-variant">{{ $address->label }}: {{ $address->detailed_address }}, {{ $address->city }}</p>
            @empty
                <p class="font-body-md text-body-md text-on-surface-variant">Aucune adresse enregistrée.</p>
            @endforelse
        </div>

        <div class="lg:col-span-2 bg-surface-container-lowest rounded-xl p-4 sm:p-6 shadow-[0_4px_20px_rgba(0,0,0,0.03)]">
            <h2 class="font-headline-md text-headline-md text-on-surface mb-4">Historique des commandes ({{ $customer->orders->count() }})</h2>
            <div class="divide-y divide-surface-variant">
                @forelse($customer->orders as $order)
                    <a href="{{ route('admin.orders.show', $order) }}" class="flex justify-between items-center gap-3 py-3 hover:bg-surface-container-low transition-colors -mx-2 px-2 rounded">
                        <div class="min-w-0">
                            <p class="font-body-md text-body-md text-on-surface truncate">#{{ $order->order_number }}</p>
                            <p class="font-label-sm text-label-sm text-on-surface-variant truncate">{{ $order->created_at->format('d/m/Y') }} — {{ $order->statusLabel() }}</p>
                        </div>
                        <p class="font-body-md text-body-md text-on-surface shrink-0 whitespace-nowrap">{{ number_format($order->total, 0, ',', ' ') }} FCFA</p>
                    </a>
                @empty
                    <p class="font-body-md text-body-md text-on-surface-variant py-4">Aucune commande pour ce client.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.admin>
