<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ruangans', function (Blueprint $table): void {
            if (! Schema::hasColumn('ruangans', 'kode')) {
                $table->string('kode', 32)->nullable()->unique()->after('id');
            }
        });

        Schema::table('peminjaman', function (Blueprint $table): void {
            if (Schema::hasColumn('peminjaman', 'status')) {
                $table->string('status', 32)->default('pending')->change();
            }
            if (! Schema::hasColumn('peminjaman', 'catatan_admin')) {
                $table->string('catatan_admin', 500)->nullable()->after('status');
            }
            if (! Schema::hasColumn('peminjaman', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable()->after('catatan_admin');
            }
            if (! Schema::hasColumn('peminjaman', 'reviewed_by')) {
                $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete()->after('reviewed_at');
            }
        });

        Schema::table('jadwal_listriks', function (Blueprint $table): void {
            if (! Schema::hasColumn('jadwal_listriks', 'tanggal_mulai')) {
                $table->date('tanggal_mulai')->nullable()->after('device_id');
            }
            if (! Schema::hasColumn('jadwal_listriks', 'tanggal_selesai')) {
                $table->date('tanggal_selesai')->nullable()->after('tanggal_mulai');
            }
        });
    }

    public function down(): void
    {
        Schema::table('jadwal_listriks', function (Blueprint $table): void {
            if (Schema::hasColumn('jadwal_listriks', 'tanggal_selesai')) {
                $table->dropColumn('tanggal_selesai');
            }
            if (Schema::hasColumn('jadwal_listriks', 'tanggal_mulai')) {
                $table->dropColumn('tanggal_mulai');
            }
        });

        Schema::table('peminjaman', function (Blueprint $table): void {
            if (Schema::hasColumn('peminjaman', 'reviewed_by')) {
                $table->dropConstrainedForeignId('reviewed_by');
            }
            if (Schema::hasColumn('peminjaman', 'reviewed_at')) {
                $table->dropColumn('reviewed_at');
            }
            if (Schema::hasColumn('peminjaman', 'catatan_admin')) {
                $table->dropColumn('catatan_admin');
            }
        });

        Schema::table('ruangans', function (Blueprint $table): void {
            if (Schema::hasColumn('ruangans', 'kode')) {
                $table->dropColumn('kode');
            }
        });
    }
};
