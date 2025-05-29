<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Resources\RoleCollection;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::all();
        return new RoleCollection($roles);
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
    public function store(StoreRoleRequest $request)
    {
        $role = Role::create($request->validated());
        return new RoleResource($role);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //return new CategoryTicketResource($categoryTicket);
        $role = Role::findOrFail($id);
        return new RoleResource($role);
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
    public function update(UpdateRoleRequest $request, string $id)
    {
        $role = Role::findOrFail($id);
        $role->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {

            $roleU = Role::findOrFail($id);

            $roleUser = RoleUser::where('role_id', $roleU->id)->exists();

            if ($roleUser) {
                return response()->json([
                    'status' => false,
                    'message' => 'No se puede eliminar el Rol. Tiene usuarios asociados.'
                ], 400);
            }

            $roleU->deleteOrFail();

            return response()->json([
                'status' => true,
                'message' => 'Rol eliminado correctamente'

            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error inesperado al eliminar el Rol.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
