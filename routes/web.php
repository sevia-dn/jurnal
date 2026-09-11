<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestSekretarisController;

Route::get('/', function () {
    return redirect()->route('sekretaris.dashboard');
});

Route::prefix('sekretaris')->group(function () {
    Route::get('/dashboard', [TestSekretarisController::class, 'dashboard'])->name('sekretaris.dashboard');
    
    // Ubah .name('sekretaris.index') menjadi .name('sekretaris.jurnal.index')
    Route::get('/jurnal', [TestSekretarisController::class, 'indexJurnal'])->name('sekretaris.jurnal.index');
    
    Route::post('/jurnal/approve/{id}', [TestSekretarisController::class, 'approveJurnal'])->name('sekretaris.approve');
    Route::get('/jurnal/history', [TestSekretarisController::class, 'historyJurnal'])->name('sekretaris.history');
});