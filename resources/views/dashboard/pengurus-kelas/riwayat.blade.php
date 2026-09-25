@extends('layouts.app')

@section('title', 'Riwayat Logbook - JurnalKita')

@section('sidebar')
    @include('layouts.pengurus-kelas.sidebar', ['activePage' => 'riwayat'])
@endsection

@section('navbar')
    @include('layouts.pengurus-kelas.navbar', ['activePage' => 'riwayat'])
@endsection

@section('content')
    <div class="mx-auto w-full max-w-6xl px-4 py-6 pb-24 sm:px-6 lg:px-8">
        <header class="mb-5">
            <p class="text-xs font-bold uppercase tracking-wider text-emerald-700">{{ $kelas?->nama_kelas ?? 'Kelas' }}</p>
            <h1 class="mt-1 text-2xl font-extrabold text-slate-900">Riwayat Logbook</h1>
            <p class="mt-1 text-sm text-slate-500">Daftar seluruh logbook yang dikirim guru pengajar untuk kelas ini.</p>
        </header>

        <form method="GET" action="{{ route('pengurus-kelas.jurnal-detail') }}" class="mb-5 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="grid gap-3 sm:grid-cols-[minmax(0,1fr)_11rem_11rem_auto] sm:items-end">
                <label class="block">
                    <span class="text-xs font-bold text-slate-700">Cari logbook</span>
                    <span class="relative mt-1.5 block">
                        <i class="bi bi-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>
                        <input type="search" name="search" value="{{ $search }}" placeholder="Nama guru, mata pelajaran, atau materi..." class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-9 pr-3 text-sm text-slate-700 placeholder:text-slate-400 focus:border-emerald-600 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-100">
                    </span>
                </label>

                <label class="block">
                    <span class="text-xs font-bold text-slate-700">Mulai tanggal</span>
                    <input type="date" name="tanggal_mulai" value="{{ $tanggalMulai }}" onchange="this.form.submit()" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 focus:border-emerald-600 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-100">
                </label>

                <label class="block">
                    <span class="text-xs font-bold text-slate-700">Sampai tanggal</span>
                    <input type="date" name="tanggal_selesai" value="{{ $tanggalSelesai }}" onchange="this.form.submit()" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 focus:border-emerald-600 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-100">
                </label>

                @if($tanggalMulai || $tanggalSelesai || $search)
                    <a href="{{ route('pengurus-kelas.jurnal-detail') }}" class="inline-flex min-h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-50" aria-label="Hapus filter"><i class="bi bi-x-lg"></i></a>
                @endif
            </div>
        </form>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm" aria-label="Daftar riwayat logbook">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <p class="text-sm font-bold text-slate-800">{{ $jurnals->total() }} logbook ditemukan</p>
                @if($tanggalMulai || $tanggalSelesai)
                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">
                        {{ $tanggalMulai ? \Carbon\Carbon::parse($tanggalMulai)->translatedFormat('d M Y') : 'Awal' }} – {{ $tanggalSelesai ? \Carbon\Carbon::parse($tanggalSelesai)->translatedFormat('d M Y') : 'Sekarang' }}
                    </span>
                @endif
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($jurnals as $jurnal)
                    @php
                        $status = $jurnal->status_validasi ?? 'belum_divalidasi';
                        $statusLabel = match($status) {
                            'disetujui' => 'Disetujui',
                            'ditolak' => 'Perlu revisi',
                            default => 'Menunggu validasi',
                        };
                        $statusClass = match($status) {
                            'disetujui' => 'bg-emerald-100 text-emerald-800',
                            'ditolak' => 'bg-rose-100 text-rose-800',
                            default => 'bg-amber-100 text-amber-800',
                        };
                    @endphp
                    <a href="{{ route('pengurus-kelas.jurnal-detail', ['id' => $jurnal->id_jurnal]) }}" class="block p-4 transition hover:bg-emerald-50/40 sm:p-5">
                        <div class="flex gap-3">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700"><i class="bi bi-journal-text"></i></span>
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                    <div class="min-w-0">
                                        <h2 class="truncate text-sm font-extrabold text-slate-800">{{ $jurnal->user?->name ?? 'Guru tidak ditemukan' }}</h2>
                                        <p class="mt-0.5 text-sm text-slate-600">{{ $jurnal->mapel?->nama_mapel ?? 'Mata pelajaran' }}</p>
                                    </div>
                                    <span class="w-fit shrink-0 rounded-full px-2.5 py-1 text-[11px] font-bold {{ $statusClass }}">{{ $statusLabel }}</span>
                                </div>
                                <p class="mt-2 line-clamp-2 text-sm leading-relaxed text-slate-600">{{ $jurnal->materi ?: 'Tidak ada materi yang dicatat.' }}</p>
                                <div class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-xs font-medium text-slate-500">
                                    <span><i class="bi bi-calendar3 mr-1"></i>{{ \Carbon\Carbon::parse($jurnal->tanggal)->translatedFormat('d F Y') }}</span>
                                    <span><i class="bi bi-clock mr-1"></i>Jam ke-{{ $jurnal->jam_ke }}{{ $jurnal->jam_selesai && $jurnal->jam_selesai !== $jurnal->jam_ke ? '-'.$jurnal->jam_selesai : '' }}</span>
                                    <span class="font-semibold text-emerald-700"><i class="bi bi-people mr-1"></i>{{ $jurnal->jumlah_hadir ?? 0 }} hadir</span>
                                </div>
                            </div>
                            <i class="bi bi-chevron-right self-center text-slate-400"></i>
                        </div>
                    </a>
                @empty
                    <div class="px-6 py-14 text-center">
                        <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-xl text-slate-400"><i class="bi bi-journal-x"></i></span>
                        <h2 class="mt-3 text-sm font-bold text-slate-700">Logbook tidak ditemukan</h2>
                        <p class="mt-1 text-sm text-slate-500">Ubah tanggal atau kata kunci pencarian untuk melihat riwayat lain.</p>
                    </div>
                @endforelse
            </div>

            @if($jurnals->hasPages())
                <div class="border-t border-slate-100 px-4 py-3 sm:px-5">{{ $jurnals->links() }}</div>
            @endif
        </section>
    </div>
@endsection
