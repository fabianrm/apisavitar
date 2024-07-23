<?php

namespace App\Http\Controllers;

use App\Models\Enterprise;
use App\Http\Requests\StoreEnterpriseRequest;
use App\Http\Requests\UpdateEnterpriseRequest;
use App\Http\Resources\EnterpriseCollection;
use App\Http\Resources\EnterpriseResource;

class EnterpriseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $enterprise = Enterprise::first();
        return new EnterpriseResource($enterprise);
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
    public function store(StoreEnterpriseRequest $request)
    {
        return new EnterpriseResource(Enterprise::create($request->all()));
    }

    /**
     * Display the specified resource.
     */
    public function show(Enterprise $enterprise)
    {
        return new EnterpriseResource($enterprise);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Enterprise $enterprise)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEnterpriseRequest $request, Enterprise $enterprise)
    {
        $enterprise->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Enterprise $enterprise)
    {
        //
    }
}
