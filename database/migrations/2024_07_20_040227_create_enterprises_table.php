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
        Schema::create('enterprises', function (Blueprint $table) {
            $table->id();
            $table->string('ruc')->nullable();
            $table->string('name');
            $table->unsignedBigInteger('city_id');
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('logo')->nullable();
            $table->boolean('status');
            $table->timestamps();
            $table->foreign('city_id')->references('id')->on('cities');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enterprises');
    }
};
