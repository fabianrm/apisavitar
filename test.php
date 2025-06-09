<?php
require 'vendor/autoload.php';

use RouterOS\Client;
use RouterOS\Query;

// Configura tu acceso al MikroTik
$client = new Client([
    'host' => '10.100.100.2', //Ip del servidor VPN si el test es en la nube, sino ip local
    'user' => 'fabianrm',
    'pass' => '*binroot*'
]);

$query = new Query('/system/resource/print');
$response = $client->query($query)->read();

print_r($response);
