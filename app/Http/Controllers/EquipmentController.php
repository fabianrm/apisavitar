<?php

namespace App\Http\Controllers;

use App\Http\Resources\EquipmentCollection;
use App\Http\Resources\EquipmentResource;
use App\Models\Equipment;
use App\Models\Service;
use App\Http\Requests\StoreEquipmentRequest;
use App\Http\Requests\UpdateEquipmentRequest;
use Illuminate\Http\Request;

class EquipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 10);
        $perPage = $perPage > 0 ? min($perPage, 2000) : 10;

        $query = Equipment::with(['brand', 'service']);

        if ($request->filled('mac')) {
            $query->where('mac', 'like', '%' . $request->input('mac') . '%');
        }
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->input('brand_id'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('purchase_date_from')) {
            $query->whereDate('purchase_date', '>=', $request->input('purchase_date_from'));
        }
        if ($request->filled('purchase_date_to')) {
            $query->whereDate('purchase_date', '<=', $request->input('purchase_date_to'));
        }
        // Ventana usada solo para la vista por defecto (sin filtros), igual
        // que en Clientes: evita traer las 900+ filas en una sola petición.
        if ($request->filled('created_from')) {
            $query->whereDate('created_at', '>=', $request->input('created_from'));
        }
        if ($request->filled('created_to')) {
            $query->whereDate('created_at', '<=', $request->input('created_to'));
        }

        $equipments = $query->orderBy('id', 'desc')->paginate($perPage);
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
