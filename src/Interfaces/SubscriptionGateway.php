<?php

namespace LaraPay\Framework\Interfaces;

use LaraPay\Framework\Helpers\SubscriptionGatewayHelper;
use Illuminate\Http\Request;

abstract class SubscriptionGateway extends GatewayFoundation
{
    use SubscriptionGatewayHelper;

    public function __construct()
    {
        parent::__construct();
    }

    abstract public function subscribe($subscription);

    abstract public function callback(Request $request);

    abstract public function check($subscription): bool;
}
