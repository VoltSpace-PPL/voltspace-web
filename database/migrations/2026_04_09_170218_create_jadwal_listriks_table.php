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
        Schema::create('jadwal_listriks', function (Blueprint $table) {
            $table->id();
            $table->string('ruangan_id', 64);
            $table->foreign('ruangan_id')->references('id')->on('ruangans')->cascadeOnDelete();
            $table->time('waktu_mulai');
            $table->time('waktu_selesai');
            $table->enum('status_listrik', ['nyala', 'mati']);
            $table->timestamps();

            $table->index(['ruangan_id', 'waktu_mulai', 'waktu_selesai']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_listriks');
    }
};
