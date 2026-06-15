<?php

namespace Tests\Browser;

use App\Models\Peminjaman;
use App\Models\Ruangan;
use App\Models\User;
use Carbon\Carbon;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\CreatesTestRuangan;
use Tests\DuskTestCase;

class AdminCancelSchedulePositiveTest extends DuskTestCase
{
    use CreatesTestRuangan;

    protected function setUp(): void
    {
        parent::setUp();
        \Schema::disableForeignKeyConstraints();
        \DB::table('peminjaman')->truncate();
        User::where('email', 'admin@voltspace.id')->delete();
        Ruangan::where('nama_ruangan', 'Server Room Alpha')->delete();
        \Schema::enableForeignKeyConstraints();

        $user = User::factory()->create([
            'email' => 'admin@voltspace.id',
            'role' => 'admin',
            'password' => bcrypt('admin123'),
        ]);

        $room = $this->makeTestRuangan();
        $this->testRuanganId = $room->id;

        // Create booking 2 days from now to allow cancellation (H-1 limit)
        $date = Carbon::now()->addDays(2)->format('Y-m-d');

        Peminjaman::create([
            'user_id' => $user->id,
            'ruangan_id' => $room->id,
            'tanggal_mulai' => $date,
            'tanggal_selesai' => $date,
            'waktu_mulai' => '08:00:00',
            'waktu_selesai' => '10:00:00',
            'tujuan' => 'Rapat Organisasi',
            'status' => 'disetujui',
            'surat_peminjaman' => 'dummy_surat.xlsx',
        ]);
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

    /**
     * PBI #34 – TC.AdminCancelSchedule.Positive
     */
    public function test_admin_can_cancel_schedule()
    {
        $this->browse(function (Browser $browser) {
            $this->loginAdmin($browser);

            $date = Carbon::now()->addDays(2)->format('Y-m-d');
            $bookingId = Peminjaman::first()->id;

            $browser->visit('/room-availability/' . $this->testRuanganId . '?date=' . $date)
                ->waitForText('Student Use', 10)
                ->pause(1000)
                ->script("cancelBooking({$bookingId});");

            $browser->waitFor('#cancel-booking-modal', 10)
                ->waitForText('Cancel Booking')
                ->type('#cancel-reason', 'Jadwal urgent bentrok dengan maintenance server')
                ->press('Ya, Cancel')
                ->waitForText('Success', 10)
                ->assertSee('Success');
                
            $this->assertDatabaseHas('peminjaman', [
                'id' => $bookingId,
                'status' => 'dibatalkan',
                'catatan_admin' => 'Jadwal urgent bentrok dengan maintenance server'
            ]);
        });
    }
}
