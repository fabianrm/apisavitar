<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $settings = Permission::where('name', 'Configuración')->whereNull('parent_id')->first();

        if (! $settings) {
            return;
        }

        Permission::where('parent_id', $settings->id)->where('name', 'Red')->update(['ord' => 4]);

        $failures = Permission::forceCreate([
            'name' => 'Fallos WhatsApp',
            'icon' => 'error_outline',
            'route' => '/dashboard/settings/whatsapp-failures',
            'parent_id' => $settings->id,
            'ord' => 3,
        ]);

        $roles = Role::whereIn('name', ['Administrador', 'Super Admin'])->get();

        foreach ($roles as $role) {
            $role->permissions()->attach($failures->id);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $settings = Permission::where('name', 'Configuración')->whereNull('parent_id')->first();

        if ($settings) {
            Permission::where('parent_id', $settings->id)->where('name', 'Red')->update(['ord' => 3]);
        }

        Permission::where('name', 'Fallos WhatsApp')
            ->where('route', '/dashboard/settings/whatsapp-failures')
            ->delete();
    }
};
