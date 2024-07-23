<?php

namespace App\Http\Controllers;

use App\Http\Resources\CityCollection;
use App\Models\City;
use App\Http\Requests\StoreCityRequest;
use App\Http\Requests\UpdateCityRequest;
use App\Http\Resources\CityResource;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cities = City::all();
        return new CityCollection($cities);
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
    public function store(StoreCityRequest $request)
    {
        return new CityResource(City::create($request->all()));
    }

    /**
     * Display the specified resource.
     */
    public function show(City $city)
    {
        return new CityResource($city);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(City $city)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCityRequest $request, City $city)
    {
        $city->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $city = City::findOrFail($id);

        // Asegúrate de verificar todas las tablas que podrían tener registros asociados con la ciudad
        if ($city->customers()->exists() || $city->boxes()->exists() || $city->services()->exists()  || $city->enterprises()->exists()) {

            return response()->json([
                'data' => [
                    'status' => false,
                    'message' => 'La ciudad tiene registros asociados.'
                ]
            ], 400);
        }

        $city->delete();

        return response()->json([
            'data' => [
                'status' => true,
                'message' => 'Ciudad eliminada correctamente'
            ]
        ]);
    }
}
