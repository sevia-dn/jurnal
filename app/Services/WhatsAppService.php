<?php

namespace App\Services;

use App\Models\Dispensasi;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    /**
     * Kirim pesan notifikasi dispensasi ke nomor WA milik Waka
     */
    public function sendDispensasiNotificationToWaka(Dispensasi $dispensasi, ?User $waka = null): bool
    {
        // Cari Waka jika tidak dispesifikasikan
        if (!$waka) {
            $waka = User::where('is_waka', true)->orWhere('role', 'waka')->first();
        }

        if (!$waka || !$waka->no_hp) {
            Log::warning("WhatsAppService: Tidak ditemukan Waka atau nomor HP Waka kosong.", [
                'dispensasi_id' => $dispensasi->id
            ]);
        }

        $targetPhone = $waka?->no_hp ?? '081234567890';
        $approvalUrl = route('dispensasi.approval', ['token' => $dispensasi->token_approval]);
        $namaSiswa = $dispensasi->siswa?->nama ?? $dispensasi->nama;
        $kelasSiswa = $dispensasi->siswa?->kelas?->nama_kelas ?? '-';
        $pembuat = $dispensasi->pembuat?->name ?? 'Guru Piket';
        $waktuStr = $dispensasi->deskripsi_waktu;

        $message = "🔔 *PERMINTAAN PERSETUJUAN DISPENSASI SISWA*\n\n"
            . "Nama Siswa: *{$namaSiswa}*\n"
            . "Kelas: *{$kelasSiswa}*\n"
            . "Jenis Dispensasi: {$dispensasi->jenis_dispensasi}\n"
            . "Waktu: {$waktuStr}\n"
            . "Alasan: {$dispensasi->alasan}\n"
            . "Diajukan Oleh: {$pembuat}\n\n"
            . "Mohon Waka dapat memberikan persetujuan melalui tautan berikut:\n"
            . "👉 {$approvalUrl}\n\n"
            . "_Pesan otomatis dari Sistem Jurnal Sekolah_";

        // 1. Simpan Log Simulasi WhatsApp (Untuk testing & dev)
        Log::info("=== SIMULASI WHATSAPP NOTIFICATION ===", [
            'to_user' => $waka?->name ?? 'Waka Kesiswaan',
            'no_hp' => $targetPhone,
            'approval_url' => $approvalUrl,
            'message' => $message,
        ]);

        // 2. Jika konfigurasi WA Gateway di .env diaktifkan (Fonnte/Wablas/dll), eksekusi HTTP Request
        $gatewayUrl = config('services.whatsapp.url');
        $gatewayApiKey = config('services.whatsapp.api_key');

        if ($gatewayUrl && $gatewayApiKey) {
            try {
                Http::withHeaders([
                    'Authorization' => $gatewayApiKey,
                ])->post($gatewayUrl, [
                    'target' => $targetPhone,
                    'message' => $message,
                ]);
            } catch (\Exception $e) {
                Log::error("WhatsAppService Error: " . $e->getMessage());
            }
        }

        return true;
    }
}
