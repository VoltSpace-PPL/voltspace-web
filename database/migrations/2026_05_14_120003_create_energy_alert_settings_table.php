<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('energy_alert_settings', function (Blueprint $table): void {
            $table->id();
            $table->decimal('high_usage_threshold_kwh', 14, 3)->default(100);
            $table->decimal('peak_demand_limit_kw', 14, 3)->default(10);
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('energy_alert_settings');
    }
};
