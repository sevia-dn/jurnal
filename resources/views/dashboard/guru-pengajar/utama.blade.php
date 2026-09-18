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
        selectedKelas: '{{ $activeJadwal->id_kelas ?? "" }}',
        selectedMapel: '{{ $activeJadwal->id_mapel ?? "" }}',
        selectedJamKe: '{{ $activeJadwal->jam_mulai ?? 1 }}',
        selectedJamSelesai: '{{ $activeJadwal->jam_selesai ?? ($activeJadwal->jam_mulai ?? 1) }}',
        searchSiswa: '',
        liveClock: '{{ $currentFullTime ?? \Carbon\Carbon::now("Asia/Jakarta")->format("H:i:s") }}',

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
            this.selectedKelas = idKelas;
            this.selectedMapel = idMapel;
            this.selectedJamKe = jamMulai;
            this.selectedJamSelesai = jamSelesai || jamMulai;
            const logbookEl = document.getElementById('form-logbook-section');
            if (logbookEl) {
                logbookEl.scrollIntoView({ behavior: 'smooth' });
            }
        },

        matchesSearch(nama, nis) {
            if (!this.searchSiswa || this.searchSiswa.trim() === '') return true;
            const query = this.searchSiswa.toLowerCase().trim();
            return nama.includes(query) || (nis && nis.includes(query));
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
    <section class="flex items-center justify-between">
        <div>
            <p class="text-base font-bold text-slate-800 sm:text-lg">
                {{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('l, d F Y') }}
            </p>
        </div>

        <span class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-800 border border-emerald-200 shadow-xs">
            <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span class="hidden sm:inline">Waktu Server:</span>
            <strong x-text="liveClock + ' WIB'" class="font-mono text-emerald-950 font-bold"></strong>
        </span>
    </section>

    {{-- ========================================================= --}}
    {{-- SECTION : JADWAL MENGAJAR GURU HARI INI --}}
    {{-- ========================================================= --}}
    <section class="mt-5 rounded-2xl bg-white p-4 shadow-sm border border-slate-100 sm:p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100 text-base text-emerald-700">
                    <i class="bi bi-calendar-week-fill"></i>
                </span>
                <h2 class="text-base font-bold text-slate-900">
                    Jadwal Mengajar Hari Ini ({{ $hariIni }})
                </h2>
            </div>
            <span class="text-xs font-semibold text-slate-500">
                {{ $jadwals->count() }} Sesi
            </span>
        </div>

        <div class="divide-y divide-slate-100 rounded-xl border border-slate-100 bg-slate-50/50 overflow-hidden">
            @forelse($jadwals ?? [] as $jadwal)
                @php
                    $isFilled = $jadwal->is_filled;
                    $statusWaktu = $jadwal->status_waktu;
                @endphp

                <div class="flex flex-col gap-2.5 p-3.5 sm:flex-row sm:items-center sm:justify-between transition hover:bg-slate-100/50">
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="flex flex-col items-start">
                            <span class="rounded-lg bg-emerald-100/90 px-2 py-0.5 text-xs font-bold text-emerald-800">
                                Jam ke-{{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}
                            </span>
                            <span class="mt-0.5 text-[11px] font-medium text-slate-500 font-mono">
                                {{ $jadwal->waktu_mulai }} - {{ $jadwal->waktu_selesai }}
                            </span>
                        </div>

                        <div>
                            <h3 class="text-sm font-bold text-slate-900">
                                {{ $jadwal->kelas->nama_kelas ?? 'Kelas' }}
                                <span class="text-slate-300 font-normal mx-1">|</span>
                                {{ $jadwal->mapel->nama_mapel ?? 'Mata Pelajaran' }}
                            </h3>

                            <div class="mt-0.5 flex items-center gap-2">
                                @if($isFilled)
                                    <span class="inline-flex items-center gap-1 rounded-md bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700 border border-emerald-200">
                                        <i class="bi bi-check-circle-fill"></i> Jurnal Terisi
                                    </span>
                                @elseif($statusWaktu === 'lewat')
                                    <span class="inline-flex items-center gap-1 rounded-md bg-rose-50 px-2 py-0.5 text-[10px] font-semibold text-rose-700 border border-rose-200">
                                        <i class="bi bi-clock-history"></i> Lewat Jam Mengajar
                                    </span>
                                @elseif($statusWaktu === 'berlangsung')
                                    <span class="inline-flex items-center gap-1 rounded-md bg-emerald-500 px-2 py-0.5 text-[10px] font-bold text-white shadow-xs">
                                        <span class="h-1.5 w-1.5 rounded-full bg-white animate-ping"></span> Sedang Berlangsung
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-md bg-sky-50 px-2 py-0.5 text-[10px] font-semibold text-sky-700 border border-sky-200">
                                        <i class="bi bi-hourglass-split"></i> Akan Datang
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 self-end sm:self-center">
                        @if($isFilled)
                            <a href="{{ route('guru.riwayat') }}"
                               class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 transition hover:bg-slate-50">
                                <i class="bi bi-eye"></i>
                                <span>Lihat</span>
                            </a>
                        @elseif($statusWaktu === 'lewat')
                            <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-400 cursor-not-allowed">
                                <i class="bi bi-lock-fill mr-1"></i>Tenggat Lewat
                            </span>
                        @elseif($statusWaktu === 'belum_mulai')
                            <span class="rounded-lg bg-sky-50 border border-sky-200 px-2.5 py-1 text-xs font-semibold text-sky-700 cursor-not-allowed">
                                <i class="bi bi-clock-history mr-1"></i>Belum Dimulai
                            </span>
                        @else
                            <button
                                type="button"
                                @click="pilihJadwal('{{ $jadwal->id_kelas }}', '{{ $jadwal->id_mapel }}', '{{ $jadwal->jam_mulai }}', '{{ $jadwal->jam_selesai }}')"
                                class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-bold text-white shadow-xs transition hover:bg-emerald-700"
                            >
                                <i class="bi bi-pencil-square"></i>
                                <span>Isi Logbook</span>
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-xs text-slate-400">
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
            x-data="{
                cameraActive: false,
                cameraStream: null,
                capturedPhoto: null,

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
                        Data Kelas &amp; Mata Pelajaran
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

                    {{-- MATA PELAJARAN --}}
                    <label class="block">
                        <span class="text-xs font-bold text-slate-700">Mata Pelajaran <span class="text-rose-500">*</span></span>
                        <select
                            name="id_mapel"
                            x-model="selectedMapel"
                            required
                            class="mt-1.5 w-full rounded-lg border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100"
                        >
                            <option value="">-- Pilih Mata Pelajaran --</option>
                            @foreach($mapels as $mapel)
                                <option value="{{ $mapel->id }}">
                                    {{ $mapel->nama_mapel ?? $mapel->nama ?? $mapel->kode }}
                                </option>
                            @endforeach
                        </select>
                    </label>

                    {{-- KELAS --}}
                    <label class="block">
                        <span class="text-xs font-bold text-slate-700">Kelas <span class="text-rose-500">*</span></span>
                        <select
                            name="id_kelas"
                            x-model="selectedKelas"
                            required
                            class="mt-1.5 w-full rounded-lg border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100"
                        >
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelases as $kelas)
                                <option value="{{ $kelas->id_kelas }}">
                                    {{ $kelas->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                    </label>

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
                            <option value="Ya">Ya, Ada Tugas</option>
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

                {{-- CONTAINER PRESENSI SISWA DENGAN FLOATING STICKY SEARCH --}}
                <div x-show="selectedKelas" class="relative rounded-xl border border-slate-200/80 bg-slate-50/60 overflow-hidden">
                    
                    {{-- FLOATING / STICKY SEARCH BAR --}}
                    <div class="sticky top-0 z-20 bg-white/95 backdrop-blur-xs border-b border-slate-200 p-2.5 shadow-xs">
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
                                @click="searchSiswa = ''"
                                class="absolute right-2.5 text-slate-400 hover:text-slate-600 text-xs"
                            >
                                <i class="bi bi-x-circle-fill"></i>
                            </button>
                        </div>
                    </div>

                    {{-- LIST SISWA SCROLLABLE --}}
                    <div class="max-h-80 overflow-y-auto p-3 space-y-2 custom-scrollbar">
                        @forelse($siswas as $siswa)
                            <div
                                x-show="selectedKelas == '{{ $siswa->kelas_id }}' && matchesSearch('{{ strtolower($siswa->nama) }}', '{{ strtolower($siswa->nis ?? '') }}')"
                                class="flex flex-col gap-2 rounded-xl border border-slate-200/70 bg-white p-3 sm:flex-row sm:items-center sm:justify-between shadow-2xs transition hover:border-emerald-300"
                            >
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-bold text-slate-800 truncate">
                                        {{ $siswa->nama }}
                                    </p>
                                    <p class="text-[11px] text-slate-400">
                                        NIS: {{ $siswa->nis ?? '-' }} | {{ $siswa->jenis_kelamin }}
                                    </p>
                                </div>

                                {{-- PILIHAN STATUS H, S, I, A, D --}}
                                <div class="flex items-center gap-1.5 shrink-0 self-start sm:self-center">
                                    {{-- HADIR (H) --}}
                                    <label class="cursor-pointer" title="Hadir">
                                        <input
                                            type="radio"
                                            name="absensi[{{ $siswa->id }}]"
                                            value="Hadir"
                                            checked
                                            class="peer sr-only"
                                        >
                                        <span class="flex h-7 w-7 items-center justify-center rounded-lg border text-xs font-bold transition peer-checked:border-emerald-600 peer-checked:bg-emerald-600 peer-checked:text-white border-slate-200 bg-white text-slate-600 hover:bg-slate-50">
                                            H
                                        </span>
                                    </label>

                                    {{-- SAKIT (S) --}}
                                    <label class="cursor-pointer" title="Sakit">
                                        <input
                                            type="radio"
                                            name="absensi[{{ $siswa->id }}]"
                                            value="Sakit"
                                            class="peer sr-only"
                                        >
                                        <span class="flex h-7 w-7 items-center justify-center rounded-lg border text-xs font-bold transition peer-checked:border-amber-500 peer-checked:bg-amber-500 peer-checked:text-white border-slate-200 bg-white text-slate-600 hover:bg-slate-50">
                                            S
                                        </span>
                                    </label>

                                    {{-- IZIN (I) --}}
                                    <label class="cursor-pointer" title="Izin">
                                        <input
                                            type="radio"
                                            name="absensi[{{ $siswa->id }}]"
                                            value="Izin"
                                            class="peer sr-only"
                                        >
                                        <span class="flex h-7 w-7 items-center justify-center rounded-lg border text-xs font-bold transition peer-checked:border-blue-500 peer-checked:bg-blue-500 peer-checked:text-white border-slate-200 bg-white text-slate-600 hover:bg-slate-50">
                                            I
                                        </span>
                                    </label>

                                    {{-- ALPA (A) --}}
                                    <label class="cursor-pointer" title="Alpa">
                                        <input
                                            type="radio"
                                            name="absensi[{{ $siswa->id }}]"
                                            value="Alpa"
                                            class="peer sr-only"
                                        >
                                        <span class="flex h-7 w-7 items-center justify-center rounded-lg border text-xs font-bold transition peer-checked:border-rose-500 peer-checked:bg-rose-500 peer-checked:text-white border-slate-200 bg-white text-slate-600 hover:bg-slate-50">
                                            A
                                        </span>
                                    </label>

                                    {{-- DISPENSASI (D) --}}
                                    <label class="cursor-pointer" title="Dispensasi">
                                        <input
                                            type="radio"
                                            name="absensi[{{ $siswa->id }}]"
                                            value="Dispensasi"
                                            class="peer sr-only"
                                        >
                                        <span class="flex h-7 w-7 items-center justify-center rounded-lg border text-xs font-bold transition peer-checked:border-indigo-600 peer-checked:bg-indigo-600 peer-checked:text-white border-slate-200 bg-white text-slate-600 hover:bg-slate-50">
                                            D
                                        </span>
                                    </label>
                                </div>
                            </div>
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
                class="flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-3.5 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-700"
            >
                <i class="bi bi-send-fill"></i>
                <span>Kirim Logbook &amp; Presensi Siswa</span>
            </button>
        </form>
    </section>

</div>

@endsection
