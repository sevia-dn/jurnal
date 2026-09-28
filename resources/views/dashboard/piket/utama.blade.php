@extends('layouts.app')

@section('title', 'Monitoring Jurnal Piket - JurnalKita')

@section('sidebar')
    @include('layouts.guru-pengajar.sidebar', ['activePage' => 'piket'])
@endsection

@section('navbar')
    @include('layouts.guru-pengajar.navbar', ['activePage' => 'piket'])
@endsection

@section('content')
    <div id="piket-dashboard" class="min-h-full bg-slate-50 p-4 pb-24 font-sans sm:p-6 lg:p-8">
        <div class="mx-auto max-w-7xl">

            {{-- AKSI CEPAT --}}
            <section class="mb-7 grid gap-4 sm:grid-cols-2 xl:grid-cols-3" aria-label="Aksi piket">
                <a href="{{ route('piket.dispensasi.form') }}" class="group flex items-center gap-4 rounded-2xl border border-emerald-300 bg-gradient-to-br from-emerald-600 to-teal-700 p-5 text-white shadow-lg shadow-emerald-600/25 transition hover:-translate-y-0.5 hover:shadow-xl">
                    <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white/20 text-2xl"><i class="bi bi-file-earmark-plus-fill"></i></span>
                    <span class="min-w-0"><span class="block text-base font-extrabold">Pengajuan Dispensasi</span><span class="mt-1 block text-xs text-emerald-50">Buat dan kirim pengajuan dispensasi siswa ke Wakasek.</span></span>
                    <i class="bi bi-chevron-right ml-auto text-xl text-emerald-100 transition group-hover:translate-x-1"></i>
                </a>
                {{-- CARD LAPOR KEHADIRAN GURU -> Mengarah ke form pengisian izin/sakit guru --}}
                <a href="{{ route('piket.kehadiran.form') }}" class="group flex items-center gap-4 rounded-2xl border border-amber-300 bg-gradient-to-br from-amber-500 to-orange-600 p-5 text-white shadow-lg shadow-amber-600/25 transition hover:-translate-y-0.5 hover:shadow-xl">
                    <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white/20 text-2xl"><i class="bi bi-person-exclamation"></i></span>
                    <span class="min-w-0"><span class="block text-base font-extrabold">Lapor Kehadiran Guru</span><span class="mt-1 block text-xs text-amber-50">Form pencatatan sakit atau izin guru yang tidak masuk sekolah.</span></span>
                    <i class="bi bi-chevron-right ml-auto text-xl text-amber-100 transition group-hover:translate-x-1"></i>
                </a>
                <a href="{{ route('piket.kehadiran-siswa') }}" class="group flex items-center gap-4 rounded-2xl border border-sky-300 bg-gradient-to-br from-sky-600 to-indigo-700 p-5 text-white shadow-lg shadow-sky-600/25 transition hover:-translate-y-0.5 hover:shadow-xl">
                    <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white/20 text-2xl"><i class="bi bi-people-fill"></i></span>
                    <span class="min-w-0"><span class="block text-base font-extrabold">Kehadiran Siswa</span><span class="mt-1 block text-xs text-sky-50">Pilih kelas dan catat status izin, sakit, atau dispensasi siswa.</span></span>
                    <i class="bi bi-chevron-right ml-auto text-xl text-sky-100 transition group-hover:translate-x-1"></i>
                </a>
            </section>

            {{-- RINGKASAN: KEHADIRAN (KIRI) & LAPORAN JURNAL + RIWAYAT DISPENSASI (KANAN) --}}
            <section aria-label="Ringkasan jurnal, kehadiran, dan dispensasi" class="mb-7 grid gap-4 lg:grid-cols-2 lg:items-start">

                {{-- CARD KEHADIRAN GURU — berisi ringkasan, dipencet mengarah ke halaman daftar guru yang hadir di hari itu --}}
                <a href="{{ route('piket.kehadiran') }}"
                   class="group flex flex-col justify-between rounded-2xl border border-emerald-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-emerald-100">
                    <div>
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Kehadiran Guru</p>
                                <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $presentTeacherCount + $sickTeacherCount + $permissionTeacherCount }}</p>
                            </div>
                            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-100 text-lg text-emerald-700 transition group-hover:bg-emerald-200">
                                <i class="bi bi-person-check-fill"></i>
                            </span>
                        </div>
                        <div class="mt-4 flex flex-wrap gap-2 text-xs font-bold">
                            <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-emerald-800">{{ $presentTeacherCount }} Hadir</span>
                            <span class="rounded-full bg-amber-100 px-2.5 py-1 text-amber-800">{{ $permissionTeacherCount }} Izin</span>
                            <span class="rounded-full bg-rose-100 px-2.5 py-1 text-rose-800">{{ $sickTeacherCount }} Sakit</span>
                        </div>
                        <p class="mt-3 text-xs text-slate-500">
                            Total guru tercatat hari ini (hadir dari jurnal KBM &amp; tidak hadir dari laporan piket).
                        </p>
                    </div>

                </a>

                {{-- SISI KANAN: CARD LAPORAN JURNAL + CARD RIWAYAT DISPENSASI TEPAT DI BAWAHNYA --}}
                <div class="flex flex-col gap-4">

                    {{-- CARD LAPORAN JURNAL — dipencet langsung scroll ke bawah ke bagian tabel aktivitas jurnal --}}
                    <div id="card-laporan-jurnal" class="group cursor-pointer rounded-2xl border border-sky-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-sky-100">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Laporan Jurnal</p>
                                <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $journalCount }}</p>
                            </div>
                            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-sky-100 text-lg text-sky-700 transition group-hover:bg-sky-200">
                                <i class="bi bi-journal-check"></i>
                            </span>
                        </div>
                        <div class="mt-4 flex flex-wrap gap-2 text-xs font-bold">
                            <button type="button" data-filter-card="disetujui" class="rounded-full bg-emerald-100 px-2.5 py-1 text-emerald-800 transition hover:bg-emerald-200">
                                {{ $validatedJournalCount }} Sudah Divalidasi
                            </button>
                            <button type="button" data-filter-card="belum_divalidasi" class="rounded-full bg-amber-100 px-2.5 py-1 text-amber-800 transition hover:bg-amber-200">
                                {{ $pendingJournalCount }} Menunggu Validasi
                            </button>
                        </div>

                    </div>

                    {{-- CARD RIWAYAT DISPENSASI (Mengarahkan ke halaman riwayat & pemantauan dispensasi) --}}
                    <a href="{{ route('piket.dispensasi.history') }}" class="group flex items-center justify-between rounded-2xl border border-indigo-200 bg-white p-5 shadow-xs transition hover:-translate-y-0.5 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-indigo-100 !no-underline" aria-label="Riwayat & Pemantauan Dispensasi">
                        <div class="flex items-center gap-3.5">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-lg text-indigo-700 transition group-hover:bg-indigo-200">
                                <i class="bi bi-file-earmark-person-fill"></i>
                            </span>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h2 class="font-bold text-slate-800 text-sm">Riwayat &amp; Pemantauan Dispensasi</h2>
                                </div>
                                <p class="mt-1 text-xs text-indigo-600 font-semibold flex items-center gap-1.5">
                                    @if($dispensasiPendingCount > 0)
                                        <span class="rounded-full bg-amber-100 px-2 py-0.5 text-[10px] text-amber-800 font-bold">{{ $dispensasiPendingCount }} Menunggu</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <i class="bi bi-chevron-right text-slate-400 text-base transition group-hover:translate-x-1 group-hover:text-indigo-600"></i>
                    </a>
                </div>
            </section>

