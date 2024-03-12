<?php

namespace App\Http\Controllers;

use App\Filters\BoxFilter;
use App\Http\Resources\BoxCollection;
use App\Http\Resources\BoxResource;
use App\Models\Box;
use App\Http\Requests\StoreBoxRequest;
use App\Http\Requests\UpdateBoxRequest;
use Illuminate\Http\Request;

class BoxController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filter = new BoxFilter();
        $queryItems = $filter->transform($request);

        $plans = Box::where($queryItems);

        return new BoxCollection($plans->paginate()->appends($request->query()));

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
    public function store(StoreBoxRequest $request)
    {
        return new BoxResource(Box::create($request->all()));
    }

    /**
     * Display the specified resource.
     */
    public function show(Box $box)
    {
        return new BoxResource($box);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Box $box)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBoxRequest $request, Box $box)
    {
        $box->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Box $box)
    {
        //
    }
}
