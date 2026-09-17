@extends('layouts.app')

@section('title', 'Halaman Utama Guru - JurnalKita')

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
</style>

<div
    x-data="{
        hasCheckedIn: {{ $hasCheckedIn ? 'true' : 'false' }},
        hasSubmittedJournal: {{ $hasSubmittedJournal ? 'true' : 'false' }},
        isWithinSchedule: true,
        showForm: false,

        selectedTeacherId: '{{ $user->id }}',
        nipGuru: '{{ $user->nip ?? $user->username ?? "" }}',
        statusKehadiran: 'Hadir',
        selectedKelas: '{{ $activeJadwal->id_kelas ?? "" }}',
        selectedMapel: '{{ $activeJadwal->id_mapel ?? "" }}',
        selectedJamKe: '{{ $activeJadwal->jam_mulai ?? 1 }}',

        teachers: @js($teachers ?? []),

        updateNip() {
            let found = this.teachers.find(
                t => t.id == this.selectedTeacherId
            );

            this.nipGuru = found
                ? (found.nip ?? found.username ?? '-')
                : '';
        },

        pilihJadwal(idKelas, idMapel, jamMulai) {
            this.selectedKelas = idKelas;
            this.selectedMapel = idMapel;
            this.selectedJamKe = jamMulai;
            const logbookEl = document.getElementById('form-logbook-section');
            if (logbookEl) {
                logbookEl.scrollIntoView({ behavior: 'smooth' });
            }
        }
    }"
    class="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 lg:px-8"
