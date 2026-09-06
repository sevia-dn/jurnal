{{--
    resources/views/mapel/index.blade.php
    Halaman Master Data Mapel: form tambah + tabel daftar mapel
    Catatan: $mapels dikirim dari route sebagai data dummy (collection of objects/array)
--}}
@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-emerald-800">Manajemen Mata Pelajaran</h1>
        <p class="text-emerald-600 text-sm">Kelola daftar mata pelajaran yang diajarkan di sekolah.</p>
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
                        <option value="{{ $guru->id }}">{{ $guru->nama }}</option>
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
                @forelse($mapels as $i => $mapel)
                    <tr class="hover:bg-emerald-50/50">
                        <td class="px-4 py-3">{{ $i + 1 }}</td>
                        <td class="px-4 py-3 font-medium text-emerald-700">{{ $mapel['kode'] }}</td>
                        <td class="px-4 py-3">{{ $mapel['nama'] }}</td>
                        <td class="px-4 py-3">{{ $mapel['guru'] }}</td>
                        <td class="px-4 py-3 text-right space-x-2">
                            <a href="#" class="text-emerald-600 hover:underline text-sm">Edit</a>
                            <a href="#" class="text-red-500 hover:underline text-sm">Hapus</a>
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