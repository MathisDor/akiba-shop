<?php

namespace App\Livewire\Checkout;

use App\Models\DeliveryZone;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Livraison extends Component
{
    public string $deliveryMethod = 'domicile';

    public ?int $deliveryZoneId = null;

    #[Validate('required|string|max:255')]
    public string $fullName = '';

    #[Validate('required|string|max:20')]
    public string $phone = '';

    #[Validate('required|string|max:255')]
    public string $province = 'Estuaire';

    #[Validate('required|string|max:255')]
    public string $city = 'Libreville';

    public string $commune = '';

    #[Validate('required|string|max:255')]
    public string $detailedAddress = '';

    public string $landmark = '';

    public string $error = '';

    public function mount(): void
    {
        if (! session()->has('checkout.informations')) {
            redirect()->route('checkout.informations');

            return;
        }

        $user = Auth::guard('web')->user();
        $default = $user->addresses()->where('is_default', true)->first() ?? $user->addresses()->first();
        $data = session('checkout.livraison', []);

        $this->deliveryMethod = $data['delivery_method'] ?? 'domicile';
        $this->deliveryZoneId = $data['delivery_zone_id'] ?? null;
        $this->fullName = $data['full_name'] ?? $default?->full_name ?? $user->name;
        $this->phone = $data['phone'] ?? $default?->phone ?? '';
        $this->province = $data['province'] ?? $default?->province ?? 'Estuaire';
        $this->city = $data['city'] ?? $default?->city ?? 'Libreville';
        $this->commune = $data['commune'] ?? $default?->commune ?? '';
        $this->detailedAddress = $data['detailed_address'] ?? $default?->detailed_address ?? '';
        $this->landmark = $data['landmark'] ?? $default?->landmark ?? '';
    }

    public function getDeliveryZonesProperty()
    {
        return DeliveryZone::where('is_active', true)->orderBy('fee')->get();
    }

    public function getDeliveryFeeProperty(): int
    {
        if ($this->deliveryMethod !== 'domicile' || ! $this->deliveryZoneId) {
            return 0;
        }

        return $this->deliveryZones->firstWhere('id', $this->deliveryZoneId)?->fee ?? 0;
    }

    public function continue()
    {
        $this->validate();

        if ($this->deliveryMethod === 'domicile' && ! $this->deliveryZoneId) {
            $this->error = 'Veuillez sélectionner votre zone de livraison.';

            return null;
        }

        $user = Auth::guard('web')->user();

        $address = $user->addresses()->create([
            'label' => 'Livraison',
            'full_name' => $this->fullName,
            'phone' => $this->phone,
            'province' => $this->province,
            'city' => $this->city,
            'commune' => $this->commune,
            'detailed_address' => $this->detailedAddress,
            'landmark' => $this->landmark,
            'is_default' => $user->addresses()->count() === 0,
        ]);

        session()->put('checkout.livraison', [
            'address_id' => $address->id,
            'delivery_method' => $this->deliveryMethod,
            'delivery_zone_id' => $this->deliveryMethod === 'domicile' ? $this->deliveryZoneId : null,
            'full_name' => $this->fullName,
            'phone' => $this->phone,
            'province' => $this->province,
            'city' => $this->city,
            'commune' => $this->commune,
            'detailed_address' => $this->detailedAddress,
            'landmark' => $this->landmark,
        ]);

        return redirect()->route('checkout.paiement');
    }

    public function render()
    {
        $cart = app(CartService::class)->current();
        $cart->load('items.variant.product.images', 'items.variant.product.promotions');

        return view('livewire.checkout.livraison', [
            'cart' => $cart,
            'subtotal' => $cart->subtotal(),
        ])->layout('layouts.checkout', ['title' => 'Livraison — AkibaShop', 'step' => 'livraison', 'backUrl' => route('checkout.informations')]);
    }
}
