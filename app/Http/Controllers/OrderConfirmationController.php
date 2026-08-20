<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class OrderConfirmationController extends Controller
{
    public function __invoke(Order $order): View
    {
        Gate::authorize('view', $order);

        return view('orders.confirmation', compact('order'));
    }
}
