<?php

namespace LaraPay\Framework\Foundation;

use LaraPay\Framework\Helpers\GatewayFoundationHelpers;

abstract class GatewayFoundation
{
    use GatewayFoundationHelpers;
    /**
     * Define the gateway identifier. This identifier should be unique. For example,
     * if the gateway name is "PayPal Express", the gateway identifier should be "paypal_express".
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

    public function __construct()
    {

    }
}
