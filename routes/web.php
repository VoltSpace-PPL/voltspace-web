<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

// Blade routes for the bundled frontend (views live in resources/views).
Route::view('/login', 'auth.login')->name('login');
Route::view('/dashboard', 'dashboard.index');
Route::view('/users', 'users.index');
Route::view('/rooms', 'rooms.index');

use App\Http\Controllers\RoomAvailabilityController;

Route::view('/room-availability', 'room-availability.index');
Route::get('/room-availability/{id}', [RoomAvailabilityController::class, 'show']);
Route::view('/devices', 'devices.index');
Route::view('/schedule', 'schedule.index');
Route::view('/bookings', 'bookings.index');
Route::view('/settings', 'settings.index');
Route::view('/alerts', 'alerts.index');
Route::view('/reports', 'reports.index');

Route::view('/student/dashboard', 'student.dashboard');
Route::view('/student/rooms', 'student.rooms');
Route::view('/student/room-availability', 'student.room-availability');
Route::get('/student/room-availability/{id}', [RoomAvailabilityController::class, 'show']);
Route::view('/student/bookings/create', 'student.bookings-create');
Route::view('/student/bookings', 'student.bookings');

// Keep a simple JSON health endpoint for the backend.
Route::get('/health', function () {
    return response()->json([
        'message' => 'VoltSpace Backend API is running.',
    ]);
});