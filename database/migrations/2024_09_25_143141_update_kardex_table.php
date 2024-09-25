<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('kardexes', function (Blueprint $table) {
            // Eliminar la columna entry_detail_id
            $table->dropForeign(['entry_detail_id']); // Si había una clave foránea
            $table->dropColumn('entry_detail_id');

            // Agregar la columna material_id
            $table->unsignedBigInteger('material_id')->after('id');
            $table->foreign('material_id')->references('id')->on('materials')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kardexes', function (Blueprint $table) {
            Schema::table('kardexes', function (Blueprint $table) {
                // Volver a agregar el campo entry_detail_id
                $table->unsignedBigInteger('entry_detail_id')->after('id');
                $table->foreign('entry_detail_id')->references('id')->on('entry_details')->onDelete('cascade');

                // Eliminar el campo material_id
                $table->dropForeign(['material_id']);
                $table->dropColumn('material_id');
            });
        });
    }
};
