<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsurePermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $admin = Auth::guard('admin')->user();

        if (! $admin || ! $admin->hasPermission($permission)) {
            abort(403, "Vous n'avez pas la permission d'accéder à cette section.");
        }

        return $next($request);
    }
}
