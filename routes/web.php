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

// --- HALAMAN TERPROTEKSI ---
Route::middleware('auth')->group(function () {

    Route::view('/dashboard', 'dashboard.admin')->name('dashboard');
    Route::view('/dashboard/catatan-jurnal', 'dashboard.catatan-jurnal')->name('catatan-jurnal');

    Route::get('/dashboard/guru', function () {
        $mapels = [
            ['kode' => 'MTK', 'nama' => 'Matematika'],
            ['kode' => 'RPL', 'nama' => 'Pemrograman Web'],
        ];
        $users = [
            ['nip' => '198005122005011002', 'nama' => 'Budi Santoso, S.Pd', 'mapel' => 'Matematika', 'no_hp' => '081234567890'],
            ['nip' => '198507232010012004', 'nama' => 'Siti Aminah, M.Pd', 'mapel' => 'Pemrograman Web', 'no_hp' => '082345678901'],
        ];
        return view('dashboard.guru', compact('mapels', 'users'));
    })->name('dashboard.guru');

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

    Route::prefix('sekretaris')->group(function () {
        Route::get('/jurnal', fn() => view('jurnal.index'))->name('sekretaris.jurnal.index');
        Route::get('/jurnal/create', fn() => view('jurnal.create'))->name('sekretaris.jurnal.create');
        Route::get('/notifikasi', fn() => view('notifikasi.index'))->name('sekretaris.notifikasi');
        Route::get('/jadwal', fn() => view('jurnal.jadwal.index'))->name('sekretaris.jadwal');
    });

    Route::prefix('guru')->group(function () {
        Route::get('/logbook/create', fn() => view('guru.logbook.create'))->name('guru.logbook.create');
    });
});