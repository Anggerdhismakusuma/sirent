<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';

// Route khusus Admin
Route::get('/test-admin', function () {
    return "Halo Admin! Kamu berhasil masuk ke halaman rahasia.";
})->middleware(['auth', 'role:admin']);

// Route khusus Pemilik
Route::get('/test-pemilik', function () {
    return "Halo Pemilik! Ini halaman khusus buat kamu.";
})->middleware(['auth', 'role:pemilik']);
