<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SekretarisController;
use Illuminate\Support\Facades\Route;

// --- AUTH ---
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::get('/login', [AuthController::class, 'showLoginForm']);
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- ROUTE DASHBOARD ADMIN ---
Route::prefix('dashboard')->group(function () {
    // Tampilan Utama Admin (Dinamis dari AdminController)
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    
    // Fitur Monitoring Catatan Jurnal
    Route::get('/catatan-jurnal', [AdminController::class, 'catatanJurnal'])->name('catatan-jurnal');
    Route::post('/catatan-jurnal/validasi/{id}', [AdminController::class, 'validasiJurnal'])->name('catatan-jurnal.validasi');
    Route::post('/catatan-jurnal/inval/{id}', [AdminController::class, 'invalJurnal'])->name('catatan-jurnal.inval');

    // Route Guru CRUD
    Route::get('/guru', [AdminController::class, 'guru'])->name('dashboard.guru');
    Route::post('/guru', [AdminController::class, 'storeGuru'])->name('dashboard.guru.store');
    Route::put('/guru/{id}', [AdminController::class, 'updateGuru'])->name('dashboard.guru.update');
    Route::delete('/guru/{id}', [AdminController::class, 'destroyGuru'])->name('dashboard.guru.destroy');

    // Route Kelas CRUD
    Route::get('/kelas', [AdminController::class, 'kelas'])->name('dashboard.kelas');
    Route::post('/kelas', [AdminController::class, 'storeKelas'])->name('dashboard.kelas.store');
    Route::put('/kelas/{id}', [AdminController::class, 'updateKelas'])->name('dashboard.kelas.update');
    Route::delete('/kelas/{id}', [AdminController::class, 'destroyKelas'])->name('dashboard.kelas.destroy');
    
    // Route Siswa CRUD
    Route::get('/siswa', [AdminController::class, 'siswa'])->name('dashboard.siswa');
    Route::post('/siswa', [AdminController::class, 'storeSiswa'])->name('dashboard.siswa.store');
    Route::put('/siswa/{id}', [AdminController::class, 'updateSiswa'])->name('dashboard.siswa.update');
    Route::delete('/siswa/{id}', [AdminController::class, 'destroySiswa'])->name('dashboard.siswa.destroy');
    
    // Route Jadwal Pelajaran (Dinamis dari AdminController)
    Route::get('/jadwal', [AdminController::class, 'jadwal'])->name('dashboard.jadwal');
    Route::post('/jadwal', [AdminController::class, 'storeJadwal'])->name('dashboard.jadwal.store');
    Route::post('/jadwal/shift-time', [AdminController::class, 'shiftTimeJadwal'])->name('dashboard.jadwal.shift-time');
    Route::put('/jadwal/{id}', [AdminController::class, 'updateJadwal'])->name('dashboard.jadwal.update');
    Route::delete('/jadwal/{id}', [AdminController::class, 'destroyJadwal'])->name('dashboard.jadwal.destroy');

    // Route Mapel CRUD (35 Mapel, Kategori, Multi-Guru Pengampu)
    Route::get('/mapel', [AdminController::class, 'mapel'])->name('dashboard.mapel');
    Route::post('/mapel', [AdminController::class, 'storeMapel'])->name('dashboard.mapel.store');
    Route::put('/mapel/{id}', [AdminController::class, 'updateMapel'])->name('dashboard.mapel.update');
    Route::delete('/mapel/{id}', [AdminController::class, 'destroyMapel'])->name('dashboard.mapel.destroy');
    
    // Route Tambah Akun Pengguna & Manajemen User (4 Role: Admin, Waka, Piket, Sekretaris Kelas)
    Route::redirect('/tambah-akun', '/dashboard/admin/manajemen-user?tambah=1')->name('tambah-akun');
    Route::get('/admin/manajemen-user', [AdminController::class, 'user'])->name('admin.manajemen-user');
    Route::get('/manajemen-user', [AdminController::class, 'user']);
    Route::post('/admin/manajemen-user', [AdminController::class, 'storeUser'])->name('admin.manajemen-user.store');
    Route::put('/admin/manajemen-user/{id}', [AdminController::class, 'updateUser'])->name('admin.manajemen-user.update');
    Route::delete('/admin/manajemen-user/{id}', [AdminController::class, 'destroyUser'])->name('admin.manajemen-user.destroy');
    Route::post('/admin/manajemen-user/{id}/restore', [AdminController::class, 'restoreUser'])->name('admin.manajemen-user.restore');

    // Route Laporan Ganti Password (Notifikasi Admin)
    Route::post('/admin/laporan-ganti-pw/{id}/terima', [AdminController::class, 'terimaResetPassword'])->name('admin.laporan-pw.terima');
    Route::post('/admin/laporan-ganti-pw/{id}/tolak', [AdminController::class, 'tolakResetPassword'])->name('admin.laporan-pw.tolak');

    // Route Piket
    Route::view('/piket', 'dashboard.piket.utama')->name('dashboard.piket');
});

// Route Public Pengajuan Laporan Reset Password (dari Halaman Login)
Route::post('/laporan-ganti-pw', [AuthController::class, 'kirimLaporanReset'])->name('laporan-pw.kirim');

// --- ROUTE SEKRETARIS ---
Route::prefix('sekretaris')->group(function () {
    Route::get('/jurnal', [SekretarisController::class, 'jurnalIndex'])->name('sekretaris.jurnal.index');
    Route::get('/jurnal/create', [SekretarisController::class, 'jurnalCreate'])->name('sekretaris.jurnal.create');
    Route::get('/jadwal', [SekretarisController::class, 'jadwal'])->name('sekretaris.jadwal');
    Route::get('/notifikasi', [SekretarisController::class, 'notifikasi'])->name('sekretaris.notifikasi');
    Route::post('/notifikasi/read-all', [SekretarisController::class, 'readAllNotifikasi'])->name('sekretaris.notifikasi.read-all');
    Route::post('/notifikasi/{id}/read', [SekretarisController::class, 'readNotifikasi'])->name('sekretaris.notifikasi.read');
});

// --- ROUTE GURU ---
Route::prefix('guru')->group(function () {
    Route::view('/logbook/create', 'guru.logbook.create')->name('guru.logbook.create');
});
