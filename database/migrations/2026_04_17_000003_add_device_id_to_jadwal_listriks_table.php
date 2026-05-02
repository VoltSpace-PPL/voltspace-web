<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('jadwal_listriks') || Schema::hasColumn('jadwal_listriks', 'device_id')) {
            return;
        }

        Schema::table('jadwal_listriks', function (Blueprint $table) {
            $table->foreignId('device_id')
                ->nullable()
                ->after('ruangan_id')
                ->constrained('devices')
                ->nullOnDelete();

            $table->index(['device_id', 'waktu_mulai', 'waktu_selesai']);
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('jadwal_listriks') || ! Schema::hasColumn('jadwal_listriks', 'device_id')) {
            return;
        }

        Schema::table('jadwal_listriks', function (Blueprint $table) {
            $table->dropIndex(['device_id', 'waktu_mulai', 'waktu_selesai']);
            $table->dropConstrainedForeignId('device_id');
        });
    }
};

