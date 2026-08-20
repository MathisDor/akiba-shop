<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AddressController extends Controller
{
    private function rules(): array
    {
        return [
            'label' => ['required', 'string', 'max:255'],
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'province' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'commune' => ['nullable', 'string', 'max:255'],
            'detailed_address' => ['required', 'string', 'max:255'],
            'landmark' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        return AddressResource::collection($request->user()->addresses()->orderByDesc('is_default')->get());
    }

    public function store(Request $request): AddressResource
    {
        $validated = $request->validate($this->rules());
        $user = $request->user();

        $address = $user->addresses()->create($validated + ['is_default' => $user->addresses()->count() === 0]);

        return new AddressResource($address);
    }

    public function update(Request $request, Address $address): AddressResource|JsonResponse
    {
        if ($address->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        $address->update($request->validate($this->rules()));

        return new AddressResource($address);
    }

    public function destroy(Request $request, Address $address): JsonResponse
    {
        if ($address->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        $address->delete();

        return response()->json(['message' => 'Adresse supprimée.']);
    }
}
