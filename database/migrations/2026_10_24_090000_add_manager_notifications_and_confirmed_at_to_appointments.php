<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('whatsapp_bot_training') && !Schema::hasColumn('whatsapp_bot_training', 'manager_notification_phone')) {
            Schema::table('whatsapp_bot_training', function (Blueprint $table) {
                $table->string('manager_notification_phone', 32)->nullable()->after('resource_label');
                $table->boolean('notify_manager_on_booking')->default(true)->after('manager_notification_phone');
                $table->boolean('notify_manager_on_confirm')->default(true)->after('notify_manager_on_booking');
                $table->boolean('notify_manager_on_cancel')->default(true)->after('notify_manager_on_confirm');
            });
        }

        if (Schema::hasTable('appointments') && !Schema::hasColumn('appointments', 'confirmed_at')) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->timestamp('confirmed_at')->nullable()->after('reminder_second_sent_at');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('whatsapp_bot_training') && Schema::hasColumn('whatsapp_bot_training', 'manager_notification_phone')) {
            Schema::table('whatsapp_bot_training', function (Blueprint $table) {
                $table->dropColumn([
                    'manager_notification_phone',
                    'notify_manager_on_booking',
                    'notify_manager_on_confirm',
                    'notify_manager_on_cancel',
                ]);
            });
        }

        if (Schema::hasTable('appointments') && Schema::hasColumn('appointments', 'confirmed_at')) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->dropColumn('confirmed_at');
            });
        }
    }
};
