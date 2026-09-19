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
        $settings = Permission::forceCreate([
            'name' => 'Configuración',
            'icon' => 'settings_applications',
            'route' => null,
            'ord' => 8,
        ]);

        $notifications = Permission::forceCreate([
            'name' => 'Notificaciones',
            'icon' => 'notifications',
            'route' => '/dashboard/settings/notifications',
            'parent_id' => $settings->id,
            'ord' => 0,
        ]);

        $network = Permission::forceCreate([
            'name' => 'Red',
            'icon' => 'vpn_lock',
            'route' => '/dashboard/settings/network',
            'parent_id' => $settings->id,
            'ord' => 1,
        ]);

        $roles = Role::whereIn('name', ['Administrador', 'Super Admin'])->get();

        foreach ($roles as $role) {
            $role->permissions()->attach([$settings->id, $notifications->id, $network->id]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Permission::whereIn('name', ['Configuración', 'Notificaciones', 'Red'])
            ->where(function ($query) {
                $query->whereIn('route', ['/dashboard/settings/notifications', '/dashboard/settings/network'])
                    ->orWhere('icon', 'settings_applications');
            })
            ->delete();
    }
};
