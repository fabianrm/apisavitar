<?php

namespace App\Http\Controllers;

use App\Filters\RouterFilter;
use App\Http\Resources\RouterCollection;
use App\Http\Resources\RouterResource;
use App\Models\Router;
use App\Http\Requests\StoreRouterRequest;
use App\Http\Requests\UpdateRouterRequest;
use App\Services\MikrotikService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RouterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filter = new RouterFilter();
        $queryItems = $filter->transform($request);

        $routers = Router::where($queryItems);

        $routers = Router::all();
        return new RouterCollection($routers);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRouterRequest $request)
    {
        return new RouterResource(Router::create($request->all()));
    }

    /**
     * Display the specified resource.
     */
    public function show(Router $router)
    {
        return new RouterResource($router);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Router $router)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRouterRequest $request, Router $router)
    {
        $router->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Router $router)
    {
        //
    }


    /**
     * Test de Conexion al Mikrotik
     */
    public function test(Router $router)
    {
        Log::info("Testing router: $router->ip");

        $mkService = new MikrotikService([
            'host' => $router->ip,
            'user' => $router->usuario,
            'pass' => $router->password
        ]);

        if (!$mkService->verificarConexion()) {
            return response()->json([
                'error' => 'No se pudo establecer conexión con el router MikroTik'
            ], 500);
        }

        return response()->json([
            'ip' => $router->ip,
            'usuario' => $router->usuario,
            'conectado' => true,
            'mensaje' => 'Conexión exitosa',
            'system_info' => $mkService->getDataMK()
        ]);
    }

    /**
     * Sincroniza contratos con el router MikroTik
     */
    public function sincronizarContratos(Router $router)
    {
        try {
            $resultado = MikrotikService::sincronizarEstadosContratos($router->id);
            return response()->json($resultado);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'success' => false
            ], 500);
        }
    }
}
