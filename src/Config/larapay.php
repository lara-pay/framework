<?php

return [
    'route_prefix' => 'payments',

    'tables' => [
        'gateways' => 'larapay_gateways',
        'payments' => 'larapay_payments',
        'subscriptions' => 'larapay_subscriptions',
    ],

    'directories' => [
        base_path('app/Gateways'),
    ],

    'suggested_gateways' => [
        'laravelpay/gateway-example' => 'example',
    ],
];
