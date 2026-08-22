<?php

namespace App\Livewire\Checkout;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class PaymentPending extends Component
{
    public Order $order;

    public function mount(Order $order): void
    {
        Gate::authorize('view', $order);

        $this->order = $order;
    }

    public function poll()
    {
        $this->order->refresh();

        if ($this->order->statusEnum() === OrderStatus::Payee) {
            return redirect()->route('checkout.confirmation', $this->order);
        }
    }

    public function render()
    {
        return view('livewire.checkout.payment-pending')
            ->layout('layouts.checkout', ['title' => 'Paiement en attente — AkibaShop', 'step' => 'paiement']);
    }
}
