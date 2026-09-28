@extends('layouts.app')

@section('title', 'Laporan Wali Kelas - JurnalKita')

@section('sidebar')
    @include('layouts.guru-pengajar.sidebar', ['activePage' => 'wali-kelas'])
@endsection

@section('navbar')
    @include('layouts.guru-pengajar.navbar', ['activePage' => 'wali-kelas'])
@endsection

@section('content')
    <main class="mx-auto w-full max-w-7xl px-4 py-4 pb-24 sm:px-6 lg:px-8 lg:py-6">
        <section class="rounded-2xl border border-emerald-100 bg-linear-to-br from-emerald-50 to-white p-4 shadow-sm sm:p-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-full bg-emerald-100 px-3 py-1 text-[11px] font-bold text-emerald-800">
                        <i class="bi bi-people-fill"></i> Area Wali Kelas
                    </div>
                </div>

                <form method="GET" action="{{ route('guru.wali-kelas') }}" class="grid w-full gap-2 sm:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_auto] lg:max-w-2xl">
                    @if($waliKelases->count() > 1)
                        <label class="sr-only" for="kelas_id">Kelas</label>
                        <select id="kelas_id" name="kelas_id" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs font-semibold text-slate-700 outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100 sm:text-sm">
                            @foreach($waliKelases as $waliKelas)
                                <option value="{{ $waliKelas->id_kelas }}" @selected($waliKelas->id_kelas === $kelas->id_kelas)>{{ $waliKelas->nama_kelas }}</option>
                            @endforeach
                        </select>
                    @else
                        <input type="hidden" name="kelas_id" value="{{ $kelas->id_kelas }}">
                        <div class="rounded-xl border border-emerald-200 bg-white px-3 py-2.5 text-xs font-bold text-emerald-800 sm:text-sm">{{ $kelas->nama_kelas }}</div>
                    @endif

                    <label class="sr-only" for="tanggal">Tanggal</label>
                    <input id="tanggal" type="date" name="tanggal" value="{{ $tanggal }}" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs font-semibold text-slate-700 outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100 sm:text-sm">
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-emerald-700 sm:text-sm">
                        <i class="bi bi-funnel"></i> Tampilkan
                    </button>
                </form>
            </div>
        </section>

        <section class="mt-4 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-col gap-1 border-b border-slate-100 px-4 py-4 sm:px-5 sm:py-5">
                <h2 class="text-base font-extrabold text-slate-900">{{ $kelas->nama_kelas }}</h2>
                <p class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($tanggal, 'Asia/Jakarta')->translatedFormat('l, d F Y') }} · {{ $jadwals->count() }} sesi terjadwal</p>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($jadwals as $jadwal)
                    @php($jurnal = $jurnalsTervalidasi->get($jadwal->jam_mulai))
                    <article class="p-4 sm:p-5">
                        <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-[11px] font-extrabold text-slate-700">Jam ke-{{ $jadwal->jam_mulai }}@if($jadwal->jam_selesai > $jadwal->jam_mulai) s/d {{ $jadwal->jam_selesai }}@endif</span>
                                    <span class="text-xs font-semibold text-emerald-700">{{ $jadwal->mapel?->nama_mapel ?? 'Kegiatan' }}</span>
                                </div>
                                <p class="mt-2 text-xs text-slate-500">Guru pengampu: <span class="font-semibold text-slate-700">{{ $jadwal->user?->name ?? '-' }}</span></p>
                            </div>
                            <span class="inline-flex w-fit items-center gap-1.5 rounded-full {{ $jurnal ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }} px-2.5 py-1 text-[11px] font-bold">
                                <i class="bi {{ $jurnal ? 'bi-check-circle-fill' : 'bi-clock-history' }}"></i>
                                {{ $jurnal ? 'Tervalidasi' : 'Belum tervalidasi' }}
                            </span>
                        </div>

                        @if($jurnal)
                            <div class="mt-3 rounded-xl bg-slate-50 p-3.5 sm:p-4">
                                <p class="text-xs font-bold text-slate-800">Materi: <span class="font-medium">{{ $jurnal->materi ?: '-' }}</span></p>
                                @if($jurnal->catatan)
                                    <p class="mt-1.5 text-xs leading-relaxed text-slate-600">{{ $jurnal->catatan }}</p>
                                @endif
                                <div class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-[11px] text-slate-500">
                                    <span>Hadir: <b class="text-slate-700">{{ $jurnal->jumlah_hadir ?? 0 }}</b></span>
                                    <span>Sakit: <b class="text-slate-700">{{ $jurnal->jumlah_sakit ?? 0 }}</b></span>
                                    <span>Izin: <b class="text-slate-700">{{ $jurnal->jumlah_izin ?? 0 }}</b></span>
                                    <span>Alpa: <b class="text-slate-700">{{ $jurnal->jumlah_alpa ?? 0 }}</b></span>
                                    <span class="sm:ml-auto">Divalidasi: <b class="text-slate-700">{{ $jurnal->divalidasi_pada ? \Carbon\Carbon::parse($jurnal->divalidasi_pada)->format('H:i') . ' WIB' : '-' }}</b></span>
                                </div>
                            </div>
                        @endif
                    </article>
                @empty
                    <div class="p-8 text-center text-sm text-slate-500">
                        <i class="bi bi-calendar-x text-2xl text-slate-300"></i>
                        <p class="mt-2 font-semibold">Tidak ada jadwal pelajaran pada hari {{ $hari }}.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </main>
@endsection
