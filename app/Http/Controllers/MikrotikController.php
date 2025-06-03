<?php

namespace App\Http\Controllers;

use App\Services\MikrotikService;
use Illuminate\Http\Request;

class MikrotikController extends Controller
{

    public function crearUsuario()
    {
        $mikrotik = new MikrotikService();
        $respuesta = $mikrotik->crearUsuarioPPP('cliente123', 'clave123');

        return response()->json($respuesta);
    }

    public function cortarUsuario()
    {
        $mikrotik = new MikrotikService();
        $respuesta = $mikrotik->deshabilitarUsuarioPPP('cliente123');

        return response()->json($respuesta);
    }
}
