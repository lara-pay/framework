<?php

use Illuminate\Support\Facades\Route;
use LaraPay\Framework\Http\Controllers\PaymentController;

// create route group with prefix /payments
Route::prefix(config('larapay.route_prefix', 'payments'))->group(function () {
    Route::get('pay/{token}', [PaymentController::class, 'pay'])->name('larapay.pay');
    Route::any('webhooks/{gateway_id}', [PaymentController::class, 'listener'])->name('larapay.webhook');
});
