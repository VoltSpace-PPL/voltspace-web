<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('generated_energy_reports', function (Blueprint $table): void {
            if (! Schema::hasColumn('generated_energy_reports', 'jenis_periode')) {
                $table->string('jenis_periode', 16)->default('bulanan')->after('title');
            }
        });

        Schema::table('generated_energy_reports', function (Blueprint $table): void {
            $table->unsignedTinyInteger('bulan')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('generated_energy_reports', function (Blueprint $table): void {
            if (Schema::hasColumn('generated_energy_reports', 'jenis_periode')) {
                $table->dropColumn('jenis_periode');
            }
            $table->unsignedTinyInteger('bulan')->nullable(false)->change();
        });
    }
};
