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
        Schema::create('ruangans', function (Blueprint $table) {
            $table->string('id', 64)->primary();
            $table->string('nama_ruangan');
            $table->string('lokasi');
            $table->unsignedInteger('kapasitas');
            $table->enum('status', ['tersedia', 'digunakan', 'dipesan'])->default('tersedia');
            $table->timestamps();

            $table->unique(['nama_ruangan', 'lokasi']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ruangans');
    }
};
