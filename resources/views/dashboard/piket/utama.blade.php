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

                    {{-- CARD RIWAYAT DISPENSASI (Tepat dibawah card laporan jurnal, tidak menampilkan nama anak langsung, harus dipencet dulu baru muncul) --}}
                    <div class="overflow-hidden rounded-2xl border border-indigo-200 bg-white shadow-sm" aria-labelledby="dispensasi-history-title">
                        <button
                            type="button"
                            id="dispensasi-toggle"
                            aria-expanded="false"
                            aria-controls="dispensasi-body"
                            class="flex w-full items-center justify-between p-5 text-left transition hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-indigo-100 cursor-pointer"
                        >
                            <div class="flex items-center gap-3.5">
                                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-lg text-indigo-700">
                                    <i class="bi bi-file-earmark-person-fill"></i>
                                </span>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h2 id="dispensasi-history-title" class="font-bold text-slate-800 text-sm">Riwayat Dispensasi</h2>
                                        <span class="rounded-full bg-indigo-50 px-2.5 py-0.5 text-[11px] font-bold text-indigo-700 border border-indigo-200">
                                            {{ $dispensasiHistory->count() }} Siswa
                                        </span>
                                    </div>
                                    <p class="mt-1 text-xs text-indigo-600 font-semibold flex items-center gap-1">
                                        <i class="bi bi-hand-index-thumb"></i>
                                        <span>Klik untuk melihat daftar siswa</span>
                                    </p>
                                </div>
                            </div>
                            <i id="dispensasi-chevron" class="bi bi-chevron-down text-slate-400 text-base transition-transform duration-200"></i>
                        </button>

                        {{-- DAFTAR NAMA SISWA DISPENSASI — HANYA MUNCUL SAAT DIPENCET --}}
                        <div id="dispensasi-body" class="hidden divide-y divide-slate-100 border-t border-slate-100 max-h-64 overflow-y-auto">
                            @forelse($dispensasiHistory as $dispensasi)
                                <article class="flex flex-col gap-2 p-3.5 sm:flex-row sm:items-center sm:justify-between text-xs hover:bg-indigo-50/30 transition">
                                    <div class="min-w-0">
                                        <h3 class="truncate font-bold text-slate-800">{{ $dispensasi->siswa?->nama ?? 'Siswa' }}</h3>
                                        <p class="text-[11px] text-slate-500">{{ $dispensasi->siswa?->kelas?->nama_kelas ?? '-' }} · {{ $dispensasi->jenis_dispensasi }}</p>
                                        <p class="text-[11px] text-slate-400 truncate">{{ $dispensasi->deskripsi_waktu }} · {{ $dispensasi->alasan }}</p>
                                    </div>
                                    <div class="flex shrink-0 items-center gap-1.5">
                                        @php
                                            $sw = strtolower($dispensasi->status_waka ?? 'menunggu');
                                            $swClass = in_array($sw, ['disetujui','approved']) ? 'bg-emerald-100 text-emerald-800' : (in_array($sw, ['ditolak','rejected']) ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800');
                                            $swLabel = in_array($sw, ['disetujui','approved']) ? 'Disetujui' : (in_array($sw, ['ditolak','rejected']) ? 'Ditolak' : 'Menunggu');
                                        @endphp
                                        <span class="rounded-full px-2 py-0.5 text-[10px] font-bold {{ $swClass }}">{{ $swLabel }}</span>
                                        @if(in_array($sw, ['disetujui','approved']))
                                            <a href="{{ route('dispensasi.cetak', $dispensasi) }}" target="_blank" class="rounded-md bg-emerald-600 px-2 py-1 text-[10px] font-bold text-white transition hover:bg-emerald-700">Cetak</a>
                                        @endif
                                    </div>
                                </article>
                            @empty
                                <div class="px-4 py-8 text-center text-xs text-slate-400">Belum ada riwayat pengajuan dispensasi.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            {{-- DAFTAR AKTIVITAS JURNAL --}}
            <section id="aktivitas-jurnal" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm scroll-mt-6" aria-labelledby="journal-list-title">
                <div class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                    <div>
                        <h2 id="journal-list-title" class="font-bold text-slate-800">Aktivitas Jurnal &amp; Kehadiran Hari Ini</h2>
                        <p id="filter-description" class="mt-0.5 text-sm text-slate-500" aria-live="polite">{{ $journalCount }} jurnal tersimpan hari ini.</p>
                    </div>
                </div>

                {{-- SEARCHBAR & FILTER JURNAL (SEMUA, SUDAH DIVALIDASI, BELUM DIVALIDASI) --}}
                <div class="border-b border-slate-100 px-5 py-3 sm:px-6 space-y-3">
                    {{-- SEARCHBAR --}}
                    <div class="relative">
                        <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                        <input
                            type="search"
                            id="journal-search"
                            placeholder="Cari nama guru, kelas, atau materi..."
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-9 pr-9 text-sm text-slate-700 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100"
                        >
                        <button type="button" id="journal-search-clear" class="absolute right-3 top-1/2 -translate-y-1/2 hidden text-slate-400 hover:text-slate-600">
                            <i class="bi bi-x-circle-fill text-xs"></i>
                        </button>
                    </div>

                    {{-- FILTER BUTTONS: SEMUA, SUDAH DIVALIDASI, BELUM DIVALIDASI --}}
                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button" data-journal-filter="all" class="journal-filter-btn rounded-xl px-3.5 py-1.5 text-xs font-bold transition bg-emerald-600 text-white shadow-xs">
                            Semua ({{ $journalCount }})
                        </button>
                        <button type="button" data-journal-filter="disetujui" class="journal-filter-btn rounded-xl px-3.5 py-1.5 text-xs font-bold transition bg-slate-100 text-slate-700 hover:bg-slate-200">
                            Sudah Divalidasi ({{ $validatedJournalCount }})
                        </button>
                        <button type="button" data-journal-filter="belum_divalidasi" class="journal-filter-btn rounded-xl px-3.5 py-1.5 text-xs font-bold transition bg-slate-100 text-slate-700 hover:bg-slate-200">
                            Belum Divalidasi ({{ $pendingJournalCount }})
                        </button>
                    </div>
                </div>

                <div id="journal-list" class="max-h-[34rem] divide-y divide-slate-100 overflow-y-auto">
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
                        @endphp
                        <a href="{{ route('piket.jurnal.show', $journal) }}"
                           data-journal
                           data-validation="{{ $validation }}"
                           data-guru="{{ strtolower($journal->guru?->name ?? '') }}"
                           data-kelas="{{ strtolower($journal->kelas?->nama_kelas ?? '') }}"
                           data-materi="{{ strtolower($journal->materi ?? '') }}"
                           class="block p-4 transition hover:bg-emerald-50/50 sm:p-5">
                            <div class="flex gap-3">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700"><i class="bi bi-journal-text"></i></span>
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                        <div class="min-w-0">
                                            <h3 class="truncate font-bold text-slate-800">{{ $journal->guru?->name ?? 'Guru tidak ditemukan' }}</h3>
                                            <p class="mt-0.5 text-sm text-slate-500">{{ $journal->mapel?->nama_mapel ?? 'Mata pelajaran' }} · {{ $journal->kelas?->nama_kelas ?? 'Kelas' }}</p>
                                        </div>
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-bold text-emerald-700 ring-1 ring-emerald-200">Hadir · Jurnal terisi</span>
                                            <span class="inline-flex rounded-full px-2.5 py-1 text-[11px] font-bold ring-1 {{ $validationClass }}">{{ $validationLabel }}</span>
                                        </div>
                                    </div>
                                    <div class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-xs font-medium text-slate-500">
                                        <span><i class="bi bi-clock mr-1 text-slate-400"></i>Jam {{ $journal->jam_ke }}{{ $journal->jam_selesai && $journal->jam_selesai !== $journal->jam_ke ? '–'.$journal->jam_selesai : '' }}</span>
                                        <span class="line-clamp-1"><i class="bi bi-book mr-1 text-slate-400"></i>{{ $journal->materi }}</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div id="empty-state" class="px-6 py-14 text-center">
                            <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-xl text-slate-400"><i class="bi bi-journal-x"></i></span>
                            <p class="mt-3 font-semibold text-slate-700">Belum ada jurnal yang diisi hari ini.</p>
                            <p class="mt-1 text-sm text-slate-500">Guru akan tercatat hadir otomatis setelah jurnal pembelajaran tersimpan.</p>
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
                    btn.className = 'journal-filter-btn rounded-xl px-3.5 py-1.5 text-xs font-bold transition bg-emerald-600 text-white shadow-xs';
                } else {
                    btn.className = 'journal-filter-btn rounded-xl px-3.5 py-1.5 text-xs font-bold transition bg-slate-100 text-slate-700 hover:bg-slate-200';
                }
            });
        }

        function applyFilters() {
            const q = searchInput.value.toLowerCase().trim();
            let visible = 0;
            entries.forEach((e) => {
                const matchFilter = activeFilter === 'all' || e.dataset.validation === activeFilter;
                const matchSearch = !q || e.dataset.guru.includes(q) || e.dataset.kelas.includes(q) || e.dataset.materi.includes(q);
                const show = matchFilter && matchSearch;
                e.classList.toggle('hidden', !show);
                if (show) visible++;
            });
            const labels = {
                disetujui:        'Jurnal yang sudah divalidasi pengurus kelas.',
                belum_divalidasi: 'Jurnal yang menunggu validasi pengurus kelas.',
                all:              '{{ $journalCount }} jurnal tersimpan hari ini.',
            };
            desc.textContent = (labels[activeFilter] || labels.all) + (q ? ` (pencarian: "${searchInput.value}")` : '');
            filteredEmpty.classList.toggle('hidden', visible !== 0 || entries.length === 0);
            searchClear.classList.toggle('hidden', !q);
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
        searchInput.addEventListener('input', applyFilters);
        searchClear.addEventListener('click', () => {
            searchInput.value = '';
            applyFilters();
            searchInput.focus();
        });

        // ── Card Laporan Jurnal Click -> Scroll ke Tabel Aktivitas Jurnal ──
        const cardLaporanJurnal = document.getElementById('card-laporan-jurnal');
        if (cardLaporanJurnal) {
            cardLaporanJurnal.addEventListener('click', (e) => {
                // If clicked specific badge button inside card
                const filterBtn = e.target.closest('[data-filter-card]');
                if (filterBtn) {
                    activeFilter = filterBtn.dataset.filterCard;
                    applyFilters();
                }
                // Smooth scroll to aktivitas jurnal section
                aktivitasSection?.scrollIntoView({ behavior: 'smooth' });
            });
        }

        // ── Collapsible Riwayat Dispensasi ──────────────────────────────
        const toggleBtn  = document.getElementById('dispensasi-toggle');
        const body       = document.getElementById('dispensasi-body');
        const chevron    = document.getElementById('dispensasi-chevron');
        if (toggleBtn && body && chevron) {
            toggleBtn.addEventListener('click', () => {
                const open = body.classList.toggle('hidden');
                toggleBtn.setAttribute('aria-expanded', String(!open));
                chevron.classList.toggle('rotate-180', !open);
            });
        }
    });
    </script>
@endsection
