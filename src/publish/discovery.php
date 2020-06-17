<?php

declare(strict_types=1);
return [
    'consul' => [
        'url' => env('DISCOVERY_CONSUL_URL', 'http://127.0.0.1:8500'),
        'enable' => (int) env('DISCOVERY_CONSUL_ENABLE', 0),
        'net_card' => env('DISCOVERY_CONSUL_NET_CARD', ''),
    ],
];
