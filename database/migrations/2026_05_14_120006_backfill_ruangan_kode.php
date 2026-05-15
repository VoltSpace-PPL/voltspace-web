<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ruangans') && Schema::hasColumn('ruangans', 'kode')) {
            DB::table('ruangans')->whereNull('kode')->update(['kode' => DB::raw('id')]);
        }
    }

    public function down(): void
    {
        //
    }
};
