<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use App\Http\Requests\StoreDestinationRequest;
use App\Http\Requests\UpdateDestinationRequest;
use App\Http\Resources\DestinationCollection;
use App\Http\Resources\DestinationResource;
use App\Models\OutputDetail;

class DestinationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new DestinationCollection(Destination::all());
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
    public function store(StoreDestinationRequest $request)
    {
        $destination = Destination::create($request->validated());
        return new DestinationResource($destination);
    }

    /**
     * Display the specified resource.
     */
    public function show(Destination $destination)
    {
        return new DestinationResource($destination);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Destination $destination)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDestinationRequest $request, Destination $destination)
    {
        $destination->update($request->validated());
        return new DestinationResource($destination);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Destination $destination)
    {
        $destination->delete();
        return response()->noContent();
    }


    public function getMaterialsByDestination($destinationId)
    {
        $materials = OutputDetail::select('outputs.date', 'materials.code', 'materials.name', 'brands.name as brand', 'presentations.name as presentation', 'output_details.quantity', 'output_details.subtotal')
        ->join('materials', 'output_details.material_id', '=', 'materials.id')
        ->join('presentations', 'presentations.id', '=', 'materials.presentation_id')
        ->join('brands', 'brands.id', '=', 'materials.brand_id')
        ->join('outputs', 'output_details.output_id', '=', 'outputs.id')
        ->where('outputs.destination_id', $destinationId)
        ->get();

        return response()->json(
            ['data'=>$materials]
        );
    }

}
