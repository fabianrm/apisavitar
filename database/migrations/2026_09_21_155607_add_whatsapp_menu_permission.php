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

        Permission::where('parent_id', $settings->id)->where('name', 'Red')->update(['ord' => 3]);

        $whatsapp = Permission::forceCreate([
            'name' => 'WhatsApp',
            'icon' => 'chat',
            'route' => '/dashboard/settings/whatsapp',
            'parent_id' => $settings->id,
            'ord' => 2,
        ]);

        $roles = Role::whereIn('name', ['Administrador', 'Super Admin'])->get();

        foreach ($roles as $role) {
            $role->permissions()->attach($whatsapp->id);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $settings = Permission::where('name', 'Configuración')->whereNull('parent_id')->first();

        if ($settings) {
            Permission::where('parent_id', $settings->id)->where('name', 'Red')->update(['ord' => 2]);
        }

        Permission::where('name', 'WhatsApp')
            ->where('route', '/dashboard/settings/whatsapp')
            ->delete();
    }
};
