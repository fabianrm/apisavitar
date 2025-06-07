<?php
// require 'vendor/autoload.php';

// use RouterOS\Client;
// use RouterOS\Query;

// // Configura tu acceso al MikroTik
// $client = new Client([
//     'host' => '192.168.1.5',
//     'user' => 'fabianrm',
//     'pass' => '*binroot*'
// ]);

// $query = new Query('/system/resource/print');
// $response = $client->query($query)->read();

// print_r($response);

// En un controlador

use App\Http\Controllers\Controller;
use App\Services\MikrotikService;

class MikrotikController extends Controller
{
    protected $mikrotik;

    public function __construct(MikrotikService $mikrotik)
    {
        $this->mikrotik = $mikrotik;
    }

    public function listarPerfiles()
    {
        $perfiles = $this->mikrotik->obtenerPerfilesPPP();
        return response()->json([
            'data' => [
                'perfiles' => $perfiles,
                'message' => 'Perfiles cargados correctamente.'
            ]
        ], 400);
    }

    public function crearUsuarioPPP()
    {
        $response = $this->mikrotik->ejecutarComando('/ppp/secret/add', [
            'name' => 'nuevo_usuario',
            'password' => 'clave_segura',
            'service' => 'pppoe',
            'profile' => 'default'
        ]);

        // Procesar respuesta...
    }
}
