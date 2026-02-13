<?php

namespace App\Http\Controllers;

use App\Models\BoxRoute;
use App\Models\BoxRoutePhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class BoxRoutePhotoController extends Controller
{
    protected $imageManager;

    public function __construct()
    {
        $this->imageManager = new ImageManager(new Driver);
    }

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
                $fileType = $file->getClientOriginalExtension();

                // Verificar si es una imagen
                if (in_array(strtolower($fileType), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    // Comprimir imagen
                    $image = $this->imageManager->read($file->getRealPath());

                    // Redimensionar si la imagen es muy grande
                    if ($image->width() > 1920) {
                        $image->scale(width: 1920);
                    }

                    $filePath = 'box-routes/'.$boxRoute->id.'/'.$fileName;

                    // Codificar y guardar la imagen con calidad específica
                    if ($fileType === 'png') {
                        $encodedImage = $image->toPng()->toFilePointer();
                    } else {
                        $encodedImage = $image->toJpeg(75)->toFilePointer();
                    }

                    // Guardar el archivo
                    Storage::disk('public')->put($filePath, $encodedImage);

                    // Liberar recursos
                    fclose($encodedImage);
                } else {
                    // Si no es una imagen, guardar el archivo normal
                    $filePath = $file->storeAs('box-routes/'.$boxRoute->id, $fileName, 'public');
                }

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

            // Usar Storage para obtener el archivo
            if (! Storage::disk('public')->exists($photo->path)) {
                return response()->json([
                    'error' => 'File not found',
                    'path' => $photo->path,
                ], 404);
            }

            // Obtener el contenido del archivo
            $file = Storage::disk('public')->get($photo->path);

            // Obtener el tipo MIME
            $mimeType = Storage::disk('public')->mimeType($photo->path);

            return response($file, 200)
                ->header('Content-Type', $mimeType)
                ->header('Cache-Control', 'public, max-age=31536000')
                ->header('Access-Control-Allow-Origin', '*');

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
