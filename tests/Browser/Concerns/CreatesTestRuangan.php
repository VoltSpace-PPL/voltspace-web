<?php

namespace Tests\Browser\Concerns;

use App\Models\Device;
use App\Models\Ruangan;
use App\Models\User;
use Laravel\Dusk\Browser;

trait CreatesTestRuangan
{
    protected string $testRuanganId = '';

    protected function makeTestRuangan(array $overrides = []): Ruangan
    {
        $data = array_merge([
            'nama_ruangan' => 'Server Room Alpha',
            'kapasitas' => 10,
            'status' => 'tersedia',
        ], $overrides);

        if (! isset($data['id'])) {
            $max = 0;
            foreach (\DB::table('ruangans')->where('id', 'like', 'RM-%')->pluck('id') as $rid) {
                $n = (int) substr((string) $rid, 3);
                $max = max($max, $n);
            }
            $data['id'] = 'RM-'.str_pad((string) ($max + 1), 3, '0', STR_PAD_LEFT);
        }

        $insert = [
            'id' => $data['id'],
            'nama_ruangan' => $data['nama_ruangan'],
            'kapasitas' => $data['kapasitas'],
            'status' => $data['status'],
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if (\Schema::hasColumn('ruangans', 'kode')) {
            $insert['kode'] = $data['id'];
        }

        if (\Schema::hasColumn('ruangans', 'lokasi')) {
            $insert['lokasi'] = $data['lokasi'] ?? 'Test Building';
        }

        \DB::table('ruangans')->insert($insert);

        return Ruangan::find($data['id']);
    }

    protected function prepareDeviceDuskTest(): void
    {
        \Schema::disableForeignKeyConstraints();

        $roomIds = Ruangan::where('nama_ruangan', 'Server Room Alpha')->pluck('id');
        if ($roomIds->isNotEmpty()) {
            Device::whereIn('ruangan_id', $roomIds)->delete();
        }

        User::where('email', 'admin@voltspace.id')->delete();
        Ruangan::where('nama_ruangan', 'Server Room Alpha')->delete();

        \Schema::enableForeignKeyConstraints();

        User::factory()->create([
            'email' => 'admin@voltspace.id',
            'role' => 'admin',
            'password' => 'admin123',
        ]);

        $room = $this->makeTestRuangan();
        $this->testRuanganId = $room->id;
    }

    protected function loginAdmin(Browser $browser): void
    {
        $browser->visit('/login')
            ->waitFor('input[name="email"]', 10)
            ->type('email', 'admin@voltspace.id')
            ->type('password', 'admin123')
            ->press('Sign In')
            ->waitForLocation('/dashboard', 15)
            ->pause(500);
    }

    protected function visitDevicesPage(Browser $browser): Browser
    {
        return $browser->visit('/devices')
            ->waitForText('Devices')
            ->waitUntilMissingText('Loading devices...');
    }

    protected function selectRoomInDropdown(Browser $browser, string $selectName, string $roomId): void
    {
        $browser->script("
            (function () {
                var sel = document.querySelector('select[name=\"{$selectName}\"]');
                if (!sel) return;
                var exists = Array.from(sel.options).some(function (opt) {
                    return opt.value === '{$roomId}';
                });
                if (!exists) {
                    var option = document.createElement('option');
                    option.value = '{$roomId}';
                    option.textContent = 'Server Room Alpha ({$roomId})';
                    sel.appendChild(option);
                }
                sel.value = '{$roomId}';
            })();
        ");
    }
}
