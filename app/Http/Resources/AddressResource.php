<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'full_name' => $this->full_name,
            'phone' => $this->phone,
            'province' => $this->province,
            'city' => $this->city,
            'commune' => $this->commune,
            'detailed_address' => $this->detailed_address,
            'landmark' => $this->landmark,
            'is_default' => $this->is_default,
        ];
    }
}
