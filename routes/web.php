<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DispensasiApprovalController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\JadwalMengajarController;
use App\Http\Controllers\LogbookController;
use App\Http\Controllers\PengurusKelasController;
use App\Http\Controllers\PiketController;
use Illuminate\Support\Facades\Route;

// ==========================================
// 1. AREA GUEST
// ==========================================

// Public routes – always show the login form regardless of authentication state.
Route::get('/', [AuthController::class, 'showLoginForm']);
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Retain any guest‑only routes in a separate group (currently none).
Route::middleware('guest')->group(function () {
    // Add guest‑only routes here if needed.
});

// ==========================================
// 2. AREA AUTH
// ==========================================

Route::middleware('auth')->group(function () {

    // ==========================================
    // LOGOUT
    // ==========================================

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

    Route::get('/dispensasi/approval/{token}', [DispensasiApprovalController::class, 'show'])
        ->name('dispensasi.approval');

    Route::post('/dispensasi/approval/{dispensasi}', [DispensasiApprovalController::class, 'process'])
        ->name('dispensasi.process');

    Route::get('/dispensasi/{dispensasi}/cetak', [DispensasiApprovalController::class, 'cetakSurat'])
        ->name('dispensasi.cetak');

    // ==========================================
    // DASHBOARD ADMIN
    // ==========================================

    Route::prefix('dashboard')->group(function () {

        Route::get('/', [AdminController::class, 'index'])
            ->name('dashboard');

        Route::get('/catatan-jurnal', [AdminController::class, 'catatanJurnal'])
            ->name('catatan-jurnal');

        Route::get('/kelas', [AdminController::class, 'kelas'])
            ->name('dashboard.kelas');

        Route::get('/siswa', [AdminController::class, 'siswa'])
            ->name('dashboard.siswa');

        Route::view('/tambah-akun', 'dashboard.admin.tambah-akun')
            ->name('tambah-akun');

        Route::get('/piket', [PiketController::class, 'utama'])
            ->name('dashboard.piket');

        Route::get('/manajemen-user', [AdminController::class, 'user'])
            ->name('admin.manajemen-user');
        Route::post('/manajemen-user', [AdminController::class, 'storeUser'])
            ->name('admin.manajemen-user.store');
        Route::put('/manajemen-user/{id}', [AdminController::class, 'updateUser'])
            ->name('admin.manajemen-user.update');
        Route::delete('/manajemen-user/{id}', [AdminController::class, 'destroyUser'])
            ->name('admin.manajemen-user.destroy');

        Route::get('/piket/kehadiran', [PiketController::class, 'kehadiran'])
            ->name('piket.kehadiran');

        Route::get('/piket/kehadiran/lapor', [PiketController::class, 'laporKehadiranForm'])
            ->name('piket.kehadiran.form');

        Route::get('/piket/jurnal/{jurnal}', [PiketController::class, 'jurnalDetail'])
            ->name('piket.jurnal.show');

        Route::post('/piket/kehadiran', [PiketController::class, 'storeKehadiranGuru'])
            ->name('piket.kehadiran.store');

        Route::post('/piket/kehadiran/{kehadiran}/verifikasi', [PiketController::class, 'verifikasiKehadiran'])
            ->name('piket.kehadiran.verifikasi');

        Route::get('/piket/dispensasi', [PiketController::class, 'dispensasiForm'])
            ->name('piket.dispensasi.form');

        Route::post('/piket/dispensasi', [PiketController::class, 'dispensasiStore'])
            ->name('piket.dispensasi.store');

        Route::get('/piket/kehadiran-siswa', [PiketController::class, 'kehadiranSiswa'])
            ->name('piket.kehadiran-siswa');

        Route::post('/piket/kehadiran-siswa', [PiketController::class, 'updateKehadiranSiswa'])
            ->name('piket.kehadiran-siswa.update');

        // ==========================================
        // DATA GURU
        // ==========================================

        Route::get('/guru', [AdminController::class, 'guru'])->name('dashboard.guru');
        Route::post('/guru', [AdminController::class, 'storeGuru'])->name('dashboard.guru.store');
        Route::put('/guru/{id}', [AdminController::class, 'updateGuru'])->name('dashboard.guru.update');
        Route::delete('/guru/{id}', [AdminController::class, 'destroyGuru'])->name('dashboard.guru.destroy');
        Route::post('/guru/batch-delete', [AdminController::class, 'batchDeleteGuru'])->name('dashboard.guru.batch-delete');
        Route::post('/guru/batch-edit', [AdminController::class, 'batchEditGuru'])->name('dashboard.guru.batch-edit');
        Route::post('/guru/import', [AdminController::class, 'importGuru'])->name('dashboard.guru.import');
        Route::get('/guru/download-template', [AdminController::class, 'downloadTemplateGuru'])->name('dashboard.guru.download-template');

        // ==========================================
        // JADWAL MENGAJAR
        // ==========================================

        Route::get('/jadwal', [AdminController::class, 'jadwal'])->name('dashboard.jadwal');

        // FORM TAMBAH JADWAL

        Route::get(
            '/jadwal/create',
            [JadwalMengajarController::class, 'create']
        )->name('jadwal.create');

        // SIMPAN JADWAL

        Route::post('/jadwal', [AdminController::class, 'storeJadwal'])->name('dashboard.jadwal.store');
        Route::put('/jadwal/{id}', [AdminController::class, 'updateJadwal'])->name('dashboard.jadwal.update');
        Route::delete('/jadwal/{id}', [AdminController::class, 'destroyJadwal'])->name('dashboard.jadwal.destroy');
        Route::post('/jadwal/batch-delete', [AdminController::class, 'batchDeleteJadwal'])->name('dashboard.jadwal.batch-delete');
        Route::post('/jadwal/shift-time', [AdminController::class, 'shiftTimeJadwal'])->name('dashboard.jadwal.shift-time');
        Route::post('/jadwal/import', [AdminController::class, 'importJadwal'])->name('dashboard.jadwal.import');
        Route::get('/jadwal/download-template', [AdminController::class, 'downloadTemplateJadwal'])->name('dashboard.jadwal.download-template');

        // ==========================================
        // DATA MAPEL
        // ==========================================

        Route::get('/mapel', [AdminController::class, 'mapel'])->name('dashboard.mapel');
        Route::post('/mapel', [AdminController::class, 'storeMapel'])->name('dashboard.mapel.store');
        Route::put('/mapel/{id}', [AdminController::class, 'updateMapel'])->name('dashboard.mapel.update');
        Route::delete('/mapel/{id}', [AdminController::class, 'destroyMapel'])->name('dashboard.mapel.destroy');
        Route::post('/mapel/batch-delete', [AdminController::class, 'batchDeleteMapel'])->name('dashboard.mapel.batch-delete');

        Route::post('/kelas', [AdminController::class, 'storeKelas'])->name('dashboard.kelas.store');
        Route::put('/kelas/{id}', [AdminController::class, 'updateKelas'])->name('dashboard.kelas.update');
        Route::delete('/kelas/{id}', [AdminController::class, 'destroyKelas'])->name('dashboard.kelas.destroy');
        Route::post('/kelas/batch-delete', [AdminController::class, 'batchDeleteKelas'])->name('dashboard.kelas.batch-delete');
        Route::post('/siswa', [AdminController::class, 'storeSiswa'])->name('dashboard.siswa.store');
        Route::put('/siswa/{id}', [AdminController::class, 'updateSiswa'])->name('dashboard.siswa.update');
        Route::delete('/siswa/{id}', [AdminController::class, 'destroySiswa'])->name('dashboard.siswa.destroy');
        Route::post('/siswa/batch-delete', [AdminController::class, 'batchDeleteSiswa'])->name('dashboard.siswa.batch-delete');
        Route::post('/siswa/batch-edit', [AdminController::class, 'batchEditSiswa'])->name('dashboard.siswa.batch-edit');
        Route::get('/rekap-jurnal', [AdminController::class, 'rekapJurnal'])->name('dashboard.rekap-jurnal');
        Route::post('/rekap-jurnal/penugasan-piket', [AdminController::class, 'updatePenugasanPiket'])->name('dashboard.rekap-jurnal.penugasan-piket');
        Route::get('/pengaturan', [AdminController::class, 'pengaturan'])->name('admin.pengaturan');
        Route::post('/pengaturan', [AdminController::class, 'updatePengaturan'])->name('admin.pengaturan.update');

    });

    // ==========================================
    // PENGURUS KELAS
    // ==========================================

    Route::prefix('pengurus-kelas')->group(function () {

        Route::get(
            '/dashboard',
            [PengurusKelasController::class, 'dashboard']
        )->name('pengurus-kelas.dashboard');

        Route::get(
            '/jurnal-detail/{id?}',
            function ($id = null) {
                if ($id) {
                    return app(PengurusKelasController::class)->jurnalDetail($id);
                }

                return app(PengurusKelasController::class)->jurnalIndex(request());
            }
        )->name('pengurus-kelas.jurnal-detail');

        Route::post(
            '/jurnal-validasi/{id}',
            [PengurusKelasController::class, 'validasiJurnal']
        )->name('pengurus-kelas.jurnal-validasi');

        Route::get(
            '/kehadiran-guru',
            [PengurusKelasController::class, 'kehadiranGuru']
        )->name('pengurus-kelas.kehadiran-guru');

        Route::get(
            '/kehadiran-siswa',
            [PengurusKelasController::class, 'kehadiranSiswa']
        )->name('pengurus-kelas.kehadiran-siswa');

        Route::post(
            '/notifikasi/read-all',
            [PengurusKelasController::class, 'markAllNotificationsRead']
        )->name('pengurus-kelas.notifikasi.read-all');

    });

    // ==========================================
    // GURU PENGAJAR
    // ==========================================

    Route::get('/guru-pengajar', [GuruController::class, 'beranda'])
        ->name('guru');

    Route::get(
        '/guru-pengajar/beranda',
        [GuruController::class, 'beranda']
    )->name('guru.utama');

    Route::post(
        '/guru-pengajar/absen',
        [GuruController::class, 'storeAbsen']
    )->name('guru.absen.store');

    Route::post('/guru-pengajar/absen-masuk', [GuruController::class, 'absenMasuk'])
        ->name('guru.absen-masuk');

    Route::post('/guru-pengajar/notifikasi/{notifikasi}/read', [GuruController::class, 'markNotificationRead'])
        ->name('guru.notifikasi.read');

    Route::post('/guru-pengajar/notifikasi/read-all', [GuruController::class, 'markAllNotificationsRead'])
        ->name('guru.notifikasi.read-all');

    Route::post(
        '/guru-pengajar/jurnal',
        [LogbookController::class, 'store']
    )->name('guru.jurnal.store');

    Route::get(
        '/guru-pengajar/riwayat',
        [LogbookController::class, 'history']
    )->name('guru.riwayat');

});
