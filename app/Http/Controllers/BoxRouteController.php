<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBoxRouteRequest;
use App\Http\Requests\UpdateBoxRouteRequest;
use App\Http\Resources\BoxRouteResource;
use App\Models\BoxRoute;

class BoxRouteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $boxRoutes = BoxRoute::with(['startBox', 'endBox'])->get();

        return BoxRouteResource::collection($boxRoutes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBoxRouteRequest $request)
    {
        $boxRoute = BoxRoute::create($request->validated());

        return new BoxRouteResource($boxRoute->load(['startBox', 'endBox']));
    }

    /**
     * Display the specified resource.
     */
    public function show(BoxRoute $boxRoute)
    {
        return new BoxRouteResource($boxRoute->load(['startBox', 'endBox']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBoxRouteRequest $request, BoxRoute $boxRoute)
    {
        $boxRoute->update($request->validated());

        return new BoxRouteResource($boxRoute->load(['startBox', 'endBox']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BoxRoute $boxRoute)
    {
        $boxRoute->delete();

        return response()->noContent();
    }
}
