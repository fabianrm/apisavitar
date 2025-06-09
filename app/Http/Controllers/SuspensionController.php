<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSuspensionRequest;
use App\Http\Resources\SuspensionCollection;
use App\Http\Resources\SuspensionResource;
use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\Router;
use App\Models\Service;
use App\Models\Suspension;
use App\Services\MikrotikService;
use App\Services\SuspensionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SuspensionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $suspensions = Suspension::with([
            'service',
            // 'service.customers',
            // 'service.plans',
            'user'
        ])->get();

        return new SuspensionCollection($suspensions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSuspensionRequest $request)
    {
        $validatedData = $request->validated();
        $serviceID =  $validatedData['service_id'];

        $contract = Service::findOrFail($serviceID);

        $today = Carbon::today();

        try {
            DB::beginTransaction();

            // Verificar que el servicio esté activo
            if ($contract->status !== 'activo') {
                return response()->json([
                    'message' => 'No se puede suspender un servicio que no está activo'
                ], 400);
            }

            // Verificar que no tenga facturas pendientes
            $hasUnpaidInvoices = Invoice::where('service_id', $contract->id)
                ->where('status', 'pendiente')
                ->where('start_date', '<=', $today)
                ->where('end_date', '>=', $today)
                ->exists();

            if ($hasUnpaidInvoices) {
                return response()->json([
                    'message' => 'No se puede suspender el servicio mientras tenga facturas pendientes'
                ], 400);
            }

            // Validar plazo máximo de 2 meses
            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);

            if ($endDate->diffInMonths($startDate) > 2) {
                return response()->json([
                    'message' => 'El plazo máximo de suspensión es de 2 meses'
                ], 400);
            }

            //Crear y guardar la suspension
            $suspension = Suspension::create($validatedData);

            // Actualizar estado del servicio si es necesario
            $contract->status = 'suspendido';
            $contract->save();

            // Cargar relación 'services' en la suspensión
            $suspension->load('service');

            //Desactivar en MK
            if ($request['mikrotik']) {
                //Agregar cliente a MK
                $router = Router::where('id', $contract->router_id)->firstOrFail();
                Log::info("Router => $router->ip");

                //Conectamos con el MK
                $mkService = new MikrotikService([
                    'host' => $router->ip,
                    'user' => $router->usuario,
                    'pass' => $router->password
                ]);

                // Verificar conexión antes de continuar
                if (!$mkService->verificarConexion()) {
                    throw new \Exception('No se pudo establecer conexión con el router MikroTik');
                }

                //Desactivamos el usuario en MK
                $mkService->desactivarUsuario($suspension->service->user_pppoe);
            }

            DB::commit();

            return response()->json(
                [
                    'message' => 'Suspensión registrada exitosamente',
                    'suspension' => new SuspensionResource($suspension),
                ],
                200
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return response()->json([
                'message' => 'Error al suspender el servicio.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $suspension = Suspension::with(['service'])->findOrFail($id);
        return new SuspensionResource($suspension);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    //reactivation suspension
    public function reactivation(string $id, Request $request)
    {
        try {
            DB::beginTransaction();

            $currentDate = Carbon::now();

            // Obtener la suspensión activa (debería ser una sola)
            $suspension = Suspension::where('service_id', $id)
                ->where('status', true)
                ->first();  // Cambiado de get() a first() porque esperamos un solo registro

            if (!$suspension) {
                throw new \Exception('No se encontró una suspensión activa para este servicio.');
            }

            // Actualizamos la suspensión
            $suspension->status = false;
            $suspension->reactivation_date = $currentDate;
            $suspension->save();

            // Actualizamos el servicio relacionado
            $service = $suspension->service;
            $service->status = 'activo';
            $service->save();

            if ($request['mikrotik']) {
                //Agregar cliente a MK
                $router = Router::where('id', $service->router_id)->firstOrFail();
                Log::info("Router => $router->ip");

                //Conectamos con el MK
                $mkService = new MikrotikService([
                    'host' => $router->ip,
                    'user' => $router->usuario,
                    'pass' => $router->password
                ]);

                // Verificar conexión antes de continuar
                if (!$mkService->verificarConexion()) {
                    throw new \Exception('No se pudo establecer conexión con el router MikroTik');
                }
                //Activamos el usuario en MK
                $mkService->activarUsuario($suspension->service->user_pppoe);
            }

            DB::commit();

            return response()->json([
                'message' => 'Servicio reactivado correctamente.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return response()->json([
                'message' => 'Error al reactivar el servicio.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
