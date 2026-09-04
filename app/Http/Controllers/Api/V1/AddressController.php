<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddressRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class AddressController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return AddressResource::collection($request->user()->addresses()->orderByDesc('is_default')->get());
    }

    public function store(AddressRequest $request): AddressResource
    {
        $user = $request->user();

        $address = $user->addresses()->create(
            $request->validated() + ['is_default' => $user->addresses()->count() === 0]
        );

        return new AddressResource($address);
    }

    public function update(AddressRequest $request, Address $address): AddressResource
    {
        Gate::authorize('update', $address);

        $address->update($request->validated());

        return new AddressResource($address);
    }

    public function destroy(Request $request, Address $address): JsonResponse
    {
        Gate::authorize('delete', $address);

        $address->delete();

        return response()->json(['message' => 'Adresse supprimée.']);
    }
}
