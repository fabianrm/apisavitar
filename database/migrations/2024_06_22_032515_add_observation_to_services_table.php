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
            $table->string('user_pppoe')->nullable()->after('end_date'); // Adjust the 'after' parameter as needed
            $table->string('pass_pppoe')->nullable()->after('user_pppoe'); 
            $table->text('observation')->nullable()->after('pass_pppoe'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('observation');
            $table->dropColumn('user_pppoe');
            $table->dropColumn('pass_pppoe');
        });
    }
};
