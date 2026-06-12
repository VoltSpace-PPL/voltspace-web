<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\XlsxService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserExcelImportController extends Controller
{
    public function downloadTemplate()
    {
        return XlsxService::download('template_import_users.xlsx', function ($spreadsheet): void {
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->fromArray([
                ['name', 'email', 'role', 'password'],
                ['Mahasiswa Contoh', 'mhs.import@example.com', 'mahasiswa', 'password123'],
            ], null, 'A1', true);
        });
    }

    public function import(Request $request): JsonResponse
    {
        if (! $request->user()->isSuperAdmin() && ! $request->user()->isAdmin()) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }
        $request->validate([
            'file' => [
                'required',
                'file',
                'max:10240',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (! $value instanceof UploadedFile) {
                        $fail('File tidak valid.');

                        return;
                    }
                    XlsxService::validateUploadedSpreadsheet($value, $fail);
                },
            ],
        ]);
        $path = $request->file('file')->getRealPath();
        if (! $path) {
            return response()->json(['message' => 'File tidak valid.'], 422);
        }
        $rows = XlsxService::readRows($path);
        $header = array_map(fn ($h) => is_string($h) ? strtolower(trim($h)) : $h, $rows[0] ?? []);
        $idx = array_flip($header);
        foreach (['name', 'email', 'role', 'password'] as $c) {
            if (! isset($idx[$c])) {
                return response()->json(['message' => 'Kolom wajib: name, email, role, password'], 422);
            }
        }

        $created = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach (array_slice($rows, 1) as $n => $row) {
                $line = $n + 2;
                if (! array_filter($row, fn ($x) => $x !== null && $x !== '')) {
                    continue;
                }
                $name = trim((string) ($row[$idx['name']] ?? ''));
                $email = strtolower(trim((string) ($row[$idx['email']] ?? '')));
                $role = strtolower(trim((string) ($row[$idx['role']] ?? '')));
                $password = (string) ($row[$idx['password']] ?? '');
                if ($name === '' || $email === '' || $password === '') {
                    continue;
                }
                if (! in_array($role, ['admin', 'mahasiswa', 'super_admin'], true)) {
                    $errors[] = "Baris {$line}: role tidak valid";

                    continue;
                }
                if (! $request->user()->isSuperAdmin() && $role !== 'mahasiswa') {
                    $errors[] = "Baris {$line}: admin hanya dapat import mahasiswa";

                    continue;
                }
                if (User::query()->where('email', $email)->exists()) {
                    $errors[] = "Baris {$line}: email sudah terdaftar";

                    continue;
                }
                User::create([
                    'name' => $name,
                    'email' => $email,
                    'role' => $role,
                    'password' => Hash::make($password),
                ]);
                $created++;
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json(['message' => 'Import gagal: '.$e->getMessage()], 422);
        }

        return response()->json(['message' => 'Import selesai.', 'created' => $created, 'errors' => $errors]);
    }
}
