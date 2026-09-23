@extends('layouts.app')

@section('title', 'Rekap Jurnal Mengajar - JurnalKita')

@section('sidebar')
    @if(Auth::user()->role === 'admin')
        @include('layouts.admin.sidebar')
    @else
        @include('layouts.guru-pengajar.sidebar', ['activePage' => 'rekap'])
    @endif
@endsection

@section('navbar')
    @if(Auth::user()->role === 'admin')
        @include('layouts.admin.navbar')
    @else
        @include('layouts.guru-pengajar.navbar', ['activePage' => 'rekap'])
    @endif
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
        photoModalOpen: false,
        activePhotoUrl: '',
        openPhoto(url) {
            this.activePhotoUrl = url;
            this.photoModalOpen = true;
        }
    }"
    class="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 lg:px-8 font-sans"
>
    {{-- HEADER --}}
    <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2">
                <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800">
                    Rekapitulasi Mengajar
                </span>
                @if($isAdmin && $targetUser)
                    <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-800">
                        Guru: {{ $targetUser->name }}
                    </span>
                @endif
            </div>
            <h1 class="mt-2 text-2xl font-black text-slate-900 sm:text-3xl">Rekapitulasi Jurnal Guru</h1>
            <p class="mt-1 text-sm text-slate-500">
                Statistik kepatuhan pengisian jurnal, perbandingan sesi terjadwal vs terisi, dan rincian per sesi.
            </p>
        </div>
        <div class="flex items-center gap-2 self-start sm:self-auto">
            <a
                href="{{ route('guru.riwayat') }}"
                class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-700 shadow-xs hover:bg-slate-50"
            >
                <i class="bi bi-clock-history"></i>
                <span class="hidden sm:inline">Lihat</span> Riwayat Logbook
            </a>
        </div>
    </div>

    {{-- FILTER BAR (STICKY WITH BACKDROP BLUR) --}}
    <div class="sticky top-3 sm:top-5 z-20 mb-6 rounded-2xl border border-slate-200/80 bg-white/95 p-4 shadow-sm backdrop-blur-md">
        <form method="GET" action="{{ route('guru.jurnal.rekap') }}" class="flex flex-col gap-3 lg:flex-row lg:items-end">
            @if($isAdmin && $teachers->isNotEmpty())
                <div class="w-full lg:w-48">
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        <i class="bi bi-person mr-1"></i>Pilih Guru
                    </label>
                    <select
                        name="guru_id"
                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-700 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"
                    >
                        @foreach($teachers as $t)
                            <option value="{{ $t->id }}" {{ $targetUserId == $t->id ? 'selected' : '' }}>
                                {{ $t->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="grid grid-cols-2 gap-2 sm:grid-cols-4 flex-1">
                {{-- BULAN --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        <i class="bi bi-calendar3 mr-1"></i>Bulan
                    </label>
                    <select
                        name="bulan"
                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-700 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"
                    >
                        @php
                            $namaBulan = [
                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                            ];
                        @endphp
                        @foreach($namaBulan as $num => $nama)
                            <option value="{{ $num }}" {{ $bulan == $num ? 'selected' : '' }}>
                                {{ $nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- TAHUN --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        <i class="bi bi-calendar-event mr-1"></i>Tahun
                    </label>
                    <select
                        name="tahun"
                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-700 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"
                    >
                        @for($y = date('Y') - 1; $y <= date('Y') + 1; $y++)
                            <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>

                {{-- KELAS --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        <i class="bi bi-door-closed mr-1"></i>Kelas
                    </label>
                    <select
                        name="id_kelas"
                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-700 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"
                    >
                        <option value="">Semua Kelas</option>
                        @foreach($kelases as $k)
                            <option value="{{ $k->id_kelas }}" {{ ($filterKelas == $k->id_kelas) ? 'selected' : '' }}>
                                {{ $k->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- MAPEL --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        <i class="bi bi-journal-text mr-1"></i>Mapel
                    </label>
                    <select
                        name="id_mapel"
                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-700 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"
                    >
                        <option value="">Semua Mapel</option>
                        @foreach($mapels as $m)
                            <option value="{{ $m->id }}" {{ ($filterMapel == $m->id) ? 'selected' : '' }}>
                                {{ $m->nama_mapel ?? $m->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- TOMBOL FILTER --}}
            <div class="flex items-center gap-2 shrink-0">
                <button
                    type="submit"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-4 py-2 text-xs font-bold text-white shadow-xs transition hover:bg-emerald-700"
                >
                    <i class="bi bi-funnel-fill"></i>
                    <span>Filter</span>
                </button>
                <a
                    href="{{ route('guru.jurnal.rekap') }}"
                    class="inline-flex items-center gap-1 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50"
                    title="Reset Filter"
                >
                    <i class="bi bi-arrow-counterclockwise"></i>
                    <span class="hidden sm:inline">Reset</span>
                </a>
            </div>
        </form>
    </div>

    {{-- STATISTIK RINGKASAN (4 KARTU) --}}
    <div class="mb-8 grid grid-cols-2 gap-4 lg:grid-cols-4">
        {{-- CARD 1: TOTAL TERJADWAL --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-xs">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Sesi Terjadwal</p>
                    <h3 class="mt-2 text-2xl sm:text-3xl font-black text-slate-900">{{ $totalTerjadwal }}</h3>
                </div>
                <div class="flex h-10 w-10 sm:h-11 sm:w-11 items-center justify-center rounded-xl bg-slate-100 text-slate-600 text-lg">
                    <i class="bi bi-calendar-week"></i>
                </div>
            </div>
            <p class="mt-3 text-[11px] text-slate-500">
                Sesi mengajar aktif s.d. hari ini
            </p>
        </div>

        {{-- CARD 2: TOTAL TERISI --}}
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50/50 p-4 sm:p-5 shadow-xs">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-emerald-700">Jurnal Terisi</p>
                    <h3 class="mt-2 text-2xl sm:text-3xl font-black text-emerald-800">{{ $totalTerisi }}</h3>
                </div>
                <div class="flex h-10 w-10 sm:h-11 sm:w-11 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700 text-lg">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
            </div>
            <p class="mt-3 text-[11px] text-emerald-700 font-semibold">
                {{ $totalTerisi }} sesi berhasil dilaporkan
            </p>
        </div>

        {{-- CARD 3: TOTAL KOSONG --}}
        <div class="rounded-2xl border border-rose-200 bg-rose-50/50 p-4 sm:p-5 shadow-xs">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-rose-700">Belum Terisi / Kosong</p>
                    <h3 class="mt-2 text-2xl sm:text-3xl font-black text-rose-800">{{ $totalKosong }}</h3>
                </div>
                <div class="flex h-10 w-10 sm:h-11 sm:w-11 items-center justify-center rounded-xl bg-rose-100 text-rose-700 text-lg">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
            </div>
            <p class="mt-3 text-[11px] text-rose-700 font-semibold">
                {{ $totalKosong }} sesi belum diisi jurnal
            </p>
        </div>

        {{-- CARD 4: PERSENTASE KEPATUHAN --}}
        <div class="rounded-2xl border border-blue-200 bg-blue-50/50 p-4 sm:p-5 shadow-xs">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-blue-700">Kepatuhan Jurnal</p>
                    <h3 class="mt-2 text-2xl sm:text-3xl font-black text-blue-900">{{ $persentaseKepatuhan }}%</h3>
                </div>
                <div class="flex h-10 w-10 sm:h-11 sm:w-11 items-center justify-center rounded-xl bg-blue-100 text-blue-700 text-lg">
                    <i class="bi bi-pie-chart-fill"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="h-2 w-full rounded-full bg-blue-200/60 overflow-hidden">
                    <div
                        class="h-full rounded-full transition-all duration-500 {{ $persentaseKepatuhan >= 80 ? 'bg-emerald-500' : ($persentaseKepatuhan >= 50 ? 'bg-amber-500' : 'bg-rose-500') }}"
                        style="width: {{ min(100, $persentaseKepatuhan) }}%"
                    ></div>
                </div>
            </div>
        </div>
    </div>

    {{-- TABEL BREAKDOWN DETAIL SESI --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-5 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h3 class="text-base font-bold text-slate-900">Rincian Keterisian Jurnal Per Sesi</h3>
                <p class="text-xs text-slate-500">Daftar lengkap sesi mengajar pada bulan terpilih beserta catatan dan bukti kegiatan.</p>
            </div>
            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                Total: {{ count($sessions) }} sesi
            </span>
        </div>

        @if(count($sessions) > 0)
            {{-- DESKTOP TABLE VIEW --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100">
                        <tr>
                            <th class="px-5 py-3.5">Tanggal / Hari</th>
                            <th class="px-4 py-3.5">Jam Ke-</th>
                            <th class="px-4 py-3.5">Kelas &amp; Mapel</th>
                            <th class="px-4 py-3.5">Status</th>
                            <th class="px-4 py-3.5">Materi Pokok</th>
                            <th class="px-4 py-3.5">Foto / Bukti</th>
                            <th class="px-5 py-3.5 text-right">Validasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($sessions as $item)
                            @php
                                $jurnal = $item['jurnal'];
                                $isFilled = $item['status'] === 'Terisi';
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition {{ !$isFilled ? 'bg-rose-50/20' : '' }}">
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="font-bold text-slate-900">{{ \Carbon\Carbon::parse($item['tanggal'])->translatedFormat('d F Y') }}</div>
                                    <div class="text-[11px] text-slate-400 font-medium">{{ $item['hari'] }}</div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1 rounded-md bg-slate-100 px-2 py-1 font-semibold text-slate-700">
                                        <i class="bi bi-clock"></i> Ke-{{ $item['jam_ke'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="font-bold text-slate-800">{{ $item['kelas'] }}</div>
                                    <div class="text-[11px] text-emerald-700 font-medium">{{ $item['mapel'] }}</div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    @if($isFilled)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-0.5 text-[11px] font-bold text-emerald-800">
                                            <i class="bi bi-check-circle-fill"></i> Terisi
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-rose-100 px-2.5 py-0.5 text-[11px] font-bold text-rose-800">
                                            <i class="bi bi-x-circle-fill"></i> Belum Diisi
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 max-w-xs">
                                    @if($jurnal && $jurnal->materi)
                                        <p class="truncate text-slate-700 font-medium" title="{{ $jurnal->materi }}">{{ $jurnal->materi }}</p>
                                    @else
                                        <span class="text-slate-400 italic">- Belum ada materi -</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    @php
                                        $photoPath = $jurnal?->foto ?? $jurnal?->lampiran;
                                    @endphp
                                    @if($photoPath)
                                        <button
                                            type="button"
                                            @click="openPhoto('{{ asset('storage/' . $photoPath) }}')"
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-[11px] font-semibold text-emerald-700 hover:bg-emerald-50"
                                        >
                                            <i class="bi bi-image"></i>
                                            <span>Lihat Foto</span>
                                        </button>
                                    @else
                                        <span class="text-[11px] text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-right whitespace-nowrap">
                                    @if($jurnal)
                                        @if($jurnal->status_validasi === 'disetujui')
                                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700 border border-emerald-200">
                                                <i class="bi bi-check2"></i> Disetujui
                                            </span>
                                        @elseif($jurnal->status_validasi === 'ditolak')
                                            <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-2 py-0.5 text-[10px] font-bold text-rose-700 border border-rose-200">
                                                <i class="bi bi-x"></i> Ditolak
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2 py-0.5 text-[10px] font-bold text-amber-700 border border-amber-200">
                                                <i class="bi bi-hourglass-split"></i> Pending
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-slate-300">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- MOBILE CARD VIEW --}}
            <div class="divide-y divide-slate-100 md:hidden">
                @foreach($sessions as $item)
                    @php
                        $jurnal = $item['jurnal'];
                        $isFilled = $item['status'] === 'Terisi';
                        $photoPath = $jurnal?->foto ?? $jurnal?->lampiran;
                    @endphp
                    <div class="p-4 space-y-2 {{ !$isFilled ? 'bg-rose-50/20' : '' }}">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <span class="text-xs font-bold text-slate-900">
                                    {{ \Carbon\Carbon::parse($item['tanggal'])->translatedFormat('d M Y') }}
                                </span>
                                <span class="text-xs text-slate-400">({{ $item['hari'] }})</span>
                            </div>
                            @if($isFilled)
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-800">
                                    <i class="bi bi-check-circle-fill"></i> Terisi
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-rose-100 px-2 py-0.5 text-[10px] font-bold text-rose-800">
                                    <i class="bi bi-x-circle-fill"></i> Kosong
                                </span>
                            @endif
                        </div>

                        <div class="flex items-center gap-2 text-xs">
                            <span class="rounded bg-slate-100 px-2 py-0.5 font-bold text-slate-700">
                                Jam Ke-{{ $item['jam_ke'] }}
                            </span>
                            <span class="font-bold text-slate-800">{{ $item['kelas'] }}</span>
                            <span class="text-slate-400">•</span>
                            <span class="text-emerald-700 font-semibold">{{ $item['mapel'] }}</span>
                        </div>

                        @if($jurnal && $jurnal->materi)
                            <p class="text-xs text-slate-600 line-clamp-2">
                                <span class="font-semibold text-slate-700">Materi:</span> {{ $jurnal->materi }}
                            </p>
                        @endif

                        <div class="flex items-center justify-between pt-1 border-t border-slate-50 text-xs">
                            <div>
                                @if($photoPath)
                                    <button
                                        type="button"
                                        @click="openPhoto('{{ asset('storage/' . $photoPath) }}')"
                                        class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-700 hover:underline"
                                    >
                                        <i class="bi bi-image"></i> Lihat Foto
                                    </button>
                                @endif
                            </div>
                            <div>
                                @if($jurnal)
                                    @if($jurnal->status_validasi === 'disetujui')
                                        <span class="text-[10px] font-bold text-emerald-600">✓ Disetujui</span>
                                    @elseif($jurnal->status_validasi === 'ditolak')
                                        <span class="text-[10px] font-bold text-rose-600">✕ Ditolak</span>
                                    @else
                                        <span class="text-[10px] font-bold text-amber-600">⌛ Menunggu Validasi</span>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-10 text-center text-sm text-slate-400">
                <i class="bi bi-calendar-x text-3xl mb-2 block"></i>
                Tidak ada data sesi atau jadwal mengajar untuk periode yang dipilih.
            </div>
        @endif
    </div>

    {{-- MODAL PREVIEW FOTO --}}
    <div
        x-cloak
        x-show="photoModalOpen"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-xs"
        @keydown.escape.window="photoModalOpen = false"
    >
        <div
            @click.away="photoModalOpen = false"
            class="relative max-w-lg w-full overflow-hidden rounded-2xl bg-white shadow-2xl p-4"
        >
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h4 class="text-sm font-bold text-slate-800">Bukti Dokumentasi Kegiatan</h4>
                <button
                    type="button"
                    @click="photoModalOpen = false"
                    class="rounded-lg p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-600"
                >
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="mt-4 flex justify-center bg-slate-900 rounded-xl overflow-hidden">
                <img :src="activePhotoUrl" alt="Foto Dokumentasi" class="max-h-96 w-auto object-contain">
            </div>
        </div>
    </div>
</div>
@endsection
