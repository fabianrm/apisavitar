<?php

namespace App\Http\Controllers;

use App\Http\Resources\PermissionCollection;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class PermissionController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $permissions = Permission::all();
        return new PermissionCollection($permissions);


    }

    public function getUserPermissions(Request $request)
    {
        $user = User::with('roles.permissions')->find($request->user()->id);

        // Obtener todos los permisos asignados al usuario a través de sus roles
        $permissions = $user->roles->flatMap(function ($role) {
            return $role->permissions;
        })->unique('id');

        // Filtrar permisos padres que tienen hijos permitidos
        $filteredPermissions = $permissions->whereNull('parent_id')->map(function ($permission) use ($permissions) {
            return $this->formatPermission($permission,
                $permissions
            );
        })->filter(function ($permission) {
            return !empty($permission['children']) || $permission['route'] !== null;
        });

        return response()->json(['data' => $filteredPermissions->values()]);
    }

    private function formatPermission($permission, $allPermissions)
    {
        return [
            'name' => $permission->name,
            'icon' => $permission->icon,
            'route' => $permission->route,
            'children' => $allPermissions->where('parent_id', $permission->id)->map(function ($child) use ($allPermissions) {
                return $this->formatPermission($child, $allPermissions);
            })->values()->toArray() // Convertir a array simple
        ];
    }
    




}
