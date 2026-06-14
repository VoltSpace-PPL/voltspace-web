<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ScheduleIoTIntegrationTest extends DuskTestCase
{
    private string $roomId = 'RM-PBI46';

    protected function setUp(): void
    {
        parent::setUp();

        Schema::disableForeignKeyConstraints();

        User::where('email', 'admin@voltspace.id')->delete();

        Schema::enableForeignKeyConstraints();

        User::factory()->create([
            'name' => 'Admin Schedule Test',
            'email' => 'admin@voltspace.id',
            'role' => 'admin',
            'password' => bcrypt('admin123'),
        ]);
    }

    private function loginAdmin(Browser $browser): void
    {
        $browser->visit('/login')
            ->waitFor('input[name="email"]', 10)
            ->type('email', 'admin@voltspace.id')
            ->type('password', 'admin123')
            ->press('Sign In')
            ->waitForLocation('/dashboard', 15)
            ->pause(500);
    }

    private function prepareScheduleDataViaBrowserApi(Browser $browser): void
    {
        $browser->script(<<<'JS'
            window.__pb46SeedResult = null;

            (async function () {
                const roomId = 'RM-PBI46';
                const roomName = 'Lab IoT PBI46';

                const today = new Date()
                    .toLocaleDateString('en-US', { weekday: 'long' })
                    .toLowerCase();

                try {
                    // 1. Cek room
                    const roomsRes = await apiFetch('/ruangan');
                    const rooms = await roomsRes.json().catch(() => []);

                    const existingRoom = Array.isArray(rooms)
                        ? rooms.find(r => r.id === roomId || r.nama_ruangan === roomName)
                        : null;

                    // 2. Buat room kalau belum ada
                    if (!existingRoom) {
                        const createRoomRes = await apiFetch('/ruangan', {
                            method: 'POST',
                            body: JSON.stringify({
                                id: roomId,
                                nama_ruangan: roomName,
                                kapasitas: 30,
                                lantai: 1,
                                status: 'tersedia'
                            })
                        });

                        const createRoomBody = await createRoomRes.json().catch(() => ({}));

                        if (!createRoomRes.ok) {
                            window.__pb46SeedResult = {
                                ok: false,
                                step: 'create-room',
                                status: createRoomRes.status,
                                body: createRoomBody
                            };
                            return;
                        }
                    }

                    // 3. Bersihkan jadwal lama untuk room test
                    const oldSchedulesRes = await apiFetch('/jadwal-listrik?ruangan_id=' + encodeURIComponent(roomId));
                    const oldSchedules = await oldSchedulesRes.json().catch(() => []);

                    if (Array.isArray(oldSchedules)) {
                        for (const schedule of oldSchedules) {
                            await apiFetch('/jadwal-listrik/' + schedule.id, {
                                method: 'DELETE'
                            });
                        }
                    }

                    // 4. Cek device
                    const devicesRes = await apiFetch('/devices?ruangan_id=' + encodeURIComponent(roomId));
                    const devices = await devicesRes.json().catch(() => []);

                    const existingDevice = Array.isArray(devices)
                        ? devices.find(d => d.ruangan_id === roomId)
                        : null;

                    // 5. Buat device dummy kalau belum ada
                    if (!existingDevice) {
                        const createDeviceRes = await apiFetch('/devices', {
                            method: 'POST',
                            body: JSON.stringify({
                                name: 'ESP32 Relay PBI46',
                                type: 'Energy Meter',
                                ip_address: '127.0.0.1',
                                ruangan_id: roomId
                            })
                        });

                        const createDeviceBody = await createDeviceRes.json().catch(() => ({}));

                        if (!createDeviceRes.ok) {
                            window.__pb46SeedResult = {
                                ok: false,
                                step: 'create-device',
                                status: createDeviceRes.status,
                                body: createDeviceBody
                            };
                            return;
                        }
                    }

                    // 6. Buat jadwal AUTO ON
                    const createOnRes = await apiFetch('/jadwal-listrik', {
                        method: 'POST',
                        body: JSON.stringify({
                            ruangan_id: roomId,
                            selected_days: [today],
                            start_time: '08:00',
                            end_time: '17:00',
                            automation_action: 'on',
                            schedule_status: 'active'
                        })
                    });

                    const createOnBody = await createOnRes.json().catch(() => ({}));

                    if (!createOnRes.ok) {
                        window.__pb46SeedResult = {
                            ok: false,
                            step: 'create-auto-on',
                            status: createOnRes.status,
                            body: createOnBody
                        };
                        return;
                    }

                    // 7. Buat jadwal AUTO OFF di tengah jadwal ON
                    const createOffRes = await apiFetch('/jadwal-listrik', {
                        method: 'POST',
                        body: JSON.stringify({
                            ruangan_id: roomId,
                            selected_days: [today],
                            start_time: '11:00',
                            end_time: '12:00',
                            automation_action: 'off',
                            schedule_status: 'active'
                        })
                    });

                    const createOffBody = await createOffRes.json().catch(() => ({}));

                    if (!createOffRes.ok) {
                        window.__pb46SeedResult = {
                            ok: false,
                            step: 'create-auto-off',
                            status: createOffRes.status,
                            body: createOffBody
                        };
                        return;
                    }

                    window.__pb46SeedResult = {
                        ok: true,
                        room_id: roomId,
                        today: today
                    };
                } catch (error) {
                    window.__pb46SeedResult = {
                        ok: false,
                        step: 'exception',
                        message: error.message
                    };
                }
            })();
        JS);

        $browser->waitUntil('window.__pb46SeedResult !== null', 25);

        $result = $browser->script('return window.__pb46SeedResult;')[0];

        $this->assertTrue(
            $result['ok'],
            'Gagal prepare schedule data via API: '.json_encode($result)
        );
    }

    /**
     * PB-46 — Integrasi Penjadwalan Listrik dengan IoT
     * Memastikan schedule AUTO ON dan AUTO OFF dapat dibuat dan tampil pada halaman schedule.
     */
    public function test_pb46_admin_can_create_overlapping_on_off_electricity_schedules(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAdmin($browser);

            // Buat room, device, dan schedule lewat API browser.
            $this->prepareScheduleDataViaBrowserApi($browser);

            // Buka halaman Electricity Schedule dan cek data tampil.
            $browser->visit('/schedule')
                ->waitForText('Electricity Schedule', 15)
                ->waitUntilMissingText('Loading schedules...', 15)
                ->pause(1500)
                ->waitForText('Lab IoT PBI46', 20)
                ->assertSee('AUTO ON')
                ->assertSee('AUTO OFF')
                ->assertSee('08:00 - 17:00')
                ->assertSee('11:00 - 12:00');

            // Verifikasi data schedule lewat API browser.
            $browser->script(<<<'JS'
                window.__pb46ScheduleCheck = null;

                (async function () {
                    const res = await apiFetch('/jadwal-listrik?ruangan_id=RM-PBI46');
                    const data = await res.json();

                    window.__pb46ScheduleCheck = {
                        status: res.status,
                        count: Array.isArray(data) ? data.length : 0,
                        has_on: Array.isArray(data) && data.some(s =>
                            String(s.start_time).startsWith('08:00') &&
                            String(s.end_time).startsWith('17:00') &&
                            s.automation_action === 'on'
                        ),
                        has_off: Array.isArray(data) && data.some(s =>
                            String(s.start_time).startsWith('11:00') &&
                            String(s.end_time).startsWith('12:00') &&
                            s.automation_action === 'off'
                        )
                    };
                })();
            JS);

            $browser->waitUntil('window.__pb46ScheduleCheck !== null', 15);

            $check = $browser->script('return window.__pb46ScheduleCheck;')[0];

            $this->assertSame(200, $check['status']);
            $this->assertTrue($check['has_on'], 'Schedule AUTO ON tidak ditemukan.');
            $this->assertTrue($check['has_off'], 'Schedule AUTO OFF tidak ditemukan.');
        });
    }
}