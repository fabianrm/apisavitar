<?php

namespace App\Http\Controllers;

use App\Models\Entry;
use App\Http\Requests\StoreEntryRequest;
use App\Http\Requests\UpdateEntryRequest;
use App\Http\Resources\EntryCollection;
use App\Http\Resources\EntryResource;
use App\Models\EntryDetail;
use App\Models\Kardex;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EntryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $entry = Entry::with(['supplier', 'document', 'entryType'])->get();
        return new EntryCollection($entry);
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

        // $entry = Entry::with(['entryType'])->get();

        DB::beginTransaction();

        try {
            // Crear la entrada
            $entry = Entry::create($request->only(['date', 'document_number', 'supplier_id', 'document_id', 'entry_type_id', 'total', 'status']));

            // Crear los detalles de la entrada
            foreach ($request->entry_details as $detail) {
                $entryDetail =   EntryDetail::create([
                    'entry_id' => $entry->id,
                    'date' => $entry->date,
                    'material_id' => $detail['material_id'],
                    'quantity' => $detail['quantity'],
                    'current_stock' => $detail['quantity'],
                    'price' => $detail['price'],
                    'subtotal' => $detail['quantity'] * $detail['price'],
                    'warehouse_id' => $detail['warehouse_id'],
                    'location' => $detail['location']
                ]);

                // Obtener el último registro de Kardex para este material
                $lastKardex = Kardex::where('entry_detail_id', $entryDetail->id)->orderBy('id', 'desc')->first();
                $previousStock = $lastKardex ? $lastKardex->stock : 0;

                // Actualizar el Kardex
                $kardex =  Kardex::create([
                    'entry_detail_id' => $entryDetail->id,
                    'date' => $entryDetail->date,
                    'has' => $previousStock,
                    'operation' => 'entry',
                    'quantity' => $entryDetail->quantity,
                    'stock' => $previousStock + $entryDetail->quantity,
                    'comment' => 'Entrada por ' . $entry->entryType->abbreviation,
                    'created_by' => auth()->user()->id,
                    'updated_by' => auth()->user()->id,
                ]);

                // Log::info($kardex);
            }

            DB::commit();

            return new EntryResource($entry);
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            Log::info($e->getFile());
            Log::info($e->getLine());
            DB::rollBack();
            return response()->json(['error' => 'Failed to create entry'], 500);
        }
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
        DB::beginTransaction();

        try {
            // Actualizar la entrada
            $entry->update($request->only(['date', 'document_number', 'supplier_id', 'document_id', 'entry_type_id', 'total', 'status']));

            // Eliminar los registros del Kardex relacionados con los detalles de la entrada
            foreach ($entry->entryDetails as $entryDetail) {
                Kardex::where('entry_detail_id', $entryDetail->id)->delete();
            }

            // Eliminar los detalles existentes
            $entry->entryDetails()->delete();

            // Crear los detalles de la entrada
            foreach ($request->entry_details as $detail) {
                $entryDetail =   EntryDetail::create([
                    'entry_id' => $entry->id,
                    'date' => $entry->date,
                    'material_id' => $detail['material_id'],
                    'quantity' => $detail['quantity'],
                    'current_stock' => $detail['quantity'],
                    'price' => $detail['price'],
                    'subtotal' => $detail['quantity'] * $detail['price'],
                    'warehouse_id' => $detail['warehouse_id'],
                    'location' => $detail['location']
                ]);

                // Obtener el último registro de Kardex para este material
                $lastKardex = Kardex::where('entry_detail_id', $entryDetail->id)->orderBy('id', 'desc')->first();
                $previousStock = $lastKardex ? $lastKardex->stock : 0;

                // Actualizar el Kardex
                $kardex =  Kardex::create([
                    'entry_detail_id' => $entryDetail->id,
                    'date' => $entry->date,
                    'has' => $previousStock,
                    'operation' => 'entry',
                    'quantity' => $entryDetail->quantity,
                    'stock' => $previousStock + $entryDetail->quantity,
                    'comment' => 'Entrada por ' . $entry->entryType->abbreviation,
                    'created_by' => auth()->user()->id,
                    'updated_by' => auth()->user()->id,
                ]);
            }

            DB::commit();

            return new EntryResource($entry);
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            DB::rollBack();
            return response()->json(['error' => 'Failed to update entry'], 500);
        }
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
