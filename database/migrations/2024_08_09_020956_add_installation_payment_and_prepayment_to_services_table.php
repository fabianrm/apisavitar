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
        Schema::table('services', function (Blueprint $table) {
            $table->boolean('installation_payment')->default(false)->after('due_date'); // Indica si hay un pago de instalación
            $table->decimal('installation_amount', 10, 2)->nullable()->after('installation_payment'); // Monto del pago de instalación
            $table->boolean('prepayment')->default(false)->after('installation_amount'); // Indica si el pago es por adelantado
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['installation_payment', 'installation_amount', 'prepayment']);
        });
    }
};
