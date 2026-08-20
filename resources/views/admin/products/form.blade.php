<x-layouts.admin active="products" :title="$product ? 'Modifier le produit' : 'Nouveau produit'">
    <livewire:admin.product-form :product="$product" />
</x-layouts.admin>
