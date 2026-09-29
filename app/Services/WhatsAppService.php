<?php

namespace App\Services;

use App\Models\Dispensasi;
use App\Models\User;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
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
        $wakaRecipients = $this->wakaRecipients();

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
        })->map(function (array $recipient): array {
            $recipient['target'] = $this->normalizeIndonesianWhatsAppNumber((string) $recipient['number']);

            return $recipient;
        })->filter(fn (array $recipient): bool => $recipient['target'] !== null)->values();

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
                $response = Http::asForm()->withHeaders([
                    'Authorization' => $gatewayApiKey,
                ])->post($gatewayUrl, [
                    'target' => $recipient['target'],
                    'message' => $recipient['message'],
                ]);

                if ($this->wasAcceptedByGateway($response)) {
                    $delivered++;
                } else {
                    Log::error('WhatsAppService: Gateway menolak notifikasi dispensasi.', [
                        'dispensasi_id' => $dispensasi->id,
                        'number' => $recipient['target'],
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);
                }
            } catch (\Throwable $exception) {
                Log::error('WhatsAppService Error: '.$exception->getMessage(), [
                    'dispensasi_id' => $dispensasi->id,
                    'number' => $recipient['target'],
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
            $normalizedTarget = $this->normalizeIndonesianWhatsAppNumber($targetNumber);
            if ($normalizedTarget === null) {
                Log::warning('WhatsAppService: Nomor tujuan WhatsApp tidak valid.', [
                    'target' => $targetNumber,
                ]);

                return false;
            }

            $response = Http::asForm()->withHeaders([
                'Authorization' => $gatewayApiKey,
            ])->post($gatewayUrl, [
                'target' => $normalizedTarget,
                'message' => $message,
            ]);

            if ($this->wasAcceptedByGateway($response)) {
                Log::info('WhatsAppService: Pesan WhatsApp berhasil dikirim ke '.$normalizedTarget);

                return true;
            }

            Log::error('WhatsAppService: Gagal mengirim pesan via API ke '.$targetNumber, [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;
        } catch (\Throwable $e) {
            Log::error('WhatsAppService Error saat mengirim pesan: '.$e->getMessage(), [
                'target' => $normalizedTarget,
            ]);

            return false;
        }
    }

    private function normalizeIndonesianWhatsAppNumber(string $number): ?string
    {
        $normalized = preg_replace('/\D+/', '', $number) ?? '';

        if (str_starts_with($normalized, '00')) {
            $normalized = substr($normalized, 2);
        }

        if (str_starts_with($normalized, '0')) {
            $normalized = '62'.substr($normalized, 1);
        } elseif (! str_starts_with($normalized, '62')) {
            $normalized = '62'.$normalized;
        }

        return preg_match('/^62\d{8,13}$/', $normalized) === 1 ? $normalized : null;
    }

    private function wasAcceptedByGateway(Response $response): bool
    {
        if (! $response->successful()) {
            return false;
        }

        $payload = $response->json();
        $status = $payload['status'] ?? $payload['Status'] ?? null;

        return $status !== null && filter_var($status, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Ambil empat Waka tujuan dari konfigurasi, dengan nomor terbaru diambil
     * dari data pengguna apabila tersedia.
     *
     * @return Collection<int, array{username: string, name: string, number: string}>
     */
    private function wakaRecipients(): Collection
    {
        $configuredRecipients = collect(config('services.whatsapp.waka_recipients', []))
            ->filter(fn (array $recipient): bool => filled($recipient['username'] ?? null))
            ->values();

        $phoneNumbersByUsername = User::query()
            ->whereIn('username', $configuredRecipients->pluck('username'))
            ->pluck('no_hp', 'username')
            ->mapWithKeys(fn (?string $number, string $username): array => [mb_strtolower($username) => $number]);

        return $configuredRecipients
            ->map(function (array $recipient) use ($phoneNumbersByUsername): array {
                $username = (string) $recipient['username'];
                $databaseNumber = $phoneNumbersByUsername->get(mb_strtolower($username));

                return [
                    'username' => $username,
                    'name' => (string) ($recipient['name'] ?? $username),
                    'number' => filled($databaseNumber) ? $databaseNumber : (string) ($recipient['number'] ?? ''),
                ];
            })
            ->filter(fn (array $recipient): bool => $this->normalizeIndonesianWhatsAppNumber($recipient['number']) !== null)
            ->values();
    }
}
