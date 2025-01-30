<?php

namespace LaraPay\Framework\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use LaraPay\Framework\Payment;
use LaraPay\Framework\Gateway;

class PaymentController extends Controller
{
    public function pay($token)
    {
        $payment = Payment::findOrFail(
            Cache::get($token)
        );

        return $payment->payWith($payment->gateway->id);
    }

    public function listener(Request $request, $gatewayId)
    {
        $gateway = Gateway::where('identifier', $gatewayId)->firstOrFail();

        return $gateway->callback($request);
    }
}
