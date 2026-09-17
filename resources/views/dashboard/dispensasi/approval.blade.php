@extends('layouts.app')

@section('title', 'Persetujuan Dispensasi Siswa - Waka Kesiswaan')

@section('sidebar')
    @include('layouts.guru-pengajar.sidebar', ['activePage' => 'utama'])
@endsection

@section('navbar')
    @include('layouts.guru-pengajar.navbar', ['activePage' => 'utama'])
@endsection

@section('content')
<div class="mx-auto w-full max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
    {{-- Header Banner --}}
    <div class="rounded-2xl bg-gradient-to-r from-purple-900 to-indigo-900 p-6 text-white shadow-xl">
        <div class="flex items-center justify-between">
            <span class="rounded-full bg-purple-700/60 px-3 py-1 text-xs font-semibold text-purple-200 border border-purple-500/30">
                <i class="bi bi-shield-check me-1"></i> Hak Persetujuan Waka
            </span>
            <span class="text-xs text-purple-300 font-mono">ID: #DISP-{{ $dispensasi->id }}</span>
        </div>
        <h1 class="mt-3 text-2xl font-bold sm:text-3xl">Persetujuan Dispensasi Siswa</h1>
        <p class="mt-1 text-sm text-purple-200">Silakan tinjau alasan dan dokumen pengajuan dispensasi dari Guru Piket.</p>
    </div>

    @if(session('success'))
        <div class="mt-4 rounded-xl bg-emerald-100 border border-emerald-300 p-4 text-emerald-900 font-semibold flex items-center gap-2">
            <i class="bi bi-check-circle-fill text-emerald-600 text-xl"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mt-4 rounded-xl bg-rose-100 border border-rose-300 p-4 text-rose-900 font-semibold flex items-center gap-2">
            <i class="bi bi-exclamation-triangle-fill text-rose-600 text-xl"></i>
            {{ session('error') }}
        </div>
    @endif

    {{-- Detail Dispensasi Card --}}
    <div class="mt-6 rounded-2xl bg-white p-6 shadow-md border border-slate-200 space-y-6">
        <div class="flex flex-wrap items-center justify-between border-b border-slate-100 pb-4 gap-2">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Nama Siswa</p>
                <h2 class="text-xl font-bold text-slate-900 mt-0.5">{{ $dispensasi->nama }} <span class="text-sm font-semibold text-purple-700 font-sans">({{ $dispensasi->siswa?->kelas?->nama_kelas ?? 'Umum' }})</span></h2>
            </div>
            <div>
                @if($dispensasi->status_waka === 'disetujui')
                    <span class="rounded-full bg-emerald-100 px-4 py-1.5 text-xs font-bold text-emerald-800 border border-emerald-200">
                        <i class="bi bi-check-circle-fill me-1"></i> Disetujui
                    </span>
                @elseif($dispensasi->status_waka === 'ditolak')
                    <span class="rounded-full bg-rose-100 px-4 py-1.5 text-xs font-bold text-rose-800 border border-rose-200">
                        <i class="bi bi-x-circle-fill me-1"></i> Ditolak
                    </span>
                @else
                    <span class="rounded-full bg-amber-100 px-4 py-1.5 text-xs font-bold text-amber-800 border border-amber-300 animate-pulse">
                        <i class="bi bi-clock-history me-1"></i> Menunggu Persetujuan Waka
                    </span>
                @endif
            </div>
        </div>

        <div class="grid gap-6 sm:grid-cols-2 text-sm">
            <div class="rounded-xl bg-slate-50 p-4 border border-slate-100">
                <p class="text-xs font-semibold text-slate-500 uppercase">Jenis Dispensasi</p>
                <p class="mt-1 font-bold text-slate-800 text-base">{{ $dispensasi->jenis_dispensasi }}</p>
            </div>
            <div class="rounded-xl bg-slate-50 p-4 border border-slate-100">
                <p class="text-xs font-semibold text-slate-500 uppercase">Waktu & Durasi Dispensasi</p>
                <p class="mt-1 font-bold text-slate-800 text-base">
                    {{ $dispensasi->deskripsi_waktu }}
                </p>
            </div>
            <div class="sm:col-span-2 rounded-xl bg-slate-50 p-4 border border-slate-100">
                <p class="text-xs font-semibold text-slate-500 uppercase">Alasan / Alasan Keperluan</p>
                <p class="mt-2 text-slate-800 leading-relaxed font-medium bg-white p-3 rounded-lg border border-slate-200">
                    "{{ $dispensasi->alasan }}"
                </p>
            </div>
            <div class="sm:col-span-2 rounded-xl bg-slate-50 p-4 border border-slate-100 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase">Diajukan Oleh Guru Piket</p>
                    <p class="mt-0.5 font-semibold text-slate-800">{{ $dispensasi->pembuat?->name ?? 'Guru Piket' }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs font-semibold text-slate-500 uppercase">Waktu Pengajuan</p>
                    <p class="mt-0.5 text-xs text-slate-600 font-mono">{{ $dispensasi->created_at ? $dispensasi->created_at->format('d/m/Y H:i') : '-' }}</p>
                </div>
            </div>
        </div>

        @if($dispensasi->bukti)
            <div class="rounded-xl border border-slate-200 p-4">
                <p class="text-xs font-semibold text-slate-500 uppercase mb-2">Dokumentasi / Bukti Pendukung</p>
                <a href="{{ asset('storage/' . $dispensasi->bukti) }}" target="_blank" class="inline-flex items-center gap-2 text-emerald-700 font-semibold hover:underline">
                    <i class="bi bi-file-earmark-pdf-fill text-xl"></i> Lihat Lampiran Bukti
                </a>
            </div>
        @endif

        {{-- Form Aksi Persetujuan Waka --}}
        @if($dispensasi->status_waka === 'menunggu')
            <form action="{{ route('dispensasi.process', $dispensasi->id) }}" method="POST" class="pt-4 border-t border-slate-100">
                @csrf
                <label class="block mb-4">
                    <span class="text-sm font-semibold text-slate-700">Catatan Waka (Opsional)</span>
                    <textarea name="catatan_waka" rows="3" placeholder="Tuliskan catatan atau instruksi tambahan jika ada..." class="mt-2 w-full rounded-xl border border-slate-200 p-3 text-sm focus:border-purple-600 focus:ring-4 focus:ring-purple-100 outline-none transition"></textarea>
                </label>

                <div class="flex flex-col sm:flex-row items-center gap-3">
                    <button type="submit" name="keputusan" value="disetujui" class="w-full sm:w-1/2 flex items-center justify-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3.5 shadow-md transition">
                        <i class="bi bi-check-circle-fill text-lg"></i> Setujui Dispensasi
                    </button>
                    <button type="submit" name="keputusan" value="ditolak" class="w-full sm:w-1/2 flex items-center justify-center gap-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold py-3.5 shadow-md transition">
                        <i class="bi bi-x-circle-fill text-lg"></i> Tolak Dispensasi
                    </button>
                </div>
            </form>
        @else
            <div class="pt-4 border-t border-slate-100 rounded-xl bg-slate-50 p-5 text-center">
                <p class="text-xs text-slate-500 font-semibold uppercase">Status Keputusan Waka</p>
                <p class="text-sm font-bold text-slate-800 mt-1">Dispensasi telah diproses oleh {{ $dispensasi->pemroses?->name ?? 'Waka' }} pada {{ $dispensasi->diproses_at ? $dispensasi->diproses_at->format('d/m/Y H:i') : '-' }}.</p>
                @if($dispensasi->catatan_waka)
                    <p class="text-xs text-slate-600 mt-1 italic">"{{ $dispensasi->catatan_waka }}"</p>
                @endif
                @if($dispensasi->status_waka === 'disetujui')
                    <div class="mt-4">
                        <a href="{{ route('dispensasi.cetak', $dispensasi->id) }}" target="_blank" class="inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-5 py-2.5 text-sm font-bold text-white shadow hover:bg-emerald-800 transition">
                            <i class="bi bi-printer-fill"></i> Cetak Surat Dispensasi
                        </a>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <div class="mt-6 text-center">
        <a href="{{ route('guru') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-slate-900 transition">
            <i class="bi bi-arrow-left"></i> Kembali ke Dashboard Utama
        </a>
    </div>
</div>
@endsection
