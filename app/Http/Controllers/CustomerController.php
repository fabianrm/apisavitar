<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\CustomerCollection;
use App\Services\UtilService;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Exports\CustomersExport;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Obtener los clientes junto con el total de contratos
        // $customers = Customer::withCount('services')->get();
        $customers = Customer::orderBy('created_at', 'desc')->get();

        // Retornar la colección de clientes con el total de contratos
        return new CustomerCollection($customers);
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
    public function store(StoreCustomerRequest $request)
    {

        $clientService = app(UtilService::class);

        // Genera un código único para el cliente
        $uniqueCode = $clientService->generateUniqueCodeCustomer('CS');

        // Almacena el nuevo cliente con el código único
        $customer = new Customer($request->all());
        $customer->client_code = $uniqueCode;
        $customer->save();

        // Retorna una respuesta:
        return new CustomerResource($customer);

        // return new CustomerResource(Customer::create($request->all()));
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        $includeServices = request()->query('includeServices');
        if ($includeServices) {
            return new CustomerResource($customer->loadMissing('services'));
        }
        return new CustomerResource($customer);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $customer->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        $customer->deleteOrFail();

        return response()->json([
            'data' => [
                'status' => true,
                'message' => 'Cliente eliminado correctamente'
            ]
        ]);
    }


    /**
     * Check if a customer exists by document number.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkIfExistsByDocumentNumber(Request $request)
    {
        $request->validate([
            'documentNumber' => 'required|string',
        ]);

        $exists = Customer::where('document_number', $request->documentNumber)->exists();

        return response()->json(['exists' => $exists]);
    }

    /**
     * Verificar estado activo de cliente por documento
     */
    public function getCustomerActiveStatus(Request $request)
    {
        $request->validate([
            'documentNumber' => 'required|string',
        ]);

        $customer = Customer::where('document_number', $request->documentNumber)
            ->withCount(['services as active_services' => function ($query) {
                $query->where('status', 'activo');
            }])
            ->first();

        if (!$customer) {
            return response()->json([
                'status' => false,
                'message' => 'Cliente no encontrado'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'document_number' => $customer->document_number,
            'customer_name' => $customer->name,
            'has_active_services' => $customer->active_services > 0
        ]);
    }

    /**
     * Suspender contrato
     */
    public function suspend(Request $request, $id)
    {
        $request->validate([
            'observation' => 'nullable|string',
        ]);

        $customer = Customer::findOrFail($id);

        $customer->status = $request->input('status');
        $customer->observation = $request->input('observation');
        $customer->save();

        return response()->json([
            'status' => true,
            'message' => 'Operación realizada con exito',
            'customer' => $customer,
        ], 200);
    }

    /**
     * Reactivar cliente
     */
    public function activate($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->status = true;
        $customer->observation = null;
        $customer->save();

        return response()->json([
            'status' => true,
            'message' => 'Cliente reactivado con éxito',
            'customer' => $customer,
        ], 200);
    }



    /**
     * Retrieve customers with their total number of contracts.
     *
     */
    public function getCustomersWithContracts()
    {
        // Obtener los clientes junto con el total de contratos
        $customers = Customer::withCount('services')->get();

        // Retornar la colección de clientes con el total de contratos
        return new CustomerCollection($customers);
    }


    /***
     * Retornar listado de clientes para boot en excel
     */
    public function exportCustomers()
    {
        return Excel::download(new CustomersExport, 'customers.xlsx');
    }
}
