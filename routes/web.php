<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Blade routes for the bundled frontend (views live in resources/views).
Route::view('/login', 'auth.login')->name('login');
Route::view('/dashboard', 'dashboard.index');
Route::view('/users', 'users.index');
Route::view('/rooms', 'rooms.index');
Route::view('/devices', 'devices.index');
Route::view('/schedule', 'schedule.index');
Route::view('/settings', 'settings.index');
<<<<<<< HEAD

// Student Portal Routes (PBI 30, 31, 32)
Route::view('/student/dashboard', 'student.dashboard');
Route::view('/student/rooms', 'student.rooms');
Route::view('/student/bookings/create', 'student.bookings-create');
Route::view('/student/bookings', 'student.bookings');
=======
>>>>>>> 2873898c6c276f7d11904636f170978514e680f0

// Keep a simple JSON health endpoint for the backend.
Route::get('/health', function () {
    return response()->json([
        'message' => 'VoltSpace Backend API is running.',
    ]);
});
