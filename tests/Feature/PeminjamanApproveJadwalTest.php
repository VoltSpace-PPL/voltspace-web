<?php

namespace Tests\Feature;

use App\Models\JadwalListrik;
use App\Models\Peminjaman;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PeminjamanApproveJadwalTest extends TestCase
{
    use RefreshDatabase;

    public function test_approve_peminjaman_creates_jadwal_listrik_with_lamp_on(): void
    {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        $admin = User::factory()->create(['role' => 'admin']);
        $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);

        Ruangan::query()->create([
            'id' => 'RM-001',
            'kode' => 'RM-001',
            'nama_ruangan' => 'Ruang Uji',
            'kapasitas' => 20,
            'status' => 'tersedia',
        ]);

        $peminjaman = Peminjaman::query()->create([
            'user_id' => $mahasiswa->id,
            'ruangan_id' => 'RM-001',
            'tanggal_mulai' => now()->addDays(3)->toDateString(),
            'tanggal_selesai' => now()->addDays(3)->toDateString(),
            'waktu_mulai' => '09:00',
            'waktu_selesai' => '12:00',
            'tujuan' => 'Seminar',
            'status' => 'pending',
        ]);

        $token = $admin->createToken('test')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/peminjaman/'.$peminjaman->id.'/approve')
            ->assertOk()
            ->assertJsonPath('data.status', 'disetujui')
            ->assertJsonPath('jadwal_listrik.automation_action', 'on')
            ->assertJsonPath('jadwal_listrik.status_listrik', 'nyala');

        $this->assertDatabaseHas('jadwal_listriks', [
            'peminjaman_id' => $peminjaman->id,
            'ruangan_id' => 'RM-001',
            'schedule_status' => 'active',
            'automation_action' => 'on',
        ]);
    }

    public function test_cancel_peminjaman_deletes_linked_jadwal(): void
    {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        $admin = User::factory()->create(['role' => 'admin']);
        $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);

        Ruangan::query()->create([
            'id' => 'RM-002',
            'kode' => 'RM-002',
            'nama_ruangan' => 'Ruang B',
            'kapasitas' => 15,
            'status' => 'tersedia',
        ]);

        $peminjaman = Peminjaman::query()->create([
            'user_id' => $mahasiswa->id,
            'ruangan_id' => 'RM-002',
            'tanggal_mulai' => now()->addDays(5)->toDateString(),
            'tanggal_selesai' => now()->addDays(5)->toDateString(),
            'waktu_mulai' => '10:00',
            'waktu_selesai' => '14:00',
            'tujuan' => 'Workshop',
            'status' => 'disetujui',
        ]);

        JadwalListrik::query()->create([
            'ruangan_id' => 'RM-002',
            'peminjaman_id' => $peminjaman->id,
            'start_time' => '10:00',
            'end_time' => '14:00',
            'waktu_mulai' => '10:00',
            'waktu_selesai' => '14:00',
            'automation_action' => 'on',
            'schedule_status' => 'active',
            'status_listrik' => 'nyala',
            'tanggal_mulai' => $peminjaman->tanggal_mulai,
            'tanggal_selesai' => $peminjaman->tanggal_selesai,
        ]);

        $jadwalId = JadwalListrik::query()->where('peminjaman_id', $peminjaman->id)->value('id');

        $token = $admin->createToken('test')->plainTextToken;
        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/peminjaman/'.$peminjaman->id.'/cancel')
            ->assertOk();

        $this->assertDatabaseMissing('jadwal_listriks', ['id' => $jadwalId]);
        $this->assertDatabaseCount('jadwal_listriks', 0);
    }

    public function test_mahasiswa_cancel_deletes_linked_jadwal(): void
    {
        $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);

        Ruangan::query()->create([
            'id' => 'RM-003',
            'kode' => 'RM-003',
            'nama_ruangan' => 'Ruang C',
            'kapasitas' => 10,
            'status' => 'tersedia',
        ]);

        $peminjaman = Peminjaman::query()->create([
            'user_id' => $mahasiswa->id,
            'ruangan_id' => 'RM-003',
            'tanggal_mulai' => now()->addDays(4)->toDateString(),
            'tanggal_selesai' => now()->addDays(4)->toDateString(),
            'waktu_mulai' => '08:00',
            'waktu_selesai' => '11:00',
            'tujuan' => 'Study group',
            'status' => 'disetujui',
        ]);

        JadwalListrik::query()->create([
            'ruangan_id' => 'RM-003',
            'peminjaman_id' => $peminjaman->id,
            'start_time' => '08:00',
            'end_time' => '11:00',
            'waktu_mulai' => '08:00',
            'waktu_selesai' => '11:00',
            'automation_action' => 'on',
            'schedule_status' => 'active',
            'status_listrik' => 'nyala',
            'tanggal_mulai' => $peminjaman->tanggal_mulai,
            'tanggal_selesai' => $peminjaman->tanggal_selesai,
        ]);

        $token = $mahasiswa->createToken('test')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/peminjaman/'.$peminjaman->id.'/cancel')
            ->assertOk();

        $this->assertDatabaseMissing('jadwal_listriks', [
            'peminjaman_id' => $peminjaman->id,
        ]);
    }
}
