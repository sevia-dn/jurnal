<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestSekretarisController;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\LogbookController;
use App\Http\Controllers\JadwalMengajarController;


// ==========================================
// 1. AREA GUEST
// ==========================================

Route::middleware('guest')->group(function () {

    Route::get('/', [AuthController::class, 'showLoginForm']);

    Route::get('/login', [AuthController::class, 'showLoginForm'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login']);

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


        Route::view('/piket/kehadiran', 'dashboard.piket.kehadiran')
            ->name('piket.kehadiran');


        Route::view('/piket/dispensasi', 'dashboard.piket.dispensasi')
            ->name('piket.dispensasi');


        Route::get('/piket/kehadiran-siswa', function () {

            return view('dashboard.piket.kehadiran-siswa');

        })->name('piket.kehadiran-siswa');


        // ==========================================
        // DATA GURU
        // ==========================================

        Route::get('/guru', function () {

            $mapels = [
                [
                    'kode' => 'MTK',
                    'nama' => 'Matematika'
                ],
                [
                    'kode' => 'RPL',
                    'nama' => 'Pemrograman Web'
                ],
            ];


            $users = [
                [
                    'nip' => '198005122005011002',
                    'nama' => 'Budi Santoso, S.Pd',
                    'mapel' => 'Matematika',
                    'no_hp' => '081234567890'
                ],
                [
                    'nip' => '198507232010012004',
                    'nama' => 'Siti Aminah, M.Pd',
                    'mapel' => 'Pemrograman Web',
                    'no_hp' => '082345678901'
                ],
            ];


            return view(
                'dashboard.admin.guru',
                compact('mapels', 'users')
            );

        })->name('dashboard.guru');


        // ==========================================
        // JADWAL MENGAJAR
        // ==========================================

        Route::get(
            '/jadwal',
            [JadwalMengajarController::class, 'index']
        )->name('dashboard.jadwal');


        // FORM TAMBAH JADWAL

        Route::get(
            '/jadwal/create',
            [JadwalMengajarController::class, 'create']
        )->name('jadwal.create');


        // SIMPAN JADWAL

        Route::post(
            '/jadwal',
            [JadwalMengajarController::class, 'store']
        )->name('jadwal.store');


        // ==========================================
        // DATA MAPEL
        // ==========================================

        Route::get('/mapel', function () {

            $mapels = [
                [
                    'kode' => 'MAT-301',
                    'nama' => 'Matematika Lanjut',
                    'guru' => 'Budi Santoso, S.Pd'
                ],
                [
                    'kode' => 'RPL-201',
                    'nama' => 'Pemrograman Web',
                    'guru' => 'Siti Aminah, M.Pd'
                ],
                [
                    'kode' => 'BSD-101',
                    'nama' => 'Basis Data',
                    'guru' => 'Eko Prasetyo, S.Kom'
                ],
            ];


            return view(
                'dashboard.admin.mapel',
                compact('mapels')
            );

        })->name('dashboard.mapel');

    });


    // ==========================================
    // PENGURUS KELAS
    // ==========================================

    Route::prefix('pengurus-kelas')->group(function () {

        Route::view(
            '/dashboard',
            'dashboard.pengurus-kelas.utama'
        )->name('pengurus-kelas.dashboard');


        Route::view(
            '/jadwal',
            'dashboard.pengurus-kelas.jadwal'
        )->name('pengurus-kelas.jadwal');


        Route::view(
            '/jurnal-detail',
            'dashboard.pengurus-kelas.jurnal-detail'
        )->name('pengurus-kelas.jurnal-detail');


        Route::view(
            '/kehadiran-guru',
            'dashboard.pengurus-kelas.kehadiran-guru'
        )->name('pengurus-kelas.kehadiran-guru');


        Route::view(
            '/kehadiran-siswa',
            'dashboard.pengurus-kelas.kehadiran-siswa'
        )->name('pengurus-kelas.kehadiran-siswa');

    });


    // ==========================================
    // GURU PENGAJAR
    // ==========================================

    Route::get(
        '/guru-pengajar/beranda',
        [GuruController::class, 'beranda']
    )->name('guru.utama');


    Route::post(
        '/guru-pengajar/absen',
        [GuruController::class, 'storeAbsen']
    )->name('guru.absen.store');


    Route::post(
        '/guru-pengajar/jurnal',
        [LogbookController::class, 'store']
    )->name('guru.jurnal.store');


    Route::get(
        '/guru-pengajar/riwayat',
        [LogbookController::class, 'history']
    )->name('guru.riwayat');

});