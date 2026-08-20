<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::whereHas('role', fn ($q) => $q->where('slug', 'client'))
            ->withCount('orders')
            ->withSum('orders as total_spent', 'total');

        if ($request->filled('q')) {
            $term = $request->query('q');
            $query->where(fn ($q) => $q->where('name', 'like', "%{$term}%")->orWhere('email', 'like', "%{$term}%"));
        }

        $customers = $query->latest()->paginate(15)->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    public function show(User $user): View
    {
        $user->load(['orders' => fn ($q) => $q->latest(), 'addresses']);

        return view('admin.customers.show', ['customer' => $user]);
    }
}
