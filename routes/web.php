<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
<<<<<<< HEAD
use App\Http\Controllers\PiketController;
=======
use App\Http\Controllers\GuruController;
>>>>>>> dev

// ==========================================
// 1. AREA GUEST (Belum Login)
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLoginForm']);
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// ==========================================
// 2. AREA AUTH (Sudah Login)
// ==========================================
Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // --- DASHBOARD ADMIN ---
    Route::prefix('dashboard')->group(function () {
        Route::view('/', 'dashboard.admin.admin')->name('dashboard');
        Route::view('/catatan-jurnal', 'dashboard.admin.catatan-jurnal')->name('catatan-jurnal');
        Route::view('/kelas', 'dashboard.admin.kelas')->name('dashboard.kelas');
        Route::view('/siswa', 'dashboard.admin.siswa')->name('dashboard.siswa');
        Route::view('/tambah-akun', 'dashboard.admin.tambah-akun')->name('tambah-akun');
        Route::view('/piket', 'dashboard.piket.utama')->name('dashboard.piket');
        Route::view('/manajemen-user', 'dashboard.admin.manajemen-user')->name('admin.manajemen-user');

<<<<<<< HEAD
=======
        Route::view('/piket/kehadiran', 'dashboard.piket.kehadiran')->name('piket.kehadiran');
        Route::view('/piket/dispensasi', 'dashboard.piket.dispensasi')->name('piket.dispensasi');

>>>>>>> dev
        Route::get('/piket/kehadiran-siswa', function () {
            return view('dashboard.piket.kehadiran-siswa');
        })->name('piket.kehadiran-siswa');

        // --- Guru Piket: Kehadiran & Dispensasi (pakai Controller + data asli) ---
        // NOTE: sebelumnya ada versi Route::view() statis untuk 2 halaman ini
        // yang dobel nama route dan bikin error (variabel $kehadirans/$siswas
        // tidak pernah dikirim). Sudah dihapus, disatukan di sini saja.
        Route::get('/piket/kehadiran', [PiketController::class, 'kehadiran'])->name('piket.kehadiran');
        Route::post('/piket/kehadiran/{kehadiran}/verifikasi', [PiketController::class, 'verifikasiKehadiran'])->name('piket.kehadiran.verifikasi');

        Route::get('/piket/dispensasi', [PiketController::class, 'dispensasiForm'])->name('piket.dispensasi.form');
        Route::post('/piket/dispensasi', [PiketController::class, 'dispensasiStore'])->name('piket.dispensasi.store');

        // Route Guru (dengan data dummy FE)

        Route::get('/guru', function () {
            $mapels = [
                ['kode' => 'MTK', 'nama' => 'Matematika'],
                ['kode' => 'RPL', 'nama' => 'Pemrograman Web'],
            ];
            $users = [
                ['nip' => '198005122005011002', 'nama' => 'Budi Santoso, S.Pd', 'mapel' => 'Matematika', 'no_hp' => '081234567890'],
                ['nip' => '198507232010012004', 'nama' => 'Siti Aminah, M.Pd', 'mapel' => 'Pemrograman Web', 'no_hp' => '082345678901'],
            ];
            return view('dashboard.admin.guru', compact('mapels', 'users'));
        })->name('dashboard.guru');

        // Route Jadwal (dengan data dummy FE)
        Route::get('/jadwal', function () {
            $mapels = [
                ['kode' => 'MAT-301', 'nama' => 'Matematika Lanjut', 'guru' => 'Budi Santoso, S.Pd'],
                ['kode' => 'RPL-201', 'nama' => 'Pemrograman Web', 'guru' => 'Siti Aminah, M.Pd'],
                ['kode' => 'BSD-101', 'nama' => 'Basis Data', 'guru' => 'Eko Prasetyo, S.Kom'],
            ];
            $gurus = [
                ['id' => 1, 'nama' => 'Budi Santoso, S.Pd'],
                ['id' => 2, 'nama' => 'Siti Aminah, M.Pd'],
                ['id' => 3, 'nama' => 'Eko Prasetyo, S.Kom'],
            ];
            return view('dashboard.admin.jadwal', compact('mapels', 'gurus'));
        })->name('dashboard.jadwal');

        // Route Mapel (dengan data dummy FE)
        Route::get('/mapel', function () {
            $mapels = [
                ['kode' => 'MAT-301', 'nama' => 'Matematika Lanjut', 'guru' => 'Budi Santoso, S.Pd'],
                ['kode' => 'RPL-201', 'nama' => 'Pemrograman Web', 'guru' => 'Siti Aminah, M.Pd'],
                ['kode' => 'BSD-101', 'nama' => 'Basis Data', 'guru' => 'Eko Prasetyo, S.Kom'],
            ];
            return view('dashboard.admin.mapel', compact('mapels'));
        })->name('dashboard.mapel');
    });

// --- PENGURUS KELAS / SEKRETARIS ---
    Route::prefix('pengurus-kelas')->group(function () {
    Route::view('/dashboard', 'dashboard.pengurus-kelas.utama')->name('pengurus-kelas.dashboard');
    Route::view('/jadwal', 'dashboard.pengurus-kelas.jadwal')->name('pengurus-kelas.jadwal');
    Route::view('/jurnal-detail', 'dashboard.pengurus-kelas.jurnal-detail')->name('pengurus-kelas.jurnal-detail');
    Route::view('/kehadiran-guru', 'dashboard.pengurus-kelas.kehadiran-guru')->name('pengurus-kelas.kehadiran-guru');
    Route::view('/kehadiran-siswa', 'dashboard.pengurus-kelas.kehadiran-siswa')->name('pengurus-kelas.kehadiran-siswa');
});

   Route::get('/guru-pengajar', [GuruController::class, 'index'])->name('guru');    Route::view('/guru-pengajar/beranda', 'dashboard.guru-pengajar.utama')->name('guru.utama');
    Route::view('/guru-pengajar/riwayat', 'dashboard.guru-pengajar.riwayat')->name('guru.riwayat');});
