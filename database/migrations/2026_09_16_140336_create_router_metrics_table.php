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
        Schema::create('router_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('router_id')->constrained('routers')->onDelete('cascade');
            $table->unsignedTinyInteger('cpu_load');
            $table->unsignedBigInteger('mem_used');
            $table->unsignedBigInteger('mem_total');
            $table->unsignedBigInteger('disk_used');
            $table->unsignedBigInteger('disk_total');
            $table->string('uptime')->nullable();
            $table->timestamp('recorded_at');

            $table->index(['router_id', 'recorded_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('router_metrics');
    }
};
