@extends('layouts.app')

@section('title', 'Status Piket - JurnalKita')

@section('sidebar')
    @include('layouts.guru-pengajar.sidebar', ['activePage' => 'piket'])
@endsection

@section('navbar')
    @include('layouts.guru-pengajar.navbar', ['activePage' => 'piket'])
@endsection

@section('content')
<div class="min-h-full bg-slate-50 p-4 pb-24 font-sans sm:p-6 lg:p-8">
    <div class="mx-auto max-w-4xl space-y-6">

        {{-- STATUS BANNER: ANDA TIDAK SEDANG PIKET --}}
        <div class="rounded-3xl border border-amber-200 bg-gradient-to-br from-amber-50 via-white to-emerald-50/40 p-6 sm:p-8 shadow-sm text-center">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-2xl bg-amber-100 text-amber-600 shadow-inner">
                <i class="bi bi-shield-slash text-4xl"></i>
            </div>

            <div class="mt-4 inline-flex items-center gap-1.5 rounded-full bg-amber-100/80 px-3 py-1 text-xs font-bold text-amber-900 border border-amber-300">
                <i class="bi bi-info-circle-fill"></i>
                <span>Status Piket Hari Ini</span>
            </div>

            <h1 class="mt-3 text-2xl font-extrabold text-slate-900 sm:text-3xl">
                Anda Tidak Sedang Piket
            </h1>

            <p class="mx-auto mt-2 max-w-xl text-sm leading-relaxed text-slate-600">
                Anda tidak memiliki jadwal tugas piket untuk hari ini (<span class="font-bold text-slate-800">{{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('l, d F Y') }}</span>).
                Halaman dan fitur piket KBM (Monitoring Jurnal, Kehadiran Guru, Kehadiran Siswa, dan Dispensasi) hanya dapat diakses pada saat Anda bertugas piket.
            </p>

            {{-- PROFILE INFO CARD --}}
            <div class="mx-auto mt-6 max-w-md rounded-2xl border border-slate-200/80 bg-white/90 p-4 text-left shadow-2xs">
                <div class="flex items-center gap-3">
                    @php
                        $namaParts = explode(' ', auth()->user()?->name ?? 'Guru');
                        $initials = strtoupper(substr($namaParts[0], 0, 1) . (isset($namaParts[1]) ? substr($namaParts[1], 0, 1) : ''));
                    @endphp
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-sm font-bold text-emerald-800">
                        {{ $initials }}
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-bold text-slate-800 truncate">{{ auth()->user()?->name }}</p>
                        <p class="text-xs text-slate-500 truncate">NIP: {{ auth()->user()?->nip ?? auth()->user()?->username ?? '-' }}</p>
                    </div>
                    <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-600">
                        Guru Pengajar
                    </span>
                </div>
            </div>

            {{-- ACTION BUTTONS --}}
            <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                <a
                    href="{{ route('guru.utama') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white shadow-xs transition hover:bg-emerald-700 active:scale-98 !no-underline"
                >
                    <i class="bi bi-arrow-left"></i>
                    <span>Kembali ke Halaman Utama Guru</span>
                </a>

                <a
                    href="{{ route('guru.riwayat') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-2xs transition hover:bg-slate-50 !no-underline"
                >
                    <i class="bi bi-clock-history"></i>
                    <span>Lihat Riwayat &amp; Rekap</span>
                </a>
            </div>
        </div>

        {{-- JADWAL PIKET ANDA BERIKUTNYA --}}
        @if(isset($upcomingSchedules) && $upcomingSchedules->count() > 0)
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center gap-2 border-b border-slate-100 pb-3 mb-4">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700 text-sm">
                        <i class="bi bi-calendar-week-fill"></i>
                    </span>
                    <div>
                        <h2 class="text-sm font-bold text-slate-800">Jadwal Tugas Piket Anda Mendatang</h2>
                        <p class="text-xs text-slate-500">Berdasarkan jadwal resmi KBM Semester Ganjil 2026/2027</p>
                    </div>
                </div>

                <div class="space-y-2.5">
                    @foreach($upcomingSchedules as $jadwal)
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 rounded-xl border border-slate-200/80 bg-slate-50/50 p-3.5 transition hover:border-emerald-300 hover:bg-emerald-50/20">
                            <div class="flex items-center gap-3">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-600 text-white font-bold text-xs shadow-2xs">
                                    {{ \Carbon\Carbon::parse($jadwal->tanggal)->format('d') }}
                                </span>
                                <div>
                                    <p class="text-xs font-bold text-slate-800">
                                        {{ \Carbon\Carbon::parse($jadwal->tanggal)->locale('id')->translatedFormat('l, d F Y') }}
                                    </p>
                                    <p class="text-[11px] text-slate-500">
                                        Shift {{ $jadwal->shift ?? 1 }} &bull; {{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }} WIB &bull; Peran: <span class="font-semibold uppercase text-emerald-700">{{ $jadwal->tipe ?? 'Petugas Piket' }}</span>
                                    </p>
                                </div>
                            </div>
                            <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-[11px] font-bold text-emerald-800 w-fit">
                                Terjadwal
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</div>
@endsection