{{-- DAFTAR AKTIVITAS & RIWAYAT LOGBOOK MENGAJAR --}}
            <section id="aktivitas-jurnal" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm scroll-mt-6" aria-labelledby="journal-list-title">
                {{-- HEADER & INFORMASI JUMLAH --}}
                <div class="border-b border-slate-100 px-4 py-3.5 sm:px-6">
                    <h2 id="journal-list-title" class="font-bold text-slate-800 text-base">Aktivitas &amp; Riwayat Logbook</h2>
                    <p id="filter-description" class="mt-0.5 text-xs text-slate-500" aria-live="polite">
                        {{ $journalCount }} logbook ditampilkan
                        @if($filterDate)
                            ({{ \Carbon\Carbon::parse($filterDate)->translatedFormat('d F Y') }})
                        @elseif($filterStart || $filterEnd)
                            ({{ $filterStart ? \Carbon\Carbon::parse($filterStart)->translatedFormat('d M Y') : 'Awal' }} - {{ $filterEnd ? \Carbon\Carbon::parse($filterEnd)->translatedFormat('d M Y') : 'Sekarang' }})
                        @elseif($filterPreset && $filterPreset !== 'semua')
                            ({{ str_replace('_', ' ', $filterPreset) }})
                        @else
                            (Seluruh Riwayat)
                        @endif
                    </p>
                </div>

                {{-- AREA FILTER & SEARCHBAR --}}
                <div class="border-b border-slate-100 px-4 py-3 sm:px-6 space-y-3">
                    <form method="GET" action="{{ route('dashboard.piket') }}" class="space-y-2.5">
                        {{-- 1. SEARCHBAR UTAMA --}}
                        <div class="relative">
                            <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                            <input
                                type="search"
                                name="search"
                                id="journal-search"
                                value="{{ $search }}"
                                placeholder="Cari guru, kelas, mapel, materi..."
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2 pl-9 pr-8 text-xs text-slate-700 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-100"
                            >
                            <button type="button" id="journal-search-clear" class="absolute right-2.5 top-1/2 -translate-y-1/2 {{ $search ? '' : 'hidden' }} text-slate-400 hover:text-slate-600">
                                <i class="bi bi-x-circle-fill text-xs"></i>
                            </button>
                        </div>

                        {{-- 2. CHIPS PRESET TANGGAL (HORIZONTAL SCROLL DI MOBILE) --}}
                        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs no-scrollbar -mx-4 px-4 sm:mx-0 sm:px-0">
                            <a href="{{ route('dashboard.piket', array_merge(request()->except(['preset', 'tanggal', 'tanggal_mulai', 'tanggal_selesai']), ['preset' => 'semua'])) }}"
                               class="whitespace-nowrap rounded-lg px-2.5 py-1 transition {{ ($filterPreset === 'semua' && !$filterDate && !$filterStart && !$filterEnd) ? 'bg-emerald-600 text-white font-bold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                Semua
                            </a>
                            <a href="{{ route('dashboard.piket', array_merge(request()->except(['preset', 'tanggal', 'tanggal_mulai', 'tanggal_selesai']), ['preset' => 'hari_ini'])) }}"
                               class="whitespace-nowrap rounded-lg px-2.5 py-1 transition {{ $filterPreset === 'hari_ini' ? 'bg-emerald-600 text-white font-bold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                Hari Ini
                            </a>
                            <a href="{{ route('dashboard.piket', array_merge(request()->except(['preset', 'tanggal', 'tanggal_mulai', 'tanggal_selesai']), ['preset' => '7_hari'])) }}"
                               class="whitespace-nowrap rounded-lg px-2.5 py-1 transition {{ $filterPreset === '7_hari' ? 'bg-emerald-600 text-white font-bold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                7 Hari
                            </a>
                            <a href="{{ route('dashboard.piket', array_merge(request()->except(['preset', 'tanggal', 'tanggal_mulai', 'tanggal_selesai']), ['preset' => '30_hari'])) }}"
                               class="whitespace-nowrap rounded-lg px-2.5 py-1 transition {{ $filterPreset === '30_hari' ? 'bg-emerald-600 text-white font-bold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                30 Hari
                            </a>
                        </div>

                        {{-- 3. FILTER TANGGAL CUSTOM (GRID 2 KOLOM DI MOBILE) --}}
                        <div class="grid grid-cols-2 sm:flex sm:items-center gap-2 pt-1">
                            <input
                                type="date"
                                name="tanggal_mulai"
                                value="{{ $filterStart ?? ($filterDate ?? '') }}"
                                title="Tanggal Mulai"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-2.5 py-1.5 text-xs text-slate-700 outline-none focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-100"
                            >
                            <input
                                type="date"
                                name="tanggal_selesai"
                                value="{{ $filterEnd ?? ($filterDate ?? '') }}"
                                title="Tanggal Selesai"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-2.5 py-1.5 text-xs text-slate-700 outline-none focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-100"
                            >
                            <div class="col-span-2 sm:col-span-1 flex items-center gap-2">
                                <button type="submit" class="flex-1 sm:flex-initial rounded-xl bg-emerald-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-emerald-700 transition">
                                    Cari
                                </button>
                                @if($filterDate || $filterStart || $filterEnd || $search || ($filterPreset && $filterPreset !== 'semua'))
                                    <a href="{{ route('dashboard.piket') }}" class="flex-1 sm:flex-initial text-center rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-500 hover:bg-slate-100 transition" title="Reset filter">
                                        Reset
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>

                    {{-- 4. FILTER STATUS VALIDASI (HORIZONTAL SCROLL DI MOBILE) --}}
                    <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs pt-2 border-t border-slate-100 no-scrollbar -mx-4 px-4 sm:mx-0 sm:px-0">
                        <button type="button" data-journal-filter="all" class="journal-filter-btn whitespace-nowrap rounded-lg px-2.5 py-1 font-bold transition bg-emerald-600 text-white">
                            Semua ({{ $journalCount }})
                        </button>
                        <button type="button" data-journal-filter="disetujui" class="journal-filter-btn whitespace-nowrap rounded-lg px-2.5 py-1 font-bold transition bg-slate-100 text-slate-700 hover:bg-slate-200">
                            Sudah Divalidasi ({{ $validatedJournalCount }})
                        </button>
                        <button type="button" data-journal-filter="belum_divalidasi" class="journal-filter-btn whitespace-nowrap rounded-lg px-2.5 py-1 font-bold transition bg-slate-100 text-slate-700 hover:bg-slate-200">
                            Belum Divalidasi ({{ $pendingJournalCount }})
                        </button>
                    </div>
                </div>

                <div id="journal-list" class="max-h-[36rem] divide-y divide-slate-100 overflow-y-auto">
                    @forelse($journals as $journal)
                        @php
                            $validation = $journal->status_validasi ?? 'belum_divalidasi';
                            $validationLabel = match ($validation) {
                                'disetujui' => 'Sudah divalidasi', 'ditolak' => 'Perlu revisi', default => 'Menunggu validasi',
                            };
                            $validationClass = match ($validation) {
                                'disetujui' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
                                'ditolak'   => 'bg-rose-50 text-rose-700 ring-rose-200',
                                default     => 'bg-amber-50 text-amber-700 ring-amber-200',
                            };
                            $tanggalDisplay = $journal->tanggal ? \Carbon\Carbon::parse($journal->tanggal)->translatedFormat('l, d M Y') : '-';
                        @endphp
                        <a href="{{ route('piket.jurnal.show', $journal) }}"
                           data-journal
                           data-validation="{{ $validation }}"
                           data-guru="{{ strtolower($journal->guru?->name ?? '') }}"
                           data-kelas="{{ strtolower($journal->kelas?->nama_kelas ?? '') }}"
                           data-materi="{{ strtolower($journal->materi ?? '') }}"
                           data-tanggal="{{ $journal->tanggal }}"
                           class="block p-4 transition hover:bg-emerald-50/50 sm:p-5">
                            <div class="flex gap-3">
                                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700 text-lg"><i class="bi bi-journal-text"></i></span>
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                        <div class="min-w-0">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <h3 class="truncate font-bold text-slate-800 text-sm sm:text-base">{{ $journal->guru?->name ?? 'Guru tidak ditemukan' }}</h3>
                                                <span class="inline-flex items-center gap-1 rounded-md bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600">
                                                    <i class="bi bi-calendar-event text-slate-400"></i>{{ $tanggalDisplay }}
                                                </span>
                                            </div>
                                            <p class="mt-0.5 text-xs sm:text-sm text-slate-500 font-medium">
                                                {{ $journal->mapel?->nama_mapel ?? 'Mata pelajaran' }} · {{ $journal->kelas?->nama_kelas ?? 'Kelas' }}
                                            </p>
                                        </div>
                                        <div class="flex flex-wrap items-center gap-1.5">
                                            <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-0.5 text-[11px] font-bold text-emerald-700 ring-1 ring-emerald-200">Hadir · Jurnal terisi</span>
                                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-[11px] font-bold ring-1 {{ $validationClass }}">{{ $validationLabel }}</span>
                                        </div>
                                    </div>

                                    <div class="mt-2.5 flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-600">
                                        <span class="font-medium text-slate-500"><i class="bi bi-clock mr-1 text-slate-400"></i>Jam ke-{{ $journal->jam_ke }}{{ $journal->jam_selesai && $journal->jam_selesai !== $journal->jam_ke ? ' s/d '.$journal->jam_selesai : '' }}</span>
                                        <span class="line-clamp-1"><strong class="text-slate-700 font-semibold">Materi:</strong> {{ $journal->materi ?: '-' }}</span>
                                    </div>

                                    @if($journal->keterangan || $journal->catatan)
                                        <p class="mt-1 line-clamp-1 text-xs text-slate-500">
                                            <i class="bi bi-card-text mr-1 text-slate-400"></i>{{ $journal->keterangan ?: $journal->catatan }}
                                        </p>
                                    @endif

                                    {{-- KEHADIRAN SISWA --}}
                                    <div class="mt-2 flex flex-wrap items-center gap-2 text-xs">
                                        <span class="inline-flex items-center gap-1 rounded-md bg-emerald-50 px-2 py-0.5 text-[11px] font-bold text-emerald-700">
                                            <i class="bi bi-people-fill"></i>
                                            {{ $journal->jumlah_hadir ?? 0 }} Siswa Hadir
                                        </span>
                                        @if(($journal->jumlah_tidak_hadir ?? 0) > 0)
                                            <span class="inline-flex items-center gap-1 rounded-md bg-rose-50 px-2 py-0.5 text-[11px] font-bold text-rose-700">
                                                <i class="bi bi-person-x-fill"></i>
                                                {{ $journal->jumlah_tidak_hadir }} Tidak Masuk
                                                <span class="text-rose-600 font-medium">({{ $journal->jumlah_sakit ?? 0 }}S, {{ $journal->jumlah_izin ?? 0 }}I, {{ $journal->jumlah_alpa ?? 0 }}A, {{ $journal->jumlah_dispensasi ?? 0 }}D)</span>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div id="empty-state" class="px-6 py-14 text-center">
                            <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-xl text-slate-400"><i class="bi bi-journal-x"></i></span>
                            <p class="mt-3 font-semibold text-slate-700">Belum ada riwayat logbook yang cocok.</p>
                            <p class="mt-1 text-xs text-slate-500">Gunakan preset tanggal atau reset filter untuk menampilkan seluruh data logbook KBM.</p>
                            <a href="{{ route('dashboard.piket', ['preset' => 'semua']) }}" class="mt-3 inline-flex items-center gap-1 rounded-xl bg-emerald-600 px-3 py-1.5 text-xs font-bold text-white shadow-xs hover:bg-emerald-700">
                                Tampilkan Semua Riwayat
                            </a>
                        </div>
                    @endforelse
                </div>
                <div id="filtered-empty" class="hidden px-6 py-14 text-center">
                    <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-xl text-slate-400"><i class="bi bi-inbox"></i></span>
                    <p class="mt-3 font-semibold text-slate-700">Tidak ada jurnal yang cocok.</p>
                </div>
            </section>

        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // ── Filter & Search Jurnal ──────────────────────────────────────
        const entries       = [...document.querySelectorAll('[data-journal]')];
        const desc          = document.getElementById('filter-description');
        const filteredEmpty = document.getElementById('filtered-empty');
        const searchInput   = document.getElementById('journal-search');
        const searchClear   = document.getElementById('journal-search-clear');
        const filterBtns    = [...document.querySelectorAll('[data-journal-filter]')];
        const aktivitasSection = document.getElementById('aktivitas-jurnal');

        let activeFilter = 'all';

        function updateFilterButtonStyles() {
            filterBtns.forEach(btn => {
                const isActive = btn.dataset.journalFilter === activeFilter;
                if (isActive) {
                    btn.className = 'journal-filter-btn rounded-xl px-3 py-1 text-xs font-bold transition bg-emerald-600 text-white shadow-xs';
                } else {
                    btn.className = 'journal-filter-btn rounded-xl px-3 py-1 text-xs font-bold transition bg-slate-100 text-slate-700 hover:bg-slate-200';
                }
            });
        }

        function applyFilters() {
            const q = searchInput ? searchInput.value.toLowerCase().trim() : '';
            let visible = 0;
            entries.forEach((e) => {
                const matchFilter = activeFilter === 'all' || e.dataset.validation === activeFilter;
                const matchSearch = !q ||
                    (e.dataset.guru && e.dataset.guru.includes(q)) ||
                    (e.dataset.kelas && e.dataset.kelas.includes(q)) ||
                    (e.dataset.materi && e.dataset.materi.includes(q)) ||
                    (e.dataset.tanggal && e.dataset.tanggal.includes(q));
                const show = matchFilter && matchSearch;
                e.classList.toggle('hidden', !show);
                if (show) visible++;
            });
            const labels = {
                disetujui:        'Logbook yang sudah divalidasi pengurus kelas.',
                belum_divalidasi: 'Logbook yang menunggu validasi pengurus kelas.',
                all:              '{{ $journalCount }} logbook ditampilkan.',
            };
            if (desc) {
                desc.textContent = (labels[activeFilter] || labels.all) + (q ? ` (pencarian: "${searchInput.value}")` : '');
            }
            if (filteredEmpty) {
                filteredEmpty.classList.toggle('hidden', visible !== 0 || entries.length === 0);
            }
            if (searchClear) {
                searchClear.classList.toggle('hidden', !q);
            }
            updateFilterButtonStyles();
        }

        // Filter button click in table header
        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                activeFilter = btn.dataset.journalFilter;
                applyFilters();
            });
        });

        // Search input events
        if (searchInput) {
            searchInput.addEventListener('input', applyFilters);
        }
        if (searchClear) {
            searchClear.addEventListener('click', () => {
                searchInput.value = '';
                applyFilters();
                searchInput.focus();
            });
        }

        // ── Card Laporan Jurnal Click -> Scroll ke Tabel Aktivitas Jurnal ──
        const cardLaporanJurnal = document.getElementById('card-laporan-jurnal');
        if (cardLaporanJurnal) {
            cardLaporanJurnal.addEventListener('click', (e) => {
                const filterBtn = e.target.closest('[data-filter-card]');
                if (filterBtn) {
                    activeFilter = filterBtn.dataset.filterCard;
                    applyFilters();
                }
                aktivitasSection?.scrollIntoView({ behavior: 'smooth' });
            });
        }
    });
    </script>
@endsection
