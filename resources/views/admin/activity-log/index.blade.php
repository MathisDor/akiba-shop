<x-layouts.admin active="activity-log" title="Journal d'activité">
    <form method="GET" class="flex flex-wrap gap-3 mb-6">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher dans le journal…" class="flex-1 min-w-[240px] bg-surface-container-lowest border border-outline-variant rounded-full px-4 py-2 font-body-md text-body-md focus:border-primary focus:ring-0" />
        <select name="admin" onchange="this.form.submit()" class="bg-surface-container-lowest border border-outline-variant rounded-full px-4 py-2 font-body-md text-body-md">
            <option value="">Tous les administrateurs</option>
            @foreach($admins as $adminUser)
                <option value="{{ $adminUser->id }}" @selected(request('admin') == $adminUser->id)>{{ $adminUser->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="bg-primary text-on-primary px-6 py-2 rounded-full font-label-sm text-label-sm uppercase">Filtrer</button>
    </form>

    <div class="bg-surface-container-lowest rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.03)] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
            <thead class="bg-surface-container-low">
                <tr class="font-label-sm text-label-sm uppercase text-on-surface-variant">
                    <th class="px-4 py-3 md:px-6 md:py-4">Administrateur</th>
                    <th class="px-4 py-3 md:px-6 md:py-4">Action</th>
                    <th class="px-4 py-3 md:px-6 md:py-4">Concerne</th>
                    <th class="px-4 py-3 md:px-6 md:py-4">Date</th>
                    <th class="px-4 py-3 md:px-6 md:py-4">IP</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-variant">
                @forelse($logs as $log)
                    <tr class="font-body-md text-body-md text-on-surface align-top">
                        <td class="px-4 py-3 md:px-6 md:py-4">{{ $log->causer?->name ?? 'Système' }}</td>
                        <td class="px-4 py-3 md:px-6 md:py-4">{{ $log->description }}</td>
                        <td class="px-4 py-3 md:px-6 md:py-4 font-label-sm text-label-sm text-on-surface-variant">
                            @if($log->subject_type)
                                {{ class_basename($log->subject_type) }} #{{ $log->subject_id }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-4 py-3 md:px-6 md:py-4 font-label-sm text-label-sm text-on-surface-variant whitespace-nowrap">{{ $log->created_at->translatedFormat('d M Y à H:i') }}</td>
                        <td class="px-4 py-3 md:px-6 md:py-4 font-label-sm text-[11px] text-on-surface-variant">{{ $log->properties['ip'] ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-8 text-center font-body-md text-body-md text-on-surface-variant">Aucune activité enregistrée.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    <div class="mt-6">{{ $logs->onEachSide(1)->links('pagination.akiba') }}</div>
</x-layouts.admin>
