<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Riwayat Jurnal - JurnalKita</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo-mark-64.png') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-800">
    <header class="bg-[#0d6b5a] text-white">
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-4 sm:px-6">
            <a href="{{ route('public.jurnal') }}" class="flex items-center gap-3 no-underline text-white">
                <img src="{{ asset('img/logo-rounded.png') }}" alt="Logo JurnalKita" class="h-10 w-10 rounded-xl object-cover">
                <span class="text-lg font-extrabold">JurnalKita</span>
            </a>
            @auth
                <a href="{{ route('guru.riwayat') }}" class="rounded-xl border border-white/30 px-4 py-2 text-sm font-bold text-white transition hover:bg-white/10">Kembali ke Riwayat Saya</a>
            @else
                <a href="{{ route('login') }}" class="rounded-xl border border-white/30 px-4 py-2 text-sm font-bold text-white transition hover:bg-white/10">Login</a>
            @endauth
        </div>
    </header>

    <main class="mx-auto max-w-6xl space-y-6 px-4 py-8 sm:px-6 sm:py-10">
        @if (! $publicEnabled)
            <section class="mx-auto max-w-xl rounded-3xl border border-slate-200 bg-white p-8 text-center shadow-sm sm:p-12">
                <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-100 text-2xl text-amber-700"><i class="bi bi-lock-fill"></i></span>
                <h1 class="mt-5 text-2xl font-extrabold text-slate-900">Riwayat jurnal publik sedang dinonaktifkan</h1>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">Data jurnal tetap tersimpan. Pengunjung dapat melihatnya kembali setelah administrator mengaktifkan akses publik.</p>
                @auth
                    <div><a href="{{ route('guru.riwayat') }}" class="mt-5 inline-flex items-center gap-2 rounded-xl bg-[#0d6b5a] px-5 py-3 text-sm font-bold text-white hover:bg-[#0b5548]"><i class="bi bi-arrow-left"></i>Kembali ke Riwayat Saya</a></div>
                @else
                    <div><a href="{{ route('login') }}" class="mt-5 inline-flex items-center gap-2 rounded-xl bg-[#0d6b5a] px-5 py-3 text-sm font-bold text-white hover:bg-[#0b5548]"><i class="bi bi-box-arrow-in-right"></i> Login</a></div>
                @endauth
            </section>
        @else
            <section class="flex flex-col justify-between gap-5 rounded-3xl bg-gradient-to-br from-[#0d6b5a] to-[#123f37] p-6 text-white shadow-sm sm:flex-row sm:items-end sm:p-8">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[.18em] text-emerald-200">Informasi kegiatan belajar</p>
                    <h1 class="mt-2 text-3xl font-extrabold tracking-tight sm:text-4xl">Riwayat Jurnal</h1>
                    <p class="mt-2 max-w-2xl text-sm leading-relaxed text-white/75">Pilih jurnal hari ini atau arsip keseluruhan jurnal mengajar yang telah disetujui sekolah.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('public.jurnal', ['tab' => 'hari-ini']) }}" class="inline-flex w-fit items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-bold {{ ! $isHistory ? 'bg-white text-[#0d6b5a]' : 'bg-white/15 text-white hover:bg-white/25' }}"><i class="bi bi-calendar-day"></i>Jurnal Hari Ini</a>
                    <a href="{{ route('public.jurnal.riwayat') }}" class="inline-flex w-fit items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-bold {{ $isHistory ? 'bg-white text-[#0d6b5a]' : 'bg-white/15 text-white hover:bg-white/25' }}"><i class="bi bi-clock-history"></i>Riwayat Keseluruhan</a>

                </div>
            </section>

            @if ($showJamKosong ?? false)
                <section class="flex items-start gap-4 rounded-2xl border border-orange-200 bg-orange-50 p-5 text-orange-950" role="status">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-orange-100 text-xl text-orange-700"><i class="bi bi-calendar-x-fill"></i></span>
                    <div>
                        <p class="text-xs font-extrabold uppercase tracking-wide text-orange-700">Informasi kegiatan hari ini</p>
                        <h2 class="mt-1 text-base font-extrabold">{{ $jamKosong['nama'] }}</h2>
                        <p class="mt-1 text-sm">Hari ini tidak ada kegiatan belajar mengajar. Jurnal mengajar tidak diperlukan.</p>
                    </div>
                </section>
            @elseif ($showEvent)
                <section class="flex items-start gap-4 rounded-2xl border border-amber-200 bg-amber-50 p-5 text-amber-950" role="status">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-xl text-amber-700"><i class="bi bi-megaphone-fill"></i></span>
                    <div>
                        <p class="text-xs font-extrabold uppercase tracking-wide text-amber-700">Informasi kegiatan hari ini</p>
                        <h2 class="mt-1 text-base font-extrabold">{{ $event['name'] }}</h2>
                        <p class="mt-1 text-sm">Kegiatan selesai dan siswa pulang pukul <strong>{{ substr(str_replace(':', '.', $event['dismissal_time']), 0, 5) }} WIB</strong>.</p>
                    </div>
                </section>
            @endif

            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                @if ($isHistory)
                    <form method="GET" action="{{ route('public.jurnal.riwayat') }}" class="border-b border-slate-100 p-4 sm:px-5">
                        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                            <div><h2 class="font-extrabold text-slate-900">Arsip jurnal</h2><p class="mt-1 text-xs text-slate-500">Hanya jurnal yang telah disetujui ditampilkan.</p></div>
                            <div class="grid w-full gap-2 sm:grid-cols-2 lg:max-w-3xl lg:grid-cols-[minmax(12rem,1fr)_minmax(9rem,auto)_minmax(9rem,auto)_auto]">
                                <label class="relative block sm:col-span-2 lg:col-span-1">
                                    <span class="sr-only">Cari kelas atau guru</span>
                                    <i class="bi bi-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Cari kelas atau guru..." class="w-full rounded-lg border border-slate-200 py-2 pl-8 pr-3 text-sm focus:border-emerald-600 focus:outline-none">
                                </label>
                                <label class="block text-[11px] font-bold text-slate-500">Tanggal mulai
                                    <div class="relative mt-1">
                                        <i class="bi bi-calendar3 pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                                        <input type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}" class="w-full rounded-lg border border-slate-200 py-2 pl-8 pr-3 text-sm font-normal focus:border-emerald-600 focus:outline-none">
                                    </div>
                                </label>
                                <label class="block text-[11px] font-bold text-slate-500">Tanggal selesai
                                    <div class="relative mt-1">
                                        <i class="bi bi-calendar3 pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                                        <input type="date" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}" class="w-full rounded-lg border border-slate-200 py-2 pl-8 pr-3 text-sm font-normal focus:border-emerald-600 focus:outline-none">
                                    </div>
                                </label>
                                <div class="flex items-end gap-2">
                                    <button class="w-full rounded-lg bg-emerald-700 px-4 py-2 text-sm font-bold text-white hover:bg-emerald-800">Terapkan</button>
                                    @if (request()->filled('search') || request()->filled('tanggal_mulai') || request()->filled('tanggal_selesai'))
                                        <a href="{{ route('public.jurnal.riwayat') }}" class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50" aria-label="Reset filter"><i class="bi bi-arrow-counterclockwise"></i></a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                @else
                    <div class="border-b border-slate-100 p-4 sm:px-5"><h2 class="font-extrabold text-slate-900">Jurnal hari ini</h2><p class="mt-1 text-xs text-slate-500">Data hari ini otomatis tampil setelah jurnal disetujui.</p></div>
                @endif
                <div class="divide-y divide-slate-100">
                    @forelse ($journals as $journal)
                        <article class="p-5 sm:px-6">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div><p class="text-xs font-bold uppercase tracking-wide text-emerald-700">{{ $journal->mapel?->nama_mapel ?? 'Mata pelajaran' }} · {{ $journal->kelas?->nama_kelas ?? 'Kelas' }}</p><h3 class="mt-1 text-base font-extrabold text-slate-900">{{ $journal->user?->name ?? 'Guru' }}</h3></div>
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">{{ \Carbon\Carbon::parse($journal->tanggal)->translatedFormat('d F Y') }} · Jam ke-{{ $journal->jam_ke }}{{ $journal->jam_selesai && $journal->jam_selesai > $journal->jam_ke ? ' s/d '.$journal->jam_selesai : '' }}</span>
                            </div>
                            <p class="mt-3 whitespace-pre-line text-sm leading-relaxed text-slate-700">{{ $journal->materi ?: 'Materi belum dicatat.' }}</p>
                            @if ($journal->keterangan || $journal->catatan)
                                <p class="mt-2 text-xs leading-relaxed text-slate-500"><i class="bi bi-card-text mr-1"></i>{{ $journal->keterangan ?: $journal->catatan }}</p>
                            @endif
                            <div class="mt-3 flex flex-wrap gap-1.5 text-[11px] font-bold">
                                <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-emerald-800">{{ $journal->jumlah_hadir ?? 0 }} hadir</span>
                                <span class="rounded-full bg-amber-100 px-2.5 py-1 text-amber-800">{{ $journal->jumlah_sakit ?? 0 }} sakit</span>
                                <span class="rounded-full bg-blue-100 px-2.5 py-1 text-blue-800">{{ $journal->jumlah_izin ?? 0 }} izin</span>
                                <span class="rounded-full bg-rose-100 px-2.5 py-1 text-rose-800">{{ $journal->jumlah_alpa ?? 0 }} alpa</span>
                                <span class="rounded-full bg-indigo-100 px-2.5 py-1 text-indigo-800">{{ $journal->jumlah_dispensasi ?? 0 }} dispensasi</span>
                            </div>
                        </article>
                    @empty
                        <div class="px-6 py-14 text-center"><span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-xl text-slate-400"><i class="bi bi-journal-x"></i></span><h3 class="mt-3 text-sm font-bold text-slate-700">Belum ada jurnal yang disetujui</h3><p class="mt-1 text-sm text-slate-500">Jurnal yang disetujui akan muncul di sini secara otomatis.</p></div>
                    @endforelse
                </div>
                @if ($isHistory && $journals->hasPages())<div class="border-t border-slate-100 px-5 py-4">{{ $journals->links() }}</div>@endif
            </section>
        @endif
    </main>
    <footer class="py-8 text-center text-xs text-slate-400">© {{ now('Asia/Jakarta')->format('Y') }} JurnalKita</footer>
</body>
</html>
