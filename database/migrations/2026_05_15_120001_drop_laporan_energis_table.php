<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('laporan_energis');
    }

    public function down(): void
    {
        if (Schema::hasTable('laporan_energis')) {
            return;
        }

        Schema::create('laporan_energis', function (Blueprint $table): void {
            $table->id();
            $table->string('ruangan_id', 64);
            $table->foreign('ruangan_id')->references('id')->on('ruangans')->cascadeOnDelete();
            $table->unsignedTinyInteger('bulan');
            $table->unsignedSmallInteger('tahun');
            $table->decimal('total_kwh', 12, 2);
            $table->timestamps();
            $table->unique(['ruangan_id', 'bulan', 'tahun']);
        });
    }
};
