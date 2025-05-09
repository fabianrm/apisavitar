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
            $table->boolean('iptv')->default(false)->after('pass_pppoe');
            $table->string('user_iptv')->nullable()->after('iptv');
            $table->string('pass_iptv')->nullable()->after('user_iptv');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('iptv');
            $table->dropColumn('user_iptv');
            $table->dropColumn('pass_iptv');
        });
    }
};
