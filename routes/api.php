<?php

use App\Http\Controllers\Webhooks\EbillingCallbackController;
use Illuminate\Support\Facades\Route;

// API v1 routes are registered in routes/api_v1.php and included below.
require __DIR__.'/api_v1.php';

// External payment gateway webhooks — unauthenticated (called server-to-server
// by the provider), validated by business data (order reference + amount).
// Throttled anyway: it's a public endpoint and a legitimate provider never
// bursts anywhere near 60 callbacks/min for this shop's volume.
Route::post('/webhooks/ebilling/callback', EbillingCallbackController::class)
    ->middleware('throttle:60,1');
