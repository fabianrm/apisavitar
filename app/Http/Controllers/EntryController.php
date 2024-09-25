<?php

namespace App\Http\Controllers;

use App\Models\Entry;
use App\Http\Requests\StoreEntryRequest;
use App\Http\Requests\UpdateEntryRequest;
use App\Http\Resources\EntryCollection;
use App\Http\Resources\EntryResource;
use App\Models\EntryDetail;
use App\Models\Kardex;
use Carbon\Carbon;
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
                    'date' => Carbon::parse($entry->date),
                    'material_id' => $detail['material_id'],
                    'quantity' => $detail['quantity'],
                    'price' => $detail['price'],
                    'subtotal' => $detail['quantity'] * $detail['price'],
                    'warehouse_id' => $detail['warehouse_id'],
                    'location' => $detail['location']
                ]);

                // Obtener el último registro de Kardex para este material
                // $lastKardex = Kardex::where('entry_detail_id', $entryDetail->id)->orderBy('id', 'desc')->first();
                // 

                $lastKardex = Kardex::where('material_id', $detail['material_id'])
                    ->latest('created_at')
                    ->first();

                $previousStock = $lastKardex ? $lastKardex->stock : 0;
                // Calcular el nuevo stock
                $newStock = $lastKardex ? $lastKardex->stock + $detail['quantity'] : $detail['quantity'];

                // Actualizar el Kardex
                Kardex::create([
                    'material_id' => $entryDetail->material_id,
                    'date' => $entryDetail->date,
                    'has' => $previousStock,
                    'operation' => 'entry',
                    'quantity' => $entryDetail->quantity,
                    'stock' => $newStock,
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
            return response()->json([
                'message' => $e->getMessage(),
                'error' => 'Failed to create entry'
            
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Entry $entry)
    {
        $entry = Entry::with(['supplier', 'document', 'entryType', 'entryDetails', 'entryDetails.material',  'entryDetails.material.presentation', 'entryDetails.warehouse'])->findOrFail($entry->id);
        return new EntryResource($entry);
        //return new EntryResource($entry);
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
        // Iniciar la transacción
        DB::beginTransaction();

        try {
            // Obtener el ingreso
            $entry = Entry::with('entryDetails.kardexes')->findOrFail($entry->id);

            // Verificar que no se haya afectado el stock
            foreach ($entry->entryDetails as $entryDetail) {
                if ($entryDetail->quantity != $entryDetail->current_stock) {
                    return response()->json([
                        'message' => 'Cannot cancel entry. Stock has been affected.'
                    ], 400);
                }
            }

            // Eliminar los registros relacionados en el Kardex
            foreach ($entry->entryDetails as $entryDetail) {
                $entryDetail->kardexes()->delete();
            }

            // Eliminar los detalles de la entrada
            $entry->entryDetails()->delete();

            // Eliminar la entrada
            $entry->delete();

            // Confirmar la transacción
            DB::commit();

            return response()->json([
                'status' => 'true',
                'message' => 'Entrada eliminada correctamente.'
            ], 200);
        } catch (\Exception $e) {
            // Revertir la transacción en caso de error
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to cancel and delete entry.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function cancelEntry($id)
    {

        // Iniciar la transacción
        DB::beginTransaction();

        try {
            // Obtener el ingreso
            $entry = Entry::with('entryDetails.kardexes')->findOrFail($id);

            // Verificar que no se haya afectado el stock
            foreach ($entry->entryDetails as $entryDetail) {
                if ($entryDetail->quantity != $entryDetail->current_stock) {
                    return response()->json([
                        'message' => 'Cannot cancel entry. Stock has been affected.'
                    ], 400);
                }
            }

            // Anular el ingreso, estableciendo el estado a 0
            $entry->status = 0;
            $entry->save();

            // Eliminar los registros relacionados en el Kardex
            foreach ($entry->entryDetails as $entryDetail) {
                $entryDetail->kardexes()->delete();
            }

            // Confirmar la transacción
            DB::commit();

            return response()->json([
                'message' => 'Entry successfully cancelled.'
            ], 200);
        } catch (\Exception $e) {
            // Revertir la transacción en caso de error
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to cancel entry.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
