<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('ruangans') || ! Schema::hasColumn('ruangans', 'lokasi')) {
            return;
        }

        Schema::table('ruangans', function (Blueprint $table): void {
            $table->dropUnique(['nama_ruangan', 'lokasi']);
        });

        Schema::table('ruangans', function (Blueprint $table): void {
            $table->dropColumn('lokasi');
        });

        Schema::table('ruangans', function (Blueprint $table): void {
            $table->unique('nama_ruangan');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('ruangans') || Schema::hasColumn('ruangans', 'lokasi')) {
            return;
        }

        Schema::table('ruangans', function (Blueprint $table): void {
            $table->dropUnique(['nama_ruangan']);
        });

        Schema::table('ruangans', function (Blueprint $table): void {
            $table->string('lokasi')->after('nama_ruangan');
        });

        Schema::table('ruangans', function (Blueprint $table): void {
            $table->unique(['nama_ruangan', 'lokasi']);
        });
    }
};
