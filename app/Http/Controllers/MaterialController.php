<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Http\Requests\StoreMaterialRequest;
use App\Http\Requests\UpdateMaterialRequest;
use App\Http\Resources\MaterialCollection;
use App\Http\Resources\MaterialResource;
use App\Models\Kardex;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $material = Material::with(['category', 'presentation', 'brand'])->get();
        return new MaterialCollection($material);
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
    public function store(StoreMaterialRequest $request)
    {
        $material = Material::create($request->validated());
        return new MaterialResource($material);
    }

    /**
     * Display the specified resource.
     */
    public function show(Material $material)
    {
        $material = Material::with(['category', 'presentation', 'brand'])->findOrFail($material->id);
        return new MaterialResource($material);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Material $material)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMaterialRequest $request, Material $material)
    {
        $material->update($request->validated());
        return new MaterialResource($material);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Material $material)
    {
        $material->delete();
        return response()->noContent();
    }

    public function uploadFile(Request $request)
    {
        if ($request->hasFile('file')) {
            $file  = $request->file('file');
            $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $name_file = str_replace(' ', '_', $filename);
            $extension = $file->getClientOriginalExtension();
            $picture = date('His') . '-' . $name_file . '.' . $extension;

            // Mover el archivo a la carpeta 'images'
            $file->move(public_path('images/'), $picture);

            // Devuelve la ruta relativa de la imagen al frontend
            return response()->json(['nombre_archivo' => 'images/' . $picture]);
        }

        return response()->json(['mensaje' => 'No se ha subido ningún archivo'], 400);
    }


    //Obtener Stock
    public function getStockSummary()
    {
        // Obtenemos el último stock registrado para cada material
        $stockSummary = Kardex::select(
            'material_id as id',
            'materials.code',
            'materials.name as name',
            'brands.name as brand',
            'presentations.prefix as unit',
            'kardexes.stock as total_stock' // Último stock registrado
        )
            ->join('materials', 'kardexes.material_id', '=', 'materials.id') // Unimos con la tabla materials
            ->join('presentations', 'materials.presentation_id', '=', 'presentations.id') // Unimos con la tabla materials
            ->leftJoin('brands', 'materials.brand_id', '=', 'brands.id') // Unimos con la tabla brands
            ->whereIn('kardexes.id', function ($query) {
                $query->select(DB::raw('MAX(id)'))
                    ->from('kardexes')
                    ->groupBy('material_id'); // Tomamos el último registro del Kardex por material
            })
            ->get();

        return response()->json([
            'data' => $stockSummary
        ]);
    }
}
