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
     * Buat URL wa.me langsung untuk permohonan persetujuan dispensasi ke Wakasek Kesiswaan.
     */
    public function getDispensasiWhatsAppUrl(Dispensasi $dispensasi, ?string $wakaUsername = null): ?string
    {
        $recipients = $this->wakaRecipients();
        $waka = null;

        if ($wakaUsername) {
            $waka = $recipients->first(fn (array $r): bool => strtolower($r['username'] ?? '') === strtolower($wakaUsername));
        }

        if (! $waka) {
            $waka = $recipients->first();
        }

        if (! $waka || blank($waka['number'] ?? null)) {
            return null;
        }

        $message = $this->buildDispensasiMessage($dispensasi, $waka['username'] ?? null);

        return $this->formatWhatsAppUrl((string) $waka['number'], $message);
    }

    /**
     * Susun teks template pesan WhatsApp untuk dispensasi siswa.
     */
    public function buildDispensasiMessage(Dispensasi $dispensasi, ?string $wakaUsername = null): string
    {
        $dispensasi->loadMissing(['siswa.kelas', 'siswas.kelas', 'pembuat']);
        $siswas = $dispensasi->siswas->isNotEmpty() ? $dispensasi->siswas : collect([$dispensasi->siswa])->filter();
        $namaSiswa = $siswas->pluck('nama')->join(', ');
        $kelasSiswa = $siswas->pluck('kelas.nama_kelas')->filter()->unique()->join(', ') ?: '-';
        $pembuat = $dispensasi->pembuat?->name ?? 'Guru Piket';
        $waktuStr = $dispensasi->deskripsi_waktu;

        $approvalBaseUrl = rtrim((string) config('services.whatsapp.approval_base_url'), '/');
        $approvalPath = route('waka.dispensasi.show', ['token' => $dispensasi->token_approval], false);
        $baseUrl = blank($approvalBaseUrl) ? url('/') : $approvalBaseUrl;
        $approvalUrl = rtrim($baseUrl, '/').$approvalPath;

        if ($wakaUsername) {
            $approvalUrl .= '?'.http_build_query([
                'waka' => $wakaUsername,
            ]);
        }

        return "🔔 *PERMINTAAN PERSETUJUAN DISPENSASI SISWA*\n\n"
            ."Nama Siswa: *{$namaSiswa}*\n"
            ."Kelas: *{$kelasSiswa}*\n"
            ."Jenis Dispensasi: {$dispensasi->jenis_dispensasi}\n"
            ."Waktu: {$waktuStr}\n"
            ."Alasan: {$dispensasi->alasan}\n"
            ."Diajukan Oleh: {$pembuat}\n\n"
            ."Mohon Wakasek Kesiswaan dapat memberikan persetujuan melalui tautan berikut:\n"
            ."👉 {$approvalUrl}\n\n"
            .'_Pesan otomatis dari Sistem Jurnal Sekolah_';
    }

    /**
     * Buat data link notifikasi wa.me dispensasi untuk diteruskan ke controller/view.
     *
     * @return array{configured: bool, recipients: int, delivered: int, url: ?string}
     */
    public function sendDispensasiNotificationToWaka(Dispensasi $dispensasi): array
    {
        $waUrl = $this->getDispensasiWhatsAppUrl($dispensasi);
        $waka = $this->wakaRecipients()->first();

        Log::info('WhatsAppService: Link wa.me dispensasi berhasil dibuat.', [
            'dispensasi_id' => $dispensasi->id,
            'to_user' => $waka['name'] ?? 'Wakasek Kesiswaan',
            'no_hp' => $waka['number'] ?? null,
            'wa_url' => $waUrl,
        ]);

        return [
            'configured' => filled($waUrl),
            'recipients' => $this->wakaRecipients()->count(),
            'delivered' => filled($waUrl) ? 1 : 0,
            'url' => $waUrl,
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

            $senderNumber = $this->getSenderNumber();
            $payload = [
                'target' => $normalizedTarget,
                'message' => $message,
            ];

            if ($senderNumber !== null) {
                $payload['sender'] = $senderNumber;
            }

            $response = Http::asForm()->withHeaders([
                'Authorization' => $gatewayApiKey,
            ])->post($gatewayUrl, $payload);

            if ($this->wasAcceptedByGateway($response)) {
                Log::info('WhatsAppService: Pesan WhatsApp berhasil dikirim ke '.$normalizedTarget, [
                    'sender' => $senderNumber,
                ]);

                return true;
            }

            Log::error('WhatsAppService: Gagal mengirim pesan via API ke '.$targetNumber, [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;
        } catch (\Throwable $e) {
            Log::error('WhatsAppService Error saat mengirim pesan: '.$e->getMessage(), [
                'target' => $normalizedTarget ?? $targetNumber,
            ]);

            return false;
        }
    }

    /**
     * Dapatkan nomor pengirim WhatsApp (device Fonnte) dari konfigurasi.
     * Nomor ini adalah nomor yang terdaftar sebagai device di akun Fonnte.
     */
    public function getSenderNumber(): ?string
    {
        $number = config('services.whatsapp.piket_confirmation_number')
            ?? config('services.whatsapp.admin_number');

        if (blank($number)) {
            return null;
        }

        return $this->normalizeIndonesianWhatsAppNumber((string) $number);
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
     * Ambil daftar Waka tujuan dari konfigurasi, dengan nomor terbaru diambil
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
