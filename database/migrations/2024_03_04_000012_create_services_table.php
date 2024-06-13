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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('service_code')->unique();
            $table->unsignedBigInteger('customer_id');

            $table->unsignedBigInteger('plan_id');

            $table->unsignedBigInteger('router_id');
            $table->unsignedBigInteger('box_id');
            $table->string('port_number');

            $table->unsignedBigInteger('equipment_id');

            $table->unsignedBigInteger('city_id');

            $table->string('address_installation')->nullable();
            $table->string('reference')->nullable();
            $table->date('registration_date');
            $table->date('installation_date');
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            
            $table->string('billing_date')->nullable();
            $table->string('due_date')->nullable();

            $table->enum('status', ['activo', 'inactivo', 'terminado'])->default('activo');
            $table->date('end_date')->nullable(); // Nuevo campo

            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('router_id')->references('id')->on('routers')->onDelete('cascade');
            $table->foreign('box_id')->references('id')->on('boxes')->onDelete('cascade');
            $table->foreign('plan_id')->references('id')->on('plans')->onDelete('cascade');
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
            $table->foreign('equipment_id')->references('id')->on('equipment')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
