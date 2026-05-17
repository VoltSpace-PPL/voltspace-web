<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('generated_energy_reports', function (Blueprint $table): void {
            if (! Schema::hasColumn('generated_energy_reports', 'meta')) {
                $table->json('meta')->nullable()->after('total_kwh_ringkasan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('generated_energy_reports', function (Blueprint $table): void {
            if (Schema::hasColumn('generated_energy_reports', 'meta')) {
                $table->dropColumn('meta');
            }
        });
    }
};
