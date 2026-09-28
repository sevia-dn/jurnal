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
        $approvalPath = route('waka.dispensasi.show', ['token' => $dispensasi->token_approval], false);
        $approvalBaseUrl = rtrim((string) config('services.whatsapp.approval_base_url'), '/');
        $namaSiswa = $dispensasi->siswa?->nama ?? $dispensasi->nama;
        $kelasSiswa = $dispensasi->siswa?->kelas?->nama_kelas ?? '-';
        $pembuat = $dispensasi->pembuat?->name ?? 'Guru Piket';
        $waktuStr = $dispensasi->deskripsi_waktu;

        $gatewayUrl = config('services.whatsapp.url');
        $gatewayApiKey = config('services.whatsapp.api_key');
        $wakaRecipients = collect(config('services.whatsapp.waka_recipients', []))
            ->filter(fn (array $recipient): bool => filled($recipient['number'] ?? null))
            ->values();
        $piketConfirmationNumber = config('services.whatsapp.piket_confirmation_number');

        $recipients = $wakaRecipients->map(function (array $recipient) use ($approvalBaseUrl, $approvalPath, $namaSiswa, $kelasSiswa, $waktuStr, $pembuat, $dispensasi): array {
            $approvalUrl = $approvalBaseUrl.$approvalPath.'?'.http_build_query([
                'waka' => $recipient['username'] ?? '',
            ]);
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

            return [
                ...$recipient,
                'approval_url' => $approvalUrl,
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
                'approval_urls' => $recipients->pluck('approval_url')->filter()->all(),
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
                'approval_url' => $recipient['approval_url'] ?? null,
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

    /**
     * Dapatkan nomor WhatsApp Admin (nomor yang sama dengan konfirmasi dispensasi).
     */
    public function getAdminNumber(): string
    {
        return (string) config('services.whatsapp.admin_number', config('services.whatsapp.piket_confirmation_number', '083838606396'));
    }

    /**
     * Format tautan wa.me dengan nomor dan pesan teks otomatis.
     */
    public function formatWhatsAppUrl(string $number, string $message): string
    {
        $cleanNumber = preg_replace('/[^0-9]/', '', $number);
        if (str_starts_with($cleanNumber, '0')) {
            $cleanNumber = '62'.substr($cleanNumber, 1);
        } elseif (! str_starts_with($cleanNumber, '62') && ! empty($cleanNumber)) {
            $cleanNumber = '62'.$cleanNumber;
        }

        return 'https://wa.me/'.$cleanNumber.'?text='.urlencode($message);
    }

    /**
     * Template pesan permohonan reset password dari pengguna ke WhatsApp Admin.
     */
    public function buildPasswordResetRequestMessage(string $nama, string $username, string $role = 'Pengguna', ?string $noHp = null, ?string $alasan = null): string
    {
        $noHpText = $noHp ?: '-';
        $alasanText = $alasan ?: 'Lupa kata sandi lama, meminta bantuan reset password.';

        return "🔐 *PERMOHONAN RESET PASSWORD - JURNALKITA*\n\n"
            ."Halo Admin, saya mengalami kendala lupa password dan ingin meminta pengaturan ulang kata sandi akun saya:\n\n"
            ."• *Nama Lengkap:* {$nama}\n"
            ."• *NIP / Username:* {$username}\n"
            ."• *Peran / Role:* {$role}\n"
            ."• *Nomor Kontak:* {$noHpText}\n"
            ."• *Alasan Kendala:* {$alasanText}\n\n"
            ."Mohon bantuannya untuk mereset kata sandi akun saya. Terima kasih!\n\n"
            .'_Dikirim melalui Sistem JurnalKita_';
    }

    /**
     * Template pesan konfirmasi password baru dari Admin ke Pengguna.
     */
    public function buildPasswordResetApprovedMessage(string $nama, string $username, string $passwordBaru): string
    {
        $loginUrl = url('/login');

        return "✅ *INFORMASI RESET PASSWORD - JURNALKITA*\n\n"
            ."Halo *{$nama}*,\n"
            ."Permohonan reset kata sandi akun JurnalKita Anda telah diproses dan disetujui oleh Administrator.\n\n"
            ."Berikut kredensial login baru Anda:\n"
            ."• *Username / NIP:* *{$username}*\n"
            ."• *Password Baru:* `{$passwordBaru}`\n\n"
            ."Silakan login kembali melalui tautan:\n"
            ."👉 {$loginUrl}\n\n"
            ."⚠️ *Pemberitahuan Keamanan:*\n"
            ."Demi keamanan akun Anda, silakan segera perbarui kata sandi Anda setelah berhasil masuk.\n\n"
            ."Terima kasih.\n"
            .'_Administrator JurnalKita_';
    }

    /**
     * Kirim pesan teks generik via WhatsApp Gateway (Fonnte) jika terkonfigurasi.
     */
    public function sendMessage(string $targetNumber, string $message): bool
    {
        $gatewayUrl = config('services.whatsapp.url');
        $gatewayApiKey = config('services.whatsapp.api_key');

        if (blank($gatewayUrl) || blank($gatewayApiKey) || blank($targetNumber)) {
            Log::info('WhatsAppService: Gateway belum dikonfigurasi atau target kosong. Pesan tidak terkirim via API.', [
                'target' => $targetNumber,
                'message' => $message,
            ]);

            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $gatewayApiKey,
            ])->post($gatewayUrl, [
                'target' => $targetNumber,
                'message' => $message,
            ]);

            if ($response->successful()) {
                Log::info('WhatsAppService: Pesan WhatsApp berhasil dikirim ke '.$targetNumber);

                return true;
            }

            Log::error('WhatsAppService: Gagal mengirim pesan via API ke '.$targetNumber, [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;
        } catch (\Throwable $e) {
            Log::error('WhatsAppService Error saat mengirim pesan: '.$e->getMessage(), [
                'target' => $targetNumber,
            ]);

            return false;
        }
    }
}
