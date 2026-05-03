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
        if (Schema::hasTable('devices')) {
            return;
        }

        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->string('ip_address');
            $table->string('ruangan_id', 64);
            $table->timestamps();

            $table->foreign('ruangan_id')->references('id')->on('ruangans')->cascadeOnDelete();
            $table->index(['ruangan_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('devices')) {
            return;
        }

        Schema::dropIfExists('devices');
    }
};

