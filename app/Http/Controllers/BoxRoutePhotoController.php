<?php

namespace App\Http\Controllers;

use App\Models\BoxRoute;
use App\Models\BoxRoutePhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BoxRoutePhotoController extends Controller
{
    /**
     * Store a newly created photo
     */
    public function store(Request $request, BoxRoute $boxRoute)
    {
        $request->validate([
            'photo' => 'required|image|max:10240', // 10MB max
        ]);

        try {
            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $fileName = time().'_'.$file->getClientOriginalName();

                // Guardar el archivo directamente
                $filePath = $file->storeAs('box-routes/'.$boxRoute->id, $fileName, 'public');

                $photo = $boxRoute->photos()->create([
                    'path' => $filePath,
                ]);

                return response()->json($photo, 201);
            }

            return response()->json(['message' => 'No file uploaded'], 400);

        } catch (\Exception $e) {
            Log::error('Error uploading photo: '.$e->getMessage());

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified photo
     */
    public function show($id)
    {
        try {
            $photo = BoxRoutePhoto::findOrFail($id);

            // Construir la ruta completa del archivo
            $fullPath = storage_path('app/public/'.$photo->path);

            // Verificar si el archivo existe
            if (! file_exists($fullPath)) {
                return response()->json([
                    'error' => 'File not found',
                    'path' => $photo->path,
                ], 404);
            }

            // Devolver el archivo con el MIME type correcto
            return response()->file($fullPath, [
                'Cache-Control' => 'public, max-age=31536000',
                'Access-Control-Allow-Origin' => '*',
            ]);

        } catch (\Exception $e) {
            Log::error('Error serving photo: '.$e->getMessage());

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get all photos for a route
     */
    public function index(BoxRoute $boxRoute)
    {
        return response()->json($boxRoute->photos);
    }

    /**
     * Remove the specified photo
     */
    public function destroy($id)
    {
        try {
            $photo = BoxRoutePhoto::findOrFail($id);

            // Eliminar el archivo del storage
            if (Storage::disk('public')->exists($photo->path)) {
                Storage::disk('public')->delete($photo->path);
            }

            // Eliminar el registro de la base de datos
            $photo->delete();

            return response()->json(['message' => 'Photo deleted successfully'], 200);

        } catch (\Exception $e) {
            Log::error('Error deleting photo: '.$e->getMessage());

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
