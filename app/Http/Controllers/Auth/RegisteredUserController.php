<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Support\Countries;
use App\Support\Phone;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.register', ['countries' => Countries::options()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge(['phone' => Phone::normalize($request->input('phone'), $request->input('phone_country') ?: $request->input('country_code')) ?? $request->input('phone')]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'regex:/^\+\d{8,15}$/', 'unique:users,phone'],
            'country_code' => ['required', Rule::in(array_keys(config('countries')))],
            'city' => ['nullable', 'string', 'max:100'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'terms' => ['accepted'],
        ], [
            'phone.required' => 'Le numéro de téléphone permet aux acheteurs de vous contacter.',
            'phone.regex' => 'Numéro de téléphone invalide pour ce pays.',
            'phone.unique' => 'Ce numéro est déjà associé à un compte.',
            'terms.accepted' => 'Vous devez accepter les conditions d\'utilisation.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'country_code' => $validated['country_code'],
            'city' => $validated['city'] ?? null,
            'is_pro' => $request->boolean('is_pro'),
            'password' => $validated['password'],
            'role_id' => Role::where('slug', 'client')->firstOrFail()->id,
        ]);

        event(new Registered($user));

        Auth::guard('web')->login($user);

        return redirect()->intended(route('home'));
    }
}
