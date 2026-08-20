<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class OrderNumberGenerator
{
    public function generate(): string
    {
        $datePart = Carbon::now()->format('Ymd');

        return DB::transaction(function () use ($datePart) {
            $countToday = Order::where('order_number', 'like', "AKB-{$datePart}-%")->lockForUpdate()->count();
            $sequence = str_pad((string) ($countToday + 1), 5, '0', STR_PAD_LEFT);

            return "AKB-{$datePart}-{$sequence}";
        });
    }
}
