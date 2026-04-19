<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Blade routes for the bundled frontend (views live in resources/views).
Route::view('/login', 'auth.login');
Route::view('/users', 'users.index');
Route::view('/rooms', 'rooms.index');

// Keep a simple JSON health endpoint for the backend.
Route::get('/health', function () {
    return response()->json([
        'message' => 'VoltSpace Backend API is running.',
    ]);
});
