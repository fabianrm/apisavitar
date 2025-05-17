<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePromotionRequest;
use App\Http\Requests\UpdatePromotionRequest;
use App\Http\Resources\PromotionCollection;
use App\Http\Resources\PromotionResource;
use App\Models\Promotion;

class PromotionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new PromotionCollection(
            Promotion::orderBy('created_at', 'desc')->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePromotionRequest $request)
    {
        try {
            $promotion = Promotion::create($request->validated());

            return response()->json([
                'message' => 'Promoción registrada correctamente.',
                //'data' => new PromotionResource($promotion),
            ], 201); // Código 201: Created

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ocurrió un error al registrar la promoción.',
                'error' => $e->getMessage(),
            ], 500); // Código 500: Error interno del servidor

        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Promotion $promotion)
    {
        return new PromotionResource($promotion);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePromotionRequest $request, Promotion $promotion)
    {
        try {
            $promotion->update($request->validated());
            // return new PromotionResource($promotion);

            return response()->json([
                'message' => 'Promoción actualizada correctamente.',
                //'data' => new PromotionResource($promotion),
            ], 200); // Código 200: Updated

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ocurrió un error al actualizar la promoción.',
                'error' => $e->getMessage(),
            ], 500); // Código 500: Error interno del servidor
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Promotion $promotion)
    {
        $promotion->delete();
        return response()->noContent();
    }
}
