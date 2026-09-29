@extends('layouts.app')

@section('title', 'Dashboard Piket - JurnalKita')

@section('sidebar')
    @include('layouts.guru-pengajar.sidebar', ['activePage' => 'piket'])
@endsection

@section('navbar')
    @include('layouts.guru-pengajar.navbar', ['activePage' => 'piket'])
@endsection

@section('content')
    <div class="min-h-full bg-slate-50 p-4 pb-24 font-sans sm:p-6 lg:p-8">
        <div class="mx-auto max-w-7xl">
            @if($isJamKosong ?? false)
                <div class="mb-5 rounded-xl border border-orange-200 bg-orange-50 p-4 text-orange-950" role="status">
                    <p class="text-sm font-bold">{{ $jamKosongNama ?: 'Jam Kosong Seharian' }}</p>
                    <p class="mt-0.5 text-xs text-orange-800">Hari ini tidak ada KBM reguler sehingga jurnal mengajar tidak perlu diperiksa.</p>
                </div>
            @elseif($eventDismissalTime ?? false)
                <div class="mb-5 rounded-xl border border-amber-200 bg-amber-50 p-4 text-amber-950" role="status">
                    <p class="text-sm font-bold">{{ $eventSchoolName ?: 'Pulang Cepat' }}</p>
                    <p class="mt-0.5 text-xs text-amber-800">KBM hari ini berakhir pukul {{ str_replace(':', '.', $eventDismissalTime) }}.</p>
                </div>
            @endif

            <section class="mb-7 grid gap-4 sm:grid-cols-2" aria-label="Aksi piket">
                <a href="{{ route('piket.dispensasi.form') }}" class="group flex items-center gap-4 rounded-2xl bg-gradient-to-br from-emerald-600 to-teal-700 p-5 text-white shadow-lg shadow-emerald-600/25 transition hover:-translate-y-0.5">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-white/20 text-xl"><i class="bi bi-file-earmark-plus-fill"></i></span>
                    <span><span class="block font-extrabold">Pengajuan Dispensasi</span><span class="mt-1 block text-xs text-emerald-50">Buat pengajuan dispensasi siswa ke Wakasek.</span></span>
                    <i class="bi bi-chevron-right ml-auto"></i>
                </a>
                <a href="{{ route('piket.kehadiran-siswa') }}" class="group flex items-center gap-4 rounded-2xl bg-gradient-to-br from-sky-600 to-indigo-700 p-5 text-white shadow-lg shadow-sky-600/25 transition hover:-translate-y-0.5">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-white/20 text-xl"><i class="bi bi-people-fill"></i></span>
                    <span><span class="block font-extrabold">Kehadiran Siswa</span><span class="mt-1 block text-xs text-sky-50">Catat izin, sakit, atau dispensasi siswa.</span></span>
                    <i class="bi bi-chevron-right ml-auto"></i>
                </a>
            </section>

            <section aria-label="Ringkasan" class="mb-7 grid gap-4 lg:grid-cols-3">
                <a href="{{ route('piket.kehadiran') }}" class="rounded-2xl border border-emerald-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                    <div class="flex items-start justify-between gap-4"><div><p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Kehadiran Guru</p><p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $presentTeacherCount + $sickTeacherCount + $permissionTeacherCount }}</p></div><i class="bi bi-person-check-fill rounded-xl bg-emerald-100 p-3 text-lg text-emerald-700"></i></div>
                    <p class="mt-4 text-xs font-bold text-slate-600">{{ $presentTeacherCount }} Hadir · {{ $permissionTeacherCount }} Izin · {{ $sickTeacherCount }} Sakit</p>
                    @if($pendingTeacherAbsenceCount > 0)<p class="mt-2 text-xs font-bold text-orange-700">{{ $pendingTeacherAbsenceCount }} izin menunggu approval</p>@endif
                </a>
                <a href="{{ route('piket.rekap-jurnal') }}" class="rounded-2xl border border-sky-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                    <div class="flex items-start justify-between gap-4"><div><p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Laporan Jurnal</p><p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $journalCount }}</p></div><i class="bi bi-journal-check rounded-xl bg-sky-100 p-3 text-lg text-sky-700"></i></div>
                    <p class="mt-4 text-xs font-bold text-slate-600">{{ $validatedJournalCount }} tervalidasi · {{ $pendingJournalCount }} menunggu validasi</p>
                    <p class="mt-2 text-xs text-sky-700">Buka riwayat dan rekap jurnal.</p>
                </a>
                <a href="{{ route('piket.dispensasi.history') }}" class="rounded-2xl border border-indigo-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                    <div class="flex items-start justify-between gap-4"><div><p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Riwayat Dispensasi</p><p class="mt-2 text-base font-extrabold text-slate-900">Pemantauan Dispensasi</p></div><i class="bi bi-file-earmark-person-fill rounded-xl bg-indigo-100 p-3 text-lg text-indigo-700"></i></div>
                    <p class="mt-4 text-xs font-bold text-indigo-700">{{ $dispensasiPendingCount }} menunggu keputusan Wakasek</p>
                </a>
            </section>

            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm" aria-labelledby="journal-list-title">
                <div class="border-b border-slate-100 px-4 py-4 sm:px-6">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h1 id="journal-list-title" class="text-base font-extrabold text-slate-800">Aktivitas &amp; Riwayat Logbook Kelas</h1>
                            <p class="mt-1 text-xs text-slate-500">Pilih kelas untuk memeriksa semua sesi jurnal hari ini, {{ \Carbon\Carbon::parse($today)->translatedFormat('d F Y') }}.</p>
                        </div>
                        <a href="{{ route('piket.rekap-jurnal') }}" class="inline-flex shrink-0 items-center justify-center gap-1.5 rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs font-bold text-slate-700 transition hover:bg-slate-50"><i class="bi bi-clock-history"></i>Lihat Riwayat &amp; Rekap</a>
                    </div>
                    <label class="relative mt-4 block">
                        <span class="sr-only">Cari kelas</span>
                        <i class="bi bi-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input id="class-journal-search" type="search" placeholder="Cari kelas..." class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-3 text-sm text-slate-700 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-100">
                    </label>
                </div>

                <div id="class-journal-list" class="divide-y divide-slate-100">
                    @forelse($classJournalSummaries as $classSummary)
                        <article data-class-journal data-class-name="{{ strtolower($classSummary->nama_kelas) }}" class="p-4 sm:p-5">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div class="flex min-w-0 items-start gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $classSummary->persetujuan ? 'bg-emerald-100 text-emerald-700' : ($classSummary->siap_disetujui_piket ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-500') }}"><i class="bi {{ $classSummary->persetujuan ? 'bi-patch-check-fill' : 'bi-journals' }}"></i></span>
                                    <div class="min-w-0">
                                        <h2 class="font-extrabold text-slate-800">Kelas {{ $classSummary->nama_kelas }}</h2>
                                        <p class="mt-0.5 text-xs text-slate-500">Jurnal terisi <strong class="text-slate-700">{{ $classSummary->total_terisi }}/{{ $classSummary->total_sesi }}</strong> sesi · tervalidasi pengurus <strong class="text-slate-700">{{ $classSummary->total_tervalidasi }}/{{ $classSummary->total_sesi }}</strong></p>
                                        @if($classSummary->persetujuan)
                                            <p class="mt-1 text-xs font-semibold text-emerald-700">Disetujui piket oleh {{ $classSummary->persetujuan->piket?->name ?? '-' }}.</p>
                                        @elseif($classSummary->total_sesi === 0)
                                            <p class="mt-1 text-xs font-semibold text-slate-500">Tidak ada jadwal KBM hari ini.</p>
                                        @elseif($classSummary->total_kosong > 0)
                                            <p class="mt-1 text-xs font-semibold text-rose-600">Belum lengkap: {{ $classSummary->total_terisi }}/{{ $classSummary->total_sesi }} jurnal terisi.</p>
                                        @elseif($classSummary->total_menunggu_validasi > 0)
                                            <p class="mt-1 text-xs font-semibold text-amber-700">Menunggu validasi pengurus pada {{ $classSummary->total_menunggu_validasi }} sesi.</p>
                                        @else
                                            <p class="mt-1 text-xs font-semibold text-indigo-700">Seluruh sesi lengkap dan siap diperiksa.</p>
                                        @endif
                                    </div>
                                </div>
                                <a href="{{ route('piket.jurnal-kelas.sessions', ['kelas' => $classSummary->id_kelas, 'tanggal' => $today]) }}" class="inline-flex shrink-0 items-center justify-center gap-1.5 rounded-xl {{ $classSummary->siap_disetujui_piket && ! $classSummary->persetujuan ? 'bg-[#155d50] text-white hover:bg-[#0f463c]' : 'border border-slate-300 bg-white text-slate-700 hover:bg-slate-50' }} px-3 py-2 text-xs font-bold transition"><i class="bi bi-eye"></i>Periksa Jurnal</a>
                            </div>
                        </article>
                    @empty
                        <div class="px-6 py-12 text-center text-sm text-slate-500"><i class="bi bi-people block text-3xl text-slate-300"></i><p class="mt-2 font-bold">Belum ada kelas yang dapat diperiksa.</p></div>
                    @endforelse
                </div>
                <div id="class-journal-empty" class="hidden px-6 py-12 text-center text-sm text-slate-500">Kelas tidak ditemukan.</div>
            </section>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const search = document.getElementById('class-journal-search');
            const entries = [...document.querySelectorAll('[data-class-journal]')];
            const empty = document.getElementById('class-journal-empty');

            search?.addEventListener('input', () => {
                const keyword = search.value.trim().toLowerCase();
                const visible = entries.filter((entry) => {
                    const matches = !keyword || entry.dataset.className.includes(keyword);
                    entry.classList.toggle('hidden', !matches);

                    return matches;
                }).length;

                empty?.classList.toggle('hidden', visible > 0);
            });
        });
    </script>
@endsection
