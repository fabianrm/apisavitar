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
        Schema::table('materials', function (Blueprint $table) {
            Schema::table('materials', function (Blueprint $table) {
                // Agregar la columna price como decimal con 10 dígitos y 2 decimales
                $table->decimal('price', 10, 2)->nullable()->after('name');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn('price');
        });
    }
};
