<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FavoriteController extends Controller
{
    public function index(): View
    {
        $products = Auth::guard('web')->user()
            ->favoriteProducts()
            ->with(['images', 'variants', 'colors', 'promotions', 'favoritedBy'])
            ->get();

        $recommended = Product::recommended(
            excludeIds: $products->pluck('id'),
            categoryIds: $products->pluck('category_id'),
        );

        return view('account.favorites', compact('products', 'recommended'));
    }

    public function toggle(Product $product): RedirectResponse
    {
        $user = Auth::guard('web')->user();

        if ($user->favoriteProducts()->where('product_id', $product->id)->exists()) {
            $user->favoriteProducts()->detach($product->id);
        } else {
            $user->favoriteProducts()->attach($product->id);
        }

        return back();
    }
}
