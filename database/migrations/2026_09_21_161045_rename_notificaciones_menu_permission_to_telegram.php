<?php

use App\Models\Permission;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Permission::where('route', '/dashboard/settings/notifications')
            ->update(['name' => 'Telegram']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Permission::where('route', '/dashboard/settings/notifications')
            ->update(['name' => 'Notificaciones']);
    }
};
