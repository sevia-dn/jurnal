@extends('layouts.app')

@section('sidebar')
    @include('layouts.pengurus-kelas.sidebar', ['activePage' => 'dashboard'])
@endsection

@section('navbar')
    @include('layouts.pengurus-kelas.navbar', ['activePage' => 'dashboard'])
@endsection

@section('content')
<div class="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
    <div class="mb-6">
        <p class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Dashboard Pengurus Kelas</p>
        <h1 class="mt-1 text-xl font-extrabold leading-tight text-slate-900 sm:text-2xl">
            Selamat datang, {{ Auth::user()->name }}
        </h1>
        <p class="mt-1 text-xs text-slate-500">{{ $tanggalFormatted }}</p>
    </div>

    {{-- KARTU STATISTIK --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <a href="{{ route('pengurus-kelas.jadwal') }}"
            class="group flex min-h-36 justify-between rounded-2xl border border-emerald-200 bg-emerald-50/70 p-5 shadow-2xs transition hover:-translate-y-0.5 hover:border-emerald-400 hover:shadow-sm focus:outline-none">
            <div>
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-700 transition group-hover:text-emerald-700">Kelas Hari Ini</h2>
                <p class="mt-4 text-3xl font-extrabold text-emerald-700">{{ $totalSesi }}</p>
                <p class="mt-1 flex items-center gap-1 text-[11px] font-medium text-slate-500">Total sesi KBM <i class="bi bi-arrow-right" aria-hidden="true"></i></p>
            </div>
            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-200/80 text-lg text-emerald-800"><i class="bi bi-calendar-week-fill" aria-hidden="true"></i></span>
        </a>

        <a href="{{ route('pengurus-kelas.jurnal-detail') }}"
            class="group flex min-h-36 justify-between rounded-2xl border border-amber-200 bg-amber-50/70 p-5 shadow-2xs transition hover:-translate-y-0.5 hover:border-amber-400 hover:shadow-sm focus:outline-none">
            <div>
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-700 transition group-hover:text-amber-700">Perlu Persetujuan</h2>
                <p class="mt-4 text-3xl font-extrabold text-amber-600">{{ $perluPersetujuan }}</p>
                <p class="mt-1 flex items-center gap-1 text-[11px] font-medium text-slate-500">Logbook menunggu validasi <i class="bi bi-arrow-right" aria-hidden="true"></i></p>
            </div>
            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-200/80 text-lg text-amber-800"><i class="bi bi-clipboard-check-fill" aria-hidden="true"></i></span>
        </a>

        <a href="{{ route('pengurus-kelas.kehadiran-guru') }}"
            class="group flex min-h-36 justify-between rounded-2xl border border-emerald-200 bg-emerald-50/70 p-5 shadow-2xs transition hover:-translate-y-0.5 hover:border-emerald-400 hover:shadow-sm focus:outline-none">
            <div>
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-700 transition group-hover:text-emerald-700">Kehadiran Guru</h2>
                <p class="mt-4 text-3xl font-extrabold text-emerald-700">{{ $kehadiranGuruText }}</p>
                <p class="mt-1 flex items-center gap-1 text-[11px] font-medium text-slate-500">Logbook guru terisi <i class="bi bi-arrow-right" aria-hidden="true"></i></p>
            </div>
            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-200/80 text-lg text-emerald-800"><i class="bi bi-person-badge-fill" aria-hidden="true"></i></span>
        </a>

        <a href="{{ route('pengurus-kelas.kehadiran-siswa') }}"
            class="group flex min-h-36 justify-between rounded-2xl border border-emerald-200 bg-emerald-50/70 p-5 shadow-2xs transition hover:-translate-y-0.5 hover:border-emerald-400 hover:shadow-sm focus:outline-none">
            <div>
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-700 transition group-hover:text-emerald-700">Kehadiran Siswa</h2>
                <p class="mt-4 text-3xl font-extrabold text-emerald-700">{{ $kehadiranSiswaText }}</p>
                <p class="mt-1 flex items-center gap-1 text-[11px] font-medium text-slate-500">Siswa hadir / total <i class="bi bi-arrow-right" aria-hidden="true"></i></p>
            </div>
            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-200/80 text-lg text-emerald-800"><i class="bi bi-people-fill" aria-hidden="true"></i></span>
        </a>
    </div>

    {{-- LOGBOOK MENUNGGU VALIDASI CEPAT --}}
    @if(isset($jurnalAntrean) && $jurnalAntrean->count() > 0)
        <div class="mt-8">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-extrabold text-slate-800 uppercase tracking-wide">
                    Antrean Validasi Logbook Guru
                </h2>
                <a href="{{ route('pengurus-kelas.jadwal') }}" class="text-xs font-semibold text-emerald-600 hover:underline">
                    Lihat Semua Jadwal &rarr;
                </a>
            </div>

            <div class="space-y-3">
                @foreach($jurnalAntrean as $ja)
                    @php
                        $jamText = ($ja->jam_selesai && $ja->jam_selesai > $ja->jam_ke)
                            ? "Jam ke-{$ja->jam_ke}-{$ja->jam_selesai}"
                            : "Jam ke-{$ja->jam_ke}";
                        $tgl = \Carbon\Carbon::parse($ja->tanggal)->format('d/m/y');
                    @endphp
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 rounded-2xl border border-amber-200 bg-amber-50/40 p-4 shadow-2xs">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-slate-800">{{ $ja->mapel->nama_mapel ?? '-' }}</span>
                                <span class="text-slate-300">&bull;</span>
                                <span class="text-xs text-slate-600 font-semibold">{{ $ja->user->name ?? 'Guru' }}</span>
                                <span class="rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-700">Menunggu</span>
                            </div>
                            <p class="mt-1 text-xs text-slate-600 line-clamp-1">
                                <span class="font-semibold text-slate-700">Materi:</span> {{ $ja->materi }}
                            </p>
                            <div class="mt-1.5 flex flex-wrap gap-2 text-[11px]">
                                <span class="text-slate-500"><i class="bi bi-calendar3 mr-1"></i>{{ $tgl }} ({{ $jamText }})</span>
                                <span class="text-emerald-700 font-semibold">{{ $ja->jumlah_hadir }} Hadir</span>
                                @if($ja->lampiran)
                                    <span class="text-purple-700 font-semibold"><i class="bi bi-camera-fill mr-0.5"></i>Foto Live Tersedia</span>
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('pengurus-kelas.jurnal-detail', ['id' => $ja->id_jurnal]) }}"
                           class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-emerald-600 px-4 py-2 text-xs font-bold text-white hover:bg-emerald-700 shadow-2xs transition shrink-0">
                            <i class="bi bi-check2-circle"></i> Periksa &amp; Validasi
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
