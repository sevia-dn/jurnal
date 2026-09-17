<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KehadiranGuru;
use App\Models\Dispensasi;
use App\Models\Siswa;
use App\Services\WhatsAppService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class PiketController extends Controller
{
    protected $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    // Halaman Rekap Kehadiran Guru
    public function kehadiran(Request $request)
    {
        $tanggal = $request->input('tanggal', now()->format('Y-m-d'));

        // Ambil SEMUA akun guru pengajar
        $gurus = \App\Models\User::where('role', 'guru')->orderBy('name')->get();

        // Ambil data kehadiran guru pada tanggal yang dipilih
        $kehadiranRecords = KehadiranGuru::whereDate('tanggal', $tanggal)
            ->get()
            ->keyBy('user_id');

        $teachersData = $gurus->map(function ($guru) use ($kehadiranRecords) {
            $record = $kehadiranRecords->get($guru->id);

            return [
                'user_id' => $guru->id,
                'id' => $record?->id,
                'name' => $guru->name,
                'nip' => $guru->nip ?? '-',
                'no_hp' => $guru->no_hp ?? '-',
                'checkIn' => $record && $record->jam_masuk ? substr($record->jam_masuk, 0, 5) . ' WIB' : '—',
                'status' => $record ? $record->status : 'Belum Hadir',
                'verified' => $record && $record->diverifikasi_at !== null,
            ];
        });

        // Hitung statistik guru hari ini
        $totalGuru = $teachersData->count();
        $totalHadir = $teachersData->where('status', 'Hadir')->count();
        $totalIzin = $teachersData->where('status', 'Izin')->count();
        $totalSakit = $teachersData->where('status', 'Sakit')->count();
        $totalBelumHadir = $teachersData->where('status', 'Belum Hadir')->count();

        return view('dashboard.piket.kehadiran', compact(
            'teachersData',
            'tanggal',
            'totalGuru',
            'totalHadir',
            'totalIzin',
            'totalSakit',
            'totalBelumHadir'
        ));
    }

    // Verifikasi kehadiran guru (dipanggil dari tombol "Verifikasi")
    public function verifikasiKehadiran(Request $request, $id)
    {
        $kehadiran = KehadiranGuru::find($id);

        if (!$kehadiran) {
            // Jika belum ada record tapi piket ingin verifikasi hadir langsung
            $userId = $request->input('user_id');
            $tanggal = $request->input('tanggal', now()->format('Y-m-d'));
            
            $kehadiran = KehadiranGuru::create([
                'user_id' => $userId,
                'tanggal' => $tanggal,
                'jam_masuk' => now()->format('H:i:s'),
                'status' => 'Hadir',
                'diverifikasi_oleh' => auth()->id(),
                'diverifikasi_at' => now(),
            ]);
        } else {
            $kehadiran->update([
                'diverifikasi_oleh' => auth()->id(),
                'diverifikasi_at' => now(),
            ]);
        }

        return back()->with('success', 'Kehadiran guru berhasil diverifikasi.');
    }

    // Halaman Rekap Kehadiran Siswa
    public function kehadiranSiswa(Request $request)
    {
        $tanggal = $request->input('tanggal', now()->format('Y-m-d'));
        $kelasList = \App\Models\Kelas::orderBy('nama_kelas')->get();

        $selectedKelasId = $request->input('kelas_id', $kelasList->first()?->id_kelas);
        $selectedKelas = $kelasList->firstWhere('id_kelas', $selectedKelasId) ?? $kelasList->first();

        // Ambil siswa dari kelas yang dipilih
        $siswas = \App\Models\Siswa::where('kelas_id', $selectedKelas?->id_kelas)
            ->orderBy('nama')
            ->get();

        // Ambil dispensasi yang aktif & disetujui waka pada tanggal ini
        $dispensasis = Dispensasi::where('status_akhir', 'disetujui')
            ->whereDate('tanggal', '<=', $tanggal)
            ->whereDate('tanggal_selesai', '>=', $tanggal)
            ->get()
            ->keyBy('siswa_id');

        // Ambil data absensi siswa jika ada di tabel absensis
        $absensiRecords = \App\Models\Absensi::whereIn('id_siswa', $siswas->pluck('id'))
            ->whereHas('jurnal', function ($q) use ($tanggal) {
                $q->whereDate('tanggal', $tanggal);
            })
            ->latest()
            ->get()
            ->keyBy('id_siswa');

        $studentsData = $siswas->map(function ($s) use ($dispensasis, $absensiRecords, $selectedKelas) {
            $dispen = $dispensasis->get($s->id);
            $absen = $absensiRecords->get($s->id);

            if ($dispen) {
                $status = 'Dispensasi';
                $catatan = 'Dispensasi: ' . $dispen->deskripsi_waktu . ' (' . $dispen->alasan . ')';
            } elseif ($absen) {
                $status = $absen->status;
                $catatan = $absen->catatan ?? '-';
            } else {
                $status = 'Hadir';
                $catatan = '-';
            }

            return [
                'id' => $s->id,
                'nis' => $s->nis,
                'name' => $s->nama,
                'gender' => $s->jenis_kelamin,
                'class' => $selectedKelas?->nama_kelas ?? 'Umum',
                'status' => $status,
                'note' => $catatan,
                'is_dispen' => $dispen !== null,
            ];
        });

        // Hitung statistik
        $totalSiswa = $studentsData->count();
        $totalHadir = $studentsData->where('status', 'Hadir')->count();
        $totalSakit = $studentsData->where('status', 'Sakit')->count();
        $totalIzin = $studentsData->where('status', 'Izin')->count();
        $totalAlfa = $studentsData->whereIn('status', ['Alfa', 'Alpa'])->count();
        $totalDispen = $studentsData->where('status', 'Dispensasi')->count();

        return view('dashboard.piket.kehadiran-siswa', compact(
            'kelasList',
            'selectedKelas',
            'selectedKelasId',
            'tanggal',
            'studentsData',
            'totalSiswa',
            'totalHadir',
            'totalSakit',
            'totalIzin',
            'totalAlfa',
            'totalDispen'
        ));
    }

    // Update Status Kehadiran Siswa oleh Guru Piket
    public function updateKehadiranSiswa(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'status' => 'required|in:Hadir,Sakit,Izin,Alfa,Dispensasi',
            'catatan' => 'nullable|string|max:255',
            'tanggal' => 'required|date',
            'kelas_id' => 'required',
        ]);

        $siswa = Siswa::findOrFail($request->siswa_id);
        
        // Cari atau buat jurnal placeholder untuk tanggal ini agar tercatat di tabel absensis
        $jurnal = \App\Models\JurnalMengajar::firstOrCreate(
            [
                'id_kelas' => $request->kelas_id,
                'tanggal' => $request->tanggal,
                'jam_ke' => 1,
            ],
            [
                'id_user' => auth()->id(),
                'id_mapel' => 1,
                'materi' => 'Monitoring Presensi oleh Piket',
                'keterangan' => 'Dicatat/diverifikasi oleh Guru Piket',
                'status_kehadiran_guru' => 'Hadir',
            ]
        );

        \App\Models\Absensi::updateOrCreate(
            [
                'id_jurnal' => $jurnal->id_jurnal,
                'id_siswa' => $siswa->id,
            ],
            [
                'status' => $request->status === 'Alfa' ? 'Alpa' : $request->status,
                'catatan' => $request->catatan,
            ]
        );

        return back()->with('success', "Status kehadiran untuk {$siswa->nama} berhasil diperbarui.");
    }

    // Halaman form Pengajuan & Monitoring Dispensasi
    public function dispensasiForm()
    {
        $user = Auth::user();

        // Proteksi: hanya guru piket aktif, waka, atau admin yang boleh akses
        $bolehAkses = $user->role === 'admin'
            || $user->isWaka()
            || $user->isPiketActive();

        if (!$bolehAkses) {
            return redirect()->route('guru.utama')
                ->with('error', 'Halaman ini hanya dapat diakses oleh Guru Piket yang sedang bertugas atau Waka Kesiswaan.');
        }

        $siswas = Siswa::with('kelas')->orderBy('nama')->get();
        $dispensasis = Dispensasi::with(['siswa.kelas', 'pembuat', 'pemroses'])
            ->latest()
            ->paginate(15);

        // Ambil data jam pelajaran unik per jam_ke dari jadwal_pelajarans
        $daftarJam = \App\Models\JadwalPelajaran::select('jam_ke', 'jam_mulai', 'jam_selesai')
            ->orderBy('jam_ke')
            ->get()
            ->unique('jam_ke');

        return view('dashboard.piket.dispensasi', compact('siswas', 'dispensasis', 'daftarJam'));
    }

    // Simpan Pengajuan Dispensasi oleh Guru Piket
    public function dispensasiStore(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'jenis_dispensasi' => 'required|string',
            'mode_waktu' => 'required|in:sepanjang_hari,jam_tertentu',
            'jam_ke_mulai' => 'nullable|required_if:mode_waktu,jam_tertentu|integer|min:1',
            'jam_ke_selesai' => 'nullable|integer|gte:jam_ke_mulai',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan' => 'required|string',
            'bukti' => 'nullable|file|mimes:png,jpg,jpeg,pdf|max:10240',
        ], [
            'jam_ke_mulai.required_if' => 'Jam ke mulai wajib dipilih jika memilih mode Jam Tertentu.',
            'jam_ke_selesai.gte' => 'Jam ke selesai harus sama atau lebih besar dari jam ke mulai.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
        ]);

        $siswa = Siswa::with('kelas')->findOrFail($request->siswa_id);

        $buktiPath = null;
        if ($request->hasFile('bukti')) {
            $buktiPath = $request->file('bukti')->store('dispensasi', 'public');
        }

        // Tentukan jam dan hitung tipe_dispensasi secara otomatis di server
        $tglMulai = $request->tanggal_mulai;
        $tglSelesai = $request->tanggal_selesai;
        $modeWaktu = $request->mode_waktu;

        $jamKeMulai = ($modeWaktu === 'jam_tertentu') ? $request->jam_ke_mulai : null;
        $jamKeSelesai = ($modeWaktu === 'jam_tertentu' && !empty($request->jam_ke_selesai)) ? $request->jam_ke_selesai : null;

        $isSingleDay = ($tglMulai === $tglSelesai);
        $hasJamMulai = !empty($jamKeMulai);

        if ($isSingleDay && !$hasJamMulai) {
            $tipeDispensasi = 'satu_hari';
        } elseif ($isSingleDay && $hasJamMulai) {
            $tipeDispensasi = 'per_jam';
        } elseif (!$isSingleDay && !$hasJamMulai) {
            $tipeDispensasi = 'multi_hari_penuh';
        } else {
            $tipeDispensasi = 'multi_hari_per_jam';
        }

        $tokenApproval = Str::random(32);

        $dispensasi = Dispensasi::create([
            'siswa_id' => $siswa->id,
            'jenis_dispensasi' => $request->jenis_dispensasi,
            'tipe_dispensasi' => $tipeDispensasi,
            'jam_ke_mulai' => $jamKeMulai,
            'jam_ke_selesai' => $jamKeSelesai,
            'tanggal' => $tglMulai,
            'tanggal_selesai' => $tglSelesai,
            'alasan' => $request->alasan,
            'bukti' => $buktiPath,
            'status_piket' => 'disetujui',
            'status_waka' => 'menunggu',
            'status_akhir' => 'menunggu',
            'token_approval' => $tokenApproval,
            'dibuat_oleh' => auth()->id(),
        ]);

        $dispensasi->load(['siswa.kelas', 'pembuat']);

        // Trigger Notifikasi WhatsApp ke Waka
        $this->whatsAppService->sendDispensasiNotificationToWaka($dispensasi);

        $approvalUrl = route('dispensasi.approval', ['token' => $tokenApproval]);

        return redirect()->route('piket.dispensasi.form')
            ->with('success', "Pengajuan dispensasi untuk {$siswa->nama} berhasil dibuat.")
            ->with('approval_url', $approvalUrl)
            ->with('token_approval', $tokenApproval);
    }
}
