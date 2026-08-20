<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Promotion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PromotionController extends Controller
{
    public function index(): View
    {
        $promotions = Promotion::withCount('products')->orderByDesc('created_at')->get();

        return view('admin.promotions.index', compact('promotions'));
    }

    public function create(): View
    {
        $products = Product::orderBy('name')->get();

        return view('admin.promotions.form', ['promotion' => new Promotion, 'products' => $products]);
    }

    private function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:percentage,fixed'],
            'value' => ['required', 'integer', 'min:1'],
            'min_purchase' => ['nullable', 'integer', 'min:0'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'products' => ['array'],
            'products.*' => ['exists:products,id'],
        ];
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $validated['is_active'] = $request->boolean('is_active', true);
        $products = $validated['products'] ?? [];
        unset($validated['products']);

        $promotion = Promotion::create($validated);
        $promotion->products()->sync($products);

        return redirect()->route('admin.promotions.index')->with('status', 'Promotion créée.');
    }

    public function edit(Promotion $promotion): View
    {
        $products = Product::orderBy('name')->get();
        $promotion->load('products');

        return view('admin.promotions.form', compact('promotion', 'products'));
    }

    public function update(Request $request, Promotion $promotion): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $validated['is_active'] = $request->boolean('is_active', true);
        $products = $validated['products'] ?? [];
        unset($validated['products']);

        $promotion->update($validated);
        $promotion->products()->sync($products);

        return redirect()->route('admin.promotions.index')->with('status', 'Promotion mise à jour.');
    }

    public function destroy(Promotion $promotion): RedirectResponse
    {
        $promotion->delete();

        return redirect()->route('admin.promotions.index')->with('status', 'Promotion supprimée.');
    }
}
