<?php

use Illuminate\Support\Facades\Route;

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