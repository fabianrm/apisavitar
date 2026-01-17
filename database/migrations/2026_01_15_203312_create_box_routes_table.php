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
        Schema::create('box_routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('start_box_id')->constrained('boxes')->onDelete('cascade');
            $table->foreignId('end_box_id')->constrained('boxes')->onDelete('cascade');
            $table->string('color')->default('#0000FF');
            $table->json('points')->nullable(); // Stores [ [lat,lng], [lat,lng] ]
            $table->float('distance')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('box_routes');
    }
};
