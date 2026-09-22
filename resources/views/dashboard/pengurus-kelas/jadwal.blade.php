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
            Jadwal Pelajaran Kelas {{ $kelas->nama_kelas ?? '' }}
        </h1>
        <p class="mt-1 text-xs text-slate-500">
            Daftar sesi belajar mengajar dan status pengisian logbook guru hari ini.
        </p>
    </header>

    @if($jadwals->isEmpty())
        <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-2xl text-slate-400">
                <i class="bi bi-calendar-x"></i>
            </div>
            <h3 class="mt-3 text-sm font-bold text-slate-700">Tidak ada jadwal KBM pada hari {{ $hariIni }}</h3>
            <p class="mt-1 text-xs text-slate-400">Silakan periksa kembali jadwal kelas pada hari aktif sekolah.</p>
        </div>
    @else
        <ol class="relative space-y-4 border-l-2 border-emerald-200 pl-5 sm:pl-7" aria-label="Jadwal pelajaran hari ini">
            @foreach($jadwals as $jadwal)
                @php
                    $jamText = ($jadwal->jam_selesai && $jadwal->jam_selesai > $jadwal->jam_mulai)
                        ? "Jam ke {$jadwal->jam_mulai}-{$jadwal->jam_selesai}"
                        : "Jam ke {$jadwal->jam_mulai}";

                    $jurnal = $jurnalHariIni->get($jadwal->jam_mulai);
                    $hasJurnal = (bool) $jurnal;
                    $statusValidasi = $jurnal?->status_validasi;
                @endphp

                <li class="relative">
                    <span class="absolute -left-[29px] top-5 flex h-3.5 w-3.5 rounded-full border-2 border-white {{ $hasJurnal ? ($statusValidasi === 'disetujui' ? 'bg-emerald-600' : 'bg-amber-500') : 'bg-slate-300' }} sm:-left-[37px]" aria-hidden="true"></span>
                    <article class="rounded-2xl border {{ $hasJurnal ? ($statusValidasi === 'disetujui' ? 'border-emerald-200 bg-white' : 'border-amber-200 bg-amber-50/30') : 'border-slate-200 bg-white' }} p-4 shadow-2xs sm:p-5 transition hover:shadow-sm">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-emerald-700">{{ $jamText }}</span>
                                    <span class="text-slate-300">&bull;</span>
                                    <span class="text-xs text-slate-500">{{ $jadwal->hari }}</span>
                                </div>
                                <h2 class="mt-1 text-base font-extrabold text-slate-900">{{ $jadwal->mapel->nama_mapel ?? '-' }}</h2>
                                <p class="mt-0.5 text-xs text-slate-600 font-medium">{{ $jadwal->user->name ?? 'Guru Pengajar' }}</p>

                                @if($hasJurnal)
                                    <div class="mt-2.5 rounded-lg bg-slate-50 p-2.5 border border-slate-100 text-xs text-slate-600">
                                        <p class="font-semibold text-slate-700">Materi: <span class="font-normal">{{ \Illuminate\Support\Str::limit($jurnal->materi, 120) }}</span></p>
                                        <div class="mt-1 flex flex-wrap gap-2 text-[11px]">
                                            <span class="text-emerald-700 font-bold">{{ $jurnal->jumlah_hadir }} Hadir</span>
                                            @if(($jurnal->jumlah_tidak_hadir ?? 0) > 0)
                                                <span class="text-rose-600 font-bold">{{ $jurnal->jumlah_tidak_hadir }} Tidak Masuk</span>
                                            @endif
                                            @if($jurnal->lampiran)
                                                <span class="text-purple-700 font-semibold"><i class="bi bi-camera-fill mr-0.5"></i>Foto Live Tersedia</span>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="shrink-0 flex items-center gap-2">
                                @if($hasJurnal)
                                    @if($statusValidasi === 'disetujui')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700 border border-emerald-200">
                                            <i class="bi bi-check-circle-fill"></i> Disetujui
                                        </span>
                                    @elseif($statusValidasi === 'ditolak')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-rose-100 px-3 py-1 text-xs font-bold text-rose-700 border border-rose-200">
                                            <i class="bi bi-x-circle-fill"></i> Ditolak
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-700 border border-amber-200">
                                            <i class="bi bi-hourglass-split"></i> Menunggu Validasi
                                        </span>
                                    @endif

                                    <a href="{{ route('pengurus-kelas.jurnal-detail', ['id' => $jurnal->id_jurnal]) }}"
                                       class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-600 bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-700 hover:bg-emerald-600 hover:text-white transition">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-500">
                                        <i class="bi bi-clock"></i> Belum Mengisi
                                    </span>
                                @endif
                            </div>
                        </div>
                    </article>
                </li>
            @endforeach
        </ol>
    @endif

</div>
@endsection
