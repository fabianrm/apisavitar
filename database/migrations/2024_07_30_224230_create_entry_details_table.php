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
        Schema::create('entry_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entry_id')->constrained('entries');
            $table->datetime('date');
            $table->foreignId('material_id')->constrained('materials');
            $table->integer('quantity');
            $table->integer('current_stock');
            $table->decimal('price', 8, 2);
            $table->decimal('subtotal', 8, 2);
            $table->foreignId('warehouse_id')->constrained('warehouses');
            $table->string('location')->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entry_details');
    }
};
