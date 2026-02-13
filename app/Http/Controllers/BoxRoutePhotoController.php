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
        Log::info('BoxRoutePhoto show() called', ['photo_id' => $id]);

        try {
            $photo = BoxRoutePhoto::findOrFail($id);

            Log::info('Photo found in database', [
                'photo_id' => $id,
                'path' => $photo->path,
                'box_route_id' => $photo->box_route_id,
            ]);

            // Construir la ruta completa del archivo
            $fullPath = storage_path('app/public/'.$photo->path);

            Log::info('Full path constructed', [
                'full_path' => $fullPath,
                'file_exists' => file_exists($fullPath),
            ]);

            // Verificar si el archivo existe
            if (! file_exists($fullPath)) {
                Log::error('Photo file not found', [
                    'photo_id' => $id,
                    'stored_path' => $photo->path,
                    'full_path' => $fullPath,
                ]);

                return response()->json([
                    'error' => 'File not found',
                    'path' => $photo->path,
                ], 404);
            }

            // Detectar el MIME type del archivo
            $mimeType = mime_content_type($fullPath);
            $fileSize = filesize($fullPath);

            Log::info('File details', [
                'mime_type' => $mimeType,
                'file_size' => $fileSize,
                'file_extension' => pathinfo($fullPath, PATHINFO_EXTENSION),
            ]);

            // Leer el contenido del archivo
            $fileContent = file_get_contents($fullPath);

            Log::info('File content loaded', [
                'content_length' => strlen($fileContent),
            ]);

            // Devolver la imagen con los headers correctos
            return response($fileContent, 200)
                ->header('Content-Type', $mimeType)
                ->header('Content-Length', $fileSize)
                ->header('Cache-Control', 'public, max-age=31536000')
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');

        } catch (\Exception $e) {
            Log::error('Error serving photo: '.$e->getMessage(), [
                'photo_id' => $id,
                'exception_class' => get_class($e),
                'trace' => $e->getTraceAsString(),
            ]);

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
