<?php

namespace App\Http\Controllers;

use App\Models\EntryType;
use App\Http\Requests\StoreEntryTypeRequest;
use App\Http\Requests\UpdateEntryTypeRequest;
use App\Http\Resources\EntryTypeCollection;
use App\Http\Resources\EntryTypeResource;

class EntryTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new EntryTypeCollection(EntryType::all());
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
    public function store(StoreEntryTypeRequest $request)
    {
        $entryType = EntryType::create($request->validated());
        return new EntryTypeResource($entryType);
    }

    /**
     * Display the specified resource.
     */
    public function show(EntryType $entryType)
    {
        return new EntryTypeResource($entryType);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EntryType $entryType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEntryTypeRequest $request, EntryType $entryType)
    {
        $entryType->update($request->validated());
        return new EntryTypeResource($entryType);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EntryType $entryType)
    {
        $entryType->delete();
        return response()->noContent();
    }
}
