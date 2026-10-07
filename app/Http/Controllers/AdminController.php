<?php

namespace App\Http\Controllers;

use App\Imports\GuruImport;
use App\Imports\JadwalImport;
use App\Models\Absensi;
use App\Models\Dispensasi;
use App\Models\JadwalPelajaran;
use App\Models\JadwalPiket;
use App\Models\JurnalMengajar;
use App\Models\Kelas;
use App\Models\KetidakhadiranGuru;
use App\Models\Mapel;
use App\Models\PasswordResetRequest;
use App\Models\Pengaturan;
use App\Models\PiketKehadiranSiswa;
use App\Models\Siswa;
use App\Models\User;
use App\Services\JournalStatusService;
use App\Services\ScheduleTimeService;
use App\Services\WhatsAppService;
use App\SimplePdfDocument;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{
    // =========================================================================
    // 1. DASHBOARD UTAMA
    // =========================================================================
    public function index()
    {
        $jumlahGuru = User::where('role', 'guru')->count();
        $jumlahKelas = Kelas::count();
        $jumlahMapel = Mapel::count();
        $jumlahSiswa = Siswa::count();
        $jurnalHariIni = JurnalMengajar::whereDate('tanggal', now()->toDateString())->count();

        $recentJurnals = JurnalMengajar::with(['kelas', 'guru', 'mapel'])
            ->orderBy('id_jurnal', 'desc')
            ->take(5)
            ->get();

        $aktivitasTerbaru = [];
        foreach ($recentJurnals as $j) {
            $aktivitasTerbaru[] = [
                'waktu' => Carbon::parse($j->tanggal)->translatedFormat('d M Y').', Jam ke-'.($j->jam_ke ?? '1'),
                'nama_guru' => optional($j->guru)->name ?? 'Guru Pengampu',
                'mapel' => optional($j->mapel)->nama_mapel ?? 'Mata Pelajaran',
                'kelas' => optional($j->kelas)->nama_kelas ?? '-',
                'status' => $j->status_kehadiran_guru ?? 'Hadir',
            ];
        }

        return view('dashboard.admin.admin', compact(
            'jumlahGuru',
            'jumlahKelas',
            'jumlahMapel',
            'jumlahSiswa',
            'jurnalHariIni',
            'aktivitasTerbaru'
        ));
    }

    // =========================================================================
    // 2. GURU (DATA GURU, CRUD, BATCH ACTIONS, IMPORT EXCEL)
    // =========================================================================
    public function guru(Request $request)
    {
        $search = $request->query('search');

        $query = User::where('role', 'guru')->with('mapel');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('name')->get();

        // Ambil pemetaan seluruh mapel yang diajar tiap guru di jadwal pelajaran (multi-mapel)
        $jadwalRecords = JadwalPelajaran::whereNotNull('id_user')
            ->whereNotNull('mapel')
            ->where('mapel', 'not like', '%istirahat%')
            ->where('mapel', 'not like', '%upacara%')
            ->where('mapel', 'not like', '%pembiasaan%')
            ->select('id_user', 'mapel')
            ->distinct()
            ->get();

        $allMapelsByUser = [];
        foreach ($jadwalRecords as $jr) {
            if ($jr->mapel) {
                $allMapelsByUser[$jr->id_user][$jr->mapel] = true;
            }
        }

        foreach ($users as $u) {
            $mapelList = isset($allMapelsByUser[$u->id]) ? array_keys($allMapelsByUser[$u->id]) : [];
            if ($u->mapel && ! in_array($u->mapel->nama_mapel, $mapelList)) {
                $mapelList[] = $u->mapel->nama_mapel;
            }
            sort($mapelList);
            $u->all_mapel_names = $mapelList;
        }

        $mapels = Mapel::orderBy('nama_mapel')->get();

        return view('dashboard.admin.guru', compact('users', 'mapels', 'search'));
    }

    public function storeGuru(Request $request)
    {
        $validated = $request->validate([
            'nip' => 'nullable|string|max:30',
            'nama' => 'required|string|max:255',
            'mapel_id' => 'nullable|exists:mapels,id',
            'no_hp' => 'nullable|string|max:25',
        ], [
            'nama.required' => 'Nama guru wajib diisi.',
            'mapel_id.exists' => 'Mata pelajaran yang dipilih tidak valid.',
        ]);

        if (! empty($validated['nip'])) {
            $existing = User::where('nip', $validated['nip'])->first();
            if ($existing) {
                return back()->withErrors(['nip' => 'NIP sudah terdaftar dalam sistem.'])->withInput();
            }
        }

        $baseUsername = ! empty($validated['nip']) ? $validated['nip'] : Str::slug($validated['nama'], '_');
        $username = $baseUsername;
        $counter = 1;
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername.'_'.$counter;
            $counter++;
        }

        $defaultPassword = ! empty($validated['nip']) ? $validated['nip'] : 'guru123';

        User::create([
            'name' => $validated['nama'],
            'username' => $username,
            'nip' => $validated['nip'] ?? null,
            'role' => 'guru',
            'no_hp' => $validated['no_hp'] ?? null,
            'mapel_id' => $validated['mapel_id'] ?: null,
            'password' => Hash::make($defaultPassword),
        ]);

        return redirect()->route('dashboard.guru')->with('success', 'Data guru baru berhasil ditambahkan!');
    }

    public function updateGuru(Request $request, $id)
    {
        $user = User::where('role', 'guru')->findOrFail($id);

        $validated = $request->validate([
            'nip' => 'nullable|string|max:30|unique:users,nip,'.$user->id,
            'nama' => 'required|string|max:255',
            'mapel_id' => 'nullable|exists:mapels,id',
            'no_hp' => 'nullable|string|max:25',
        ]);

        $user->update([
            'name' => $validated['nama'],
            'nip' => $validated['nip'] ?? null,
            'no_hp' => $validated['no_hp'] ?? null,
            'mapel_id' => $validated['mapel_id'] ?: null,
        ]);

        return redirect()->route('dashboard.guru')->with('success', 'Data guru '.$user->name.' berhasil diperbarui!');
    }

    public function destroyGuru($id)
    {
        $user = User::where('role', 'guru')->findOrFail($id);
        $name = $user->name;
        $user->delete();

        return redirect()->route('dashboard.guru')->with('success', 'Data guru '.$name.' berhasil dihapus!');
    }

    // BATCH DELETE GURU
    public function batchDeleteGuru(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids) || ! is_array($ids)) {
            return back()->with('error', 'Pilih minimal satu data guru untuk dihapus.');
        }

        $count = User::where('role', 'guru')->whereIn('id', $ids)->delete();

        return redirect()->route('dashboard.guru')->with('success', "{$count} data guru berhasil dihapus secara masal!");
    }

    // BATCH EDIT MAPEL GURU
    public function batchEditGuru(Request $request)
    {
        $ids = $request->input('ids', []);
        $mapelId = $request->input('mapel_id');

        if (empty($ids) || ! is_array($ids)) {
            return back()->with('error', 'Pilih minimal satu data guru.');
        }

        $validated = $request->validate([
            'mapel_id' => 'required|exists:mapels,id',
        ]);

        $mapel = Mapel::find($mapelId);
        $count = User::where('role', 'guru')->whereIn('id', $ids)->update(['mapel_id' => $mapelId]);

        return redirect()->route('dashboard.guru')->with('success', "Mata pelajaran {$mapel->nama_mapel} berhasil diterapkan ke {$count} guru!");
    }

    // IMPORT GURU (EXCEL / CSV)
    public function importGuru(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ], [
            'file.required' => 'Pilih file Excel (.xlsx, .xls) atau .csv terlebih dahulu.',
            'file.mimes' => 'Format file harus berupa Excel (.xlsx, .xls) atau .csv.',
        ]);

        try {
            $import = new GuruImport;
            Excel::import($import, $request->file('file'));

            return redirect()->route('dashboard.guru')->with('success', "Import selesai! {$import->importedCount} data guru baru ditambahkan dan {$import->updatedCount} data diperbarui.");
        } catch (\Throwable $e) {
            return redirect()->route('dashboard.guru')->with('error', 'Gagal memproses file Excel: '.$e->getMessage());
        }
    }

    // DOWNLOAD TEMPLATE GURU
    public function downloadTemplateGuru()
    {
        $filename = 'template_import_guru.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            // Header
            fputcsv($handle, ['nip', 'nama', 'mapel', 'no_hp']);
            // Contoh baris
            fputcsv($handle, ['198005122005011002', 'Budi Santoso, S.Pd', 'Pemrograman Web dan Perangkat Bergerak', '081234567890']);
            fputcsv($handle, ['198507232010012004', 'Siti Aminah, M.Pd', 'Basis Data (Database)', '082345678901']);
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    // =========================================================================
    // 3. KELAS (DATA KELAS, CRUD, BATCH DELETE)
    // =========================================================================
    public function kelas(Request $request)
    {
        $search = $request->query('search');

        $query = Kelas::withCount('siswas')
            ->where('nama_kelas', 'not like', 'XII%')
            ->where('nama_kelas', 'not like', '12%');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_kelas', 'like', "%{$search}%")
                    ->orWhere('wali_kelas', 'like', "%{$search}%");
            });
        }

        $kelasList = $query->orderBy('nama_kelas')->get();
        $allActiveSiswas = Siswa::with('kelas')->get();
        $gurus = User::where('role', 'guru')->orderBy('name', 'asc')->get();

        return view('dashboard.admin.kelas', compact('kelasList', 'allActiveSiswas', 'gurus', 'search'));
    }

    public function storeKelas(Request $request)
    {
        if (preg_match('/^(XII|12)\b/i', $request->input('nama_kelas', ''))) {
            return back()->withErrors(['nama_kelas' => 'Kelas 12 sedang PKL, hanya kelas 10 dan 11 yang dapat didaftarkan.'])->withInput();
        }

        $waliInput = trim($request->input('wali_kelas') ?: $request->input('nama_guru') ?: '');
        $waliName = null;
        if (! empty($waliInput)) {
            $guru = User::where('role', 'guru')
                ->where(function ($q) use ($waliInput) {
                    $q->where('name', $waliInput)
                        ->orWhere('nip', $waliInput);
                })->first();

            if (! $guru) {
                return back()->withErrors(['wali_kelas' => 'Guru tidak ada / tidak terdaftar di data guru sekolah.'])->withInput();
            }
            $waliName = $guru->name;
        }

        $request->merge(['wali_kelas' => $waliName]);

        $validated = $request->validate([
            'nama_kelas' => 'required|string|max:20|unique:kelas,nama_kelas',
            'wali_kelas' => 'nullable|string|max:100',
            'jumlah_siswa' => 'nullable|integer|min:0',
        ], [
            'nama_kelas.required' => 'Nama kelas wajib diisi.',
            'nama_kelas.unique' => 'Nama kelas sudah terdaftar.',
        ]);

        Kelas::create([
            'nama_kelas' => $validated['nama_kelas'],
            'wali_kelas' => $validated['wali_kelas'] ?? null,
            'jumlah_siswa' => $validated['jumlah_siswa'] ?? 0,
        ]);

        return redirect()->route('dashboard.kelas')->with('success', 'Kelas baru berhasil ditambahkan!');
    }

    public function updateKelas(Request $request, $id)
    {
        $kelas = Kelas::findOrFail($id);

        if (preg_match('/^(XII|12)\b/i', $request->input('nama_kelas', ''))) {
            return back()->withErrors(['nama_kelas' => 'Kelas 12 sedang PKL, hanya kelas 10 dan 11 yang dapat didaftarkan.'])->withInput();
        }

        $waliInput = trim($request->input('wali_kelas') ?: $request->input('nama_guru') ?: '');
        $waliName = null;
        if (! empty($waliInput)) {
            $guru = User::where('role', 'guru')
                ->where(function ($q) use ($waliInput) {
                    $q->where('name', $waliInput)
                        ->orWhere('nip', $waliInput);
                })->first();

            if (! $guru) {
                return back()->withErrors(['wali_kelas' => 'Guru tidak ada / tidak terdaftar di data guru sekolah.'])->withInput();
            }
            $waliName = $guru->name;
        }

        $request->merge(['wali_kelas' => $waliName]);

        $validated = $request->validate([
            'nama_kelas' => 'required|string|max:20|unique:kelas,nama_kelas,'.$kelas->id_kelas.',id_kelas',
            'wali_kelas' => 'nullable|string|max:100',
            'jumlah_siswa' => 'nullable|integer|min:0',
        ]);

        $kelas->update([
            'nama_kelas' => $validated['nama_kelas'],
            'wali_kelas' => $validated['wali_kelas'] ?? null,
            'jumlah_siswa' => $validated['jumlah_siswa'] ?? 0,
        ]);

        return redirect()->route('dashboard.kelas')->with('success', 'Data kelas '.$kelas->nama_kelas.' berhasil diperbarui!');
    }

    public function destroyKelas($id)
    {
        $kelas = Kelas::findOrFail($id);
        $nama = $kelas->nama_kelas;
        $kelas->delete();

        return redirect()->route('dashboard.kelas')->with('success', 'Kelas '.$nama.' berhasil dihapus!');
    }

    public function batchDeleteKelas(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids) || ! is_array($ids)) {
            return back()->with('error', 'Pilih minimal satu kelas untuk dihapus.');
        }

        $count = Kelas::whereIn('id_kelas', $ids)->delete();

        return redirect()->route('dashboard.kelas')->with('success', "{$count} data kelas berhasil dihapus masal!");
    }

    // =========================================================================
    // 4. SISWA (DATA SISWA, CRUD, BATCH ACTIONS)
    // =========================================================================
    public function siswa(Request $request)
    {
        $search = $request->query('search');
        $kelasId = $request->query('kelas_id');

        $query = Siswa::with('kelas');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%")
                    ->orWhereHas('kelas', function ($k) use ($search) {
                        $k->where('nama_kelas', 'like', "%{$search}%");
                    });
            });
        }

        if ($kelasId) {
            $query->where('kelas_id', $kelasId);
        }

        $siswas = $query->orderBy('nama')->get();
        $kelasList = Kelas::orderBy('nama_kelas')->get();

        return view('dashboard.admin.siswa', compact('siswas', 'kelasList', 'search', 'kelasId'));
    }

    public function storeSiswa(Request $request)
    {
        if (! $request->filled('nama') && $request->filled('nama_siswa')) {
            $request->merge(['nama' => $request->input('nama_siswa')]);
        }
        if (! $request->filled('nisn') && $request->filled('nis')) {
            $request->merge(['nisn' => $request->input('nis')]);
        }
        if (! $request->filled('jenis_kelamin')) {
            $request->merge(['jenis_kelamin' => 'L']);
        }

        $validated = $request->validate([
            'nisn' => 'required|string|max:30',
            'nama' => 'required|string|max:255',
            'kelas_id' => 'required|exists:kelas,id_kelas',
            'jenis_kelamin' => 'required|in:L,P',
        ], [
            'nisn.required' => 'NISN wajib diisi.',
            'nama.required' => 'Nama siswa wajib diisi.',
            'kelas_id.required' => 'Pilih kelas siswa.',
        ]);

        $siswa = Siswa::create($validated);
        $siswa->load('kelas');
        $this->syncKelasJumlahSiswa($validated['kelas_id']);

        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'message' => 'Data siswa '.$siswa->nama.' berhasil ditambahkan!',
                'siswa' => [
                    'id' => $siswa->id,
                    'nama' => $siswa->nama,
                    'nisn' => $siswa->nisn,
                    'nis' => $siswa->nisn,
                    'kelas_id' => $siswa->kelas_id,
                    'jenis_kelamin' => $siswa->jenis_kelamin ?? 'L',
                    'nama_kelas' => $siswa->kelas->nama_kelas ?? '-',
                ],
            ]);
        }

        if (str_contains(url()->previous(), 'kelas') || $request->input('from_kelas')) {
            return redirect()->route('dashboard.kelas', ['kelas_id' => $validated['kelas_id']])
                ->with('open_kelas_id', $validated['kelas_id'])
                ->with('success', 'Data siswa berhasil ditambahkan!');
        }

        return redirect()->route('dashboard.siswa')->with('success', 'Data siswa berhasil ditambahkan!');
    }

    public function updateSiswa(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);
        $oldKelasId = $siswa->kelas_id;

        if (! $request->filled('nama') && $request->filled('nama_siswa')) {
            $request->merge(['nama' => $request->input('nama_siswa')]);
        }
        if (! $request->filled('nisn') && $request->filled('nis')) {
            $request->merge(['nisn' => $request->input('nis')]);
        }
        if (! $request->filled('jenis_kelamin')) {
            $request->merge(['jenis_kelamin' => $siswa->jenis_kelamin ?? 'L']);
        }

        $validated = $request->validate([
            'nisn' => 'required|string|max:30',
            'nama' => 'required|string|max:255',
            'kelas_id' => 'required|exists:kelas,id_kelas',
            'jenis_kelamin' => 'required|in:L,P',
        ], [
            'nisn.required' => 'NISN wajib diisi.',
            'nama.required' => 'Nama siswa wajib diisi.',
            'kelas_id.required' => 'Pilih kelas siswa.',
        ]);

        $siswa->update($validated);
        $siswa->load('kelas');
        $this->syncKelasJumlahSiswa($oldKelasId, $validated['kelas_id']);

        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'message' => 'Data siswa '.$siswa->nama.' berhasil diperbarui!',
                'siswa' => [
                    'id' => $siswa->id,
                    'nama' => $siswa->nama,
                    'nisn' => $siswa->nisn,
                    'nis' => $siswa->nisn,
                    'kelas_id' => $siswa->kelas_id,
                    'jenis_kelamin' => $siswa->jenis_kelamin ?? 'L',
                    'nama_kelas' => $siswa->kelas->nama_kelas ?? '-',
                ],
            ]);
        }

        if (str_contains(url()->previous(), 'kelas') || $request->input('from_kelas')) {
            return redirect()->route('dashboard.kelas', ['kelas_id' => $validated['kelas_id']])
                ->with('open_kelas_id', $validated['kelas_id'])
                ->with('success', 'Data siswa '.$siswa->nama.' berhasil diperbarui!');
        }

        return redirect()->route('dashboard.siswa')->with('success', 'Data siswa '.$siswa->nama.' berhasil diperbarui!');
    }

    public function destroySiswa($id)
    {
        $siswa = Siswa::findOrFail($id);
        $nama = $siswa->nama;
        $kelasId = $siswa->kelas_id;
        $siswa->delete();
        $this->syncKelasJumlahSiswa($kelasId);

        if (str_contains(url()->previous(), 'kelas') || request()->input('from_kelas')) {
            return redirect()->route('dashboard.kelas', ['kelas_id' => $kelasId])
                ->with('open_kelas_id', $kelasId)
                ->with('success', 'Data siswa '.$nama.' berhasil dihapus!');
        }

        return redirect()->route('dashboard.siswa')->with('success', 'Data siswa '.$nama.' berhasil dihapus!');
    }

    public function batchDeleteSiswa(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids) || ! is_array($ids)) {
            return back()->with('error', 'Pilih minimal satu siswa untuk dihapus.');
        }

        $affectedKelasIds = Siswa::whereIn('id', $ids)->pluck('kelas_id')->unique()->toArray();
        $count = Siswa::whereIn('id', $ids)->delete();
        $this->syncKelasJumlahSiswa(...$affectedKelasIds);

        return redirect()->route('dashboard.siswa')->with('success', "{$count} data siswa berhasil dihapus masal!");
    }

    public function batchEditSiswa(Request $request)
    {
        $ids = $request->input('ids', []);
        $kelasId = $request->input('kelas_id');

        if (empty($ids) || ! is_array($ids)) {
            return back()->with('error', 'Pilih minimal satu siswa.');
        }

        $request->validate([
            'kelas_id' => 'required|exists:kelas,id_kelas',
        ]);

        $kelas = Kelas::find($kelasId);
        $oldKelasIds = Siswa::whereIn('id', $ids)->pluck('kelas_id')->unique()->toArray();
        $count = Siswa::whereIn('id', $ids)->update(['kelas_id' => $kelasId]);
        $this->syncKelasJumlahSiswa($kelasId, ...$oldKelasIds);

        return redirect()->route('dashboard.siswa')->with('success', "{$count} siswa berhasil dipindahkan ke kelas {$kelas->nama_kelas}!");
    }

    protected function syncKelasJumlahSiswa(...$kelasIds)
    {
        $kelasIds = array_unique(array_filter($kelasIds));
        foreach ($kelasIds as $kId) {
            $kelas = Kelas::find($kId);
            if ($kelas) {
                $actualCount = Siswa::where('kelas_id', $kId)->count();
                $kelas->update(['jumlah_siswa' => $actualCount]);
            }
        }
    }

    // =========================================================================
    // 5. MAPEL (MATA PELAJARAN, CRUD, BATCH DELETE)
    // =========================================================================
    public function mapel(Request $request)
    {
        $search = $request->query('search');
        $kategoriFilter = $request->query('kategori');

        $query = Mapel::with('gurus');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_mapel', 'like', "%{$search}%")
                    ->orWhere('kode_mapel', 'like', "%{$search}%")
                    ->orWhereHas('gurus', function ($g) use ($search) {
                        $g->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereIn('id', function ($sub) use ($search) {
                        $sub->select('id_mapel')
                            ->from('jadwal_pelajarans')
                            ->whereIn('id_user', function ($uSub) use ($search) {
                                $uSub->select('id')->from('users')->where('name', 'like', "%{$search}%");
                            });
                    });
            });
        }

        if ($kategoriFilter === 'jurusan') {
            $query->where('kategori', 'jurusan');
        } elseif ($kategoriFilter === 'biasa') {
            $query->where(function ($q) {
                $q->where('kategori', 'biasa')->orWhereNull('kategori');
            });
        }

        $mapels = $query->orderBy('nama_mapel')->get();
        $gurus = User::where('role', 'guru')->with('mapel')->orderBy('name')->get();

        // Ambil pemetaan guru yang mengajar mapel terkait di jadwal pelajaran (multi-mapel sesuai jadwal)
        $jadwalTeachersByMapelId = [];
        $jadwalTeachersByMapelName = [];
        $allMapelsByUser = [];

        $jadwalRecords = JadwalPelajaran::join('users', 'jadwal_pelajarans.id_user', '=', 'users.id')
            ->where('jadwal_pelajarans.mapel', 'not like', '%istirahat%')
            ->where('jadwal_pelajarans.mapel', 'not like', '%upacara%')
            ->where('jadwal_pelajarans.mapel', 'not like', '%pembiasaan%')
            ->select('jadwal_pelajarans.id_mapel', 'jadwal_pelajarans.mapel', 'users.id as user_id', 'users.name', 'users.nip')
            ->distinct()
            ->get();

        foreach ($jadwalRecords as $jr) {
            $teacherData = (object) ['id' => $jr->user_id, 'name' => $jr->name, 'nip' => $jr->nip];
            if ($jr->id_mapel) {
                $jadwalTeachersByMapelId[$jr->id_mapel][$jr->user_id] = $teacherData;
            }
            if ($jr->mapel) {
                $jadwalTeachersByMapelName[$jr->mapel][$jr->user_id] = $teacherData;
                $allMapelsByUser[$jr->user_id][$jr->mapel] = true;
            }
        }

        foreach ($mapels as $m) {
            $fromId = $jadwalTeachersByMapelId[$m->id] ?? [];
            $fromName = $jadwalTeachersByMapelName[$m->nama_mapel] ?? [];
            $byJadwal = $fromId + $fromName; // array keyed by user_id

            // Gabungkan juga dengan guru yang ditautkan di users.mapel_id
            $byMapelId = [];
            foreach ($m->gurus as $g) {
                $byMapelId[$g->id] = (object) ['id' => $g->id, 'name' => $g->name, 'nip' => $g->nip];
            }

            $allTeachers = array_values($byJadwal + $byMapelId);
            usort($allTeachers, fn ($a, $b) => strcmp($a->name, $b->name));

            $m->all_pengampus = collect($allTeachers);
            $m->jadwal_gurus = array_values(array_unique(array_map(fn ($t) => $t->name, array_values($byJadwal))));
        }

        $counts = [
            'total' => Mapel::count(),
            'jurusan' => Mapel::where('kategori', 'jurusan')->count(),
            'biasa' => Mapel::where(function ($q) {
                $q->where('kategori', '!=', 'jurusan')->orWhereNull('kategori');
            })->count(),
        ];

        $guruJson = $gurus->map(function ($g) use ($allMapelsByUser) {
            $jadwalMapelNames = isset($allMapelsByUser[$g->id]) ? array_keys($allMapelsByUser[$g->id]) : [];
            if ($g->mapel && ! in_array($g->mapel->nama_mapel, $jadwalMapelNames)) {
                $jadwalMapelNames[] = $g->mapel->nama_mapel;
            }

            return [
                'id' => $g->id,
                'name' => $g->name,
                'nip' => $g->nip ?? '',
                'mapel_id' => $g->mapel_id,
                'mapel_name' => ! empty($jadwalMapelNames) ? implode(', ', $jadwalMapelNames) : '',
                'mapel_names' => $jadwalMapelNames,
            ];
        });

        return view('dashboard.admin.mapel', compact('mapels', 'gurus', 'counts', 'search', 'guruJson'));
    }

    public function storeMapel(Request $request)
    {
        $validated = $request->validate([
            'kode_mapel' => 'required|string|max:30|unique:mapels,kode_mapel',
            'nama_mapel' => 'required|string|max:255',
            'kategori' => 'nullable|string|in:biasa,jurusan',
            'guru_id' => 'nullable|exists:users,id',
            'guru_names' => 'nullable',
        ]);

        $mapel = Mapel::create([
            'kode_mapel' => $validated['kode_mapel'],
            'nama_mapel' => $validated['nama_mapel'],
            'kategori' => $validated['kategori'] ?? 'biasa',
        ]);

        $assignedGuruIds = [];
        if (! empty($validated['guru_id'])) {
            $assignedGuruIds[] = (int) $validated['guru_id'];
        }
        if ($request->filled('guru_names')) {
            $rawNames = $request->input('guru_names');
            $names = is_array($rawNames) ? $rawNames : json_decode($rawNames, true);
            if (! is_array($names)) {
                $names = array_filter(array_map('trim', explode(',', (string) $rawNames)));
            }
            if (! empty($names)) {
                $foundIds = User::where('role', 'guru')->whereIn('name', $names)->pluck('id')->toArray();
                $assignedGuruIds = array_merge($assignedGuruIds, $foundIds);
            }
        }

        $assignedGuruIds = array_unique($assignedGuruIds);
        $maxGuru = 25;
        $assignedGuruIds = array_slice($assignedGuruIds, 0, $maxGuru);

        if (! empty($assignedGuruIds)) {
            User::whereIn('id', $assignedGuruIds)->update(['mapel_id' => $mapel->id]);
        }

        return redirect()->route('dashboard.mapel')->with('success', 'Mata pelajaran berhasil ditambahkan!');
    }

    public function updateMapel(Request $request, $id)
    {
        $mapel = Mapel::findOrFail($id);

        $validated = $request->validate([
            'kode_mapel' => 'required|string|max:30|unique:mapels,kode_mapel,'.$mapel->id,
            'nama_mapel' => 'required|string|max:255',
            'kategori' => 'nullable|string|in:biasa,jurusan',
            'guru_id' => 'nullable|exists:users,id',
            'guru_names' => 'nullable',
        ]);

        $kategori = $validated['kategori'] ?? $mapel->kategori ?? 'biasa';

        $mapel->update([
            'kode_mapel' => $validated['kode_mapel'],
            'nama_mapel' => $validated['nama_mapel'],
            'kategori' => $kategori,
        ]);

        $assignedGuruIds = [];
        if (! empty($validated['guru_id'])) {
            $assignedGuruIds[] = (int) $validated['guru_id'];
        }
        if ($request->filled('guru_names')) {
            $rawNames = $request->input('guru_names');
            $names = is_array($rawNames) ? $rawNames : json_decode($rawNames, true);
            if (! is_array($names)) {
                $names = array_filter(array_map('trim', explode(',', (string) $rawNames)));
            }
            if (! empty($names)) {
                $foundIds = User::where('role', 'guru')->whereIn('name', $names)->pluck('id')->toArray();
                $assignedGuruIds = array_merge($assignedGuruIds, $foundIds);
            }
        }

        $assignedGuruIds = array_unique($assignedGuruIds);
        $maxGuru = 25;
        $assignedGuruIds = array_slice($assignedGuruIds, 0, $maxGuru);

        if (! empty($assignedGuruIds)) {
            // Dissociate teachers no longer assigned to this mapel
            User::where('mapel_id', $mapel->id)
                ->whereNotIn('id', $assignedGuruIds)
                ->update(['mapel_id' => null]);

            // Associate assigned teachers
            User::whereIn('id', $assignedGuruIds)->update(['mapel_id' => $mapel->id]);
        } else {
            // Remove all teachers assigned to this mapel
            User::where('mapel_id', $mapel->id)->update(['mapel_id' => null]);
        }

        return redirect()->route('dashboard.mapel')->with('success', 'Mata pelajaran '.$mapel->nama_mapel.' berhasil diperbarui!');
    }

    public function destroyMapel($id)
    {
        $mapel = Mapel::findOrFail($id);
        $nama = $mapel->nama_mapel;

        // Dissociate teachers and remove related schedules safely
        User::where('mapel_id', $mapel->id)->update(['mapel_id' => null]);
        JadwalPelajaran::where('id_mapel', $mapel->id)->delete();
        $mapel->delete();

        return redirect()->route('dashboard.mapel')->with('success', 'Mata pelajaran '.$nama.' berhasil dihapus!');
    }

    public function batchDeleteMapel(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids) || ! is_array($ids)) {
            return back()->with('error', 'Pilih minimal satu mata pelajaran.');
        }

        User::whereIn('mapel_id', $ids)->update(['mapel_id' => null]);
        JadwalPelajaran::whereIn('id_mapel', $ids)->delete();
        $count = Mapel::whereIn('id', $ids)->delete();

        return redirect()->route('dashboard.mapel')->with('success', "{$count} mata pelajaran berhasil dihapus masal!");
    }

    // =========================================================================
    // 6. JADWAL PELAJARAN (CRUD & BATCH DELETE)
    // =========================================================================
    public function jadwal(Request $request)
    {
        $kelasId = $request->query('kelas_id') ?: $request->query('kelas');
        $hari = $request->query('hari');

        $kelasList = Kelas::orderBy('nama_kelas')->get();
        $kelases = $kelasList;
        $selectedKelas = $kelasList->firstWhere('id_kelas', $kelasId) ?? $kelasList->first();
        $selectedHari = $hari ?? 'Senin';
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        $search = $request->query('search');

        $query = JadwalPelajaran::with(['kelas', 'guru', 'mapelItem']);

        if ($selectedKelas) {
            $query->where('id_kelas', $selectedKelas->id_kelas);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('mapel', 'like', "%{$search}%")
                    ->orWhereHas('guru', fn ($g) => $g->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('mapelItem', fn ($m) => $m->where('nama_mapel', 'like', "%{$search}%"));
            });
        }

        $jadwals = $query->orderByRaw("CASE hari WHEN 'Senin' THEN 1 WHEN 'Selasa' THEN 2 WHEN 'Rabu' THEN 3 WHEN 'Kamis' THEN 4 WHEN 'Jumat' THEN 5 WHEN 'Sabtu' THEN 6 ELSE 7 END")
            ->orderBy('jam_mulai', 'asc')
            ->orderBy('jam_ke', 'asc')
            ->get();
        $mapels = Mapel::orderBy('nama_mapel', 'asc')->get();
        $gurus = User::where('role', 'guru')->orderBy('name', 'asc')->get();

        $piketWakas = JadwalPiket::with('user')
            ->where('tipe', 'waka')
            ->orderByRaw("CASE hari WHEN 'Senin' THEN 1 WHEN 'Selasa' THEN 2 WHEN 'Rabu' THEN 3 WHEN 'Kamis' THEN 4 WHEN 'Jumat' THEN 5 WHEN 'Sabtu' THEN 6 ELSE 7 END")
            ->get();

        $piketGurus = JadwalPiket::with('user')
            ->where('tipe', 'guru')
            ->orderByRaw("CASE hari WHEN 'Senin' THEN 1 WHEN 'Selasa' THEN 2 WHEN 'Rabu' THEN 3 WHEN 'Kamis' THEN 4 WHEN 'Jumat' THEN 5 WHEN 'Sabtu' THEN 6 ELSE 7 END")
            ->get();

        $shiftSeninMinutes = (int) Pengaturan::getValue('shift_senin_minutes', 40);
        $shiftJumatMinutes = (int) Pengaturan::getValue('shift_jumat_minutes', 30);
        $isSeninMaju = (bool) Pengaturan::getValue('senin_is_maju', 0);
        $isJumatMaju = (bool) Pengaturan::getValue('jumat_is_maju', 0);

        return view('dashboard.admin.jadwal', compact(
            'jadwals',
            'kelasList',
            'kelases',
            'mapels',
            'gurus',
            'kelasId',
            'hari',
            'selectedKelas',
            'selectedHari',
            'hariList',
            'piketWakas',
            'piketGurus',
            'shiftSeninMinutes',
            'shiftJumatMinutes',
            'isSeninMaju',
            'isJumatMaju',
            'search'
        ));
    }

    public function jadwalPenugasan(Request $request)
    {
        $bulan = min(12, max(1, $request->integer('bulan', now('Asia/Jakarta')->month)));
        $tahun = min(2100, max(2026, $request->integer('tahun', now('Asia/Jakarta')->year)));

        $penugasanTanggal = JadwalPiket::with('user')
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->orderBy('tanggal')
            ->orderBy('shift')
            ->get()
            ->groupBy(fn (JadwalPiket $jadwal): string => $jadwal->tanggal->toDateString());

        $periodeTersedia = JadwalPiket::query()
            ->whereNotNull('tanggal')
            ->selectRaw('YEAR(tanggal) as tahun, MONTH(tanggal) as bulan')
            ->distinct()
            ->orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->get();

        $gurus = User::where('role', 'guru')->orderBy('name')->get();
        $penugasanMingguan = JadwalPiket::with('user')
            ->whereNull('tanggal')
            ->get()
            ->groupBy('hari');

        $penugasanPiket = collect();
        $tanggalMulai = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $tanggalSelesai = $tanggalMulai->copy()->endOfMonth();

        for ($tanggal = $tanggalMulai->copy(); $tanggal->lte($tanggalSelesai); $tanggal->addDay()) {
            $penugasanKhusus = $penugasanTanggal->get($tanggal->toDateString(), collect());
            if ($penugasanKhusus->isNotEmpty()) {
                $penugasanPiket->put($tanggal->toDateString(), $penugasanKhusus);

                continue;
            }

            $hari = match ($tanggal->dayOfWeek) {
                Carbon::MONDAY => 'Senin',
                Carbon::TUESDAY => 'Selasa',
                Carbon::WEDNESDAY => 'Rabu',
                Carbon::THURSDAY => 'Kamis',
                Carbon::FRIDAY => 'Jumat',
                Carbon::SATURDAY => 'Sabtu',
                default => null,
            };
            $penugasanMingguanHari = $hari ? $penugasanMingguan->get($hari, collect()) : collect();

            if ($penugasanMingguanHari->isNotEmpty()) {
                $penugasanPiket->put($tanggal->toDateString(), $penugasanMingguanHari);
            }
        }

        return view('dashboard.admin.jadwal-penugasan', compact(
            'bulan',
            'tahun',
            'penugasanPiket',
            'periodeTersedia',
            'gurus',
            'penugasanMingguan'
        ));
    }

    public function downloadJadwalPdf(Request $request): Response
    {
        $lines = JadwalPelajaran::with(['kelas', 'guru', 'mapelItem'])
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get()
            ->map(function (JadwalPelajaran $jadwal): string {
                $kelas = $jadwal->kelas?->nama_kelas ?? '-';
                $mapel = $jadwal->mapelItem?->nama_mapel ?? $jadwal->mapel ?? '-';
                $guru = $jadwal->guru?->name ?? '-';

                return sprintf(
                    '%s | %s | %s-%s | %s | %s',
                    $jadwal->hari,
                    $kelas,
                    substr((string) $jadwal->jam_mulai, 0, 5),
                    substr((string) $jadwal->jam_selesai, 0, 5),
                    $mapel,
                    $guru
                );
            })
            ->all();

        return $this->pdfDownload(
            'Jadwal Pelajaran',
            array_merge(['Hari | Kelas | Waktu | Mata Pelajaran | Guru', str_repeat('-', 120)], $lines),
            'jadwal-pelajaran.pdf'
        );
    }

    public function rekapJurnalDetail(int $id): JsonResponse
    {
        $jurnal = JurnalMengajar::with(['kelas', 'mapel', 'guru'])->findOrFail($id);
        $statusInfo = app(JournalStatusService::class)->determineStatus($jurnal->tanggal, $jurnal);
        $data = $jurnal->toArray();
        $data['status_info'] = $statusInfo;
        $data['filled_at_formatted'] = $jurnal->filled_at ? Carbon::parse($jurnal->filled_at)->translatedFormat('d M Y H:i') : null;

        return response()->json($data);
    }

    public function downloadRekapJurnalPdf(Request $request): Response
    {

        $periode = $request->query('periode', 'harian');
        $tanggal = $request->query('tanggal', now('Asia/Jakarta')->toDateString());
        $bulan = min(12, max(1, $request->integer('bulan', now('Asia/Jakarta')->month)));
        $tahun = min(2100, max(2026, $request->integer('tahun', now('Asia/Jakarta')->year)));

        $query = JurnalMengajar::with(['kelas', 'guru', 'mapel'])->orderByDesc('tanggal')->orderBy('jam_ke');
        if ($periode === 'bulanan') {
            $query->whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan);
            $periodeLabel = Carbon::create($tahun, $bulan)->translatedFormat('F Y');
        } else {
            $query->whereDate('tanggal', $tanggal);
            $periodeLabel = Carbon::parse($tanggal)->translatedFormat('d F Y');
        }

        $lines = $query->get()->map(function (JurnalMengajar $jurnal): string {
            $statusInfo = app(JournalStatusService::class)->determineStatus($jurnal->tanggal, $jurnal);

            return sprintf(
                '%s | Kelas %s | Jam %s | %s | %s | Status: %s | Hadir: %s',
                $jurnal->tanggal,
                $jurnal->kelas?->nama_kelas ?? '-',
                $jurnal->jam_ke,
                $jurnal->guru?->name ?? '-',
                $jurnal->mapel?->nama_mapel ?? '-',
                $statusInfo['short_label'],
                $jurnal->jumlah_hadir
            );
        })->all();

        return $this->pdfDownload(
            'Rekap Jurnal Mengajar - '.$periodeLabel,
            array_merge(['Tanggal | Kelas | Jam | Guru | Mata Pelajaran | Kehadiran', str_repeat('-', 120)], $lines),
            'rekap-jurnal-'.str($periodeLabel)->slug().'.pdf'
        );
    }

    private function pdfDownload(string $title, array $lines, string $filename): Response
    {
        return response(SimplePdfDocument::make($title, $lines), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    public function storeJadwal(Request $request)
    {
        $userId = $request->input('id_user') ?: $request->input('guru_id');
        if (! $userId) {
            $userId = auth()->id() ?? optional(User::where('role', 'admin')->first())->id ?? optional(User::first())->id;
        }

        $kelasId = $request->input('id_kelas') ?: $request->input('kelas_id');
        $mapelId = $request->input('id_mapel') ?: $request->input('mapel_id');

        $mapelName = $request->input('mapel');
        if (empty($mapelName) && $mapelId) {
            $mapelName = optional(Mapel::find($mapelId))->nama_mapel;
        }
        if (empty($mapelName)) {
            $mapelName = 'Kegiatan Sekolah';
        }

        $request->merge([
            'id_user' => $userId,
            'id_kelas' => $kelasId,
            'id_mapel' => $mapelId,
            'mapel' => $mapelName,
        ]);

        $validated = $request->validate([
            'id_user' => 'required|exists:users,id',
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'id_mapel' => 'nullable|exists:mapels,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_ke' => 'required|integer|min:0',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'mapel' => 'required|string|max:100',
        ], [
            'id_kelas.required' => 'Pilih kelas jadwal.',
            'hari.required' => 'Pilih hari pelaksanaan.',
        ]);

        JadwalPelajaran::create($this->normalizeScheduleTimes($validated));

        return redirect()->route('dashboard.jadwal', ['kelas_id' => $kelasId, 'hari' => $validated['hari']])
            ->with('success', 'Jadwal pelajaran berhasil ditambahkan!');
    }

    public function updateJadwal(Request $request, $id)
    {
        $jadwal = JadwalPelajaran::findOrFail($id);

        $userId = $request->input('id_user') ?: $request->input('guru_id');
        if (! $userId) {
            $userId = $jadwal->id_user ?? auth()->id() ?? optional(User::where('role', 'admin')->first())->id;
        }

        $kelasId = $request->input('id_kelas') ?: $request->input('kelas_id') ?: $jadwal->id_kelas;
        $mapelId = $request->input('id_mapel') ?: $request->input('mapel_id') ?: $jadwal->id_mapel;

        $mapelName = $request->input('mapel');
        if (empty($mapelName) && $mapelId) {
            $mapelName = optional(Mapel::find($mapelId))->nama_mapel;
        }
        if (empty($mapelName)) {
            $mapelName = $jadwal->mapel ?? 'Kegiatan Sekolah';
        }

        $request->merge([
            'id_user' => $userId,
            'id_kelas' => $kelasId,
            'id_mapel' => $mapelId,
            'mapel' => $mapelName,
        ]);

        $validated = $request->validate([
            'id_user' => 'required|exists:users,id',
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'id_mapel' => 'nullable|exists:mapels,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_ke' => 'required|integer|min:0',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'mapel' => 'required|string|max:100',
        ]);

        $jadwal->update($this->normalizeScheduleTimes($validated));

        return redirect()->route('dashboard.jadwal', ['kelas_id' => $kelasId, 'hari' => $validated['hari']])
            ->with('success', 'Jadwal pelajaran berhasil diperbarui!');
    }

    /**
     * Pastikan sesi pelajaran tidak tersimpan sebelum slot jam ke-nya.
     * Kegiatan khusus (jam ke-0) tetap dapat menggunakan waktu kustom.
     *
     * @param  array{id_user: int|string, id_kelas: int|string, id_mapel: int|string|null, hari: string, jam_ke: int|string, jam_mulai: string, jam_selesai: string, mapel: string}  $schedule
     * @return array{id_user: int|string, id_kelas: int|string, id_mapel: int|string|null, hari: string, jam_ke: int|string, jam_mulai: string, jam_selesai: string, mapel: string}
     */
    private function normalizeScheduleTimes(array $schedule): array
    {
        $jamKe = (int) $schedule['jam_ke'];
        if ($jamKe === 0) {
            return $schedule;
        }

        $slot = app(ScheduleTimeService::class)->slot($schedule['hari'], $jamKe);
        $jamMulai = substr((string) $schedule['jam_mulai'], 0, 5);
        $jamSelesai = substr((string) $schedule['jam_selesai'], 0, 5);

        if ($jamMulai < $slot['start'] || $jamSelesai < $slot['end'] || $jamSelesai <= $jamMulai) {
            $schedule['jam_mulai'] = $slot['start'];
            $schedule['jam_selesai'] = $slot['end'];
        }

        return $schedule;
    }

    public function destroyJadwal($id)
    {
        $jadwal = JadwalPelajaran::findOrFail($id);
        $jadwal->delete();

        return redirect()->route('dashboard.jadwal')->with('success', 'Jadwal pelajaran berhasil dihapus!');
    }

    public function batchDeleteJadwal(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids) || ! is_array($ids)) {
            return back()->with('error', 'Pilih minimal satu jadwal.');
        }

        $count = JadwalPelajaran::whereIn('id_jadwal', $ids)->delete();

        return redirect()->route('dashboard.jadwal')->with('success', "{$count} jadwal pelajaran berhasil dihapus masal!");
    }

    public function shiftTimeJadwal(Request $request)
    {
        $this->ensureAdminAccess();

        $validated = $request->validate([
            'hari' => 'required|in:Senin,Jumat',
            'mode' => 'required|in:maju,normal',
            'minutes' => 'nullable|integer|min:5|max:180',
            'kelas_id' => 'nullable',
        ]);

        $hari = $validated['hari'];
        $mode = $validated['mode'];
        $hariKey = strtolower($hari); // 'senin' or 'jumat'

        $defaultMinutes = ($hari === 'Jumat')
            ? (int) Pengaturan::getValue('shift_jumat_minutes', 30)
            : (int) Pengaturan::getValue('shift_senin_minutes', 40);

        $minutes = (int) ($request->minutes ?: $defaultMinutes);
        $isCurrentlyMaju = (bool) Pengaturan::getValue($hariKey.'_is_maju', 0);

        // Guard Idempotensi: cegah pergeseran waktu berulang kali
        if ($mode === 'maju' && $isCurrentlyMaju) {
            return redirect()->back()->with('info', "Mode Jam Maju untuk hari {$hari} sudah aktif sebelumnya.");
        }

        if ($mode === 'normal' && ! $isCurrentlyMaju) {
            return redirect()->back()->with('info', "Jadwal hari {$hari} sudah dalam status jam normal.");
        }

        // Otomatis diterapkan untuk SELURUH KELAS
        $allKelasIds = Kelas::pluck('id_kelas')->toArray();
        $targetKeyword = ($hari === 'Jumat') ? 'pembiasaan' : 'upacara';

        if ($mode === 'maju') {
            DB::transaction(function () use ($allKelasIds, $hari, $targetKeyword, $minutes): void {
                // Mode maju tetap memperbarui jadwal administrasi. Guru dan validasi jurnal
                // membaca durasi yang sama melalui ScheduleTimeService.
                foreach ($allKelasIds as $kId) {
                    $lessons = JadwalPelajaran::where('id_kelas', $kId)
                        ->where('hari', $hari)
                        ->get();

                    foreach ($lessons as $lesson) {
                        $isTargetActivity = str_contains(mb_strtolower($lesson->mapel), $targetKeyword);
                        if ($isTargetActivity) {
                            $lesson->update(['status' => 'ditiadakan']);
                        } else {
                            $lesson->update([
                                'jam_mulai' => date('H:i:s', max(0, strtotime($lesson->jam_mulai) - ($minutes * 60))),
                                'jam_selesai' => date('H:i:s', max(0, strtotime($lesson->jam_selesai) - ($minutes * 60))),
                                'status' => 'aktif',
                            ]);
                        }
                    }
                }
            });

            Pengaturan::setValue($hariKey.'_is_maju', 1);
            Pengaturan::setValue($hariKey.'_shifted_minutes', $minutes);
            $msg = "Mode Jam Maju hari {$hari} berhasil diaktifkan untuk SELURUH KELAS ({$targetKeyword} ditiadakan, jam pelajaran dimajukan {$minutes} menit).";
        } else {
            // Mode Normal: Kembalikan waktu dengan menambah shifted_minutes yang tersimpan
            $shiftedMinutes = (int) Pengaturan::getValue($hariKey.'_shifted_minutes', 0);
            if ($shiftedMinutes <= 0) {
                $shiftedMinutes = $minutes;
            }

            DB::transaction(function () use ($allKelasIds, $hari, $targetKeyword, $shiftedMinutes): void {
                foreach ($allKelasIds as $kId) {
                    $lessons = JadwalPelajaran::where('id_kelas', $kId)
                        ->where('hari', $hari)
                        ->get();

                    foreach ($lessons as $lesson) {
                        $isTargetActivity = str_contains(mb_strtolower($lesson->mapel), $targetKeyword);
                        if ($isTargetActivity) {
                            $lesson->update(['status' => 'aktif']);
                        } else {
                            $lesson->update([
                                'jam_mulai' => date('H:i:s', strtotime($lesson->jam_mulai) + ($shiftedMinutes * 60)),
                                'jam_selesai' => date('H:i:s', strtotime($lesson->jam_selesai) + ($shiftedMinutes * 60)),
                                'status' => 'aktif',
                            ]);
                        }
                    }
                }
            });

            Pengaturan::setValue($hariKey.'_is_maju', 0);
            Pengaturan::setValue($hariKey.'_shifted_minutes', 0);
            $msg = "Jadwal hari {$hari} untuk SELURUH KELAS berhasil dikembalikan ke jam normal ({$targetKeyword} dilaksanakan).";
        }

        $redirectKelasId = $request->input('kelas_id') ?: optional(Kelas::first())->id_kelas;

        return redirect()->route('dashboard.jadwal', ['kelas' => $redirectKelasId, 'hari' => $hari])
            ->with('success', $msg);
    }

    // =========================================================================
    // PENGATURAN SISTEM & JAM MAJU
    // =========================================================================
    public function pengaturan()
    {
        $this->ensureAdminAccess();

        $shiftSenin = (int) Pengaturan::getValue('shift_senin_minutes', 40);
        $shiftJumat = (int) Pengaturan::getValue('shift_jumat_minutes', 30);
        $isSeninMaju = (bool) Pengaturan::getValue('senin_is_maju', 0);
        $seninShiftedMinutes = (int) Pengaturan::getValue('senin_shifted_minutes', $shiftSenin);
        $isJumatMaju = (bool) Pengaturan::getValue('jumat_is_maju', 0);
        $jumatShiftedMinutes = (int) Pengaturan::getValue('jumat_shifted_minutes', $shiftJumat);

        $tenggatOpsi = (string) Pengaturan::getValue('tenggat_opsi', 'terbatas_jam');
        $publikRiwayatAktif = (bool) Pengaturan::getValue('publik_riwayat_aktif', 1);
        $eventSekolah = (string) Pengaturan::getValue('event_sekolah', '');
        $eventSekolahTanggal = (string) Pengaturan::getValue('event_sekolah_tanggal', '');
        $eventSekolahJamPulang = (string) Pengaturan::getValue('event_sekolah_jam_pulang', '');
        $jamKosongNama = (string) Pengaturan::getValue('jam_kosong_nama', '');
        $jamKosongTanggalMulai = (string) Pengaturan::getValue('jam_kosong_tanggal_mulai', '');
        $jamKosongTanggalSelesai = (string) Pengaturan::getValue('jam_kosong_tanggal_selesai', '');

        $totalKelas = Kelas::count();

        return view('dashboard.admin.pengaturan', compact(
            'shiftSenin',
            'shiftJumat',
            'isSeninMaju',
            'seninShiftedMinutes',
            'isJumatMaju',
            'jumatShiftedMinutes',
            'tenggatOpsi',
            'publikRiwayatAktif',
            'eventSekolah',
            'eventSekolahTanggal',
            'eventSekolahJamPulang',
            'jamKosongNama',
            'jamKosongTanggalMulai',
            'jamKosongTanggalSelesai',
            'totalKelas'
        ));
    }

    public function updatePengaturan(Request $request)
    {
        $this->ensureAdminAccess();

        if ($request->input('action_type') === 'publik') {
            $validated = $request->validate([
                'publik_riwayat_aktif' => 'required|boolean',
            ]);

            Pengaturan::setValue('publik_riwayat_aktif', (int) $validated['publik_riwayat_aktif']);

            return redirect()->to(route('admin.pengaturan').'#jurnal-publik')->with('success', 'Pengaturan riwayat jurnal publik berhasil diperbarui.');
        }

        if ($request->input('action_type') === 'event') {
            $validated = $request->validate([
                'event_sekolah' => 'nullable|string|max:120|required_with:event_sekolah_tanggal,event_sekolah_jam_pulang',
                'event_sekolah_tanggal' => 'nullable|date|required_with:event_sekolah_jam_pulang',
                'event_sekolah_jam_pulang' => ['nullable', 'regex:/^(?:[01][0-9]|2[0-3])[:.][0-5][0-9]$/', 'required_with:event_sekolah_tanggal'],
            ], [
                'event_sekolah.required_with' => 'Nama kegiatan wajib diisi jika tanggal atau jam pulang diatur.',
                'event_sekolah_tanggal.required_with' => 'Tanggal kegiatan wajib diisi jika jam pulang khusus diatur.',
                'event_sekolah_jam_pulang.required_with' => 'Jam pulang khusus wajib diisi jika tanggal kegiatan diatur.',
            ]);

            Pengaturan::setValue('event_sekolah', trim((string) ($validated['event_sekolah'] ?? '')));
            Pengaturan::setValue('event_sekolah_tanggal', (string) ($validated['event_sekolah_tanggal'] ?? ''));
            $dismissalTime = str_replace('.', ':', (string) ($validated['event_sekolah_jam_pulang'] ?? ''));
            Pengaturan::setValue('event_sekolah_jam_pulang', $dismissalTime);

            return redirect()->to(route('admin.pengaturan').'#pemajuan-jam')->with('success', 'Pengaturan jam pulang khusus berhasil diperbarui.');
        }

        if ($request->input('action_type') === 'jam_kosong') {
            $validated = $request->validate([
                'jam_kosong_nama' => 'nullable|string|max:120|required_with:jam_kosong_tanggal_mulai,jam_kosong_tanggal_selesai',
                'jam_kosong_tanggal_mulai' => 'nullable|date|required_with:jam_kosong_tanggal_selesai',
                'jam_kosong_tanggal_selesai' => 'nullable|date|after_or_equal:jam_kosong_tanggal_mulai|required_with:jam_kosong_tanggal_mulai',
            ], [
                'jam_kosong_nama.required_with' => 'Nama kegiatan wajib diisi.',
                'jam_kosong_tanggal_mulai.required_with' => 'Tanggal mulai wajib diisi.',
                'jam_kosong_tanggal_selesai.required_with' => 'Tanggal selesai wajib diisi.',
                'jam_kosong_tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama dengan atau setelah tanggal mulai.',
            ]);

            Pengaturan::setValue('jam_kosong_nama', trim((string) ($validated['jam_kosong_nama'] ?? '')));
            Pengaturan::setValue('jam_kosong_tanggal_mulai', (string) ($validated['jam_kosong_tanggal_mulai'] ?? ''));
            Pengaturan::setValue('jam_kosong_tanggal_selesai', (string) ($validated['jam_kosong_tanggal_selesai'] ?? ''));

            return redirect()->to(route('admin.pengaturan').'#pemajuan-jam')->with('success', 'Pengaturan jam kosong seharian berhasil diperbarui.');
        }

        // Pengaturan Kebijakan Tenggat Waktu Pengisian Jurnal
        if ($request->has('tenggat_form') || $request->has('tenggat_opsi') || $request->input('action_type') === 'tenggat') {
            $validated = $request->validate([
                'tenggat_opsi' => 'required|in:terbatas_jam,hari_ini,los',
            ], [
                'tenggat_opsi.required' => 'Pilihan opsi kebijakan tenggat waktu wajib ditentukan.',
                'tenggat_opsi.in' => 'Pilihan opsi kebijakan tidak valid.',
            ]);

            Pengaturan::setValue('tenggat_opsi', $validated['tenggat_opsi']);

            return redirect()->route('admin.pengaturan')->with('success', 'Kebijakan tenggat waktu pengisian jurnal berhasil diperbarui.');
        }

        // Pengaturan Durasi Jam Maju
        $validated = $request->validate([
            'shift_senin_minutes' => 'required|integer|min:5|max:180',
            'shift_jumat_minutes' => 'required|integer|min:5|max:180',
        ], [
            'shift_senin_minutes.required' => 'Menit pemajuan hari Senin wajib diisi.',
            'shift_jumat_minutes.required' => 'Menit pemajuan hari Jum\'at wajib diisi.',
            'shift_senin_minutes.min' => 'Menit minimal adalah 5 menit.',
            'shift_jumat_minutes.min' => 'Menit minimal adalah 5 menit.',
        ]);

        $this->synchronizeActiveShiftDuration('Senin', (int) Pengaturan::getValue('shift_senin_minutes', 40), $validated['shift_senin_minutes']);
        $this->synchronizeActiveShiftDuration('Jumat', (int) Pengaturan::getValue('shift_jumat_minutes', 30), $validated['shift_jumat_minutes']);

        Pengaturan::setValue('shift_senin_minutes', $validated['shift_senin_minutes']);
        Pengaturan::setValue('shift_jumat_minutes', $validated['shift_jumat_minutes']);

        return redirect()->route('admin.pengaturan')->with('success', 'Pengaturan durasi pemajuan jam berhasil disimpan.');
    }

    private function ensureAdminAccess(): void
    {
        abort_unless(Auth::user()?->role === 'admin', 403);
    }

    private function synchronizeActiveShiftDuration(string $hari, int $previousMinutes, int $updatedMinutes): void
    {
        $dayKey = mb_strtolower($hari);
        if ($previousMinutes === $updatedMinutes || ! (bool) Pengaturan::getValue($dayKey.'_is_maju', 0)) {
            return;
        }

        $targetKeyword = $hari === 'Jumat' ? 'pembiasaan' : 'upacara';
        $adjustment = $previousMinutes - $updatedMinutes;

        DB::transaction(function () use ($hari, $targetKeyword, $adjustment): void {
            JadwalPelajaran::where('hari', $hari)
                ->get()
                ->each(function (JadwalPelajaran $lesson) use ($targetKeyword, $adjustment): void {
                    if (str_contains(mb_strtolower($lesson->mapel), $targetKeyword)) {
                        return;
                    }

                    $lesson->update([
                        'jam_mulai' => date('H:i:s', strtotime($lesson->jam_mulai) + ($adjustment * 60)),
                        'jam_selesai' => date('H:i:s', strtotime($lesson->jam_selesai) + ($adjustment * 60)),
                    ]);
                });
        });

        Pengaturan::setValue($dayKey.'_shifted_minutes', $updatedMinutes);
    }

    // IMPORT JADWAL (EXCEL / CSV)
    public function importJadwal(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ], [
            'file.required' => 'Pilih file Excel (.xlsx, .xls) atau .csv terlebih dahulu.',
            'file.mimes' => 'Format file harus berupa Excel (.xlsx, .xls) atau .csv.',
        ]);

        try {
            $import = new JadwalImport;
            Excel::import($import, $request->file('file'));

            return redirect()->route('dashboard.jadwal')->with('success', "Import selesai! {$import->importedCount} jadwal baru ditambahkan dan {$import->updatedCount} jadwal diperbarui.");
        } catch (\Throwable $e) {
            return redirect()->route('dashboard.jadwal')->with('error', 'Gagal memproses file Excel: '.$e->getMessage());
        }
    }

    // DOWNLOAD TEMPLATE JADWAL
    public function downloadTemplateJadwal()
    {
        $filename = 'template_import_jadwal.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['hari', 'kelas', 'mapel', 'guru', 'jam_ke', 'jam_mulai', 'jam_selesai']);
            fputcsv($handle, ['Senin', 'X RPL 1', 'Pemrograman Web', 'Budi Santoso, S.Pd', 1, '07:00', '08:30']);
            fputcsv($handle, ['Senin', 'X RPL 1', 'Matematika', '198005122005011002', 2, '08:30', '10:00']);
            fputcsv($handle, ['Selasa', 'XI RPL 2', 'Basis Data', 'Siti Aminah, M.Pd', 1, '07:00', '08:30']);
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    // =========================================================================
    // 7. REKAP JURNAL MONITORING
    // =========================================================================
    public function rekapJurnal(Request $request, ScheduleTimeService $scheduleTimeService)
    {
        $periode = $request->query('periode', 'harian');
        $tanggal = $request->query('tanggal', now()->toDateString());
        $bulan = min(12, max(1, (int) $request->query('bulan', now()->month)));
        $tahun = min(2100, max(2026, (int) $request->query('tahun', max(2026, now()->year))));
        $guruId = $request->query('guru_id');
        $kelasId = $request->query('kelas_id');
        $kehadiran = $request->query('kehadiran', 'all');
        $validasi = $request->query('validasi', 'all');
        $search = $request->query('search', '');
        $keterlambatan = $request->query('keterlambatan', 'all');
        $tab = $request->query('tab', 'jurnal');

        // Jika user sengaja memilih bulan dan tidak mengubah tanggal, atau klik filter bulanan
        if ($request->has('bulan') && ! $request->has('tanggal')) {
            $periode = 'bulanan';
        }

        // Hitung distribusi hari dalam bulan terpilih (untuk menghitung sesi terjadwal & sesi kosong bulanan)
        $daysCount = ['Senin' => 0, 'Selasa' => 0, 'Rabu' => 0, 'Kamis' => 0, 'Jumat' => 0, 'Sabtu' => 0];
        try {
            $startOfMonth = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
            $endOfMonth = $startOfMonth->copy()->endOfMonth();
            for ($d = $startOfMonth->copy(); $d->lte($endOfMonth); $d->addDay()) {
                $h = match ($d->dayOfWeek) {
                    1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', default => null
                };
                if ($h && isset($daysCount[$h])) {
                    $daysCount[$h]++;
                }
            }
        } catch (\Exception $e) {
            $startOfMonth = now()->startOfMonth();
            $endOfMonth = now()->endOfMonth();
        }

        // Info Hari untuk tanggal terpilih
        try {
            $dayOfWeek = Carbon::parse($tanggal)->dayOfWeek;
        } catch (\Exception $e) {
            $tanggal = now()->toDateString();
            $dayOfWeek = Carbon::parse($tanggal)->dayOfWeek;
        }
        $namaHari = match ($dayOfWeek) {
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            default => 'Minggu',
        };

        // Query Jurnal Mengajar (Riwayat Mengajar yang sudah lalu)
        $query = JurnalMengajar::with(['kelas', 'guru', 'mapel']);

        $hasExplicitTanggal = $request->filled('tanggal');
        $hasSearch = $request->filled('search');

        if ($periode === 'bulanan') {
            $query->whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan);
        } elseif ($hasExplicitTanggal) {
            $query->whereDate('tanggal', $tanggal);
        } elseif (! $hasSearch) {
            $query->whereDate('tanggal', $tanggal);
        }

        if ($guruId && $guruId !== 'all') {
            $query->where('id_user', $guruId);
        }

        if ($kelasId && $kelasId !== 'all') {
            $query->where('id_kelas', $kelasId);
        }

        if ($keterlambatan === 'terlambat') {
            $query->where('menit_keterlambatan', '>', 0);
        } elseif ($keterlambatan === 'tepat_waktu') {
            $query->where('menit_keterlambatan', '<=', 0);
        }

        if ($request->filled('search')) {
            $term = trim($request->query('search'));
            $query->where(function ($q) use ($term) {
                $q->where('materi', 'like', "%{$term}%")
                    ->orWhere('tanggal', 'like', "%{$term}%")
                    ->orWhereHas('guru', fn ($g) => $g->where('name', 'like', "%{$term}%"))
                    ->orWhereHas('mapel', fn ($m) => $m->where('nama_mapel', 'like', "%{$term}%"))
                    ->orWhereHas('kelas', fn ($k) => $k->where('nama_kelas', 'like', "%{$term}%"));
            });
        }

        $allJurnals = (clone $query)->orderBy('tanggal', 'desc')->orderBy('jam_ke', 'asc')->get();
        $jurnals = $allJurnals;

        $kelases = Kelas::orderBy('nama_kelas', 'asc')->get();
        $totalKelas = $kelases->count();
        $gurus = User::where('role', 'guru')->orderBy('name', 'asc')->get();

        // Jadwal yang melewati jam pulang event pada tanggal tertentu tidak dihitung.
        $jadwalQuery = JadwalPelajaran::query()->where('jam_ke', '>', 0);
        if ($guruId && $guruId !== 'all') {
            $jadwalQuery->where('id_user', $guruId);
        }
        if ($kelasId && $kelasId !== 'all') {
            $jadwalQuery->where('id_kelas', $kelasId);
        }

        $jadwalRows = $jadwalQuery->get();
        $reportDates = [];
        if ($periode === 'bulanan') {
            for ($day = $startOfMonth->copy(); $day->lte($endOfMonth); $day->addDay()) {
                $reportDates[] = $day->toDateString();
            }
        } else {
            $reportDates[] = $tanggal;
        }

        $namaHariByNumber = [0 => 'Minggu', 1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'];
        $scheduledByTeacher = [];
        $totalTerjadwal = 0;
        foreach ($reportDates as $reportDate) {
            $reportDay = $namaHariByNumber[Carbon::parse($reportDate)->dayOfWeek];
            foreach ($jadwalRows->where('hari', $reportDay) as $jadwal) {
                if (! $scheduleTimeService->isScheduleEndApplicableOnDate($reportDate, (string) $jadwal->jam_selesai)) {
                    continue;
                }

                $totalTerjadwal++;
                $scheduledByTeacher[$jadwal->id_user] = ($scheduledByTeacher[$jadwal->id_user] ?? 0) + 1;
            }
        }

        $kelasLapor = $periode === 'bulanan'
            ? JurnalMengajar::whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan)->distinct('id_kelas')->count('id_kelas')
            : JurnalMengajar::whereDate('tanggal', $tanggal)->distinct('id_kelas')->count('id_kelas');
        $guruTerlambat = $periode === 'bulanan'
            ? JurnalMengajar::whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan)->where('menit_keterlambatan', '>', 0)->count()
            : JurnalMengajar::whereDate('tanggal', $tanggal)->where('menit_keterlambatan', '>', 0)->count();

        $totalTerisi = $allJurnals->count();
        $totalKosong = max(0, $totalTerjadwal - $totalTerisi);
        $guruHadir = $totalTerisi;
        $guruTidakHadir = 0;
        $guruAbsen = 0;

        // 1. Ambil jurnal lengkap untuk masing-masing guru pada periode terpilih
        $guruJurnalsQuery = JurnalMengajar::with(['kelas', 'mapel', 'guru']);
        if ($periode === 'bulanan') {
            $guruJurnalsQuery->whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan);
        } elseif ($hasExplicitTanggal) {
            $guruJurnalsQuery->whereDate('tanggal', $tanggal);
        } elseif (! $hasSearch) {
            $guruJurnalsQuery->whereDate('tanggal', $tanggal);
        }

        if ($guruId && $guruId !== 'all') {
            $guruJurnalsQuery->where('id_user', $guruId);
        }

        if ($request->filled('search')) {
            $term = trim($request->query('search'));
            $guruJurnalsQuery->where(function ($q) use ($term) {
                $q->where('materi', 'like', "%{$term}%")
                    ->orWhereHas('guru', fn ($g) => $g->where('name', 'like', "%{$term}%"))
                    ->orWhereHas('mapel', fn ($m) => $m->where('nama_mapel', 'like', "%{$term}%"))
                    ->orWhereHas('kelas', fn ($k) => $k->where('nama_kelas', 'like', "%{$term}%"));
            });
        }

        $allGuruJurnalsGrouped = $guruJurnalsQuery->orderBy('tanggal', 'desc')->orderBy('jam_ke', 'asc')->get()->groupBy('id_user');

        $rekapGuru = $gurus->map(function ($g) use ($scheduledByTeacher, $allGuruJurnalsGrouped) {
            $terjadwal = $scheduledByTeacher[$g->id] ?? 0;
            $daftarJurnal = $allGuruJurnalsGrouped->get($g->id, collect());
            $terisi = $daftarJurnal->count();
            $terakhir = $daftarJurnal->first()?->tanggal;
            $kosong = max(0, $terjadwal - $terisi);

            return (object) [
                'id' => $g->id,
                'name' => $g->name,
                'nip' => $g->nip,
                'terjadwal' => $terjadwal,
                'terisi' => $terisi,
                'kosong' => $kosong,
                'terakhir' => $terakhir,
                'persentase' => $terjadwal > 0 ? min(100, round(($terisi / $terjadwal) * 100)) : ($terisi > 0 ? 100 : 0),
                'jurnals' => $daftarJurnal,
            ];
        });

        if ($guruId && $guruId !== 'all') {
            $rekapGuru = $rekapGuru->where('id', $guruId);
        }

        if ($request->filled('search')) {
            $termLower = strtolower(trim($request->query('search')));
            $rekapGuru = $rekapGuru->filter(function ($item) use ($termLower) {
                return str_contains(strtolower($item->name), $termLower)
                    || str_contains(strtolower($item->nip ?? ''), $termLower)
                    || $item->jurnals->isNotEmpty();
            });
        }

        // 2. Rekapitulasi Per Kelas: Menampilkan Jadwal Pelajaran Kelas dengan Status Jurnal per Sesi
        $classesToInspect = Kelas::query()
            ->when($kelasId && $kelasId !== 'all', fn ($q) => $q->where('id_kelas', $kelasId))
            ->orderBy('nama_kelas')
            ->get();

        $jadwalKelasList = JadwalPelajaran::with(['guru', 'mapelItem'])
            ->whereIn('id_kelas', $classesToInspect->pluck('id_kelas'))
            ->where('hari', $namaHari)
            ->where('jam_ke', '>', 0)
            ->orderBy('jam_ke')
            ->get()
            ->groupBy('id_kelas');

        $jurnalKelasList = JurnalMengajar::with(['guru', 'mapel'])
            ->whereIn('id_kelas', $classesToInspect->pluck('id_kelas'))
            ->whereDate('tanggal', $tanggal)
            ->get()
            ->groupBy('id_kelas');

        $teacherAbsences = KetidakhadiranGuru::whereDate('tanggal', $tanggal)->get()->keyBy('user_id');

        $rekapJadwalKelas = $classesToInspect->map(function ($kelas) use ($jadwalKelasList, $jurnalKelasList, $teacherAbsences, $scheduleTimeService, $tanggal) {
            $jadwals = $jadwalKelasList->get($kelas->id_kelas, collect());
            $jurnals = $jurnalKelasList->get($kelas->id_kelas, collect());

            $applicableJadwals = $jadwals->filter(function ($jadwal) use ($scheduleTimeService, $tanggal) {
                return $scheduleTimeService->isScheduleEndApplicableOnDate($tanggal, (string) $jadwal->jam_selesai);
            });

            $sesiItems = $applicableJadwals->map(function ($jadwal) use ($jurnals, $teacherAbsences, $tanggal) {
                $jurnal = $jurnals->first(function ($j) use ($jadwal) {
                    return $j->jam_ke == $jadwal->jam_ke
                        || ($jadwal->id_mapel && $j->id_mapel == $jadwal->id_mapel && $j->jam_ke <= $jadwal->jam_ke && ($j->jam_selesai ?? $j->jam_ke) >= $jadwal->jam_ke);
                });

                $teacherAbsence = $jadwal->id_user ? $teacherAbsences->get($jadwal->id_user) : null;
                $statusInfo = app(JournalStatusService::class)->determineStatus($tanggal, $jurnal, $teacherAbsence);

                return (object) [
                    'jam_ke' => $jadwal->jam_ke,
                    'jam_selesai' => $jadwal->jam_ke_selesai ?? $jadwal->jam_ke,
                    'jam_ke_formatted' => $jadwal->jam_ke_formatted ?? "Jam ke-{$jadwal->jam_ke}",
                    'waktu_mulai' => $jadwal->jam_mulai,
                    'waktu_selesai' => $jadwal->jam_selesai,
                    'mapel' => $jadwal->mapelItem?->nama_mapel ?? $jadwal->mapel ?? 'Mata Pelajaran',
                    'guru' => $jadwal->guru?->name ?? 'Guru Pengampu',
                    'guru_nip' => $jadwal->guru?->nip,
                    'is_terisi' => $jurnal !== null,
                    'jurnal' => $jurnal,
                    'status_info' => $statusInfo,
                ];
            });

            $totalSesi = $sesiItems->count();
            $totalTerisi = $sesiItems->where('is_terisi', true)->count();
            $totalKosong = $totalSesi - $totalTerisi;

            return (object) [
                'id_kelas' => $kelas->id_kelas,
                'nama_kelas' => $kelas->nama_kelas,
                'wali_kelas' => $kelas->wali_kelas ?? '-',
                'total_sesi' => $totalSesi,
                'total_terisi' => $totalTerisi,
                'total_kosong' => $totalKosong,
                'sesi_items' => $sesiItems,
            ];
        });

        $rekapKelas = $this->buildClassAttendanceSummary($periode, $tanggal, $bulan, $tahun, $kelasId);

        // Notif Approval Dispensasi Siswa
        $requestDispensasi = Dispensasi::with('siswa.kelas')
            ->whereDate('tanggal', $tanggal)
            ->where('status_akhir', 'Pending')
            ->get();

        $countSemua = $allJurnals->count();
        $countGuruAbsen = 0;
        $countBelumValidasi = 0;
        $menungguValidasi = 0;

        $eventDismissalTime = $scheduleTimeService->dismissalTimeForDate($tanggal);
        $eventSchoolName = (string) Pengaturan::getValue('event_sekolah', '');
        $isJamKosong = $scheduleTimeService->isAllDayEmptyForDate($tanggal);
        $jamKosongNama = (string) Pengaturan::getValue('jam_kosong_nama', '');

        return view('dashboard.admin.rekap-jurnal', compact(
            'jurnals',
            'kelases',
            'gurus',
            'rekapGuru',
            'rekapKelas',
            'rekapJadwalKelas',
            'periode',
            'tanggal',
            'bulan',
            'tahun',
            'guruId',
            'kelasId',
            'kehadiran',
            'validasi',
            'search',
            'keterlambatan',
            'totalKelas',
            'kelasLapor',
            'totalTerjadwal',
            'totalTerisi',
            'totalKosong',
            'guruHadir',
            'guruTidakHadir',
            'guruTerlambat',
            'guruAbsen',
            'menungguValidasi',
            'tab',
            'countSemua',
            'countBelumValidasi',
            'countGuruAbsen',
            'namaHari',
            'requestDispensasi',
            'eventDismissalTime',
            'eventSchoolName',
            'isJamKosong',
            'jamKosongNama'
        ));
    }

    private function buildClassAttendanceSummary(string $periode, string $tanggal, int $bulan, int $tahun, mixed $kelasId): Collection
    {
        $journalQuery = Absensi::query()
            ->with(['siswa', 'jurnal'])
            ->whereIn('status', ['Sakit', 'Izin', 'Alpa', 'D'])
            ->whereHas('jurnal', function ($query) use ($periode, $tanggal, $bulan, $tahun, $kelasId): void {
                if ($periode === 'bulanan') {
                    $query->whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan);
                } else {
                    $query->whereDate('tanggal', $tanggal);
                }
                if ($kelasId && $kelasId !== 'all') {
                    $query->where('id_kelas', $kelasId);
                }
            });

        $piketQuery = PiketKehadiranSiswa::query()
            ->with(['siswa', 'kelas'])
            ->whereIn('status', ['S', 'Sakit', 'I', 'Izin', 'A', 'Alpa', 'Alfa', 'D', 'Dispensasi']);
        if ($periode === 'bulanan') {
            $journalQuery->whereHas('jurnal', fn ($query) => $query->whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan));
            $piketQuery->whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan);
        } else {
            $piketQuery->whereDate('tanggal', $tanggal);
        }
        if ($kelasId && $kelasId !== 'all') {
            $piketQuery->where('kelas_id', $kelasId);
        }

        $absentRecords = collect();
        $normalise = static function (?string $status): ?string {
            return match (strtoupper(trim((string) $status))) {
                'S', 'SAKIT' => 'Sakit',
                'I', 'IZIN' => 'Izin',
                'A', 'ALPA', 'ALFA' => 'Alpa',
                'D', 'DISPENSASI' => 'Dispensasi',
                default => null,
            };
        };

        foreach ($piketQuery->get() as $record) {
            $status = $normalise($record->status);
            if ($status === null || ! $record->siswa) {
                continue;
            }
            $key = $record->tanggal->toDateString().':'.$record->siswa_id;
            $absentRecords->put($key, [
                'tanggal' => $record->tanggal->toDateString(),
                'kelas_id' => $record->kelas_id,
                'kelas' => $record->kelas?->nama_kelas ?? '-',
                'siswa' => $record->siswa->nama,
                'nis' => $record->siswa->nis ?? '-',
                'status' => $status,
                'catatan' => $record->catatan ?: 'Tidak ada keterangan.',
            ]);
        }

        $journalAbsences = $journalQuery->orderByDesc('id')->get()
            ->filter(fn (Absensi $record): bool => $record->jurnal !== null && $record->siswa !== null)
            ->groupBy(fn (Absensi $record): string => $record->jurnal->tanggal.':'.$record->id_siswa);
        foreach ($journalAbsences as $key => $records) {
            if ($absentRecords->has($key)) {
                continue;
            }
            $record = $records->first();
            $status = $normalise($record->status);
            if ($status === null) {
                continue;
            }
            $absentRecords->put($key, [
                'tanggal' => (string) $record->jurnal->tanggal,
                'kelas_id' => $record->jurnal->id_kelas,
                'kelas' => $record->jurnal->kelas?->nama_kelas ?? '-',
                'siswa' => $record->siswa->nama,
                'nis' => $record->siswa->nis ?? '-',
                'status' => $status,
                'catatan' => $record->catatan ?: 'Tidak ada keterangan.',
            ]);
        }

        $classes = Kelas::query()
            ->when($kelasId && $kelasId !== 'all', fn ($query) => $query->where('id_kelas', $kelasId))
            ->orderBy('nama_kelas')
            ->get();

        return $classes->map(function (Kelas $kelas) use ($absentRecords) {
            $records = $absentRecords->filter(fn (array $record): bool => (int) $record['kelas_id'] === (int) $kelas->id_kelas)
                ->sortBy([['tanggal', 'desc'], ['siswa', 'asc']])
                ->values();

            return (object) [
                'id' => $kelas->id_kelas,
                'nama' => $kelas->nama_kelas,
                'sakit' => $records->where('status', 'Sakit')->count(),
                'izin' => $records->where('status', 'Izin')->count(),
                'alpa' => $records->where('status', 'Alpa')->count(),
                'dispensasi' => $records->where('status', 'Dispensasi')->count(),
                'records' => $records,
            ];
        });
    }

    public function updatePenugasanPiket(Request $request)
    {
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $validated = $request->validate([
            'penugasan' => ['nullable', 'array'],
            'penugasan.*.pagi.guru' => ['nullable', 'array', 'max:3'],
            'penugasan.*.pagi.guru.*' => ['nullable', 'integer', 'distinct', 'exists:users,id'],
            'penugasan.*.pagi.koordinator' => ['nullable', 'integer', 'exists:users,id'],
            'penugasan.*.siang.guru' => ['nullable', 'array', 'max:3'],
            'penugasan.*.siang.guru.*' => ['nullable', 'integer', 'distinct', 'exists:users,id'],
            'penugasan.*.siang.koordinator' => ['nullable', 'integer', 'exists:users,id'],
            'penugasan.*.waka' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        foreach ($days as $day) {
            $penugasanHari = $validated['penugasan'][$day] ?? [];

            JadwalPiket::query()
                ->where('hari', $day)
                ->whereNull('tanggal')
                ->whereIn('tipe', ['guru', 'koordinator', 'waka'])
                ->delete();

            $this->simpanPenugasanPiketMingguan(
                $day,
                $penugasanHari['pagi']['guru'] ?? [],
                $penugasanHari['pagi']['koordinator'] ?? null,
                $penugasanHari['siang']['guru'] ?? [],
                $penugasanHari['siang']['koordinator'] ?? null,
                $penugasanHari['waka'] ?? null,
            );
        }

        return redirect()->route('dashboard.jadwal-penugasan')->with('success', 'Penugasan sesi pagi, sesi siang, koordinator, dan Waka berhasil diperbarui!');
    }

    /**
     * @param  array<int, int|string|null>  $guruPagiIds
     * @param  array<int, int|string|null>  $guruSiangIds
     */
    private function simpanPenugasanPiketMingguan(
        string $day,
        array $guruPagiIds,
        int|string|null $koordinatorPagiId,
        array $guruSiangIds,
        int|string|null $koordinatorSiangId,
        int|string|null $wakaId,
    ): void {
        $sesi = [
            ['guru' => $guruPagiIds, 'koordinator' => $koordinatorPagiId, 'shift' => 1, 'mulai' => '07:00:00', 'selesai' => '11:00:00'],
            ['guru' => $guruSiangIds, 'koordinator' => $koordinatorSiangId, 'shift' => 2, 'mulai' => '11:00:00', 'selesai' => '15:00:00'],
        ];

        foreach ($sesi as $pengaturanSesi) {
            foreach (array_unique(array_filter($pengaturanSesi['guru'])) as $guruId) {
                JadwalPiket::create([
                    'user_id' => $guruId,
                    'hari' => $day,
                    'tipe' => 'guru',
                    'shift' => $pengaturanSesi['shift'],
                    'jam_mulai' => $pengaturanSesi['mulai'],
                    'jam_selesai' => $pengaturanSesi['selesai'],
                    'keterangan' => "Petugas Piket {$day}",
                ]);
            }

            if ($pengaturanSesi['koordinator']) {
                JadwalPiket::create([
                    'user_id' => $pengaturanSesi['koordinator'],
                    'hari' => $day,
                    'tipe' => 'koordinator',
                    'shift' => $pengaturanSesi['shift'],
                    'jam_mulai' => $pengaturanSesi['mulai'],
                    'jam_selesai' => $pengaturanSesi['selesai'],
                    'keterangan' => "Koordinator Piket {$day}",
                ]);
            }
        }

        if ($wakaId) {
            JadwalPiket::create([
                'user_id' => $wakaId,
                'hari' => $day,
                'tipe' => 'waka',
                'shift' => 1,
                'jam_mulai' => '07:00:00',
                'jam_selesai' => '15:00:00',
                'keterangan' => "Piket Waka {$day}",
            ]);
        }
    }

    public function catatanJurnal(Request $request)
    {
        return redirect()->route('dashboard.rekap-jurnal', $request->query());
    }

    // =========================================================================
    // 8. MANAJEMEN USER (ADMIN, GURU, PENGURUS KELAS)
    // =========================================================================
    public function user(Request $request)
    {
        $search = $request->query('search');
        $role = $request->query('role');

        $query = User::with('mapel')->whereIn('role', ['admin', 'pengurus_kelas', 'guru']);

        if ($role) {
            $query->where('role', $role);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%")
                    ->orWhere('no_hp', 'like', "%{$search}%")
                    ->orWhereHas('mapel', function ($m) use ($search) {
                        $m->where('nama_mapel', 'like', "%{$search}%");
                    })
                    ->orWhereIn('id', function ($sub) use ($search) {
                        $sub->select('id_user')
                            ->from('jadwal_pelajarans')
                            ->where('mapel', 'like', "%{$search}%");
                    });
            });
        }

        $users = $query->orderBy('name')->get();

        $jadwalRecords = JadwalPelajaran::whereNotNull('id_user')
            ->whereNotNull('mapel')
            ->where('mapel', 'not like', '%istirahat%')
            ->where('mapel', 'not like', '%upacara%')
            ->where('mapel', 'not like', '%pembiasaan%')
            ->select('id_user', 'mapel')
            ->distinct()
            ->get();

        $allMapelsByUser = [];
        foreach ($jadwalRecords as $jr) {
            if ($jr->mapel) {
                $allMapelsByUser[$jr->id_user][$jr->mapel] = true;
            }
        }

        $usersForJs = $users->map(function ($u) use ($allMapelsByUser) {
            $roleLabel = match ($u->role) {
                'admin' => 'Admin',
                'guru' => 'Guru Pengajar',
                'pengurus_kelas' => 'Sekretaris Kelas',
                default => ucfirst($u->role),
            };
            $roleClass = match ($u->role) {
                'admin' => 'bg-purple-50 text-purple-700 border border-purple-200',
                'guru' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                'pengurus_kelas' => 'bg-sky-50 text-sky-700 border border-sky-200',
                default => 'bg-gray-50 text-gray-700 border border-gray-200',
            };

            $mapelList = isset($allMapelsByUser[$u->id]) ? array_keys($allMapelsByUser[$u->id]) : [];
            if ($u->mapel && ! in_array($u->mapel->nama_mapel, $mapelList)) {
                $mapelList[] = $u->mapel->nama_mapel;
            }
            $namaMapel = ! empty($mapelList) ? implode(', ', $mapelList) : (optional($u->mapel)->nama_mapel ?? '');

            return [
                'id' => $u->id,
                'name' => $u->name,
                'username' => $u->username ?? '',
                'identifier' => $u->username ?? $u->nip ?? '-',
                'nip' => $u->nip ?? '',
                'role' => $roleLabel,
                'raw_role' => $u->role,
                'roleClass' => $roleClass,
                'phone' => $u->no_hp ?? '-',
                'status' => 'aktif',
                'nama_kelas' => '',
                'nama_mapel' => $namaMapel,
                'id_kelas' => '',
                'mapel_id' => $u->mapel_id ?? '',
            ];
        });

        $kelasList = Kelas::orderBy('nama_kelas')->get();
        $kelases = $kelasList;
        $mapels = Mapel::orderBy('nama_mapel')->get();

        $usersForJs = $users->map(function ($u) use ($allMapelsByUser, $kelasList) {
            $roleLabel = match ($u->role) {
                'admin' => 'Admin',
                'guru' => 'Guru Pengajar',
                'pengurus_kelas' => 'Sekretaris Kelas',
                default => ucfirst($u->role),
            };
            $roleClass = match ($u->role) {
                'admin' => 'bg-purple-50 text-purple-700 border border-purple-200',
                'guru' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                'pengurus_kelas' => 'bg-sky-50 text-sky-700 border border-sky-200',
                default => 'bg-gray-50 text-gray-700 border border-gray-200',
            };

            $mapelList = isset($allMapelsByUser[$u->id]) ? array_keys($allMapelsByUser[$u->id]) : [];
            if ($u->mapel && ! in_array($u->mapel->nama_mapel, $mapelList)) {
                $mapelList[] = $u->mapel->nama_mapel;
            }
            $namaMapel = ! empty($mapelList) ? implode(', ', $mapelList) : (optional($u->mapel)->nama_mapel ?? '');

            // Deteksi nama kelas jika role pengurus_kelas
            $detectedNamaKelas = '';
            $detectedIdKelas = '';
            if ($u->role === 'pengurus_kelas') {
                $cleanUser = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $u->username ?? ''));
                $cleanName = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $u->name ?? ''));
                $matchedKelas = $kelasList->first(function ($k) use ($cleanUser, $cleanName) {
                    $cleanKelas = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $k->nama_kelas));

                    return str_contains($cleanUser, $cleanKelas) || str_contains($cleanName, $cleanKelas);
                });
                if ($matchedKelas) {
                    $detectedNamaKelas = $matchedKelas->nama_kelas;
                    $detectedIdKelas = $matchedKelas->id_kelas;
                }
            }

            return [
                'id' => $u->id,
                'name' => $u->name,
                'username' => $u->username ?? '',
                'identifier' => $u->username ?? $u->nip ?? '-',
                'nip' => $u->nip ?? '',
                'role' => $roleLabel,
                'raw_role' => $u->role,
                'roleClass' => $roleClass,
                'phone' => $u->no_hp ?? '-',
                'no_hp' => $u->no_hp ?? '',
                'status' => 'aktif',
                'nama_kelas' => $detectedNamaKelas,
                'nama_mapel' => $namaMapel,
                'id_kelas' => $detectedIdKelas,
                'mapel_id' => $u->mapel_id ?? '',
            ];
        });

        return view('dashboard.admin.manajemen-user', compact('users', 'usersForJs', 'kelasList', 'kelases', 'mapels', 'search', 'role'));
    }

    public function storeUser(Request $request)
    {
        // Dukung input name lama maupun standar
        if (! $request->filled('name') && $request->filled('nama')) {
            $request->merge(['name' => $request->input('nama')]);
        }
        if (! $request->filled('username') && $request->filled('identifier')) {
            $request->merge(['username' => $request->input('identifier')]);
        }
        if (! $request->filled('no_hp') && $request->filled('phone')) {
            $request->merge(['no_hp' => $request->input('phone')]);
        }
        if ($request->input('role') === 'sekretaris') {
            $request->merge(['role' => 'pengurus_kelas']);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username',
            'nip' => 'nullable|string|max:30|unique:users,nip',
            'role' => 'required|in:admin,pengurus_kelas,guru',
            'password' => 'required|string|min:6',
            'no_hp' => 'nullable|string|max:25',
            'mapel_id' => 'nullable|exists:mapels,id',
        ], [
            'name.required' => 'Nama lengkap pengguna wajib diisi.',
            'username.required' => 'Username login wajib diisi.',
            'username.unique' => 'Username ini sudah digunakan.',
            'nip.unique' => 'NIP ini sudah terdaftar.',
            'password.required' => 'Password awal wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'nip' => $validated['nip'] ?? null,
            'role' => $validated['role'],
            'no_hp' => $validated['no_hp'] ?? null,
            'mapel_id' => $validated['mapel_id'] ?? null,
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.manajemen-user')->with('success', 'Akun '.$validated['name'].' berhasil dibuat!');
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if (! $request->filled('no_hp') && $request->filled('phone')) {
            $request->merge(['no_hp' => $request->input('phone')]);
        }
        if ($request->input('role') === 'sekretaris') {
            $request->merge(['role' => 'pengurus_kelas']);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username,'.$user->id,
            'nip' => 'nullable|string|max:30|unique:users,nip,'.$user->id,
            'role' => 'required|in:admin,pengurus_kelas,guru',
            'password' => 'nullable|string|min:6',
            'no_hp' => 'nullable|string|max:25',
            'mapel_id' => 'nullable|exists:mapels,id',
        ], [
            'name.required' => 'Nama lengkap pengguna wajib diisi.',
            'username.required' => 'Username login wajib diisi.',
            'username.unique' => 'Username ini sudah digunakan oleh akun lain.',
            'nip.unique' => 'NIP ini sudah terdaftar pada akun lain.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'username' => $validated['username'],
            'nip' => $validated['nip'] ?? null,
            'role' => $validated['role'],
            'no_hp' => $validated['no_hp'] ?? null,
            'mapel_id' => $validated['mapel_id'] ?? null,
        ];

        if (! empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()->route('admin.manajemen-user')->with('success', 'Data akun '.$user->name.' berhasil diperbarui!');
    }

    public function destroyUser($id)
    {
        $user = User::findOrFail($id);

        // Proteksi akun administrator utama & akun aktif
        if ($user->id === 1 || strtolower($user->username ?? '') === 'admin1' || strtolower($user->username ?? '') === 'admin') {
            return redirect()->route('admin.manajemen-user')->withErrors(['Akun Administrator utama tidak boleh dihapus demi keamanan sistem!']);
        }

        if (Auth::id() === $user->id) {
            return redirect()->route('admin.manajemen-user')->withErrors(['Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif digunakan!']);
        }

        $name = $user->name;

        try {
            $user->delete();
        } catch (\Exception $e) {
            return redirect()->route('admin.manajemen-user')->withErrors([
                "Akun {$name} tidak dapat dihapus karena masih terkait dengan data jadwal pelajaran, jurnal mengajar, atau riwayat KBM.",
            ]);
        }

        return redirect()->route('admin.manajemen-user')->with('success', 'Akun '.$name.' berhasil dihapus dari sistem!');
    }

    // =========================================================================
    // 9. LAPORAN GANTI PASSWORD (NOTIFIKASI NAVBAR)
    // =========================================================================
    public function terimaResetPassword(Request $request, $id, WhatsAppService $whatsAppService)
    {
        $request->validate([
            'password_baru' => 'required|min:4',
            'catatan' => 'nullable|string|max:500',
        ], [
            'password_baru.required' => 'Password baru wajib diisi.',
            'password_baru.min' => 'Password baru minimal 4 karakter.',
        ]);

        $laporan = PasswordResetRequest::findOrFail($id);
        $user = $laporan->user ?? User::where('username', $laporan->username)->first();

        if (! $user) {
            return back()->with('error', 'Akun pengguna terkait laporan ini tidak ditemukan di database.');
        }

        // Update password user
        $user->update([
            'password' => Hash::make($request->password_baru),
        ]);

        // Tandai status laporan sebagai disetujui
        $laporan->update([
            'status' => 'disetujui',
            'catatan_admin' => $request->catatan ?: ('Password berhasil diubah oleh Admin pada '.now()->translatedFormat('d M Y H:i')),
            'handled_by' => Auth::id(),
            'handled_at' => now(),
        ]);

        // Buat template pesan konfirmasi reset password untuk pengguna
        $templatePesan = $whatsAppService->buildPasswordResetApprovedMessage(
            $user->name,
            $user->username,
            $request->password_baru
        );

        $targetNoHp = $laporan->no_hp ?: $user->no_hp;
        $waSendUrl = null;

        if ($targetNoHp) {
            // Coba kirim via gateway WhatsApp jika gateway aktif
            $whatsAppService->sendMessage($targetNoHp, $templatePesan);

            // Tautan langsung untuk admin mengirim via WhatsApp
            $waSendUrl = $whatsAppService->formatWhatsAppUrl($targetNoHp, $templatePesan);
        }

        return back()
            ->with('success', "Password untuk akun {$user->name} ({$user->username}) berhasil diubah menjadi '{$request->password_baru}'!")
            ->with('pesan_wa_reset', $templatePesan)
            ->with('wa_send_url', $waSendUrl)
            ->with('wa_target_nama', $user->name)
            ->with('wa_target_phone', $targetNoHp);
    }

    public function tolakResetPassword(Request $request, $id)
    {
        $laporan = PasswordResetRequest::findOrFail($id);

        $laporan->update([
            'status' => 'ditolak',
            'catatan_admin' => $request->catatan ?: 'Permohonan ganti password ditolak oleh Admin.',
            'handled_by' => Auth::id(),
            'handled_at' => now(),
        ]);

        return back()->with('success', "Permohonan ganti password dari {$laporan->nama} telah ditolak.");
    }
}
