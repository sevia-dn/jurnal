@extends('layouts.app')

@section('title', 'Riwayat & Rekap Guru - JurnalKita')

@section('sidebar')
    @include('layouts.guru-pengajar.sidebar', ['activePage' => 'riwayat'])
@endsection

@section('navbar')
    @include('layouts.guru-pengajar.navbar', ['activePage' => 'riwayat'])
@endsection

@section('content')
    <style>
        [x-cloak] { display: none !important; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: rgba(16, 185, 129, 0.08); }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(16, 185, 129, 0.35); border-radius: 9999px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(16, 185, 129, 0.5); }
    </style>

    <div
        x-data="{
            openDetailModal: false,
            detail: {},
            openDetail(data) {
                this.detail = data;
                this.openDetailModal = true;
            }
        }"
        id="riwayat"
        class="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 lg:px-8"
    >

        {{-- HEADER --}}
        <section class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-semibold text-emerald-700">Arsip Pembelajaran</p>
                <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Riwayat &amp; Rekap</h1>
                <p class="mt-2 text-sm text-slate-500">Riwayat logbook, dokumentasi, dan presensi siswa.</p>
            </div>
            <span class="w-fit rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700">Guru Pengajar</span>
        </section>

        {{-- NOTIFIKASI SUCCESS --}}
        @if(session('success'))
            <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-900 shadow-sm" role="alert">
                <div class="flex items-center gap-3">
                    <i class="bi bi-check-circle-fill text-lg text-emerald-600"></i>
                    <p class="text-sm font-semibold">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        {{-- FORM PENCARIAN --}}
        <section class="mt-6 rounded-2xl bg-white p-4 shadow-md sm:p-6">
            <form method="GET" action="{{ route('guru.riwayat') }}" class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_190px_190px_auto]">

                <label class="relative flex items-center">
                    <span class="sr-only">Cari riwayat</span>
                    <i class="bi bi-search absolute left-4 text-slate-400" aria-hidden="true"></i>
                    <input
                        type="search"
                        name="keyword"
                        value="{{ $keyword ?? '' }}"
                        placeholder="Cari mata pelajaran, kelas, atau materi..."
                        class="w-full rounded-lg border border-slate-200 bg-white py-3 pl-11 pr-4 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-emerald-600 focus:ring-4 focus:ring-emerald-100"
                    >
                </label>

                <div class="relative flex items-center">
                    <i class="bi bi-calendar3 pointer-events-none absolute left-4 text-emerald-700" aria-hidden="true"></i>
                    <input
                        type="date"
                        name="start_date"
                        value="{{ $filterStart ?? '' }}"
                        onclick="this.showPicker()"
                        class="w-full cursor-pointer rounded-lg border border-slate-200 bg-white py-3 pl-11 pr-3 text-sm text-slate-700 outline-none transition focus:border-emerald-600 focus:ring-4 focus:ring-emerald-100 [&::-webkit-calendar-picker-indicator]:hidden"
                        placeholder="Tanggal mulai"
                    >
                </div>

                <div class="relative flex items-center">
                    <i class="bi bi-calendar3 pointer-events-none absolute left-4 text-emerald-700" aria-hidden="true"></i>
                    <input
                        type="date"
                        name="end_date"
                        value="{{ $filterEnd ?? '' }}"
                        onclick="this.showPicker()"
                        class="w-full cursor-pointer rounded-lg border border-slate-200 bg-white py-3 pl-11 pr-3 text-sm text-slate-700 outline-none transition focus:border-emerald-600 focus:ring-4 focus:ring-emerald-100 [&::-webkit-calendar-picker-indicator]:hidden"
                        placeholder="Tanggal akhir"
                    >
                </div>

                <button type="submit"
                        class="rounded-lg bg-emerald-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">
                    Cari
                </button>
            </form>

            @if($keyword || $filterStart || $filterEnd)
                <div class="mt-3 flex items-center gap-2">
                    <a href="{{ route('guru.riwayat') }}" class="text-xs font-medium text-slate-500 hover:text-rose-600">
                        <i class="bi bi-x-circle mr-1"></i>Reset filter
                    </a>
                    @if($keyword)
                        <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs text-slate-600">Keyword: "{{ $keyword }}"</span>
                    @endif
                </div>
            @endif
        </section>


        {{-- DAFTAR RIWAYAT --}}
        <section class="mt-6 rounded-2xl bg-white p-3 shadow-md sm:p-4">
            <div class="mb-5 flex items-center justify-end">
                <span class="rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700">
                    Total {{ $riwayatJurnals->count() }} data
                </span>
            </div>

            <div class="space-y-4">

                @forelse($riwayatJurnals as $jurnal)
                    @php
                        $statusValidasi = $jurnal->status_validasi ?? 'belum_divalidasi';
                        $statusLabel = match($statusValidasi) {
                            'disetujui' => 'Disetujui',
                            'ditolak' => 'Ditolak',
                            default => 'Menunggu Validasi',
                        };
                        $statusColor = match($statusValidasi) {
                            'disetujui' => 'emerald',
                            'ditolak' => 'rose',
                            default => 'amber',
                        };
                        $tanggalFormatted = \Carbon\Carbon::parse($jurnal->tanggal)->format('d/m/y');
                        $tanggalLong = \Carbon\Carbon::parse($jurnal->tanggal)->translatedFormat('l, d F Y');
                    @endphp

                    <article class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                        <div class="grid gap-5 md:grid-cols-[minmax(0,1fr)_auto]">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <h2 class="text-lg font-bold text-slate-900">
                                        {{ $jurnal->mapel->nama_mapel ?? '-' }}
                                    </h2>
                                    <span class="rounded-full bg-{{ $statusColor }}-100 px-2.5 py-1 text-[10px] font-bold text-{{ $statusColor }}-700">
                                        {{ $statusLabel }}
                                    </span>
                                </div>

                                <p class="mt-2 text-sm font-semibold text-slate-700">
                                    Kelas {{ $jurnal->kelas->nama_kelas ?? '-' }}
                                    <span class="mx-1 text-slate-300">&bull;</span>
                                    Jam ke-{{ $jurnal->jam_ke }}
                                </p>

                                <p class="mt-1.5 text-sm leading-relaxed text-slate-500">
                                    Materi: {{ $jurnal->materi }}
                                </p>

                                <div class="mt-3 flex flex-wrap gap-2">
                                    <span class="rounded-full bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600">
                                        <i class="bi bi-calendar3 mr-1"></i>{{ $tanggalFormatted }}
                                    </span>
                                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                                        {{ $jurnal->jumlah_hadir ?? 0 }} Hadir
                                    </span>
                                    @if(($jurnal->jumlah_sakit ?? 0) > 0)
                                        <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">
                                            {{ $jurnal->jumlah_sakit }} Sakit
                                        </span>
                                    @endif
                                    @if(($jurnal->jumlah_izin ?? 0) > 0)
                                        <span class="rounded-full bg-sky-50 px-3 py-1 text-xs font-semibold text-sky-700">
                                            {{ $jurnal->jumlah_izin }} Izin
                                        </span>
                                    @endif
                                    @if(($jurnal->jumlah_alpa ?? 0) > 0)
                                        <span class="rounded-full bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-700">
                                            {{ $jurnal->jumlah_alpa }} Alpa
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-start justify-end">
                                <button
                                    type="button"
                                    @click="openDetail({
                                        mapel: '{{ addslashes($jurnal->mapel->nama_mapel ?? '-') }}',
                                        kelas: '{{ addslashes($jurnal->kelas->nama_kelas ?? '-') }}',
                                        tanggal: '{{ $tanggalLong }}',
                                        tanggalShort: '{{ $tanggalFormatted }}',
                                        jamKe: '{{ $jurnal->jam_ke }}',
                                        materi: '{{ addslashes($jurnal->materi) }}',
                                        catatan: '{{ addslashes($jurnal->catatan ?? '') }}',
                                        statusValidasi: '{{ $statusLabel }}',
                                        statusColor: '{{ $statusColor }}',
                                        catatanValidasi: '{{ addslashes($jurnal->catatan_validasi ?? '') }}',
                                        divalidasiPada: '{{ $jurnal->divalidasi_pada ? \Carbon\Carbon::parse($jurnal->divalidasi_pada)->format(\'d/m/y\') : \'\' }}',
                                        jumlahHadir: '{{ $jurnal->jumlah_hadir ?? 0 }}',
                                        jumlahSakit: '{{ $jurnal->jumlah_sakit ?? 0 }}',
                                        jumlahIzin: '{{ $jurnal->jumlah_izin ?? 0 }}',
                                        jumlahAlpa: '{{ $jurnal->jumlah_alpa ?? 0 }}',
                                        adaTugas: '{{ $jurnal->ada_tugas ? \'Ya\' : \'Tidak\' }}',
                                        lampiran: '{{ $jurnal->lampiran ? asset(\'storage/\'.$jurnal->lampiran) : \'\' }}'
                                    })"
                                    class="inline-flex items-center gap-2 rounded-lg border border-emerald-200 px-3 py-2 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:ring-offset-2"
                                >
                                    <i class="bi bi-eye" aria-hidden="true"></i>
                                    Lihat Detail
                                </button>
                            </div>
                        </div>
                    </article>

                @empty
                    <div class="flex flex-col items-center justify-center py-16 text-center">
                        <span class="flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-2xl text-slate-400">
                            <i class="bi bi-journal-x"></i>
                        </span>
                        <p class="mt-4 text-sm font-semibold text-slate-600">Belum ada riwayat logbook</p>
                        <p class="mt-1 text-xs text-slate-400">
                            @if($keyword || $filterStart || $filterEnd)
                                Tidak ada data yang cocok dengan filter pencarian.
                            @else
                                Riwayat jurnal pembelajaran akan muncul di sini setelah Anda mengisi logbook.
                            @endif
                        </p>
                        @if($keyword || $filterStart || $filterEnd)
                            <a href="{{ route('guru.riwayat') }}" class="mt-4 text-xs font-semibold text-emerald-600 hover:underline">Reset filter</a>
                        @endif
                    </div>
                @endforelse

            </div>
        </section>


        {{-- MODAL DETAIL LOGBOOK --}}
        <template x-teleport="body">
            <div
                x-show="openDetailModal"
                x-cloak
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-sm"
                @click.self="openDetailModal = false"
            >
                <div
                    x-show="openDetailModal"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    @click.stop
                    class="flex w-full max-w-2xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl"
                    style="max-height: 88vh;"
                >
                    {{-- Header Modal --}}
                    <div class="flex shrink-0 items-center justify-between border-b border-slate-200 px-5 py-4">
                        <h3 class="text-base font-bold text-slate-900">Detail Logbook Mengajar</h3>
                        <button type="button" @click="openDetailModal = false"
                                class="flex h-8 w-8 items-center justify-center rounded-full text-slate-500 transition hover:bg-slate-100"
                                aria-label="Tutup">
                            <i class="bi bi-x-lg text-base"></i>
                        </button>
                    </div>

                    {{-- Body Modal Scrollable --}}
                    <div class="flex-1 overflow-y-auto p-5 custom-scrollbar min-h-0">
                        <div class="space-y-4">

                            {{-- Status & Tanggal --}}
                            <div class="flex items-center justify-between gap-3">
                                <span
                                    class="inline-flex items-center rounded-full px-2.5 py-1 text-[10px] font-semibold sm:text-xs"
                                    :class="{
                                        'bg-emerald-100 text-emerald-700': detail.statusColor === 'emerald',
                                        'bg-amber-100 text-amber-700': detail.statusColor === 'amber',
                                        'bg-rose-100 text-rose-700': detail.statusColor === 'rose'
                                    }"
                                    x-text="detail.statusValidasi"
                                ></span>
                                <span class="text-[10px] font-medium text-slate-400 sm:text-xs" x-text="detail.tanggal"></span>
                            </div>

                            {{-- Info Grid --}}
                            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 sm:gap-4">
                                <div class="rounded-xl bg-slate-50 p-3">
                                    <p class="text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-500">Tanggal</p>
                                    <p class="mt-2 text-sm font-bold text-slate-800" x-text="detail.tanggalShort"></p>
                                </div>
                                <div class="rounded-xl bg-slate-50 p-3">
                                    <p class="text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-500">Mata Pelajaran</p>
                                    <p class="mt-2 text-sm font-bold text-slate-800" x-text="detail.mapel"></p>
                                </div>
                                <div class="rounded-xl bg-slate-50 p-3">
                                    <p class="text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-500">Kelas</p>
                                    <p class="mt-2 text-sm font-bold text-slate-800" x-text="detail.kelas"></p>
                                </div>
                                <div class="rounded-xl bg-slate-50 p-3">
                                    <p class="text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-500">Jam ke-</p>
                                    <p class="mt-2 text-sm font-bold text-slate-800" x-text="detail.jamKe"></p>
                                </div>
                            </div>

                            {{-- Materi --}}
                            <div>
                                <p class="text-sm font-semibold text-slate-700">Materi / Pokok Pembahasan</p>
                                <div class="mt-2 rounded-xl bg-slate-50 p-3 sm:p-4">
                                    <p class="text-sm leading-relaxed text-slate-600" x-text="detail.materi"></p>
                                </div>
                            </div>

                            {{-- Catatan --}}
                            <template x-if="detail.catatan">
                                <div>
                                    <p class="text-sm font-semibold text-slate-700">Catatan Khusus / Hambatan</p>
                                    <div class="mt-2 rounded-xl border border-amber-100 bg-amber-50 p-3 sm:p-4">
                                        <p class="text-sm leading-relaxed text-amber-800" x-text="detail.catatan"></p>
                                    </div>
                                </div>
                            </template>

                            {{-- Tugas --}}
                            <div class="flex items-center gap-3 rounded-xl border border-slate-100 bg-slate-50 px-4 py-3">
                                <i class="bi bi-clipboard-check text-slate-400"></i>
                                <span class="text-sm text-slate-600">Ada Tugas:</span>
                                <span class="text-sm font-bold text-slate-800" x-text="detail.adaTugas"></span>
                            </div>

                            {{-- Rekap Kehadiran Siswa --}}
                            <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-3 sm:p-4">
                                <p class="text-sm font-semibold text-slate-700">Rekap Kehadiran Siswa</p>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                                        <span x-text="detail.jumlahHadir"></span> Hadir
                                    </span>
                                    <template x-if="parseInt(detail.jumlahSakit) > 0">
                                        <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                            <span x-text="detail.jumlahSakit"></span> Sakit
                                        </span>
                                    </template>
                                    <template x-if="parseInt(detail.jumlahIzin) > 0">
                                        <span class="rounded-full bg-sky-100 px-3 py-1 text-xs font-semibold text-sky-700">
                                            <span x-text="detail.jumlahIzin"></span> Izin
                                        </span>
                                    </template>
                                    <template x-if="parseInt(detail.jumlahAlpa) > 0">
                                        <span class="rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold text-rose-700">
                                            <span x-text="detail.jumlahAlpa"></span> Alpa
                                        </span>
                                    </template>
                                </div>
                            </div>

                            {{-- Validasi info --}}
                            <template x-if="detail.statusColor === 'emerald' && detail.divalidasiPada">
                                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs text-emerald-700">
                                    <i class="bi bi-shield-check mr-1"></i>
                                    Divalidasi pada: <span class="font-semibold" x-text="detail.divalidasiPada"></span>
                                    <template x-if="detail.catatanValidasi">
                                        <p class="mt-1 text-emerald-600">Catatan: <span x-text="detail.catatanValidasi"></span></p>
                                    </template>
                                </div>
                            </template>

                            <template x-if="detail.statusColor === 'rose' && detail.catatanValidasi">
                                <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-xs text-rose-700">
                                    <i class="bi bi-exclamation-triangle mr-1"></i>
                                    Alasan ditolak: <span class="font-semibold" x-text="detail.catatanValidasi"></span>
                                </div>
                            </template>

                            {{-- Lampiran / Dokumentasi Foto --}}
                            <div>
                                <p class="text-sm font-semibold text-slate-700">Dokumentasi / Lampiran</p>
                                <template x-if="detail.lampiran">
                                    <div class="mt-2">
                                        <a :href="detail.lampiran" target="_blank" class="group block overflow-hidden rounded-xl border border-slate-200 bg-slate-50 hover:border-emerald-300">
                                            <img :src="detail.lampiran" alt="Lampiran" class="h-48 w-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'">
                                            <div style="display:none;" class="h-16 items-center justify-center gap-2 text-sm text-slate-500">
                                                <i class="bi bi-file-earmark text-xl"></i>
                                                <span>Lihat dokumen lampiran</span>
                                            </div>
                                        </a>
                                        <p class="mt-1 text-xs text-slate-400">Klik untuk membuka lampiran di tab baru.</p>
                                    </div>
                                </template>
                                <template x-if="!detail.lampiran">
                                    <div class="mt-2 flex h-24 items-center justify-center rounded-xl border border-dashed border-slate-200 bg-slate-50">
                                        <div class="flex flex-col items-center text-slate-400">
                                            <i class="bi bi-image text-2xl"></i>
                                            <span class="mt-1 text-xs">Tidak ada lampiran</span>
                                        </div>
                                    </div>
                                </template>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </template>

    </div>

@endsection