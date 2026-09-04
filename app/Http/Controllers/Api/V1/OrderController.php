<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\PlaceOrderAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Address;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class OrderController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $orders = $request->user()->orders()->with('items')->latest()->paginate(10);

        return OrderResource::collection($orders);
    }

    public function show(Request $request, Order $order): OrderResource
    {
        Gate::authorize('view', $order);

        $order->load('items', 'statusHistories');

        return new OrderResource($order);
    }

    public function store(StoreOrderRequest $request, PlaceOrderAction $placeOrder): OrderResource|JsonResponse
    {
        $validated = $request->validated();

        $address = Address::where('user_id', $request->user()->id)->findOrFail($validated['address_id']);

        try {
            $order = $placeOrder->execute(
                user: $request->user(),
                address: $address,
                deliveryMethod: $validated['delivery_method'],
                deliveryZoneId: $validated['delivery_zone_id'] ?? null,
                paymentMethod: $validated['payment_method'],
                couponCode: $validated['coupon_code'] ?? null,
                paymentPhone: $validated['payment_phone'] ?? null,
            );
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return new OrderResource($order);
    }
}
