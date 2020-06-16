<?php

declare(strict_types=1);
return [
    'consul' => [
        'url' => env('CONSUL_URL', 'http://127.0.0.1:8500'),
        'enable' => (int) env('CONSUL_ENABLE', 1),
        'net_card' => env('CONSUL_NET_CARD', ''),
    ],
];
