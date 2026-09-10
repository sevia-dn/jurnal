<?php

use Illuminate\Support\Facades\Route;

// --- AUTH ---
Route::view('/', 'auth.login');
Route::view('/login', 'auth.login')->name('login');

// --- ROUTE DASHBOARD ADMIN ---
Route::prefix('dashboard')->group(function () {
    // Tampilan Utama Admin
    Route::view('/', 'dashboard.admin.admin')->name('dashboard');
    
    // Fitur-fitur Admin (Sesuai struktur folder dashboard/admin/ di gambar)
    Route::view('/catatan-jurnal', 'dashboard.admin.catatan-jurnal')->name('catatan-jurnal');
    Route::view('/guru', 'dashboard.admin.guru')->name('dashboard.guru');
    Route::view('/kelas', 'dashboard.admin.kelas')->name('dashboard.kelas');
    
    // Ini jawaban untuk route siswa (sudah nyambung ke siswa.blade.php di dalam folder admin)
    Route::view('/siswa', 'dashboard.admin.siswa')->name('dashboard.siswa');
    
    Route::view('/jadwal', 'dashboard.admin.jadwal')->name('dashboard.jadwal');
    Route::view('/mapel', 'dashboard.admin.mapel')->name('dashboard.mapel');
    
    // Pastikan file "tambah-akun.balde.php" sudah Anda rename menjadi "tambah-akun.blade.php"
    Route::view('/tambah-akun', 'dashboard.admin.tambah-akun')->name('tambah-akun');

    // Route Piket (Di gambar, folder 'piket' ada di luar 'admin', tapi di dalam 'dashboard')
    Route::view('/piket', 'dashboard.piket.utama')->name('dashboard.piket');

    Route::get('/dashboard/admin/manajemen-user', function () {
    return view('dashboard.admin.manajemen-user');
})->name('admin.manajemen-user');
});

// --- ROUTE SEKRETARIS (LIA) ---
Route::prefix('sekretaris')->group(function () {
    Route::view('/jurnal', 'jurnal.index')->name('sekretaris.jurnal.index');
    Route::view('/jurnal/create', 'jurnal.create')->name('sekretaris.jurnal.create');
    Route::view('/jadwal', 'jurnal.jadwal.index')->name('sekretaris.jadwal');
    
    // Asumsi folder notifikasi ada di resources/views/notifikasi/index.blade.php
    Route::view('/notifikasi', 'notifikasi.index')->name('sekretaris.notifikasi'); 
});

// --- ROUTE GURU ---
Route::prefix('guru')->group(function () {
    // Sesuai dengan folder views/guru/logbook/create.blade.php di gambar
    Route::view('/logbook/create', 'guru.logbook.create')->name('guru.logbook.create');
});