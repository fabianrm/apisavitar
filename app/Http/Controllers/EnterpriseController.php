<?php

namespace App\Http\Controllers;

use App\Models\Enterprise;
use App\Helpers\CurrentEnterprise;
use App\Http\Requests\StoreEnterpriseRequest;
use App\Http\Requests\UpdateEnterpriseRequest;
use App\Http\Resources\EnterpriseCollection;
use App\Http\Resources\EnterpriseResource;
use App\Services\TelegramNotifierService;
use Illuminate\Support\Facades\Storage;

class EnterpriseController extends Controller
{
    /**
     * Empresa del usuario logueado (resuelta desde el token, no desde un id
     * que mande el cliente), para las pantallas de autoservicio de Configuración.
     */
    public function mine()
    {
        $enterprise = Enterprise::findOrFail(CurrentEnterprise::get());

        return new EnterpriseResource($enterprise);
    }

    /**
     * Envía un mensaje de prueba al Telegram configurado de la propia empresa.
     */
    public function testTelegram(TelegramNotifierService $telegram)
    {
        $enterprise = Enterprise::findOrFail(CurrentEnterprise::get());

        if (! $enterprise->hasTelegramConfigured()) {
            return response()->json([
                'success' => false,
                'message' => 'Completa el token y el chat ID antes de probar.',
            ], 422);
        }

        $sent = $telegram->sendTest($enterprise);

        return response()->json([
            'success' => $sent,
            'message' => $sent
                ? 'Mensaje de prueba enviado. Revisa tu grupo de Telegram.'
                : 'No se pudo enviar el mensaje. Verifica el token y el chat ID.',
        ]);
    }
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
