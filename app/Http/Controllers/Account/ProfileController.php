<?php

namespace App\Http\Controllers\Account;

use App\Enums\ListingStatus;
use App\Http\Controllers\Controller;
use App\Support\Countries;
use App\Support\Phone;
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
        ];

        return view('account.profile', [
            'user' => $user,
            'stats' => $stats,
            'countries' => Countries::options(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = Auth::guard('web')->user();

        $request->merge(['phone' => Phone::normalize($request->input('phone'), $request->input('phone_country') ?: $request->input('country_code')) ?? $request->input('phone')]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['required', 'regex:/^\+\d{8,15}$/', Rule::unique('users', 'phone')->ignore($user->id)],
            'country_code' => ['required', Rule::in(array_keys(config('countries')))],
            'city' => ['nullable', 'string', 'max:100'],
        ], [
            'phone.regex' => 'Numéro de téléphone invalide pour ce pays.',
            'phone.unique' => 'Ce numéro est déjà utilisé par un autre compte.',
        ]);

        $user->update([...$validated, 'is_pro' => $request->boolean('is_pro')]);

        return redirect()->route('account.profile')->with('status', 'Profil mis à jour.');
    }
}
