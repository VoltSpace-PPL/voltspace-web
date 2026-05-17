<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            if (! Schema::hasColumn('users', 'energy_alert_high_usage_kwh')) {
                $table->decimal('energy_alert_high_usage_kwh', 12, 3)->nullable()->after('password');
            }
            if (! Schema::hasColumn('users', 'energy_alert_peak_kw')) {
                $table->decimal('energy_alert_peak_kw', 12, 3)->nullable()->after('energy_alert_high_usage_kwh');
            }
        });

        if (Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->string('role', 32)->default('mahasiswa')->change();
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            if (Schema::hasColumn('users', 'energy_alert_peak_kw')) {
                $table->dropColumn('energy_alert_peak_kw');
            }
            if (Schema::hasColumn('users', 'energy_alert_high_usage_kwh')) {
                $table->dropColumn('energy_alert_high_usage_kwh');
            }
        });
    }
};
