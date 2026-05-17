<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('generated_energy_reports', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->unsignedTinyInteger('bulan');
            $table->unsignedSmallInteger('tahun');
            $table->decimal('total_kwh_ringkasan', 14, 3)->default(0);
            $table->string('disk', 32)->default('local');
            $table->string('path');
            $table->string('mime', 128)->default('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            $table->unsignedBigInteger('size_bytes')->default(0);
            $table->timestamps();

            $table->index(['tahun', 'bulan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generated_energy_reports');
    }
};
