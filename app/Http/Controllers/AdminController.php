<?php

namespace App\Http\Controllers;

use App\Models\JadwalPelajaran;
use App\Models\JurnalMengajar;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Notifikasi;
use App\Models\PasswordResetRequest;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    // =========================================================================
    // 1. DASHBOARD UTAMA
    // =========================================================================
    public function index()
    {
        $jumlahGuru = User::where('role', 'guru')
            ->where(function ($q) {
                $q->where('status', 'aktif')->orWhereNull('status');
            })->count();

        $jumlahKelas = Kelas::where(function ($q) {
            $q->where('status', 'aktif')->orWhereNull('status');
        })->count();

        $jumlahMapel = Mapel::where(function ($q) {
            $q->where('status', 'aktif')->orWhereNull('status');
        })->count();

        $jurnalHariIni = JurnalMengajar::whereDate('tanggal', now()->toDateString())->count();

        // Data aktivitas nyata dari jurnal mengajar terbaru
        $recentJurnals = JurnalMengajar::with(['kelas', 'guru', 'mapel'])
            ->orderBy('id_jurnal', 'desc')
            ->take(5)
            ->get();

        $aktivitasTerbaru = [];
        foreach ($recentJurnals as $j) {
            $aktivitasTerbaru[] = [
                'waktu' => \Carbon\Carbon::parse($j->tanggal)->translatedFormat('d M Y') . ', ' . ($j->jam_mulai ? substr($j->jam_mulai, 0, 5) : '07:30'),
                'nama_guru' => optional($j->guru)->name ?? 'Guru Pengampu',
                'mapel' => optional($j->mapel)->nama_mapel ?? 'Mata Pelajaran',
                'kelas' => optional($j->kelas)->nama_kelas ?? '-',
                'status' => $j->status_validasi ?? 'Selesai',
            ];
        }

        return view('dashboard.admin.admin', compact(
            'jumlahGuru',
            'jumlahKelas',
            'jumlahMapel',
            'jurnalHariIni',
            'aktivitasTerbaru'
        ));
    }

    // =========================================================================
    // 2. CATATAN JURNAL MONITORING
    // =========================================================================
    public function catatanJurnal(Request $request)
    {
        $tanggal = $request->query('tanggal', now()->toDateString());
        $tab = $request->query('tab', 'all');
        $kelasId = $request->query('kelas_id');
        $kehadiran = $request->query('kehadiran');
        $validasi = $request->query('validasi');
        $search = $request->query('search');

        // Daftar Kelas Aktif (hanya kelas X dan XI)
        $kelases = Kelas::where(function ($q) {
            $q->where('status', 'aktif')->orWhereNull('status');
        })
        ->where('nama_kelas', 'not like', 'XII%')
        ->where('nama_kelas', 'not like', '12%')
        ->orderBy('nama_kelas')
        ->get();

        $totalKelas = $kelases->count();

        // Daftar Guru untuk Pilihan Guru Inval / Pengganti
        $gurus = User::where(function ($q) {
            $q->whereIn('role', ['guru', 'piket']);
        })
        ->where(function ($q) {
            $q->where('status', 'aktif')->orWhereNull('status');
        })
        ->orderBy('name')
        ->get();

        // 4 Statistik Real-time sesuai Tanggal
        $kelasLapor = JurnalMengajar::whereDate('tanggal', $tanggal)->distinct('id_kelas')->count('id_kelas');
        $guruHadir = JurnalMengajar::whereDate('tanggal', $tanggal)->where('status_kehadiran_guru', 'Hadir')->distinct('id_user')->count('id_user');
        $guruAbsen = JurnalMengajar::whereDate('tanggal', $tanggal)->where('status_kehadiran_guru', '!=', 'Hadir')->distinct('id_user')->count('id_user');
        $menungguValidasi = JurnalMengajar::whereDate('tanggal', $tanggal)->where('status_validasi', 'Menunggu')->count();

        // Hitung badge untuk setiap tab
        $countSemua = JurnalMengajar::whereDate('tanggal', $tanggal)->count();
        $countBelumValidasi = JurnalMengajar::whereDate('tanggal', $tanggal)->where('status_validasi', 'Menunggu')->count();
        $countGuruAbsen = JurnalMengajar::whereDate('tanggal', $tanggal)->where('status_kehadiran_guru', '!=', 'Hadir')->count();

        // Query Jurnal
        $query = JurnalMengajar::with(['kelas', 'guru', 'guruInval', 'mapel'])
            ->whereDate('tanggal', $tanggal);

        if ($tab === 'belum_validasi') {
            $query->where('status_validasi', 'Menunggu');
        } elseif ($tab === 'guru_absen') {
            $query->where('status_kehadiran_guru', '!=', 'Hadir');
        }

        if ($kelasId && $kelasId !== 'all') {
            $query->where('id_kelas', $kelasId);
        }

        if ($kehadiran && $kehadiran !== 'all') {
            $query->where('status_kehadiran_guru', $kehadiran);
        }

        if ($validasi && $validasi !== 'all') {
            $query->where('status_validasi', $validasi);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('materi', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%")
                  ->orWhere('catatan', 'like', "%{$search}%")
                  ->orWhereHas('guru', function ($g) use ($search) {
                      $g->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('mapel', function ($m) use ($search) {
                      $m->where('nama_mapel', 'like', "%{$search}%");
                  })
                  ->orWhereHas('kelas', function ($k) use ($search) {
                      $k->where('nama_kelas', 'like', "%{$search}%");
                  });
            });
        }

        $jurnals = $query->orderBy('jam_mulai', 'asc')->orderBy('id_jurnal', 'asc')->get();

        return view('dashboard.admin.catatan-jurnal', compact(
            'totalKelas',
            'kelasLapor',
            'guruHadir',
            'guruAbsen',
            'menungguValidasi',
            'countSemua',
            'countBelumValidasi',
            'countGuruAbsen',
            'kelases',
            'gurus',
            'jurnals',
            'tanggal',
            'tab',
            'kelasId',
            'kehadiran',
            'validasi',
            'search'
        ));
    }

    public function validasiJurnal(Request $request, $id)
    {
        $jurnal = JurnalMengajar::with(['kelas', 'mapel', 'guru'])->findOrFail($id);
        $jurnal->update([
            'status_validasi' => 'Selesai',
        ]);

        $namaKelas = optional($jurnal->kelas)->nama_kelas ?? 'Kelas';
        $namaMapel = optional($jurnal->mapel)->nama_mapel ?? 'Mata Pelajaran';
        $namaGuru = optional($jurnal->guru)->name ?? 'Guru Pengampu';
        $jamKe = $jurnal->jam_ke > 0 ? "Jam ke-{$jurnal->jam_ke}" : "Sesi Pembelajaran";

        // Cari user sekretaris untuk kelas ini atau default sekretaris
        $sekretaris = User::where('role', 'sekretaris')
            ->where('id_kelas', $jurnal->id_kelas)
            ->first() ?? User::where('role', 'sekretaris')
            ->where(function ($q) use ($namaKelas) {
                $q->where('name', 'like', "%{$namaKelas}%");
            })->first() ?? User::where('role', 'sekretaris')->first();

        Notifikasi::create([
            'id_user' => $sekretaris ? $sekretaris->id : null,
            'id_kelas' => $jurnal->id_kelas,
            'id_jurnal' => $jurnal->id_jurnal,
            'judul' => 'Jurnal KBM Divalidasi Guru Piket',
            'pesan' => "Jurnal mengajar kelas {$namaKelas} untuk mata pelajaran {$namaMapel} ({$namaGuru} • {$jamKe}) telah divalidasi oleh Petugas Guru Piket (Admin).",
            'tipe' => 'validasi',
            'is_read' => false,
        ]);

        return redirect()->back()->with('success', 'Jurnal kelas ' . $namaKelas . ' berhasil divalidasi dan notifikasi terkirim ke sekretaris!');
    }

    public function invalJurnal(Request $request, $id)
    {
        $jurnal = JurnalMengajar::with(['kelas', 'mapel', 'guru'])->findOrFail($id);
        $request->validate([
            'guru_inval_id' => 'required|exists:users,id',
            'catatan_inval' => 'nullable|string|max:200',
        ]);

        $invalUser = User::findOrFail($request->guru_inval_id);

        $catatanTambahan = 'Ditugaskan guru inval: ' . $invalUser->name;
        if ($request->catatan_inval) {
            $catatanTambahan .= ' (' . $request->catatan_inval . ')';
        }

        $jurnal->update([
            'guru_inval_id' => $request->guru_inval_id,
            'catatan' => $jurnal->catatan ? $jurnal->catatan . ' | ' . $catatanTambahan : $catatanTambahan,
        ]);

        $namaKelas = optional($jurnal->kelas)->nama_kelas ?? 'Kelas';
        $namaMapel = optional($jurnal->mapel)->nama_mapel ?? 'Mata Pelajaran';
        $jamKe = $jurnal->jam_ke > 0 ? "Jam ke-{$jurnal->jam_ke}" : "Sesi Pembelajaran";

        $sekretaris = User::where('role', 'sekretaris')
            ->where('id_kelas', $jurnal->id_kelas)
            ->first() ?? User::where('role', 'sekretaris')
            ->where(function ($q) use ($namaKelas) {
                $q->where('name', 'like', "%{$namaKelas}%");
            })->first() ?? User::where('role', 'sekretaris')->first();

        Notifikasi::create([
            'id_user' => $sekretaris ? $sekretaris->id : null,
            'id_kelas' => $jurnal->id_kelas,
            'id_jurnal' => $jurnal->id_jurnal,
            'judul' => 'Penugasan Guru Inval / Pengganti',
            'pesan' => "Petugas Guru Piket telah menugaskan {$invalUser->name} sebagai guru inval di kelas {$namaKelas} untuk mapel {$namaMapel} ({$jamKe}).",
            'tipe' => 'inval',
            'is_read' => false,
        ]);

        return redirect()->back()->with('success', 'Guru Inval ' . $invalUser->name . ' berhasil ditugaskan untuk kelas ' . $namaKelas . '!');
    }

    // =========================================================================
    // 3. DATA GURU
    // =========================================================================
    public function guru(Request $request)
    {
        $search = $request->query('search');

        $query = User::where('role', 'guru')
                     ->where(function ($q) {
                         $q->where('status', 'aktif')->orWhereNull('status');
                     })
                     ->with(['mapel', 'mapelsPengampu']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhereHas('mapel', function ($m) use ($search) {
                      $m->where('nama_mapel', 'like', "%{$search}%");
                  })
                  ->orWhereHas('mapelsPengampu', function ($m) use ($search) {
                      $m->where('nama_mapel', 'like', "%{$search}%");
                  });
            });
        }

        $users = $query->orderBy('name')->get();

        $mapels = Mapel::where(function ($q) {
            $q->where('status', 'aktif')->orWhereNull('status');
        })->orderByRaw("CASE WHEN kategori = 'jurusan' THEN 1 ELSE 2 END")
          ->orderBy('nama_mapel')
          ->get();

        return view('dashboard.admin.guru', compact('users', 'mapels'));
    }

    public function storeGuru(Request $request)
    {
        $validated = $request->validate([
            'nip' => 'nullable|string|max:30',
            'nama' => 'required|string|max:255',
            'mapel_id' => 'nullable|exists:mapels,id',
            'no_hp' => 'nullable|string|max:20',
        ], [
            'nama.required' => 'Nama guru wajib diisi.',
            'mapel_id.exists' => 'Mata pelajaran yang dipilih tidak valid.',
        ]);

        if (!empty($validated['nip'])) {
            $existingUser = User::where('nip', $validated['nip'])->first();
            if ($existingUser) {
                if ($existingUser->status === 'nonaktif') {
                    $existingUser->update([
                        'name' => $validated['nama'],
                        'no_hp' => $validated['no_hp'] ?? $existingUser->no_hp,
                        'mapel_id' => $validated['mapel_id'] ?: $existingUser->mapel_id,
                        'status' => 'aktif',
                        'alasan_hapus' => null,
                    ]);

                    if (!empty($validated['mapel_id'])) {
                        DB::table('mapel_user')->updateOrInsert(
                            ['mapel_id' => $validated['mapel_id'], 'user_id' => $existingUser->id],
                            ['created_at' => now(), 'updated_at' => now()]
                        );
                    }

                    return redirect()->route('dashboard.guru')->with('success', 'Guru ' . $existingUser->name . ' berhasil diaktifkan kembali!');
                }
                return back()->withErrors(['nip' => 'NIP sudah terdaftar dan sedang aktif.'])->withInput();
            }
        }

        $baseUsername = !empty($validated['nip']) ? $validated['nip'] : Str::slug($validated['nama'], '_');
        $username = $baseUsername;
        $counter = 1;
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . '_' . $counter;
            $counter++;
        }

        $user = User::create([
            'name' => $validated['nama'],
            'nip' => $validated['nip'] ?: null,
            'username' => $username,
            'email' => null,
            'password' => Hash::make('password'),
            'role' => 'guru',
            'status' => 'aktif',
            'alasan_hapus' => null,
            'no_hp' => $validated['no_hp'] ?? null,
            'mapel_id' => $validated['mapel_id'] ?: null,
        ]);

        if (!empty($validated['mapel_id'])) {
            DB::table('mapel_user')->updateOrInsert(
                ['mapel_id' => $validated['mapel_id'], 'user_id' => $user->id],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }

        return redirect()->route('dashboard.guru')->with('success', 'Data guru berhasil ditambahkan!');
    }

    public function updateGuru(Request $request, $id)
    {
        $guru = User::where('role', 'guru')->findOrFail($id);
        $oldName = $guru->name;

        $validated = $request->validate([
            'nip' => 'nullable|string|max:30|unique:users,nip,' . $guru->id,
            'nama' => 'required|string|max:255',
            'mapel_id' => 'nullable|exists:mapels,id',
            'no_hp' => 'nullable|string|max:20',
        ], [
            'nama.required' => 'Nama guru wajib diisi.',
            'nip.unique' => 'NIP sudah terdaftar.',
            'mapel_id.exists' => 'Mata pelajaran yang dipilih tidak valid.',
        ]);

        $guru->update([
            'name' => $validated['nama'],
            'nip' => $validated['nip'] ?: null,
            'no_hp' => $validated['no_hp'] ?? null,
            'mapel_id' => $validated['mapel_id'] ?: null,
        ]);

        // Sinkronkan nama wali kelas di tabel kelas jika guru ini adalah wali kelas
        if ($oldName !== $validated['nama']) {
            Kelas::where('wali_kelas', $oldName)->update(['wali_kelas' => $validated['nama']]);
        }

        DB::table('mapel_user')->where('user_id', $guru->id)->delete();
        if (!empty($validated['mapel_id'])) {
            DB::table('mapel_user')->updateOrInsert(
                ['mapel_id' => $validated['mapel_id'], 'user_id' => $guru->id],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }

        return redirect()->route('dashboard.guru')->with('success', 'Data guru berhasil diperbarui!');
    }

    public function destroyGuru(Request $request, $id)
    {
        $request->validate([
            'alasan' => 'required|string|max:500',
        ], [
            'alasan.required' => 'Alasan penghapusan/penonaktifan guru wajib diisi.',
        ]);

        $guru = User::where('role', 'guru')->findOrFail($id);
        $guru->update([
            'status' => 'nonaktif',
            'alasan_hapus' => $request->alasan,
        ]);

        return redirect()->route('dashboard.guru')->with('success', 'Data guru berhasil dinonaktifkan dari sistem!');
    }

    // =========================================================================
    // 4. DATA KELAS
    // =========================================================================
    public function kelas(Request $request)
    {
        $search = $request->query('search');

        $query = Kelas::where(function ($q) {
            $q->where('status', 'aktif')->orWhereNull('status');
        });

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_kelas', 'like', "%{$search}%")
                  ->orWhere('wali_kelas', 'like', "%{$search}%");
            });
        }

        $kelasList = $query->with('activeSiswas')->get();

        $allActiveSiswas = Siswa::with('kelas')
            ->where(function ($q) {
                $q->where('status', 'aktif')->orWhereNull('status');
            })
            ->orderBy('kelas_id')
            ->orderBy('nama', 'asc')
            ->get();

        $gurus = User::where('role', 'guru')
            ->where(function ($q) {
                $q->where('status', 'aktif')->orWhereNull('status');
            })
            ->orderBy('name')
            ->get();

        return view('dashboard.admin.kelas', compact('kelasList', 'allActiveSiswas', 'gurus'));
    }

    public function storeKelas(Request $request)
    {
        $validated = $request->validate([
            'nama_kelas' => 'required|string|max:50',
            'nama_guru' => 'nullable|string|max:100',
            'jumlah_siswa' => 'nullable|integer|min:0',
        ], [
            'nama_kelas.required' => 'Nama kelas wajib diisi.',
        ]);

        $existingKelas = Kelas::where('nama_kelas', $validated['nama_kelas'])->first();

        if ($existingKelas) {
            if ($existingKelas->status === 'nonaktif') {
                $existingKelas->update([
                    'wali_kelas' => $validated['nama_guru'] ?? $existingKelas->wali_kelas,
                    'jumlah_siswa' => $validated['jumlah_siswa'] ?? $existingKelas->jumlah_siswa,
                    'status' => 'aktif',
                    'alasan_hapus' => null,
                ]);

                return redirect()->route('dashboard.kelas')->with('success', 'Kelas ' . $existingKelas->nama_kelas . ' berhasil diaktifkan kembali!');
            }

            return back()->withErrors(['nama_kelas' => 'Nama kelas sudah terdaftar dan sedang aktif.'])->withInput();
        }

        Kelas::create([
            'nama_kelas' => $validated['nama_kelas'],
            'wali_kelas' => $validated['nama_guru'] ?? null,
            'jumlah_siswa' => $validated['jumlah_siswa'] ?? 0,
            'status' => 'aktif',
            'alasan_hapus' => null,
        ]);

        return redirect()->route('dashboard.kelas')->with('success', 'Data kelas berhasil ditambahkan!');
    }

    public function updateKelas(Request $request, $id)
    {
        $kelas = Kelas::findOrFail($id);

        $validated = $request->validate([
            'nama_kelas' => 'required|string|max:50|unique:kelas,nama_kelas,' . $id . ',id_kelas',
            'nama_guru' => 'nullable|string|max:100',
            'jumlah_siswa' => 'nullable|integer|min:0',
        ], [
            'nama_kelas.required' => 'Nama kelas wajib diisi.',
            'nama_kelas.unique' => 'Nama kelas sudah terdaftar.',
        ]);

        $kelas->update([
            'nama_kelas' => $validated['nama_kelas'],
            'wali_kelas' => $validated['nama_guru'] ?? $kelas->wali_kelas,
            'jumlah_siswa' => $validated['jumlah_siswa'] ?? $kelas->jumlah_siswa,
        ]);

        return redirect()->route('dashboard.kelas')->with('success', 'Data kelas berhasil diperbarui!');
    }

    public function destroyKelas(Request $request, $id)
    {
        $request->validate([
            'alasan' => 'required|string|max:500',
        ], [
            'alasan.required' => 'Alasan penghapusan kelas wajib diisi.',
        ]);

        $kelas = Kelas::findOrFail($id);
        $kelas->update([
            'status' => 'nonaktif',
            'alasan_hapus' => $request->alasan,
        ]);

        return redirect()->route('dashboard.kelas')->with('success', 'Data kelas berhasil dinonaktifkan dari sistem!');
    }

    // =========================================================================
    // 5. DATA SISWA
    // =========================================================================
    public function siswa(Request $request)
    {
        $search = $request->query('search');
        $kelasId = $request->query('kelas_id');

        $query = Siswa::with('kelas')
            ->where(function ($q) {
                $q->where('status', 'aktif')->orWhereNull('status');
            });

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%")
                  ->orWhereHas('kelas', function ($kq) use ($search) {
                      $kq->where('nama_kelas', 'like', "%{$search}%");
                  });
            });
        }

        if ($kelasId && $kelasId !== 'all') {
            $query->where('kelas_id', $kelasId);
        }

        $siswas = $query->orderBy('kelas_id')->orderBy('nis')->get();

        $kelasList = Kelas::where(function ($q) {
            $q->where('status', 'aktif')->orWhereNull('status');
        })
        ->where('nama_kelas', 'not like', 'XII%')
        ->where('nama_kelas', 'not like', '12%')
        ->orderBy('nama_kelas')->get();

        return view('dashboard.admin.siswa', compact('siswas', 'kelasList', 'search', 'kelasId'));
    }

    public function storeSiswa(Request $request)
    {
        $nama = $request->input('nama_siswa') ?? $request->input('nama');
        $request->merge(['nama' => $nama]);

        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'nis' => 'required|string|max:20',
            'kelas_id' => 'required|exists:kelas,id_kelas',
            'jenis_kelamin' => 'nullable|in:L,P',
        ], [
            'nama.required' => 'Nama siswa wajib diisi.',
            'nis.required' => 'NIS wajib diisi.',
            'kelas_id.required' => 'Kelas wajib dipilih.',
            'kelas_id.exists' => 'Kelas yang dipilih tidak valid.',
        ]);

        $existingSiswa = Siswa::where('nis', $validated['nis'])->first();

        if ($existingSiswa) {
            if ($existingSiswa->status === 'nonaktif') {
                $existingSiswa->update([
                    'nama' => $validated['nama'],
                    'kelas_id' => $validated['kelas_id'],
                    'jenis_kelamin' => $validated['jenis_kelamin'] ?? $existingSiswa->jenis_kelamin ?? 'L',
                    'status' => 'aktif',
                    'alasan_hapus' => null,
                ]);

                $this->syncJumlahSiswa($validated['kelas_id']);

                return redirect()->back()
                    ->with('open_kelas_id', $validated['kelas_id'])
                    ->with('success', 'Siswa ' . $existingSiswa->nama . ' (NIS: ' . $existingSiswa->nis . ') berhasil diaktifkan kembali!');
            }

            return redirect()->back()
                ->with('open_kelas_id', $validated['kelas_id'])
                ->withErrors(['nis' => 'NIS sudah digunakan oleh siswa aktif lain.'])
                ->withInput();
        }

        $siswa = Siswa::create([
            'nama' => $validated['nama'],
            'nis' => $validated['nis'],
            'kelas_id' => $validated['kelas_id'],
            'jenis_kelamin' => $validated['jenis_kelamin'] ?? 'L',
            'status' => 'aktif',
            'alasan_hapus' => null,
        ]);

        $this->syncJumlahSiswa($validated['kelas_id']);

        return redirect()->back()
            ->with('open_kelas_id', $validated['kelas_id'])
            ->with('success', 'Data siswa ' . $siswa->nama . ' berhasil ditambahkan!');
    }

    public function updateSiswa(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);
        $oldKelasId = $siswa->kelas_id;

        $nama = $request->input('nama_siswa') ?? $request->input('nama');
        $request->merge(['nama' => $nama]);

        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'nis' => 'required|string|max:20|unique:siswas,nis,' . $id . ',id',
            'kelas_id' => 'required|exists:kelas,id_kelas',
            'jenis_kelamin' => 'nullable|in:L,P',
        ], [
            'nama.required' => 'Nama siswa wajib diisi.',
            'nis.required' => 'NIS wajib diisi.',
            'nis.unique' => 'NIS sudah digunakan oleh siswa lain.',
            'kelas_id.required' => 'Kelas wajib dipilih.',
        ]);

        $siswa->update([
            'nama' => $validated['nama'],
            'nis' => $validated['nis'],
            'kelas_id' => $validated['kelas_id'],
            'jenis_kelamin' => $validated['jenis_kelamin'] ?? $siswa->jenis_kelamin ?? 'L',
        ]);

        $this->syncJumlahSiswa($validated['kelas_id']);
        if ($oldKelasId != $validated['kelas_id']) {
            $this->syncJumlahSiswa($oldKelasId);
        }

        return redirect()->back()
            ->with('open_kelas_id', $validated['kelas_id'])
            ->with('success', 'Data siswa ' . $siswa->nama . ' berhasil diperbarui!');
    }

    public function destroySiswa(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);
        $kelasId = $siswa->kelas_id;

        $request->validate([
            'alasan' => 'required|string',
        ], [
            'alasan.required' => 'Alasan penghapusan siswa wajib diisi.',
        ]);

        $siswa->update([
            'status' => 'nonaktif',
            'alasan_hapus' => $request->alasan,
        ]);

        $this->syncJumlahSiswa($kelasId);

        return redirect()->back()
            ->with('open_kelas_id', $kelasId)
            ->with('success', 'Siswa ' . $siswa->nama . ' berhasil dihapus dari kelas.');
    }

    private function syncJumlahSiswa($kelasId)
    {
        $count = Siswa::where('kelas_id', $kelasId)
            ->where(function ($q) {
                $q->where('status', 'aktif')->orWhereNull('status');
            })->count();

        Kelas::where('id_kelas', $kelasId)->update(['jumlah_siswa' => $count]);
    }

    // =========================================================================
    // 6. MATA PELAJARAN (35 Mapel, Jurusan & Biasa)
    // =========================================================================
    public function mapel(Request $request)
    {
        $search = $request->query('search');
        $kategori = $request->query('kategori');

        $query = Mapel::with(['guru', 'pengampu'])->where(function ($q) {
            $q->where('status', 'aktif')->orWhereNull('status');
        });

        if ($kategori === 'jurusan') {
            $query->where('kategori', 'jurusan');
        } elseif ($kategori === 'biasa' || $kategori === 'umum' || $kategori === 'pilihan') {
            $query->whereIn('kategori', ['biasa', 'umum', 'pilihan']);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_mapel', 'like', "%{$search}%")
                  ->orWhere('nama_mapel', 'like', "%{$search}%")
                  ->orWhereHas('pengampu', function ($g) use ($search) {
                      $g->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('guru', function ($g) use ($search) {
                      $g->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $mapels = $query->orderByRaw("CASE WHEN kategori = 'jurusan' THEN 1 ELSE 2 END")
                        ->orderBy('nama_mapel')
                        ->get();

        $counts = [
            'total' => Mapel::where(fn($q) => $q->where('status', 'aktif')->orWhereNull('status'))->count(),
            'jurusan' => Mapel::where(fn($q) => $q->where('status', 'aktif')->orWhereNull('status'))->where('kategori', 'jurusan')->count(),
            'biasa' => Mapel::where(fn($q) => $q->where('status', 'aktif')->orWhereNull('status'))->whereIn('kategori', ['biasa', 'umum', 'pilihan'])->count(),
        ];

        $gurus = User::where('role', 'guru')
            ->where(function ($q) {
                $q->where('status', 'aktif')->orWhereNull('status');
            })
            ->orderBy('name')
            ->get();

        return view('dashboard.admin.mapel', compact('mapels', 'gurus', 'counts', 'kategori'));
    }

    protected function resolveGuruIds(Request $request, $kategori = 'biasa')
    {
        $guruIds = $request->input('guru_ids', []);
        if (!is_array($guruIds)) {
            $guruIds = array_filter([$guruIds]);
        }

        $guruNames = $request->input('guru_names', []);
        if (is_string($guruNames)) {
            $decoded = json_decode($guruNames, true);
            if (is_array($decoded)) {
                $guruNames = $decoded;
            } elseif (strpos($guruNames, '|||') !== false) {
                $guruNames = explode('|||', $guruNames);
            } else {
                $guruNames = array_map('trim', explode(',', $guruNames));
            }
        }
        if (!is_array($guruNames)) {
            $guruNames = [];
        }

        foreach ($guruNames as $name) {
            $name = trim($name);
            if (empty($name)) continue;

            $user = User::where('name', $name)->where('role', 'guru')->first();
            if (!$user) {
                $user = User::whereRaw('LOWER(name) = ?', [strtolower($name)])->where('role', 'guru')->first();
            }
            if (!$user) {
                $user = User::where('name', 'like', "%{$name}%")->where('role', 'guru')->first();
            }

            if (!$user) {
                $baseUname = Str::slug($name, '_');
                if (empty($baseUname)) {
                    $baseUname = 'guru_' . time();
                }
                $uname = $baseUname;
                $c = 1;
                while (User::where('username', $uname)->exists()) {
                    $uname = $baseUname . '_' . $c;
                    $c++;
                }

                $user = User::create([
                    'name' => $name,
                    'username' => $uname,
                    'role' => 'guru',
                    'status' => 'aktif',
                    'password' => Hash::make('password'),
                ]);
            }

            if ($user && !in_array($user->id, $guruIds)) {
                $guruIds[] = $user->id;
            }
        }

        // Mapel Jurusan maksimal 10 guru, Mapel Biasa hanya 1 guru
        $maxGuru = ($kategori === 'jurusan') ? 10 : 1;
        return array_slice(array_unique(array_filter($guruIds)), 0, $maxGuru);
    }

    public function storeMapel(Request $request)
    {
        $validated = $request->validate([
            'kode_mapel' => 'required|string|max:50',
            'nama_mapel' => 'required|string|max:100',
            'kategori' => 'required|in:jurusan,biasa,pilihan,umum',
            'guru_ids' => 'nullable|array',
            'guru_id' => 'nullable|exists:users,id',
            'guru_names' => 'nullable',
        ], [
            'kode_mapel.required' => 'Kode mata pelajaran wajib diisi.',
            'nama_mapel.required' => 'Nama mata pelajaran wajib diisi.',
            'kategori.required' => 'Kategori mata pelajaran wajib dipilih.',
        ]);

        if (in_array($validated['kategori'], ['umum', 'pilihan'])) {
            $validated['kategori'] = 'biasa';
        }

        $guruIds = $this->resolveGuruIds($request, $validated['kategori']);
        $primaryGuruId = !empty($guruIds) ? $guruIds[0] : null;

        $existing = Mapel::where('kode_mapel', $validated['kode_mapel'])->first();

        if ($existing) {
            if ($existing->status === 'nonaktif') {
                $existing->update([
                    'nama_mapel' => $validated['nama_mapel'],
                    'kategori' => $validated['kategori'],
                    'guru_id' => $primaryGuruId,
                    'status' => 'aktif',
                    'alasan_hapus' => null,
                ]);

                if (!empty($guruIds)) {
                    $existing->pengampu()->sync($guruIds);
                    User::whereIn('id', $guruIds)->update(['mapel_id' => $existing->id]);
                }

                return redirect()->route('dashboard.mapel')
                    ->with('success', 'Mata pelajaran ' . $existing->nama_mapel . ' berhasil diaktifkan kembali!');
            }

            return back()->withErrors(['kode_mapel' => 'Kode mapel sudah terdaftar dan sedang aktif.'])->withInput();
        }

        $mapel = Mapel::create([
            'kode_mapel' => $validated['kode_mapel'],
            'nama_mapel' => $validated['nama_mapel'],
            'kategori' => $validated['kategori'],
            'guru_id' => $primaryGuruId,
            'status' => 'aktif',
            'alasan_hapus' => null,
        ]);

        if (!empty($guruIds)) {
            $mapel->pengampu()->sync($guruIds);
            User::whereIn('id', $guruIds)->update(['mapel_id' => $mapel->id]);
        }

        return redirect()->route('dashboard.mapel')
            ->with('success', 'Mata pelajaran ' . $mapel->nama_mapel . ' berhasil ditambahkan!');
    }

    public function updateMapel(Request $request, $id)
    {
        $mapel = Mapel::findOrFail($id);

        $validated = $request->validate([
            'kode_mapel' => 'required|string|max:50|unique:mapels,kode_mapel,' . $id . ',id',
            'nama_mapel' => 'required|string|max:100',
            'kategori' => 'required|in:jurusan,biasa,pilihan,umum',
            'guru_ids' => 'nullable|array',
            'guru_id' => 'nullable|exists:users,id',
            'guru_names' => 'nullable',
        ], [
            'kode_mapel.required' => 'Kode mata pelajaran wajib diisi.',
            'kode_mapel.unique' => 'Kode mapel sudah digunakan oleh mata pelajaran lain.',
            'nama_mapel.required' => 'Nama mata pelajaran wajib diisi.',
            'kategori.required' => 'Kategori mata pelajaran wajib dipilih.',
        ]);

        if (in_array($validated['kategori'], ['umum', 'pilihan'])) {
            $validated['kategori'] = 'biasa';
        }

        $guruIds = $this->resolveGuruIds($request, $validated['kategori']);
        $primaryGuruId = !empty($guruIds) ? $guruIds[0] : null;

        $mapel->update([
            'kode_mapel' => $validated['kode_mapel'],
            'nama_mapel' => $validated['nama_mapel'],
            'kategori' => $validated['kategori'],
            'guru_id' => $primaryGuruId,
        ]);

        if (!empty($guruIds)) {
            $mapel->pengampu()->sync($guruIds);
            User::whereIn('id', $guruIds)->update(['mapel_id' => $mapel->id]);
        } else {
            $mapel->pengampu()->detach();
        }

        return redirect()->route('dashboard.mapel')
            ->with('success', 'Data mata pelajaran berhasil diperbarui!');
    }

    public function destroyMapel(Request $request, $id)
    {
        $mapel = Mapel::findOrFail($id);

        $request->validate([
            'alasan' => 'required|string',
        ], [
            'alasan.required' => 'Alasan penghapusan mata pelajaran wajib diisi.',
        ]);

        $mapel->update([
            'status' => 'nonaktif',
            'alasan_hapus' => $request->alasan,
        ]);

        return redirect()->route('dashboard.mapel')
            ->with('success', 'Mata pelajaran ' . $mapel->nama_mapel . ' berhasil dinonaktifkan.');
    }

    // =========================================================================
    // 7. JADWAL PELAJARAN
    // =========================================================================
    public function jadwal(Request $request)
    {
        $kelases = Kelas::where(function ($q) {
            $q->where('status', 'aktif')->orWhereNull('status');
        })
        ->where('nama_kelas', 'not like', 'XII%')
        ->where('nama_kelas', 'not like', '12%')
        ->orderBy('nama_kelas')
        ->get();

        $mapels = Mapel::where(function ($q) {
            $q->where('status', 'aktif')->orWhereNull('status');
        })->orderByRaw("CASE WHEN kategori = 'jurusan' THEN 1 ELSE 2 END")
          ->orderBy('nama_mapel')
          ->get();

        $gurus = User::where('role', 'guru')
            ->where(function ($q) {
                $q->where('status', 'aktif')->orWhereNull('status');
            })
            ->orderBy('name')
            ->get();

        $selectedKelasId = $request->query('kelas_id', optional($kelases->first())->id_kelas);
        $selectedKelas = $kelases->firstWhere('id_kelas', $selectedKelasId) ?? $kelases->first();

        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

        $jadwals = collect();
        if ($selectedKelas) {
            $jadwals = JadwalPelajaran::with(['guru', 'mapelItem', 'kelas'])
                ->where('id_kelas', $selectedKelas->id_kelas)
                ->where(function ($q) {
                    $q->whereIn('status', ['aktif', 'ditiadakan'])->orWhereNull('status');
                })
                ->orderByRaw("CASE hari 
                    WHEN 'Senin' THEN 1 
                    WHEN 'Selasa' THEN 2 
                    WHEN 'Rabu' THEN 3 
                    WHEN 'Kamis' THEN 4 
                    WHEN 'Jumat' THEN 5 
                    ELSE 6 END")
                ->orderBy('jam_mulai')
                ->orderBy('jam_ke')
                ->get();
        }

        $selectedHari = $request->query('hari', 'Senin');

        return view('dashboard.admin.jadwal', compact(
            'kelases',
            'mapels',
            'gurus',
            'selectedKelas',
            'jadwals',
            'hariList',
            'selectedHari'
        ));
    }

    public function storeJadwal(Request $request)
    {
        $validated = $request->validate([
            'kelas_id' => 'required|exists:kelas,id_kelas',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat',
            'jam_ke' => 'nullable|integer|min:0|max:20',
            'mapel_id' => 'required|exists:mapels,id',
            'guru_id' => 'nullable|exists:users,id',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
        ], [
            'kelas_id.required' => 'Pilih kelas terlebih dahulu.',
            'hari.required' => 'Pilih hari pelaksanaan jadwal.',
            'mapel_id.required' => 'Pilih mata pelajaran atau kegiatan.',
            'jam_mulai.required' => 'Jam mulai wajib diisi.',
            'jam_selesai.required' => 'Jam selesai wajib diisi.',
        ]);

        $mapel = Mapel::find($validated['mapel_id']);
        $isKegiatan = $mapel && $mapel->kategori === 'kegiatan';

        if (!$isKegiatan && empty($validated['guru_id'])) {
            return back()->withErrors(['guru_id' => 'Pilih guru pengampu untuk mata pelajaran ini.'])->withInput();
        }

        $jamMulai = date('H:i:s', strtotime($validated['jam_mulai']));
        $jamSelesai = date('H:i:s', strtotime($validated['jam_selesai']));

        if ($request->filled('jam_ke')) {
            $jamKe = (int) $request->jam_ke;
        } elseif ($isKegiatan) {
            $jamKe = 0;
        } else {
            $countHari = JadwalPelajaran::where('id_kelas', $validated['kelas_id'])
                ->where('hari', $validated['hari'])
                ->where(function ($q) {
                    $q->whereIn('status', ['aktif', 'ditiadakan'])->orWhereNull('status');
                })
                ->where('jam_ke', '>', 0)
                ->count();
            $jamKe = $countHari + 1;
        }

        JadwalPelajaran::create([
            'id_kelas' => $validated['kelas_id'],
            'id_user' => $validated['guru_id'] ?? null,
            'id_mapel' => $validated['mapel_id'],
            'hari' => $validated['hari'],
            'jam_ke' => $jamKe,
            'jam_mulai' => $jamMulai,
            'jam_selesai' => $jamSelesai,
            'mapel' => $mapel ? $mapel->nama_mapel : 'Mata Pelajaran',
            'status' => 'aktif',
        ]);

        $labelSesi = ($jamKe > 0) ? 'Jam Ke-' . $jamKe : ($mapel ? $mapel->nama_mapel : 'Kegiatan');
        return redirect()->route('dashboard.jadwal', ['kelas_id' => $validated['kelas_id'], 'hari' => $validated['hari']])
            ->with('success', 'Jadwal hari ' . $validated['hari'] . ' (' . $labelSesi . ') berhasil disimpan.');
    }

    public function updateJadwal(Request $request, $id)
    {
        $jadwal = JadwalPelajaran::findOrFail($id);

        $validated = $request->validate([
            'kelas_id' => 'required|exists:kelas,id_kelas',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat',
            'jam_ke' => 'nullable|integer|min:0|max:20',
            'mapel_id' => 'required|exists:mapels,id',
            'guru_id' => 'nullable|exists:users,id',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
        ], [
            'kelas_id.required' => 'Pilih kelas terlebih dahulu.',
            'hari.required' => 'Pilih hari pelaksanaan jadwal.',
            'mapel_id.required' => 'Pilih mata pelajaran atau kegiatan.',
            'jam_mulai.required' => 'Jam mulai wajib diisi.',
            'jam_selesai.required' => 'Jam selesai wajib diisi.',
        ]);

        $mapel = Mapel::find($validated['mapel_id']);
        $isKegiatan = $mapel && $mapel->kategori === 'kegiatan';

        if (!$isKegiatan && empty($validated['guru_id'])) {
            return back()->withErrors(['guru_id' => 'Pilih guru pengampu untuk mata pelajaran ini.'])->withInput();
        }

        $jamMulai = date('H:i:s', strtotime($validated['jam_mulai']));
        $jamSelesai = date('H:i:s', strtotime($validated['jam_selesai']));

        $jamKe = $request->filled('jam_ke') ? (int) $request->jam_ke : ($isKegiatan ? 0 : $jadwal->jam_ke);

        $jadwal->update([
            'id_kelas' => $validated['kelas_id'],
            'id_user' => $validated['guru_id'] ?? null,
            'id_mapel' => $validated['mapel_id'],
            'hari' => $validated['hari'],
            'jam_ke' => $jamKe,
            'jam_mulai' => $jamMulai,
            'jam_selesai' => $jamSelesai,
            'mapel' => $mapel ? $mapel->nama_mapel : $jadwal->mapel,
        ]);

        $labelSesi = ($jamKe > 0) ? 'Jam Ke-' . $jamKe : ($mapel ? $mapel->nama_mapel : 'Kegiatan');
        return redirect()->route('dashboard.jadwal', ['kelas_id' => $validated['kelas_id'], 'hari' => $validated['hari']])
            ->with('success', 'Jadwal hari ' . $validated['hari'] . ' (' . $labelSesi . ') berhasil diperbarui.');
    }

    public function destroyJadwal(Request $request, $id)
    {
        $jadwal = JadwalPelajaran::findOrFail($id);

        $request->validate([
            'alasan' => 'required|string|min:3',
        ], [
            'alasan.required' => 'Alasan penonaktifan jadwal wajib diisi.',
            'alasan.min' => 'Alasan minimal 3 karakter.',
        ]);

        $jadwal->update([
            'status' => 'nonaktif',
            'alasan_hapus' => $request->alasan,
        ]);

        return redirect()->route('dashboard.jadwal', ['kelas_id' => $jadwal->id_kelas, 'hari' => $jadwal->hari])
            ->with('success', 'Jadwal pelajaran hari ' . $jadwal->hari . ' (Jam Ke-' . $jadwal->jam_ke . ') berhasil dinonaktifkan.');
    }

    public function shiftTimeJadwal(Request $request)
    {
        $validated = $request->validate([
            'kelas_id' => 'required',
            'hari' => 'required|in:Senin,Jumat',
            'mode' => 'required|in:maju,normal',
            'minutes' => 'nullable|integer|min:15|max:120',
        ]);

        $hari = $validated['hari'];
        $mode = $validated['mode'];
        $defaultMinutes = ($hari === 'Jumat') ? 30 : 40;
        $minutes = (int) ($request->minutes ?: $defaultMinutes);

        // Tentukan daftar kelas yang dituju
        if ($request->boolean('apply_all') || $validated['kelas_id'] === 'all') {
            $kelasIds = Kelas::where(function ($q) {
                $q->where('status', 'aktif')->orWhereNull('status');
            })
            ->where('nama_kelas', 'not like', 'XII%')
            ->where('nama_kelas', 'not like', '12%')
            ->pluck('id_kelas')->toArray();
        } else {
            $kelasIds = [(int) $validated['kelas_id']];
        }

        $keyword = $hari === 'Senin' ? '%Upacara%' : '%Pembiasaan%';

        foreach ($kelasIds as $kId) {
            $kegiatan = JadwalPelajaran::where('id_kelas', $kId)
                ->where('hari', $hari)
                ->where('mapel', 'like', $keyword)
                ->first();

            $lessons = JadwalPelajaran::where('id_kelas', $kId)
                ->where('hari', $hari)
                ->where('status', '!=', 'nonaktif')
                ->when($kegiatan, function ($q) use ($kegiatan) {
                    $q->where('id_jadwal', '!=', $kegiatan->id_jadwal);
                })
                ->get();

            if ($mode === 'maju') {
                if ($kegiatan && $kegiatan->status !== 'ditiadakan') {
                    $kegiatan->update(['status' => 'ditiadakan']);
                    foreach ($lessons as $l) {
                        $newStart = date('H:i:s', max(0, strtotime($l->jam_mulai) - ($minutes * 60)));
                        $newEnd = date('H:i:s', max(0, strtotime($l->jam_selesai) - ($minutes * 60)));
                        $l->update(['jam_mulai' => $newStart, 'jam_selesai' => $newEnd]);
                    }
                }
            } else { // mode === 'normal'
                if ($kegiatan && $kegiatan->status === 'ditiadakan') {
                    $kegiatan->update(['status' => 'aktif']);
                    foreach ($lessons as $l) {
                        $newStart = date('H:i:s', strtotime($l->jam_mulai) + ($minutes * 60));
                        $newEnd = date('H:i:s', strtotime($l->jam_selesai) + ($minutes * 60));
                        $l->update(['jam_mulai' => $newStart, 'jam_selesai' => $newEnd]);
                    }
                }
            }
        }

        $namaKegiatan = $hari === 'Senin' ? 'Upacara Bendera' : 'Pembiasaan Jum\'at';
        $redirectKelasId = ($validated['kelas_id'] !== 'all') ? $validated['kelas_id'] : optional(Kelas::first())->id_kelas;

        if ($mode === 'maju') {
            return redirect()->route('dashboard.jadwal', ['kelas_id' => $redirectKelasId, 'hari' => $hari])
                ->with('success', "Mode Jam Maju Aktif: Kegiatan {$namaKegiatan} ditiadakan, seluruh jam pelajaran dimajukan {$minutes} menit (pulang lebih awal).");
        } else {
            return redirect()->route('dashboard.jadwal', ['kelas_id' => $redirectKelasId, 'hari' => $hari])
                ->with('success', "Mode Jam Normal Aktif: Kegiatan {$namaKegiatan} dilaksanakan, seluruh jam pelajaran telah dikembalikan ke waktu normal.");
        }
    }

    // =========================================================================
    // 8. MANAJEMEN USER (4 Role: Admin, Waka, Guru Piket, Sekretaris Kelas)
    // =========================================================================
    public function user(Request $request)
    {
        $roleMap = [
            'admin' => ['label' => 'Admin', 'class' => 'bg-purple-100 text-purple-700'],
            'waka' => ['label' => 'Waka', 'class' => 'bg-amber-100 text-amber-700'],
            'piket' => ['label' => 'Guru Piket', 'class' => 'bg-teal-100 text-teal-700'],
            'guru' => ['label' => 'Guru Pengajar', 'class' => 'bg-emerald-100 text-emerald-700'],
            'sekretaris' => ['label' => 'Sekretaris Kelas', 'class' => 'bg-sky-100 text-sky-700'],
        ];

        // Ambil akun pengguna sistem yang dikelola di Manajemen User
        $rawUsers = User::with(['kelas', 'mapel'])
            ->where(function ($q) {
                $q->where('is_system_user', true)
                  ->orWhereIn('role', ['admin', 'waka', 'piket', 'sekretaris']);
            })
            ->orderBy('role')
            ->orderBy('name')
            ->get();

        $usersForJs = $rawUsers->map(function ($u) use ($roleMap) {
            $r = $u->role;
            $meta = $roleMap[$r] ?? ['label' => ucfirst($r), 'class' => 'bg-gray-100 text-gray-700'];

            return [
                'id' => $u->id,
                'name' => $u->name,
                'identifier' => $u->username ?: ($u->nip ?: '-'),
                'username' => $u->username,
                'nip' => $u->nip,
                'role' => $meta['label'],
                'raw_role' => $r,
                'phone' => $u->no_hp ?: '-',
                'status' => $u->status ?? 'aktif',
                'alasan_hapus' => $u->alasan_hapus ?: '',
                'id_kelas' => $u->id_kelas,
                'nama_kelas' => optional($u->kelas)->nama_kelas ?? '',
                'mapel_id' => $u->mapel_id,
                'nama_mapel' => optional($u->mapel)->nama_mapel ?? '',
                'roleClass' => $meta['class'],
            ];
        });

        $kelases = Kelas::where(function ($q) {
            $q->where('status', 'aktif')->orWhereNull('status');
        })->orderBy('nama_kelas')->get();

        $mapels = Mapel::where(function ($q) {
            $q->where('status', 'aktif')->orWhereNull('status');
        })->orderBy('nama_mapel')->get();

        return view('dashboard.admin.manajemen-user', compact('usersForJs', 'kelases', 'mapels'));
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'role' => 'required|in:admin,waka,piket,sekretaris,guru',
            'nama' => 'required|string|max:255',
            'identifier' => 'nullable|string|max:100',
            'nip' => 'nullable|string|max:30',
            'phone' => 'nullable|string|max:20',
            'id_kelas' => 'nullable|exists:kelas,id_kelas',
            'mapel_id' => 'nullable|exists:mapels,id',
            'password' => 'required|string|min:6',
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        $username = ($validated['identifier'] ?? null) ?: (($validated['nip'] ?? null) ?: Str::slug($validated['nama'], '_'));
        $baseUsername = $username;
        $counter = 1;
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . '_' . $counter;
            $counter++;
        }

        User::create([
            'name' => $validated['nama'],
            'username' => $username,
            'nip' => $validated['nip'] ?? null,
            'role' => $validated['role'],
            'is_system_user' => true,
            'id_kelas' => $validated['role'] === 'sekretaris' ? ($validated['id_kelas'] ?? null) : null,
            'mapel_id' => $validated['role'] === 'guru' ? ($validated['mapel_id'] ?? null) : null,
            'no_hp' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'status' => 'aktif',
            'alasan_hapus' => null,
        ]);

        return redirect()->route('admin.manajemen-user')->with('success', 'Akun pengguna baru (' . $validated['nama'] . ') berhasil ditambahkan ke sistem!');
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string',
            'username' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'nip' => 'nullable|string|max:30',
            'id_kelas' => 'nullable|exists:kelas,id_kelas',
            'mapel_id' => 'nullable|exists:mapels,id',
            'status' => 'nullable|in:aktif,nonaktif',
            'password' => 'nullable|string|min:6',
        ]);

        $reverseRoleMap = [
            'Admin' => 'admin',
            'Waka' => 'waka',
            'Guru Piket' => 'piket',
            'Guru Pengajar' => 'guru',
            'Sekretaris Kelas' => 'sekretaris',
            'admin' => 'admin',
            'waka' => 'waka',
            'piket' => 'piket',
            'guru' => 'guru',
            'sekretaris' => 'sekretaris',
        ];

        $rawRole = $reverseRoleMap[$validated['role']] ?? $user->role;

        $updateData = [
            'name' => $validated['name'],
            'role' => $rawRole,
            'no_hp' => $validated['phone'] ?? $user->no_hp,
            'nip' => $validated['nip'] ?? $user->nip,
            'id_kelas' => $rawRole === 'sekretaris' ? ($validated['id_kelas'] ?? $user->id_kelas) : null,
            'mapel_id' => $rawRole === 'guru' ? ($validated['mapel_id'] ?? $user->mapel_id) : null,
            'is_system_user' => true,
        ];

        if (!empty($validated['status'])) {
            $updateData['status'] = $validated['status'];
            if ($validated['status'] === 'aktif') {
                $updateData['alasan_hapus'] = null;
            }
        }

        if (!empty($validated['username'])) {
            $updateData['username'] = $validated['username'];
        }

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $oldName = $user->name;
        $user->update($updateData);

        // Sinkronisasi otomatis ke data kelas jika wali kelas
        if ($oldName !== $validated['name']) {
            Kelas::where('wali_kelas', $oldName)->update(['wali_kelas' => $validated['name']]);
        }

        return redirect()->route('admin.manajemen-user')->with('success', 'Informasi akun pengguna ' . $validated['name'] . ' berhasil diperbarui!');
    }

    public function destroyUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $alasan = $request->input('alasan', 'Dinonaktifkan oleh administrator pada Manajemen User');

        $user->update([
            'status' => 'nonaktif',
            'alasan_hapus' => $alasan,
        ]);

        return redirect()->route('admin.manajemen-user')->with('success', 'Akun pengguna ' . $user->name . ' berhasil dinonaktifkan.');
    }

    public function restoreUser($id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'status' => 'aktif',
            'alasan_hapus' => null,
        ]);

        return redirect()->route('admin.manajemen-user')->with('success', 'Akun pengguna ' . $user->name . ' berhasil diaktifkan kembali!');
    }

    // =========================================================================
    // 9. LAPORAN GANTI PASSWORD USER
    // =========================================================================
    public function terimaResetPassword(Request $request, $id)
    {
        $request->validate([
            'password_baru' => 'required|min:4',
            'catatan' => 'nullable|string',
        ], [
            'password_baru.required' => 'Password baru wajib diisi.',
            'password_baru.min' => 'Password baru minimal 4 karakter.',
        ]);

        $laporan = PasswordResetRequest::findOrFail($id);
        $user = $laporan->user ?? User::where('username', $laporan->username)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'User terkait laporan ini tidak ditemukan di database.');
        }

        // Update password user di database
        $user->update([
            'password' => Hash::make($request->password_baru),
        ]);

        // Tandai status laporan sebagai disetujui
        $laporan->update([
            'status' => 'disetujui',
            'catatan_admin' => $request->catatan ?? ('Password berhasil diubah oleh Admin pada ' . now()->translatedFormat('d M Y H:i')),
            'handled_by' => auth()->id(),
            'handled_at' => now(),
        ]);

        // Buat notifikasi jika kelas Notifikasi ada
        if (class_exists(Notifikasi::class)) {
            try {
                Notifikasi::create([
                    'id_user' => $user->id,
                    'id_kelas' => $user->id_kelas ?? null,
                    'judul' => 'Permintaan Reset Password Disetujui',
                    'pesan' => 'Permintaan reset password Anda telah disetujui oleh Administrator. Password baru Anda telah aktif.',
                    'tipe' => 'info',
                    'is_read' => false,
                ]);
            } catch (\Throwable $e) {
                // Abaikan jika tabel notifikasi opsional
            }
        }

        return redirect()->back()->with('success', 'Permintaan ganti password untuk ' . $user->name . ' (' . $user->username . ') berhasil diterima dan password baru telah diaktifkan!');
    }

    public function tolakResetPassword(Request $request, $id)
    {
        $laporan = PasswordResetRequest::findOrFail($id);

        $laporan->update([
            'status' => 'ditolak',
            'catatan_admin' => $request->input('catatan', 'Permintaan ditolak oleh Administrator.'),
            'handled_by' => auth()->id(),
            'handled_at' => now(),
        ]);

        return redirect()->back()->with('info', 'Permintaan ganti password untuk ' . $laporan->nama . ' telah ditolak.');
    }
}
