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
        .custom-scrollbar::-webkit-scrollbar { width: 8px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: rgba(16, 185, 129, 0.08); }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(16, 185, 129, 0.35); border-radius: 9999px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(16, 185, 129, 0.5); }
    </style>

    <div x-data="{ openDetailModal: false, filterStart: '2026-09-01', filterEnd: '2026-09-30' }" id="riwayat" class="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <section class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-semibold text-emerald-700">Arsip Pembelajaran</p>
                <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Riwayat &amp; Rekap</h1>
                <p class="mt-2 text-sm text-slate-500">Riwayat logbook, dokumentasi, dan presensi siswa.</p>
            </div>
            <span class="w-fit rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700">Guru Pengajar</span>
        </section>

<section class="mt-6 rounded-2xl bg-white p-4 shadow-md sm:p-6">
            <div class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_190px_190px]">
                
                <!-- Kolom Pencarian -->
                <label class="relative flex items-center">
                    <span class="sr-only">Cari riwayat</span>
                    <i class="bi bi-search absolute left-4 text-slate-400" aria-hidden="true"></i>
                    <input type="search" placeholder="Cari mata pelajaran, kelas, atau materi..." class="w-full rounded-lg border border-slate-200 bg-white py-3 pl-11 pr-4 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-emerald-600 focus:ring-4 focus:ring-emerald-100">
                </label>

                <div class="relative flex items-center">
                    <span class="sr-only">Tanggal mulai</span>
                    <i class="bi bi-calendar3 pointer-events-none absolute left-4 text-emerald-700" aria-hidden="true"></i>

                    <input type="date" 
                           x-model="filterStart" 
                           lang="id-ID"
                           onclick="this.showPicker()" 
                           class="w-full cursor-pointer rounded-lg border border-slate-200 bg-white py-3 pl-11 pr-3 text-sm text-slate-700 outline-none transition focus:border-emerald-600 focus:ring-4 focus:ring-emerald-100 [&::-webkit-calendar-picker-indicator]:hidden">
                </div>

                <div class="relative flex items-center">
                    <span class="sr-only">Tanggal akhir</span>
                    <i class="bi bi-calendar3 pointer-events-none absolute left-4 text-emerald-700" aria-hidden="true"></i>
                    
                    <input type="date" 
                           x-model="filterEnd" 
                           lang="id-ID"
                           onclick="this.showPicker()" 
                           class="w-full cursor-pointer rounded-lg border border-slate-200 bg-white py-3 pl-11 pr-3 text-sm text-slate-700 outline-none transition focus:border-emerald-600 focus:ring-4 focus:ring-emerald-100 [&::-webkit-calendar-picker-indicator]:hidden">
                </div>
                
            </div>
        </section>

        <section class="mt-6 rounded-2xl bg-white p-3 shadow-md sm:p-4">
            <div class="mb-5 flex items-center justify-end">
                <span class="rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700">Total 9 data</span>
            </div>

            <div class="space-y-4">
                <article class="rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm">
                    <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                        <div class="flex items-start gap-3">
                            <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-white text-emerald-700 shadow-sm"><i class="bi bi-journal-text text-xl" aria-hidden="true"></i></span>
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <h2 class="text-lg font-bold text-slate-900">Informatika</h2>
                                    <span class="rounded-full bg-amber-100 px-2.5 py-1 text-[10px] font-bold text-amber-700">Menunggu Validasi</span>
                                </div>
                                <p class="mt-2 text-sm font-semibold text-slate-700">Kelas XI RPL 2</p>
                                <p class="mt-1 text-sm text-slate-500">Jam Pelajaran: Jam ke 1-2</p>
                            </div>
                        </div>
                        <span class="rounded-full bg-white px-3 py-1.5 text-xs font-semibold text-amber-700 shadow-sm">Senin, 14 Sep 2026</span>
                    </div>
                    <div class="mt-4 rounded-xl border border-amber-100 bg-white/70 p-4">
                        <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-amber-700">Ringkasan Logbook</p>
                        <p class="mt-2 text-sm leading-relaxed text-slate-600">Materi: Pengenalan struktur data array dan penerapan dalam program sederhana. Siswa mengikuti praktek dengan antusias, serta menyelesaikan latihan pengolahan data pada kelompok kecil.</p>
                    </div>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <span class="rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700">07.00 - 08.20 WIB</span>
                        <span class="rounded-full bg-sky-50 px-3 py-1.5 text-xs font-semibold text-sky-700">34 Hadir</span>
                        <span class="rounded-full bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700">1 Sakit</span>
                    </div>
                </article>

                <article class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                    <div class="grid gap-5 md:grid-cols-[112px_minmax(0,1fr)_auto]">
                        <div class="flex h-28 items-center justify-center rounded-xl bg-emerald-100 text-center text-emerald-700"><div><i class="bi bi-camera-fill text-xl" aria-hidden="true"></i><p class="mt-1 text-[10px] font-semibold">Foto Kelas</p></div></div>
                        <div>
                            <div class="flex flex-wrap items-center gap-2"><h2 class="text-lg font-bold text-slate-900">Informatika</h2><span class="rounded-full bg-emerald-100 px-2.5 py-1 text-[10px] font-bold text-emerald-700">Disetujui</span></div>
                            <p class="mt-2 text-sm font-semibold text-slate-700">Kelas XI RPL 2 <span class="mx-1 text-slate-300">&bull;</span> 07.00 - 08.20 WIB</p>
                            <p class="mt-2 text-sm text-slate-500">Materi: Pengenalan struktur data array dan penerapannya dalam pemrograman.</p>
                            <p class="mt-2 text-xs font-medium text-slate-400">Senin, 14 September 2026</p>
                        </div>
                        <div class="flex items-start md:justify-end">
                            <button type="button" @click="openDetailModal = true" class="inline-flex items-center gap-2 rounded-lg border border-emerald-200 px-3 py-2 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:ring-offset-2">
                                <i class="bi bi-people" aria-hidden="true"></i>
                                Lihat Detail
                            </button>
                        </div>
                    </div>
                </article>

                <article class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                    <div class="grid gap-5 md:grid-cols-[112px_minmax(0,1fr)_auto]">
                        <div class="flex h-28 items-center justify-center rounded-xl bg-amber-100 text-center text-amber-700"><div><i class="bi bi-camera-fill text-xl" aria-hidden="true"></i><p class="mt-1 text-[10px] font-semibold">Foto Kelas</p></div></div>
                        <div>
                            <div class="flex flex-wrap items-center gap-2"><h2 class="text-lg font-bold text-slate-900">Informatika</h2><span class="rounded-full bg-amber-100 px-2.5 py-1 text-[10px] font-bold text-amber-700">Perlu Revisi</span></div>
                            <p class="mt-2 text-sm font-semibold text-slate-700">Kelas XI RPL 1 <span class="mx-1 text-slate-300">&bull;</span> 08.20 - 09.40 WIB</p>
                            <p class="mt-2 text-sm text-slate-500">Materi: Pengolahan data menggunakan array multidimensi.</p>
                            <p class="mt-2 text-xs font-medium text-slate-400">Jumat, 11 September 2026</p>
                        </div>
                        <div class="flex items-start md:justify-end">
                            <button type="button" @click="openDetailModal = true" class="inline-flex items-center gap-2 rounded-lg border border-emerald-200 px-3 py-2 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:ring-offset-2">
                                <i class="bi bi-people" aria-hidden="true"></i>
                                Lihat Detail
                            </button>
                        </div>
                    </div>
                </article>
            </div>
        </section>

