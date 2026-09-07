<?php

use Illuminate\Support\Facades\Route;

// --- AUTH & DASHBOARD ADMIN ---
Route::view('/', 'auth.login');
Route::view('/login', 'auth.login')->name('login');

Route::view('/dashboard', 'dashboard.admin')->name('dashboard');
Route::view('/dashboard/catatan-jurnal', 'dashboard.catatan-jurnal')->name('catatan-jurnal');

// --- ROUTE PREVIEW KELAS & SISWA (RAGA) ---
Route::get('/preview/kelas', function () {
    return view('kelas.index');
});

Route::get('/preview/kelas/create', function () {
    return view('kelas.create');
});

Route::post('/preview/kelas/store', function () {
    return redirect('/preview/kelas');
});

Route::get('/preview/kelas/edit', function () {
    return view('kelas.edit');
});

Route::post('/preview/kelas/update', function () {
    return redirect('/preview/kelas');
});

Route::get('/preview/kelas/delete', function () {
    return view('kelas.delete');
});

Route::post('/preview/kelas/destroy', function () {
    return redirect('/preview/kelas');
});

Route::get('/preview/kelas/siswa', function () {
    return view('kelas.siswa');
});

Route::get('/preview/kelas/siswa/create', function () {
    return view('kelas.siswa-create');
});

Route::post('/preview/kelas/siswa/store', function () {
    return redirect('/preview/kelas/siswa');
});

Route::get('/preview/kelas/siswa/edit', function () {
    return view('kelas.siswa-edit');
});

Route::post('/preview/kelas/siswa/update', function () {
    return redirect('/preview/kelas/siswa');
});

Route::get('/preview/kelas/siswa/delete', function () {
    return view('kelas.siswa-delete');
});

// Perbaikan sintaks yang error sebelumnya:
Route::post('/preview/kelas/siswa/destroy', function () {
    return redirect('/preview/kelas/siswa');
});

// --- ROUTE SEKRETARIS (LIA) ---
Route::prefix('sekretaris')->group(function () {
    Route::get('/jurnal', function () {
        return view('jurnal.index');
    })->name('sekretaris.jurnal.index');

    Route::get('/jurnal/create', function () {
        return view('jurnal.create');
    })->name('sekretaris.jurnal.create');

    Route::get('/notifikasi', function () {
        return view('notifikasi.index');
    })->name('sekretaris.notifikasi');

    Route::get('/jadwal', function () {
        return view('jurnal.jadwal.index');
    })->name('sekretaris.jadwal');
});

// --- ROUTE GURU ---
Route::prefix('guru')->group(function () {
    Route::get('/logbook/create', function () {
        return view('guru.logbook.create');
    })->name('guru.logbook.create');
});