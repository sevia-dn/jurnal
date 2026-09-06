<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


// --- PREVIEW UI PITA ---
Route::view('/', 'auth.login');
Route::view('/login', 'auth.login')->name('login');

// Dummy Route ke Dashboard 
Route::view('/dashboard', 'dashboard')->name('dashboard');