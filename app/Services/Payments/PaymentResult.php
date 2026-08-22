<?php

namespace App\Services\Payments;

class PaymentResult
{
    public function __construct(
        public readonly bool $success,
        public readonly ?string $reference = null,
        public readonly array $raw = [],
        /** True when the provider confirms payment asynchronously (e.g. USSD push awaiting operator confirmation). */
        public readonly bool $pending = false,
    ) {}
}
