<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ListingStatus;
use App\Enums\ReportStatus;
use App\Http\Controllers\Controller;
use App\Models\ListingReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $status = ReportStatus::tryFrom((string) $request->query('statut', ReportStatus::Ouvert->value));

        $reports = ListingReport::with(['listing.user', 'listing.images', 'user'])
            ->when($status, fn ($q) => $q->where('status', $status->value))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.reports.index', [
            'reports' => $reports,
            'currentStatus' => $status,
            'openCount' => ListingReport::where('status', ReportStatus::Ouvert->value)->count(),
        ]);
    }

    public function update(Request $request, ListingReport $report): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::enum(ReportStatus::class)],
            'remove_listing' => ['boolean'],
        ]);

        $report->update(['status' => $validated['status']]);

        if ($request->boolean('remove_listing') && $report->listing) {
            $report->listing->update([
                'status' => ListingStatus::Refusee,
                'rejection_reason' => 'Retirée suite à un signalement : '.$report->reason->label(),
            ]);

            // Every other open report on the same listing is resolved by the removal.
            $report->listing->reports()
                ->where('status', ReportStatus::Ouvert->value)
                ->update(['status' => ReportStatus::Traite->value]);
        }

        activity('report')->performedOn($report)->causedBy(auth('admin')->user())
            ->log('Signalement '.mb_strtolower($report->status->label()).($request->boolean('remove_listing') ? ' — annonce retirée' : ''));

        return back()->with('status', 'Signalement mis à jour.');
    }
}