<!-- POP-UP MODAL KESELURUHAN -->
        <template x-teleport="body">
            <div
                x-show="openDetailModal"
                x-cloak
                {{-- PERUBAHAN ADA DI BARIS BAWAH INI: Menambahkan md:left-64 dan z-[9999] mutlak --}}
                class="fixed inset-0 md:left-64 z-[9999] flex items-center justify-center p-4 sm:p-6 bg-slate-900/60 backdrop-blur-md"
                @click="openDetailModal = false"
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
                    class="flex flex-col w-full max-w-2xl max-h-[85vh] bg-white rounded-2xl shadow-2xl overflow-hidden"
                >
                    <!-- Header Modal -->
                    <div class="flex shrink-0 items-center justify-between border-b border-slate-200 px-4 py-3 sm:px-6 sm:py-4">
                        <h3 class="text-base font-bold text-slate-900 sm:text-lg">Detail Logbook Mengajar</h3>
                        <button
                            type="button"
                            @click="openDetailModal = false"
                            class="flex h-8 w-8 items-center justify-center rounded-full text-slate-500 transition hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            aria-label="Tutup detail logbook"
                        >
                            <i class="bi bi-x-lg text-base" aria-hidden="true"></i>
                        </button>
                    </div>

                    <!-- Body Modal -->
                    <div class="flex-1 overflow-y-auto p-4 sm:p-6 min-h-0">
                        <div class="space-y-4 sm:space-y-5">
                            
                            <div class="flex items-center justify-between gap-3">
                                <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-1 text-[10px] font-semibold text-emerald-700 sm:text-xs">Disetujui</span>
                                <span class="text-[10px] font-medium text-slate-400 sm:text-xs">Senin, 14 Sep 2026</span>
                            </div>

                            <!-- Grid 4 Kolom -->
                            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 sm:gap-4">
                                <div class="rounded-xl bg-slate-50 p-3">
                                    <p class="text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-500 sm:text-[11px]">Tanggal</p>
                                    <p class="mt-2 text-sm font-bold text-slate-800 sm:text-base">14 Sep 2026</p>
                                </div>
                                <div class="rounded-xl bg-slate-50 p-3">
                                    <p class="text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-500 sm:text-[11px]">Mata Pelajaran</p>
                                    <p class="mt-2 text-sm font-bold text-slate-800 sm:text-base">Informatika</p>
                                </div>
                                <div class="rounded-xl bg-slate-50 p-3">
                                    <p class="text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-500 sm:text-[11px]">Kelas</p>
                                    <p class="mt-2 text-sm font-bold text-slate-800 sm:text-base">XI RPL 2</p>
                                </div>
                                <div class="rounded-xl bg-slate-50 p-3">
                                    <p class="text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-500 sm:text-[11px]">Jam Pelajaran</p>
                                    <p class="mt-2 text-sm font-bold text-slate-800 sm:text-base">Jam ke 1-2</p>
                                </div>
                            </div>

                            <!-- Ringkasan Materi -->
                            <div>
                                <p class="text-sm font-semibold text-slate-700">Ringkasan Materi</p>
                                <div class="mt-2 rounded-xl bg-slate-50 p-3 sm:p-4">
                                    <p class="text-sm leading-relaxed text-slate-600">
                                        Materi yang dibahas hari ini adalah pengenalan struktur data array dan penerapannya dalam program sederhana. Siswa mengikuti praktek dengan antusias dan menyelesaikan latihan secara berkelompok.
                                    </p>
                                </div>
                            </div>

                            <!-- Form Catatan Khusus / Hambatan -->
                            <div>
                                <p class="text-sm font-semibold text-slate-700">Catatan Khusus / Hambatan Kelas</p>
                                <div class="mt-2 rounded-xl border border-amber-100 bg-amber-50 p-3 sm:p-4">
                                    <p class="text-sm leading-relaxed text-amber-800">
                                        Beberapa siswa masih mengalami kesulitan dalam memahami konsep array multidimensi. Perlu diadakan review singkat pada pertemuan berikutnya.
                                    </p>
                                </div>
                            </div>

                            <!-- Rincian Kehadiran Siswa -->
                            <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-3 sm:p-4">
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                    <p class="text-sm font-semibold text-slate-700">Rincian Kehadiran Siswa</p>
                                    <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-center text-[10px] font-semibold text-emerald-700 sm:text-xs">34 Hadir, 1 Sakit, 1 Alpa</span>
                                </div>

                                <div class="mt-3 max-h-48 overflow-y-auto rounded-xl border border-slate-100 bg-white custom-scrollbar">
                                    <div class="divide-y divide-slate-100">
                                        <div class="flex items-center justify-between gap-3 px-3 py-3 sm:px-4">
                                            <span class="text-sm font-medium text-slate-700">Aisyah Nurhaliza</span>
                                            <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-[10px] font-semibold text-emerald-700">Hadir</span>
                                        </div>
                                        <div class="flex items-center justify-between gap-3 px-3 py-3 sm:px-4">
                                            <span class="text-sm font-medium text-slate-700">Bagas Pratama</span>
                                            <span class="rounded-full bg-amber-100 px-2.5 py-1 text-[10px] font-semibold text-amber-700">Sakit</span>
                                        </div>
                                        <div class="flex items-center justify-between gap-3 px-3 py-3 sm:px-4">
                                            <span class="text-sm font-medium text-slate-700">Citra Lestari</span>
                                            <span class="rounded-full bg-rose-100 px-2.5 py-1 text-[10px] font-semibold text-rose-700">Alpa</span>
                                        </div>
                                        <div class="flex items-center justify-between gap-3 px-3 py-3 sm:px-4">
                                            <span class="text-sm font-medium text-slate-700">Dimas Rahardian</span>
                                            <span class="rounded-full bg-sky-100 px-2.5 py-1 text-[10px] font-semibold text-sky-700">Izin</span>
                                        </div>
                                        <div class="flex items-center justify-between gap-3 px-3 py-3 sm:px-4">
                                            <span class="text-sm font-medium text-slate-700">Eka Putri Maharani</span>
                                            <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-[10px] font-semibold text-emerald-700">Hadir</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Dokumentasi Foto -->
                            <div>
                                <p class="text-sm font-semibold text-slate-700">Dokumentasi / Bukti Foto</p>
                                <div class="mt-2 flex h-32 items-center justify-center rounded-xl border border-dashed border-slate-300 bg-slate-100 sm:h-48">
                                    <div class="flex flex-col items-center text-slate-400">
                                        <i class="bi bi-image text-3xl" aria-hidden="true"></i>
                                        <span class="mt-2 text-sm font-medium">Foto Dokumentasi</span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </template>
        @endsection