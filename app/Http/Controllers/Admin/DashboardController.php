<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $today = Carbon::today();

        $revenue = Order::whereIn('status', ['payee', 'preparation', 'prete_a_expedier', 'en_livraison', 'livree', 'terminee'])->sum('total');
        $ordersToday = Order::whereDate('created_at', $today)->count();
        $ordersPending = Order::where('status', OrderStatus::EnAttentePaiement->value)->count();
        $ordersDelivered = Order::where('status', OrderStatus::Livree->value)->count();
        $productsSold = OrderItem::whereHas('order', fn ($q) => $q->whereIn('status', ['payee', 'preparation', 'prete_a_expedier', 'en_livraison', 'livree', 'terminee']))->sum('quantity');
        $customersCount = User::whereHas('role', fn ($q) => $q->where('slug', 'client'))->count();
        $lowStock = ProductVariant::where('stock', '>', 0)->where('stock', '<=', 5)->with('product', 'color', 'size')->take(8)->get();

        $bestSellers = Product::withSum(['variants as sold' => function ($q) {
            $q->join('order_items', 'order_items.product_variant_id', '=', 'product_variants.id');
        }], 'order_items.quantity')
            ->with('images')
            ->orderByDesc('sold')
            ->take(5)
            ->get();

        $salesByDay = Order::selectRaw('DATE(created_at) as day, SUM(total) as total')
            ->where('created_at', '>=', $today->copy()->subDays(6))
            ->whereIn('status', ['payee', 'preparation', 'prete_a_expedier', 'en_livraison', 'livree', 'terminee'])
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        return view('admin.dashboard', compact(
            'revenue', 'ordersToday', 'ordersPending', 'ordersDelivered',
            'productsSold', 'customersCount', 'lowStock', 'bestSellers', 'salesByDay'
        ));
    }
}
