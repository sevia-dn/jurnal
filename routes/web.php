<?php

use Illuminate\Support\Facades\Route;

// Lia - UI Jurnal & Absensi

// Dashboard Utama (4 Kartu Ringkasan)
Route::get('/', function () {
    return view('jurnal.index');
});

// Halaman Isi Jurnal (Form)
Route::get('/create', function () {
    return view('jurnal.create');
});