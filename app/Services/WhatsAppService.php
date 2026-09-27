<?php

namespace App\Services;

use App\Models\Dispensasi;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Kirim pesan notifikasi dispensasi ke nomor WA milik Waka
     */
    /**
     * @return array{configured: bool, recipients: int, delivered: int}
     */
    public function sendDispensasiNotificationToWaka(Dispensasi $dispensasi): array
    {
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

        $gatewayUrl = config('services.whatsapp.url');
        $gatewayApiKey = config('services.whatsapp.api_key');
        $wakaRecipients = collect(config('services.whatsapp.waka_recipients', []))
            ->filter(fn (array $recipient): bool => filled($recipient['number'] ?? null))
            ->values();
        $piketConfirmationNumber = config('services.whatsapp.piket_confirmation_number');

        $recipients = $wakaRecipients->map(function (array $recipient) use ($message): array {
            return [
                ...$recipient,
                'message' => $message,
            ];
        });
        if (filled($piketConfirmationNumber)) {
            $recipients->prepend([
                'name' => 'Guru Piket (konfirmasi pengajuan)',
                'number' => $piketConfirmationNumber,
                'message' => "Pengajuan dispensasi untuk {$namaSiswa} telah diteruskan kepada Wakasek Kesiswaan. "
                    .'Status saat ini: menunggu validasi Wakasek.',
            ]);
        }

        if (blank($gatewayUrl) || blank($gatewayApiKey)) {
            Log::warning('WhatsAppService: Pengajuan dispensasi tersimpan, tetapi gateway WhatsApp belum dikonfigurasi.', [
                'dispensasi_id' => $dispensasi->id,
                'approval_url' => $approvalUrl,
                'recipients' => $recipients->pluck('number')->all(),
            ]);

            return [
                'configured' => false,
                'recipients' => $recipients->count(),
                'delivered' => 0,
            ];
        }

        $delivered = 0;
        foreach ($recipients as $recipient) {
            Log::info('=== NOTIFIKASI WHATSAPP DISPENSASI ===', [
                'to_user' => $recipient['name'],
                'no_hp' => $recipient['number'],
                'approval_url' => $approvalUrl,
                'message' => $recipient['message'],
            ]);

            try {
                $response = Http::withHeaders([
                    'Authorization' => $gatewayApiKey,
                ])->post($gatewayUrl, [
                    'target' => $recipient['number'],
                    'message' => $recipient['message'],
                ]);

                if ($response->successful()) {
                    $delivered++;
                } else {
                    Log::error('WhatsAppService: Gateway menolak notifikasi dispensasi.', [
                        'dispensasi_id' => $dispensasi->id,
                        'number' => $recipient['number'],
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);
                }
            } catch (\Throwable $exception) {
                Log::error('WhatsAppService Error: '.$exception->getMessage(), [
                    'dispensasi_id' => $dispensasi->id,
                    'number' => $recipient['number'],
                ]);
            }
        }

        return [
            'configured' => true,
            'recipients' => $recipients->count(),
            'delivered' => $delivered,
        ];
    }
}
