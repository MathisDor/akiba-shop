<?php

use App\Http\Controllers\Webhooks\EbillingCallbackController;
use Illuminate\Support\Facades\Route;

// API v1 routes are registered in routes/api_v1.php and included below.
require __DIR__.'/api_v1.php';

// External payment gateway webhooks — unauthenticated (called server-to-server
// by the provider), validated by business data (order reference + amount).
Route::post('/webhooks/ebilling/callback', EbillingCallbackController::class);
