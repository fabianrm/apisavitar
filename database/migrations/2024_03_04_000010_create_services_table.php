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
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('router_id');
            $table->unsignedBigInteger('plan_id');
            $table->unsignedBigInteger('box_id');
            $table->string('port_number');
            $table->date('registration_date');
            $table->string('billing_date');
            $table->boolean('recurrent');
            $table->string('due_date');
            $table->string('address_instalation')->nullable();
            $table->string('reference')->nullable();
            $table->string('city');
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->boolean('is_active');
            $table->boolean('status');
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('router_id')->references('id')->on('routers')->onDelete('cascade');
            $table->foreign('box_id')->references('id')->on('boxes')->onDelete('cascade');
            $table->foreign('plan_id')->references('id')->on('plans')->onDelete('cascade');

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
