<?php

namespace App\Services;

use App\Models\Dispensasi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Kirim pesan notifikasi dispensasi ke nomor WA milik Waka
     */
    public function sendDispensasiNotificationToWaka(Dispensasi $dispensasi): int
    {
        $tanggalPenugasan = Carbon::parse($dispensasi->tanggal)->toDateString();
        $wakasTerjadwal = User::wakaKesiswaan()
            ->whereHas('jadwalPikets', function ($query) use ($tanggalPenugasan): void {
                $query->whereDate('tanggal', $tanggalPenugasan)->where('tipe', 'waka');
            })
            ->get();
        $wakas = $wakasTerjadwal->filter(fn (User $waka): bool => filled($waka->no_hp));
        $approvalUrl = route('dispensasi.approval', ['token' => $dispensasi->token_approval]);
        $namaSiswa = $dispensasi->siswa?->nama ?? $dispensasi->nama;
        $kelasSiswa = $dispensasi->siswa?->kelas?->nama_kelas ?? '-';
        $pembuat = $dispensasi->pembuat?->name ?? 'Guru Piket';
        $waktuStr = $dispensasi->deskripsi_waktu;

        $message = "🔔 *PERMINTAAN PERSETUJUAN DISPENSASI SISWA*\n\n"
            ."Nama Siswa: *{$namaSiswa}*\n"
            ."Kelas: *{$kelasSiswa}*\n"
            ."Jenis Dispensasi: {$dispensasi->jenis_dispensasi}\n"
            ."Waktu: {$waktuStr}\n"
            ."Alasan: {$dispensasi->alasan}\n"
            ."Diajukan Oleh: {$pembuat}\n\n"
            ."Mohon Waka dapat memberikan persetujuan melalui tautan berikut:\n"
            ."👉 {$approvalUrl}\n\n"
            .'_Pesan otomatis dari Sistem Jurnal Sekolah_';

        if ($wakasTerjadwal->isEmpty()) {
            Log::warning('WhatsAppService: Tidak ada Wakasek Kesiswaan terjadwal untuk tanggal dispensasi.', [
                'dispensasi_id' => $dispensasi->id,
                'tanggal' => $tanggalPenugasan,
                'approval_url' => $approvalUrl,
            ]);

            return 0;
        }

        if ($wakas->isEmpty()) {
            Log::warning('WhatsAppService: Wakasek Kesiswaan terjadwal belum memiliki nomor WhatsApp.', [
                'dispensasi_id' => $dispensasi->id,
                'tanggal' => $tanggalPenugasan,
                'waka' => $wakasTerjadwal->pluck('name')->all(),
                'approval_url' => $approvalUrl,
            ]);

            return 0;
        }

        // 2. Jika konfigurasi WA Gateway di .env diaktifkan (Fonnte/Wablas/dll), eksekusi HTTP Request
        $gatewayUrl = config('services.whatsapp.url');
        $gatewayApiKey = config('services.whatsapp.api_key');

        foreach ($wakas as $waka) {
            Log::info('=== NOTIFIKASI WHATSAPP DISPENSASI ===', [
                'to_user' => $waka->name,
                'no_hp' => $waka->no_hp,
                'approval_url' => $approvalUrl,
                'message' => $message,
            ]);

            if ($gatewayUrl && $gatewayApiKey) {
                try {
                    Http::withHeaders([
                        'Authorization' => $gatewayApiKey,
                    ])->post($gatewayUrl, [
                        'target' => $waka->no_hp,
                        'message' => $message,
                    ]);
                } catch (\Exception $e) {
                    Log::error('WhatsAppService Error: '.$e->getMessage());
                }
            }
        }

        return $wakas->count();
    }
}
