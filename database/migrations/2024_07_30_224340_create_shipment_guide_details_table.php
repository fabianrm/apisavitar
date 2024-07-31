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
        Schema::create('shipment_guide_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_guide_id')->constrained('shipment_guides');
            $table->foreignId('output_id')->constrained('outputs');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_guide_details');
    }
};
