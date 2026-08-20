<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AdminAuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('admin.auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::guard('admin')->attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => 'Identifiants incorrects.',
            ]);
        }

        $admin = Auth::guard('admin')->user();

        if (! $admin->isAdmin()) {
            Auth::guard('admin')->logout();

            throw ValidationException::withMessages([
                'email' => "Ce compte n'a pas accès au dashboard administrateur.",
            ]);
        }

        if (! $admin->is_active) {
            Auth::guard('admin')->logout();

            throw ValidationException::withMessages([
                'email' => 'Ce compte administrateur a été désactivé.',
            ]);
        }

        $request->session()->regenerate();

        $admin->forceFill([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ])->save();

        activity('admin')
            ->causedBy($admin)
            ->withProperties(['ip' => $request->ip()])
            ->log('Connexion au dashboard administrateur');

        return redirect()->intended(route('admin.dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        if ($admin = Auth::guard('admin')->user()) {
            activity('admin')->causedBy($admin)->log('Déconnexion du dashboard administrateur');
        }

        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
