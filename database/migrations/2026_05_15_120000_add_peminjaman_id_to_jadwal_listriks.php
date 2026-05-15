<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwal_listriks', function (Blueprint $table): void {
            if (! Schema::hasColumn('jadwal_listriks', 'peminjaman_id')) {
                $table->foreignId('peminjaman_id')
                    ->nullable()
                    ->after('ruangan_id')
                    ->constrained('peminjaman')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('jadwal_listriks', function (Blueprint $table): void {
            if (Schema::hasColumn('jadwal_listriks', 'peminjaman_id')) {
                $table->dropConstrainedForeignId('peminjaman_id');
            }
        });
    }
};
