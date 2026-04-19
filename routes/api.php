<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DeviceControlController;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\JadwalListrikController;
use App\Http\Controllers\Api\RuanganController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
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
        Route::delete('/users/{user}', [UserController::class, 'destroy']);

        Route::post('/devices', [DeviceController::class, 'store']);
        Route::put('/devices/{device}', [DeviceController::class, 'update']);
        Route::patch('/devices/{device}', [DeviceController::class, 'update']);
        Route::delete('/devices/{device}', [DeviceController::class, 'destroy']);

        Route::post('/devices/toggle', [DeviceControlController::class, 'toggle']);

        Route::post('/jadwal-listrik', [JadwalListrikController::class, 'store']);
        Route::put('/jadwal-listrik/{jadwal}', [JadwalListrikController::class, 'update']);
        Route::patch('/jadwal-listrik/{jadwal}', [JadwalListrikController::class, 'update']);
        Route::delete('/jadwal-listrik/{jadwal}', [JadwalListrikController::class, 'destroy']);
    });
});
