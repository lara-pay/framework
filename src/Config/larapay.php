<?php

return [
    'route_prefix' => 'payments',

    'directories' => [
        base_path('app/Gateways'),
    ],

    'suggested_gateways' => [
        'lara-pay/gateway-example' => 'example',
    ],
];
