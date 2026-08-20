<x-layouts.admin active="reviews" title="Avis clients">
    <div class="flex gap-3 mb-6">
        <a href="{{ route('admin.reviews.index') }}" class="px-6 py-2 rounded-full font-label-sm text-label-sm {{ !request('filter') ? 'bg-primary text-on-primary' : 'border border-outline-variant text-on-surface' }}">Tous</a>
        <a href="{{ route('admin.reviews.index', ['filter' => 'pending']) }}" class="px-6 py-2 rounded-full font-label-sm text-label-sm {{ request('filter') === 'pending' ? 'bg-primary text-on-primary' : 'border border-outline-variant text-on-surface' }}">En attente</a>
    </div>

    @session('status')
        <div class="mb-6 rounded-lg bg-primary-container/10 text-primary px-4 py-3 font-body-md text-body-md">{{ $value }}</div>
    @endsession

    <div class="flex flex-col gap-4">
        @forelse($reviews as $review)
            <div class="bg-surface-container-lowest rounded-xl p-6 shadow-[0_4px_20px_rgba(0,0,0,0.03)] flex flex-col md:flex-row md:items-center gap-4 justify-between">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        @for($i = 1; $i <= 5; $i++)
                            <span class="material-symbols-outlined text-[16px] {{ $i <= $review->rating ? 'icon-fill text-primary' : 'text-outline-variant' }}">star</span>
                        @endfor
                        <span class="font-label-sm text-label-sm text-on-surface-variant ml-2">{{ $review->product->name }}</span>
                    </div>
                    <p class="font-body-md text-body-md text-on-surface">{{ $review->comment }}</p>
                    <p class="font-label-sm text-label-sm text-on-surface-variant mt-1">— {{ $review->user->name }}, {{ $review->created_at->format('d/m/Y') }}</p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <span class="px-3 py-1 rounded-full font-label-sm text-[11px] {{ $review->is_approved ? 'bg-primary/10 text-primary' : 'bg-surface-variant text-on-surface-variant' }}">
                        {{ $review->is_approved ? 'Approuvé' : 'En attente' }}
                    </span>
                    <form method="POST" action="{{ route('admin.reviews.moderate', $review) }}">
                        @csrf @method('PATCH')
                        <input type="hidden" name="approve" value="{{ $review->is_approved ? 0 : 1 }}" />
                        <button type="submit" class="px-4 py-2 rounded-full border border-outline-variant font-label-sm text-label-sm hover:border-primary hover:text-primary transition-colors">
                            {{ $review->is_approved ? 'Rejeter' : 'Approuver' }}
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <p class="font-body-md text-body-md text-on-surface-variant">Aucun avis pour le moment.</p>
        @endforelse
    </div>

    <div class="mt-6">{{ $reviews->onEachSide(1)->links('pagination.akiba') }}</div>
</x-layouts.admin>
