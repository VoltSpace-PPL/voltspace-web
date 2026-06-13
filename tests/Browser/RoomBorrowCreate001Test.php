<?php

namespace Tests\Browser;

use App\Models\Ruangan;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\CreatesTestRuangan;
use Tests\DuskTestCase;

class RoomBorrowCreate001Test extends DuskTestCase
{
    use CreatesTestRuangan;

    protected function setUp(): void
    {
        parent::setUp();
        \Schema::disableForeignKeyConstraints();
        \DB::table('peminjaman')->truncate();
        User::where('email', 'student@voltspace.id')->delete();
        Ruangan::where('nama_ruangan', 'Server Room Alpha')->delete();
        \Schema::enableForeignKeyConstraints();

        User::factory()->create([
            'email' => 'student@voltspace.id',
            'role' => 'mahasiswa',
            'password' => 'student123',
        ]);

        $room = $this->makeTestRuangan();
        $this->testRuanganId = $room->id;
    }

    protected function loginStudent(Browser $browser): void
    {
        $browser->visit('/login')
            ->waitFor('input[name="email"]', 10)
            ->type('email', 'student@voltspace.id')
            ->type('password', 'student123')
            ->press('Sign In')
            ->waitForLocation('/student/dashboard', 15)
            ->pause(500);
    }

    /**
     * PBI #30 TC.RoomBorrow.Create.001 (Positive)
     */
    public function test_tc_room_borrow_create_001()
    {
        $this->browse(function (Browser $browser) {
            $this->loginStudent($browser);

            $browser->visit('/student/bookings/create')
                ->waitForText('New Booking Request')
                ->waitUntil('document.getElementById("waktu_mulai")._flatpickr !== undefined')
                ->script([
                    "document.getElementById('tanggal_mulai').value = '2026-12-31';",
                    "document.getElementById('tanggal_mulai').dispatchEvent(new Event('change'));",
                ]);

            $browser->pause(1000);

            $browser->scrollIntoView('#surat_peminjaman')
                ->attach('#surat_peminjaman', realpath(__DIR__.'/dummy.xlsx'));

            $browser->script("
                (function () {
                    document.getElementById('tanggal_mulai').value = '2026-12-31';
                    document.getElementById('waktu_mulai').value = '08:00';
                    document.getElementById('waktu_selesai').value = '10:00';
                    document.getElementById('tujuan').value = 'Kegiatan Rapat Himpunan';

                    var sel = document.getElementById('ruangan_id');
                    var wrap = document.getElementById('room-select-wrap');
                    if (wrap) wrap.classList.remove('hidden');
                    if (sel) {
                        var exists = Array.from(sel.options).some(function (opt) {
                            return opt.value === '{$this->testRuanganId}';
                        });
                        if (!exists) {
                            var option = document.createElement('option');
                            option.value = '{$this->testRuanganId}';
                            option.textContent = 'Server Room Alpha ({$this->testRuanganId})';
                            sel.appendChild(option);
                        }
                        sel.value = '{$this->testRuanganId}';
                        sel.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                })();
            ");

            $browser->script("
                (function () {
                    var btn = document.getElementById('submit-booking-btn');
                    btn.type = 'button';
                    btn.click();
                })();
            ");

            $browser->waitForText('Submission Successful!', 15);

            $browser->script("document.querySelector('#vs-alert-actions button').click();");

            $browser->visit('/student/bookings')
                ->waitForText('My Bookings')
                ->waitUntilMissingText('Loading bookings...')
                ->assertSee('Kegiatan Rapat Himpunan');
        });
    }
}
