<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Shared validation for creating/updating a delivery address, used by both the
 * web account area (App\Http\Controllers\Account\AddressController) and the REST
 * API (App\Http\Controllers\Api\V1\AddressController).
 *
 * Ownership on update/delete is enforced separately through AddressPolicy
 * (Gate::authorize) because the route-model-bound {address} isn't available
 * on the create path and the two entry points bind it differently.
 */
class AddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
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
}
