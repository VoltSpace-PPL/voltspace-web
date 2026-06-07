<?php

namespace Tests\Feature;

use App\Models\Ruangan;
use App\Models\User;
use App\Services\XlsxService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class ExcelDownloadApiTest extends TestCase
{
    use RefreshDatabase;

    private function authHeaders(string $token): array
    {
        return ['Authorization' => 'Bearer '.$token];
    }

    private function assertValidXlsxDownload($response, string $expectedFilename): void
    {
        $response->assertOk();
        $response->assertHeader('Content-Type', XlsxService::MIME);

        $disposition = $response->headers->get('Content-Disposition');
        $this->assertNotNull($disposition);
        $this->assertStringContainsString($expectedFilename, $disposition);

        $content = $response->streamedContent();
        $this->assertNotEmpty($content);
        $this->assertSame("PK\x03\x04", substr($content, 0, 4));
    }

    private function createXlsxUpload(array $rows, string $filename = 'import.xlsx'): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'xlsx_test_');
        $this->assertNotFalse($path);

        $spreadsheet = new Spreadsheet;
        $spreadsheet->getActiveSheet()->fromArray($rows, null, 'A1', true);
        (new Xlsx($spreadsheet))->save($path);

        return new UploadedFile($path, $filename, XlsxService::MIME, null, true);
    }

    public function test_peminjaman_template_download_with_custom_filename(): void
    {
        $user = User::factory()->create(['role' => 'mahasiswa']);
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeaders($this->authHeaders($token))
            ->get('/api/peminjaman/template/download?filename=Surat_Saya');

        $this->assertValidXlsxDownload($response, 'Surat_Saya.xlsx');
    }

    public function test_import_accepts_valid_xlsx_with_wrong_extension(): void
    {
        Ruangan::query()->create([
            'id' => 'RM-001',
            'kode' => 'RM-001',
            'nama_ruangan' => 'Lab Komputer',
            'kapasitas' => 30,
            'status' => 'tersedia',
        ]);

        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('test')->plainTextToken;

        $file = $this->createXlsxUpload([
            ['ruangan_kode', 'start_time', 'end_time', 'automation_action'],
            ['RM-001', '08:00', '17:00', 'on'],
        ], 'jadwal_salah_nama.txt');

        $response = $this->withHeaders($this->authHeaders($token))
            ->post('/api/jadwal-listrik/import', ['file' => $file]);

        $response->assertOk()
            ->assertJsonPath('created', 1);
    }

    public function test_peminjaman_template_download_is_valid_xlsx(): void
    {
        $user = User::factory()->create(['role' => 'mahasiswa']);
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeaders($this->authHeaders($token))
            ->get('/api/peminjaman/template/download');

        $this->assertValidXlsxDownload($response, 'Template_Surat_Peminjaman.xlsx');
    }

    public function test_jadwal_listrik_template_download_is_valid_xlsx(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('test')->plainTextToken;

        $response = $this->withHeaders($this->authHeaders($token))
            ->get('/api/jadwal-listrik/template/download');

        $this->assertValidXlsxDownload($response, 'template_jadwal_listrik.xlsx');
    }

    public function test_users_template_download_is_valid_xlsx(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        $token = $superAdmin->createToken('test')->plainTextToken;

        $response = $this->withHeaders($this->authHeaders($token))
            ->get('/api/users/template/download');

        $this->assertValidXlsxDownload($response, 'template_import_users.xlsx');
    }

    public function test_jadwal_listrik_import_accepts_renamed_xlsx_file(): void
    {
        Ruangan::query()->create([
            'id' => 'RM-001',
            'kode' => 'RM-001',
            'nama_ruangan' => 'Lab Komputer',
            'kapasitas' => 30,
            'status' => 'tersedia',
        ]);

        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('test')->plainTextToken;

        $file = $this->createXlsxUpload([
            ['ruangan_kode', 'tanggal_mulai', 'tanggal_selesai', 'selected_days', 'start_time', 'end_time', 'automation_action', 'schedule_status', 'device_id'],
            ['RM-001', '2026-05-01', '2026-12-31', 'monday,wednesday', '08:00', '17:00', 'on', 'active', ''],
        ], 'jadwal_custom_name.xlsx');

        $response = $this->withHeaders($this->authHeaders($token))
            ->post('/api/jadwal-listrik/import', ['file' => $file]);

        $response->assertOk()
            ->assertJsonPath('created', 1)
            ->assertJsonPath('message', 'Import selesai.');
    }

    public function test_users_import_accepts_renamed_xlsx_file(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        $token = $superAdmin->createToken('test')->plainTextToken;

        $file = $this->createXlsxUpload([
            ['name', 'email', 'role', 'password'],
            ['Mahasiswa Import', 'mhs.import.test@example.com', 'mahasiswa', 'password123'],
        ], 'users_custom_name.xlsx');

        $response = $this->withHeaders($this->authHeaders($token))
            ->post('/api/users/import', ['file' => $file]);

        $response->assertOk()
            ->assertJsonPath('created', 1)
            ->assertJsonPath('message', 'Import selesai.');

        $this->assertDatabaseHas('users', [
            'email' => 'mhs.import.test@example.com',
            'role' => 'mahasiswa',
        ]);
    }

    public function test_import_rejects_invalid_excel_content(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('test')->plainTextToken;

        $path = tempnam(sys_get_temp_dir(), 'bad_xlsx_');
        file_put_contents($path, 'Template belum digenerate.');

        $file = new UploadedFile($path, 'fake.xlsx', XlsxService::MIME, null, true);

        $response = $this->withHeaders($this->authHeaders($token))
            ->post('/api/jadwal-listrik/import', ['file' => $file]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['file']);
    }

    public function test_energy_report_download_is_valid_xlsx(): void
    {
        Storage::fake('local');

        Ruangan::query()->create([
            'id' => 'RM-TEST',
            'kode' => 'RM-TEST',
            'nama_ruangan' => 'Lab Komputer',
            'kapasitas' => 30,
            'status' => 'digunakan',
        ]);

        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('test')->plainTextToken;

        $create = $this->withHeaders($this->authHeaders($token))
            ->postJson('/api/laporan-energi/generate', [
                'jenis_periode' => 'bulanan',
                'bulan' => 5,
                'tahun' => 2026,
            ])
            ->assertCreated();

        $reportId = $create->json('data.id');

        $response = $this->withHeaders($this->authHeaders($token))
            ->get('/api/laporan-energi/'.$reportId.'/download');

        $this->assertValidXlsxDownload($response, 'laporan_energi_bulanan_2026_05.xlsx');
    }
}
