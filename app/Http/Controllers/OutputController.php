<?php

namespace App\Http\Controllers;

use App\Models\Output;
use App\Http\Requests\StoreOutputRequest;
use App\Http\Requests\UpdateOutputRequest;
use App\Http\Resources\OutputCollection;
use App\Http\Resources\OutputResource;

class OutputController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new OutputCollection(Output::all());
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
    public function store(StoreOutputRequest $request)
    {
        $output = Output::create($request->validated());
        return new OutputResource($output);
    }

    /**
     * Display the specified resource.
     */
    public function show(Output $output)
    {
        return new OutputResource($output);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Output $output)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOutputRequest $request, Output $output)
    {
        $output->update($request->validated());
        return new OutputResource($output);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Output $output)
    {
        $output->delete();
        return response()->noContent();
    }
}
