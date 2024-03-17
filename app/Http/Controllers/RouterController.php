<?php

namespace App\Http\Controllers;

use App\Filters\RouterFilter;
use App\Http\Resources\RouterCollection;
use App\Http\Resources\RouterResource;
use App\Models\Router;
use App\Http\Requests\StoreRouterRequest;
use App\Http\Requests\UpdateRouterRequest;
use Illuminate\Http\Request;

class RouterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filter = new RouterFilter();
        $queryItems = $filter->transform($request);

        $routers = Router::where($queryItems);

        $routers = Router::all();
        return new RouterCollection($routers);

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
    public function store(StoreRouterRequest $request)
    {
        return new RouterResource(Router::create($request->all()));
    }

    /**
     * Display the specified resource.
     */
    public function show(Router $router)
    {
        return new RouterResource($router);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Router $router)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRouterRequest $request, Router $router)
    {
        $router->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Router $router)
    {
        //
    }
}
