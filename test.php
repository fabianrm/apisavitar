<?php
require 'vendor/autoload.php';

use RouterOS\Client;
use RouterOS\Query;

// Configura tu acceso al MikroTik
$client = new Client([
    'host' => '192.168.1.5',
    'user' => 'fabianrm',
    'pass' => '*binroot*'
]);

$query = new Query('/system/resource/print');
$response = $client->query($query)->read();

print_r($response);
