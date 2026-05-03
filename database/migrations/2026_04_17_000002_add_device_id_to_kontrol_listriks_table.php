<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('kontrol_listriks') || Schema::hasColumn('kontrol_listriks', 'device_id')) {
            return;
        }

        Schema::table('kontrol_listriks', function (Blueprint $table) {
            $table->foreignId('device_id')
                ->nullable()
                ->after('ruangan_id')
                ->constrained('devices')
                ->nullOnDelete();

            $table->index(['device_id', 'created_at']);
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('kontrol_listriks') || ! Schema::hasColumn('kontrol_listriks', 'device_id')) {
            return;
        }

        Schema::table('kontrol_listriks', function (Blueprint $table) {
            $table->dropIndex(['device_id', 'created_at']);
            $table->dropConstrainedForeignId('device_id');
        });
    }
};

