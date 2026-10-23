@php use App\Enums\ReportStatus; @endphp

<x-layouts.admin active="reports" title="Signalements">
    <div class="flex gap-2 overflow-x-auto hide-scrollbar mb-5">
        @foreach(ReportStatus::cases() as $status)
            <a href="{{ route('admin.reports.index', ['statut' => $status->value]) }}" class="shrink-0 px-4 py-2 rounded-full text-[13px] font-semibold border {{ $currentStatus === $status ? 'bg-on-surface text-surface border-on-surface' : 'bg-surface-container-lowest border-outline-variant text-on-surface' }}">
                {{ $status->label() }}@if($status === ReportStatus::Ouvert) ({{ $openCount }})@endif
            </a>
        @endforeach
    </div>

    @session('status')
        <div class="mb-4 rounded-xl bg-primary-fixed text-on-primary-fixed px-4 py-3 text-[14px]">{{ $value }}</div>
    @endsession

    <div class="flex flex-col gap-3">
        @forelse($reports as $report)
            <div class="card p-4 flex flex-col md:flex-row md:items-center gap-4">
                <div class="flex-1 min-w-0">
                    <p class="text-[14px] font-bold text-on-surface">{{ $report->reason->label() }}</p>
                    @if($report->details)
                        <p class="text-[14px] text-on-surface mt-1 break-words">« {{ $report->details }} »</p>
                    @endif
                    <p class="text-[12px] text-on-surface-variant mt-1">
                        {{ $report->created_at->diffForHumans() }} · par {{ $report->user?->name ?? 'un visiteur' }} · annonce :
                        @if($report->listing)
                            <a href="{{ route('admin.listings.show', $report->listing) }}" class="font-semibold text-secondary hover:underline">{{ $report->listing->title }}</a> ({{ $report->listing->user->name }}, {{ $report->listing->status->label() }})
                        @else
                            supprimée
                        @endif
                    </p>
                </div>

                @if($report->status === ReportStatus::Ouvert)
                    <div class="flex flex-wrap gap-2 shrink-0">
                        <form method="POST" action="{{ route('admin.reports.update', $report) }}" onsubmit="return confirm('Retirer l’annonce du site ?')">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="{{ ReportStatus::Traite->value }}" />
                            <input type="hidden" name="remove_listing" value="1" />
                            <button class="btn btn-sm btn-primary">Retirer l’annonce</button>
                        </form>
                        <form method="POST" action="{{ route('admin.reports.update', $report) }}">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="{{ ReportStatus::Rejete->value }}" />
                            <button class="btn btn-sm btn-outline">Classer sans suite</button>
                        </form>
                    </div>
                @else
                    <span class="shrink-0 rounded-md bg-surface-container-low px-2 py-1 text-[12px] font-semibold">{{ $report->status->label() }}</span>
                @endif
            </div>
        @empty
            <div class="card p-10 text-center text-on-surface-variant">Aucun signalement dans cette liste.</div>
        @endforelse
    </div>

    <div class="mt-6">{{ $reports->onEachSide(1)->links('pagination.akiba') }}</div>
</x-layouts.admin>
