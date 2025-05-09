<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSuspensionRequest;
use App\Http\Resources\SuspensionCollection;
use App\Http\Resources\SuspensionResource;
use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\Service;
use App\Models\Suspension;
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
        $suspensions = Suspension::with(['service'])->get();
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

            DB::commit();

            return response()->json(
                [
                    'message' => 'Suspensión registrada exitosamente',
                    'suspension' => new SuspensionResource($suspension)
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
    public function reactivation(string $id)
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
