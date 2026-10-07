@extends('layouts.app')

@section('sidebar')
    @include('layouts.pengurus-kelas.sidebar', ['activePage' => 'kehadiran-siswa'])
@endsection

@section('navbar')
    @include('layouts.pengurus-kelas.navbar', ['activePage' => 'kehadiran-siswa'])
@endsection

@section('content')
<div
    x-data="{
        searchQuery: '',
        modalSuratOpen: false,
        suratData: {
            nama: '',
            nisn: '',
            kelas: '',
            tanggal: '',
            alasan: '',
            waktu: '',
            pencatat: ''
        },
        openSuratModal(data) {
            this.suratData = data;
            this.modalSuratOpen = true;
        },
        matches(nama, nis, no) {
            if (!this.searchQuery.trim()) return true;
            const q = this.searchQuery.toLowerCase();
            return nama.toLowerCase().includes(q) || nis.toLowerCase().includes(q) || String(no).includes(q);
        }
    }"
    class="mx-auto w-full max-w-5xl px-4 py-6 pb-24 sm:px-6 lg:px-8"
>

    <header class="mb-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <div>
            <p class="text-xs font-bold text-emerald-700 uppercase tracking-wider">{{ $tanggalFormatted }} <span class="text-xs font-semibold text-slate-500">({{ $siswas->count() }} Siswa)</span></p>
        </div>

        {{-- Search Input --}}
        <div class="relative w-full sm:w-64">
            <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input
                type="text"
                x-model="searchQuery"
                placeholder="Cari nama atau NISN siswa..."
                class="w-full rounded-xl border border-slate-200 bg-white py-2 pl-8 pr-3 text-xs text-slate-700 placeholder:text-slate-400 focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-100"
            >
        </div>
    </header>

    {{-- Pemberitahuan Siswa Terlambat Hari Ini (Izin Masuk Piket) --}}
    @if(isset($siswaTerlambatHariIni) && $siswaTerlambatHariIni->isNotEmpty())
        <div class="mb-4 rounded-2xl border border-amber-300 bg-amber-50/90 p-4 shadow-xs" role="alert">
            <div class="flex items-start gap-3">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-amber-500 text-white shadow-xs">
                    <i class="bi bi-clock-history text-lg"></i>
                </span>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-2 flex-wrap">
                        <h3 class="text-sm font-bold text-amber-950">
                            Pemberitahuan Siswa Terlambat Hari Ini (Izin Masuk Piket)
                        </h3>
                        <span class="rounded-full bg-amber-200/90 px-2.5 py-0.5 text-[11px] font-bold text-amber-900">
                            {{ $siswaTerlambatHariIni->count() }} Siswa
                        </span>
                    </div>
                    <p class="text-xs text-amber-800 mt-0.5">
                        Guru piket telah menginput siswa berikut yang terlambat dan telah memberikan izin masuk kelas setelah pembinaan/sanksi. <strong>Harap sampaikan alasan keterlambatan ini kepada Guru Pengajar di kelas:</strong>
                    </p>
                    <div class="mt-3 grid gap-2.5 sm:grid-cols-2">
                        @foreach($siswaTerlambatHariIni as $tItem)
                            @php
                                $tSiswa = $tItem->siswa;
                            @endphp
                            <div class="flex items-start justify-between gap-2.5 rounded-xl border border-amber-200 bg-white p-3 shadow-2xs">
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-bold text-slate-800">{{ $tSiswa?->nama ?? 'Siswa' }}</p>
                                    <p class="text-[11px] text-amber-900 font-semibold mt-0.5">
                                        <i class="bi bi-chat-left-text text-amber-600 mr-1"></i>Alasan: <span class="italic text-slate-700 font-medium">{{ $tItem->catatan ?: 'Terlambat masuk sekolah' }}</span>
                                    </p>
                                    <p class="text-[10px] text-slate-400 mt-1">
                                        Dicatat: {{ $tItem->created_at ? $tItem->created_at->timezone('Asia/Jakarta')->format('H:i') . ' WIB' : '-' }} &bull; {{ $tItem->pencatat?->name ?? 'Guru Piket' }}
                                    </p>
                                </div>
                                <button
                                    type="button"
                                    @click="openSuratModal({{ json_encode([
                                        'nama' => $tSiswa?->nama ?? 'Siswa',
                                        'nisn' => $tSiswa?->nis ?? $tSiswa?->nisn ?? '-',
                                        'kelas' => $kelas->nama_kelas ?? 'Kelas',
                                        'tanggal' => $tanggalFormatted,
                                        'alasan' => $tItem->catatan ?: 'Terlambat masuk sekolah',
                                        'waktu' => $tItem->created_at ? $tItem->created_at->timezone('Asia/Jakarta')->format('H:i') . ' WIB' : 'Hari ini',
                                        'pencatat' => $tItem->pencatat?->name ?? 'Guru Piket',
                                    ]) }})"
                                    class="shrink-0 inline-flex items-center gap-1 rounded-lg bg-amber-50 border border-amber-300 px-2.5 py-1 text-[11px] font-bold text-amber-800 hover:bg-amber-100 transition cursor-pointer"
                                >
                                    <i class="bi bi-file-earmark-text"></i> Surat
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(isset($jurnalPertama) && $jurnalPertama)
        <div class="mb-4 flex items-center justify-between flex-wrap gap-2 rounded-xl border border-emerald-200 bg-emerald-50/70 px-4 py-2.5 text-xs">
            <div class="flex items-center gap-2 text-emerald-900">
                <i class="bi bi-info-circle-fill text-emerald-600 text-sm"></i>
                <span>
                    Absensi terupdate dari <strong>seluruh jurnal hari ini ({{ $totalJurnalHariIni ?? 1 }} sesi terisi)</strong>.
                    Pembaruan terakhir: <span class="font-bold text-emerald-800">{{ $jurnalPertama->user->name ?? 'Guru' }}</span>
                    &bull; {{ $jurnalPertama->mapel->nama_mapel ?? 'Mapel' }}
                    (Jam ke-{{ $jurnalPertama->jam_ke }}{{ $jurnalPertama->jam_selesai && $jurnalPertama->jam_selesai > $jurnalPertama->jam_ke ? " s/d {$jurnalPertama->jam_selesai}" : '' }})
                </span>
            </div>
            <span class="inline-flex items-center gap-1 rounded-md bg-white border border-emerald-200 px-2 py-0.5 text-[11px] font-semibold text-emerald-700">
                <i class="bi bi-check2-circle"></i> Basis Absensi Terkini
            </span>
        </div>
    @else
        <div class="mb-4 flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs text-slate-600">
            <i class="bi bi-clock-history text-slate-400 text-sm"></i>
            <span>Belum ada guru yang mengisi jurnal mengajar hari ini. Status siswa saat ini default hadir (kecuali yang memiliki dispensasi disetujui).</span>
        </div>
    @endif

    <div class="mb-4 flex flex-wrap items-center gap-x-4 gap-y-2 rounded-xl border border-emerald-100 bg-emerald-50/60 px-4 py-2.5 text-xs text-slate-700">
        <span class="font-bold text-emerald-800">Status Kehadiran:</span>
        <span class="inline-flex items-center gap-1 font-semibold text-emerald-700"><span class="flex h-5 w-5 items-center justify-center rounded-full bg-emerald-100 text-[10px] font-bold">H</span> Hadir</span>
        <span class="inline-flex items-center gap-1 font-semibold text-amber-700"><span class="flex h-5 w-5 items-center justify-center rounded-full bg-amber-100 text-[10px] font-bold">S</span> Sakit</span>
        <span class="inline-flex items-center gap-1 font-semibold text-blue-700"><span class="flex h-5 w-5 items-center justify-center rounded-full bg-blue-100 text-[10px] font-bold">I</span> Izin</span>
        <span class="inline-flex items-center gap-1 font-semibold text-indigo-700"><span class="flex h-5 w-5 items-center justify-center rounded-full bg-indigo-100 text-[10px] font-bold">D</span> Dispensasi</span>
        <span class="inline-flex items-center gap-1 font-semibold text-rose-700"><span class="flex h-5 w-5 items-center justify-center rounded-full bg-rose-100 text-[10px] font-bold">A</span> Alpa</span>
        <span class="inline-flex items-center gap-1 font-semibold text-amber-800"><span class="flex h-5 w-5 items-center justify-center rounded-full bg-amber-200 text-[10px] font-bold">T</span> Terlambat</span>
    </div>

    @if($siswas->isEmpty())
        <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-2xl text-slate-400">
                <i class="bi bi-people"></i>
            </div>
            <h3 class="mt-3 text-sm font-bold text-slate-700">Belum Ada Data Siswa</h3>
            <p class="mt-1 text-xs text-slate-400">Data siswa untuk kelas ini belum terdaftar di database.</p>
        </div>
    @else
        <ul class="divide-y divide-slate-100 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xs" aria-label="Daftar absensi siswa">
            @foreach($siswas as $index => $siswa)
                @php
                    $noAbsen = $index + 1;
                    $noAbsenStr = sprintf('%02d', $noAbsen);
                    $rawRecord = $absensiTerakhir->get($siswa->id);
                    $rawStatus = is_object($rawRecord) ? $rawRecord->status : (is_string($rawRecord) ? $rawRecord : 'Hadir');
                    $status = match(strtoupper(trim((string) $rawStatus))) {
                        'S', 'SAKIT' => 'Sakit',
                        'I', 'IZIN' => 'Izin',
                        'A', 'ALPA', 'ALFA' => 'Alpa',
                        'D', 'DISPENSASI' => 'Dispensasi',
                        default => 'Hadir',
                    };
                    $catatanAbsen = is_object($rawRecord) ? $rawRecord->catatan : null;

                    $isTerlambat = false;
                    $catatanPiket = $piketKehadiranHariIni->get($siswa->id);
                    if ($catatanPiket) {
                        $piketStatus = strtoupper(trim((string) $catatanPiket->status));
                        if (in_array($piketStatus, ['TERLAMBAT', 'TELAT', 'T'], true)) {
                            $status = 'Terlambat';
                            $isTerlambat = true;
                            $catatanAbsen = $catatanPiket->catatan ?: 'Terlambat masuk sekolah';
                        } else {
                            $status = match($piketStatus) {
                                'S', 'SAKIT' => 'Sakit',
                                'I', 'IZIN' => 'Izin',
                                'A', 'ALPA', 'ALFA' => 'Alpa',
                                'D', 'DISPENSASI' => 'Dispensasi',
                                default => 'Hadir',
                            };
                            $catatanAbsen = 'Otomatis dari Guru Piket'.($catatanPiket->catatan ? ': '.$catatanPiket->catatan : '.');
                        }
                    }

                    // Override: jika ada dispensasi aktif & disetujui hari ini → paksa status Dispensasi
                    $dispensasiSiswa = $dispensasiAktifHariIni->get($siswa->id);
                    if ($dispensasiSiswa) {
                        $status = 'Dispensasi';
                        $isTerlambat = false;
                        $jamKet = '';
                        if ($dispensasiSiswa->jam_ke_mulai) {
                            $jamKet = $dispensasiSiswa->jam_ke_selesai && $dispensasiSiswa->jam_ke_selesai > $dispensasiSiswa->jam_ke_mulai
                                ? " (Jam ke-{$dispensasiSiswa->jam_ke_mulai} s/d {$dispensasiSiswa->jam_ke_selesai})"
                                : " (Mulai Jam ke-{$dispensasiSiswa->jam_ke_mulai})";
                        }
                        $catatanAbsen = 'Dispensasi disetujui: ' . $dispensasiSiswa->alasan . $jamKet;
                    }
                @endphp
                <li
                    x-show="matches('{{ addslashes($siswa->nama) }}', '{{ $siswa->nis }}', {{ $noAbsen }})"
                    class="flex items-center justify-between gap-3 px-4 py-3 sm:px-5 hover:bg-slate-50/50 transition"
                >
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-xs font-bold text-emerald-700">
                            {{ $noAbsenStr }}
                        </span>
                        <div class="min-w-0">
                            <p class="truncate text-xs sm:text-sm font-bold text-slate-800">{{ $siswa->nama }}</p>
                            <p class="text-[11px] text-slate-400">NISN: {{ $siswa->nis }} · L/P: {{ $siswa->jenis_kelamin }}</p>
                            @if($isTerlambat)
                                <div class="mt-1 flex flex-wrap items-center gap-1.5">
                                    <span class="inline-flex items-center gap-1 rounded-md bg-amber-100 border border-amber-300 px-2 py-0.5 text-[11px] font-bold text-amber-800">
                                        <i class="bi bi-clock-history"></i> Terlambat &bull; Izin Masuk Piket
                                    </span>
                                    <button
                                        type="button"
                                        @click="openSuratModal({{ json_encode([
                                            'nama' => $siswa->nama,
                                            'nisn' => $siswa->nis ?? $siswa->nisn ?? '-',
                                            'kelas' => $kelas->nama_kelas ?? 'Kelas',
                                            'tanggal' => $tanggalFormatted,
                                            'alasan' => $catatanAbsen ?: 'Terlambat masuk sekolah',
                                            'waktu' => $catatanPiket?->created_at ? $catatanPiket->created_at->timezone('Asia/Jakarta')->format('H:i') . ' WIB' : 'Hari ini',
                                            'pencatat' => $catatanPiket?->pencatat?->name ?? 'Guru Piket',
                                        ]) }})"
                                        class="inline-flex items-center gap-1 rounded-md bg-white border border-amber-300 px-2 py-0.5 text-[11px] font-bold text-amber-700 hover:bg-amber-50 transition cursor-pointer"
                                    >
                                        <i class="bi bi-file-earmark-text"></i> Surat Izin
                                    </button>
                                </div>
                                <p class="text-[11px] text-amber-800 font-medium mt-1">
                                    <i class="bi bi-chat-left-text-fill mr-1 text-amber-600"></i><strong>Alasan Siswa:</strong> {{ $catatanAbsen }}
                                </p>
                            @elseif($catatanAbsen)
                                <p class="text-[11px] {{ $status === 'Dispensasi' ? 'text-indigo-600 font-medium' : 'text-slate-500 italic' }} mt-0.5">
                                    @if($status === 'Dispensasi')
                                        <i class="bi bi-file-earmark-check mr-0.5"></i>
                                    @endif
                                    Ket: {{ $catatanAbsen }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <fieldset class="flex shrink-0 items-center gap-1" aria-label="Status absensi {{ $siswa->nama }}">
                        @foreach([
                            'Hadir' => ['label' => 'H', 'cls' => 'peer-checked:border-emerald-600 peer-checked:bg-emerald-600 peer-checked:text-white'],
                            'Sakit' => ['label' => 'S', 'cls' => 'peer-checked:border-amber-500 peer-checked:bg-amber-500 peer-checked:text-white'],
                            'Izin' => ['label' => 'I', 'cls' => 'peer-checked:border-blue-500 peer-checked:bg-blue-500 peer-checked:text-white'],
                            'Alpa' => ['label' => 'A', 'cls' => 'peer-checked:border-rose-500 peer-checked:bg-rose-500 peer-checked:text-white'],
                            'Dispensasi' => ['label' => 'D', 'cls' => 'peer-checked:border-indigo-600 peer-checked:bg-indigo-600 peer-checked:text-white'],
                            'Terlambat' => ['label' => 'T', 'cls' => 'peer-checked:border-amber-600 peer-checked:bg-amber-600 peer-checked:text-white']
                        ] as $option => $cfg)
                            <label class="cursor-not-allowed">
                                <input type="radio" name="status_{{ $siswa->id }}" value="{{ $option }}" @checked($status === $option) disabled class="peer sr-only">
                                <span class="flex h-7 w-7 items-center justify-center rounded-lg border border-slate-200 bg-white text-[11px] font-bold text-slate-400 transition {{ $cfg['cls'] }} peer-checked:shadow-sm">{{ $cfg['label'] }}</span>
                            </label>
                        @endforeach
                    </fieldset>
                </li>
            @endforeach
        </ul>
    @endif

    {{-- MODAL SURAT IZIN MASUK KELAS DIGITAL --}}
    <div
        x-show="modalSuratOpen"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
        @keydown.escape.window="modalSuratOpen = false"
    >
        <div
            @click.outside="modalSuratOpen = false"
            x-show="modalSuratOpen"
            x-transition
            class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl border border-slate-200"
        >
            {{-- Header Surat --}}
            <div class="bg-gradient-to-r from-amber-600 to-amber-700 px-6 py-4 text-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/20 text-white font-bold">
                            <i class="bi bi-file-earmark-check-fill text-lg"></i>
                        </span>
                        <div>
                            <h3 class="font-bold text-sm sm:text-base leading-tight">SURAT IZIN MASUK KELAS</h3>
                            <p class="text-[11px] text-amber-100">Layanan Ketertiban &amp; Guru Piket</p>
                        </div>
                    </div>
                    <button type="button" @click="modalSuratOpen = false" class="text-white/80 hover:text-white cursor-pointer p-1">
                        <i class="bi bi-x-lg text-lg"></i>
                    </button>
                </div>
            </div>

            <div class="p-6 text-slate-700 space-y-4 text-xs">
                <div class="rounded-xl border border-amber-200 bg-amber-50/70 p-3 flex items-start gap-2.5 text-amber-900">
                    <i class="bi bi-shield-check text-base text-amber-600 mt-0.5"></i>
                    <div>
                        <p class="font-bold">Izin Masuk Resmi Diterbitkan Guru Piket</p>
                        <p class="text-[11px] text-amber-800 mt-0.5">Siswa telah melapor ke piket, diberikan pembinaan/sanksi atas keterlambatan, dan telah diizinkan untuk mengikuti Kegiatan Belajar Mengajar (KBM).</p>
                    </div>
                </div>

                <div class="divide-y divide-slate-100 rounded-xl border border-slate-200 overflow-hidden">
                    <div class="flex justify-between px-3.5 py-2.5 bg-slate-50/60">
                        <span class="text-slate-500 font-medium">Nama Siswa</span>
                        <span class="font-bold text-slate-800" x-text="suratData.nama"></span>
                    </div>
                    <div class="flex justify-between px-3.5 py-2.5 bg-white">
                        <span class="text-slate-500 font-medium">Kelas / NISN</span>
                        <span class="font-semibold text-slate-800" x-text="suratData.kelas + ' · ' + suratData.nisn"></span>
                    </div>
                    <div class="flex justify-between px-3.5 py-2.5 bg-slate-50/60">
                        <span class="text-slate-500 font-medium">Hari / Tanggal</span>
                        <span class="font-semibold text-slate-800" x-text="suratData.tanggal"></span>
                    </div>
                    <div class="flex justify-between px-3.5 py-2.5 bg-white">
                        <span class="text-slate-500 font-medium">Waktu Melapor</span>
                        <span class="font-semibold text-slate-800" x-text="suratData.waktu"></span>
                    </div>
                    <div class="px-3.5 py-3 bg-amber-50/40">
                        <span class="text-slate-500 font-medium block">Alasan Keterlambatan:</span>
                        <p class="mt-1 font-bold text-amber-950 text-xs sm:text-sm bg-white p-2.5 rounded-lg border border-amber-200" x-text="suratData.alasan"></p>
                    </div>
                    <div class="flex justify-between px-3.5 py-2.5 bg-white">
                        <span class="text-slate-500 font-medium">Guru Piket</span>
                        <span class="font-bold text-slate-800" x-text="suratData.pencatat"></span>
                    </div>
                </div>

                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 text-[11px] text-slate-500 italic">
                    * Pengurus kelas dapat menunjukkan bukti surat izin digital ini kepada Guru Pengajar yang sedang mengajar di kelas sebagai konfirmasi alasan keterlambatan.
                </div>
            </div>

            <div class="border-t border-slate-100 bg-slate-50 px-6 py-3 flex justify-end">
                <button
                    type="button"
                    @click="modalSuratOpen = false"
                    class="rounded-xl bg-slate-800 px-4 py-2 text-xs font-bold text-white hover:bg-slate-900 transition cursor-pointer"
                >
                    Tutup
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

