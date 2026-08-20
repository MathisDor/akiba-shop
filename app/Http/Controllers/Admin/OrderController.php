<?php

namespace App\Http\Controllers\Admin;

use App\Actions\UpdateOrderStatusAction;
use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $query = Order::with('user')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('q')) {
            $term = $request->query('q');
            $query->where(function ($q) use ($term) {
                $q->where('order_number', 'like', "%{$term}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$term}%")->orWhere('email', 'like', "%{$term}%"));
            });
        }

        $orders = $query->paginate(15)->withQueryString();
        $statuses = OrderStatus::cases();

        return view('admin.orders.index', compact('orders', 'statuses'));
    }

    public function show(Order $order): View
    {
        $order->load('items.variant.product.images', 'statusHistories.createdBy', 'payments', 'delivery.zone', 'user', 'coupon');

        return view('admin.orders.show', ['order' => $order, 'statuses' => OrderStatus::cases()]);
    }

    public function updateStatus(Request $request, Order $order, UpdateOrderStatusAction $action): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string'],
            'note' => ['nullable', 'string'],
        ]);

        $status = OrderStatus::from($validated['status']);
        $action->execute($order, $status, $validated['note'] ?? null);

        return back()->with('status', 'Statut de la commande mis à jour.');
    }
}
