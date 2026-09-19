<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\PiketController;
use App\Http\Controllers\SekretarisController;
use Illuminate\Support\Facades\Route;

// ==========================================
// 1. AREA GUEST (Belum Login)
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLoginForm']);
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::post('/laporan-ganti-pw', [AuthController::class, 'kirimLaporanReset'])->name('laporan-pw.kirim');
});

// ==========================================
// 2. AREA AUTH (Sudah Login)
// ==========================================
Route::middleware('auth')->group(function () {

    // Proses Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // --- DASHBOARD ADMIN ---
    Route::prefix('dashboard')->group(function () {
        // Halaman Utama Admin
        Route::get('/', [AdminController::class, 'index'])->name('dashboard');
        Route::get('/admin', [AdminController::class, 'index'])->name('dashboard.admin');

        // Monitoring Rekap Jurnal
        Route::get('/rekap-jurnal', [AdminController::class, 'rekapJurnal'])->name('dashboard.rekap-jurnal');
        Route::post('/rekap-jurnal/penugasan-piket', [AdminController::class, 'updatePenugasanPiket'])->name('dashboard.rekap-jurnal.penugasan-piket');
        Route::get('/catatan-jurnal', fn() => redirect()->route('dashboard.rekap-jurnal'))->name('catatan-jurnal');

        // --- GURU CRUD, BATCH & IMPORT ---
        Route::get('/guru', [AdminController::class, 'guru'])->name('dashboard.guru');
        Route::post('/guru', [AdminController::class, 'storeGuru'])->name('dashboard.guru.store');
        Route::put('/guru/{id}', [AdminController::class, 'updateGuru'])->name('dashboard.guru.update');
        Route::delete('/guru/{id}', [AdminController::class, 'destroyGuru'])->name('dashboard.guru.destroy');
        Route::post('/guru/batch-delete', [AdminController::class, 'batchDeleteGuru'])->name('dashboard.guru.batch-delete');
        Route::post('/guru/batch-edit', [AdminController::class, 'batchEditGuru'])->name('dashboard.guru.batch-edit');
        Route::post('/guru/import', [AdminController::class, 'importGuru'])->name('dashboard.guru.import');
        Route::get('/guru/download-template', [AdminController::class, 'downloadTemplateGuru'])->name('dashboard.guru.download-template');

        // --- KELAS CRUD & BATCH DELETE ---
        Route::get('/kelas', [AdminController::class, 'kelas'])->name('dashboard.kelas');
        Route::post('/kelas', [AdminController::class, 'storeKelas'])->name('dashboard.kelas.store');
        Route::put('/kelas/{id}', [AdminController::class, 'updateKelas'])->name('dashboard.kelas.update');
        Route::delete('/kelas/{id}', [AdminController::class, 'destroyKelas'])->name('dashboard.kelas.destroy');
        Route::post('/kelas/batch-delete', [AdminController::class, 'batchDeleteKelas'])->name('dashboard.kelas.batch-delete');

        // --- SISWA CRUD, BATCH DELETE & EDIT ---
        Route::get('/siswa', [AdminController::class, 'siswa'])->name('dashboard.siswa');
        Route::post('/siswa', [AdminController::class, 'storeSiswa'])->name('dashboard.siswa.store');
        Route::put('/siswa/{id}', [AdminController::class, 'updateSiswa'])->name('dashboard.siswa.update');
        Route::delete('/siswa/{id}', [AdminController::class, 'destroySiswa'])->name('dashboard.siswa.destroy');
        Route::post('/siswa/batch-delete', [AdminController::class, 'batchDeleteSiswa'])->name('dashboard.siswa.batch-delete');
        Route::post('/siswa/batch-edit', [AdminController::class, 'batchEditSiswa'])->name('dashboard.siswa.batch-edit');

        // --- MAPEL CRUD & BATCH DELETE ---
        Route::get('/mapel', [AdminController::class, 'mapel'])->name('dashboard.mapel');
        Route::post('/mapel', [AdminController::class, 'storeMapel'])->name('dashboard.mapel.store');
        Route::put('/mapel/{id}', [AdminController::class, 'updateMapel'])->name('dashboard.mapel.update');
        Route::delete('/mapel/{id}', [AdminController::class, 'destroyMapel'])->name('dashboard.mapel.destroy');
        Route::post('/mapel/batch-delete', [AdminController::class, 'batchDeleteMapel'])->name('dashboard.mapel.batch-delete');

        // --- JADWAL PELAJARAN CRUD & BATCH DELETE ---
        Route::get('/jadwal', [AdminController::class, 'jadwal'])->name('dashboard.jadwal');
        Route::post('/jadwal', [AdminController::class, 'storeJadwal'])->name('dashboard.jadwal.store');
        Route::post('/jadwal/shift-time', [AdminController::class, 'shiftTimeJadwal'])->name('dashboard.jadwal.shift-time');
        Route::put('/jadwal/{id}', [AdminController::class, 'updateJadwal'])->name('dashboard.jadwal.update');
        Route::delete('/jadwal/{id}', [AdminController::class, 'destroyJadwal'])->name('dashboard.jadwal.destroy');
        Route::post('/jadwal/batch-delete', [AdminController::class, 'batchDeleteJadwal'])->name('dashboard.jadwal.batch-delete');
        Route::post('/jadwal/import', [AdminController::class, 'importJadwal'])->name('dashboard.jadwal.import');
        Route::get('/jadwal/download-template', [AdminController::class, 'downloadTemplateJadwal'])->name('dashboard.jadwal.download-template');

        // --- MANAJEMEN USER ---
        Route::get('/admin/manajemen-user', [AdminController::class, 'user'])->name('admin.manajemen-user');
        Route::get('/manajemen-user', [AdminController::class, 'user']);
        Route::post('/admin/manajemen-user', [AdminController::class, 'storeUser'])->name('admin.manajemen-user.store');
        Route::put('/admin/manajemen-user/{id}', [AdminController::class, 'updateUser'])->name('admin.manajemen-user.update');
        Route::delete('/admin/manajemen-user/{id}', [AdminController::class, 'destroyUser'])->name('admin.manajemen-user.destroy');
        Route::redirect('/tambah-akun', '/dashboard/admin/manajemen-user?tambah=1')->name('tambah-akun');

        // --- PENGATURAN SISTEM & JAM JADWAL ---
        Route::get('/pengaturan', [AdminController::class, 'pengaturan'])->name('admin.pengaturan');
        Route::post('/pengaturan', [AdminController::class, 'updatePengaturan'])->name('admin.pengaturan.update');

        // --- PIKET (DARI DEV DILENGKAPI KONTROLLER & WAKA BACKUP) ---
        Route::get('/piket', [PiketController::class, 'index'])->name('dashboard.piket');
        Route::get('/piket/kehadiran', [PiketController::class, 'kehadiran'])->name('piket.kehadiran');
        Route::get('/piket/dispensasi', [PiketController::class, 'dispensasi'])->name('piket.dispensasi');
        Route::get('/piket/kehadiran-siswa', [PiketController::class, 'kehadiranSiswa'])->name('piket.kehadiran-siswa');
        Route::post('/piket/validasi-izin/{id}', [PiketController::class, 'validasiIzinGuru'])->name('piket.validasi-izin');
    });

    // --- PENGURUS KELAS / SEKRETARIS (DARI DEV DILENGKAPI KONTROLLER SINKRON) ---
    Route::prefix('pengurus-kelas')->group(function () {
        Route::get('/jurnal', [SekretarisController::class, 'jurnalIndex'])->name('pengurus-kelas.jurnal.index');
        Route::get('/jurnal/create', [SekretarisController::class, 'jurnalCreate'])->name('pengurus-kelas.jurnal.create');
        Route::get('/notifikasi', [SekretarisController::class, 'notifikasi'])->name('pengurus-kelas.notifikasi');
        Route::get('/jadwal', [SekretarisController::class, 'jadwal'])->name('pengurus-kelas.jadwal');
    });

    // --- GURU (DARI DEV DILENGKAPI VALIDASI JADWAL & KETERLAMBATAN) ---
    Route::prefix('guru')->group(function () {
        Route::get('/logbook/create', [GuruController::class, 'logbookCreate'])->name('guru.logbook.create');
        Route::post('/logbook', [GuruController::class, 'logbookStore'])->name('guru.logbook.store');
    });
});
