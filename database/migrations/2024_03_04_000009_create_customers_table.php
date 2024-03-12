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
            $table->string('document_number')->unique();
            $table->string('name');
            $table->string('address');
            $table->string('reference')->nullable();
            $table->string('city');
            $table->string('latitude');
            $table->string('longitude');
            $table->string('phone_number');
            $table->string('email')->nullable();
            $table->boolean('status');
            $table->timestamps();

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
