<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Role;
use App\Models\User;
use App\Support\GabonPhone;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.register', ['cities' => City::orderBy('sort_order')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge(['phone' => GabonPhone::normalize($request->input('phone')) ?? $request->input('phone')]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'regex:/^0\d{7,8}$/', 'unique:users,phone'],
            'city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'terms' => ['accepted'],
        ], [
            'phone.required' => 'Le numéro de téléphone permet aux acheteurs de vous contacter.',
            'phone.regex' => 'Numéro gabonais invalide (ex. : 077 12 34 56).',
            'phone.unique' => 'Ce numéro est déjà associé à un compte.',
            'terms.accepted' => 'Vous devez accepter les conditions d\'utilisation.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'city_id' => $validated['city_id'] ?? null,
            'is_pro' => $request->boolean('is_pro'),
            'password' => $validated['password'],
            'role_id' => Role::where('slug', 'client')->firstOrFail()->id,
        ]);

        event(new Registered($user));

        Auth::guard('web')->login($user);

        return redirect()->intended(route('home'));
    }
}
