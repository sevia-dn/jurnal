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
        return $this->show($request, false);
    }

    public function history(Request $request): View
    {
        return $this->show($request, true);
    }

    private function show(Request $request, bool $isHistory): View
    {
        $request->validate([
            'tanggal' => 'nullable|date_format:Y-m-d',
        ]);

        $publicEnabled = (bool) Pengaturan::getValue(
            $isHistory ? 'publik_riwayat_aktif' : 'publik_jurnal_aktif',
            1
        );
        $historyEnabled = (bool) Pengaturan::getValue('publik_riwayat_aktif', 1);
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

        $journals = collect();
        if ($publicEnabled) {
            $query = JurnalMengajar::query()
                ->with(['user:id,name', 'kelas:id_kelas,nama_kelas', 'mapel:id,nama_mapel'])
                ->where('status_validasi', 'disetujui')
                ->when($request->filled('tanggal'), fn ($query) => $query->whereDate('tanggal', $request->string('tanggal')->toString()))
                ->orderByDesc('tanggal')
                ->orderByDesc('jam_ke');

            $journals = $isHistory
                ? $query->paginate(12)->withQueryString()
                : $query->limit(5)->get();
        }

        return view('public.jurnal', compact('publicEnabled', 'historyEnabled', 'journals', 'event', 'showEvent', 'isHistory'));
    }
}
