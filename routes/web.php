<?php

use Illuminate\Support\Facades\Route;

// Route Preview Kelas
Route::get('/preview/kelas', function () {
    return view('kelas.index');
});


// --- PREVIEW UI PITA ---
Route::view('/', 'auth.login');
Route::view('/login', 'auth.login')->name('login');

// Dashboard (Admin)
Route::view('/dashboard', 'dashboard.admin')->name('dashboard');
Route::view('/dashboard/catatan-jurnal', 'dashboard.catatan-jurnal')->name('catatan-jurnal');
 
Route::get('/preview/kelas/create', function () {
    return view('kelas.create');
});

Route::post('/preview/kelas/store', function () {
    return redirect('/preview/kelas');
});

// Route Edit Kelas
Route::get('/preview/kelas/edit', function () {
    return view('kelas.edit');
});

Route::post('/preview/kelas/update', function () {
    return redirect('/preview/kelas');
});

// Route Hapus Kelas dengan Alasan
Route::get('/preview/kelas/delete', function () {
    return view('kelas.delete');
});

Route::post('/preview/kelas/destroy', function () {
    return redirect('/preview/kelas');
});

// Route Detail Siswa dalam Kelas tertentu
Route::get('/preview/kelas/siswa', function () {
    return view('kelas.siswa');
});

Route::get('/preview/kelas/siswa/create', function () {
    return view('kelas.siswa-create');
});

Route::post('/preview/kelas/siswa/store', function () {
    return redirect('/preview/kelas/siswa');
});

// Route Edit Siswa
Route::get('/preview/kelas/siswa/edit', function () {
    return view('kelas.siswa-edit');
});
Route::post('/preview/kelas/siswa/update', function () {
    return redirect('/preview/kelas/siswa');
});

// Route Hapus Siswa dengan Alasan
Route::get('/preview/kelas/siswa/delete', function () {
    return view('kelas.siswa-delete');
});
Route::post('/preview/kelas/siswa/destroy', function () {
    return redirect('/preview/kelas/siswa');
Route::get('/', function () {
    return redirect()->route('sekretaris.jurnal.index');
});

// ROUTE SEKRETARIS
Route::prefix('sekretaris')->group(function () {
    // Dashboard Utama
    Route::get('/jurnal', function () {
        return view('jurnal.index');
    })->name('sekretaris.jurnal.index');

    // Form Konfirmasi Guru
    Route::get('/jurnal/create', function () {
        return view('jurnal.create');
    })->name('sekretaris.jurnal.create');

    // Notifikasi (Pure Info)
    Route::get('/notifikasi', function () {
        return view('notifikasi.index');
    })->name('sekretaris.notifikasi');

    // Jadwal 10 Sesi Hari Ini
    Route::get('/jadwal', function () {
        return view('jurnal.jadwal.index'); // <-- Memanggil file di resources/views/jurnal/jadwal/index.blade.php
    })->name('sekretaris.jadwal');
});

    // ROUTE GURU
    Route::prefix('guru')->group(function () {
        Route::get('/logbook/create', function () {
            return view('guru.logbook.create');
        })->name('guru.logbook.create');
});