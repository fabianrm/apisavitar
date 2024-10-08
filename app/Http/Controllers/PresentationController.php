<?php

namespace App\Http\Controllers;

use App\Models\Presentation;
use App\Http\Requests\StorePresentationRequest;
use App\Http\Requests\UpdatePresentationRequest;
use App\Http\Resources\PresentationCollection;
use App\Http\Resources\PresentationResource;

class PresentationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new PresentationCollection(Presentation::all());
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
    public function store(StorePresentationRequest $request)
    {
        $presentation = Presentation::create($request->validated());
        return new PresentationResource($presentation);
    }

    /**
     * Display the specified resource.
     */
    public function show(Presentation $presentation)
    {
        return new PresentationResource($presentation);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Presentation $presentation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePresentationRequest $request, Presentation $presentation)
    {
        $presentation->update($request->validated());
        return new PresentationResource($presentation);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Presentation $presentation)
    {
        $presentation->delete();
        return response()->noContent();
    }
}
