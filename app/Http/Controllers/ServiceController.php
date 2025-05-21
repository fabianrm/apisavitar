<?php

namespace App\Http\Controllers;

use App\Exceptions\ModelNotFoundException;
use App\Http\Resources\ServiceCollection;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use App\Models\Box;
use App\Models\Invoice;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Services\UtilService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $service = Service::with(['customers', 'routers', 'plans', 'cities'])->orderBy('created_at', 'desc')->get();
        return new ServiceCollection($service);
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
    public function store(StoreServiceRequest $request)
    {
        $contractService = app(UtilService::class);
        // Genera un código único para el cliente
        $uniqueCode = $contractService->generateUniqueCodeService('CT');
        //Actualiza los puertos disponibles de las cajas
        $service = new Service($request->all());
        $service->service_code = $uniqueCode;
        $service->save();
        // Calcular los puertos disponibles
        $box = Box::find($service->box_id);
        $box->calculateAvailablePorts();
        return new ServiceResource($service);
        //return new ServiceResource(Service::create($request->all()));
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {

        // $service = Service::with(['customers', 'plans', 'boxes', 'cities', 'equipments'])->findOrFail($service->id);
        // return new ServiceResource($service);

        $service = Service::find($service->id);

        if (!$service) {
            throw new ModelNotFoundException('Service not found');
        }
        return new ServiceResource($service);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateServiceRequest $request, Service $service)
    {
        $service->update($request->all());
        //Log::info($request->all());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        //Buscamos que el contrato no tenga facturas
        $hasInvoices = Invoice::where('service_id', $service->id)->exists();

        if ($hasInvoices) {
            return response()->json([
                'status' => false,
                'message' => 'No se puede eliminar el contrato. Tiene facturas asociadas.'
            ], 400);
        }


        //Eliminar el contrato
        $service->deleteOrFail();

        return response()->json([
            'status' => true,
            'message' => 'Contrato eliminado correctamente'
        ]);
    }

    //Terminar Contrato
    public function terminate(string $id)
    {

        try {
            $today = Carbon::now();

            $service = Service::findOrFail($id);

            $service->status = 'terminado';
            $service->box_id = null;
            $service->port_number = null;
            $service->equipment_id = null;
            $service->observation = 'Terminado por usuario con id #' . auth()->user()->id . ' el ' . $today;
            $service->end_date = $today;

            $service->save();

            return response()->json(
                [
                    'message' => 'Contrato terminado exitosamente',
                ],
                200
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return response()->json([
                'message' => 'Error al terminar el contrato.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Obtener los puertos disponibles en la caja.
     */
    public function getPorts($box_id)
    {
        $results = DB::select('CALL obtenerPuertosDisponibles(?)', [$box_id]);
        return response()->json($results);
    }

    /****
     * Cambiar de Plan
     */

    public function updatePlan(Request $request, Service $contract)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
        ]);

        $equipo = $contract->equipment_id;

        // Finalizamos el contrato actual
        $contract->update([
            'status' => 'terminado',
            'equipment_id' => NULL,
            'end_date' => now(),
        ]);

        //Creamos un nuevo contrato con el nuevo plan
        $contractService = app(UtilService::class);

        // Genera un código único para el cliente
        $uniqueCode = $contractService->generateUniqueCodeService('CT');

        // $service = new Service($request->all());
        // $service->service_code = $uniqueCode;

        $newInstallationDate = Carbon::parse($contract->installation_date)
            ->month(Carbon::now()->month)
            ->toDateString();

        $newContract = Service::create([
            'service_code' => $uniqueCode,
            'enterprise_id' => $contract->enterprise_id,
            'customer_id' => $contract->customer_id,
            'plan_id' => $request->plan_id,
            'router_id' => $contract->router_id,
            'box_id' => $contract->box_id,
            'port_number' => $contract->port_number,
            'equipment_id' => $equipo,
            'city_id' => $contract->city_id,
            'address_installation' => $contract->address_installation,
            'reference' => $contract->reference,
            'registration_date' => now(),
            'installation_date' => $newInstallationDate,
            'latitude' => $contract->latitude,
            'longitude' => $contract->longitude,
            'billing_date' => $contract->billing_date,
            'due_date' => $contract->due_date,
            'user_pppoe' => $contract->user_pppoe,
            'pass_pppoe' => $contract->pass_pppoe,
            'iptv' => $contract->iptv,
            'user_iptv' => $contract->user_iptv,
            'pass_iptv' => $contract->pass_iptv,
            'observation' => $contract->observation,
            'prepayment' => $contract->prepayment,
            'status' => 'activo',
        ]);

        return response()->json([
            'old_contract' => $contract,
            'new_contract' => $newContract,
        ], 201);
    }


    /**
     * Cambiar caja y puerto
     */
    public function updateBoxAndPort(Request $request, $id)
    {
        $validatedData = $request->validate([
            'boxId' => 'required|exists:boxes,id',
            'portNumber' => 'required|integer',
        ]);

        $contract = Service::findOrFail($id);
        $oldBoxId = $contract->box_id;

        // Actualizar el contrato con la nueva caja y puerto
        $contract->box_id = $validatedData['boxId'];
        $contract->port_number = $validatedData['portNumber'];
        $contract->router_id = $request->routerId;
        $contract->save();

        // Actualizar los puertos disponibles en la caja antigua
        if ($oldBoxId != $contract->box_id) {
            $oldBox = Box::find($oldBoxId);
            if ($oldBox) {
                $oldBox->calculateAvailablePorts();
            }
        }

        // Actualizar los puertos disponibles en la nueva caja
        $newBox = Box::find($contract->box_id);
        if ($newBox) {
            $newBox->calculateAvailablePorts();
        }

        return response()->json($contract, 200);
    }


    /**
     * Cambiar Vlan
     */
    public function updateVlan(Request $request, $id)
    {
        $validatedData = $request->validate([
            'routerId' => 'required|exists:routers,id',
        ]);

        $contract = Service::findOrFail($id);

        // $contract->router_id = $request->routerId;
        $contract->router_id = $validatedData['routerId'];;
        $contract->save();

        return response()->json($contract, 200);
    }

    /** Actualizar Promo */

    public function updatePromo(Request $request, $id)
    {
        $validatedData = $request->validate([
            'promotionId' => 'required|exists:promotions,id',
        ]);

        $contract = Service::findOrFail($id);

        try {
            DB::beginTransaction();

            // Verificar que el servicio esté activo
            if ($contract->status !== 'activo') {
                return response()->json([
                    'message' => 'No se puede aplicar una promoción a un servicio que no está activo'
                ], 400);
            }

            $contract->promotion_id = $validatedData['promotionId'];;
            $contract->save();

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'La promoción se ha aplicado correctamente',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return response()->json([
                'message' => 'Error al aplicar la promoción.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    /**
     * Cambiar equipo y usuario
     */
    public function updateEquipment(Request $request, $id)
    {
        $validatedData = $request->validate([
            'equipmentId' => 'required|exists:equipment,id',
            'userPppoe' => 'nullable|string',
            'passPppoe' => 'nullable|string',
        ]);

        $contract = Service::findOrFail($id);

        // Actualizar el contrato con los nuevos datos del equipo
        $contract->equipment_id = $validatedData['equipmentId'];
        $contract->user_pppoe = $validatedData['userPppoe'];
        $contract->pass_pppoe = $validatedData['passPppoe'];
        $contract->save();

        return response()->json([
            'message' => 'Ok',
            'contract' => $contract
        ], 200);
    }


    /**
     * Cambiar equipo y usuario
     */
    public function updateUser(Request $request, $id)
    {
        $validatedData = $request->validate([
            'userPppoe' => 'nullable|string',
            'passPppoe' => 'nullable|string',
        ]);

        $contract = Service::findOrFail($id);

        // Actualizar el contrato con los nuevos datos del usuario
        $contract->user_pppoe = $validatedData['userPppoe'];
        $contract->pass_pppoe = $validatedData['passPppoe'];
        $contract->save();

        return response()->json([
            'message' => 'Ok',
            'contract' => $contract
        ], 200);
    }


    /**
     * Retrieve contracts by customer ID.
     *
     * @param int $customer_id
     * 
     */
    public function getServicesByCustomer($customer_id)
    {
        // Validar el ID del cliente
        if (!is_numeric($customer_id)) {
            return response()->json(['error' => 'Invalid customer ID'], 400);
        }

        // Obtener los contratos asociados al ID del cliente
        $services = Service::where('customer_id', $customer_id)->get();

        // Retornar los contratos en formato JSON
        return new ServiceCollection($services);
        // return response()->json($contracts, 200);
    }


    /**
     * Check if a equipment exists in Services.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkServicesByEquipment(Request $request)
    {
        $request->validate([
            'equipmentId' => 'required|int',
        ]);

        $exists = Service::where('equipment_id', $request->equipmentId)->exists();

        return response()->json(
            [
                'exists' => $exists
            ]
        );
    }
}
