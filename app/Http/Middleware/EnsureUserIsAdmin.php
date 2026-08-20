<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $admin = Auth::guard('admin')->user();

        if (! $admin || ! $admin->isAdmin()) {
            return redirect()->route('admin.login');
        }

        if (! $admin->is_active) {
            Auth::guard('admin')->logout();
            $request->session()->invalidate();

            return redirect()->route('admin.login')->withErrors([
                'email' => 'Ce compte administrateur a été désactivé.',
            ]);
        }

        return $next($request);
    }
}
