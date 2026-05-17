<?php

namespace Tests\Browser\Concerns;

use App\Models\Ruangan;

trait CreatesTestRuangan
{
    protected string $testRuanganId = '';

    protected function makeTestRuangan(array $overrides = []): Ruangan
    {
        return Ruangan::create(array_merge([
            'nama_ruangan' => 'Server Room Alpha',
            'kapasitas' => 10,
            'status' => 'tersedia',
        ], $overrides));
    }
}
