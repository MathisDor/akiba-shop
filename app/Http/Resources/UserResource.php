<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Explicit whitelist of user fields exposed over the API — never hands the raw
 * Eloquent model to the client, so a future column addition can't leak by
 * accident even if the model's $hidden list falls out of sync.
 */
class UserResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'created_at' => $this->created_at,
        ];
    }
}
