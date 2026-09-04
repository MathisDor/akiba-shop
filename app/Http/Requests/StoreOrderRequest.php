<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Checkout payload for the REST API (POST /api/v1/orders).
 *
 * When the customer pays by mobile money the operator needs the payer MSISDN
 * to fire the USSD push, so `payment_phone` becomes required and is checked
 * against the same Airtel/Moov prefixes the Livewire checkout enforces
 * (App\Livewire\Checkout\Paiement).
 */
class StoreOrderRequest extends FormRequest
{
    /**
     * 9-digit local MSISDN prefixes per mobile money operator.
     *
     * @var array<string, array<int, string>>
     */
    private const MOBILE_MONEY_PREFIXES = [
        'airtel' => ['074', '076', '077'],
        'moov' => ['065', '066'],
    ];

    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        $rules = [
            'address_id' => ['required', 'integer', 'exists:addresses,id'],
            'delivery_method' => ['required', 'in:domicile,point_relais,boutique'],
            'delivery_zone_id' => ['nullable', 'integer', 'exists:delivery_zones,id'],
            'payment_method' => ['required', 'in:airtel,moov,card'],
            'coupon_code' => ['nullable', 'string'],
            'payment_phone' => ['nullable', 'string'],
        ];

        $method = $this->input('payment_method');

        if (is_string($method) && isset(self::MOBILE_MONEY_PREFIXES[$method])) {
            $prefixes = implode('|', self::MOBILE_MONEY_PREFIXES[$method]);
            $rules['payment_phone'] = ['required', 'string', 'regex:/^('.$prefixes.')\d{6}$/'];
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'payment_phone.required' => 'Le numéro mobile money est requis pour ce mode de paiement.',
            'payment_phone.regex' => 'Numéro mobile money invalide (préfixe opérateur suivi de 6 chiffres).',
        ];
    }
}
