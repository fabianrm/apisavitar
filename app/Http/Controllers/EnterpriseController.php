<?php

namespace App\Http\Controllers;

use App\Models\Enterprise;
use App\Http\Requests\StoreEnterpriseRequest;
use App\Http\Requests\UpdateEnterpriseRequest;
use App\Http\Resources\EnterpriseCollection;
use App\Http\Resources\EnterpriseResource;
use Illuminate\Support\Facades\Storage;

class EnterpriseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $enterprise = Enterprise::all();
        return new EnterpriseCollection($enterprise);
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
    public function store(StoreEnterpriseRequest $request)
    {
        try {
            $validatedData = $request->except(['logo']);
            $store = Enterprise::create($validatedData);

            // Verifica si se ha subido un archivo de imagen
            if ($request->hasFile('logo')) {
                $logo = $request->file('logo');
                $imageName = $store->id . '.' . $logo->getClientOriginalExtension();
                $imagePath = $logo->storeAs('images', $imageName, 'public');
                $store->update(['logo' => $imagePath]); // Actualiza la ruta de la imagen en la BD
            } else {
                $store->update(['logo' => 'images/no-logo.jpg']);
            }

            return response()->json([
                'message' => 'Tienda creada correctamente',
                'enterprise' => new EnterpriseResource($store)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear la tienda',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Enterprise $enterprise)
    {
        return new EnterpriseResource($enterprise);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Enterprise $enterprise)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEnterpriseRequest $request, Enterprise $enterprise)
    {
        $validatedData = $request->except('logo');
        $enterprise->update($validatedData);
        // Verifica si se ha subido un archivo de imagen
        if ($request->hasFile('logo')) {
            // Elimina la imagen anterior solo si no es "no-logo.jpg"
            if (
                $enterprise->logo &&
                $enterprise->logo !== 'images/no-image.jpg' &&
                Storage::exists('public/' . $enterprise->logo)
            ) {
                Storage::delete('public/' . $enterprise->logo);
            }
            $logo = $request->file('logo');
            $imageName = $enterprise->id . '.' . $logo->getClientOriginalExtension();
            $imagePath = $logo->storeAs('images', $imageName, 'public');
            $enterprise->update(['logo' => $imagePath]);
        }
        return new EnterpriseResource($enterprise);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Enterprise $enterprise)
    {
        //
    }
}
