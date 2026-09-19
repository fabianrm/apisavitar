<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Con "Nuevo Router" simplificado a solo VLAN + Activo, estos campos ahora
     * pueden llegar vacíos (el middleware global de Laravel convierte '' a null
     * antes de validar) hasta que "Configurar VPN" los complete. Se usa SQL
     * directo en vez de ->change() para no depender de doctrine/dbal.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE routers MODIFY ip VARCHAR(255) NULL');
        DB::statement('ALTER TABLE routers MODIFY usuario VARCHAR(255) NULL');
        DB::statement('ALTER TABLE routers MODIFY password VARCHAR(255) NULL');
        DB::statement('ALTER TABLE routers MODIFY port VARCHAR(255) NULL');
        DB::statement('ALTER TABLE routers MODIFY api_connection VARCHAR(255) NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("UPDATE routers SET ip = '' WHERE ip IS NULL");
        DB::statement("UPDATE routers SET usuario = '' WHERE usuario IS NULL");
        DB::statement("UPDATE routers SET password = '' WHERE password IS NULL");
        DB::statement("UPDATE routers SET port = '' WHERE port IS NULL");
        DB::statement("UPDATE routers SET api_connection = '' WHERE api_connection IS NULL");

        DB::statement('ALTER TABLE routers MODIFY ip VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE routers MODIFY usuario VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE routers MODIFY password VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE routers MODIFY port VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE routers MODIFY api_connection VARCHAR(255) NOT NULL');
    }
};
