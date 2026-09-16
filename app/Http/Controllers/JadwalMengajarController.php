<?php

namespace App\Http\Controllers;

use App\Models\JadwalMengajar;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\User;
use Illuminate\Http\Request;

class JadwalMengajarController extends Controller
{
    public function index()
    {
        $jadwals = JadwalMengajar::with([
            'guru',
            'kelas',
            'mapel'
        ])
        ->orderByRaw("
            FIELD(
                hari,
                'Senin',
                'Selasa',
                'Rabu',
                'Kamis',
                'Jumat'
            )
        ")
        ->orderBy('jam_mulai')
        ->get();

        return view(
            'dashboard.admin.jadwal',
            compact('jadwals')
        );
    }

    public function create()
    {
        $guru = User::where('role', 'guru')
            ->orderBy('name')
            ->get();

        $kelas = Kelas::orderBy('nama_kelas')
            ->get();

        $mapels = Mapel::orderBy('nama_mapel')
            ->get();

        return view(
            'jadwal.create',
            compact('guru', 'kelas', 'mapels')
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_user' => 'required|exists:users,id',
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'id_mapel' => 'required|exists:mapels,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat',
            'jam_mulai' => 'required|integer|min:1|max:13',
            'jam_selesai' => 'required|integer|min:1|max:13|gte:jam_mulai',
        ]);

        JadwalMengajar::create($validated);

        return redirect()
            ->route('dashboard.jadwal')
            ->with('success', 'Jadwal berhasil ditambahkan.');
    }
}