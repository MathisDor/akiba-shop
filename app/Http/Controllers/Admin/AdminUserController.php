<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(): View
    {
        $admins = User::whereHas('role', fn ($q) => $q->where('slug', 'admin'))
            ->with('permissions')
            ->orderByDesc('is_super_admin')
            ->orderBy('name')
            ->get();

        return view('admin.admins.index', compact('admins'));
    }

    public function create(): View
    {
        $permissions = Permission::orderBy('group')->orderBy('name')->get()->groupBy('group');

        return view('admin.admins.form', ['admin' => new User, 'permissions' => $permissions]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', Password::defaults()],
            'is_super_admin' => ['boolean'],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $adminRole = Role::where('slug', 'admin')->firstOrFail();

        $admin = new User([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => $validated['password'],
            'role_id' => $adminRole->id,
        ]);
        $admin->forceFill([
            'is_super_admin' => $request->boolean('is_super_admin'),
            'is_active' => true,
        ]);
        $admin->save();

        if (! $admin->is_super_admin) {
            $admin->permissions()->sync($validated['permissions'] ?? []);
        }

        activity('admin')
            ->causedBy(Auth::guard('admin')->user())
            ->performedOn($admin)
            ->log("Création du compte administrateur « {$admin->name} »");

        return redirect()->route('admin.admins.index')->with('status', 'Administrateur créé.');
    }

    public function edit(User $admin): View
    {
        abort_unless($admin->isAdmin(), 404);

        $admin->load('permissions');
        $permissions = Permission::orderBy('group')->orderBy('name')->get()->groupBy('group');

        return view('admin.admins.form', compact('admin', 'permissions'));
    }

    public function update(Request $request, User $admin): RedirectResponse
    {
        abort_unless($admin->isAdmin(), 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($admin->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['nullable', Password::defaults()],
            'is_super_admin' => ['boolean'],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $currentAdmin = Auth::guard('admin')->user();

        if ($admin->is($currentAdmin) && ! $request->boolean('is_super_admin') && $admin->is_super_admin) {
            return back()->with('error', 'Vous ne pouvez pas retirer vos propres droits de super administrateur.');
        }

        $admin->name = $validated['name'];
        $admin->email = $validated['email'];
        $admin->phone = $validated['phone'] ?? null;

        if (! empty($validated['password'])) {
            $admin->password = $validated['password'];
        }

        $admin->forceFill(['is_super_admin' => $request->boolean('is_super_admin')]);
        $admin->save();

        $admin->permissions()->sync($admin->is_super_admin ? [] : ($validated['permissions'] ?? []));

        activity('admin')
            ->causedBy($currentAdmin)
            ->performedOn($admin)
            ->log("Modification du compte administrateur « {$admin->name} »");

        return redirect()->route('admin.admins.index')->with('status', 'Administrateur mis à jour.');
    }

    public function toggleActive(User $admin): RedirectResponse
    {
        abort_unless($admin->isAdmin(), 404);

        $currentAdmin = Auth::guard('admin')->user();

        if ($admin->is($currentAdmin)) {
            return back()->with('error', 'Vous ne pouvez pas désactiver votre propre compte.');
        }

        $admin->update(['is_active' => ! $admin->is_active]);

        activity('admin')
            ->causedBy($currentAdmin)
            ->performedOn($admin)
            ->log($admin->is_active
                ? "Réactivation du compte administrateur « {$admin->name} »"
                : "Désactivation du compte administrateur « {$admin->name} »");

        return back()->with('status', $admin->is_active ? 'Administrateur réactivé.' : 'Administrateur désactivé.');
    }

    public function destroy(User $admin): RedirectResponse
    {
        abort_unless($admin->isAdmin(), 404);

        $currentAdmin = Auth::guard('admin')->user();

        if ($admin->is($currentAdmin)) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $name = $admin->name;
        $admin->delete();

        activity('admin')
            ->causedBy($currentAdmin)
            ->log("Suppression du compte administrateur « {$name} »");

        return redirect()->route('admin.admins.index')->with('status', 'Administrateur supprimé.');
    }
}
