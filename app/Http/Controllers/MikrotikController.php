<?php

namespace App\Http\Controllers;

use App\Services\MikrotikService;
use Illuminate\Http\Request;

class MikrotikController extends Controller
{

    //Crear usuario
    public function crearUsuario()
    {
        $mikrotik = new MikrotikService();
        $respuesta = $mikrotik->crearUsuarioPPP('lucas', 'lucas123', "PLAN HOGAR");

        return response()->json($respuesta);
    }

    //Suspender usuario
    public function cortarUsuario()
    {
        $mikrotik = new MikrotikService();
        $respuesta = $mikrotik->deshabilitarUsuarioPPP('cliente123');

        return response()->json($respuesta);
    }

    //Obtener los perfiles (planes)
    public function listarPerfiles(MikrotikService $mikrotik)
    {
        $perfiles = $mikrotik->obtenerPerfilesPPP();
        return response()->json($perfiles);
    }
}
