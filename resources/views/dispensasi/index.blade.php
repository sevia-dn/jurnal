{{--
    resources/views/dispensasi/index.blade.php
    Dashboard Approval Dispensasi (untuk Waka & Piket)
    Catatan: $dispensasis dikirim dari route sebagai data dummy
--}}
@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-emerald-800">Dashboard Persetujuan Dispensasi</h1>
        <p class="text-emerald-600 text-sm">Periksa dan validasi pengajuan dispensasi yang menunggu persetujuan.</p>
    </div>

    {{-- ================= RINGKASAN ================= --}}
    <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-6 mb-6 flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-emerald-700 uppercase tracking-wide">Menunggu Validasi</p>
            <p class="text-3xl font-bold text-emerald-800 mt-1">
                {{ collect($dispensasis)->where('status', 'Menunggu')->count() }}
            </p>
            <p class="text-sm text-emerald-600">Pengajuan menunggu hari ini</p>
        </div>
        <div class="flex gap-2">
            <button class="px-4 py-2 rounded-lg bg-emerald-700 text-white text-sm font-medium">Semua</button>
            <button class="px-4 py-2 rounded-lg bg-white border border-emerald-200 text-emerald-700 text-sm font-medium">Hari Ini</button>
            <button class="px-4 py-2 rounded-lg bg-white border border-emerald-200 text-emerald-700 text-sm font-medium">Minggu Ini</button>
        </div>
    </div>

    {{-- ================= TABEL PENGAJUAN DISPENSASI ================= --}}
    <div class="bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-emerald-50 text-emerald-800 text-sm">
                <tr>
                    <th class="px-4 py-3">No</th>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Jenis Dispensasi</th>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                @forelse($dispensasis as $i => $item)
                    <tr class="hover:bg-emerald-50/50">
                        <td class="px-4 py-3">{{ $i + 1 }}</td>
                        <td class="px-4 py-3 font-medium">{{ $item['nama'] }}</td>
                        <td class="px-4 py-3">{{ $item['jenis'] }}</td>
                        <td class="px-4 py-3">{{ $item['tanggal'] }}</td>
                        <td class="px-4 py-3">
                            @php
                                $badge = match($item['status']) {
                                    'Menunggu' => 'bg-yellow-100 text-yellow-700',
                                    'Disetujui' => 'bg-emerald-100 text-emerald-700',
                                    'Ditolak' => 'bg-red-100 text-red-700',
                                    default => 'bg-gray-100 text-gray-600',
                                };
                            @endphp
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $badge }}">
                                {{ $item['status'] }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right space-x-2">
                            <a href="#" class="inline-block px-3 py-1 rounded-lg border border-gray-200 text-gray-600 text-xs hover:bg-gray-50">Detail</a>
                            <button class="inline-block px-3 py-1 rounded-lg bg-red-50 text-red-600 text-xs hover:bg-red-100">Tolak</button>
                            <button class="inline-block px-3 py-1 rounded-lg bg-emerald-700 text-white text-xs hover:bg-emerald-800">Terima</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-gray-400">Tidak ada pengajuan dispensasi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection