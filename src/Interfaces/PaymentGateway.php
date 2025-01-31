<?php

namespace LaraPay\Framework\Interfaces;

use Illuminate\Http\Request;
use LaraPay\Framework\Helpers\PaymentGatewayHelper;

abstract class PaymentGateway extends GatewayFoundation
{
    use PaymentGatewayHelper;

    public function __construct()
    {
        parent::__construct();
    }

    abstract public function pay($payment);

    abstract public function callback(Request $request);
}
