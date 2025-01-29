<?php

namespace LaraPay\Framework\Foundation;

abstract class GatewayFoundation
{
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
     * Define the authors of the gateway.
     *
     * @var array
     */
    protected array $authors = [];

    public function __construct()
    {

    }
}
