<?php

namespace App\Http\Controllers;

use App\Filters\RouterFilter;
use App\Http\Requests\StoreRouterRequest;
use App\Http\Requests\UpdateRouterRequest;
use App\Http\Resources\RouterCollection;
use App\Http\Resources\RouterResource;
use App\Models\Router;
use App\Models\Service;
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
        $filter = new RouterFilter;
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
    /**
     * Test de Conexion al Mikrotik
     */
    public function test(Router $router)
    {
        Log::info("Testing router: {$router->ip}");

        try {
            $mkService = new MikrotikService([
                'host' => $router->ip,
                'user' => $router->usuario,
                'pass' => $router->password,
                // 'port' => 8728,
                // 'timeout' => 5,
            ]);

            // Si lanzar aquí una excepción, la captura el catch de abajo
            if (! $mkService->verificarConexion()) {
                return response()->json([
                    'conectado' => false,
                    'mensaje' => 'No se pudo establecer conexión con el router MikroTik',
                ], 200);
            }

            return response()->json([
                'ip' => $router->ip,
                'usuario' => $router->usuario,
                'conectado' => true,
                'mensaje' => 'Conexión exitosa',
                'system_info' => $mkService->getDataMK(),
            ], 200);

        } catch (\Throwable $e) {
            Log::error('Error en test de Mikrotik: '.$e->getMessage());

            return response()->json([
                'conectado' => false,
                'mensaje' => 'Error de conexión con MikroTik: '.$e->getMessage(),
            ], 200); // o 500 si quieres tratarlo como error duro
        }
    }

    /**
     * Sincroniza contratos con el router MikroTik
     */
    public function sincronizarContratos(Router $router)
    {
        try {
            Log::info("Iniciando sincronización para router: {$router->ip}");

            // Crear instancia del servicio MikroTik
            $mkService = new MikrotikService([
                'host' => $router->ip,
                'user' => $router->usuario,
                'pass' => $router->password,
            ]);

            if (! $mkService->verificarConexion()) {
                throw new \Exception('No se pudo establecer conexión con el router MikroTik');
            }

            // 1. Obtener una lista de todos los user_pppoe que tienen al menos UN contrato activo.
            // Estos usuarios NUNCA deben ser procesados.
            $usuariosConContratoActivo = Service::where('router_id', $router->id)
                ->where('status', 'activo')
                ->whereNotNull('user_pppoe')
                ->pluck('user_pppoe')
                ->unique();

            // 2. Obtener todos los contratos 'terminado' o 'suspendido' que no pertenezcan a usuarios activos.
            $contratosAProcesar = Service::where('router_id', $router->id)
                ->whereIn('status', ['terminado', 'suspendido'])
                ->whereNotNull('user_pppoe')
                ->whereNotIn('user_pppoe', $usuariosConContratoActivo)
                ->get();

            // Obtener usuarios configurados en MikroTik
            $usuariosConfiguradosMK = collect($mkService->getUsuariosConfigurados());
            Log::info('Usuarios configurados en MK: '.$usuariosConfiguradosMK->implode(', '));

            $procesados = 0;

            // --- INICIO DE LA MODIFICACIÓN ---

            // 3. Agrupar los contratos por usuario para tomar una única decisión por cada uno.
            $contratosAgrupados = $contratosAProcesar->groupBy('user_pppoe');

            foreach ($contratosAgrupados as $user_pppoe => $contratosDelUsuario) {
                try {
                    // Decisión: Si el usuario tiene al menos un contrato 'suspendido', se suspende.
                    // La suspensión tiene prioridad sobre la terminación.
                    if ($contratosDelUsuario->contains('status', 'suspendido')) {
                        $mkService->desactivarUsuario($user_pppoe);
                        Log::info("Usuario PPPoE suspendido: {$user_pppoe} (prioridad sobre terminados)");
                    } else {
                        // Si no tiene 'suspendido', significa que todos sus contratos en este grupo son 'terminado'.
                        $mkService->removeUsuario($user_pppoe);
                        Log::info("Usuario PPPoE eliminado: {$user_pppoe}");
                    }
                    $procesados++;
                } catch (\Exception $e) {
                    Log::error("Error procesando usuario {$user_pppoe}: ".$e->getMessage());

                    continue;
                }
            }

            // --- FIN DE LA MODIFICACIÓN ---

            // 4. Identificar discrepancias: Usuarios que están en MikroTik pero no deberían estar activos.
            $discrepancias = $usuariosConfiguradosMK->diff($usuariosConContratoActivo);
            Log::info('Discrepancias encontradas => '.$discrepancias->implode(', '));

            return response()->json([
                'success' => true,
                'message' => 'Sincronización completada',
                'procesados' => $procesados,
                'total_usuarios_a_procesar' => $contratosAgrupados->count(),
                'usuarios_discrepantes' => $discrepancias->values(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error en sincronización: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
