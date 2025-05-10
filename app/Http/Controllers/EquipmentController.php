<?php

namespace App\Http\Controllers;

use App\Http\Resources\EquipmentCollection;
use App\Http\Resources\EquipmentResource;
use App\Models\Equipment;
use App\Models\Service;
use App\Http\Requests\StoreEquipmentRequest;
use App\Http\Requests\UpdateEquipmentRequest;

class EquipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $equipments = Equipment::orderBy('id', 'desc')->get();
        return new EquipmentCollection($equipments);
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
    public function store(StoreEquipmentRequest $request)
    {
        return new EquipmentResource(Equipment::create($request->all()));
    }

    /**
     * Display the specified resource.
     */
    public function show(Equipment $equipment)
    {
        return new EquipmentResource($equipment);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Equipment $equipment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEquipmentRequest $request, equipment $equipment)
    {
        $equipment->update(($request->all()));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Equipment $equipment)
    {
        //
    }
    /**
     * Display a listing of equipments not being used in services
     */

    public function available()
    {
        $equipments = Equipment::whereDoesntHave('services')
            ->orderBy('id', 'desc')
            ->get();

        return new EquipmentCollection($equipments);
    }

    // public function available()
    // {
    //     $equipments = Equipment::whereNotIn('id', Service::select('equipment_id'))
    //         ->orderBy('id', 'desc')
    //         ->get();

    //     return new EquipmentCollection($equipments);
    // }
}
