<?php

namespace App\Http\Controllers\Account;

use App\Enums\ListingStatus;
use App\Http\Controllers\Controller;
use App\Models\City;
use App\Support\GabonPhone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(): View
    {
        $user = Auth::guard('web')->user()->load('city');

        $stats = [
            'online' => $user->listings()->visible()->count(),
            'pending' => $user->listings()->where('status', ListingStatus::EnAttente->value)->count(),
            'views' => (int) $user->listings()->sum('views'),
            'favorites' => $user->favoriteListings()->count(),
        ];

        return view('account.profile', [
            'user' => $user,
            'stats' => $stats,
            'cities' => City::orderBy('sort_order')->get(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = Auth::guard('web')->user();

        $request->merge(['phone' => GabonPhone::normalize($request->input('phone')) ?? $request->input('phone')]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['required', 'regex:/^0\d{7,8}$/', Rule::unique('users', 'phone')->ignore($user->id)],
            'city_id' => ['nullable', 'integer', 'exists:cities,id'],
        ], [
            'phone.regex' => 'Numéro gabonais invalide (ex. : 077 12 34 56).',
            'phone.unique' => 'Ce numéro est déjà utilisé par un autre compte.',
        ]);

        $user->update([...$validated, 'is_pro' => $request->boolean('is_pro')]);

        return redirect()->route('account.profile')->with('status', 'Profil mis à jour.');
    }
}
