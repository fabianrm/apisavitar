<?php

return [
    'default' => [
        'host' => env('MIKROTIK_HOST', '192.168.5.1'),
        'user' => env('MIKROTIK_USERNAME', 'fabianrm'),
        'pass' => env('MIKROTIK_PASSWORD', '*binroot*'),
        'port' => env('MIKROTIK_PORT', 8728),
        'timeout' => env('MIKROTIK_TIMEOUT', 10),
        'ssl'      => (bool) env('MIKROTIK_SSL', false),
        'legacy'   => (bool) env('MIKROTIK_LEGACY', false),
    ],
];
