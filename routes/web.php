<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// ==========================================
// 1. AREA GUEST (Belum Login)
// Halaman yang bisa diakses orang sebelum masuk
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLoginForm']);
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// ==========================================
// 2. AREA AUTH (Sudah Login)
// Halaman terproteksi, wajib login dulu
// ==========================================
Route::middleware('auth')->group(function () {
    
    // Proses Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- DASHBOARD ADMIN ---
    Route::prefix('dashboard')->group(function () {
        // Semua view di bawah ini sudah diarahkan kembali ke folder dashboard.admin.*
        Route::view('/', 'dashboard.admin.admin')->name('dashboard');
        Route::view('/catatan-jurnal', 'dashboard.admin.catatan-jurnal')->name('catatan-jurnal');
        Route::view('/kelas', 'dashboard.admin.kelas')->name('dashboard.kelas');
        Route::view('/siswa', 'dashboard.admin.siswa')->name('dashboard.siswa');
        Route::view('/tambah-akun', 'dashboard.admin.tambah-akun')->name('tambah-akun');
        Route::view('/piket', 'dashboard.piket.utama')->name('dashboard.piket');
        Route::view('/manajemen-user', 'dashboard.admin.manajemen-user')->name('admin.manajemen-user');

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
            // DI SINI YANG DIUBAH: dari 'dashboard.guru' menjadi 'dashboard.admin.guru'
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
            // DI SINI YANG DIUBAH: dari 'dashboard.jadwal' menjadi 'dashboard.admin.jadwal'
            return view('dashboard.admin.jadwal', compact('mapels', 'gurus'));
        })->name('dashboard.jadwal');

        // Route Mapel (dengan data dummy FE)
        Route::get('/mapel', function () {
            $mapels = [
                ['kode' => 'MAT-301', 'nama' => 'Matematika Lanjut', 'guru' => 'Budi Santoso, S.Pd'],
                ['kode' => 'RPL-201', 'nama' => 'Pemrograman Web', 'guru' => 'Siti Aminah, M.Pd'],
                ['kode' => 'BSD-101', 'nama' => 'Basis Data', 'guru' => 'Eko Prasetyo, S.Kom'],
            ];
            // DI SINI YANG DIUBAH: dari 'dashboard.mapel' menjadi 'dashboard.admin.mapel'
            return view('dashboard.admin.mapel', compact('mapels'));
        })->name('dashboard.mapel');
    });

    // --- PENGURUS KELAS / SEKRETARIS ---
    Route::prefix('pengurus-kelas')->group(function () {
        Route::get('/jurnal', fn() => view('jurnal.index'))->name('pengurus-kelas.jurnal.index');
        Route::get('/jurnal/create', fn() => view('jurnal.create'))->name('pengurus-kelas.jurnal.create');
        Route::get('/notifikasi', fn() => view('notifikasi.index'))->name('pengurus-kelas.notifikasi');
        Route::get('/jadwal', fn() => view('jurnal.jadwal.index'))->name('pengurus-kelas.jadwal');
    });

    // --- GURU ---
    Route::prefix('guru')->group(function () {
        Route::get('/logbook/create', fn() => view('guru.logbook.create'))->name('guru.logbook.create');
    });
});