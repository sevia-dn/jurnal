@extends('layouts.app')

@section('title', 'Jurnal Mengajar Guru - JurnalKita')

@section('sidebar')
    @include('layouts.guru-pengajar.sidebar', ['activePage' => 'utama'])
@endsection

@section('navbar')
    @include('layouts.guru-pengajar.navbar', ['activePage' => 'utama'])
@endsection

@section('content')

<style>
    [x-cloak] {
        display: none !important;
    }
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: rgba(16, 185, 129, 0.05); }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(16, 185, 129, 0.3); border-radius: 9999px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(16, 185, 129, 0.5); }
</style>

<div
    x-data="{
        selectedKelas: '',
        selectedMapel: '',
        selectedJamKe: '',
        selectedJamSelesai: '',
        searchSiswa: '',
        searchKelas: '',
        searchMapel: '',
        openKelasDropdown: false,
        openMapelDropdown: false,
        liveClock: '{{ $currentFullTime ?? \Carbon\Carbon::now("Asia/Jakarta")->format("H:i:s") }}',
        cameraActive: false,
        cameraStream: null,
        capturedPhoto: null,

        mapelMap: {
            @foreach($mapels as $mapel)
                '{{ $mapel->id }}': '{{ addslashes($mapel->nama_mapel ?? $mapel->nama ?? $mapel->kode) }}',
            @endforeach
        },

        kelasMap: {
            @foreach($kelases as $kelas)
                '{{ $kelas->id_kelas }}': '{{ addslashes($kelas->nama_kelas) }}',
            @endforeach
        },

        getMapelName(id) {
            return this.mapelMap[id] || '';
        },

        getKelasName(id) {
            return this.kelasMap[id] || '';
        },

        init() {
            setInterval(() => {
                const d = new Date();
                const h = String(d.getHours()).padStart(2, '0');
                const m = String(d.getMinutes()).padStart(2, '0');
                const s = String(d.getSeconds()).padStart(2, '0');
                this.liveClock = `${h}:${m}:${s}`;
            }, 1000);
        },

        pilihJadwal(idKelas, idMapel, jamMulai, jamSelesai) {
            this.selectedKelas = String(idKelas);
            this.selectedMapel = String(idMapel);
            this.selectedJamKe = String(jamMulai);
            this.selectedJamSelesai = String(jamSelesai || jamMulai);
            this.searchSiswa = '';
            const logbookEl = document.getElementById('form-logbook-section');
            if (logbookEl) {
                logbookEl.scrollIntoView({ behavior: 'smooth' });
            }
        },

        matchesSearch(nama, nis) {
            if (!this.searchSiswa || this.searchSiswa.trim() === '') return true;
            const query = this.searchSiswa.toLowerCase().trim();
            return String(nama || '').toLowerCase().includes(query) || (nis && String(nis).toLowerCase().includes(query));
        },

        async startCamera() {
            try {
                this.cameraStream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: { ideal: 'environment' } },
                    audio: false
                });
                this.$nextTick(() => {
                    const vid = this.$refs.cameraVideoLogbook;
                    if (vid) { vid.srcObject = this.cameraStream; vid.play(); }
                });
                this.cameraActive = true;
                this.capturedPhoto = null;
            } catch(e) {
                try {
                    this.cameraStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
                    this.$nextTick(() => {
                        const vid = this.$refs.cameraVideoLogbook;
                        if (vid) { vid.srcObject = this.cameraStream; vid.play(); }
                    });
                    this.cameraActive = true;
                    this.capturedPhoto = null;
                } catch(err) {
                    alert('Kamera tidak dapat diakses. Pastikan izin kamera telah diberikan pada browser Anda.');
                }
            }
        },

        capturePhoto() {
            const vid = this.$refs.cameraVideoLogbook;
            const canvas = this.$refs.cameraCanvasLogbook;
            if (!vid || !canvas) return;
            canvas.width = vid.videoWidth;
            canvas.height = vid.videoHeight;
            canvas.getContext('2d').drawImage(vid, 0, 0);
            this.capturedPhoto = canvas.toDataURL('image/jpeg', 0.85);
            this.$refs.lampiranInput.value = '';
            fetch(this.capturedPhoto)
                .then(r => r.blob())
                .then(blob => {
                    const file = new File([blob], 'foto-bukti-mengajar-live.jpg', { type: 'image/jpeg' });
                    const dt = new DataTransfer();
                    dt.items.add(file);
                    this.$refs.lampiranInput.files = dt.files;
                });
            this.stopCamera();
        },

        retakePhoto() {
            this.capturedPhoto = null;
            this.startCamera();
        },

        stopCamera() {
            if (this.cameraStream) {
                this.cameraStream.getTracks().forEach(t => t.stop());
                this.cameraStream = null;
            }
            this.cameraActive = false;
        },

        submitForm(e) {
            if (!this.selectedKelas) {
                e.preventDefault();
                alert('Silakan pilih Kelas terlebih dahulu!');
                return false;
            }
            if (!this.selectedMapel) {
                e.preventDefault();
                alert('Silakan pilih Mata Pelajaran terlebih dahulu!');
                return false;
            }
            if (!this.capturedPhoto || !this.$refs.lampiranInput.files || this.$refs.lampiranInput.files.length === 0) {
                e.preventDefault();
                alert('Wajib mengambil foto live bukti kehadiran di kelas sebelum mengirim logbook!');
                const lampiranEl = document.getElementById('section-lampiran-logbook');
                if (lampiranEl) {
                    lampiranEl.scrollIntoView({ behavior: 'smooth' });
                }
                return false;
            }
            this.stopCamera();
        }
    }"
    class="mx-auto w-full max-w-7xl px-4 py-5 sm:px-6 lg:px-8"
