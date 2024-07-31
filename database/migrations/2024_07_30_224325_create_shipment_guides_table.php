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
        Schema::create('shipment_guides', function (Blueprint $table) {
            $table->id();
            $table->string('number');
            $table->date('emission_date');
            $table->date('transfer_date');
            $table->string('origin_address');
            $table->string('destination_address');
            $table->string('driver_name');
            $table->string('vehicle_plate');
            $table->foreignId('warehouse_id')->constrained('warehouses');
            $table->string('sender_name');
            $table->string('receiver_name');
            $table->string('comment')->nullable();
            $table->string('status')->nullable();
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_guides');
    }
};
