<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('download');
            $table->string('upload');
            $table->float('price');
            $table->string('guaranteed_speed')->nullable();
            $table->string('priority');
            $table->string('burst_limit')->nullable();
            $table->string('burst_threshold')->nullable();
            $table->string('burst_time')->nullable();
            $table->boolean('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
