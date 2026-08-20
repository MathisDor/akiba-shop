<?php

namespace App\Livewire;

use App\Actions\AddToCartAction;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ProductVariantSelector extends Component
{
    public Product $product;

    public ?int $selectedColorId = null;

    public ?int $selectedSizeId = null;

    public int $quantity = 1;

    public ?string $feedback = null;

    public bool $success = false;

    public function mount(Product $product, ?int $initialColorId = null): void
    {
        $this->product = $product;

        // Match the color shown on the product listing card (its primary
        // image), so navigating from the card to this page doesn't land
        // on a different color than what the shopper just saw.
        $preferredVariant = $initialColorId
            ? $product->variants->firstWhere('color_id', $initialColorId)
            : null;

        $firstVariant = $preferredVariant ?? $product->variants->first();
        $this->selectedColorId = $firstVariant?->color_id;
        $this->selectedSizeId = $firstVariant?->size_id;
    }

    #[On('color-picked')]
    public function selectColor(?int $colorId): void
    {
        $this->selectedColorId = $colorId;
        $this->feedback = null;
    }

    public function selectSize(?int $sizeId): void
    {
        $this->selectedSizeId = $sizeId;
        $this->feedback = null;
    }

    public function getCurrentVariantProperty()
    {
        return $this->product->variants->first(
            fn ($v) => $v->color_id === $this->selectedColorId && $v->size_id === $this->selectedSizeId
        );
    }

    public function addToCart(string $intent = 'cart')
    {
        $this->success = false;

        $variant = $this->currentVariant;

        if (! $variant) {
            $this->feedback = 'Veuillez sélectionner une couleur et une taille.';

            return null;
        }

        if ($variant->stock < $this->quantity) {
            $this->feedback = 'Stock insuffisant pour cette sélection.';

            return null;
        }

        try {
            app(AddToCartAction::class)->execute($variant->id, $this->quantity);
        } catch (\RuntimeException $e) {
            $this->feedback = $e->getMessage();

            return null;
        }

        $this->dispatch('cart-updated');
        $this->success = true;
        $this->feedback = 'Ajouté au panier avec succès.';

        if ($intent === 'buy_now') {
            return redirect()->route('cart.index');
        }

        return null;
    }

    public function render()
    {
        return view('livewire.product-variant-selector');
    }
}
