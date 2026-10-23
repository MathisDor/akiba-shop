<x-layouts.admin active="users" :title="$member->name">
    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1 text-[13px] font-semibold text-secondary hover:underline mb-4">
        <x-icon name="arrow_back" class="text-[16px]" /> Tous les utilisateurs
    </a>

    @session('status')
        <div class="mb-4 rounded-xl bg-primary-fixed text-on-primary-fixed px-4 py-3 text-[14px]">{{ $value }}</div>
    @endsession

    <div class="grid lg:grid-cols-[320px_1fr] gap-4 md:gap-6 items-start">
        <section class="card p-5 flex flex-col gap-2 text-[14px]">
            <p class="text-[18px] font-extrabold">{{ $member->name }}</p>
            <p class="text-on-surface-variant">{{ $member->email }}</p>
            <p>{{ $member->phone ? \App\Support\GabonPhone::format($member->phone) : 'Pas de téléphone' }}</p>
            <p class="text-on-surface-variant">{{ $member->city?->name ?? 'Ville non renseignée' }} · {{ $member->is_pro ? 'Professionnel' : 'Particulier' }}</p>
            <p class="text-on-surface-variant">Inscrit le {{ $member->created_at->format('d/m/Y') }}</p>

            <div class="flex flex-col gap-2 pt-3 mt-1 border-t border-surface-variant">
                <form method="POST" action="{{ route('admin.users.verify', $member) }}">
                    @csrf @method('PATCH')
                    <button class="btn btn-outline btn-sm w-full"><x-icon name="verified" class="text-[16px]" /> {{ $member->is_verified ? 'Retirer le badge « vérifié »' : 'Marquer comme vendeur vérifié' }}</button>
                </form>
                <form method="POST" action="{{ route('admin.users.toggle-active', $member) }}" onsubmit="return confirm(@js($member->is_active ? 'Suspendre ce compte ? Ses annonces seront masquées.' : 'Réactiver ce compte ?'))">
                    @csrf @method('PATCH')
                    <button class="btn btn-sm w-full {{ $member->is_active ? 'btn-outline !text-error' : 'btn-primary' }}">{{ $member->is_active ? 'Suspendre le compte' : 'Réactiver le compte' }}</button>
                </form>
            </div>
        </section>

        <section class="card p-5">
            <h2 class="text-[16px] font-extrabold mb-3">Annonces ({{ $member->listings->count() }})</h2>
            @forelse($member->listings as $listing)
                <a href="{{ route('admin.listings.show', $listing) }}" class="flex items-center gap-3 py-2.5 border-t border-surface-variant first:border-t-0 hover:bg-surface-container-low -mx-2 px-2 rounded-lg">
                    <span class="w-12 h-12 rounded-lg overflow-hidden bg-surface-container-low shrink-0 flex items-center justify-center">
                        @if($cover = $listing->coverImage())
                            <img src="{{ $cover->url() }}" alt="" loading="lazy" class="w-full h-full object-cover" />
                        @else
                            <x-icon name="image" class="text-[20px] text-outline" />
                        @endif
                    </span>
                    <span class="flex-1 min-w-0">
                        <span class="block text-[14px] font-semibold truncate">{{ $listing->title }}</span>
                        <span class="block text-[12px] text-on-surface-variant">{{ $listing->formattedPrice() }} · {{ $listing->category->name }} · {{ $listing->created_at->format('d/m/Y') }}</span>
                    </span>
                    <span class="rounded-md px-2 py-0.5 text-[11px] font-bold {{ $listing->status->badgeClasses() }}">{{ $listing->status->label() }}</span>
                </a>
            @empty
                <p class="text-[14px] text-on-surface-variant">Aucune annonce.</p>
            @endforelse
        </section>
    </div>
</x-layouts.admin>
