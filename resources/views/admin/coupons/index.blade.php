<x-layouts.admin active="coupons" title="Coupons">
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-6">
        <p class="font-body-md text-body-md text-on-surface-variant">{{ $coupons->count() }} codes promo</p>
        <a href="{{ route('admin.coupons.create') }}" class="bg-primary text-on-primary px-6 py-3 rounded-full font-label-sm text-label-sm uppercase hover:opacity-90 transition-opacity flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">add</span> Nouveau coupon
        </a>
    </div>

    @session('status')
        <div class="mb-6 rounded-lg bg-primary-container/10 text-primary px-4 py-3 font-body-md text-body-md">{{ $value }}</div>
    @endsession

    {{-- Mobile: card list --}}
    <div class="md:hidden flex flex-col gap-3">
        @forelse($coupons as $coupon)
            <div class="bg-surface-container-lowest rounded-xl p-4 shadow-[0_4px_20px_rgba(0,0,0,0.03)]">
                <div class="flex items-start justify-between gap-2">
                    <p class="font-body-md text-body-md text-on-surface font-bold truncate">{{ $coupon->code }}</p>
                    <span class="px-2 py-0.5 rounded-full font-label-sm text-[10px] shrink-0 {{ $coupon->is_active ? 'bg-primary/10 text-primary' : 'bg-surface-variant text-on-surface-variant' }}">
                        {{ $coupon->is_active ? 'Actif' : 'Inactif' }}
                    </span>
                </div>
                <p class="font-label-sm text-[11px] text-on-surface-variant mt-1">
                    {{ $coupon->type === 'percentage' ? $coupon->value.'%' : number_format($coupon->value, 0, ',', ' ').' FCFA' }}
                    · Utilisé {{ $coupon->used_count }}{{ $coupon->usage_limit ? ' / '.$coupon->usage_limit : '' }}
                    · {{ $coupon->starts_at?->format('d/m/Y') ?? '—' }} → {{ $coupon->ends_at?->format('d/m/Y') ?? '—' }}
                </p>
                <div class="flex items-center gap-4 mt-3 pt-3 border-t border-surface-variant">
                    <a href="{{ route('admin.coupons.edit', $coupon) }}" class="text-primary font-label-sm text-label-sm">Modifier</a>
                    <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" onsubmit="return confirm('Supprimer ce coupon ?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-error font-label-sm text-label-sm">Supprimer</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="font-body-md text-body-md text-on-surface-variant text-center py-8">Aucun coupon.</p>
        @endforelse
    </div>

    {{-- Desktop: table --}}
    <div class="hidden md:block bg-surface-container-lowest rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.03)] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
            <thead class="bg-surface-container-low">
                <tr class="font-label-sm text-label-sm uppercase text-on-surface-variant">
                    <th class="px-4 py-3 md:px-6 md:py-4">Code</th>
                    <th class="px-4 py-3 md:px-6 md:py-4">Réduction</th>
                    <th class="px-4 py-3 md:px-6 md:py-4">Utilisation</th>
                    <th class="px-4 py-3 md:px-6 md:py-4">Période</th>
                    <th class="px-4 py-3 md:px-6 md:py-4">Statut</th>
                    <th class="px-4 py-3 md:px-6 md:py-4"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-variant">
                @foreach($coupons as $coupon)
                    <tr class="font-body-md text-body-md text-on-surface">
                        <td class="px-4 py-3 md:px-6 md:py-4 font-bold">{{ $coupon->code }}</td>
                        <td class="px-4 py-3 md:px-6 md:py-4">{{ $coupon->type === 'percentage' ? $coupon->value.'%' : number_format($coupon->value, 0, ',', ' ').' FCFA' }}</td>
                        <td class="px-4 py-3 md:px-6 md:py-4">{{ $coupon->used_count }}{{ $coupon->usage_limit ? ' / '.$coupon->usage_limit : '' }}</td>
                        <td class="px-4 py-3 md:px-6 md:py-4 font-label-sm text-label-sm text-on-surface-variant">
                            {{ $coupon->starts_at?->format('d/m/Y') ?? '—' }} → {{ $coupon->ends_at?->format('d/m/Y') ?? '—' }}
                        </td>
                        <td class="px-4 py-3 md:px-6 md:py-4">
                            <span class="px-3 py-1 rounded-full font-label-sm text-[11px] {{ $coupon->is_active ? 'bg-primary/10 text-primary' : 'bg-surface-variant text-on-surface-variant' }}">
                                {{ $coupon->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 md:px-6 md:py-4 text-right">
                            <a href="{{ route('admin.coupons.edit', $coupon) }}" class="text-primary hover:underline font-label-sm text-label-sm">Modifier</a>
                            <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" class="inline" onsubmit="return confirm('Supprimer ce coupon ?')">
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
