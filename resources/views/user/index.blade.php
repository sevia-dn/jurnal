{{--
    resources/views/user/index.blade.php
    Halaman Master Data User/Guru: form tambah + tabel daftar user
    Catatan: $users dikirim dari route sebagai data dummy
--}}
@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-emerald-800">Manajemen Data Guru</h1>
            <p class="text-emerald-600 text-sm">Kelola informasi staf pengajar sekolah Anda.</p>
        </div>
        <input type="text" placeholder="Cari..."
               class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-56 focus:outline-none focus:ring-2 focus:ring-emerald-400">
    </div>

    {{-- ================= FORM TAMBAH USER ================= --}}
    <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-6 mb-8">
        <h2 class="text-lg font-semibold text-emerald-800 mb-4">Tambah Guru Baru</h2>

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
                @forelse($users as $user)
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
                        <td class="px-4 py-3 text-right space-x-2">
                            <a href="#" class="text-emerald-600 hover:underline text-sm">Edit</a>
                            <a href="#" class="text-red-500 hover:underline text-sm">Hapus</a>
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