<?php

namespace App\Http\Controllers;

use App\Http\Resources\ServiceCollection;
use App\Models\Customer;
use App\Models\Service;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use Illuminate\Http\Request;
use App\Filters\ServiceFilter;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        /*   $filter = new ServiceFilter();
          $queryItems = $filter->transform($request);

          if (count($queryItems) == 0) {
              return new ServiceCollection(Service::paginate());
          } else {
              $services = Service::where($queryItems)->paginate();
              return new ServiceCollection($services->appends($request->query()));
          } */



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


        $services = Service::with(['customers', 'routers', 'plans'])->get();
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        //
    }
}
