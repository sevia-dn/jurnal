{{-- resources/views/dashboard/jadwal.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="p-6 sm:p-10 font-sans">

    {{-- Header Halaman --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-emerald-800">Manajemen Mata Pelajaran</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola daftar mata pelajaran yang diajarkan di sekolah.</p>
    </div>

    {{-- ================= FORM TAMBAH MAPEL ================= --}}
    <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-6 mb-8">
        <h2 class="text-lg font-semibold text-emerald-800 mb-4">Tambah Mata Pelajaran Baru</h2>

        <form method="POST" action="#" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            @csrf

            <div class="flex flex-col">
                <label class="text-sm font-medium text-gray-700 mb-1">Kode Mapel</label>
                <input type="text" name="kode_mapel" placeholder="MAT-301"
                       class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-400">
            </div>

            <div class="flex flex-col md:col-span-2">
                <label class="text-sm font-medium text-gray-700 mb-1">Nama Mapel</label>
                <input type="text" name="nama_mapel" placeholder="Advanced Calculus"
                       class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-400">
            </div>

            <div class="flex flex-col">
                <label class="text-sm font-medium text-gray-700 mb-1">Guru Pengampu</label>
                <select name="guru_id"
                        class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                    <option value="">Pilih Guru</option>
                    @foreach($gurus ?? [] as $guru)
                        <option value="{{ is_array($guru) ? $guru['id'] : $guru->id }}">
                            {{ is_array($guru) ? $guru['nama'] : $guru->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-4">
                <button type="submit"
                        class="bg-emerald-700 hover:bg-emerald-800 text-white font-medium px-5 py-2 rounded-lg transition">
                    + Tambah Mapel
                </button>
            </div>
        </form>
    </div>

    {{-- ================= TABEL DAFTAR MAPEL ================= --}}
    <div class="bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-emerald-50 text-emerald-800 text-sm">
                <tr>
                    <th class="px-4 py-3">No</th>
                    <th class="px-4 py-3">Kode</th>
                    <th class="px-4 py-3">Nama Mapel</th>
                    <th class="px-4 py-3">Guru Pengampu</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                @forelse($mapels ?? [] as $i => $mapel)
                    <tr class="hover:bg-emerald-50/50">
                        <td class="px-4 py-3">{{ $i + 1 }}</td>
                        <td class="px-4 py-3 font-medium text-emerald-700">{{ $mapel['kode'] }}</td>
                        <td class="px-4 py-3">{{ $mapel['nama'] }}</td>
                        <td class="px-4 py-3">{{ $mapel['guru'] }}</td>
                        <td class="p-4 text-center space-x-2">
                            <button class="text-gray-400 hover:text-amber-600 transition" title="Edit"><svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></button>
                            <button onclick="openModal('modalHapusSiswa')" class="text-gray-400 hover:text-red-600 transition" title="Hapus"><svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-gray-400">Belum ada data mapel.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection