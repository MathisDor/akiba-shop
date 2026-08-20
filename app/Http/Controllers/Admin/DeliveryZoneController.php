<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryZone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DeliveryZoneController extends Controller
{
    public function index(): View
    {
        $zones = DeliveryZone::orderBy('province')->orderBy('fee')->get();

        return view('admin.delivery-zones.index', compact('zones'));
    }

    public function create(): View
    {
        return view('admin.delivery-zones.form', ['deliveryZone' => new DeliveryZone]);
    }

    private function rules(): array
    {
        return [
            'province' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'zone_name' => ['required', 'string', 'max:255'],
            'fee' => ['required', 'integer', 'min:0'],
        ];
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $validated['is_active'] = $request->boolean('is_active', true);

        DeliveryZone::create($validated);

        return redirect()->route('admin.delivery-zones.index')->with('status', 'Zone de livraison créée.');
    }

    public function edit(DeliveryZone $deliveryZone): View
    {
        return view('admin.delivery-zones.form', compact('deliveryZone'));
    }

    public function update(Request $request, DeliveryZone $deliveryZone): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $validated['is_active'] = $request->boolean('is_active', true);

        $deliveryZone->update($validated);

        return redirect()->route('admin.delivery-zones.index')->with('status', 'Zone de livraison mise à jour.');
    }

    public function destroy(DeliveryZone $deliveryZone): RedirectResponse
    {
        $deliveryZone->delete();

        return redirect()->route('admin.delivery-zones.index')->with('status', 'Zone de livraison supprimée.');
    }
}
