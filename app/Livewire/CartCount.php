<?php

namespace App\Livewire;

use App\Services\CartService;
use Livewire\Attributes\On;
use Livewire\Component;

class CartCount extends Component
{
    #[On('cart-updated')]
    public function refresh(): void {}

    public function render()
    {
        return view('livewire.cart-count', [
            'count' => app(CartService::class)->itemCount(),
        ]);
    }
}
