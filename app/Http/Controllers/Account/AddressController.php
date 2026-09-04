<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddressRequest;
use App\Models\Address;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AddressController extends Controller
{
    public function index(): View
    {
        $addresses = Auth::guard('web')->user()->addresses()->orderByDesc('is_default')->orderByDesc('created_at')->get();

        return view('account.addresses', compact('addresses'));
    }

    public function store(AddressRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $user = Auth::guard('web')->user();

        $address = $user->addresses()->create($validated + ['is_default' => $user->addresses()->count() === 0]);

        if ($request->boolean('is_default')) {
            $this->makeDefault($request, $address);
        }

        return redirect()->route('account.addresses')->with('status', 'Adresse ajoutée.');
    }

    public function update(AddressRequest $request, Address $address): RedirectResponse
    {
        Gate::authorize('update', $address);

        $address->update($request->validated());

        return redirect()->route('account.addresses')->with('status', 'Adresse mise à jour.');
    }

    public function destroy(Address $address): RedirectResponse
    {
        Gate::authorize('delete', $address);

        $address->delete();

        return redirect()->route('account.addresses')->with('status', 'Adresse supprimée.');
    }

    public function makeDefault(Request $request, Address $address): RedirectResponse
    {
        Gate::authorize('update', $address);

        $address->user->addresses()->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return redirect()->route('account.addresses')->with('status', 'Adresse par défaut mise à jour.');
    }
}
