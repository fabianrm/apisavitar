<?php

return [
    'server_public_key' => env('WG_SERVER_PUBLIC_KEY'),
    'endpoint_host' => env('WG_ENDPOINT_HOST', '13.140.189.137'),
    'endpoint_port' => env('WG_ENDPOINT_PORT', 51820),
    'interface' => env('WG_INTERFACE', 'wg0'),
    'subnet_prefix' => env('WG_SUBNET_PREFIX', '10.100.100.'),
    'subnet_cidr' => env('WG_SUBNET_CIDR', 24),
    'add_peer_command' => env('WG_ADD_PEER_COMMAND', '/usr/local/sbin/savitar-wg-add-peer'),
    'wg_binary' => env('WG_BINARY', '/usr/bin/wg'),
    'api_group' => env('WG_API_GROUP', 'savitar-api'),
    'api_username' => env('WG_API_USERNAME', 'savitarapi'),
    'api_port' => env('WG_API_PORT', 8728),
];
