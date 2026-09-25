@extends('layouts.app')

@section('sidebar')
    @include('layouts.pengurus-kelas.sidebar', ['activePage' => 'dashboard'])
@endsection

@section('navbar')
    @include('layouts.pengurus-kelas.navbar', ['activePage' => 'dashboard'])
@endsection

@section('content')
<div class="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 lg:px-8">


    {{-- KARTU STATISTIK (3 CARD: KELAS HARI INI, PERLU PERSETUJUAN, KEHADIRAN SISWA) --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">

        {{-- 1. CARD KELAS HARI INI -> POP-UP JADWAL PEMBELAJARAN SATU HALAMAN --}}
        <button type="button"
            id="btn-open-jadwal"
            class="group flex min-h-36 justify-between rounded-2xl border border-emerald-200 bg-emerald-50/70 p-5 text-left shadow-2xs transition hover:-translate-y-0.5 hover:border-emerald-400 hover:shadow-sm focus:outline-none cursor-pointer">
            <div>
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-700 transition group-hover:text-emerald-700">Kelas Hari Ini</h2>
                <p class="mt-4 text-3xl font-extrabold text-emerald-700">{{ $totalSesi }}</p>
                <p class="mt-1 flex items-center gap-1 text-[11px] font-medium text-slate-500">
                    <span>Lihat jadwal pembelajaran</span>
                    <i class="bi bi-box-arrow-up-right text-[10px]" aria-hidden="true"></i>
                </p>
            </div>
            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-200/80 text-lg text-emerald-800 transition group-hover:bg-emerald-300/80">
                <i class="bi bi-calendar-week-fill" aria-hidden="true"></i>
            </span>
        </button>

        {{-- 2. CARD PERLU PERSETUJUAN -> SCROLL KE BAWAH KE BAGIAN ANTREAN --}}
        <a href="#antrean-validasi"
            id="btn-scroll-antrean"
            class="group flex min-h-36 justify-between rounded-2xl border border-amber-200 bg-amber-50/70 p-5 shadow-2xs transition hover:-translate-y-0.5 hover:border-amber-400 hover:shadow-sm focus:outline-none cursor-pointer">
            <div>
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-700 transition group-hover:text-amber-700">Perlu Persetujuan</h2>
                <p class="mt-4 text-3xl font-extrabold text-amber-600">{{ $perluPersetujuan }}</p>
                <p class="mt-1 flex items-center gap-1 text-[11px] font-medium text-slate-500">
                    <span>Logbook menunggu validasi</span>
                    <i class="bi bi-arrow-down-short text-base" aria-hidden="true"></i>
                </p>
            </div>
            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-200/80 text-lg text-amber-800 transition group-hover:bg-amber-300/80">
                <i class="bi bi-clipboard-check-fill" aria-hidden="true"></i>
            </span>
        </a>

        {{-- 3. CARD KEHADIRAN SISWA -> MENGARAH KE HALAMAN ABSENSI SISWA --}}
        <a href="{{ route('pengurus-kelas.kehadiran-siswa') }}"
            class="group flex min-h-36 justify-between rounded-2xl border border-emerald-200 bg-emerald-50/70 p-5 shadow-2xs transition hover:-translate-y-0.5 hover:border-emerald-400 hover:shadow-sm focus:outline-none cursor-pointer">
            <div>
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-700 transition group-hover:text-emerald-700">Kehadiran Siswa</h2>
                <p class="mt-4 text-3xl font-extrabold text-emerald-700">{{ $kehadiranSiswaText }}</p>
                <p class="mt-1 flex items-center gap-1 text-[11px] font-medium text-slate-500">
                    <span>Siswa hadir / total</span>
                    <i class="bi bi-arrow-right" aria-hidden="true"></i>
                </p>
            </div>
            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-200/80 text-lg text-emerald-800 transition group-hover:bg-emerald-300/80">
                <i class="bi bi-people-fill" aria-hidden="true"></i>
            </span>
        </a>
    </div>

    {{-- BAGIAN ANTREAN VALIDASI LOGBOOK GURU --}}
    <div id="antrean-validasi" class="mt-8 scroll-mt-6">
        <div class="flex items-center justify-between mb-3">
            <div>
                <h2 class="text-sm font-extrabold text-slate-800 uppercase tracking-wide">
                    Antrean Validasi Jurnal
                </h2>
            </div>
            <a href="{{ route('pengurus-kelas.jurnal-detail') }}" class="text-xs font-semibold text-emerald-600 hover:underline">
                Lihat Riwayat Logbook &rarr;
            </a>
        </div>

        @if(isset($jurnalAntrean) && $jurnalAntrean->count() > 0)
            <div class="space-y-3">
                @foreach($jurnalAntrean as $ja)
                    @php
                        $jamText = ($ja->jam_selesai && $ja->jam_selesai > $ja->jam_ke)
                            ? "Jam ke-{$ja->jam_ke}-{$ja->jam_selesai}"
                            : "Jam ke-{$ja->jam_ke}";
                        $tgl = \Carbon\Carbon::parse($ja->tanggal)->format('d/m/y');
                    @endphp
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 rounded-2xl border border-amber-200 bg-amber-50/40 p-4 shadow-2xs">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-slate-800">{{ $ja->mapel->nama_mapel ?? '-' }}</span>
                                <span class="text-slate-300">&bull;</span>
                                <span class="text-xs text-slate-600 font-semibold">{{ $ja->user->name ?? 'Guru' }}</span>
                                <span class="rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-700">Menunggu</span>
                            </div>
                            <p class="mt-1 text-xs text-slate-600 line-clamp-1">
                                <span class="font-semibold text-slate-700">Materi:</span> {{ $ja->materi }}
                            </p>
                            <div class="mt-1.5 flex flex-wrap gap-2 text-[11px]">
                                <span class="text-slate-500"><i class="bi bi-calendar3 mr-1"></i>{{ $tgl }} ({{ $jamText }})</span>
                                <span class="text-emerald-700 font-semibold">{{ $ja->jumlah_hadir }} Hadir</span>
                                @if($ja->lampiran)
                                    <span class="text-purple-700 font-semibold"><i class="bi bi-camera-fill mr-0.5"></i>Foto Live Tersedia</span>
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('pengurus-kelas.jurnal-detail', ['id' => $ja->id_jurnal]) }}"
                           class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-emerald-600 px-4 py-2 text-xs font-bold text-white hover:bg-emerald-700 shadow-2xs transition shrink-0">
                            <i class="bi bi-check2-circle"></i> Periksa &amp; Validasi
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-emerald-50 text-xl text-emerald-600 mb-2">
                    <i class="bi bi-check-all"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-800">Tidak Ada Antrean Validasi</h3>
                <p class="mt-1 text-xs text-slate-500">Semua logbook pembelajaran guru saat ini telah divalidasi atau belum ada logbook baru yang diserahkan.</p>
            </div>
        @endif
    </div>
