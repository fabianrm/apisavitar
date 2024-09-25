<?php

namespace App\Http\Controllers;

use App\Models\EntryDetail;
use App\Http\Requests\StoreEntryDetailRequest;
use App\Http\Requests\UpdateEntryDetailRequest;
use App\Http\Resources\EntryDetailCollection;
use App\Http\Resources\EntryDetailResource;
use App\Models\Kardex;
use Illuminate\Support\Facades\DB;

class EntryDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $entry = EntryDetail::with(['material', 'material.presentation', 'material.category', 'warehouse'])->get();
        return new EntryDetailCollection($entry);

        //return new EntryDetailCollection(EntryDetail::all());
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
        $entry = EntryDetail::with(['material', 'material.presentation', 'material.category', 'warehouse'])->findOrFail($entryDetail->id);
        return new EntryDetailResource($entry);
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


    /**
     * Obtener stock
     */

    public function getStockSummary()
    {
        $stockSummary = Kardex::select('material_id', DB::raw('SUM(stock) as total_stock'))
        ->groupBy('material_id')
        ->with([
            'material:id,code,name',
            'material.brand:id,name' // Relación con la marca a través de material_details
        ])
            ->get();

        return response()->json([
            'data' =>  $stockSummary
        ]);
    }
}
