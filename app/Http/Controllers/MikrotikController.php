<?php

namespace App\Http\Controllers;

use App\Services\MikrotikService;
use Illuminate\Http\Request;

class MikrotikController extends Controller
{

    // protected $mikrotik;

    // public function __construct(MikrotikService $mikrotik)
    // {
    //     $this->mikrotik = $mikrotik;
    // }


    //Crear usuario
    public function crearUsuario()
    {

        // $response = $this->mikrotik->ejecutarComando('/ppp/secret/add', [
        //     'name' => 'lunita',
        //     'password' => 'lunita123',
        //     'service' => 'pppoe',
        //     'profile' => 'default'
        // ]);



        //     $mikrotik = new MikrotikService();
        //     $respuesta = $mikrotik->crearUsuarioPPP('lucas', 'lucas123', "PLAN HOGAR");

        // return response()->json($response);
    }

    //Suspender usuario
    // public function cortarUsuario()
    // {
    //     $mikrotik = new MikrotikService();
    //     $respuesta = $mikrotik->deshabilitarUsuarioPPP('cliente123');

    //     return response()->json($respuesta);
    // }

    //Obtener los perfiles (planes)
    // public function listarPerfiles()
    // {
    // return 'Hola';
    //     $perfiles = $this->mikrotik->obtenerPerfilesPPP();
    //     return response()->json($perfiles);
    // }
}
