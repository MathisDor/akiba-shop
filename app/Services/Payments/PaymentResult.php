<?php

namespace App\Services\Payments;

class PaymentResult
{
    public function __construct(
        public readonly bool $success,
        public readonly ?string $reference = null,
        public readonly array $raw = [],
    ) {}
}
