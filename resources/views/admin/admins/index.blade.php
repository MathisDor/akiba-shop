<x-layouts.admin active="admins" title="Administrateurs">
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-6">
        <p class="font-body-md text-body-md text-on-surface-variant">{{ $admins->count() }} compte(s) administrateur</p>
        <a href="{{ route('admin.admins.create') }}" class="bg-primary text-on-primary px-6 py-3 rounded-full font-label-sm text-label-sm uppercase hover:opacity-90 transition-opacity flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">add</span> Nouvel administrateur
        </a>
    </div>

    @session('status')
        <div class="mb-6 rounded-lg bg-primary-container/10 text-primary px-4 py-3 font-body-md text-body-md">{{ $value }}</div>
    @endsession
    @session('error')
        <div class="mb-6 rounded-lg bg-error-container text-on-error-container px-4 py-3 font-body-md text-body-md">{{ $value }}</div>
    @endsession

    <div class="bg-surface-container-lowest rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.03)] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
            <thead class="bg-surface-container-low">
                <tr class="font-label-sm text-label-sm uppercase text-on-surface-variant">
                    <th class="px-4 py-3 md:px-6 md:py-4">Administrateur</th>
                    <th class="px-4 py-3 md:px-6 md:py-4">Accès</th>
                    <th class="px-4 py-3 md:px-6 md:py-4">Dernière connexion</th>
                    <th class="px-4 py-3 md:px-6 md:py-4">Statut</th>
                    <th class="px-4 py-3 md:px-6 md:py-4"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-variant">
                @foreach($admins as $admin)
                    <tr class="font-body-md text-body-md text-on-surface">
                        <td class="px-4 py-3 md:px-6 md:py-4">
                            <p>{{ $admin->name }}</p>
                            <p class="font-label-sm text-label-sm text-on-surface-variant">{{ $admin->email }}</p>
                        </td>
                        <td class="px-4 py-3 md:px-6 md:py-4">
                            @if($admin->is_super_admin)
                                <span class="px-3 py-1 rounded-full font-label-sm text-[11px] bg-primary text-on-primary uppercase">Super admin</span>
                            @elseif($admin->permissions->isEmpty())
                                <span class="font-label-sm text-label-sm text-on-surface-variant">Aucun accès</span>
                            @else
                                <div class="flex flex-wrap gap-1">
                                    @foreach($admin->permissions as $permission)
                                        <span class="px-2 py-0.5 rounded-full font-label-sm text-[10px] bg-surface-variant text-on-surface-variant">{{ $permission->name }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-3 md:px-6 md:py-4 font-label-sm text-label-sm text-on-surface-variant">
                            {{ $admin->last_login_at?->translatedFormat('d M Y à H:i') ?? 'Jamais connecté' }}
                            @if($admin->last_login_ip)
                                <br /><span class="text-[10px]">{{ $admin->last_login_ip }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 md:px-6 md:py-4">
                            <span class="px-3 py-1 rounded-full font-label-sm text-[11px] {{ $admin->is_active ? 'bg-primary/10 text-primary' : 'bg-error-container text-on-error-container' }}">
                                {{ $admin->is_active ? 'Actif' : 'Désactivé' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 md:px-6 md:py-4 text-right whitespace-nowrap">
                            <a href="{{ route('admin.admins.edit', $admin) }}" class="text-primary hover:underline font-label-sm text-label-sm">Modifier</a>
                            @unless($admin->is(auth('admin')->user()))
                                <form method="POST" action="{{ route('admin.admins.toggle-active', $admin) }}" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-on-surface-variant hover:underline font-label-sm text-label-sm ml-4">
                                        {{ $admin->is_active ? 'Désactiver' : 'Réactiver' }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.admins.destroy', $admin) }}" class="inline" onsubmit="return confirm('Supprimer cet administrateur ?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-error hover:underline font-label-sm text-label-sm ml-4">Supprimer</button>
                                </form>
                            @endunless
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>
</x-layouts.admin>
