<?php

namespace App\Http\Controllers;

use App\Models\Entry;
use App\Http\Requests\StoreEntryRequest;
use App\Http\Requests\UpdateEntryRequest;
use App\Http\Resources\EntryCollection;
use App\Http\Resources\EntryResource;

class EntryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new EntryCollection(Entry::all());
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
    public function store(StoreEntryRequest $request)
    {
        $entry = Entry::create($request->validated());
        return new EntryResource($entry);
    }

    /**
     * Display the specified resource.
     */
    public function show(Entry $entry)
    {
        return new EntryResource($entry);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Entry $entry)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEntryRequest $request, Entry $entry)
    {
        $entry->update($request->validated());
        return new EntryResource($entry);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Entry $entry)
    {
        $entry->delete();
        return response()->noContent();
    }
}
