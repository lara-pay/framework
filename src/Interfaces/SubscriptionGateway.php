<?php

namespace LaraPay\Framework\Interfaces;

use LaraPay\Framework\Helpers\GatewayFoundationHelper;
use LaraPay\Framework\Helpers\SubscriptionGatewayHelper;
use Illuminate\Http\Request;

abstract class SubscriptionGateway
{
    use GatewayFoundationHelper, SubscriptionGatewayHelper;

    /**
     * Define the gateway identifier. This identifier should be unique. For example,
     * if the gateway name is "PayPal Express", the gateway identifier should be "paypal-express".
     *
     * @var string
     */
    protected string $identifier;

    /**
     * Define the gateway version.
     *
     * @var string
     */
    protected string $version = '1.0.0';

    /**
     * Define the supported currencies
     *
     * @var array
     */
    protected array $currencies = [];

    public function __construct() {}

    abstract public function subscribe($subscription);

    abstract public function callback(Request $request);

    abstract public function check($subscription): bool;
}
