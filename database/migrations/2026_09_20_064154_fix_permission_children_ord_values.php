<?php

use App\Models\Permission;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * La mayoría de los hijos de menú (ISP, FINANZAS, SOPORTE, ALMACEN,
     * MANTENIMIENTO) tenían ord=0 en todas sus filas. El frontend ordena el
     * menú por 'ord' (ver PermissionController::formatPermission +
     * navigation.component.ts), así que con todos en 0 el orden mostrado caía
     * al orden en que cada ROL recibió esos permisos (tabla permission_role),
     * que difiere entre roles — por eso el mismo submenú se veía distinto
     * según el rol del usuario logueado (Super Admin vs Administrador).
     * Se asigna un ord secuencial por grupo, respetando el orden por id
     * (que ya coincide con el orden correcto observado con Super Admin).
     */
    public function up(): void
    {
        $parents = Permission::whereNull('parent_id')->get();

        foreach ($parents as $parent) {
            $children = Permission::where('parent_id', $parent->id)->orderBy('id')->get();

            foreach ($children as $index => $child) {
                $child->ord = $index + 1;
                $child->save();
            }
        }
    }

    /**
     * No tiene sentido revertir a ord=0 — eso reintroduciría el bug.
     */
    public function down(): void
    {
        //
    }
};