</div>

{{-- MODAL POP-UP: JADWAL PEMBELAJARAN HARI INI (SATU HALAMAN) --}}
<div id="modal-jadwal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 p-4 backdrop-blur-xs transition-opacity duration-200" role="dialog" aria-modal="true" aria-labelledby="modal-jadwal-title">
    <div class="relative w-full max-w-2xl rounded-2xl bg-white p-5 shadow-2xl transition duration-200 sm:p-6 max-h-[85vh] flex flex-col">

        {{-- Header Modal --}}
        <div class="flex items-start justify-between border-b border-slate-100 pb-4">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700 text-lg">
                    <i class="bi bi-calendar-week-fill"></i>
                </span>
                <div>
                    <h3 id="modal-jadwal-title" class="font-extrabold text-slate-900 text-base sm:text-lg">
                        Jadwal Pembelajaran Hari Ini
                    </h3>
                    <p class="text-xs text-slate-500">
                        {{ $hariIni }}, {{ $tanggalFormatted }} · Kelas {{ $kelas->nama_kelas ?? '' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Content Modal (Daftar Jadwal) --}}
        <div class="mt-4 flex-1 overflow-y-auto space-y-3 pr-1">
            @if(isset($jadwals) && $jadwals->count() > 0)
                @foreach($jadwals as $jadwal)
                    @php
                        $jamText = ($jadwal->jam_selesai && $jadwal->jam_selesai > $jadwal->jam_mulai)
                            ? "Jam ke {$jadwal->jam_mulai}-{$jadwal->jam_selesai}"
                            : "Jam ke {$jadwal->jam_mulai}";
                        $jurnal = isset($jurnalHariIni) ? $jurnalHariIni->get($jadwal->jam_mulai) : null;
                        $hasJurnal = (bool) $jurnal;
                        $statusValidasi = $jurnal?->status_validasi;
                    @endphp
                    <div class="rounded-xl border {{ $hasJurnal ? ($statusValidasi === 'disetujui' ? 'border-emerald-200 bg-emerald-50/30' : 'border-amber-200 bg-amber-50/30') : 'border-slate-200 bg-slate-50/50' }} p-3.5 transition hover:shadow-2xs">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="rounded-md bg-emerald-100 px-2 py-0.5 text-[11px] font-bold text-emerald-800">
                                        {{ $jamText }}
                                    </span>
                                    <span class="text-xs font-bold text-slate-800 truncate">
                                        {{ $jadwal->mapel->nama_mapel ?? '-' }}
                                    </span>
                                </div>
                                <p class="mt-1 text-xs text-slate-600 flex items-center gap-1.5">
                                    <i class="bi bi-person-fill text-slate-400"></i>
                                    <span>{{ $jadwal->user->name ?? 'Guru Pengajar' }}</span>
                                </p>
                                @if($hasJurnal && $jurnal->materi)
                                    <p class="mt-1 text-[11px] text-slate-500 italic line-clamp-1">
                                        Materi: {{ $jurnal->materi }}
                                    </p>
                                @endif
                            </div>

                            <div class="shrink-0 flex items-center gap-2">
                                @if($hasJurnal)
                                    @if($statusValidasi === 'disetujui')
                                        <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-[11px] font-bold text-emerald-700 border border-emerald-200">
                                            <i class="bi bi-check-circle-fill"></i> Disetujui
                                        </span>
                                    @elseif($statusValidasi === 'ditolak')
                                        <span class="rounded-full bg-rose-100 px-2.5 py-0.5 text-[11px] font-bold text-rose-700 border border-rose-200">
                                            Perlu Revisi
                                        </span>
                                    @else
                                        <a href="{{ route('pengurus-kelas.jurnal-detail', ['id' => $jurnal->id_jurnal]) }}" class="rounded-full bg-amber-100 px-2.5 py-0.5 text-[11px] font-bold text-amber-800 border border-amber-200 hover:bg-amber-200 transition">
                                            Validasi Sekarang
                                        </a>
                                    @endif
                                @else
                                    <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-[11px] font-medium text-slate-500 border border-slate-200">
                                        Belum Diisi Guru
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="py-8 text-center text-xs text-slate-400">
                    <i class="bi bi-calendar-x text-2xl text-slate-300 block mb-1"></i>
                    Tidak ada jadwal KBM terdaftar untuk hari {{ $hariIni }}.
                </div>
            @endif
        </div>

        {{-- Footer Modal --}}
        <div class="mt-4 flex justify-end border-t border-slate-100 pt-3">
            <button type="button" id="btn-close-modal-jadwal-footer" class="rounded-xl bg-slate-100 px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-200 transition cursor-pointer">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // ── POP-UP MODAL JADWAL PEMBELAJARAN ────────────────────────────────
    const modalJadwal = document.getElementById('modal-jadwal');
    const btnOpenJadwal = document.getElementById('btn-open-jadwal');
    const btnCloseJadwal = document.getElementById('btn-close-modal-jadwal');
    const btnCloseJadwalFooter = document.getElementById('btn-close-modal-jadwal-footer');

    function openModal() {
        modalJadwal.classList.remove('hidden');
        modalJadwal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    }

    function closeModal() {
        modalJadwal.classList.add('hidden');
        modalJadwal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    }

    btnOpenJadwal?.addEventListener('click', openModal);
    btnCloseJadwal?.addEventListener('click', closeModal);
    btnCloseJadwalFooter?.addEventListener('click', closeModal);
    modalJadwal?.addEventListener('click', (e) => {
        if (e.target === modalJadwal) closeModal();
    });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modalJadwal.classList.contains('hidden')) {
            closeModal();
        }
    });

    // ── SMOOTH SCROLL KE ANTREAN VALIDASI ───────────────────────────────
    const btnScrollAntrean = document.getElementById('btn-scroll-antrean');
    const antreanSection = document.getElementById('antrean-validasi');
    btnScrollAntrean?.addEventListener('click', (e) => {
        e.preventDefault();
        antreanSection?.scrollIntoView({ behavior: 'smooth' });
    });
});
</script>
@endsection
