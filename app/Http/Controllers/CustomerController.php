<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\CustomerCollection;
use App\Services\UtilService;
use Illuminate\Http\Request;
use App\Filters\CustomerFilter;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filter = new CustomerFilter();
        $queryItems = $filter->transform($request);
        $includeServices = $request->query('includeServices');
        $customers = Customer::where($queryItems);

        if ($includeServices) {
            $customers = $customers->with('services');
        }
        //return new CustomerCollection($customers->paginate()->appends($request->query()));
        $customers = Customer::all();
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
        $uniqueCode = $clientService->generateUniqueCodeSavitar('CS');

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
        // dd($customer);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        //
    }
}
