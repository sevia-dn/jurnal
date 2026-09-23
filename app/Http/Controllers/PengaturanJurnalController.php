<?php

namespace App\Http\Controllers;

use App\Models\PengaturanJurnal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengaturanJurnalController extends Controller
{
    /**
     * Menampilkan halaman pengaturan tenggat pengisian jurnal untuk Admin.
     */
    public function index()
    {
        $user = Auth::user();
        if (! $user || $user->role !== 'admin') {
            abort(403, 'Akses terbatas untuk Administrator.');
        }

        $pengaturan = PengaturanJurnal::with('pengubah')->firstOrCreate(
            ['id' => 1],
            ['kebijakan_tenggat' => 'jam_mengajar']
        );

        return view('dashboard.admin.pengaturan-jurnal', compact('pengaturan'));
    }

    /**
     * Memperbarui kebijakan tenggat pengisian jurnal.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        if (! $user || $user->role !== 'admin') {
            abort(403, 'Akses terbatas untuk Administrator.');
        }

        $request->validate([
            'kebijakan_tenggat' => 'required|in:jam_mengajar,hari_ini,longgar',
        ], [
            'kebijakan_tenggat.required' => 'Pilihan kebijakan tenggat wajib ditentukan.',
            'kebijakan_tenggat.in' => 'Pilihan kebijakan tenggat tidak valid.',
        ]);

        $pengaturan = PengaturanJurnal::getKebijakanAktif();
        $pengaturan->update([
            'kebijakan_tenggat' => $request->kebijakan_tenggat,
            'diubah_oleh' => $user->id,
        ]);

        $namaKebijakan = match ($request->kebijakan_tenggat) {
            'jam_mengajar' => 'Ketat (Hanya Saat Jam Mengajar Berlangsung)',
            'hari_ini' => 'Fleksibel Hari Ini (Bebas Sepanjang Hari H)',
            'longgar' => 'Longgar (Hari H hingga Toleransi H-1 Kemarin)',
            default => $request->kebijakan_tenggat,
        };

        return redirect()
            ->route('admin.pengaturan-jurnal.index')
            ->with('success', "Kebijakan tenggat pengisian jurnal berhasil diperbarui menjadi: {$namaKebijakan}.");
    }
}
