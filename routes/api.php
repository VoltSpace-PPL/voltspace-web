<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DeviceControlController;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\EnergyAlertController;
use App\Http\Controllers\Api\GeneratedEnergyReportController;
use App\Http\Controllers\Api\InAppNotificationController;
use App\Http\Controllers\Api\JadwalListrikController;
use App\Http\Controllers\Api\JadwalListrikExcelController;
use App\Http\Controllers\Api\MahasiswaPeminjamanDashboardController;
use App\Http\Controllers\Api\PeminjamanController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RuanganController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserExcelImportController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::match(['put', 'patch'], '/profile', [ProfileController::class, 'update']);

    Route::get('/notifications', [InAppNotificationController::class, 'index']);

    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/dashboard/summary', [DashboardController::class, 'summary']);
    Route::get('/dashboard/trend', [DashboardController::class, 'trend']);
    Route::get('/dashboard/rooms', [DashboardController::class, 'rooms']);

    Route::get('/peminjaman', [PeminjamanController::class, 'index']);
    Route::get('/peminjaman/template/download', [PeminjamanController::class, 'downloadTemplate']);
    Route::get('/peminjaman/{peminjaman}', [PeminjamanController::class, 'show']);
    Route::get('/peminjaman/{peminjaman}/surat', [PeminjamanController::class, 'previewSurat']);
    Route::post('/peminjaman', [PeminjamanController::class, 'store']);
    Route::post('/peminjaman/{peminjaman}/approve', [PeminjamanController::class, 'approve']);
    Route::post('/peminjaman/{peminjaman}/reject', [PeminjamanController::class, 'reject']);
    Route::post('/peminjaman/{peminjaman}/cancel', [PeminjamanController::class, 'cancel']);

    Route::get('/mahasiswa/dashboard/peminjaman', MahasiswaPeminjamanDashboardController::class);

    Route::get('/ruangan', [RuanganController::class, 'index']);
    Route::get('/devices', [DeviceController::class, 'index']);
    Route::get('/jadwal-listrik', [JadwalListrikController::class, 'index']);
    Route::get('/users', [UserController::class, 'index'])->middleware('admin');

    Route::middleware('admin')->group(function () {
        Route::post('/ruangan', [RuanganController::class, 'store']);
        Route::put('/ruangan/{ruangan}', [RuanganController::class, 'update']);
        Route::patch('/ruangan/{ruangan}', [RuanganController::class, 'update']);
        Route::delete('/ruangan/{ruangan}', [RuanganController::class, 'destroy']);

        Route::post('/users', [UserController::class, 'store']);
        Route::put('/users/{user}', [UserController::class, 'update']);
        Route::patch('/users/{user}', [UserController::class, 'update']);

        Route::post('/devices', [DeviceController::class, 'store']);
        Route::put('/devices/{device}', [DeviceController::class, 'update']);
        Route::patch('/devices/{device}', [DeviceController::class, 'update']);
        Route::delete('/devices/{device}', [DeviceController::class, 'destroy']);

        Route::post('/devices/toggle', [DeviceControlController::class, 'toggle']);

        Route::post('/jadwal-listrik', [JadwalListrikController::class, 'store']);
        Route::put('/jadwal-listrik/{jadwal}', [JadwalListrikController::class, 'update']);
        Route::patch('/jadwal-listrik/{jadwal}', [JadwalListrikController::class, 'update']);
        Route::delete('/jadwal-listrik/{jadwal}', [JadwalListrikController::class, 'destroy']);

        Route::get('/jadwal-listrik/template/download', [JadwalListrikExcelController::class, 'downloadTemplate']);
        Route::post('/jadwal-listrik/import', [JadwalListrikExcelController::class, 'import']);

        Route::get('/energy-alerts/settings', [EnergyAlertController::class, 'settings']);
        Route::put('/energy-alerts/settings', [EnergyAlertController::class, 'updateSettings']);
        Route::get('/energy-alerts', [EnergyAlertController::class, 'alerts']);

        Route::get('/laporan-energi', [GeneratedEnergyReportController::class, 'index']);
        Route::post('/laporan-energi/generate', [GeneratedEnergyReportController::class, 'store']);
        Route::get('/laporan-energi/{report}/preview', [GeneratedEnergyReportController::class, 'preview']);
        Route::get('/laporan-energi/{report}/download', [GeneratedEnergyReportController::class, 'download']);
        Route::delete('/laporan-energi/{report}', [GeneratedEnergyReportController::class, 'destroy']);
    });

    Route::delete('/users/{user}', [UserController::class, 'destroy'])->middleware('super_admin');

    Route::middleware('super_admin')->group(function () {
        Route::get('/users/template/download', [UserExcelImportController::class, 'downloadTemplate']);
        Route::post('/users/import', [UserExcelImportController::class, 'import']);
    });
});

Route::get('/devices/{device}/status', [DeviceControlController::class, 'status']);
Route::post('/devices/{device}/on', [DeviceControlController::class, 'on']);
Route::post('/devices/{device}/off', [DeviceControlController::class, 'off']);
Route::post('/devices/toggle', [DeviceControlController::class, 'toggle']);
