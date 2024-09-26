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
        Schema::table('users', function (Blueprint $table) {
            $table->string('dni')->nullable()->after('id'); 
            $table->boolean('status')->default(true)->after('remember_token');
            $table->string('position')->nullable()->after('remember_token');
            $table->string('phone')->nullable()->after('remember_token'); 
            $table->string('address')->nullable()->after('remember_token'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('dni');
            $table->dropColumn('address');
            $table->dropColumn('phone');
            $table->dropColumn('position');
            $table->dropColumn('status');
        });
    }
};