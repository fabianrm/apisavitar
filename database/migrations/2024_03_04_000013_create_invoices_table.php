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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_id');
            $table->decimal('price', 8, 2)->default(0.00);
            $table->decimal('igv', 8,2)->default(0.00);
            $table->decimal('discount', 8, 2)->default(0.00);
            $table->decimal('amount', 8, 2)->default(0.00);
            $table->string('letter_amount')->nullable();
            $table->date('due_date');
            $table->date('start_date');
            $table->date('end_date');
            $table->date('paid_dated')->nullable();
            $table->string('receipt')->nullable();
            $table->string('note')->nullable();
            $table->enum('status', ['pendiente', 'pagada', 'vencida',  'anulada'])->default('pendiente');
            $table->timestamps();

            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
