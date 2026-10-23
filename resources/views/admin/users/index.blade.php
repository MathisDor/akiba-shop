<x-layouts.admin active="users" title="Utilisateurs">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 mb-5">
        <div class="flex gap-2 overflow-x-auto hide-scrollbar">
            @foreach(['' => 'Tous', 'pro' => 'Pros', 'verifies' => 'Vérifiés', 'suspendus' => 'Suspendus'] as $value => $label)
                <a href="{{ route('admin.users.index', array_filter(['filtre' => $value, 'q' => request('q')])) }}" class="shrink-0 px-4 py-2 rounded-full text-[13px] font-semibold border {{ request('filtre', '') === $value ? 'bg-on-surface text-surface border-on-surface' : 'bg-surface-container-lowest border-outline-variant text-on-surface' }}">{{ $label }}</a>
            @endforeach
        </div>
        <form method="GET" class="flex gap-2">
            <input type="hidden" name="filtre" value="{{ request('filtre') }}" />
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Nom, email, téléphone…" class="field !min-h-[40px] !py-2 w-full md:w-72" />
            <button class="btn btn-primary btn-sm">Rechercher</button>
        </form>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-[14px]">
                <thead class="bg-surface-container-low text-[12px] uppercase tracking-wide text-on-surface-variant">
                    <tr>
                        <th class="px-4 py-3">Membre</th>
                        <th class="px-4 py-3 hidden md:table-cell">Téléphone</th>
                        <th class="px-4 py-3 hidden md:table-cell">Ville</th>
                        <th class="px-4 py-3">Annonces</th>
                        <th class="px-4 py-3 hidden sm:table-cell">Inscrit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-variant">
                    @forelse($users as $user)
                        <tr class="hover:bg-surface-container-low">
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.users.show', $user) }}" class="font-semibold text-on-surface hover:text-primary">{{ $user->name }}</a>
                                <span class="inline-flex gap-1 ml-1 align-middle">
                                    @if($user->is_pro)<span class="rounded bg-secondary-container text-on-secondary-container px-1.5 text-[10px] font-bold">PRO</span>@endif
                                    @if($user->is_verified)<span class="rounded bg-secondary-container text-on-secondary-container px-1.5 text-[10px] font-bold">VÉRIFIÉ</span>@endif
                                    @unless($user->is_active)<span class="rounded bg-error-container text-on-error-container px-1.5 text-[10px] font-bold">SUSPENDU</span>@endunless
                                </span>
                                <span class="block text-[12px] text-on-surface-variant">{{ $user->email }}</span>
                            </td>
                            <td class="px-4 py-3 hidden md:table-cell">{{ $user->phone ? \App\Support\GabonPhone::format($user->phone) : '—' }}</td>
                            <td class="px-4 py-3 hidden md:table-cell">{{ $user->city?->name ?? '—' }}</td>
                            <td class="px-4 py-3">{{ $user->listings_count }}</td>
                            <td class="px-4 py-3 hidden sm:table-cell text-on-surface-variant">{{ $user->created_at->format('d/m/Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-10 text-center text-on-surface-variant">Aucun membre trouvé.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">{{ $users->onEachSide(1)->links('pagination.akiba') }}</div>
</x-layouts.admin>
