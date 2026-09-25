@extends('layouts.app')

@section('title', 'Riwayat & Rekap Jurnal - JurnalKita')

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
            startDate: '{{ $filterStart ?? '' }}',
            endDate: '{{ $filterEnd ?? '' }}',
            openDetail(data) {
                this.detail = data;
                this.openDetailModal = true;
            },
            onStartDateChange() {
                if (this.startDate) {
                    this.endDate = this.startDate;
                }
            }
        }"
        id="riwayat"
        class="mx-auto w-full max-w-7xl px-4 py-5 sm:px-6 lg:px-8"
    >

        {{-- HEADER RINGKAS --}}
        <section class="flex items-center justify-between">
            <div>
                <h1 class="text-lg font-bold text-slate-900 sm:text-xl">
                    Riwayat &amp; Rekap Jurnal
                </h1>
            </div>
            <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 border border-emerald-200">
                Total {{ $riwayatJurnals->count() }} Data
            </span>
        </section>

        {{-- NOTIFIKASI SUCCESS --}}
        @if(session('success'))
            <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 p-3.5 text-emerald-900 shadow-sm" role="alert">
                <div class="flex items-center gap-2.5">
                    <i class="bi bi-check-circle-fill text-base text-emerald-600"></i>
                    <p class="text-xs sm:text-sm font-semibold">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        {{-- NOTIFIKASI ERROR --}}
        @if(session('error'))
            <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 p-3.5 text-rose-900 shadow-sm" role="alert">
                <div class="flex items-center gap-2.5">
                    <i class="bi bi-exclamation-triangle-fill text-base text-rose-600"></i>
                    <p class="text-xs sm:text-sm font-semibold">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        {{-- FORM PENCARIAN & FILTER (TAMPILAN MOBILE MENYAMPING) --}}
        <section class="mt-4 rounded-2xl bg-white p-3.5 shadow-sm border border-slate-100 sm:p-4">
            <form method="GET" action="{{ route('guru.riwayat') }}" class="flex flex-col gap-2.5 lg:flex-row lg:items-center">

                {{-- Keyword input --}}
                <div class="relative flex-1 min-w-0">
                    <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs" aria-hidden="true"></i>
                    <input
                        type="search"
                        name="keyword"
                        value="{{ $keyword ?? '' }}"
                        placeholder="Cari mapel, kelas, atau materi..."
                        @input="if ($event.target.value.trim() === '' && '{{ $keyword ?? '' }}' !== '') { $el.form.submit(); }"
                        @search="if ($event.target.value.trim() === '') { $el.form.submit(); }"
                        class="w-full rounded-lg border border-slate-200 bg-slate-50/60 py-2 pl-9 pr-3 text-xs text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-emerald-600 focus:bg-white focus:ring-2 focus:ring-emerald-100 sm:text-sm"
                    >
                </div>

                {{-- Filter Tanggal (Menyamping di Mobile) --}}
                <div class="grid grid-cols-2 gap-2 lg:flex lg:items-center">
                    <div class="relative flex items-center">
                        <i class="bi bi-calendar3 pointer-events-none absolute left-3 text-emerald-700 text-xs" aria-hidden="true"></i>
                        <input
                            type="date"
                            name="start_date"
                            x-model="startDate"
                            @change="onStartDateChange()"
                            onclick="this.showPicker()"
                            class="w-full cursor-pointer rounded-lg border border-slate-200 bg-slate-50/60 py-2 pl-8 pr-2 text-xs text-slate-700 outline-none transition focus:border-emerald-600 focus:bg-white focus:ring-2 focus:ring-emerald-100 [&::-webkit-calendar-picker-indicator]:hidden sm:text-sm sm:w-36"
                            placeholder="Mulai"
                        >
                    </div>

                    <div class="relative flex items-center">
                        <i class="bi bi-calendar3 pointer-events-none absolute left-3 text-emerald-700 text-xs" aria-hidden="true"></i>
                        <input
                            type="date"
                            name="end_date"
                            x-model="endDate"
                            onclick="this.showPicker()"
                            class="w-full cursor-pointer rounded-lg border border-slate-200 bg-slate-50/60 py-2 pl-8 pr-2 text-xs text-slate-700 outline-none transition focus:border-emerald-600 focus:bg-white focus:ring-2 focus:ring-emerald-100 [&::-webkit-calendar-picker-indicator]:hidden sm:text-sm sm:w-36"
                            placeholder="Sampai"
                        >
                    </div>
                </div>

                {{-- Tombol Cari --}}
                <button type="submit"
                        class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-emerald-600 px-4 py-2 text-xs font-bold text-white shadow-xs transition hover:bg-emerald-700 sm:text-sm">
                    <i class="bi bi-search"></i>
                    <span>Cari</span>
                </button>
            </form>

            @if($keyword || $filterStart || $filterEnd)
                <div class="mt-2.5 flex items-center gap-2">
                    <a href="{{ route('guru.riwayat') }}" class="text-xs font-medium text-slate-500 hover:text-rose-600">
                        <i class="bi bi-x-circle mr-1"></i>Reset filter
                    </a>
                    @if($keyword)
                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] text-slate-600">"{{ $keyword }}"</span>
                    @endif
                </div>
            @endif
        </section>


        {{-- DAFTAR RIWAYAT LOGBOOK --}}
        <section class="mt-4 space-y-3">
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
                    $jamText = ($jurnal->jam_selesai && $jurnal->jam_selesai > $jurnal->jam_ke)
                        ? "{$jurnal->jam_ke}-{$jurnal->jam_selesai}"
                        : "{$jurnal->jam_ke}";
                @endphp

                <article class="rounded-2xl bg-white p-4 shadow-xs border border-slate-100 transition hover:border-emerald-300 sm:p-5">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-sm font-bold text-slate-900 sm:text-base">
                                    {{ $jurnal->mapel->nama_mapel ?? '-' }}
                                </h3>
                                <span class="rounded-full bg-{{ $statusColor }}-100 px-2.5 py-0.5 text-[10px] font-bold text-{{ $statusColor }}-700">
                                    {{ $statusLabel }}
                                </span>
                            </div>

                            <p class="mt-0.5 text-xs font-semibold text-slate-600">
                                Kelas {{ $jurnal->kelas->nama_kelas ?? '-' }}
                                <span class="mx-1 text-slate-300">&bull;</span>
                                Jam ke-{{ $jamText }}
                            </p>

                            <p class="mt-1.5 text-xs leading-relaxed text-slate-600 line-clamp-2">
                                <span class="font-semibold text-slate-700">Materi:</span> {{ $jurnal->materi }}
                            </p>

                            <div class="mt-2.5 flex flex-wrap items-center gap-1.5 text-[11px]">
                                <span class="rounded-full bg-slate-100 px-2.5 py-0.5 font-semibold text-slate-700">
                                    <i class="bi bi-calendar3 mr-1"></i>{{ $tanggalFormatted }}
                                </span>
                                <span class="rounded-full bg-emerald-50 px-2 py-0.5 font-bold text-emerald-700 border border-emerald-100">
                                    {{ $jurnal->jumlah_hadir ?? 0 }} Hadir
                                </span>
                                @if(($jurnal->jumlah_sakit ?? 0) > 0)
                                    <span class="rounded-full bg-amber-50 px-2 py-0.5 font-bold text-amber-700 border border-amber-100">
                                        {{ $jurnal->jumlah_sakit }} Sakit
                                    </span>
                                @endif
                                @if(($jurnal->jumlah_izin ?? 0) > 0)
                                    <span class="rounded-full bg-blue-50 px-2 py-0.5 font-bold text-blue-700 border border-blue-100">
                                        {{ $jurnal->jumlah_izin }} Izin
                                    </span>
                                @endif
                                @if(($jurnal->jumlah_alpa ?? 0) > 0)
                                    <span class="rounded-full bg-rose-50 px-2 py-0.5 font-bold text-rose-700 border border-rose-100">
                                        {{ $jurnal->jumlah_alpa }} Alpa
                                    </span>
                                @endif
                                @if(($jurnal->jumlah_dispensasi ?? 0) > 0)
                                    <span class="rounded-full bg-indigo-50 px-2 py-0.5 font-bold text-indigo-700 border border-indigo-100">
                                        {{ $jurnal->jumlah_dispensasi }} Disp
                                    </span>
                                @endif
                                @if($jurnal->lampiran)
                                    <span class="rounded-full bg-purple-50 px-2 py-0.5 font-semibold text-purple-700 border border-purple-100">
                                        <i class="bi bi-camera-fill mr-0.5"></i>Foto
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="shrink-0 self-end sm:self-start">
                            @php
                                $tidakHadirList = $jurnal->absensis
                                    ->where('status', '!=', 'Hadir')
                                    ->map(function($a) {
                                        return [
                                            'nama' => $a->siswa->nama ?? 'Siswa',
                                            'nis' => $a->siswa->nis ?? '-',
                                            'status' => $a->status,
                                            'catatan' => $a->catatan ?? '',
                                        ];
                                    })
                                    ->values();
                            @endphp
                            <button
                                type="button"
                                @click="openDetail(@js([
                                    'mapel' => $jurnal->mapel->nama_mapel ?? '-',
                                    'kelas' => $jurnal->kelas->nama_kelas ?? '-',
                                    'tanggal' => $tanggalLong,
                                    'tanggalShort' => $tanggalFormatted,
                                    'jamKe' => $jamText,
                                    'materi' => $jurnal->materi,
                                    'catatan' => $jurnal->catatan ?? '',
                                    'statusValidasi' => $statusLabel,
                                    'statusColor' => $statusColor,
                                    'catatanValidasi' => $jurnal->catatan_validasi ?? '',
                                    'divalidasiPada' => $jurnal->divalidasi_pada ? \Carbon\Carbon::parse($jurnal->divalidasi_pada)->format('d/m/y') : '',
                                    'jumlahHadir' => $jurnal->jumlah_hadir ?? 0,
                                    'jumlahSakit' => $jurnal->jumlah_sakit ?? 0,
                                    'jumlahIzin' => $jurnal->jumlah_izin ?? 0,
                                    'jumlahAlpa' => $jurnal->jumlah_alpa ?? 0,
                                    'jumlahDispensasi' => $jurnal->jumlah_dispensasi ?? 0,
                                    'tidakHadirList' => $tidakHadirList,
                                    'adaTugas' => $jurnal->ada_tugas ? 'Ya' : 'Tidak',
                                    'lampiran' => $jurnal->lampiran ? asset('storage/' . $jurnal->lampiran) : '',
                                ]))"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-200 bg-white px-3 py-1.5 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-50"
                            >
                                <i class="bi bi-eye"></i>
                                <span>Detail</span>
                            </button>
                        </div>
                    </div>
                </article>
            @empty
                <div class="flex flex-col items-center justify-center py-12 text-center rounded-2xl bg-white border border-slate-100">
                    <span class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-xl text-slate-400">
                        <i class="bi bi-journal-x"></i>
                    </span>
                    <p class="mt-3 text-xs sm:text-sm font-semibold text-slate-600">Belum ada riwayat logbook</p>
                </div>
            @endforelse
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
                class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-xs"
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
                    class="flex w-full max-w-xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl"
                    style="max-height: 88vh;"
                >
                    {{-- Header Modal --}}
                    <div class="flex shrink-0 items-center justify-between border-b border-slate-200 px-4 py-3.5">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 sm:text-base">Detail Logbook Mengajar</h3>
                            <p class="text-[11px] text-slate-400" x-text="detail.tanggal"></p>
                        </div>
                        <button type="button" @click="openDetailModal = false"
                                class="flex h-7 w-7 items-center justify-center rounded-full text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                                aria-label="Tutup">
                            <i class="bi bi-x-lg text-sm"></i>
                        </button>
                    </div>

                    {{-- Body Modal Scrollable --}}
                    <div class="flex-1 overflow-y-auto p-4 custom-scrollbar min-h-0 space-y-3.5">
                        {{-- Status & Tanggal --}}
                        <div class="flex items-center justify-between gap-3">
                            <span
                                class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                :class="{
                                    'bg-emerald-100 text-emerald-700': detail.statusColor === 'emerald',
                                    'bg-amber-100 text-amber-700': detail.statusColor === 'amber',
                                    'bg-rose-100 text-rose-700': detail.statusColor === 'rose'
                                }"
                                x-text="detail.statusValidasi"
                            ></span>
                            <span class="text-xs font-bold text-slate-500">
                                Jam ke-<span x-text="detail.jamKe"></span>
                            </span>
                        </div>

                        {{-- Info Grid --}}
                        <div class="grid grid-cols-2 gap-2.5">
                            <div class="rounded-xl bg-slate-50 p-2.5 border border-slate-100">
                                <p class="text-[10px] font-bold uppercase text-slate-400">Mata Pelajaran</p>
                                <p class="mt-0.5 text-xs font-bold text-slate-800" x-text="detail.mapel"></p>
                            </div>
                            <div class="rounded-xl bg-slate-50 p-2.5 border border-slate-100">
                                <p class="text-[10px] font-bold uppercase text-slate-400">Kelas</p>
                                <p class="mt-0.5 text-xs font-bold text-slate-800" x-text="detail.kelas"></p>
                            </div>
                        </div>

                        {{-- Materi --}}
                        <div>
                            <p class="text-xs font-bold text-slate-700">Materi / Pokok Pembahasan</p>
                            <div class="mt-1 rounded-xl bg-slate-50 p-3 border border-slate-100">
                                <p class="text-xs leading-relaxed text-slate-700 whitespace-pre-line" x-text="detail.materi"></p>
                            </div>
                        </div>

                        {{-- Catatan Khusus / Hambatan Pembelajaran --}}
                        <div>
                            <p class="text-xs font-bold text-slate-700">Catatan Khusus / Hambatan</p>
                            <div class="mt-1 rounded-xl p-3 border" :class="detail.catatan ? 'border-amber-200 bg-amber-50/80 text-amber-900' : 'border-slate-100 bg-slate-50 text-slate-400'">
                                <p class="text-xs leading-relaxed whitespace-pre-line" x-text="detail.catatan ? detail.catatan : 'Tidak ada catatan khusus / kendala.'"></p>
                            </div>
                        </div>

                        {{-- Tugas --}}
                        <div class="flex items-center gap-2 rounded-xl border border-slate-100 bg-slate-50 px-3 py-2 text-xs text-slate-600">
                            <i class="bi bi-clipboard-check text-slate-500"></i>
                            <span>Status Tugas:</span>
                            <strong class="text-slate-800" x-text="detail.adaTugas === 'Ya' ? 'Ada Tugas Diberikan' : 'Tidak Ada Tugas'"></strong>
                        </div>

                        {{-- Rekap Kehadiran Siswa --}}
                        <div class="rounded-xl border border-emerald-100 bg-emerald-50/60 p-3.5 space-y-3">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wider text-emerald-800">Rekap Presensi Siswa</p>
                                <div class="mt-2 flex flex-wrap gap-1.5 text-xs font-bold">
                                    <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-emerald-800">
                                        <span x-text="detail.jumlahHadir"></span> Hadir
                                    </span>
                                    <template x-if="parseInt(detail.jumlahSakit) > 0">
                                        <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-amber-800">
                                            <span x-text="detail.jumlahSakit"></span> Sakit
                                        </span>
                                    </template>
                                    <template x-if="parseInt(detail.jumlahIzin) > 0">
                                        <span class="rounded-full bg-blue-100 px-2.5 py-0.5 text-blue-800">
                                            <span x-text="detail.jumlahIzin"></span> Izin
                                        </span>
                                    </template>
                                    <template x-if="parseInt(detail.jumlahAlpa) > 0">
                                        <span class="rounded-full bg-rose-100 px-2.5 py-0.5 text-rose-800">
                                            <span x-text="detail.jumlahAlpa"></span> Alpa
                                        </span>
                                    </template>
                                    <template x-if="parseInt(detail.jumlahDispensasi) > 0">
                                        <span class="rounded-full bg-indigo-100 px-2.5 py-0.5 text-indigo-800">
                                            <span x-text="detail.jumlahDispensasi"></span> Dispensasi
                                        </span>
                                    </template>
                                </div>
                            </div>

                            {{-- DAFTAR SISWA TIDAK MASUK (S, I, A, D) --}}
                            <div class="border-t border-emerald-200/60 pt-2.5">
                                <p class="text-[11px] font-bold text-slate-700 mb-1.5 flex items-center gap-1">
                                    <i class="bi bi-person-x-fill text-slate-500"></i>
                                    <span>Siswa tidak hadir:</span>
                                </p>

                                <template x-if="detail.tidakHadirList && detail.tidakHadirList.length > 0">
                                    <div class="space-y-1.5 max-h-40 overflow-y-auto custom-scrollbar pr-1">
                                        <template x-for="(siswa, idx) in detail.tidakHadirList" :key="idx">
                                            <div class="flex items-center justify-between rounded-lg bg-white border border-slate-200/80 px-2.5 py-1.5 text-xs shadow-2xs">
                                                <div class="min-w-0 flex-1 pr-2">
                                                    <span class="font-bold text-slate-800" x-text="siswa.nama"></span><br>
                                                    <span class="text-[10px] text-slate-400 ml-1" x-text="'(NIS: ' + (siswa.nis || '-') + ')'"></span>
                                                    <template x-if="siswa.catatan">
                                                        <p class="text-[10px] text-slate-500 italic" x-text="'Ket: ' + siswa.catatan"></p>
                                                    </template>
                                                </div>
                                                <span
                                                    class="shrink-0 rounded-md px-2 py-0.5 text-[10px] font-bold"
                                                    :class="{
                                                        'bg-amber-100 text-amber-800 border border-amber-200': siswa.status === 'Sakit',
                                                        'bg-blue-100 text-blue-800 border border-blue-200': siswa.status === 'Izin',
                                                        'bg-rose-100 text-rose-800 border border-rose-200': siswa.status === 'Alpa',
                                                        'bg-indigo-100 text-indigo-800 border border-indigo-200': siswa.status === 'Dispensasi' || siswa.status === 'D',
                                                    }"
                                                    x-text="siswa.status === 'Sakit' ? 'Sakit (S)' : (siswa.status === 'Izin' ? 'Izin (I)' : (siswa.status === 'Alpa' ? 'Alpa (A)' : (siswa.status === 'Dispensasi' || siswa.status === 'D' ? 'Dispensasi (D)' : siswa.status)))"
                                                ></span>
                                            </div>
                                        </template>
                                    </div>
                                </template>

                                <template x-if="!detail.tidakHadirList || detail.tidakHadirList.length === 0">
                                    <p class="text-[11px] text-emerald-700 font-medium flex items-center gap-1 bg-white/70 rounded-lg p-2 border border-emerald-100">
                                        <i class="bi bi-check-circle-fill text-emerald-600"></i>
                                        <span>Semua siswa hadir di kelas (Nihil).</span>
                                    </p>
                                </template>
                            </div>
                        </div>

                        {{-- Validasi Info --}}
                        <template x-if="detail.statusColor === 'emerald' && detail.divalidasiPada">
                            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2.5 text-xs text-emerald-800">
                                <i class="bi bi-shield-check mr-1 text-emerald-600 font-bold"></i>
                                Divalidasi: <span class="font-bold" x-text="detail.divalidasiPada"></span>
                                <template x-if="detail.catatanValidasi">
                                    <p class="mt-0.5 text-emerald-700">Catatan: <span x-text="detail.catatanValidasi"></span></p>
                                </template>
                            </div>
                        </template>

                        <template x-if="detail.statusColor === 'rose' && detail.catatanValidasi">
                            <div class="rounded-xl border border-rose-200 bg-rose-50 px-3 py-2.5 text-xs text-rose-800">
                                <i class="bi bi-exclamation-triangle mr-1 text-rose-600 font-bold"></i>
                                Alasan Ditolak: <span class="font-bold" x-text="detail.catatanValidasi"></span>
                            </div>
                        </template>

                        {{-- Foto Live Bukti Mengajar di Kelas --}}
                        <div>
                            <p class="text-xs font-bold text-slate-700">Foto Live Bukti Kehadiran di Kelas</p>
                            <template x-if="detail.lampiran">
                                <div class="mt-1.5 overflow-hidden rounded-xl border border-slate-200 bg-slate-100">
                                    <a :href="detail.lampiran" target="_blank" title="Buka foto ukuran penuh" class="group block relative">
                                        <img :src="detail.lampiran" alt="Foto Live Kehadiran Guru di Kelas" class="max-h-80 w-full object-contain rounded-xl transition duration-200 group-hover:opacity-95">
                                        <div class="absolute bottom-2 right-2 rounded-lg bg-black/60 px-2.5 py-1 text-[10px] font-medium text-white backdrop-blur-xs flex items-center gap-1">
                                            <i class="bi bi-arrows-fullscreen"></i> Buka Ukuran Penuh
                                        </div>
                                    </a>
                                </div>
                            </template>
                            <template x-if="!detail.lampiran">
                                <div class="mt-1.5 flex h-20 flex-col items-center justify-center rounded-xl border border-dashed border-slate-200 bg-slate-50 text-slate-400">
                                    <i class="bi bi-camera-video-off text-base"></i>
                                    <span class="mt-0.5 text-xs">Tidak ada foto live bukti kehadiran</span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </template>

    </div>

@endsection