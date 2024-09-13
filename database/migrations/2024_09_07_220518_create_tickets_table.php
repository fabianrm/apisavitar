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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->unsignedBigInteger('category_ticket_id');  // Relación con categories_tickets
            $table->unsignedBigInteger('destination_id');
            $table->string('subject');
            $table->text('description');
            $table->date('expiration')->nullable();
            $table->enum('priority', ['baja', 'normal', 'alta']);
            $table->enum('status', ['registrado', 'pendiente', 'atencion', 'espera_pase', 'validacion', 'solucionado']);
            $table->unsignedBigInteger('customer_id');  // Relación con customers
            $table->unsignedBigInteger('technician_id')->nullable();  // Técnico de la tabla employees
            $table->unsignedBigInteger('admin_id');  // Administrador de la tabla employees

            // Fechas de estado
            $table->timestamp('assigned_at')->nullable();  // Cuándo fue asignado el ticket
            $table->timestamp('resolved_at')->nullable();  // Cuándo fue resuelto el ticket
            $table->timestamp('closed_at')->nullable();  // Cuándo se cerró el ticket

            $table->timestamps();

            // Llaves foráneas
            $table->foreign('category_ticket_id')->references('id')->on('categories_tickets')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('technician_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('admin_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('destination_id')->references('id')->on('destinations')->onDelete('cascade');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
