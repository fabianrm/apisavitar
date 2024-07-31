<?php

namespace App\Http\Controllers;

use App\Models\EntryDetail;
use App\Http\Requests\StoreEntryDetailRequest;
use App\Http\Requests\UpdateEntryDetailRequest;
use App\Http\Resources\EntryDetailCollection;
use App\Http\Resources\EntryDetailResource;

class EntryDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new EntryDetailCollection(EntryDetail::all());
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
    public function store(StoreEntryDetailRequest $request)
    {
        $entryDetail = EntryDetail::create($request->validated());
        return new EntryDetailResource($entryDetail);
    }

    /**
     * Display the specified resource.
     */
    public function show(EntryDetail $entryDetail)
    {
        return new EntryDetailResource($entryDetail);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EntryDetail $entryDetail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEntryDetailRequest $request, EntryDetail $entryDetail)
    {
        $entryDetail->update($request->validated());
        return new EntryDetailResource($entryDetail);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EntryDetail $entryDetail)
    {
        $entryDetail->delete();
        return response()->noContent();
    }
}
