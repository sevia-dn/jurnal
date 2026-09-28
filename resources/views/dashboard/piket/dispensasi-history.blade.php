@extends('layouts.app')

@section('title', 'Riwayat & Pemantauan Dispensasi - Piket JurnalKita')

@section('sidebar')
    @include('layouts.guru-pengajar.sidebar', ['activePage' => 'piket'])
@endsection

@section('navbar')
    @include('layouts.guru-pengajar.navbar', ['activePage' => 'piket'])
@endsection

@section('content')
<div class="min-h-full bg-slate-50 p-4 pb-24 font-sans sm:p-6 lg:p-8">
    <div class="mx-auto max-w-7xl">

        {{-- BACK & NAVIGATION TABS --}}
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <a href="{{ route('dashboard.piket') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-700 transition hover:text-emerald-800">
                    <i class="bi bi-arrow-left"></i>
                    Kembali ke Dashboard Piket
                </a>
                <h1 class="mt-2 text-2xl font-extrabold text-slate-900">Riwayat Dispensasi</h1>
            </div>

        </div>

        {{-- STATS CARDS --}}
        <div class="mb-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Pengajuan</span>
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-600"><i class="bi bi-files"></i></span>
                </div>
                <p class="mt-2 text-2xl font-extrabold text-slate-900">{{ $totalCount }}</p>
            </div>

            <div class="rounded-2xl border border-amber-200 bg-amber-50/50 p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-amber-700">Menunggu Validasi</span>
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-100 text-amber-700"><i class="bi bi-hourglass-split"></i></span>
                </div>
                <p class="mt-2 text-2xl font-extrabold text-amber-800">{{ $pendingCount }}</p>
            </div>

            <div class="rounded-2xl border border-emerald-200 bg-emerald-50/50 p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-700">Disetujui Wakasek</span>
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700"><i class="bi bi-check-circle-fill"></i></span>
                </div>
                <p class="mt-2 text-2xl font-extrabold text-emerald-800">{{ $approvedCount }}</p>
            </div>

            <div class="rounded-2xl border border-rose-200 bg-rose-50/50 p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-rose-700">Ditolak</span>
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-rose-100 text-rose-700"><i class="bi bi-x-circle-fill"></i></span>
                </div>
                <p class="mt-2 text-2xl font-extrabold text-rose-800">{{ $rejectedCount }}</p>
            </div>
        </div>

        {{-- FILTER & SEARCH SECTION --}}
        <section class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-xs" aria-label="Filter dispensasi">
            <form method="GET" action="{{ route('piket.dispensasi.history') }}" class="space-y-4">
                {{-- SEARCH BAR --}}
                <div class="relative">
                    <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none"></i>
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Cari berdasarkan nama siswa, NIS, jenis, atau alasan dispensasi..."
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-10 text-sm text-slate-800 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100"
                    >
                    @if($search)
                        <a href="{{ route('piket.dispensasi.history', request()->except('search')) }}" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600" title="Hapus pencarian">
                            <i class="bi bi-x-circle-fill text-sm"></i>
                        </a>
                    @endif
                </div>

                {{-- FILTER DROPDOWNS --}}
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    {{-- STATUS --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Status Validasi</label>
                        <select name="status" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-700 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                            <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Semua Status</option>
                            <option value="menunggu" {{ $status === 'menunggu' ? 'selected' : '' }}>Menunggu Validasi</option>
                            <option value="disetujui" {{ $status === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                            <option value="ditolak" {{ $status === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>

                    {{-- KELAS --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Kelas</label>
                        <select name="kelas_id" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-700 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                            <option value="">Semua Kelas</option>
                            @foreach($kelasList as $k)
                                <option value="{{ $k->id_kelas }}" {{ (string)$kelasId === (string)$k->id_kelas ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- TANGGAL MULAI --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" value="{{ $startDate }}" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs text-slate-700 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                    </div>

                    {{-- TANGGAL SELESAI --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" value="{{ $endDate }}" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs text-slate-700 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                    <a href="{{ route('piket.dispensasi.history') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                        Reset Filter
                    </a>
                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-4 py-2 text-xs font-bold text-white shadow-xs hover:bg-emerald-700 transition">
                        <i class="bi bi-funnel-fill text-xs"></i>
                        Terapkan Filter
                    </button>
                </div>
            </form>
        </section>

        {{-- LIST DATA DISPENSASI --}}
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xs">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 sm:px-6">
                <div class="flex items-center gap-2">
                    <h2 class="font-bold text-slate-800">Daftar Pengajuan Dispensasi</h2>
                    <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-bold text-slate-600">
                        {{ $dispensasis->total() }} Data
                    </span>
                </div>
            </div>

            @if($dispensasis->count() > 0)
                <div class="divide-y divide-slate-100">
                    @foreach($dispensasis as $dispensasi)
                        @php
                            $sw = strtolower($dispensasi->status_waka ?? 'menunggu');
                            $swClass = in_array($sw, ['disetujui', 'approved'])
                                ? 'bg-emerald-100 text-emerald-800 border-emerald-200'
                                : (in_array($sw, ['ditolak', 'rejected'])
                                    ? 'bg-rose-100 text-rose-800 border-rose-200'
                                    : 'bg-amber-100 text-amber-800 border-amber-200');
                            $swLabel = in_array($sw, ['disetujui', 'approved'])
                                ? 'Disetujui'
                                : (in_array($sw, ['ditolak', 'rejected']) ? 'Ditolak' : 'Menunggu Validasi');
                        @endphp
                        <article class="flex flex-col gap-4 p-4 sm:flex-row sm:items-center sm:justify-between sm:p-5 hover:bg-slate-50/70 transition">
                            <div class="flex items-start gap-3.5 min-w-0 flex-1">
                                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-indigo-700 text-lg">
                                    <i class="bi bi-person-badge"></i>
                                </span>
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="font-bold text-slate-900 text-sm sm:text-base">{{ $dispensasi->siswa?->nama ?? 'Nama Siswa' }}</h3>
                                        <span class="rounded-md bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-600">
                                            {{ $dispensasi->siswa?->kelas?->nama_kelas ?? 'Kelas -' }}
                                        </span>
                                        @if($dispensasi->siswa?->nis)
                                            <span class="text-[11px] text-slate-400">NIS: {{ $dispensasi->siswa->nis }}</span>
                                        @endif
                                    </div>

                                    <div class="mt-1.5 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500">
                                        <span class="font-semibold text-indigo-700">
                                            <i class="bi bi-tag-fill mr-1 text-indigo-400"></i>{{ $dispensasi->jenis_dispensasi }}
                                        </span>
                                        <span>
                                            <i class="bi bi-clock-history mr-1 text-slate-400"></i>{{ $dispensasi->deskripsi_waktu }}
                                        </span>
                                        @if($dispensasi->tanggal)
                                            <span>
                                                <i class="bi bi-calendar3 mr-1 text-slate-400"></i>{{ \Carbon\Carbon::parse($dispensasi->tanggal)->translatedFormat('d M Y') }}{{ $dispensasi->tanggal_selesai && $dispensasi->tanggal_selesai !== $dispensasi->tanggal ? ' s/d '.\Carbon\Carbon::parse($dispensasi->tanggal_selesai)->translatedFormat('d M Y') : '' }}
                                            </span>
                                        @endif
                                    </div>

                                    <p class="mt-2 text-xs text-slate-600 bg-slate-50 rounded-lg p-2.5 border border-slate-100">
                                        <span class="font-bold text-slate-700">Alasan:</span> {{ $dispensasi->alasan }}
                                    </p>

                                    <div class="mt-2 flex items-center gap-2 text-[11px] text-slate-400">
                                        <span>Diajukan oleh: <strong class="text-slate-600">{{ $dispensasi->pembuat?->name ?? 'Guru Piket' }}</strong></span>
                                        <span>•</span>
                                        <span>{{ $dispensasi->created_at?->diffForHumans() ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col sm:items-end justify-between gap-3 shrink-0">
                                <span class="inline-flex items-center gap-1.5 self-start sm:self-end rounded-full border px-3 py-1 text-xs font-bold {{ $swClass }}">
                                    @if(in_array($sw, ['disetujui', 'approved']))
                                        <i class="bi bi-check-circle-fill"></i>
                                    @elseif(in_array($sw, ['ditolak', 'rejected']))
                                        <i class="bi bi-x-circle-fill"></i>
                                    @else
                                        <i class="bi bi-hourglass-split"></i>
                                    @endif
                                    {{ $swLabel }}
                                </span>

                                <div class="flex items-center gap-2">
                                    @if(in_array($sw, ['disetujui', 'approved']))
                                        <a href="{{ route('dispensasi.cetak', $dispensasi) }}" target="_blank" class="inline-flex items-center gap-1 rounded-xl bg-emerald-600 px-3 py-1.5 text-xs font-bold text-white shadow-xs transition hover:bg-emerald-700">
                                            <i class="bi bi-printer-fill"></i>
                                            Cetak
                                        </a>
                                        @if($dispensasi->token_verifikasi)
                                            <button
                                                type="button"
                                                data-qr-url="{{ route('dispensasi.verify', $dispensasi->token_verifikasi) }}"
                                                data-qr-name="{{ $dispensasi->siswa?->nama ?? 'Siswa' }}"
                                                data-qr-kelas="{{ $dispensasi->siswa?->kelas?->nama_kelas ?? '-' }}"
                                                data-qr-alasan="{{ $dispensasi->alasan }}"
                                                data-qr-waktu="{{ $dispensasi->deskripsi_waktu }}"
                                                class="inline-flex items-center gap-1 rounded-xl bg-slate-800 px-3 py-1.5 text-xs font-bold text-white shadow-xs transition hover:bg-slate-700"
                                            >
                                                <i class="bi bi-qr-code"></i>
                                                QR Izin
                                            </button>
                                        @endif
                                    @else
                                        <span class="text-xs text-slate-400 italic">Menunggu persetujuan Wakasek</span>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                @if($dispensasis->hasPages())
                    <div class="border-t border-slate-100 p-4">
                        {{ $dispensasis->links() }}
                    </div>
                @endif
            @else
                <div class="p-12 text-center">
                    <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-2xl text-slate-400">
                        <i class="bi bi-inbox"></i>
                    </span>
                    <h3 class="mt-3 text-base font-bold text-slate-800">Tidak ada data dispensasi</h3>
                    <p class="mt-1 text-xs text-slate-500">
                        @if($search || $status !== 'all' || $kelasId || $startDate || $endDate)
                            Tidak ada pengajuan dispensasi yang cocok dengan filter pencarian.
                        @else
                            Belum ada riwayat pengajuan dispensasi yang tercatat di sistem.
                        @endif
                    </p>
                    @if($search || $status !== 'all' || $kelasId || $startDate || $endDate)
                        <a href="{{ route('piket.dispensasi.history') }}" class="mt-4 inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-700 shadow-xs hover:bg-slate-50">
                            Reset Semua Filter
                        </a>
                    @endif
                </div>
            @endif
        </section>

    </div>
</div>

{{-- MODAL QR DISPENSASI --}}
<div id="dispensasi-qr-modal" class="fixed inset-0 z-[80] hidden items-center justify-center bg-slate-950/60 p-4" role="dialog" aria-modal="true" aria-labelledby="dispensasi-qr-title">
    <div class="w-full max-w-sm rounded-2xl bg-white p-6 text-center shadow-2xl">
        <div class="flex items-start justify-between gap-4 text-left">
            <div>
                <h2 id="dispensasi-qr-title" class="text-base font-extrabold text-slate-800">QR Izin Dispensasi</h2>
                <p id="dispensasi-qr-name" class="mt-1 text-xs font-semibold text-slate-700"></p>
                <p id="dispensasi-qr-info" class="text-[11px] text-slate-400"></p>
            </div>
            <button type="button" data-close-qr class="rounded-lg p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-700" aria-label="Tutup"><i class="bi bi-x-lg"></i></button>
        </div>
        <img id="dispensasi-qr-image" class="mx-auto mt-5 h-52 w-52 rounded-xl border border-slate-200 p-2" alt="QR verifikasi dispensasi">
        <p class="mt-4 text-xs leading-relaxed text-slate-500">Siswa dapat memperlihatkan kode QR ini kepada petugas keamanan/satpam saat meninggalkan atau kembali ke sekolah.</p>
        <a id="dispensasi-qr-link" target="_blank" class="mt-4 inline-flex items-center gap-2 text-xs font-bold text-emerald-700 hover:text-emerald-800"><i class="bi bi-box-arrow-up-right"></i> Buka halaman verifikasi</a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const qrModal = document.getElementById('dispensasi-qr-modal');
    const qrImage = document.getElementById('dispensasi-qr-image');
    const qrLink = document.getElementById('dispensasi-qr-link');
    const qrName = document.getElementById('dispensasi-qr-name');
    const qrInfo = document.getElementById('dispensasi-qr-info');

    document.querySelectorAll('[data-qr-url]').forEach((button) => {
        button.addEventListener('click', () => {
            const verificationUrl = button.dataset.qrUrl;
            qrImage.src = `https://api.qrserver.com/v1/create-qr-code/?size=208x208&data=${encodeURIComponent(verificationUrl)}`;
            qrLink.href = verificationUrl;
            qrName.textContent = button.dataset.qrName;
            qrInfo.textContent = `${button.dataset.qrKelas} · ${button.dataset.qrWaktu}`;
            qrModal.classList.remove('hidden');
            qrModal.classList.add('flex');
        });
    });

    const closeQrModal = () => {
        qrModal.classList.add('hidden');
        qrModal.classList.remove('flex');
    };
    document.querySelectorAll('[data-close-qr]').forEach((button) => button.addEventListener('click', closeQrModal));
    qrModal?.addEventListener('click', (event) => {
        if (event.target === qrModal) {
            closeQrModal();
        }
    });
});
</script>
@endsection

