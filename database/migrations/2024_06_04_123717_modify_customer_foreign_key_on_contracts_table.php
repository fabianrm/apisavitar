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
        Schema::table('services', function (Blueprint $table) {
            // Primero eliminamos la restricción de clave foránea existente
            $table->dropForeign(['customer_id']);

            // Luego, agregamos la restricción de clave foránea sin onDelete('cascade')
            $table->foreign('customer_id')->references('id')->on('customers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            // Primero eliminamos la nueva restricción de clave foránea
            $table->dropForeign(['customer_id']);

            // Luego, restauramos la restricción de clave foránea con onDelete('cascade')
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }
};
