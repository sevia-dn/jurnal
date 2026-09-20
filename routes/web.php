<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DispensasiApprovalController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\JadwalMengajarController;
use App\Http\Controllers\LogbookController;
use App\Http\Controllers\PiketController;
use Illuminate\Support\Facades\Route;

// ==========================================
// 1. AREA GUEST (Belum Login)
// ==========================================

Route::middleware('guest')->group(function () {

    Route::get('/', [AuthController::class, 'showLoginForm']);

    Route::get('/login', [AuthController::class, 'showLoginForm'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login']);

});

// ==========================================
// 2. AREA AUTH (Sudah Login)
// ==========================================

Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // --- APPROVAL DISPENSASI (WAKA) & CETAK ---
    Route::get('/dispensasi/approval/{token}', [DispensasiApprovalController::class, 'show'])->name('dispensasi.approval');
    Route::post('/dispensasi/approval/{dispensasi}', [DispensasiApprovalController::class, 'process'])->name('dispensasi.process');
    Route::get('/dispensasi/{dispensasi}/cetak', [DispensasiApprovalController::class, 'cetakSurat'])->name('dispensasi.cetak');

    // ==========================================
    // DASHBOARD ADMIN
    // ==========================================

    Route::prefix('dashboard')->group(function () {

        Route::view('/', 'dashboard.admin.admin')
            ->name('dashboard');

        Route::view('/catatan-jurnal', 'dashboard.admin.catatan-jurnal')
            ->name('catatan-jurnal');

        Route::view('/kelas', 'dashboard.admin.kelas')
            ->name('dashboard.kelas');

        Route::view('/siswa', 'dashboard.admin.siswa')
            ->name('dashboard.siswa');

        Route::view('/tambah-akun', 'dashboard.admin.tambah-akun')
            ->name('tambah-akun');

        Route::view('/piket', 'dashboard.piket.utama')
            ->name('dashboard.piket');

        Route::view('/manajemen-user', 'dashboard.admin.manajemen-user')
            ->name('admin.manajemen-user');

        // --- DATA GURU ---
        Route::get('/guru', [JadwalMengajarController::class, 'guruIndex'])
            ->name('dashboard.guru');

        // --- JADWAL MENGAJAR ---
        Route::get('/jadwal', [JadwalMengajarController::class, 'index'])
            ->name('dashboard.jadwal');

        Route::get('/jadwal/create', [JadwalMengajarController::class, 'create'])
            ->name('jadwal.create');

        Route::post('/jadwal', [JadwalMengajarController::class, 'store'])
            ->name('jadwal.store');

        // --- DATA MAPEL ---
        Route::view('/mapel', 'dashboard.admin.mapel')
            ->name('dashboard.mapel');

        // --- Guru Piket: Kehadiran Siswa ---
        Route::get('/piket/kehadiran-siswa', [PiketController::class, 'kehadiranSiswa'])
            ->name('piket.kehadiran-siswa');

        Route::post('/piket/kehadiran-siswa', [PiketController::class, 'updateKehadiranSiswa'])
            ->name('piket.kehadiran-siswa.update');

        // --- Guru Piket: Kehadiran & Dispensasi ---
        Route::get('/piket/kehadiran', [PiketController::class, 'kehadiran'])
            ->name('piket.kehadiran');

        Route::post('/piket/kehadiran/{kehadiran}/verifikasi', [PiketController::class, 'verifikasiKehadiran'])
            ->name('piket.kehadiran.verifikasi');

        Route::get('/piket/dispensasi', [PiketController::class, 'dispensasiForm'])
            ->name('piket.dispensasi.form');

        Route::post('/piket/dispensasi', [PiketController::class, 'dispensasiStore'])
            ->name('piket.dispensasi.store');

    });

    // ==========================================
    // PENGURUS KELAS
    // ==========================================

    Route::prefix('pengurus-kelas')->group(function () {

        Route::view('/dashboard', 'dashboard.pengurus-kelas.utama')
            ->name('pengurus-kelas.dashboard');

        Route::view('/jadwal', 'dashboard.pengurus-kelas.jadwal')
            ->name('pengurus-kelas.jadwal');

        Route::view('/jurnal-detail', 'dashboard.pengurus-kelas.jurnal-detail')
            ->name('pengurus-kelas.jurnal-detail');

        Route::view('/kehadiran-guru', 'dashboard.pengurus-kelas.kehadiran-guru')
            ->name('pengurus-kelas.kehadiran-guru');

        Route::view('/kehadiran-siswa', 'dashboard.pengurus-kelas.kehadiran-siswa')
            ->name('pengurus-kelas.kehadiran-siswa');

    });

    // ==========================================
    // GURU PENGAJAR & WAKA
    // ==========================================

    Route::get('/guru-pengajar', [GuruController::class, 'beranda'])
        ->name('guru');

    Route::get('/guru-pengajar/beranda', [GuruController::class, 'beranda'])
        ->name('guru.utama');

    Route::post('/guru-pengajar/absen-masuk', [GuruController::class, 'absenMasuk'])
        ->name('guru.absen-masuk');

    Route::post('/guru-pengajar/absen', [GuruController::class, 'storeAbsen'])
        ->name('guru.absen.store');

    Route::post('/guru-pengajar/jurnal', [LogbookController::class, 'store'])
        ->name('guru.jurnal.store');

    Route::get('/guru-pengajar/riwayat', [LogbookController::class, 'history'])
        ->name('guru.riwayat');

});
