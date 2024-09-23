<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePermissionRoleRequest;
use App\Models\PermissionRole;
use App\Models\Role;
use Illuminate\Http\Request;

class PermissionRoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(StorePermissionRoleRequest $request) {}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }


    /**
     * Asignar permisos a un rol.
     */
    public function assignPermissionsToRole(Request $request, $roleId)
    {
        $role = Role::findOrFail($roleId);
        $permissions = $request->input('permissions'); // Array de permission_ids

        // Sincronizar los permisos, eliminando los anteriores y agregando los nuevos
        $role->permissions()->sync($permissions);

        return response()->json(
            [
                'status' => 'ok',
                'message' => 'Permisos asignados correctamente al rol.'
            ],
            200
        );
    }

    /**
     * Obtener los permisos asignados a un rol específico.
     */
    public function getPermissionsByRole($roleId)
    {
        $role = Role::with('permissions')->findOrFail($roleId);
        return response()->json([
            'data' => $role->permissions
        ]);
    }

    /**
     * Remover permisos de un rol.
     */
    public function removePermissionFromRole($roleId, $permissionId)
    {
        $role = Role::findOrFail($roleId);
        $role->permissions()->detach($permissionId);

        return response()->json(['message' => 'Permiso removido del rol.'], 200);
    }
}
