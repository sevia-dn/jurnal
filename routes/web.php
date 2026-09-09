<?php

use Illuminate\Support\Facades\Route;

// --- AUTH & DASHBOARD ADMIN ---
Route::view('/', 'auth.login');
Route::view('/login', 'auth.login')->name('login');

// Route halaman Admin milikmu
Route::view('/dashboard', 'dashboard.admin.admin')->name('dashboard');
Route::view('/dashboard/catatan-jurnal', 'dashboard.admin.catatan-jurnal')->name('catatan-jurnal');

// Route halaman Guru milik temanmu
Route::get('/dashboard/guru', function () {
    // Memberikan data dummy agar FE temanmu tidak error
    $mapels = [
        ['kode' => 'MTK', 'nama' => 'Matematika'],
        ['kode' => 'RPL', 'nama' => 'Pemrograman Web'],
    ];
    $users = [
        ['nip' => '198005122005011002', 'nama' => 'Budi Santoso, S.Pd', 'mapel' => 'Matematika', 'no_hp' => '081234567890'],
        ['nip' => '198507232010012004', 'nama' => 'Siti Aminah, M.Pd', 'mapel' => 'Pemrograman Web', 'no_hp' => '082345678901'],
    ];
    
    // Memanggil file dari dalam folder dashboard/admin
    return view('dashboard.admin.guru', compact('mapels', 'users'));
})->name('dashboard.guru');

// Route Kelas
Route::view('/dashboard/kelas', 'dashboard.admin.kelas')->name('dashboard.kelas');

// TAMBAHAN BARU: Route Jadwal/Mapel untuk file gabungan
Route::get('/dashboard/mapel', function () {
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

    // Memanggil file dari dalam folder dashboard/admin
    return view('dashboard.admin.mapel', compact('mapels', 'gurus'));
})->name('dashboard.mapel');

// Route Jadwal
Route::view('/dashboard/admin/jadwal', 'dashboard.admin.jadwal')->name('dashboard.jadwal');


// --- ROUTE SEKRETARIS (LIA) ---
Route::prefix('sekretaris')->group(function () {
    Route::get('/jurnal', function () {
        return view('jurnal.index');
    })->name('sekretaris.jurnal.index');

    Route::get('/jurnal/create', function () {
        return view('jurnal.create');
    })->name('sekretaris.jurnal.create');

    Route::get('/notifikasi', function () {
        return view('notifikasi.index');
    })->name('sekretaris.notifikasi');

    Route::get('/jadwal', function () {
        return view('jurnal.jadwal.index');
    })->name('sekretaris.jadwal');
});

// --- ROUTE GURU ---
Route::prefix('guru')->group(function () {
    Route::get('/logbook/create', function () {
        return view('guru.logbook.create');
    })->name('guru.logbook.create');
});
