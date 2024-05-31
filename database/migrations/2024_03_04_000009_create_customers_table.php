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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['natural', 'juridica']);
            $table->string('client_code')->unique();
            $table->string('document_number')->unique();
            $table->string('name');

            $table->unsignedBigInteger('city_id');
            $table->string('address')->nullable();
            $table->string('reference')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();

            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
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
        Schema::dropIfExists('customers');
    }
};
