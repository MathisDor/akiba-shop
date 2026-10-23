<?php

namespace App\Http\Controllers;

use App\Enums\ReportReason;
use App\Enums\ReportStatus;
use App\Models\Listing;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ListingReportController extends Controller
{
    public function store(Request $request, Listing $listing): RedirectResponse
    {
        abort_unless($listing->isVisible(), 404);

        $validated = $request->validate([
            'reason' => ['required', Rule::enum(ReportReason::class)],
            'details' => ['nullable', 'string', 'max:1000'],
        ], [
            'reason.required' => 'Choisissez un motif.',
        ]);

        $userId = Auth::guard('web')->id();

        $alreadyReported = $userId !== null && $listing->reports()
            ->where('user_id', $userId)
            ->where('status', ReportStatus::Ouvert->value)
            ->exists();

        if (! $alreadyReported) {
            $listing->reports()->create([
                ...$validated,
                'user_id' => $userId,
                'status' => ReportStatus::Ouvert,
            ]);
        }

        return back()->with('status', 'Merci, votre signalement a été transmis à notre équipe de modération.');
    }
}
