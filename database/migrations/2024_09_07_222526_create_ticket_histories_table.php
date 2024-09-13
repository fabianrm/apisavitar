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
        Schema::create('ticket_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ticket_id');
            $table->enum('status', ['registrado', 'pendiente', 'atencion', 'espera_pase', 'validacion', 'solucionado']);
            $table->unsignedBigInteger('changed_by');  // Quien cambió el estado (admin o técnico)
            $table->text('comment')->nullable();
            $table->timestamps();

            // Llaves foráneas
            $table->foreign('ticket_id')->references('id')->on('tickets')->onDelete('cascade');
            $table->foreign('changed_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_histories');
    }
};
