<?php

namespace App\Http\Controllers;

use App\Services\MikrotikService;
use Illuminate\Http\Request;

class MikrotikController extends Controller
{

    public function checkConecction(): void
    {

        //Conectamos con el MK
        $mkService = new MikrotikService([
            'host' => '192.168.1.5',
            'user' => 'fabianrm',
            'pass' => '*binroot*'
        ]);

        // Verificar conexión antes de continuar
        if (!$mkService->verificarConexion()) {
            throw new \Exception('No se pudo establecer conexión con el router MikroTik');
        }
    }
}