>

    {{-- NOTIFIKASI SUCCESS --}}
    @if(session('success'))
        <div
            class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-900 shadow-sm"
            role="alert"
        >
            <div class="flex items-center gap-3">
                <i class="bi bi-check-circle-fill text-lg text-emerald-600"></i>
                <p class="text-sm font-semibold">
                    {{ session('success') }}
                </p>
            </div>
        </div>
    @endif

    {{-- NOTIFIKASI ERROR / PERINGATAN --}}
    @if(session('error'))
        <div
            class="mb-6 rounded-xl border border-rose-200 bg-rose-50 p-4 text-rose-900 shadow-sm"
            role="alert"
        >
            <div class="flex items-center gap-3">
                <i class="bi bi-exclamation-triangle-fill text-lg text-rose-600"></i>
                <p class="text-sm font-semibold">
                    {{ session('error') }}
                </p>
            </div>
        </div>
    @endif

    {{-- HEADER --}}
    <section class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">

        <div>
            <p class="text-sm font-semibold text-emerald-700">
                {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
            </p>

            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                Halaman Utama Guru
            </h1>

            <p class="mt-2 text-sm text-slate-500">
                Selamat datang, <strong class="text-slate-800">{{ $user->name }}</strong> (NIP: {{ $user->nip ?? $user->username ?? '-' }}).
            </p>
        </div>

        <span class="w-fit rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700">
            SMKN 1 Boyolangu
        </span>

    </section>


    {{-- ========================================================= --}}
    {{-- SECTION 1 : KEHADIRAN GURU & JADWAL MENGAJAR --}}
    {{-- ========================================================= --}}

    <section
        id="section-kehadiran-guru"
        class="mt-6 rounded-2xl bg-white p-5 shadow-md sm:p-6"
        aria-labelledby="lapor-kehadiran-guru"
    >

        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">

            <div class="flex items-start gap-3">

                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-xl text-emerald-700">
                    <i class="bi bi-person-check-fill"></i>
                </span>

                <div>
                    <p class="text-sm font-semibold text-emerald-700">
                        Presensi & Jadwal Mengajar Pribadi
                    </p>

                    <h2
                        id="lapor-kehadiran-guru"
                        class="mt-1 text-xl font-bold text-slate-900"
                    >
                        Jadwal & Kehadiran Saya
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Lakukan absen masuk setiap hari sebelum memulai kegiatan mengajar di kelas.
                    </p>
                </div>

            </div>

            <span
                x-show="!hasCheckedIn"
                class="w-fit rounded-full bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700"
            >
                Belum absen hari ini
            </span>

            <span
                x-cloak
                x-show="hasCheckedIn"
                class="w-fit rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700"
            >
                Sudah absen masuk
            </span>

        </div>

        {{-- TAB FILTER HARI JADWAL --}}
        <div class="mt-6 flex flex-wrap items-center gap-2 border-b border-slate-100 pb-3">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500 mr-1">
                Jadwal Hari:
            </span>
            @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $hari)
                @php
                    $isHariIni = ($hari === $hariIni);
                    $isSelected = ($hari === $selectedHari);
                    $count = $jadwalCounts[$hari] ?? 0;
                @endphp
                <a
                    href="{{ route('guru.utama', ['hari' => $hari]) }}"
                    class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold transition {{ $isSelected ? 'bg-emerald-600 text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}"
                >
                    <span>{{ $hari }}</span>
                    @if($count > 0)
                        <span class="rounded-full px-1.5 py-0.5 text-[10px] {{ $isSelected ? 'bg-emerald-800 text-white' : 'bg-slate-200 text-slate-700' }}">
                            {{ $count }}
                        </span>
                    @endif
                    @if($isHariIni)
                        <span class="text-[10px] {{ $isSelected ? 'text-emerald-100 font-medium' : 'text-emerald-600 font-bold' }}">
                            (Hari Ini)
                        </span>
                    @endif
                </a>
            @endforeach
        </div>

        {{-- DAFTAR JADWAL MENGAJAR --}}
        <div class="mt-4 divide-y divide-slate-100 rounded-xl border border-slate-100 bg-slate-50/50">

            <div class="bg-slate-100/70 px-4 py-2 flex items-center justify-between">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-600">
                    Jadwal Hari {{ $selectedHari }} ({{ $jadwals->count() }} Sesi Mengajar)
                </p>
                @if($selectedHari !== $hariIni)
                    <a href="{{ route('guru.utama') }}" class="text-xs font-semibold text-emerald-600 hover:underline">
                        Kembali ke Hari Ini ({{ $hariIni }})
                    </a>
                @endif
            </div>

            @forelse($jadwals ?? [] as $index => $jadwal)

                <div class="flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-between">

                    <div class="flex items-center gap-4">

                        <span class="min-w-[85px] rounded-lg bg-emerald-50 px-2 py-1 text-center text-xs font-bold text-emerald-700">
                            Jam ke-{{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}
                        </span>

                        <div>

                            <h3 class="text-sm font-bold text-slate-800">
                                {{ $jadwal->kelas->nama_kelas ?? 'Kelas' }}
                                —
                                {{ $jadwal->mapel->nama_mapel ?? 'Mata Pelajaran' }}
                            </h3>

                        </div>

                    </div>

                    <div class="flex items-center gap-2">

                        @if($selectedHari === $hariIni && $index === 0)

                            <span class="rounded-md bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                Sesi Pertama
                            </span>

                        @else

                            <span class="text-xs font-medium text-slate-400">
                                Sesi {{ $index + 1 }}
                            </span>

                        @endif

                        <button
                            type="button"
                            x-show="hasCheckedIn"
                            @click="pilihJadwal('{{ $jadwal->id_kelas }}', '{{ $jadwal->id_mapel }}', '{{ $jadwal->jam_mulai }}')"
                            class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-200 bg-white px-2.5 py-1 text-xs font-semibold text-emerald-700 shadow-sm transition hover:bg-emerald-50"
                            title="Gunakan jadwal ini untuk isi logbook"
                        >
                            <i class="bi bi-pencil-square"></i>
                            <span>Pilih Jadwal</span>
                        </button>

                    </div>

                </div>

            @empty

                <div class="p-6 text-center text-sm text-slate-500">
                    Tidak ada jadwal mengajar terdaftar untuk hari {{ $selectedHari }}.
                </div>

            @endforelse

        </div>


        {{-- TOMBOL ABSEN --}}
        <div class="mt-5 flex flex-col gap-3 rounded-xl bg-slate-50 p-4 sm:flex-row sm:items-center sm:justify-between">

            <p class="flex items-start gap-2 text-xs leading-relaxed text-slate-500">

                <i class="bi bi-info-circle-fill mt-0.5 text-emerald-600"></i>

                <span>
                    Data presensi masuk otomatis diteruskan ke sistem monitoring Guru Piket dan Waka Kurikulum.
                </span>

            </p>

            <button
                type="button"
                @click="showForm = true"
                x-show="!hasCheckedIn"
                class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg bg-emerald-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700"
            >
                <i class="bi bi-box-arrow-in-right"></i>
                Absen Masuk / Lapor Kehadiran
            </button>

            <span
                x-cloak
                x-show="hasCheckedIn"
                class="inline-flex shrink-0 items-center gap-2 rounded-lg bg-emerald-100 px-4 py-3 text-sm font-semibold text-emerald-700"
            >
                <i class="bi bi-check-circle-fill"></i>
                Presensi Masuk Hari Ini Tercatat
            </span>

        </div>


        {{-- FORM ABSEN GURU --}}
        <form
            x-cloak
            x-show="showForm"
            x-transition
            action="{{ route('guru.absen.store') }}"
            method="POST"
            enctype="multipart/form-data"
            class="mt-6 rounded-2xl border border-emerald-100 bg-white p-5 shadow-md sm:p-6"
            x-data="{
                statusAbsen: 'Hadir',
                cameraActive: false,
                cameraStream: null,
                capturedPhoto: null,

                async startCamera() {
                    try {
                        this.cameraStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false });
                        this.$nextTick(() => {
                            const vid = this.$refs.cameraVideo;
                            if (vid) { vid.srcObject = this.cameraStream; vid.play(); }
                        });
                        this.cameraActive = true;
                        this.capturedPhoto = null;
                    } catch(e) {
                        alert('Kamera tidak dapat diakses. Pastikan izin kamera diberikan.');
                    }
                },

                capturePhoto() {
                    const vid = this.$refs.cameraVideo;
                    const canvas = this.$refs.cameraCanvas;
                    if (!vid || !canvas) return;
                    canvas.width = vid.videoWidth;
                    canvas.height = vid.videoHeight;
                    canvas.getContext('2d').drawImage(vid, 0, 0);
                    this.capturedPhoto = canvas.toDataURL('image/jpeg', 0.85);
                    this.$refs.fotoInput.value = '';
                    // Convert dataURL to File untuk input hidden
                    fetch(this.capturedPhoto)
                        .then(r => r.blob())
                        .then(blob => {
                            const file = new File([blob], 'foto-kehadiran.jpg', { type: 'image/jpeg' });
                            const dt = new DataTransfer();
                            dt.items.add(file);
                            this.$refs.fotoInput.files = dt.files;
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
                }
            }"
            @submit="stopCamera()"
        >

            @csrf

            <div class="flex flex-col gap-3 border-b border-slate-100 pb-5 sm:flex-row sm:items-start sm:justify-between">

                <div class="flex items-start gap-3">

                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                        <i class="bi bi-clipboard2-check-fill"></i>
                    </span>

                    <div>

                        <p class="text-sm font-semibold text-emerald-700">
                            Form Kehadiran
                        </p>

                        <h3 class="mt-1 text-lg font-bold text-slate-900">
                            Lapor Kehadiran Guru
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            Lengkapi laporan kehadiran Anda sebelum mengisi jurnal/logbook mengajar.
                        </p>

                    </div>

                </div>

                <span class="w-fit rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700">
                    Hari ini
                </span>

            </div>


            <div class="mt-5 grid gap-5 sm:grid-cols-2">

                {{-- NAMA GURU --}}
                <label for="nama-guru" class="block">

                    <span class="text-sm font-semibold text-slate-700">
                        Nama Lengkap Guru
                    </span>

                    <select
                        id="nama-guru"
                        name="teacher_id"
                        x-model="selectedTeacherId"
                        @change="updateNip()"
                        required
                        class="mt-2 w-full rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700"
                    >

                        <option value="">
                            -- Pilih Nama Guru --
                        </option>

                        <template x-for="t in teachers" :key="t.id">

                            <option
                                :value="t.id"
                                x-text="t.name ?? t.nama"
                                :selected="t.id == {{ $user->id }}"
                            ></option>

                        </template>

                    </select>

                </label>


                {{-- NIP --}}
                <label for="nip-guru" class="block">

                    <span class="text-sm font-semibold text-slate-700">
                        NIP / Username
                    </span>

                    <input
                        id="nip-guru"
                        type="text"
                        name="nip"
                        x-model="nipGuru"
                        readonly
                        class="mt-2 w-full cursor-not-allowed rounded-lg border border-slate-200 bg-slate-100 px-4 py-3 text-sm font-medium text-slate-600"
                        placeholder="Terisi otomatis setelah guru dipilih"
                    >

                </label>


                {{-- STATUS KEHADIRAN — Hadir / Tidak Hadir --}}
                <div class="sm:col-span-2">
                    <span class="text-sm font-semibold text-slate-700">Status Kehadiran</span>
                    <div class="mt-3 flex gap-4">
                        <label class="flex flex-1 cursor-pointer items-center gap-3 rounded-xl border-2 p-4 transition"
                               :class="statusAbsen === 'Hadir' ? 'border-emerald-500 bg-emerald-50' : 'border-slate-200 hover:border-slate-300'">
                            <input type="radio" name="status_kehadiran_guru" value="Hadir"
                                   x-model="statusAbsen" class="sr-only">
                            <span class="flex h-9 w-9 items-center justify-center rounded-full"
                                  :class="statusAbsen === 'Hadir' ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-100 text-slate-400'">
                                <i class="bi bi-check-circle-fill text-lg"></i>
                            </span>
                            <div>
                                <p class="text-sm font-bold" :class="statusAbsen === 'Hadir' ? 'text-emerald-700' : 'text-slate-700'">Hadir</p>
                                <p class="text-xs text-slate-400">Saya hadir mengajar hari ini</p>
                            </div>
                        </label>

                        <label class="flex flex-1 cursor-pointer items-center gap-3 rounded-xl border-2 p-4 transition"
                               :class="statusAbsen === 'Tidak Hadir' ? 'border-rose-500 bg-rose-50' : 'border-slate-200 hover:border-slate-300'">
                            <input type="radio" name="status_kehadiran_guru" value="Tidak Hadir"
                                   x-model="statusAbsen" class="sr-only">
                            <span class="flex h-9 w-9 items-center justify-center rounded-full"
                                  :class="statusAbsen === 'Tidak Hadir' ? 'bg-rose-100 text-rose-600' : 'bg-slate-100 text-slate-400'">
                                <i class="bi bi-x-circle-fill text-lg"></i>
                            </span>
                            <div>
                                <p class="text-sm font-bold" :class="statusAbsen === 'Tidak Hadir' ? 'text-rose-700' : 'text-slate-700'">Tidak Hadir</p>
                                <p class="text-xs text-slate-400">Saya tidak hadir hari ini</p>
                            </div>
                        </label>
                    </div>
                </div>


                {{-- ALASAN (hanya jika Tidak Hadir) --}}
                <div x-cloak x-show="statusAbsen === 'Tidak Hadir'" class="sm:col-span-2">
                    <label for="alasan-kehadiran" class="block">
                        <span class="text-sm font-semibold text-slate-700">
                            Alasan Tidak Hadir <span class="text-rose-500">*</span>
                        </span>
                        <textarea
                            id="alasan-kehadiran"
                            name="reason"
                            rows="3"
                            placeholder="Tuliskan alasan ketidakhadiran Anda secara detail..."
                            class="mt-2 w-full rounded-lg border border-slate-200 p-4 text-sm text-slate-700 focus:border-rose-400 focus:ring-2 focus:ring-rose-100"
                        ></textarea>
                    </label>
                </div>


                {{-- FOTO LIVE KAMERA (wajib jika Hadir) --}}
                <div x-cloak x-show="statusAbsen === 'Hadir'" class="sm:col-span-2">
                    <span class="block text-sm font-semibold text-slate-700">
                        Foto Kehadiran (Live Kamera) <span class="text-rose-500">*</span>
                    </span>
                    <p class="mt-0.5 text-xs text-slate-400">Ambil foto langsung melalui kamera sebagai bukti kehadiran Anda di sekolah.</p>

                    <input type="file" name="proof_file" accept="image/*" x-ref="fotoInput" class="hidden">

                    {{-- Preview Foto --}}
                    <div x-show="capturedPhoto" class="mt-3">
                        <div class="relative inline-block">
                            <img :src="capturedPhoto" alt="Foto Kehadiran" class="h-48 w-full rounded-xl object-cover shadow-md sm:w-auto sm:max-w-xs">
                            <span class="absolute left-2 top-2 rounded-full bg-emerald-600/90 px-2 py-0.5 text-[10px] font-bold text-white">Foto Tersimpan ✓</span>
                        </div>
                        <button type="button" @click="retakePhoto()" class="mt-2 block text-xs font-semibold text-emerald-600 hover:underline">
                            <i class="bi bi-arrow-repeat"></i> Ambil ulang foto
                        </button>
                    </div>

                    {{-- Canvas tersembunyi --}}
                    <canvas x-ref="cameraCanvas" class="hidden"></canvas>

                    {{-- Video Kamera --}}
                    <div x-show="cameraActive && !capturedPhoto" class="mt-3">
                        <div class="relative overflow-hidden rounded-xl bg-black shadow-md" style="max-width: 360px;">
                            <video x-ref="cameraVideo" autoplay playsinline muted class="w-full rounded-xl" style="transform: scaleX(-1);"></video>
                            <div class="absolute inset-x-0 bottom-0 flex justify-center pb-4">
                                <button type="button" @click="capturePhoto()"
                                        class="flex h-14 w-14 items-center justify-center rounded-full bg-white shadow-lg transition hover:bg-emerald-50">
                                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-600">
                                        <i class="bi bi-camera-fill text-white text-lg"></i>
                                    </span>
                                </button>
                            </div>
                        </div>
                        <button type="button" @click="stopCamera()" class="mt-2 text-xs font-medium text-slate-400 hover:text-slate-600">
                            <i class="bi bi-x"></i> Batalkan kamera
                        </button>
                    </div>

                    {{-- Tombol Buka Kamera --}}
                    <div x-show="!cameraActive && !capturedPhoto" class="mt-3">
                        <button type="button" @click="startCamera()"
                                class="inline-flex items-center gap-2 rounded-xl border-2 border-dashed border-emerald-300 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700 transition hover:border-emerald-500 hover:bg-emerald-100">
                            <i class="bi bi-camera-fill text-xl"></i>
                            Buka Kamera & Ambil Foto
                        </button>
                    </div>
                </div>

            </div>


            {{-- TOMBOL SUBMIT ABSEN --}}
            <div class="mt-6 flex justify-end gap-3 border-t border-slate-100 pt-5">

                <button
                    type="button"
                    @click="showForm = false; stopCamera()"
                    class="rounded-lg border border-slate-200 px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700 shadow-sm"
                >
                    Kirim Laporan Kehadiran
                </button>

            </div>

        </form>

    </section>


    {{-- ========================================================= --}}
    {{-- SECTION 2 : JURNAL PEMBELAJARAN (LOGBOOK) --}}
    {{-- ========================================================= --}}

    <section
        id="form-logbook-section"
        class="mt-8"
        aria-labelledby="logbook-pembelajaran"
    >

        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">

            <div>

                <h2
                    id="logbook-pembelajaran"
                    class="text-xl font-bold text-slate-900"
                >
                    Jurnal Pembelajaran (Logbook)
                </h2>

                <p class="text-sm text-slate-500">
                    Catat materi yang diajarkan dan rekap kehadiran siswa hari ini.
                </p>

            </div>

            @if($hasSubmittedJournal)
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800">
                    <i class="bi bi-check2-all"></i>
                    Jurnal Hari Ini Sudah Terkirim
                </span>
            @endif

        </div>

        {{-- PERINGATAN WAJIB ABSEN TERLEBIH DAHULU --}}
        @if(!$hasCheckedIn)
            <div class="mt-4 rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm sm:p-6">
                <div class="flex items-start gap-4">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-xl text-amber-700">
                        <i class="bi bi-shield-lock-fill"></i>
                    </span>
                    <div class="flex-1">
                        <h3 class="text-base font-bold text-amber-900">
                            Presensi Masuk Diperlukan
                        </h3>
                        <p class="mt-1 text-sm leading-relaxed text-amber-700">
                            Anda <strong>WAJIB melakukan absen/presensi masuk</strong> terlebih dahulu hari ini sebelum dapat membuka form atau mengisi Jurnal Pembelajaran.
                        </p>
                        <div class="mt-4">
                            <button
                                type="button"
                                @click="showForm = true; document.getElementById('section-kehadiran-guru')?.scrollIntoView({behavior: 'smooth'})"
                                class="inline-flex items-center gap-2 rounded-xl bg-amber-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-amber-700"
                            >
                                <i class="bi bi-box-arrow-in-right"></i>
                                Isi Absen Masuk Sekarang
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- NOTIFIKASI SUDAH SUBMIT JURNAL HARI INI --}}
        @if($hasSubmittedJournal)

            <div class="mt-4 rounded-2xl border border-emerald-100 bg-emerald-50/60 p-6 text-center">

                <div class="flex flex-col items-center justify-center gap-2">

                    <span class="flex h-12 w-12 items-center justify-center rounded-full bg-emerald-100 text-2xl text-emerald-600">
                        <i class="bi bi-check2-circle"></i>
                    </span>

                    <h3 class="text-base font-bold text-slate-800">
                        Jurnal Hari Ini Telah Diisi
                    </h3>

                    <p class="max-w-md text-xs text-slate-500">
                        Anda telah berhasil mengirimkan laporan jurnal pembelajaran hari ini. Silakan kunjungi menu 
                        <a href="{{ route('guru.riwayat') }}" class="font-semibold text-emerald-600 underline">Riwayat Jurnal</a> 
                        untuk melihat catatan Anda.
                    </p>

                </div>

            </div>

        @endif


        {{-- FORM LOGBOOK --}}
        @if(!$hasSubmittedJournal)

            <form
                x-cloak
                x-show="hasCheckedIn"
                action="{{ route('guru.jurnal.store') }}"
                method="POST"
                enctype="multipart/form-data"
                class="mt-4 space-y-6"
            >

                @csrf

                {{-- DATA JURNAL --}}
                <div class="rounded-2xl bg-white p-5 shadow-md sm:p-6">

                    <div class="border-b border-slate-100 pb-4 mb-5">
                        <h3 class="text-base font-bold text-slate-800">
                            Data Kelas & Mata Pelajaran
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Data guru dan mata pelajaran otomatis diselaraskan dengan sesi login dan jadwal mengajar Anda.
                        </p>
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">

                        {{-- NAMA GURU (AUTO-FILLED) --}}
                        <label class="block">
                            <span class="text-sm font-semibold text-slate-700">
                                Nama Guru Pengajar
                            </span>
                            <input
                                type="text"
                                value="{{ $user->name }}"
                                readonly
                                class="mt-2 w-full cursor-not-allowed rounded-lg border border-slate-200 bg-slate-100 px-4 py-3 text-sm font-semibold text-slate-700 outline-none"
                            >
                        </label>

                        {{-- NIP GURU (AUTO-FILLED) --}}
                        <label class="block">
                            <span class="text-sm font-semibold text-slate-700">
                                NIP / Username
                            </span>
                            <input
                                type="text"
                                value="{{ $user->nip ?? $user->username ?? '-' }}"
                                readonly
                                class="mt-2 w-full cursor-not-allowed rounded-lg border border-slate-200 bg-slate-100 px-4 py-3 text-sm font-semibold text-slate-700 outline-none"
                            >
                        </label>

                        {{-- MAPEL (AUTO-FILLED DARI JADWAL HARI INI) --}}
                        <label class="block">
                            <span class="text-sm font-semibold text-slate-700">
                                Mata Pelajaran
                            </span>
                            <select
                                name="id_mapel"
                                x-model="selectedMapel"
                                required
                                class="mt-2 w-full rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100"
                            >
                                <option value="">
                                    Pilih Mata Pelajaran
                                </option>
                                @foreach($mapels as $mapel)
                                    <option value="{{ $mapel->id }}">
                                        {{ $mapel->nama_mapel ?? $mapel->nama ?? $mapel->kode }}
                                    </option>
                                @endforeach
                            </select>
                        </label>

                        {{-- KELAS (AUTO-FILLED DARI JADWAL HARI INI) --}}
                        <label class="block">
                            <span class="text-sm font-semibold text-slate-700">
                                Kelas
                            </span>
                            <select
                                name="id_kelas"
                                x-model="selectedKelas"
                                required
                                class="mt-2 w-full rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100"
                            >
                                <option value="">
                                    Pilih Kelas
                                </option>
                                @foreach($kelases as $kelas)
                                    <option value="{{ $kelas->id_kelas }}">
                                        {{ $kelas->nama_kelas }}
                                    </option>
                                @endforeach
                            </select>
                        </label>

                        {{-- JAM KE (AUTO-FILLED DARI JADWAL HARI INI) --}}
                        <label class="block">
                            <span class="text-sm font-semibold text-slate-700">
                                Jam Pelajaran Ke-
                            </span>
                            <input
                                type="number"
                                name="jam_ke"
                                x-model="selectedJamKe"
                                min="1"
                                max="13"
                                required
                                placeholder="Contoh: 1"
                                class="mt-2 w-full rounded-lg border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100"
                            >
                        </label>

                        {{-- TUGAS --}}
                        <label class="block">
                            <span class="text-sm font-semibold text-slate-700">
                                Ada Tugas untuk Siswa?
                            </span>
                            <select
                                name="ada_tugas"
                                required
                                class="mt-2 w-full rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100"
                            >
                                <option value="Tidak">
                                    Tidak Ada Tugas
                                </option>
                                <option value="Ya">
                                    Ya, Ada Tugas
                                </option>
                            </select>
                        </label>

                        {{-- MATERI --}}
                        <label class="block sm:col-span-2">
                            <span class="text-sm font-semibold text-slate-700">
                                Materi / Pokok Pembahasan
                            </span>
                            <input
                                type="text"
                                name="materi"
                                required
                                placeholder="Contoh: Pengenalan struktur data array dan penerapannya"
                                class="mt-2 w-full rounded-lg border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100"
                            >
                        </label>

                    </div>

                </div>


                {{-- ================================================= --}}
                {{-- LAMPIRAN BUKTI HADIR DI KELAS --}}
                {{-- ================================================= --}}

                <div class="rounded-2xl bg-white p-5 shadow-md sm:p-6">
                    <div class="border-b border-slate-100 pb-4">
                        <h3 class="text-base font-bold text-slate-800">Lampiran Bukti Hadir di Kelas</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Unggah foto atau dokumen sebagai bukti bahwa Anda benar-benar hadir di kelas saat mengajar.</p>
                    </div>
                    <div class="mt-4">
                        <label class="block">
                            <span class="text-sm font-semibold text-slate-700">Foto / Dokumen Lampiran (Opsional)</span>
                            <input
                                type="file"
                                name="lampiran"
                                accept="image/*,application/pdf"
                                class="mt-2 w-full text-sm text-slate-500 file:mr-4 file:rounded-lg file:border-0 file:bg-emerald-50 file:px-4 file:py-2.5 file:text-sm file:font-semibold file:text-emerald-700 hover:file:bg-emerald-100"
                            >
                            <p class="mt-1 text-xs text-slate-400">Format: JPG, PNG, WebP, PDF. Maksimal 5 MB.</p>
                        </label>
                    </div>
                </div>


                {{-- ================================================= --}}
                {{-- ABSENSI SISWA — TOMBOL BUKA POPUP --}}
                {{-- ================================================= --}}

                <div
                    x-data="{ absensiOpen: false }"
                    class="rounded-2xl bg-white p-5 shadow-md sm:p-6"
                >
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div>
                            <h3 class="text-base font-bold text-slate-800">Presensi Kehadiran Siswa</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Isi status kehadiran setiap siswa untuk sesi ini.</p>
                        </div>
                        <button
                            type="button"
                            @click="absensiOpen = true"
                            class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700"
                        >
                            <i class="bi bi-people-fill"></i>
                            Isi Absensi Siswa
                        </button>
                    </div>

                    <p class="mt-3 text-xs text-slate-400">
                        <i class="bi bi-info-circle mr-1"></i>
                        Klik tombol "Isi Absensi Siswa" untuk mengisi daftar hadir. Data yang sudah diisi akan tersimpan saat form dikirim.
                    </p>

                    {{-- HIDDEN inputs absensi siswa (agar ikut tersubmit bersama form) --}}
                    <div id="absensi-hidden-inputs" class="hidden">
                        @forelse($siswas as $siswa)
                            <input type="hidden" name="absensi[{{ $siswa->id }}]" value="Hadir" id="absensi-input-{{ $siswa->id }}">
                        @empty
                        @endforelse
                    </div>

                    {{-- POPUP MODAL ABSENSI SISWA --}}
                    <template x-teleport="body">
                        <div
                            x-show="absensiOpen"
                            x-cloak
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-sm"
                            @click.self="absensiOpen = false"
                        >
                            <div
                                x-show="absensiOpen"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                @click.stop
                                class="flex w-full max-w-2xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl"
                                style="max-height: 85vh;"
                            >
                                {{-- Header Modal --}}
                                <div class="flex shrink-0 items-center justify-between border-b border-slate-200 px-5 py-4">
                                    <div>
                                        <h3 class="text-base font-bold text-slate-900">Daftar Hadir Siswa</h3>
                                        <p class="text-xs text-slate-400 mt-0.5">Scroll untuk melihat semua siswa. Pilih status untuk setiap siswa.</p>
                                    </div>
                                    <button
                                        type="button"
                                        @click="absensiOpen = false"
                                        class="flex h-8 w-8 items-center justify-center rounded-full text-slate-500 transition hover:bg-slate-100"
                                        aria-label="Tutup"
                                    >
                                        <i class="bi bi-x-lg text-base"></i>
                                    </button>
                                </div>

                                {{-- Body Scrollable --}}
                                <div class="flex-1 overflow-y-auto p-4 min-h-0" style="max-height: calc(85vh - 130px);">
                                    <div class="space-y-2">
                                        @forelse($siswas as $siswa)
                                            <div
                                                x-show="selectedKelas == '{{ $siswa->kelas_id }}'"
                                                class="flex flex-col gap-3 rounded-xl border border-slate-100 bg-slate-50/50 p-4 sm:flex-row sm:items-center sm:justify-between"
                                            >
                                                <div>
                                                    <p class="text-sm font-bold text-slate-800">{{ $siswa->nama }}</p>
                                                    <p class="text-xs text-slate-400">NIS: {{ $siswa->nis ?? '-' }} | {{ $siswa->jenis_kelamin }}</p>
                                                </div>

                                                <div class="flex flex-wrap items-center gap-2">
                                                    @foreach(['Hadir' => 'emerald', 'Sakit' => 'amber', 'Izin' => 'blue', 'Alpa' => 'rose'] as $statusSiswa => $color)
                                                        <label
                                                            class="flex cursor-pointer items-center gap-1.5 rounded-lg border px-3 py-1.5 text-xs font-semibold transition"
                                                            :class="$refs['ab{{ $siswa->id }}']?.value === '{{ $statusSiswa }}'
                                                                ? 'border-{{ $color }}-400 bg-{{ $color }}-50 text-{{ $color }}-700'
                                                                : 'border-slate-200 bg-white text-slate-600 hover:border-slate-300'"
                                                        >
                                                            <input
                                                                type="radio"
                                                                name="absensi_popup[{{ $siswa->id }}]"
                                                                value="{{ $statusSiswa }}"
                                                                class="sr-only"
                                                                {{ $statusSiswa === 'Hadir' ? 'checked' : '' }}
                                                                @change="
                                                                    document.getElementById('absensi-input-{{ $siswa->id }}').value = '{{ $statusSiswa }}';
                                                                "
                                                            >
                                                            {{ $statusSiswa }}
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @empty
                                            <div class="p-6 text-center text-sm text-slate-500">
                                                Belum ada data siswa terdaftar untuk kelas ini.
                                            </div>
                                        @endforelse
                                    </div>
                                </div>

                                {{-- Footer --}}
                                <div class="shrink-0 border-t border-slate-200 px-5 py-4">
                                    <button
                                        type="button"
                                        @click="absensiOpen = false"
                                        class="w-full rounded-xl bg-emerald-600 py-3 text-sm font-bold text-white transition hover:bg-emerald-700"
                                    >
                                        <i class="bi bi-check2-all mr-1"></i>
                                        Simpan & Tutup Absensi
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>

                </div>


                {{-- CATATAN --}}
                <div class="rounded-2xl bg-white p-5 shadow-md sm:p-6">

                    <label
                        for="catatan-khusus"
                        class="block"
                    >

                        <span class="text-sm font-semibold text-slate-700">
                            Catatan Khusus / Hambatan Kelas (Opsional)
                        </span>

                        <textarea
                            id="catatan-khusus"
                            name="catatan"
                            rows="4"
                            placeholder="Tulis catatan atau hambatan selama pembelajaran berlangsung..."
                            class="mt-2 w-full resize-none rounded-lg border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100"
                        ></textarea>

                    </label>

                </div>


                {{-- SUBMIT --}}
                <button
                    type="submit"
                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-3.5 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-700"
                >

                    <i class="bi bi-send-fill"></i>

                    Kirim Logbook & Absensi Siswa

                </button>

            </form>

        @endif

    </section>

</div>

@endsection
