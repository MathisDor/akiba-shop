<?php

namespace App\Http\Controllers\Account;

use App\Enums\ListingStatus;
use App\Http\Controllers\Controller;
use App\Support\Phone;
use App\Support\Provinces;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(): View
    {
        $user = Auth::guard('web')->user();

        $stats = [
            'online' => $user->listings()->visible()->count(),
            'pending' => $user->listings()->where('status', ListingStatus::EnAttente->value)->count(),
            'views' => (int) $user->listings()->sum('views'),
            'favorites' => $user->favoriteListings()->count(),
            'unread' => $user->unreadMessagesCount(),
        ];

        return view('account.profile', [
            'user' => $user,
            'stats' => $stats,
            'provinces' => Provinces::options(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = Auth::guard('web')->user();

        $request->merge(['phone' => Phone::normalize($request->input('phone'), $request->input('phone_country')) ?? $request->input('phone')]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['required', 'regex:/^\+\d{8,15}$/', Rule::unique('users', 'phone')->ignore($user->id)],
            'province' => ['nullable', Rule::in(array_keys(config('marketplace.provinces')))],
            'city' => ['nullable', 'string', 'max:100'],
        ], [
            'phone.regex' => 'Numéro de téléphone invalide pour ce pays.',
            'phone.unique' => 'Ce numéro est déjà utilisé par un autre compte.',
        ]);

        $user->update([...$validated, 'is_pro' => $request->boolean('is_pro')]);

        return redirect()->route('account.profile')->with('status', 'Profil mis à jour.');
    }
}
