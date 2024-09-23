<?php

namespace App\Http\Controllers;

use App\Http\Resources\RoleResource;
use App\Http\Resources\RoleUserCollection;
use App\Http\Resources\RoleUserResource;
use App\Models\RoleUser;
use Illuminate\Http\Request;

class RoleUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = RoleUser::all();
        return new RoleUserCollection($roles);
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
    public function store(Request $request)
    {
        RoleUser::create($request->all());
     //   return new RoleUserResource(RoleUser::create($request->all()));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

        $role = RoleUser::where('user_id', $id)->orderBy('id', 'desc')->first();
        return new RoleUserResource($role);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $role = RoleUser::where('user_id', $id)->orderBy('id', 'desc')->first();
        $role->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
