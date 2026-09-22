@extends('layouts.app')

@section('sidebar')
    @include('layouts.pengurus-kelas.sidebar', ['activePage' => 'jadwal'])
@endsection

@section('navbar')
    @include('layouts.pengurus-kelas.navbar', ['activePage' => 'jadwal'])
@endsection

@section('content')
<div class="mx-auto w-full max-w-4xl px-4 py-6 sm:px-6 lg:px-8">

    <header class="mb-6">
        <p class="text-xs font-bold text-emerald-700 uppercase tracking-wider">{{ $tanggalFormatted }}</p>
        <h1 class="mt-1 text-xl font-extrabold text-slate-900 sm:text-2xl">
            Status Kehadiran Guru Kelas {{ $kelas->nama_kelas ?? '' }}
        </h1>
        <p class="mt-1 text-xs text-slate-500">Pantau kehadiran guru yang mengajar di kelas hari ini.</p>
    </header>

    @if($jadwals->isEmpty())
        <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-2xl text-slate-400">
                <i class="bi bi-calendar-x"></i>
            </div>
            <h3 class="mt-3 text-sm font-bold text-slate-700">Tidak ada jadwal KBM pada hari {{ $hariIni }}</h3>
            <p class="mt-1 text-xs text-slate-400">Belum ada data kehadiran guru untuk hari ini.</p>
        </div>
    @else
        <div class="grid gap-3.5 sm:grid-cols-2" aria-label="Daftar kehadiran guru">
            @foreach($jadwals as $jadwal)
                @php
                    $namaGuru = $jadwal->user->name ?? 'Guru Pengajar';
                    $initials = strtoupper(substr($namaGuru, 0, 1));
                    $parts = explode(' ', $namaGuru);
                    if (count($parts) > 1) {
                        $initials .= strtoupper(substr($parts[1], 0, 1));
                    }
                    $jurnal = $jurnalHariIni->get($jadwal->jam_mulai);
                    $hasJurnal = (bool) $jurnal;
                    $jamText = ($jadwal->jam_selesai && $jadwal->jam_selesai > $jadwal->jam_mulai)
                        ? "Jam ke {$jadwal->jam_mulai}-{$jadwal->jam_selesai}"
                        : "Jam ke {$jadwal->jam_mulai}";
                @endphp

                <article class="flex items-center gap-3.5 rounded-2xl border {{ $hasJurnal ? 'border-emerald-200 bg-emerald-50/30' : 'border-slate-200 bg-white' }} p-4 shadow-2xs">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl {{ $hasJurnal ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }} font-bold text-sm">
                        {{ $initials }}
                    </span>
                    <div class="min-w-0 flex-1">
                        <h2 class="truncate text-xs sm:text-sm font-bold text-slate-900">{{ $namaGuru }}</h2>
                        <p class="truncate text-xs text-slate-500">{{ $jadwal->mapel->nama_mapel ?? '-' }} · <span class="font-medium text-emerald-700">{{ $jamText }}</span></p>
                    </div>
                    @if($hasJurnal)
                        <span class="shrink-0 rounded-full bg-emerald-600 px-3 py-1 text-[11px] font-bold text-white shadow-2xs">
                            Hadir
                        </span>
                    @else
                        <span class="shrink-0 rounded-full bg-slate-100 px-3 py-1 text-[11px] font-semibold text-slate-500">
                            Belum Masuk
                        </span>
                    @endif
                </article>
            @endforeach
        </div>
    @endif
</div>
@endsection
