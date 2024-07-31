<?php

namespace App\Http\Controllers;

use App\Models\Kardex;
use App\Http\Requests\StoreKardexRequest;
use App\Http\Requests\UpdateKardexRequest;
use App\Http\Resources\KardexCollection;
use App\Http\Resources\KardexResource;

class KardexController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new KardexCollection(Kardex::all());
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
    public function store(StoreKardexRequest $request)
    {
        $kardex = Kardex::create($request->validated());
        return new KardexResource($kardex);
    }

    /**
     * Display the specified resource.
     */
    public function show(Kardex $kardex)
    {
        return new KardexResource($kardex);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kardex $kardex)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateKardexRequest $request, Kardex $kardex)
    {
        $kardex->update($request->validated());
        return new KardexResource($kardex);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kardex $kardex)
    {
        $kardex->delete();
        return response()->noContent();
    }
}
