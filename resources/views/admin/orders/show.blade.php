<x-layouts.admin active="orders" :title="'Commande #'.$order->order_number">
    @session('status')
        <div class="mb-6 rounded-lg bg-primary-container/10 text-primary px-4 py-3 font-body-md text-body-md">{{ $value }}</div>
    @endsession

    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3 mb-6 print:hidden">
        <a href="{{ route('admin.orders.index') }}" class="font-label-sm text-label-sm text-on-surface-variant hover:text-primary flex items-center gap-1">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span> Retour aux commandes
        </a>
        <button onclick="window.print()" type="button" class="w-full sm:w-auto justify-center flex items-center gap-2 px-5 py-2 rounded-full border border-outline-variant font-label-sm text-label-sm uppercase text-on-surface-variant hover:text-primary hover:border-primary transition-colors">
            <span class="material-symbols-outlined text-[18px]">print</span> Imprimer / Facture
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 flex flex-col gap-6">
            <div class="bg-surface-container-lowest rounded-xl p-4 sm:p-6 shadow-[0_4px_20px_rgba(0,0,0,0.03)]">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-2 mb-4">
                    <div>
                        <h1 class="font-headline-md text-headline-md text-on-surface">Commande #{{ $order->order_number }}</h1>
                        <p class="font-label-sm text-label-sm text-on-surface-variant">{{ $order->created_at->format('d/m/Y à H:i') }}</p>
                    </div>
                    <span class="self-start px-3 py-1 rounded-full font-label-sm text-[11px] bg-primary/10 text-primary">{{ $order->statusLabel() }}</span>
                </div>

                <div class="divide-y divide-surface-variant">
                    @foreach($order->items as $item)
                        <div class="flex items-center gap-3 sm:gap-4 py-3">
                            @php($img = $item->variant?->product?->images->first())
                            <div class="w-12 h-14 sm:w-14 sm:h-16 rounded-md overflow-hidden bg-surface-variant flex-shrink-0">
                                @if($img)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($img->path) }}" class="w-full h-full object-cover" alt="{{ $item->product_name }}" />
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-body-md text-body-md text-on-surface truncate">{{ $item->product_name }}</p>
                                <p class="font-label-sm text-label-sm text-on-surface-variant truncate">
                                    {{ $item->color_name }} {{ $item->size_name ? '/ '.$item->size_name : '' }} · Qté {{ $item->quantity }}
                                </p>
                            </div>
                            <p class="font-body-md text-body-md text-on-surface shrink-0 whitespace-nowrap">{{ number_format($item->lineTotal(), 0, ',', ' ') }} FCFA</p>
                        </div>
                    @endforeach
                </div>

                <div class="flex flex-col gap-1 pt-4 mt-2 border-t border-surface-variant font-body-md text-body-md">
                    <div class="flex justify-between text-on-surface-variant"><span>Sous-total</span><span>{{ number_format($order->subtotal, 0, ',', ' ') }} FCFA</span></div>
                    @if($order->discount > 0)
                        <div class="flex justify-between text-primary"><span>Réduction{{ $order->coupon ? ' ('.$order->coupon->code.')' : '' }}</span><span>-{{ number_format($order->discount, 0, ',', ' ') }} FCFA</span></div>
                    @endif
                    <div class="flex justify-between text-on-surface-variant"><span>Livraison</span><span>{{ number_format($order->delivery_fee, 0, ',', ' ') }} FCFA</span></div>
                    <div class="flex justify-between font-bold text-on-surface border-t border-surface-variant pt-2 mt-1"><span>Total</span><span>{{ number_format($order->total, 0, ',', ' ') }} FCFA</span></div>
                </div>
            </div>

            <div class="bg-surface-container-lowest rounded-xl p-4 sm:p-6 shadow-[0_4px_20px_rgba(0,0,0,0.03)] print:hidden">
                <h2 class="font-headline-md text-headline-md text-on-surface mb-4">Changer le statut</h2>
                <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="flex flex-col sm:flex-row gap-3">
                    @csrf @method('PATCH')
                    <select name="status" class="flex-1 bg-surface border border-outline-variant rounded-lg px-4 py-2 font-body-md text-body-md">
                        @foreach($statuses as $status)
                            <option value="{{ $status->value }}" @selected($order->status === $status->value)>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                    <input type="text" name="note" placeholder="Note (optionnel)" class="flex-1 bg-surface border border-outline-variant rounded-lg px-4 py-2 font-body-md text-body-md" />
                    <button type="submit" class="w-full sm:w-auto bg-primary text-on-primary px-6 py-2 rounded-full font-label-sm text-label-sm uppercase">Mettre à jour</button>
                </form>

                <div class="mt-6 flex flex-col gap-3">
                    @foreach($order->statusHistories as $history)
                        <div class="flex items-start gap-3 font-body-md text-body-md">
                            <span class="material-symbols-outlined text-[18px] text-primary mt-0.5">check_circle</span>
                            <div class="min-w-0">
                                <p class="text-on-surface">{{ \App\Enums\OrderStatus::from($history->status)->label() }} — <span class="text-on-surface-variant font-label-sm text-label-sm">{{ $history->created_at->format('d/m/Y H:i') }}</span></p>
                                @if($history->note)<p class="text-on-surface-variant font-label-sm text-label-sm">{{ $history->note }}</p>@endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="flex flex-col gap-6">
            <div class="bg-surface-container-lowest rounded-xl p-4 sm:p-6 shadow-[0_4px_20px_rgba(0,0,0,0.03)]">
                <h2 class="font-headline-md text-headline-md text-on-surface mb-3">Client</h2>
                <p class="font-body-md text-body-md text-on-surface">{{ $order->user->name }}</p>
                <p class="font-body-md text-body-md text-on-surface-variant break-words">{{ $order->user->email }}</p>
                <p class="font-body-md text-body-md text-on-surface-variant">{{ $order->user->phone }}</p>
            </div>

            <div class="bg-surface-container-lowest rounded-xl p-4 sm:p-6 shadow-[0_4px_20px_rgba(0,0,0,0.03)]">
                <h2 class="font-headline-md text-headline-md text-on-surface mb-3">Livraison</h2>
                <p class="font-body-md text-body-md text-on-surface">{{ $order->address_snapshot['full_name'] ?? '' }}</p>
                <p class="font-body-md text-body-md text-on-surface-variant">{{ $order->address_snapshot['detailed_address'] ?? '' }}@if(!empty($order->address_snapshot['commune'])), {{ $order->address_snapshot['commune'] }}@endif</p>
                <p class="font-body-md text-body-md text-on-surface-variant">{{ $order->address_snapshot['city'] ?? '' }}, {{ $order->address_snapshot['province'] ?? '' }}</p>
                <p class="font-body-md text-body-md text-on-surface-variant mt-2">{{ $order->address_snapshot['phone'] ?? '' }}</p>
                <p class="font-label-sm text-label-sm text-on-surface-variant mt-2 capitalize">Mode: {{ str_replace('_', ' ', $order->delivery_method) }}</p>
            </div>

            <div class="bg-surface-container-lowest rounded-xl p-4 sm:p-6 shadow-[0_4px_20px_rgba(0,0,0,0.03)]">
                <h2 class="font-headline-md text-headline-md text-on-surface mb-3">Paiement</h2>
                @foreach($order->payments as $payment)
                    <div class="flex justify-between font-body-md text-body-md mb-1">
                        <span class="capitalize text-on-surface-variant">{{ $payment->provider }}</span>
                        <span class="{{ $payment->status === 'paid' ? 'text-primary' : 'text-error' }} capitalize">{{ $payment->status }}</span>
                    </div>
                    <p class="font-label-sm text-[11px] text-on-surface-variant break-words">Réf: {{ $payment->reference }}</p>
                @endforeach
            </div>
        </div>
    </div>
</x-layouts.admin>
