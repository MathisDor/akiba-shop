<a href="{{ route('cart.index') }}" class="text-primary hover:opacity-70 transition-opacity relative" aria-label="Panier">
<x-icon name="shopping_bag" />
    @if($count > 0)
        <span class="absolute -top-1 -right-1 bg-primary-container text-on-primary-container rounded-full w-4 h-4 flex items-center justify-center font-label-sm text-[10px]">{{ $count }}</span>
    @endif
</a>