>

    {{-- NOTIFIKASI SUCCESS --}}
    @if(session('success'))
        <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-900 shadow-sm" role="alert">
            <div class="flex items-center gap-3">
                <i class="bi bi-check-circle-fill text-lg text-emerald-600"></i>
                <p class="text-sm font-semibold">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    {{-- NOTIFIKASI ERROR --}}
    @if(session('error'))
        <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 p-4 text-rose-900 shadow-sm" role="alert">
            <div class="flex items-center gap-3">
                <i class="bi bi-exclamation-triangle-fill text-lg text-rose-600"></i>
                <p class="text-sm font-semibold">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    {{-- HEADER RINGKAS --}}
    <section class="flex flex-col items-start gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div class="min-w-0">
            <p class="text-base font-bold leading-tight text-slate-800 sm:text-lg">
                {{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('l, d F Y') }}
            </p>
        </div>

        <span class="inline-flex shrink-0 items-center gap-1.5 whitespace-nowrap rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-800 shadow-xs">
            <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <strong x-text="liveClock + ' WIB'" class="font-mono text-emerald-950 font-bold"></strong>
        </span>
    </section>

    {{-- ========================================================= --}}
    {{-- SECTION : JADWAL MENGAJAR GURU HARI INI (MOBILE FRIENDLY) --}}
    {{-- ========================================================= --}}
    <section class="mt-5 rounded-2xl border border-slate-200 bg-white p-3 shadow-sm sm:p-5">
        <div class="mb-3 flex flex-col items-start gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex min-w-0 items-center gap-2">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100 text-base text-emerald-700">
                    <i class="bi bi-calendar-week-fill"></i>
                </span>
                <h2 class="text-sm font-bold leading-tight text-slate-900 sm:text-base">
                    Jadwal Mengajar Hari Ini ({{ $hariIni }})
                </h2>
            </div>
            <span class="shrink-0 rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-bold text-slate-600">
                {{ $jadwals->count() }} Sesi
            </span>
        </div>

        <div class="space-y-2">
            @forelse($jadwals ?? [] as $jadwal)
                @php
                    $isFilled = $jadwal->is_filled;
                    $statusWaktu = $jadwal->status_waktu;
                    $isOngoing = ($statusWaktu === 'berlangsung' && !$isFilled);
                @endphp

                <div class="rounded-xl border p-3 transition sm:p-3.5 {{ $isOngoing ? 'border-emerald-400 bg-white' : 'border-slate-200 bg-white hover:border-slate-300' }}">
                    
                    {{-- Baris 1: Jam Pelajaran & Waktu (Kiri) vs Status Sesi (Kanan) --}}
                    <div class="mb-2 flex flex-wrap items-center gap-1.5 border-b border-slate-100 pb-2 sm:gap-2">
                        <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                            <span class="inline-flex shrink-0 items-center rounded-md px-2 py-0.5 text-xs font-bold {{ $isOngoing ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-700' }}">
                                Jam ke-{{ $jadwal->jam_mulai }}@if($jadwal->jam_selesai && $jadwal->jam_selesai != $jadwal->jam_mulai) - {{ $jadwal->jam_selesai }}@endif
                            </span>
                            <span class="whitespace-nowrap font-mono text-[11px] font-semibold text-slate-500 sm:text-xs">
                                {{ $jadwal->waktu_mulai }} - {{ $jadwal->waktu_selesai }}
                            </span>
                        </div>

                        {{-- Status Badge --}}
                        <div class="shrink-0">
                            @if($isFilled)
                                <span class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-white px-2 py-0.5 text-[10px] font-bold text-slate-600">
                                    <i class="bi bi-check-circle-fill text-emerald-600"></i> Terisi
                                </span>
                            @elseif($statusWaktu === 'berlangsung')
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-600 px-2 py-0.5 text-[10px] font-bold text-white">
                                    <span class="h-1.5 w-1.5 rounded-full bg-white"></span> Berlangsung
                                </span>
                            @elseif($statusWaktu === 'lewat')
                                <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-medium text-slate-500">
                                    <i class="bi bi-clock-history"></i> Terlewat
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-medium text-slate-600">
                                    <i class="bi bi-hourglass-split"></i> Akan Datang
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Informasi kelas dan aksi selalu tampil utuh di semua ukuran layar. --}}
                    <div class="space-y-2">
                        <div class="flex items-start gap-2">
                            <div class="flex min-w-0 flex-wrap items-center gap-2">
                                <span class="inline-flex shrink-0 items-center rounded-md bg-slate-100 px-2 py-0.5 text-xs font-bold text-slate-700">
                                    {{ $jadwal->kelas->nama_kelas ?? 'Kelas' }}
                                </span>
                                <h3 class="break-words text-xs font-bold leading-tight text-slate-900 sm:text-sm" title="{{ $jadwal->mapel->nama_mapel ?? 'Mata Pelajaran' }}">
                                    {{ $jadwal->mapel->nama_mapel ?? 'Mata Pelajaran' }}
                                </h3>
                            </div>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="flex w-full items-center">
                            @if($isFilled)
                                <a href="{{ route('guru.riwayat') }}"
                                   class="inline-flex w-full items-center justify-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">
                                    <i class="bi bi-eye"></i>
                                    <span>Lihat Jurnal</span>
                                </a>
                            @elseif($statusWaktu === 'lewat')
                                <span class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 text-center text-xs font-medium text-slate-400 cursor-not-allowed">
                                    <i class="bi bi-lock-fill mr-1"></i>Tenggat Lewat
                                </span>
                            @elseif($statusWaktu === 'belum_mulai')
                                <span class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 text-center text-xs font-medium text-slate-500 cursor-not-allowed">
                                    <i class="bi bi-clock-history mr-1"></i>Belum Dimulai
                                </span>
                            @else
                                <button
                                    type="button"
                                    @click="pilihJadwal('{{ $jadwal->id_kelas }}', '{{ $jadwal->id_mapel }}', '{{ $jadwal->jam_mulai }}', '{{ $jadwal->jam_selesai }}')"
                                    class="inline-flex w-full items-center justify-center gap-1.5 rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-bold text-white transition hover:bg-emerald-700"
                                >
                                    <i class="bi bi-pencil-square"></i>
                                    <span>Isi Logbook</span>
                                </button>
                            @endif
                        </div>
                    </div>

                </div>
            @empty
                <div class="p-8 text-center text-xs text-slate-400 bg-slate-50 rounded-xl border border-dashed border-slate-200">
                    <i class="bi bi-calendar-x text-2xl text-slate-300 block mb-1"></i>
                    Tidak ada jadwal mengajar terdaftar untuk hari {{ $hariIni }}.
                </div>
            @endforelse
        </div>
    </section>

    {{-- ========================================================= --}}
    {{-- SECTION : FORM PENGISIAN JURNAL / LOGBOOK --}}
    {{-- ========================================================= --}}
    <section id="form-logbook-section" class="mt-6">
        <form
            action="{{ route('guru.jurnal.store') }}"
            method="POST"
            enctype="multipart/form-data"
            class="space-y-5"
            @submit="submitForm($event)"
        >
            @csrf

            {{-- 1. DATA KELAS & MATA PELAJARAN --}}
            <div class="rounded-2xl bg-white p-4 shadow-sm border border-slate-100 sm:p-5">
                <div class="border-b border-slate-100 pb-3 mb-4 flex items-center gap-2">
                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-100 text-sm text-emerald-700">
                        <i class="bi bi-journal-text"></i>
                    </span>
                    <h3 class="text-base font-bold text-slate-800">
                        Isi logbook &amp; jurnal mengajar
                    </h3>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    {{-- GURU PENGAJAR --}}
                    <label class="block">
                        <span class="text-xs font-bold text-slate-700">Nama Guru Pengajar</span>
                        <input
                            type="text"
                            value="{{ $user->name }}"
                            readonly
                            class="mt-1.5 w-full cursor-not-allowed rounded-lg border border-slate-200 bg-slate-100 px-3.5 py-2.5 text-sm font-semibold text-slate-700 outline-none"
                        >
                    </label>

                    {{-- NIP GURU --}}
                    <label class="block">
                        <span class="text-xs font-bold text-slate-700">NIP / Username</span>
                        <input
                            type="text"
                            value="{{ $user->nip ?? $user->username ?? '-' }}"
                            readonly
                            class="mt-1.5 w-full cursor-not-allowed rounded-lg border border-slate-200 bg-slate-100 px-3.5 py-2.5 text-sm font-semibold text-slate-700 outline-none"
                        >
                    </label>

                    {{-- MATA PELAJARAN (SEARCHABLE DROPDOWN WITH FLOATING/STICKY SEARCH) --}}
                    <div class="relative block" @click.outside="openMapelDropdown = false">
                        <span class="text-xs font-bold text-slate-700">Mata Pelajaran <span class="text-rose-500">*</span></span>
                        <input type="hidden" name="id_mapel" :value="selectedMapel" required>
                        
                        {{-- Trigger Button --}}
                        <button
                            type="button"
                            @click="openMapelDropdown = !openMapelDropdown; if(openMapelDropdown) { openKelasDropdown = false; $nextTick(() => $refs.inputSearchMapel?.focus()); }"
                            class="mt-1.5 flex w-full items-center justify-between rounded-lg border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-left text-slate-700 transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100"
                        >
                            <span x-text="getMapelName(selectedMapel) || '-- Pilih Mata Pelajaran --'" :class="{'text-slate-400': !selectedMapel, 'text-slate-800 font-medium': selectedMapel}"></span>
                            <i class="bi bi-chevron-down text-xs text-slate-400 transition-transform duration-200" :class="{'rotate-180': openMapelDropdown}"></i>
                        </button>

                        {{-- Dropdown Menu --}}
                        <div
                            x-show="openMapelDropdown"
                            x-cloak
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute left-0 right-0 z-30 mt-1 max-h-64 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl"
                        >
                            {{-- FLOATING / STICKY SEARCH INPUT --}}
                            <div class="sticky top-0 z-10 border-b border-slate-100 bg-white/95 backdrop-blur-xs p-2 shadow-2xs">
                                <div class="relative flex items-center">
                                    <i class="bi bi-search absolute left-2.5 text-slate-400 text-xs"></i>
                                    <input
                                        type="search"
                                        x-ref="inputSearchMapel"
                                        x-model="searchMapel"
                                        placeholder="Cari mata pelajaran..."
                                        class="w-full rounded-lg border border-slate-200 bg-slate-50/80 py-1.5 pl-8 pr-7 text-xs text-slate-700 outline-none focus:border-emerald-600 focus:bg-white focus:ring-2 focus:ring-emerald-100"
                                    >
                                    <button
                                        type="button"
                                        x-show="searchMapel"
                                        @click="searchMapel = ''; $refs.inputSearchMapel?.focus()"
                                        class="absolute right-2 text-slate-400 hover:text-slate-600 text-xs"
                                    >
                                        <i class="bi bi-x-circle-fill"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- Scrollable List --}}
                            <div class="max-h-48 overflow-y-auto p-1 space-y-0.5 custom-scrollbar">
                                @foreach($mapels as $mapel)
                                    @php
                                        $namaMapel = $mapel->nama_mapel ?? $mapel->nama ?? $mapel->kode;
                                    @endphp
                                    <button
                                        type="button"
                                        x-show="!searchMapel || '{{ strtolower(addslashes($namaMapel)) }}'.includes(searchMapel.toLowerCase().trim())"
                                        @click="selectedMapel = '{{ $mapel->id }}'; openMapelDropdown = false; searchMapel = ''"
                                        class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-left text-xs font-medium transition hover:bg-emerald-50 hover:text-emerald-800"
                                        :class="selectedMapel == '{{ $mapel->id }}' ? 'bg-emerald-100 text-emerald-900 font-bold' : 'text-slate-700'"
                                    >
                                        <span>{{ $namaMapel }}</span>
                                        <i x-show="selectedMapel == '{{ $mapel->id }}'" class="bi bi-check-lg text-emerald-600 font-bold"></i>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- KELAS (SEARCHABLE DROPDOWN WITH FLOATING/STICKY SEARCH) --}}
                    <div class="relative block" @click.outside="openKelasDropdown = false">
                        <span class="text-xs font-bold text-slate-700">Kelas <span class="text-rose-500">*</span></span>
                        <input type="hidden" name="id_kelas" :value="selectedKelas" required>
                        
                        {{-- Trigger Button --}}
                        <button
                            type="button"
                            @click="openKelasDropdown = !openKelasDropdown; if(openKelasDropdown) { openMapelDropdown = false; $nextTick(() => $refs.inputSearchKelas?.focus()); }"
                            class="mt-1.5 flex w-full items-center justify-between rounded-lg border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-left text-slate-700 transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100"
                        >
                            <span x-text="getKelasName(selectedKelas) || '-- Pilih Kelas --'" :class="{'text-slate-400': !selectedKelas, 'text-slate-800 font-medium': selectedKelas}"></span>
                            <i class="bi bi-chevron-down text-xs text-slate-400 transition-transform duration-200" :class="{'rotate-180': openKelasDropdown}"></i>
                        </button>

                        {{-- Dropdown Menu --}}
                        <div
                            x-show="openKelasDropdown"
                            x-cloak
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute left-0 right-0 z-30 mt-1 max-h-64 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl"
                        >
                            {{-- FLOATING / STICKY SEARCH INPUT --}}
                            <div class="sticky top-0 z-10 border-b border-slate-100 bg-white/95 backdrop-blur-xs p-2 shadow-2xs">
                                <div class="relative flex items-center">
                                    <i class="bi bi-search absolute left-2.5 text-slate-400 text-xs"></i>
                                    <input
                                        type="search"
                                        x-ref="inputSearchKelas"
                                        x-model="searchKelas"
                                        placeholder="Cari kelas (contoh: X RPL 1, XI TKJ)..."
                                        class="w-full rounded-lg border border-slate-200 bg-slate-50/80 py-1.5 pl-8 pr-7 text-xs text-slate-700 outline-none focus:border-emerald-600 focus:bg-white focus:ring-2 focus:ring-emerald-100"
                                    >
                                    <button
                                        type="button"
                                        x-show="searchKelas"
                                        @click="searchKelas = ''; $refs.inputSearchKelas?.focus()"
                                        class="absolute right-2 text-slate-400 hover:text-slate-600 text-xs"
                                    >
                                        <i class="bi bi-x-circle-fill"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- Scrollable List --}}
                            <div class="max-h-48 overflow-y-auto p-1 space-y-0.5 custom-scrollbar">
                                @foreach($kelases as $kelas)
                                    <button
                                        type="button"
                                        x-show="!searchKelas || '{{ strtolower(addslashes($kelas->nama_kelas)) }}'.includes(searchKelas.toLowerCase().trim())"
                                        @click="selectedKelas = '{{ $kelas->id_kelas }}'; openKelasDropdown = false; searchKelas = ''; searchSiswa = ''"
                                        class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-left text-xs font-medium transition hover:bg-emerald-50 hover:text-emerald-800"
                                        :class="selectedKelas == '{{ $kelas->id_kelas }}' ? 'bg-emerald-100 text-emerald-900 font-bold' : 'text-slate-700'"
                                    >
                                        <span>{{ $kelas->nama_kelas }}</span>
                                        <i x-show="selectedKelas == '{{ $kelas->id_kelas }}'" class="bi bi-check-lg text-emerald-600 font-bold"></i>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- JAM PELAJARAN --}}
                    <div class="grid grid-cols-2 gap-3 sm:col-span-1">
                        <label class="block">
                            <span class="text-xs font-bold text-slate-700">Jam Ke- <span class="text-rose-500">*</span></span>
                            <input
                                type="number"
                                name="jam_ke"
                                x-model="selectedJamKe"
                                min="1"
                                max="13"
                                required
                                placeholder="Mulai"
                                class="mt-1.5 w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm text-slate-700 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100"
                            >
                        </label>

                        <label class="block">
                            <span class="text-xs font-bold text-slate-700">Sampai Jam Ke- <span class="text-rose-500">*</span></span>
                            <input
                                type="number"
                                name="jam_selesai"
                                x-model="selectedJamSelesai"
                                :min="selectedJamKe"
                                max="13"
                                required
                                placeholder="Selesai"
                                class="mt-1.5 w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm text-slate-700 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100"
                            >
                        </label>
                    </div>

                    {{-- STATUS TUGAS --}}
                    <label class="block">
                        <span class="text-xs font-bold text-slate-700">Ada Tugas untuk Siswa?</span>
                        <select
                            name="ada_tugas"
                            required
                            class="mt-1.5 w-full rounded-lg border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100"
                        >
                            <option value="Tidak">Tidak Ada Tugas</option>
                            <option value="Ya">Ada Tugas</option>
                            <option value="Ya">Ada Tugas luar</option>
                        </select>
                    </label>

                    {{-- MATERI PEMBELAJARAN --}}
                    <label class="block sm:col-span-2">
                        <span class="text-xs font-bold text-slate-700">Materi / Pokok Pembahasan <span class="text-rose-500">*</span></span>
                        <input
                            type="text"
                            name="materi"
                            required
                            placeholder="Contoh: Pengenalan struktur data array dan penerapannya"
                            class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm text-slate-700 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100"
                        >
                    </label>
                </div>
            </div>

            {{-- 2. LAMPIRAN BUKTI HADIR DI KELAS (FOTO LIVE) --}}
            <div id="section-lampiran-logbook" class="rounded-2xl bg-white p-4 shadow-sm border border-slate-100 sm:p-5">
                <div class="border-b border-slate-100 pb-3 flex items-center gap-2">
                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-100 text-sm text-emerald-700">
                        <i class="bi bi-camera-fill"></i>
                    </span>
                    <h3 class="text-base font-bold text-slate-800">
                        Lampiran Foto Live Mengajar <span class="text-rose-500">*</span>
                    </h3>
                </div>

                <div class="mt-3">
                    <input
                        type="file"
                        name="lampiran"
                        accept="image/*"
                        x-ref="lampiranInput"
                        class="hidden"
                    >

                    {{-- Preview Foto Tersimpan --}}
                    <div x-show="capturedPhoto" class="mt-2">
                        <div class="relative inline-block">
                            <img :src="capturedPhoto" alt="Foto Bukti Mengajar" class="h-48 w-full rounded-xl object-cover shadow-sm sm:w-auto sm:max-w-xs">
                            <span class="absolute left-2 top-2 rounded-full bg-emerald-600/90 px-2 py-0.5 text-[10px] font-bold text-white shadow">
                                Foto Tersimpan ✓
                            </span>
                        </div>
                        <div class="mt-1.5">
                            <button type="button" @click="retakePhoto()" class="text-xs font-semibold text-emerald-600 hover:underline inline-flex items-center gap-1">
                                <i class="bi bi-arrow-repeat"></i> Ambil Ulang Foto Live
                            </button>
                        </div>
                    </div>

                    {{-- Canvas Capture --}}
                    <canvas x-ref="cameraCanvasLogbook" class="hidden"></canvas>

                    {{-- Video Kamera Live --}}
                    <div x-show="cameraActive && !capturedPhoto" class="mt-2">
                        <div class="relative overflow-hidden rounded-xl bg-black shadow-sm" style="max-width: 360px;">
                            <video x-ref="cameraVideoLogbook" autoplay playsinline muted class="w-full rounded-xl"></video>
                            <div class="absolute inset-x-0 bottom-0 flex justify-center pb-3">
                                <button type="button" @click="capturePhoto()"
                                        class="flex h-12 w-12 items-center justify-center rounded-full bg-white shadow-lg transition hover:bg-emerald-50"
                                        title="Jepret Foto Live">
                                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-emerald-600">
                                        <i class="bi bi-camera-fill text-white text-base"></i>
                                    </span>
                                </button>
                            </div>
                        </div>
                        <button type="button" @click="stopCamera()" class="mt-1.5 text-xs font-medium text-slate-400 hover:text-slate-600">
                            <i class="bi bi-x-circle"></i> Batalkan kamera
                        </button>
                    </div>

                    {{-- Tombol Buka Kamera Live --}}
                    <div x-show="!cameraActive && !capturedPhoto" class="mt-2">
                        <button type="button" @click="startCamera()"
                                class="inline-flex items-center gap-2 rounded-xl border-2 border-dashed border-emerald-400 bg-emerald-50 px-4 py-3 text-xs font-bold text-emerald-700 transition hover:border-emerald-600 hover:bg-emerald-100">
                            <i class="bi bi-camera-fill text-lg"></i>
                            Buka Kamera Live
                        </button>
                    </div>
                </div>
            </div>

            {{-- 3. PRESENSI KEHADIRAN SISWA --}}
            <div class="rounded-2xl bg-white p-4 shadow-sm border border-slate-100 sm:p-5">
                <div class="flex flex-col gap-2.5 sm:flex-row sm:items-center sm:justify-between border-b border-slate-100 pb-3 mb-3">
                    <div class="flex items-center gap-2">
                        <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-100 text-sm text-emerald-700">
                            <i class="bi bi-people-fill"></i>
                        </span>
                        <h3 class="text-base font-bold text-slate-800">
                            Presensi Kehadiran Siswa
                        </h3>
                    </div>

                    {{-- LEGENDA STATUS ABSENSI DENGAN DISPENSASI (D) --}}
                    <div class="flex flex-wrap items-center gap-1.5 text-[11px] font-semibold">
                        <span class="inline-flex items-center gap-1 rounded-md bg-emerald-50 px-2 py-0.5 text-emerald-700 border border-emerald-200">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> H: Hadir
                        </span>
                        <span class="inline-flex items-center gap-1 rounded-md bg-amber-50 px-2 py-0.5 text-amber-700 border border-amber-200">
                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span> S: Sakit
                        </span>
                        <span class="inline-flex items-center gap-1 rounded-md bg-blue-50 px-2 py-0.5 text-blue-700 border border-blue-200">
                            <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span> I: Izin
                        </span>
                        <span class="inline-flex items-center gap-1 rounded-md bg-rose-50 px-2 py-0.5 text-rose-700 border border-rose-200">
                            <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span> A: Alpa
                        </span>
                        <span class="inline-flex items-center gap-1 rounded-md bg-indigo-50 px-2 py-0.5 text-indigo-700 border border-indigo-200">
                            <span class="h-1.5 w-1.5 rounded-full bg-indigo-500"></span> D: Dispensasi
                        </span>
                    </div>
                </div>

                {{-- INFO KELAS BELUM TERPILIH --}}
                <div x-show="!selectedKelas" class="rounded-xl border border-dashed border-slate-200 bg-slate-50 p-6 text-center text-xs text-slate-500">
                    <i class="bi bi-info-circle text-lg text-slate-400 block mb-1"></i>
                    Pilih kelas terlebih dahulu untuk menampilkan daftar siswa.
                </div>

                {{-- SEARCH BAR TERPISAH (TIDAK STICKY, INDEPENDENT) --}}
                <div x-show="selectedKelas" class="rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
                    <div class="relative flex items-center">
                        <i class="bi bi-search absolute left-3 text-slate-400 text-xs"></i>
                        <input
                            type="search"
                            x-model="searchSiswa"
                            placeholder="Cari nama atau NIS siswa..."
                            class="w-full rounded-lg border border-slate-200 bg-slate-50/80 py-2 pl-9 pr-3 text-xs text-slate-700 outline-none transition focus:border-emerald-600 focus:bg-white focus:ring-2 focus:ring-emerald-100"
                        >
                        <button
                            type="button"
                            x-show="searchSiswa"
                            @click.stop="searchSiswa = ''"
                            class="absolute right-2.5 text-slate-400 hover:text-slate-600 text-xs"
                        >
                            <i class="bi bi-x-circle-fill"></i>
                        </button>
                    </div>
                </div>

                {{-- CONTAINER PRESENSI SISWA (STANDALONE, SCROLLABLE) --}}
                <div x-show="selectedKelas" class="relative rounded-xl border border-slate-200/80 bg-slate-50/60 overflow-hidden">
                    {{-- LIST SISWA GROUPED BY KELAS DENGAN NOMOR ABSEN 1..N --}}
                    <div class="p-3 space-y-2">
                        @forelse($siswasByKelas as $kelasId => $kelasSiswas)
                            @if(count($kelasSiswas) > 0)
                                <div class="space-y-2" x-show="selectedKelas === @js((string) $kelasId)">
                                    @foreach($kelasSiswas as $idx => $siswa)
                                        <div
                                            x-show="matchesSearch(@js($siswa->nama), @js($siswa->nis))"
                                            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5 rounded-xl border border-slate-200/80 bg-white p-3 shadow-2xs transition hover:border-emerald-300"
                                        >
                                            {{-- NOMOR & NAMA SISWA (FULL WIDTH DI MOBILE) --}}
                                            <div class="flex items-center gap-2.5 w-full sm:flex-1 sm:min-w-0">
                                                <span class="flex h-6 min-w-6 px-1.5 items-center justify-center rounded-full bg-emerald-50 text-[11px] font-bold text-emerald-800 border border-emerald-200/80 shrink-0">
                                                    {{ $idx + 1 }}
                                                </span>
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-xs font-bold text-slate-800 break-words">
                                                        {{ $siswa->nama }}
                                                    </p>
                                                    <p class="text-[11px] text-slate-400 truncate">
                                                        NIS: {{ $siswa->nis ?? '-' }} &bull; {{ $siswa->jenis_kelamin }}
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- PILIHAN STATUS H, S, I, A, D (FULL WIDTH DI MOBILE) --}}
                                            <fieldset class="flex items-center gap-1 w-full sm:w-auto sm:shrink-0 sm:justify-end justify-between">
                                            {{-- HADIR (H) --}}
                                            <div class="flex-1 sm:flex-none">
                                                <input
                                                    type="radio"
                                                    id="absensi_{{ $siswa->id }}_hadir"
                                                    name="absensi[{{ $siswa->id }}]"
                                                    value="Hadir"
                                                    checked
                                                    class="peer sr-only"
                                                >
                                                <label for="absensi_{{ $siswa->id }}_hadir" class="cursor-pointer flex h-7 w-full sm:w-7 items-center justify-center rounded-lg border text-xs font-bold transition peer-checked:border-emerald-600 peer-checked:bg-emerald-600 peer-checked:text-white border-slate-200 bg-white text-slate-600 hover:bg-slate-50" title="Hadir">
                                                    H
                                                </label>
                                            </div>

                                            {{-- SAKIT (S) --}}
                                            <div class="flex-1 sm:flex-none">
                                                <input
                                                    type="radio"
                                                    id="absensi_{{ $siswa->id }}_sakit"
                                                    name="absensi[{{ $siswa->id }}]"
                                                    value="Sakit"
                                                    class="peer sr-only"
                                                >
                                                <label for="absensi_{{ $siswa->id }}_sakit" class="cursor-pointer flex h-7 w-full sm:w-7 items-center justify-center rounded-lg border text-xs font-bold transition peer-checked:border-amber-500 peer-checked:bg-amber-500 peer-checked:text-white border-slate-200 bg-white text-slate-600 hover:bg-slate-50" title="Sakit">
                                                    S
                                                </label>
                                            </div>

                                            {{-- IZIN (I) --}}
                                            <div class="flex-1 sm:flex-none">
                                                <input
                                                    type="radio"
                                                    id="absensi_{{ $siswa->id }}_izin"
                                                    name="absensi[{{ $siswa->id }}]"
                                                    value="Izin"
                                                    class="peer sr-only"
                                                >
                                                <label for="absensi_{{ $siswa->id }}_izin" class="cursor-pointer flex h-7 w-full sm:w-7 items-center justify-center rounded-lg border text-xs font-bold transition peer-checked:border-blue-500 peer-checked:bg-blue-500 peer-checked:text-white border-slate-200 bg-white text-slate-600 hover:bg-slate-50" title="Izin">
                                                    I
                                                </label>
                                            </div>

                                            {{-- ALPA (A) --}}
                                            <div class="flex-1 sm:flex-none">
                                                <input
                                                    type="radio"
                                                    id="absensi_{{ $siswa->id }}_alpa"
                                                    name="absensi[{{ $siswa->id }}]"
                                                    value="Alpa"
                                                    class="peer sr-only"
                                                >
                                                <label for="absensi_{{ $siswa->id }}_alpa" class="cursor-pointer flex h-7 w-full sm:w-7 items-center justify-center rounded-lg border text-xs font-bold transition peer-checked:border-rose-500 peer-checked:bg-rose-500 peer-checked:text-white border-slate-200 bg-white text-slate-600 hover:bg-slate-50" title="Alpa">
                                                    A
                                                </label>
                                            </div>

                                            {{-- DISPENSASI (D) --}}
                                            <div class="flex-1 sm:flex-none">
                                                <input
                                                    type="radio"
                                                    id="absensi_{{ $siswa->id }}_dispensasi"
                                                    name="absensi[{{ $siswa->id }}]"
                                                    value="Dispensasi"
                                                    class="peer sr-only"
                                                >
                                                <label for="absensi_{{ $siswa->id }}_dispensasi" class="cursor-pointer flex h-7 w-full sm:w-7 items-center justify-center rounded-lg border text-xs font-bold transition peer-checked:border-indigo-600 peer-checked:bg-indigo-600 peer-checked:text-white border-slate-200 bg-white text-slate-600 hover:bg-slate-50" title="Dispensasi">
                                                    D
                                                </label>
                                            </div>
                                        </fieldset>
                                    </div>
                                @endforeach
                                </div>
                            @endif
                        @empty
                            <div class="p-6 text-center text-xs text-slate-400">
                                Belum ada data siswa terdaftar.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- 4. CATATAN KHUSUS (OPSIONAL) --}}
            <div class="rounded-2xl bg-white p-4 shadow-sm border border-slate-100 sm:p-5">
                <label for="catatan-khusus" class="block">
                    <span class="text-xs font-bold text-slate-700">Catatan Khusus / Hambatan (Opsional)</span>
                    <textarea
                        id="catatan-khusus"
                        name="catatan"
                        rows="3"
                        placeholder="Tulis catatan atau kendala pembelajaran jika ada..."
                        class="mt-1.5 w-full resize-none rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm text-slate-700 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100"
                    ></textarea>
                </label>
            </div>

            {{-- SUBMIT BUTTON --}}
            <button
                type="submit"
                class="flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-3.5 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-700 active:scale-98"
            >
                <i class="bi bi-send-fill"></i>
                <span>Kirim Logbook &amp; Presensi Siswa</span>
            </button>
        </form>
    </section>

</div>

@endsection
