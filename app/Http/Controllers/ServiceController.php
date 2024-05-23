<?php

namespace App\Http\Controllers;

use App\Exceptions\ModelNotFoundException;
use App\Http\Resources\ServiceCollection;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Services\UtilService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
       


        // $customers = Customer::all();

        // $services = Service::with('customers')->get();
        //return new ServiceCollection($services);


        /*   $services = Service::with('customers')->get();
          $transformedServices = $services->map(function ($service) {
              return [
                  'id' => $service->id,
                  'name' => $service->router_id,
                  'price' => $service->plan_id,
                  'customer_name' => $service->customers->name,
              ];
          });

          return response()->json(['data' => $transformedServices]); */

        $services = Service::with(['customers', 'routers', 'plans', 'cities'])->get();
        return new ServiceCollection($services);


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

        $service = new Service($request->all());
        $service->service_code = $uniqueCode;
        $service->save();

        return new ServiceResource($service);
        //return new ServiceResource(Service::create($request->all()));

    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $service = Service::find($id);

        if(!$service){
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
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        //
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

        // Finalizamos el contrato actual
        $contract->update([
            'status' => 'terminado',
            'end_date' => now(),
        ]);

        //Creamos un nuevo contrato con el nuevo plan

        $contractService = app(UtilService::class);

        // Genera un código único para el cliente
        $uniqueCode = $contractService->generateUniqueCodeService('CT');

        // $service = new Service($request->all());
        // $service->service_code = $uniqueCode;

        $newContract = Service::create([
            'service_code' => $uniqueCode,
            'customer_id' => $contract->customer_id,
            'plan_id' => $request->plan_id,
            'router_id' => $contract->router_id,
            'box_id' => $contract->box_id,
            'port_number' => $contract->port_number,
            'equipment_id' => $contract->equipment_id,
            'city_id' => $contract->city_id,
            'address_installation' => $contract->address_installation,
            'reference' => $contract->reference,
            'registration_date' => now(),
            'installation_date' => $contract->installation_date,
            'latitude' => $contract->latitude,
            'longitude' => $contract->longitude,
            'billing_date' => $contract->billing_date,
            'due_date' => $contract->due_date,
            'status' => 'activo',
        ]);

        return response()->json([
            'old_contract' => $contract,
            'new_contract' => $newContract,
        ], 201);


    }


}
