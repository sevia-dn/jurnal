<?php

use Illuminate\Support\Facades\Route;

// Route Preview Kelas
Route::get('/preview/kelas', function () {
    return view('kelas.index');
});

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
});