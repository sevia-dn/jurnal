@extends('layouts.app')

@section('title', 'Manajemen Data Guru')

@section('content')
<div class="p-6 sm:p-10 font-sans">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-emerald-800">Manajemen Data Guru</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola informasi staf pengajar sekolah Anda.</p>
        </div>
        <input type="text" placeholder="Cari..."
               class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-56 focus:outline-none focus:ring-2 focus:ring-emerald-400">
    </div>

    {{-- ================= FORM TAMBAH USER ================= --}}
    <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-6 mb-8">
        <h2 class="text-lg font-semibold text-emerald-800 mb-4">Tambah Guru Baru</h2>

        <!-- ... (Biarkan form temanmu sama seperti aslinya) ... -->
        <form method="POST" action="#" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            @csrf
            <div class="flex flex-col">
                <label class="text-sm font-medium text-gray-700 mb-1">NIP</label>
                <input type="text" name="nip" placeholder="198005122005011002"
                       class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-400">
            </div>

            <div class="flex flex-col">
                <label class="text-sm font-medium text-gray-700 mb-1">Nama Guru</label>
                <input type="text" name="nama" placeholder="Budi Waluyo, S.Pd"
                       class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-400">
            </div>

            <div class="flex flex-col">
                <label class="text-sm font-medium text-gray-700 mb-1">Mata Pelajaran</label>
                <select name="mapel_id"
                        class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                    <option value="">Pilih Mapel</option>
                    @foreach($mapels ?? [] as $mapel)
                        <option value="{{ $mapel['kode'] ?? '' }}">{{ $mapel['nama'] ?? $mapel }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col">
                <label class="text-sm font-medium text-gray-700 mb-1">No. HP</label>
                <input type="text" name="no_hp" placeholder="081234567890"
                       class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-400">
            </div>

            <div class="md:col-span-4">
                <button type="submit"
                        class="bg-emerald-700 hover:bg-emerald-800 text-white font-medium px-5 py-2 rounded-lg transition">
                    + Tambah Guru Baru
                </button>
            </div>
        </form>
    </div>

    {{-- ================= TABEL DAFTAR USER/GURU ================= --}}
    <div class="bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-emerald-50 text-emerald-800 text-sm">
                <tr>
                    <th class="px-4 py-3">NIP</th>
                    <th class="px-4 py-3">Nama Guru</th>
                    <th class="px-4 py-3">Mata Pelajaran</th>
                    <th class="px-4 py-3">No. HP</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                @forelse($users ?? [] as $user)
                    <tr class="hover:bg-emerald-50/50">
                        <td class="px-4 py-3">{{ $user['nip'] }}</td>
                        <td class="px-4 py-3 flex items-center gap-2">
                            <span class="w-7 h-7 rounded-full bg-emerald-600 text-white text-xs flex items-center justify-center font-semibold">
                                {{ collect(explode(' ', $user['nama']))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->implode('') }}
                            </span>
                            {{ $user['nama'] }}
                        </td>
                        <td class="px-4 py-3">{{ $user['mapel'] }}</td>
                        <td class="px-4 py-3">{{ $user['no_hp'] }}</td>
                        <td class="p-4 text-center space-x-2">
                            <button class="text-gray-400 hover:text-amber-600 transition" title="Edit"><svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></button>
                            <button onclick="openModal('modalHapusSiswa')" class="text-gray-400 hover:text-red-600 transition" title="Hapus"><svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-gray-400">Belum ada data guru.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection