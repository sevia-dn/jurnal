<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// --- AUTH ---
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLoginForm']);
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// --- HALAMAN TERPROTEKSI (harus login) ---
Route::middleware('auth')->group(function () {

    // Admin
    Route::view('/dashboard', 'dashboard.admin.admin')->name('dashboard');
    Route::view('/dashboard/catatan-jurnal', 'dashboard.catatan-jurnal')->name('catatan-jurnal');

    // Guru
    Route::get('/guru/logbook', function () {
        $mapels = [
            ['kode' => 'MTK', 'nama' => 'Matematika'],
            ['kode' => 'RPL', 'nama' => 'Pemrograman Web'],
        ];
        $users = [
            ['nip' => '198005122005011002', 'nama' => 'Budi Santoso, S.Pd', 'mapel' => 'Matematika', 'no_hp' => '081234567890'],
            ['nip' => '198507232010012004', 'nama' => 'Siti Aminah, M.Pd', 'mapel' => 'Pemrograman Web', 'no_hp' => '082345678901'],
        ];
        return view('guru/logbook', compact('mapels', 'users'));
    })->name('guru/logbook');

    // Kelas & Jadwal
    Route::view('/dashboard/kelas', 'dashboard.kelas')->name('dashboard.kelas');

    Route::get('/dashboard/jadwal', function () {
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
        return view('dashboard.jadwal', compact('mapels', 'gurus'));
    })->name('dashboard.jadwal');

    // Pengurus Kelas (dulu "Sekretaris")
    Route::prefix('pengurus-kelas')->group(function () {
        Route::get('/jurnal', fn() => view('jurnal.index'))->name('pengurus-kelas.jurnal.index');
        Route::get('/jurnal/create', fn() => view('jurnal.create'))->name('pengurus-kelas.jurnal.create');
        Route::get('/notifikasi', fn() => view('notifikasi.index'))->name('pengurus-kelas.notifikasi');
        Route::get('/jadwal', fn() => view('jurnal.jadwal.index'))->name('pengurus-kelas.jadwal');
    });

    // Guru - Logbook
    Route::prefix('guru')->group(function () {
        Route::get('/logbook/create', fn() => view('guru.logbook.create'))->name('guru.logbook.create');
    });
    // Route mapel
    Route::get('/dashboard/mapel', function () {
    $mapels = [
        ['kode' => 'MAT-301', 'nama' => 'Matematika Lanjut', 'guru' => 'Budi Santoso, S.Pd'],
        ['kode' => 'RPL-201', 'nama' => 'Pemrograman Web', 'guru' => 'Siti Aminah, M.Pd'],
        ['kode' => 'BSD-101', 'nama' => 'Basis Data', 'guru' => 'Eko Prasetyo, S.Kom'],
    ];

    return view('dashboard.mapel', compact('mapels'));
    })->name('dashboard.mapel');
});