<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Http\Requests\StoreMaterialRequest;
use App\Http\Requests\UpdateMaterialRequest;
use App\Http\Resources\MaterialCollection;
use App\Http\Resources\MaterialResource;

class MaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $material = Material::with(['category', 'presentation', 'brand'])->get();
        return new MaterialCollection($material);
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
    public function store(StoreMaterialRequest $request)
    {
        $material = Material::create($request->validated());
        return new MaterialResource($material);
    }

    /**
     * Display the specified resource.
     */
    public function show(Material $material)
    {
        $material = Material::with(['category', 'presentation', 'brand'])->findOrFail($material->id);
        return new MaterialResource($material);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Material $material)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMaterialRequest $request, Material $material)
    {
        $material->update($request->validated());
        return new MaterialResource($material);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Material $material)
    {
        $material->delete();
        return response()->noContent();
    }
}
