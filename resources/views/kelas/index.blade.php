@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Manajemen Data Kelas</h1>
        <p class="text-sm text-gray-500">Kelola daftar kelas, tingkat, dan wali kelas.</p>
    </div>
    
    <!-- Search Bar & Tombol Tambah -->
    <div class="flex items-center gap-3">
        <div class="relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </span>
            <input type="text" placeholder="Cari kelas..." class="w-64 pl-9 pr-4 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 shadow-sm">
        </div>
        <a href="/preview/kelas/create" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 text-sm font-medium shadow-sm transition whitespace-nowrap">
            + Tambah Kelas Baru
        </a>
    </div>
</div>

<!-- Tabel Kelas -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-emerald-50/50 border-b border-gray-100 text-gray-600 text-sm font-semibold">
                <th class="p-4">No</th>
                <th class="p-4">Nama Kelas</th>
                <th class="p-4">Wali Kelas</th>
                <th class="p-4">Jumlah Siswa</th>
                <th class="p-4 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 text-gray-700 text-sm">
            <tr>
                <td class="p-4">01</td>
                <td class="p-4 font-semibold text-gray-900">XI RPL 1</td>
                <td class="p-4">Budi Santoso, S.Kom</td>
                <td class="p-4">32 Siswa</td>
                <td class="p-4 text-center space-x-2">
                    <a href="/preview/kelas/siswa" class="inline-flex items-center justify-center p-1.5 bg-emerald-50 text-emerald-600 hover:bg-emerald-100 rounded-lg transition" title="Lihat Siswa">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </a>
                    <a href="/preview/kelas/edit" class="inline-flex items-center justify-center p-1.5 bg-amber-50 text-amber-600 hover:bg-amber-100 rounded-lg transition" title="Edit">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </a>
                    <a href="/preview/kelas/delete" class="inline-flex items-center justify-center p-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg transition" title="Hapus">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </a>
                </td>
            </tr>
        </tbody>
    </table>
</div>
@endsection