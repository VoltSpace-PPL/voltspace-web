<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('devices') || Schema::hasColumn('devices', 'power')) {
            return;
        }

        Schema::table('devices', function (Blueprint $table) {
            $table->string('power')->nullable()->after('type');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('devices') || ! Schema::hasColumn('devices', 'power')) {
            return;
        }

        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn('power');
        });
    }
};

