<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Support\GabonPhone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'login' => ['required', 'string', 'max:255'],
            'password' => ['required'],
        ], [
            'login.required' => 'Indiquez votre email ou votre numéro de téléphone.',
        ]);

        // Members sign in with either their email or their Gabonese phone number.
        $login = trim($validated['login']);
        $credentials = str_contains($login, '@')
            ? ['email' => $login]
            : ['phone' => GabonPhone::normalize($login) ?? $login];

        if (! Auth::guard('web')->attempt([...$credentials, 'password' => $validated['password']], $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'login' => 'Identifiants incorrects.',
            ]);
        }

        $user = Auth::guard('web')->user();

        // Admin accounts sign in through the dedicated admin login only — never
        // the member-facing form.
        if ($user->isAdmin()) {
            Auth::guard('web')->logout();

            throw ValidationException::withMessages([
                'login' => 'Merci d\'utiliser la connexion administrateur.',
            ]);
        }

        if (! $user->is_active) {
            Auth::guard('web')->logout();

            throw ValidationException::withMessages([
                'login' => 'Ce compte a été suspendu. Contactez-nous si vous pensez qu\'il s\'agit d\'une erreur.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('home'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
