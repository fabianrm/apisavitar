<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReasonCollection;
use App\Models\Reason;
use App\Http\Requests\StorecityRequest;
use App\Http\Requests\UpdatecityRequest;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cities = Reason::all();
        return new ReasonCollection($cities);
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
    public function store(StorecityRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Reason $city)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reason $city)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatecityRequest $request, Reason $city)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reason $city)
    {
        //
    }
}
