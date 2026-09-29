<?php

namespace App\Http\Controllers;

use App\Models\JurnalMengajar;
use App\Models\Pengaturan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicJournalController extends Controller
{
    public function index(Request $request): View
    {
        return $this->show($request, $request->query('tab', 'hari-ini'));
    }

    public function history(Request $request): View
    {
        return $this->show($request, 'keseluruhan');
    }

    private function show(Request $request, string $tab): View
    {
        $filters = $request->validate([
            'tanggal_mulai' => ['nullable', 'date_format:Y-m-d'],
            'tanggal_selesai' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:tanggal_mulai'],
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $isHistory = $tab === 'keseluruhan';
        $publicEnabled = (bool) Pengaturan::getValue('publik_riwayat_aktif', 1);
        $eventDate = (string) Pengaturan::getValue('event_sekolah_tanggal', '');
        $event = [
            'name' => (string) Pengaturan::getValue('event_sekolah', ''),
            'date' => $eventDate,
            'dismissal_time' => (string) Pengaturan::getValue('event_sekolah_jam_pulang', ''),
        ];
        $showEvent = ! $isHistory
            && $event['name'] !== ''
            && $eventDate === now('Asia/Jakarta')->toDateString()
            && $event['dismissal_time'] !== '';

        $todayString = now('Asia/Jakarta')->toDateString();
        $jamKosongMulai = (string) Pengaturan::getValue('jam_kosong_tanggal_mulai', '');
        $jamKosongSelesai = (string) Pengaturan::getValue('jam_kosong_tanggal_selesai', '');
        $jamKosong = [
            'nama' => (string) Pengaturan::getValue('jam_kosong_nama', ''),
            'tanggal_mulai' => $jamKosongMulai,
            'tanggal_selesai' => $jamKosongSelesai,
        ];
        $showJamKosong = ! $isHistory
            && $jamKosong['nama'] !== ''
            && $jamKosongMulai !== ''
            && $jamKosongSelesai !== ''
            && $todayString >= $jamKosongMulai
            && $todayString <= $jamKosongSelesai;

        $journals = collect();
        if ($publicEnabled) {
            $query = JurnalMengajar::query()
                ->with(['user:id,name', 'kelas:id_kelas,nama_kelas', 'mapel:id,nama_mapel'])
                ->where('status_validasi', 'disetujui')
                ->when(! $isHistory, fn ($query) => $query->whereDate('tanggal', now('Asia/Jakarta')->toDateString()))
                ->when($isHistory && ! empty($filters['tanggal_mulai']), fn ($query) => $query->whereDate('tanggal', '>=', $filters['tanggal_mulai']))
                ->when($isHistory && ! empty($filters['tanggal_selesai']), fn ($query) => $query->whereDate('tanggal', '<=', $filters['tanggal_selesai']))
                ->when($isHistory && filled($filters['search'] ?? null), function ($query) use ($filters): void {
                    $search = trim($filters['search']);

                    $query->where(function ($journalQuery) use ($search): void {
                        $journalQuery
                            ->whereHas('kelas', fn ($kelasQuery) => $kelasQuery->where('nama_kelas', 'like', "%{$search}%"))
                            ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$search}%"));
                    });
                })
                ->orderByDesc('tanggal')
                ->orderByDesc('jam_ke');

            $journals = $isHistory
                ? $query->paginate(12)->withQueryString()
                : $query->limit(5)->get();
        }

        return view('public.jurnal', compact('publicEnabled', 'journals', 'event', 'showEvent', 'isHistory', 'jamKosong', 'showJamKosong'));
    }
}
