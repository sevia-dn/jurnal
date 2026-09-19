<?php

namespace App\Http\Controllers;

use App\Imports\GuruImport;
use App\Imports\JadwalImport;
use App\Models\JadwalPelajaran;
use App\Models\JadwalPiket;
use App\Models\JurnalMengajar;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Pengaturan;
use App\Models\Siswa;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
                'waktu' => Carbon::parse($j->tanggal)->translatedFormat('d M Y') . ', Jam ke-' . ($j->jam_ke ?? '1'),
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
            if ($u->mapel && !in_array($u->mapel->nama_mapel, $mapelList)) {
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

        if (!empty($validated['nip'])) {
            $existing = User::where('nip', $validated['nip'])->first();
            if ($existing) {
                return back()->withErrors(['nip' => 'NIP sudah terdaftar dalam sistem.'])->withInput();
            }
        }

        $baseUsername = !empty($validated['nip']) ? $validated['nip'] : Str::slug($validated['nama'], '_');
        $username = $baseUsername;
        $counter = 1;
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . '_' . $counter;
            $counter++;
        }

        $defaultPassword = !empty($validated['nip']) ? $validated['nip'] : 'guru123';

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
            'nip' => 'nullable|string|max:30|unique:users,nip,' . $user->id,
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

        return redirect()->route('dashboard.guru')->with('success', 'Data guru ' . $user->name . ' berhasil diperbarui!');
    }

    public function destroyGuru($id)
    {
        $user = User::where('role', 'guru')->findOrFail($id);
        $name = $user->name;
        $user->delete();

        return redirect()->route('dashboard.guru')->with('success', 'Data guru ' . $name . ' berhasil dihapus!');
    }

    // BATCH DELETE GURU
    public function batchDeleteGuru(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids) || !is_array($ids)) {
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

        if (empty($ids) || !is_array($ids)) {
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
            $import = new GuruImport();
            Excel::import($import, $request->file('file'));

            return redirect()->route('dashboard.guru')->with('success', "Import selesai! {$import->importedCount} data guru baru ditambahkan dan {$import->updatedCount} data diperbarui.");
        } catch (\Throwable $e) {
            return redirect()->route('dashboard.guru')->with('error', 'Gagal memproses file Excel: ' . $e->getMessage());
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
        if (!empty($waliInput)) {
            $guru = User::where('role', 'guru')
                ->where(function($q) use ($waliInput) {
                    $q->where('name', $waliInput)
                      ->orWhere('nip', $waliInput);
                })->first();

            if (!$guru) {
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
        if (!empty($waliInput)) {
            $guru = User::where('role', 'guru')
                ->where(function($q) use ($waliInput) {
                    $q->where('name', $waliInput)
                      ->orWhere('nip', $waliInput);
                })->first();

            if (!$guru) {
                return back()->withErrors(['wali_kelas' => 'Guru tidak ada / tidak terdaftar di data guru sekolah.'])->withInput();
            }
            $waliName = $guru->name;
        }

        $request->merge(['wali_kelas' => $waliName]);

        $validated = $request->validate([
            'nama_kelas' => 'required|string|max:20|unique:kelas,nama_kelas,' . $kelas->id_kelas . ',id_kelas',
            'wali_kelas' => 'nullable|string|max:100',
            'jumlah_siswa' => 'nullable|integer|min:0',
        ]);

        $kelas->update([
            'nama_kelas' => $validated['nama_kelas'],
            'wali_kelas' => $validated['wali_kelas'] ?? null,
            'jumlah_siswa' => $validated['jumlah_siswa'] ?? 0,
        ]);

        return redirect()->route('dashboard.kelas')->with('success', 'Data kelas ' . $kelas->nama_kelas . ' berhasil diperbarui!');
    }

    public function destroyKelas($id)
    {
        $kelas = Kelas::findOrFail($id);
        $nama = $kelas->nama_kelas;
        $kelas->delete();

        return redirect()->route('dashboard.kelas')->with('success', 'Kelas ' . $nama . ' berhasil dihapus!');
    }

    public function batchDeleteKelas(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids) || !is_array($ids)) {
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
                  ->orWhere('nisn', 'like', "%{$search}%");
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
        if (!$request->filled('nama') && $request->filled('nama_siswa')) {
            $request->merge(['nama' => $request->input('nama_siswa')]);
        }
        if (!$request->filled('nisn') && $request->filled('nis')) {
            $request->merge(['nisn' => $request->input('nis')]);
        }
        if (!$request->filled('jenis_kelamin')) {
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
                'message' => 'Data siswa ' . $siswa->nama . ' berhasil ditambahkan!',
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

        if (!$request->filled('nama') && $request->filled('nama_siswa')) {
            $request->merge(['nama' => $request->input('nama_siswa')]);
        }
        if (!$request->filled('nisn') && $request->filled('nis')) {
            $request->merge(['nisn' => $request->input('nis')]);
        }
        if (!$request->filled('jenis_kelamin')) {
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
                'message' => 'Data siswa ' . $siswa->nama . ' berhasil diperbarui!',
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
                ->with('success', 'Data siswa ' . $siswa->nama . ' berhasil diperbarui!');
        }

        return redirect()->route('dashboard.siswa')->with('success', 'Data siswa ' . $siswa->nama . ' berhasil diperbarui!');
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
                ->with('success', 'Data siswa ' . $nama . ' berhasil dihapus!');
        }

        return redirect()->route('dashboard.siswa')->with('success', 'Data siswa ' . $nama . ' berhasil dihapus!');
    }

    public function batchDeleteSiswa(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids) || !is_array($ids)) {
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

        if (empty($ids) || !is_array($ids)) {
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
                  ->orWhere('kode_mapel', 'like', "%{$search}%");
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
            usort($allTeachers, fn($a, $b) => strcmp($a->name, $b->name));

            $m->all_pengampus = collect($allTeachers);
            $m->jadwal_gurus = array_values(array_unique(array_map(fn($t) => $t->name, array_values($byJadwal))));
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
            if ($g->mapel && !in_array($g->mapel->nama_mapel, $jadwalMapelNames)) {
                $jadwalMapelNames[] = $g->mapel->nama_mapel;
            }
            return [
                'id' => $g->id,
                'name' => $g->name,
                'nip' => $g->nip ?? '',
                'mapel_id' => $g->mapel_id,
                'mapel_name' => !empty($jadwalMapelNames) ? implode(', ', $jadwalMapelNames) : '',
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
        if (!empty($validated['guru_id'])) {
            $assignedGuruIds[] = (int) $validated['guru_id'];
        }
        if ($request->filled('guru_names')) {
            $rawNames = $request->input('guru_names');
            $names = is_array($rawNames) ? $rawNames : json_decode($rawNames, true);
            if (!is_array($names)) {
                $names = array_filter(array_map('trim', explode(',', (string) $rawNames)));
            }
            if (!empty($names)) {
                $foundIds = User::where('role', 'guru')->whereIn('name', $names)->pluck('id')->toArray();
                $assignedGuruIds = array_merge($assignedGuruIds, $foundIds);
            }
        }

        $assignedGuruIds = array_unique($assignedGuruIds);
        $maxGuru = 25;
        $assignedGuruIds = array_slice($assignedGuruIds, 0, $maxGuru);

        if (!empty($assignedGuruIds)) {
            User::whereIn('id', $assignedGuruIds)->update(['mapel_id' => $mapel->id]);
        }

        return redirect()->route('dashboard.mapel')->with('success', 'Mata pelajaran berhasil ditambahkan!');
    }

    public function updateMapel(Request $request, $id)
    {
        $mapel = Mapel::findOrFail($id);

        $validated = $request->validate([
            'kode_mapel' => 'required|string|max:30|unique:mapels,kode_mapel,' . $mapel->id,
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
        if (!empty($validated['guru_id'])) {
            $assignedGuruIds[] = (int) $validated['guru_id'];
        }
        if ($request->filled('guru_names')) {
            $rawNames = $request->input('guru_names');
            $names = is_array($rawNames) ? $rawNames : json_decode($rawNames, true);
            if (!is_array($names)) {
                $names = array_filter(array_map('trim', explode(',', (string) $rawNames)));
            }
            if (!empty($names)) {
                $foundIds = User::where('role', 'guru')->whereIn('name', $names)->pluck('id')->toArray();
                $assignedGuruIds = array_merge($assignedGuruIds, $foundIds);
            }
        }

        $assignedGuruIds = array_unique($assignedGuruIds);
        $maxGuru = 25;
        $assignedGuruIds = array_slice($assignedGuruIds, 0, $maxGuru);

        if (!empty($assignedGuruIds)) {
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

        return redirect()->route('dashboard.mapel')->with('success', 'Mata pelajaran ' . $mapel->nama_mapel . ' berhasil diperbarui!');
    }

    public function destroyMapel($id)
    {
        $mapel = Mapel::findOrFail($id);
        $nama = $mapel->nama_mapel;

        // Dissociate teachers and remove related schedules safely
        User::where('mapel_id', $mapel->id)->update(['mapel_id' => null]);
        JadwalPelajaran::where('id_mapel', $mapel->id)->delete();
        $mapel->delete();

        return redirect()->route('dashboard.mapel')->with('success', 'Mata pelajaran ' . $nama . ' berhasil dihapus!');
    }

    public function batchDeleteMapel(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids) || !is_array($ids)) {
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

        $query = JadwalPelajaran::with(['kelas', 'guru', 'mapelItem']);

        if ($selectedKelas) {
            $query->where('id_kelas', $selectedKelas->id_kelas);
        }

        $jadwals = $query->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")
            ->orderBy('jam_mulai', 'asc')
            ->orderBy('jam_ke', 'asc')
            ->get();
        $mapels = Mapel::orderBy('nama_mapel', 'asc')->get();
        $gurus = User::where('role', 'guru')->orderBy('name', 'asc')->get();

        $piketWakas = JadwalPiket::with('user')
            ->where('tipe', 'waka')
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")
            ->get();

        $piketGurus = JadwalPiket::with('user')
            ->where('tipe', 'guru')
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")
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
            'isJumatMaju'
        ));
    }

    public function storeJadwal(Request $request)
    {
        $userId = $request->input('id_user') ?: $request->input('guru_id');
        if (!$userId) {
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

        JadwalPelajaran::create($validated);

        return redirect()->route('dashboard.jadwal', ['kelas_id' => $kelasId, 'hari' => $validated['hari']])
            ->with('success', 'Jadwal pelajaran berhasil ditambahkan!');
    }

    public function updateJadwal(Request $request, $id)
    {
        $jadwal = JadwalPelajaran::findOrFail($id);

        $userId = $request->input('id_user') ?: $request->input('guru_id');
        if (!$userId) {
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

        $jadwal->update($validated);

        return redirect()->route('dashboard.jadwal', ['kelas_id' => $kelasId, 'hari' => $validated['hari']])
            ->with('success', 'Jadwal pelajaran berhasil diperbarui!');
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
        if (empty($ids) || !is_array($ids)) {
            return back()->with('error', 'Pilih minimal satu jadwal.');
        }

        $count = JadwalPelajaran::whereIn('id_jadwal', $ids)->delete();

        return redirect()->route('dashboard.jadwal')->with('success', "{$count} jadwal pelajaran berhasil dihapus masal!");
    }

    public function shiftTimeJadwal(Request $request)
    {
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
        $isCurrentlyMaju = (bool) Pengaturan::getValue($hariKey . '_is_maju', 0);

        // Guard Idempotensi: cegah pergeseran waktu berulang kali
        if ($mode === 'maju' && $isCurrentlyMaju) {
            return redirect()->back()->with('info', "Mode Jam Maju untuk hari {$hari} sudah aktif sebelumnya.");
        }

        if ($mode === 'normal' && !$isCurrentlyMaju) {
            return redirect()->back()->with('info', "Jadwal hari {$hari} sudah dalam status jam normal.");
        }

        // Otomatis diterapkan untuk SELURUH KELAS
        $allKelasIds = Kelas::pluck('id_kelas')->toArray();
        $targetKeyword = ($hari === 'Jumat') ? 'pembiasaan' : 'upacara';

        if ($mode === 'maju') {
            // Mode Maju: Upacara/Pembiasaan ditandai ditiadakan, sesi lain dimajukan
            foreach ($allKelasIds as $kId) {
                $lessons = JadwalPelajaran::where('id_kelas', $kId)
                    ->where('hari', $hari)
                    ->get();

                foreach ($lessons as $l) {
                    $isTargetActivity = str_contains(strtolower($l->mapel), $targetKeyword);
                    if ($isTargetActivity) {
                        $l->update(['status' => 'ditiadakan']);
                    } else {
                        $newStart = date('H:i:s', max(0, strtotime($l->jam_mulai) - ($minutes * 60)));
                        $newEnd = date('H:i:s', max(0, strtotime($l->jam_selesai) - ($minutes * 60)));
                        $l->update([
                            'jam_mulai' => $newStart, 
                            'jam_selesai' => $newEnd,
                            'status' => 'aktif'
                        ]);
                    }
                }
            }

            Pengaturan::setValue($hariKey . '_is_maju', 1);
            Pengaturan::setValue($hariKey . '_shifted_minutes', $minutes);
            $msg = "Mode Jam Maju hari {$hari} berhasil diaktifkan untuk SELURUH KELAS ({$targetKeyword} ditiadakan, jam pelajaran dimajukan {$minutes} menit).";
        } else {
            // Mode Normal: Kembalikan waktu dengan menambah shifted_minutes yang tersimpan
            $shiftedMinutes = (int) Pengaturan::getValue($hariKey . '_shifted_minutes', $minutes);

            foreach ($allKelasIds as $kId) {
                $lessons = JadwalPelajaran::where('id_kelas', $kId)
                    ->where('hari', $hari)
                    ->get();

                foreach ($lessons as $l) {
                    $isTargetActivity = str_contains(strtolower($l->mapel), $targetKeyword);
                    if ($isTargetActivity) {
                        $l->update(['status' => 'aktif']);
                    } else {
                        $newStart = date('H:i:s', strtotime($l->jam_mulai) + ($shiftedMinutes * 60));
                        $newEnd = date('H:i:s', strtotime($l->jam_selesai) + ($shiftedMinutes * 60));
                        $l->update([
                            'jam_mulai' => $newStart, 
                            'jam_selesai' => $newEnd,
                            'status' => 'aktif'
                        ]);
                    }
                }
            }

            Pengaturan::setValue($hariKey . '_is_maju', 0);
            Pengaturan::setValue($hariKey . '_shifted_minutes', 0);
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
        $shiftSenin = (int) Pengaturan::getValue('shift_senin_minutes', 40);
        $shiftJumat = (int) Pengaturan::getValue('shift_jumat_minutes', 30);
        $isSeninMaju = (bool) Pengaturan::getValue('senin_is_maju', 0);
        $seninShiftedMinutes = (int) Pengaturan::getValue('senin_shifted_minutes', $shiftSenin);
        $isJumatMaju = (bool) Pengaturan::getValue('jumat_is_maju', 0);
        $jumatShiftedMinutes = (int) Pengaturan::getValue('jumat_shifted_minutes', $shiftJumat);

        $totalKelas = Kelas::count();

        return view('dashboard.admin.pengaturan', compact(
            'shiftSenin',
            'shiftJumat',
            'isSeninMaju',
            'seninShiftedMinutes',
            'isJumatMaju',
            'jumatShiftedMinutes',
            'totalKelas'
        ));
    }

    public function updatePengaturan(Request $request)
    {
        $validated = $request->validate([
            'shift_senin_minutes' => 'required|integer|min:5|max:180',
            'shift_jumat_minutes' => 'required|integer|min:5|max:180',
        ], [
            'shift_senin_minutes.required' => 'Menit pemajuan hari Senin wajib diisi.',
            'shift_jumat_minutes.required' => 'Menit pemajuan hari Jum\'at wajib diisi.',
            'shift_senin_minutes.min' => 'Menit minimal adalah 5 menit.',
            'shift_jumat_minutes.min' => 'Menit minimal adalah 5 menit.',
        ]);

        Pengaturan::setValue('shift_senin_minutes', $validated['shift_senin_minutes']);
        Pengaturan::setValue('shift_jumat_minutes', $validated['shift_jumat_minutes']);

        return redirect()->route('admin.pengaturan')->with('success', 'Pengaturan jam jadwal berhasil disimpan.');
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
            $import = new JadwalImport();
            Excel::import($import, $request->file('file'));

            return redirect()->route('dashboard.jadwal')->with('success', "Import selesai! {$import->importedCount} jadwal baru ditambahkan dan {$import->updatedCount} jadwal diperbarui.");
        } catch (\Throwable $e) {
            return redirect()->route('dashboard.jadwal')->with('error', 'Gagal memproses file Excel: ' . $e->getMessage());
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
    public function rekapJurnal(Request $request)
    {
        $tanggal = $request->query('tanggal', now()->toDateString());
        $kelasId = $request->query('kelas_id');
        $kehadiran = $request->query('kehadiran', 'all');
        $validasi = $request->query('validasi', 'all');
        $search = $request->query('search', '');
        $keterlambatan = $request->query('keterlambatan', 'all');

        $query = JurnalMengajar::with(['kelas', 'guru', 'mapel']);

        if ($tanggal) {
            $query->whereDate('tanggal', $tanggal);
        }

        if ($kelasId && $kelasId !== 'all') {
            $query->where('id_kelas', $kelasId);
        }

        if ($kehadiran && $kehadiran !== 'all') {
            $query->where('status_kehadiran_guru', $kehadiran);
        }

        if ($keterlambatan === 'terlambat') {
            $query->where('menit_keterlambatan', '>', 0);
        } elseif ($keterlambatan === 'tepat_waktu') {
            $query->where('menit_keterlambatan', '<=', 0)->where('status_kehadiran_guru', 'Hadir');
        }

        if ($request->filled('search')) {
            $term = $request->query('search');
            $query->where(function($q) use ($term) {
                $q->where('materi', 'like', "%{$term}%")
                  ->orWhereHas('guru', fn($g) => $g->where('name', 'like', "%{$term}%"))
                  ->orWhereHas('mapel', fn($m) => $m->where('nama_mapel', 'like', "%{$term}%"))
                  ->orWhereHas('kelas', fn($k) => $k->where('nama_kelas', 'like', "%{$term}%"));
            });
        }

        $allDayJurnals = (clone $query)->orderBy('jam_ke', 'asc')->get();
        $kelases = Kelas::orderBy('nama_kelas', 'asc')->get();
        $totalKelas = $kelases->count();
        $kelasLapor = JurnalMengajar::whereDate('tanggal', $tanggal)->distinct('id_kelas')->count('id_kelas');
        $guruHadir = JurnalMengajar::whereDate('tanggal', $tanggal)->where('status_kehadiran_guru', 'Hadir')->count();
        $guruTidakHadir = JurnalMengajar::whereDate('tanggal', $tanggal)->where('status_kehadiran_guru', '!=', 'Hadir')->count();
        $guruTerlambat = JurnalMengajar::whereDate('tanggal', $tanggal)->where('menit_keterlambatan', '>', 0)->count();

        // Info Piket Hari Ini & Waka Backup
        $dayOfWeek = Carbon::parse($tanggal)->dayOfWeek;
        $namaHari = match ($dayOfWeek) {
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            default => 'Minggu',
        };
        $piketGuru = JadwalPiket::with('user')->where('hari', $namaHari)->where('tipe', 'guru')->first();
        $piketWaka = JadwalPiket::with('user')->where('hari', $namaHari)->where('tipe', 'waka')->first();
        $allWakaUsers = JadwalPiket::where('tipe', 'waka')->with('user')->get()->pluck('user')->filter()->unique('id');
        $backupWakas = $allWakaUsers->filter(fn($u) => !$piketWaka || $u->id !== $piketWaka->user_id);
        if ($backupWakas->isEmpty()) {
            $backupWakas = User::where('role', 'guru')->where('id', '!=', optional($piketWaka)->user_id)->limit(3)->get();
        }

        // Data Penugasan Piket untuk Modal Pengaturan
        $allPiketJadwals = JadwalPiket::all()->groupBy('hari');

        // Notif Approval Dispensasi Siswa
        $requestDispensasi = \App\Models\Dispensasi::with('siswa.kelas')
            ->whereDate('tanggal', $tanggal)
            ->where('status_akhir', 'Pending')
            ->get();

        $tab = $request->query('tab', 'all');
        $guruAbsen = $guruTidakHadir;
        $menungguValidasi = 0;
        $countSemua = $allDayJurnals->count();
        $countBelumValidasi = 0;
        $countGuruAbsen = $allDayJurnals->where('status_kehadiran_guru', '!=', 'Hadir')->count();
        $jurnals = ($tab === 'guru_absen')
            ? $allDayJurnals->where('status_kehadiran_guru', '!=', 'Hadir')
            : $allDayJurnals;
        $gurus = User::where('role', 'guru')->orderBy('name', 'asc')->get();

        return view('dashboard.admin.rekap-jurnal', compact(
            'jurnals',
            'kelases',
            'gurus',
            'tanggal',
            'kelasId',
            'kehadiran',
            'validasi',
            'search',
            'keterlambatan',
            'totalKelas',
            'kelasLapor',
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
            'piketGuru',
            'piketWaka',
            'backupWakas',
            'allPiketJadwals',
            'requestDispensasi'
        ));
    }

    public function updatePenugasanPiket(Request $request)
    {
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        foreach ($days as $day) {
            $guruId = $request->input("piket.{$day}");
            if ($guruId) {
                JadwalPiket::updateOrCreate(
                    ['hari' => $day, 'tipe' => 'guru'],
                    ['user_id' => $guruId, 'keterangan' => "Petugas Piket Guru Hari {$day}"]
                );
            }

            $wakaId = $request->input("waka.{$day}");
            if ($wakaId) {
                JadwalPiket::updateOrCreate(
                    ['hari' => $day, 'tipe' => 'waka'],
                    ['user_id' => $wakaId, 'keterangan' => "Petugas Piket Waka Hari {$day}"]
                );
            }
        }

        return redirect()->route('dashboard.rekap-jurnal')->with('success', 'Penugasan Guru Piket dan Waka berhasil diperbarui!');
    }

    public function catatanJurnal(Request $request)
    {
        return $this->rekapJurnal($request);
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
            if ($u->mapel && !in_array($u->mapel->nama_mapel, $mapelList)) {
                $mapelList[] = $u->mapel->nama_mapel;
            }
            $namaMapel = !empty($mapelList) ? implode(', ', $mapelList) : (optional($u->mapel)->nama_mapel ?? '');

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

        return view('dashboard.admin.manajemen-user', compact('users', 'usersForJs', 'kelasList', 'kelases', 'mapels', 'search', 'role'));
    }

    public function storeUser(Request $request)
    {
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
            'username.unique' => 'Username ini sudah digunakan.',
            'nip.unique' => 'NIP ini sudah terdaftar.',
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

        return redirect()->route('admin.manajemen-user')->with('success', 'User baru berhasil dibuat!');
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($request->input('role') === 'sekretaris') {
            $request->merge(['role' => 'pengurus_kelas']);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username,' . $user->id,
            'nip' => 'nullable|string|max:30|unique:users,nip,' . $user->id,
            'role' => 'required|in:admin,pengurus_kelas,guru',
            'password' => 'nullable|string|min:6',
            'no_hp' => 'nullable|string|max:25',
            'mapel_id' => 'nullable|exists:mapels,id',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'username' => $validated['username'],
            'nip' => $validated['nip'] ?? null,
            'role' => $validated['role'],
            'no_hp' => $validated['no_hp'] ?? null,
            'mapel_id' => $validated['mapel_id'] ?? null,
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()->route('admin.manajemen-user')->with('success', 'Data user ' . $user->name . ' berhasil diperbarui!');
    }

    public function destroyUser($id)
    {
        $user = User::findOrFail($id);
        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.manajemen-user')->with('success', 'User ' . $name . ' berhasil dihapus!');
    }
}
