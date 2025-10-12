<?php

namespace App\Http\Controllers;

use App\Filters\BoxFilter;
use App\Http\Resources\BoxCollection;
use App\Http\Resources\BoxResource;
use App\Models\Box;
use App\Http\Requests\StoreBoxRequest;
use App\Http\Requests\UpdateBoxRequest;
use Illuminate\Http\Request;


class BoxController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filter = new BoxFilter();
        $queryItems = $filter->transform($request);

        $boxs = Box::where($queryItems);

        $boxs = Box::all();
        return new BoxCollection($boxs);

        // return new BoxCollection($boxs->paginate()->appends($request->query()));

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBoxRequest $request)
    {
        return new BoxResource(Box::create($request->all()));
    }

    /**
     * Display the specified resource.
     */
    public function show(Box $box)
    {
        return new BoxResource($box);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBoxRequest $request, Box $box)
    {
        $box->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {

        $box = Box::findOrFail($id);

        // Asegúrate de verificar los servicios que podrían tener registros asociados con la caja
        if ($box->services()->exists()) {

            return response()->json([
                'data' => [
                    'status' => false,
                    'message' => 'La caja tiene contrato asociado.'
                ]
            ], 400);
        }

        $box->delete();

        return response()->json([
            'data' => [
                'status' => true,
                'message' => 'Caja eliminada correctamente'
            ]
        ]);
    }

    /** 
     * Obtiene los puertos disponibles
     */
    public function getPorts($box_id)
    {
        $box = Box::findOrFail($box_id);
        return response()->json($box->availablePorts());
    }

    /**
     * Obtiene los servicios asociados a la caja
     */
    public function getServices($box_id)
    {
        $box = Box::findOrFail($box_id);
        return response()->json($box->getServicesInfo());
    }
}
