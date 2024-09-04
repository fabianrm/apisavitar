<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Permission;
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{
    public function getUserPermissions(Request $request)
    {
        //$user = Auth::user();
        $user = $request->user();
        $permissions = $user->roles()->with('permissions.children')->get()->pluck('permissions')->flatten()->unique('id');
        $response = $this->buildPermissionTree($permissions->whereNull('parent_id'));

        return response()->json(['data' => $response]);
    }

    private function buildPermissionTree($permissions)
    {
        return $permissions->map(function ($permission) {
            return [
                'name' => $permission->name,
                'icon' => $permission->icon,
                'route' => $permission->route,
                'children' => $this->buildPermissionTree($permission->children)
            ];
        })->values()->toArray();
    }
}
