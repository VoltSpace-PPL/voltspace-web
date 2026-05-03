<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('jadwal_listriks')) {
            return;
        }

        Schema::table('jadwal_listriks', function (Blueprint $table) {
            if (! Schema::hasColumn('jadwal_listriks', 'selected_days')) {
                // Store selected days as JSON array, e.g. ["monday","tuesday"].
                $table->json('selected_days')->nullable()->after('device_id');
            }

            if (! Schema::hasColumn('jadwal_listriks', 'start_time')) {
                $table->time('start_time')->nullable()->after('selected_days');
            }

            if (! Schema::hasColumn('jadwal_listriks', 'end_time')) {
                $table->time('end_time')->nullable()->after('start_time');
            }

            if (! Schema::hasColumn('jadwal_listriks', 'automation_action')) {
                $table->enum('automation_action', ['on', 'off'])->nullable()->after('end_time');
            }

            if (! Schema::hasColumn('jadwal_listriks', 'schedule_status')) {
                $table->enum('schedule_status', ['active', 'inactive'])->default('active')->after('automation_action');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('jadwal_listriks')) {
            return;
        }

        Schema::table('jadwal_listriks', function (Blueprint $table) {
            if (Schema::hasColumn('jadwal_listriks', 'schedule_status')) {
                $table->dropColumn('schedule_status');
            }

            if (Schema::hasColumn('jadwal_listriks', 'automation_action')) {
                $table->dropColumn('automation_action');
            }

            if (Schema::hasColumn('jadwal_listriks', 'end_time')) {
                $table->dropColumn('end_time');
            }

            if (Schema::hasColumn('jadwal_listriks', 'start_time')) {
                $table->dropColumn('start_time');
            }

            if (Schema::hasColumn('jadwal_listriks', 'selected_days')) {
                $table->dropColumn('selected_days');
            }
        });
    }
};

