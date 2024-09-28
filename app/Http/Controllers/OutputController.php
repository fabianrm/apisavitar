<?php

namespace App\Http\Controllers;

use App\Models\Output;
use App\Http\Requests\StoreOutputRequest;
use App\Http\Requests\UpdateOutputRequest;
use App\Http\Resources\OutputCollection;
use App\Http\Resources\OutputResource;
use App\Models\Kardex;
use App\Models\Material;
use App\Models\OutputDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OutputController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $outputs = Output::with([    
            'destination', 
            'user', 
            'outputDetails', 
            'outputDetails.material',
            'outputDetails.material.category',
            'outputDetails.material.presentation',
            'outputDetails.material.brand',
            ])->get();
        return new OutputCollection($outputs);

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
    public function store(StoreOutputRequest $request)
    {
        DB::beginTransaction();

        try {
            // Crear la salida
            // $output = Output::create($request->only(['number', 'date', 'destination_id', 'employee_id', 'total', 'comment']));

            // Obtener el número más reciente de salida
            $lastOutput = Output::orderBy('id', 'desc')->first();
            $lastNumber = $lastOutput ? intval(substr($lastOutput->number, 4)) : 0;

            // Incrementar el número para la nueva salida
            $newNumber = 'SAL-' . str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);

            // Crear la salida con el nuevo número
            $output = Output::create([
                'number' => $newNumber,
                'date' => Carbon::parse($request->date),
                'destination_id' => $request->destination_id,
                'user_id' => $request->employee_id,
                'total' => 0, // Se actualizará después
                'comment' => $request->comment,
            ]);

            // Crear los detalles de la salida
            $totalOutput = 0; // Para calcular el total de la salida

            foreach ($request->output_details as $detail) {
                // Obtener el detalle de entrada correspondiente
                $material = Material::findOrFail($detail['material_id']);
                
                // Calcular el subtotal
                $subtotal = $detail['quantity'] * $material->price;

                // Crear el detalle de la salida
                $outputDetail = OutputDetail::create([
                    'output_id' => $output->id,
                    'material_id' => $material->id,
                    'quantity' => $detail['quantity'],
                    'subtotal' => $subtotal,
                ]);

                // Reducir el current_stock del detalle de entrada
                //$material->decrement('current_stock', $detail['quantity']);

                // Obtener el último registro de Kardex para este material
                $lastKardex = Kardex::where('material_id', $material->id)->orderBy('created_at', 'desc')->first();
                $previousStock = $lastKardex ? $lastKardex->stock : 0;

                // Registrar en el Kardex
                Kardex::create([
                    'material_id' => $material->id,
                    'date' => $output->date,
                    'has' => $previousStock,
                    'operation' => 'output',
                    'quantity' => -$detail['quantity'],
                    'stock' => $previousStock - $detail['quantity'],
                    'comment' => 'Salida por ' . $output->number,
                    'created_by' => auth()->user()->id,
                    'updated_by' => auth()->user()->id,
                ]);

                // Sumar al total de la salida
                $totalOutput += $subtotal;
            }

            // Actualizar el total de la salida
            $output->update(['total' => $totalOutput]);

            DB::commit();

            return new OutputResource($output);
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            Log::info($e->getLine());
            DB::rollBack();
            return response()->json(['error' => 'Failed to create output'], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Output $output)
    {
        $output = Output::with([
            'destination',
            'outputDetails.entryDetail.material.category',
            'outputDetails.entryDetail.material.presentation',
            'outputDetails.entryDetail.material.brand',
            ])->findOrFail($output->id);
        return new OutputResource($output);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Output $output)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOutputRequest $request, Output $output)
    {
        $output->update($request->validated());
        return new OutputResource($output);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Output $output)
    {
        $output->delete();
        return response()->noContent();
    }
}
