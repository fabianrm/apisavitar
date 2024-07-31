<?php

namespace App\Http\Controllers;

use App\Models\OutputDetail;
use App\Http\Requests\StoreOutputDetailRequest;
use App\Http\Requests\UpdateOutputDetailRequest;
use App\Http\Resources\OutputDetailCollection;
use App\Http\Resources\OutputDetailResource;

class OutputDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new OutputDetailCollection(OutputDetail::all());
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
    public function store(StoreOutputDetailRequest $request)
    {
        $outputDetail = OutputDetail::create($request->validated());
        return new OutputDetailResource($outputDetail);
    }

    /**
     * Display the specified resource.
     */
    public function show(OutputDetail $outputDetail)
    {
        return new OutputDetailResource($outputDetail);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OutputDetail $outputDetail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOutputDetailRequest $request, OutputDetail $outputDetail)
    {
        $outputDetail->update($request->validated());
        return new OutputDetailResource($outputDetail);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OutputDetail $outputDetail)
    {
        $outputDetail->delete();
        return response()->noContent();
    }
}
