<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBoxRouteRequest;
use App\Http\Requests\UpdateBoxRouteRequest;
use App\Http\Resources\BoxRouteResource;
use App\Models\Box;
use App\Models\BoxRoute;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BoxRouteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * BoxRoute no tiene enterprise_id propio (no se puede filtrar directo),
     * así que se filtra por las cajas que conecta: whereHas aplica el
     * EnterpriseScope de Box automáticamente, dejando solo rutas cuyas dos
     * cajas son de la empresa actual.
     */
    public function index()
    {
        $boxRoutes = BoxRoute::with(['startBox', 'endBox'])
            ->whereHas('startBox')
            ->whereHas('endBox')
            ->get();

        return BoxRouteResource::collection($boxRoutes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBoxRouteRequest $request)
    {
        $data = $request->validated();

        // Box::findOrFail ya aplica el EnterpriseScope: si alguna caja no es
        // de la empresa actual, esto lanza 404 en vez de dejar crear una
        // ruta que cruce cajas de otra empresa.
        Box::findOrFail($data['start_box_id']);
        Box::findOrFail($data['end_box_id']);

        $boxRoute = BoxRoute::create($data);

        return new BoxRouteResource($boxRoute->load(['startBox', 'endBox']));
    }

    /**
     * Display the specified resource.
     */
    public function show(BoxRoute $boxRoute)
    {
        $this->authorizeSameEnterprise($boxRoute);

        return new BoxRouteResource($boxRoute->load(['startBox', 'endBox']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBoxRouteRequest $request, BoxRoute $boxRoute)
    {
        $this->authorizeSameEnterprise($boxRoute);

        $boxRoute->update($request->validated());

        return new BoxRouteResource($boxRoute->load(['startBox', 'endBox']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BoxRoute $boxRoute)
    {
        $this->authorizeSameEnterprise($boxRoute);

        $boxRoute->delete();

        return response()->noContent();
    }

    /**
     * BoxRoute no tiene su propio scope; route-model-binding la resuelve sin
     * filtrar por empresa. Se confirma que sus cajas sí pertenecen a la
     * empresa actual antes de dejar ver/editar/borrar la ruta.
     */
    private function authorizeSameEnterprise(BoxRoute $boxRoute): void
    {
        if (! Box::where('id', $boxRoute->start_box_id)->exists()) {
            throw new ModelNotFoundException;
        }
    }
}
