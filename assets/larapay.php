<?php

return [
    'route_prefix' => 'payments',

    'directories' => [
        'App\\Gateways' => base_path('app/Gateways'),
    ],

    'suggested_gateways' => [
        'laravelpay/gateway-paypal' => 'paypal',
        'laravelpay/gateway-paypal-ipn' => 'paypal-ipn',
        'laravelpay/gateway-stripe' => 'stripe',
        'laravelpay/gateway-mollie' => 'mollie',
        'laravelpay/gateway-tebex' => 'tebex',
        'laravelpay/gateway-bitpave' => 'bitpave',
        'laravelpay/gateway-paybylink' => 'paybylink',
        'laravelpay/subscriptions-paypal' => 'paypal-subscriptions',
        'laravelpay/subscriptions-stripe' => 'stripe-subscriptions',
    ],
];
