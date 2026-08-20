<?php

namespace App\Livewire\Checkout;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Informations extends Component
{
    #[Validate('required|string|max:255')]
    public string $firstName = '';

    #[Validate('required|string|max:255')]
    public string $lastName = '';

    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required|string|max:20')]
    public string $phone = '';

    public bool $newsletter = false;

    public function mount(): void
    {
        $user = Auth::guard('web')->user();
        $nameParts = explode(' ', $user->name, 2);

        $data = session('checkout.informations', []);

        $this->firstName = $data['first_name'] ?? $nameParts[0] ?? '';
        $this->lastName = $data['last_name'] ?? ($nameParts[1] ?? '');
        $this->email = $data['email'] ?? $user->email;
        $this->phone = $data['phone'] ?? ltrim((string) $user->phone, '+241 ');
    }

    public function continue()
    {
        $this->validate();

        session()->put('checkout.informations', [
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'email' => $this->email,
            'phone' => $this->phone,
            'newsletter' => $this->newsletter,
        ]);

        return redirect()->route('checkout.livraison');
    }

    public function render()
    {
        return view('livewire.checkout.informations')
            ->layout('layouts.checkout', ['title' => 'Informations — AkibaShop', 'step' => 'informations', 'backUrl' => route('cart.index')]);
    }
}
