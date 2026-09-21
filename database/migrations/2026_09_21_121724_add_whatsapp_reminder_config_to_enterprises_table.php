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
        Schema::table('enterprises', function (Blueprint $table) {
            $table->boolean('whatsapp_reminders_enabled')->default(true)->after('telegram_chat_id');
            $table->string('wa_instance')->nullable()->after('whatsapp_reminders_enabled');
            $table->string('wa_api_key')->nullable()->after('wa_instance');
            $table->unsignedTinyInteger('wa_reminder_days_before')->default(7)->after('wa_api_key');
            $table->text('wa_payment_info')->nullable()->after('wa_reminder_days_before');
            $table->text('wa_message_template_due')->nullable()->after('wa_payment_info');
            $table->text('wa_message_template_overdue')->nullable()->after('wa_message_template_due');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enterprises', function (Blueprint $table) {
            $table->dropColumn([
                'whatsapp_reminders_enabled',
                'wa_instance',
                'wa_api_key',
                'wa_reminder_days_before',
                'wa_payment_info',
                'wa_message_template_due',
                'wa_message_template_overdue',
            ]);
        });
    }
};
