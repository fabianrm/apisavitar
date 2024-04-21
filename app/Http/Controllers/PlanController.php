<?php

namespace App\Http\Controllers;

use App\Http\Resources\PlanCollection;
use App\Http\Resources\PlanResource;
use App\Models\Plan;
use App\Http\Requests\StorePlanRequest;
use App\Http\Requests\UpdatePlanRequest;
use Illuminate\Http\Request;
use App\Filters\PlanFilter;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $plans = Plan::all();
        return new PlanCollection($plans);

    }


    public function filter(Request $request)
    {

        $filter = new PlanFilter();
        $queryItems = $filter->transform($request);
        $includeServices = $request->query('includeServices');
        $plans = Plan::where($queryItems);

        if ($includeServices) {
            $plans = $plans->with('services');
        }

        return new PlanCollection($plans->paginate(100)->appends($request->query()));

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
    public function store(StorePlanRequest $request)
    {
        return new PlanResource(Plan::create($request->all()));
    }

    /**
     * Display the specified resource.
     */
    public function show(Plan $plan)
    {
        $includeServices = request()->query('includeServices');
        if ($includeServices) {
            return new PlanResource($plan->loadMissing('services'));
        }
        return new PlanResource($plan);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Plan $plan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePlanRequest $request, Plan $plan)
    {
        $plan->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Plan $plan)
    {
        //
    }
}
