<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReasonCollection;
use App\Http\Resources\ReasonResource;
use App\Models\Reason;
use App\Http\Requests\StoreReasonRequest;
use App\Http\Requests\UpdateReasonRequest;

class ReasonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reasons = Reason::all();
        return new ReasonCollection($reasons);
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
    public function store(StoreReasonRequest $request)
    {
        return new ReasonResource(Reason::create($request->all()));
    }

    /**
     * Display the specified resource.
     */
    public function show(Reason $reason)
    {
        return new ReasonResource($reason);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reason $reason)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReasonRequest $request, Reason $reason)
    {
        $reason->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reason $reason)
    {
        //
    }
}
