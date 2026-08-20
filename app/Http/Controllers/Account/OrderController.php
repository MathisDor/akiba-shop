<?php

namespace App\Http\Controllers\Account;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::guard('web')->user();

        $query = $user->orders()->with('items')->latest();

        if ($request->query('filter') === 'in_progress') {
            $query->whereIn('status', array_map(fn ($s) => $s->value, OrderStatus::inProgress()));
        } elseif ($request->query('filter') === 'delivered') {
            $query->where('status', OrderStatus::Livree->value);
        }

        $orders = $query->paginate(9)->withQueryString();

        $counts = [
            'all' => $user->orders()->count(),
            'in_progress' => $user->orders()->whereIn('status', array_map(fn ($s) => $s->value, OrderStatus::inProgress()))->count(),
            'delivered' => $user->orders()->where('status', OrderStatus::Livree->value)->count(),
        ];

        return view('account.orders', compact('orders', 'counts'));
    }

    public function show(Order $order): View
    {
        Gate::authorize('view', $order);

        $order->load('items.variant.product.images', 'statusHistories', 'delivery.zone', 'payments');

        return view('account.order-show', compact('order'));
    }
}
