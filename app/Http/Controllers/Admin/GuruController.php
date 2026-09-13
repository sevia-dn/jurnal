<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mapel;
use App\Models\User;
use Illuminate\Http\Request;

class GuruController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $users = User::where('role', 'guru')
            ->with('mapel')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('nip', 'like', "%{$search}%")
                        ->orWhereHas('mapel', function ($q2) use ($search) {
                            $q2->where('nama_mapel', 'like', "%{$search}%");
                        });
                });
            })
            ->get();

        $mapels = Mapel::all();

        return view('dashboard.admin.guru', compact('users', 'mapels', 'search'));
    }



    // Simpan guru baru
    public function store(Request $request)
    {
        $request->validate([
            'nip'      => 'nullable|string|unique:users,nip',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:6',
            'nama'     => 'required|string|max:255',
            'mapel_id' => 'nullable|exists:mapels,id',
            'no_hp'    => 'nullable|string|max:20',
        ]);

        User::create([
            'name'     => $request->nama,
            'username' => $request->username,
            'nip'      => $request->nip,
            'password' => bcrypt($request->password),
            'role'     => 'guru',
            'no_hp'    => $request->no_hp,
            'mapel_id' => $request->mapel_id,
        ]);

        return redirect()->route('admin.guru')->with('success', 'Guru baru berhasil ditambahkan.');
    }
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.guru')->with('success', 'Data guru berhasil dihapus.');
    }
    public function edit(User $user)
    {
        $mapels = Mapel::all();

        return view('dashboard.admin.guru-edit', compact('user', 'mapels'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'nip'      => 'nullable|string|unique:users,nip,' . $user->id,
            'username' => 'required|string|unique:users,username,' . $user->id,
            'password' => 'nullable|string|min:6',
            'nama'     => 'required|string|max:255',
            'mapel_id' => 'nullable|exists:mapels,id',
            'no_hp'    => 'nullable|string|max:20',
        ]);

        $data = [
            'name'     => $request->nama,
            'username' => $request->username,
            'nip'      => $request->nip,
            'no_hp'    => $request->no_hp,
            'mapel_id' => $request->mapel_id,
        ];

        // Password cuma diupdate kalau diisi. Kalau dikosongin, password lama tetap dipakai.
        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.guru')->with('success', 'Data guru berhasil diperbarui.');
    }
}
