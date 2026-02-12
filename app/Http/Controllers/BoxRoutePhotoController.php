<?php

namespace App\Http\Controllers;

use App\Models\BoxRoute;
use App\Models\BoxRoutePhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BoxRoutePhotoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(BoxRoute $boxRoute)
    {
        return response()->json($boxRoute->photos);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, BoxRoute $boxRoute)
    {
        $request->validate([
            'photo' => 'required|image|max:10240', // 10MB max
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('box-routes/'.$boxRoute->id, 'public');

            $photo = $boxRoute->photos()->create([
                'path' => $path,
            ]);

            return response()->json($photo, 201);
        }

        return response()->json(['message' => 'No file uploaded'], 400);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BoxRoutePhoto $boxRoutePhoto)
    {
        if (Storage::disk('public')->exists($boxRoutePhoto->path)) {
            Storage::disk('public')->delete($boxRoutePhoto->path);
        }

        $boxRoutePhoto->delete();

        return response()->noContent();
    }

    // En tu BoxRoutePhotoController.php

    public function show($id)
    {
        $photo = BoxRoutePhoto::findOrFail($id);
        $path = storage_path('app/public/'.$photo->path);

        if (! file_exists($path)) {
            abort(404);
        }

        $file = file_get_contents($path);
        $type = mime_content_type($path);

        return response($file, 200)
            ->header('Content-Type', $type)
            ->header('Cache-Control', 'public, max-age=31536000');
    }
}
