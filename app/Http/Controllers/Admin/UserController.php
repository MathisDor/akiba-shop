<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Marketplace members (sellers & buyers) — admins are managed in AdminUserController. */
class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::whereHas('role', fn ($q) => $q->where('slug', 'client'))
            ->withCount('listings');

        if ($request->filled('q')) {
            $like = '%'.addcslashes($request->query('q'), '%_\\').'%';
            $query->where(fn ($q) => $q->where('name', 'like', $like)->orWhere('email', 'like', $like)->orWhere('phone', 'like', $like));
        }

        match ($request->query('filtre')) {
            'pro' => $query->where('is_pro', true),
            'verifies' => $query->where('is_verified', true),
            'suspendus' => $query->where('is_active', false),
            default => null,
        };

        return view('admin.users.index', ['users' => $query->latest()->paginate(20)->withQueryString()]);
    }

    public function show(User $user): View
    {
        abort_if($user->isAdmin(), 404);

        $user->load(['listings' => fn ($q) => $q->with(['images', 'category'])->latest()]);

        return view('admin.users.show', ['member' => $user]);
    }

    public function toggleVerified(User $user): RedirectResponse
    {
        abort_if($user->isAdmin(), 404);

        $user->forceFill(['is_verified' => ! $user->is_verified])->save();

        activity('user')->performedOn($user)->causedBy(auth('admin')->user())
            ->log($user->is_verified ? 'Vendeur vérifié' : 'Badge « vérifié » retiré');

        return back()->with('status', $user->is_verified ? 'Vendeur marqué comme vérifié.' : 'Badge « vérifié » retiré.');
    }

    public function toggleActive(User $user): RedirectResponse
    {
        abort_if($user->isAdmin(), 404);

        $user->forceFill(['is_active' => ! $user->is_active])->save();

        activity('user')->performedOn($user)->causedBy(auth('admin')->user())
            ->log($user->is_active ? 'Compte réactivé' : 'Compte suspendu');

        return back()->with('status', $user->is_active
            ? 'Compte réactivé.'
            : 'Compte suspendu : ses annonces ne sont plus visibles et il ne peut plus se connecter.');
    }
}
