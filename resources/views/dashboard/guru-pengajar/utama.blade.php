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
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: rgba(16, 185, 129, 0.05); }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(16, 185, 129, 0.3); border-radius: 9999px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(16, 185, 129, 0.5); }
</style>

<div
    x-data="{
        hasCheckedIn: {{ $hasCheckedIn ? 'true' : 'false' }},
        hasSubmittedJournal: {{ $hasSubmittedJournal ? 'true' : 'false' }},
        showForm: false,

        selectedTeacherId: '{{ $user->id }}',
        nipGuru: '{{ $user->nip ?? $user->username ?? "" }}',
        statusKehadiran: 'Hadir',
        statusAbsen: 'Hadir',
        selectedKelas: '{{ $activeJadwal->id_kelas ?? "" }}',
        selectedMapel: '{{ $activeJadwal->id_mapel ?? "" }}',
        selectedJamKe: '{{ $activeJadwal->jam_mulai ?? 1 }}',
        selectedJamSelesai: '{{ $activeJadwal->jam_selesai ?? ($activeJadwal->jam_mulai ?? 1) }}',

        teachers: @js($teachers ?? []),
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

        updateNip() {
            let found = this.teachers.find(
                t => t.id == this.selectedTeacherId
            );

            this.nipGuru = found
                ? (found.nip ?? found.username ?? '-')
                : '';
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

        <div class="flex items-center gap-2">
            @if(!empty($isWaka))
                <span class="rounded-full bg-purple-100 px-3 py-1.5 text-xs font-semibold text-purple-700 border border-purple-200">
                    <i class="bi bi-shield-check me-1"></i> Waka Kesiswaan
                </span>
            @endif
            @if(!empty($isPiketActive))
                <span class="rounded-full bg-amber-100 px-3 py-1.5 text-xs font-semibold text-amber-800 border border-amber-300">
                    <i class="bi bi-clock-history me-1"></i> Bertugas Piket Hari Ini
                </span>
            @endif
            <span class="w-fit rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700">
                SMKN 1 Boyolangu
            </span>
        </div>
    </section>

    {{-- BANNER KHUSUS UNTUK WAKA JIKA ADA PENDING DISPENSASI --}}
    @if(!empty($isWaka) && count($pendingDispensasis) > 0)
        <section class="mt-6 rounded-2xl bg-purple-900 p-5 text-white shadow-lg sm:p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-start gap-3">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-purple-800 text-2xl text-purple-200">
                        <i class="bi bi-bell-fill"></i>
                    </span>
                    <div>
                        <span class="inline-block rounded-full bg-purple-700 px-2.5 py-0.5 text-xs font-semibold text-purple-100">Notifikasi Approval Waka</span>
                        <h2 class="mt-1 text-xl font-bold">Ada {{ count($pendingDispensasis) }} Pengajuan Dispensasi Menunggu Persetujuan</h2>
                        <p class="mt-1 text-sm text-purple-200">Guru Piket telah mengirimkan pengajuan dispensasi siswa yang membutuhkan konfirmasi Anda.</p>
                    </div>
                </div>
            </div>

            <div class="mt-5 space-y-3">
                @foreach($pendingDispensasis as $item)
                    <div class="flex flex-col gap-3 rounded-xl bg-purple-800/80 p-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="font-bold text-white text-base">{{ $item->nama }} <span class="text-xs font-normal text-purple-200">({{ $item->jenis_dispensasi }})</span></p>
                            <p class="text-xs text-purple-200 mt-0.5">Alasan: {{ $item->alasan }} | Tanggal: {{ $item->tanggal ? $item->tanggal->format('d/m/Y') : '-' }}</p>
                            <p class="text-xs text-purple-300 mt-1">Dibuat oleh: {{ $item->pembuat?->name ?? 'Guru Piket' }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('dispensasi.approval', ['token' => $item->token_approval ?? $item->id]) }}" class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-500 px-4 py-2 text-xs font-semibold text-white hover:bg-emerald-600 transition shadow">
                                <i class="bi bi-check-circle"></i> Tinjau & Setujui
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- BANNER KHUSUS GURU PIKET (MODE PIKET AKTIF) --}}
    @if(!empty($isPiketActive))
        <section class="mt-6 rounded-2xl border-2 border-amber-300 bg-amber-50/90 p-5 shadow-sm sm:p-6" aria-labelledby="status-piket-guru">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div class="flex items-start gap-3">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-amber-200 text-2xl text-amber-800">
                        <i class="bi bi-shield-fill-exclamation" aria-hidden="true"></i>
                    </span>
                    <div>
                        <span class="rounded-full bg-amber-200 px-3 py-1 text-xs font-bold text-amber-900">MODE PIKET AKTIF</span>
                        <h2 id="status-piket-guru" class="mt-1 text-xl font-bold text-amber-950">Anda Terjadwal Piket Hari Ini</h2>
                        <p class="mt-1 text-sm text-amber-900 leading-relaxed">
                            Fitur Logbook Mengajar Anda <strong>dikunci sementara</strong> selama sesi piket aktif. 
                            Anda tidak perlu mengisi logbook karena sesi Anda dialihkan ke piket.
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-4 flex flex-wrap gap-3">
                <a href="{{ route('piket.dispensasi.form') }}" class="inline-flex items-center gap-2 rounded-xl bg-amber-600 px-5 py-2.5 text-sm font-bold text-white shadow hover:bg-amber-700 transition">
                    <i class="bi bi-file-earmark-plus-fill"></i> Input Form Dispensasi Siswa
                </a>
                <a href="{{ route('piket.kehadiran') }}" class="inline-flex items-center gap-2 rounded-xl bg-white px-5 py-2.5 text-sm font-bold text-amber-900 border border-amber-300 shadow-sm hover:bg-amber-100 transition">
                    <i class="bi bi-person-lines-fill"></i> Rekap Kehadiran Piket
                </a>
            </div>
        </section>
    @endif

    {{-- ========================================================= --}}
    {{-- SECTION 1 : KEHADIRAN GURU & JADWAL MENGAJAR HARI INI --}}
    {{-- ========================================================= --}}
    <section
        id="section-kehadiran-guru"
        class="mt-6 rounded-2xl bg-white p-5 shadow-md sm:p-6"
        aria-labelledby="lapor-kehadiran-guru"
    >
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div class="flex items-start gap-3">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-xl text-emerald-700">
                    <i class="bi bi-calendar-check-fill"></i>
                </span>
                <div>
                    <p class="text-sm font-semibold text-emerald-700">
                        Presensi &amp; Jadwal Mengajar Hari Ini
                    </p>
                    <h2
                        id="lapor-kehadiran-guru"
                        class="mt-1 text-xl font-bold text-slate-900"
                    >
                        Jadwal Mengajar Hari Ini ({{ $hariIni }})
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Lakukan presensi terlebih dahulu sebelum memulai kegiatan belajar mengajar di kelas.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <span
                    x-show="!hasCheckedIn"
                    class="w-fit rounded-full bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700"
                >
                    Belum presensi hari ini
                </span>

                <span
                    x-cloak
                    x-show="hasCheckedIn"
                    class="w-fit rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700"
                >
                    Presensi tercatat ✓
                </span>
            </div>
        </div>

        {{-- DAFTAR JADWAL MENGAJAR HARI INI --}}
        <div class="mt-5 divide-y divide-slate-100 rounded-xl border border-slate-100 bg-slate-50/50 overflow-hidden">
            <div class="bg-slate-100/80 px-4 py-2.5 flex items-center justify-between">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-600">
                    Sesi Mengajar Hari {{ $hariIni }} ({{ $jadwals->count() }} Sesi Terdaftar)
                </p>
                <span class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-800 border border-emerald-200 shadow-xs">
                    <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Waktu Server:</span>
                    <strong x-text="liveClock + ' WIB'" class="font-mono text-emerald-950 font-bold"></strong>
                </span>
            </div>

            @forelse($jadwals ?? [] as $index => $jadwal)
                @php
                    $isFilled = $jadwal->is_filled;
                    $statusWaktu = $jadwal->status_waktu;
                @endphp

                <div class="flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-between transition hover:bg-slate-100/40">
                    <div class="flex flex-wrap items-center gap-3 sm:gap-4">
                        <div class="flex flex-col items-start">
                            <span class="rounded-lg bg-emerald-100/80 px-2.5 py-1 text-xs font-bold text-emerald-800">
                                Jam ke-{{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}
                            </span>
                            <span class="mt-1 text-[11px] font-medium text-slate-500">
                                {{ $jadwal->waktu_mulai }} - {{ $jadwal->waktu_selesai }}
                            </span>
                        </div>

                        <div>
                            <h3 class="text-sm font-bold text-slate-900">
                                {{ $jadwal->kelas->nama_kelas ?? 'Kelas' }}
                                <span class="text-slate-400 font-normal">|</span>
                                {{ $jadwal->mapel->nama_mapel ?? 'Mata Pelajaran' }}
                            </h3>

                            <div class="mt-1 flex items-center gap-2">
                                @if($isFilled)
                                    <span class="inline-flex items-center gap-1 rounded-md bg-emerald-50 px-2 py-0.5 text-[11px] font-bold text-emerald-700 border border-emerald-200">
                                        <i class="bi bi-check-circle-fill"></i> Jurnal Terisi
                                    </span>
                                @elseif($statusWaktu === 'lewat')
                                    <span class="inline-flex items-center gap-1 rounded-md bg-rose-50 px-2 py-0.5 text-[11px] font-semibold text-rose-700 border border-rose-200">
                                        <i class="bi bi-clock-history"></i> Lewat Jam Mengajar
                                    </span>
                                @elseif($statusWaktu === 'berlangsung')
                                    <span class="inline-flex items-center gap-1 rounded-md bg-emerald-500 px-2 py-0.5 text-[11px] font-bold text-white shadow-xs">
                                        <span class="h-1.5 w-1.5 rounded-full bg-white animate-ping"></span> Sedang Berlangsung
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-md bg-sky-50 px-2 py-0.5 text-[11px] font-semibold text-sky-700 border border-sky-200">
                                        <i class="bi bi-hourglass-split"></i> Akan Datang
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        @if($isFilled)
                            <a
                                href="{{ route('guru.riwayat') }}"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 transition hover:bg-slate-50"
                            >
                                <i class="bi bi-eye"></i>
                                <span>Lihat Catatan</span>
                            </a>
                        @elseif($statusWaktu === 'lewat')
                            <span class="rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-400 cursor-not-allowed" title="Batas waktu jam mengajar sesi ini telah terlewat ({{ $jadwal->waktu_mulai }} - {{ $jadwal->waktu_selesai }} WIB)">
                                <i class="bi bi-lock-fill mr-1"></i>Tenggat Lewat
                            </span>
                        @elseif($statusWaktu === 'berlangsung')
                            <button
                                type="button"
                                @click="pilihJadwal('{{ $jadwal->id_kelas }}', '{{ $jadwal->id_mapel }}', '{{ $jadwal->jam_mulai }}', '{{ $jadwal->jam_selesai }}')"
                                class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3.5 py-1.5 text-xs font-bold text-white shadow-sm transition hover:bg-emerald-700 animate-bounce"
                            >
                                <i class="bi bi-pencil-square"></i>
                                <span>Isi Jurnal Sekarang</span>
                            </button>
                        @else
                            <button
                                type="button"
                                @click="pilihJadwal('{{ $jadwal->id_kelas }}', '{{ $jadwal->id_mapel }}', '{{ $jadwal->jam_mulai }}', '{{ $jadwal->jam_selesai }}')"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-300 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-100"
                            >
                                <i class="bi bi-calendar-plus"></i>
                                <span>Pilih Sesi Ini</span>
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-sm text-slate-400">
                    <i class="bi bi-calendar-x text-2xl mb-1 block"></i>
                    Tidak ada jadwal mengajar terdaftar untuk hari {{ $hariIni }}.
                </div>
            @endforelse
        </div>

        {{-- TOMBOL ABSEN --}}
        <div class="mt-5 flex flex-col gap-3 rounded-xl bg-slate-50 p-4 sm:flex-row sm:items-center sm:justify-between border border-slate-100">
            <p class="flex items-start gap-2 text-xs leading-relaxed text-slate-500">
                <i class="bi bi-info-circle-fill mt-0.5 text-emerald-600"></i>
                <span>
                    Data presensi otomatis diteruskan ke sistem monitoring Guru Piket dan Waka Kurikulum.
                </span>
            </p>

            <button
                type="button"
                @click="showForm = true"
                x-show="!hasCheckedIn"
                class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700"
            >
                <i class="bi bi-box-arrow-in-right"></i>
                Lapor Presensi / Absen Masuk
            </button>

            <span
                x-cloak
                x-show="hasCheckedIn"
                class="inline-flex shrink-0 items-center gap-2 rounded-lg bg-emerald-100 px-4 py-2 text-xs font-bold text-emerald-800"
            >
                <i class="bi bi-check-circle-fill text-emerald-600"></i>
                Presensi Hari Ini: {{ $attendance->status ?? 'Tercatat' }}
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
                        alert('Kamera tidak dapat diakses. Pastikan izin kamera telah diberikan pada browser Anda.');
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
                            Form Kehadiran Guru
                        </p>
                        <h3 class="mt-1 text-lg font-bold text-slate-900">
                            Lapor Presensi Kehadiran
                        </h3>
                        <p class="mt-1 text-sm text-slate-500">
                            Pilih status kehadiran Anda hari ini dan sertakan bukti yang diperlukan.
                        </p>
                    </div>
                </div>

                <span class="w-fit rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700">
                    Hari ini ({{ $hariIni }})
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
                    <span class="text-sm font-semibold text-slate-700">Pilih Status Kehadiran</span>
                    <div class="mt-3 grid gap-4 sm:grid-cols-2">
                        <label class="flex cursor-pointer items-center gap-3 rounded-xl border-2 p-4 transition"
                               :class="statusAbsen === 'Hadir' ? 'border-emerald-500 bg-emerald-50' : 'border-slate-200 hover:border-slate-300'">
                            <input type="radio" name="status_kehadiran_guru" value="Hadir"
                                   x-model="statusAbsen" class="sr-only">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full"
                                  :class="statusAbsen === 'Hadir' ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-100 text-slate-400'">
                                <i class="bi bi-check-circle-fill text-lg"></i>
                            </span>
                            <div>
                                <p class="text-sm font-bold" :class="statusAbsen === 'Hadir' ? 'text-emerald-700' : 'text-slate-700'">Hadir</p>
                                <p class="text-xs text-slate-400">Saya hadir mengajar di sekolah hari ini</p>
                            </div>
                        </label>

                        <label class="flex cursor-pointer items-center gap-3 rounded-xl border-2 p-4 transition"
                               :class="statusAbsen === 'Tidak Hadir' ? 'border-rose-500 bg-rose-50' : 'border-slate-200 hover:border-slate-300'">
                            <input type="radio" name="status_kehadiran_guru" value="Tidak Hadir"
                                   x-model="statusAbsen" class="sr-only">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full"
                                  :class="statusAbsen === 'Tidak Hadir' ? 'bg-rose-100 text-rose-600' : 'bg-slate-100 text-slate-400'">
                                <i class="bi bi-x-circle-fill text-lg"></i>
                            </span>
                            <div>
                                <p class="text-sm font-bold" :class="statusAbsen === 'Tidak Hadir' ? 'text-rose-700' : 'text-slate-700'">Tidak Hadir</p>
                                <p class="text-xs text-slate-400">Saya berhalangan hadir (Sakit / Izin / Tugas Luar)</p>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- JIKA TIDAK HADIR: ALASAN + UNGGAH SURAT IZIN RESMI --}}
                <div x-cloak x-show="statusAbsen === 'Tidak Hadir'" class="sm:col-span-2 space-y-4">
                    <label for="alasan-kehadiran" class="block">
                        <span class="text-sm font-semibold text-slate-700">
                            Alasan Tidak Hadir <span class="text-rose-500">*</span>
                        </span>
                        <textarea
                            id="alasan-kehadiran"
                            name="reason"
                            rows="3"
                            placeholder="Tuliskan alasan ketidakhadiran Anda secara detail (misal: Sakit, Izin keperluan dinas luar, dll)..."
                            class="mt-2 w-full rounded-lg border border-slate-200 p-4 text-sm text-slate-700 focus:border-rose-400 focus:ring-2 focus:ring-rose-100"
                        ></textarea>
                    </label>

                    <label class="block">
                        <span class="text-sm font-semibold text-slate-700">
                            Unggah Surat Izin Resmi / Bukti Tidak Hadir (Surat Dokter / Tugas Luar / dsb)
                        </span>
                        <input
                            type="file"
                            name="proof_file"
                            accept="image/*,application/pdf"
                            class="mt-2 w-full text-sm text-slate-500 file:mr-4 file:rounded-lg file:border-0 file:bg-rose-50 file:px-4 file:py-2.5 file:text-sm file:font-semibold file:text-rose-700 hover:file:bg-rose-100"
                        >
                        <p class="mt-1 text-xs text-slate-400">Format file: JPG, PNG, PDF. Maksimal 5 MB.</p>
                    </label>
                </div>

                {{-- JIKA HADIR: FOTO LIVE KAMERA --}}
                <div x-cloak x-show="statusAbsen === 'Hadir'" class="sm:col-span-2">
                    <span class="block text-sm font-semibold text-slate-700">
                        Foto Kehadiran (Live Kamera)
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
                    Catat materi yang diajarkan dan rekap presensi kehadiran siswa hari ini.
                </p>
            </div>
        </div>

        {{-- PERINGATAN GURU PIKET (MODE PIKET AKTIF) --}}
        @if(!empty($isPiketActive))
            <div class="mt-4 rounded-xl border border-amber-300 bg-amber-50 p-5 text-amber-900" role="alert">
                <div class="flex items-start gap-3">
                    <i class="bi bi-lock-fill mt-0.5 text-2xl text-amber-700" aria-hidden="true"></i>
                    <div>
                        <p class="text-base font-bold">Logbook Mengajar Terkunci (Mode Piket Aktif)</p>
                        <p class="mt-1 text-xs leading-relaxed text-amber-800">
                            Karena Anda bertugas sebagai Guru Piket hari ini, sesi mengajar Anda digantikan dengan tugas piket. 
                            Tombol dan form pengisian Logbook dikunci sampai masa tugas piket selesai.
                        </p>
                    </div>
                </div>
            </div>
        @elseif(!$hasCheckedIn)
            {{-- PERINGATAN WAJIB ABSEN TERLEBIH DAHULU --}}
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
                            Anda <strong>WAJIB melakukan absen/presensi masuk</strong> terlebih dahulu hari ini sebelum dapat mengisi dan menyimpan Jurnal Pembelajaran.
                        </p>
                        <div class="mt-4">
                            <button
                                type="button"
                                @click="showForm = true; document.getElementById('section-kehadiran-guru')?.scrollIntoView({behavior: 'smooth'})"
                                class="inline-flex items-center gap-2 rounded-xl bg-amber-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-amber-700"
                            >
                                <i class="bi bi-box-arrow-in-right"></i>
                                Isi Presensi Sekarang
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- FORM LOGBOOK --}}
        <form
            x-cloak
            x-show="hasCheckedIn && !{{ !empty($isPiketActive) ? 'true' : 'false' }}"
            action="{{ route('guru.jurnal.store') }}"
            method="POST"
            enctype="multipart/form-data"
            class="mt-4 space-y-6"
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

            {{-- DATA JURNAL --}}
            <div class="rounded-2xl bg-white p-5 shadow-md sm:p-6">
                <div class="border-b border-slate-100 pb-4 mb-5">
                    <h3 class="text-base font-bold text-slate-800">
                        Data Kelas & Mata Pelajaran
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Pilih kelas, mata pelajaran, dan jam ke sesuai sesi pembelajaran yang sedang berlangsung.
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

                    {{-- MAPEL --}}
                    <label class="block">
                        <span class="text-sm font-semibold text-slate-700">
                            Mata Pelajaran <span class="text-rose-500">*</span>
                        </span>
                        <select
                            name="id_mapel"
                            x-model="selectedMapel"
                            required
                            class="mt-2 w-full rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100"
                        >
                            <option value="">
                                -- Pilih Mata Pelajaran --
                            </option>
                            @foreach($mapels as $mapel)
                                <option value="{{ $mapel->id }}">
                                    {{ $mapel->nama_mapel ?? $mapel->nama ?? $mapel->kode }}
                                </option>
                            @endforeach
                        </select>
                    </label>

                    {{-- KELAS --}}
                    <label class="block">
                        <span class="text-sm font-semibold text-slate-700">
                            Kelas <span class="text-rose-500">*</span>
                        </span>
                        <select
                            name="id_kelas"
                            x-model="selectedKelas"
                            required
                            class="mt-2 w-full rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100"
                        >
                            <option value="">
                                -- Pilih Kelas --
                            </option>
                            @foreach($kelases as $kelas)
                                <option value="{{ $kelas->id_kelas }}">
                                    {{ $kelas->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                    </label>

                    {{-- JAM PELAJARAN (RENTANG JAM MULAI S.D. SELESAI) --}}
                    <div class="grid grid-cols-2 gap-3 sm:col-span-1">
                        <label class="block">
                            <span class="text-sm font-semibold text-slate-700">
                                Jam Ke- <span class="text-rose-500">*</span>
                            </span>
                            <input
                                type="number"
                                name="jam_ke"
                                x-model="selectedJamKe"
                                min="1"
                                max="13"
                                required
                                placeholder="Mulai"
                                class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-3 text-sm text-slate-700 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100"
                            >
                        </label>

                        <label class="block">
                            <span class="text-sm font-semibold text-slate-700">
                                Sampai Jam Ke- <span class="text-rose-500">*</span>
                            </span>
                            <input
                                type="number"
                                name="jam_selesai"
                                x-model="selectedJamSelesai"
                                :min="selectedJamKe"
                                max="13"
                                required
                                placeholder="Selesai"
                                class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-3 text-sm text-slate-700 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100"
                            >
                        </label>
                    </div>

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
                            Materi / Pokok Pembahasan <span class="text-rose-500">*</span>
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
            {{-- LAMPIRAN BUKTI HADIR DI KELAS (FOTO LIVE LANGSUNG) --}}
            {{-- ================================================= --}}
            <div id="section-lampiran-logbook" class="rounded-2xl bg-white p-5 shadow-md sm:p-6">
                <div class="border-b border-slate-100 pb-4">
                    <h3 class="text-base font-bold text-slate-800">
                        Lampiran Bukti Hadir di Kelas (Foto Live) <span class="text-rose-500">*</span>
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Ambil foto live secara langsung di dalam kelas sebagai bukti otentik Anda hadir dan mengajar.</p>
                </div>

                <div class="mt-4">
                    <input
                        type="file"
                        name="lampiran"
                        accept="image/*"
                        x-ref="lampiranInput"
                        class="hidden"
                    >

                    {{-- Preview Foto Tersimpan --}}
                    <div x-show="capturedPhoto" class="mt-3">
                        <div class="relative inline-block">
                            <img :src="capturedPhoto" alt="Foto Bukti Mengajar" class="h-56 w-full rounded-xl object-cover shadow-md sm:w-auto sm:max-w-md">
                            <span class="absolute left-2 top-2 rounded-full bg-emerald-600/90 px-2.5 py-1 text-xs font-bold text-white shadow">
                                <i class="bi bi-camera-fill mr-1"></i> Foto Live Tersimpan ✓
                            </span>
                        </div>
                        <div class="mt-2">
                            <button type="button" @click="retakePhoto()" class="text-xs font-semibold text-emerald-600 hover:underline inline-flex items-center gap-1">
                                <i class="bi bi-arrow-repeat"></i> Ambil Ulang Foto Live
                            </button>
                        </div>
                    </div>

                    {{-- Canvas tersembunyi untuk Capture --}}
                    <canvas x-ref="cameraCanvasLogbook" class="hidden"></canvas>

                    {{-- Video Kamera Live --}}
                    <div x-show="cameraActive && !capturedPhoto" class="mt-3">
                        <div class="relative overflow-hidden rounded-xl bg-black shadow-md" style="max-width: 420px;">
                            <video x-ref="cameraVideoLogbook" autoplay playsinline muted class="w-full rounded-xl"></video>
                            <div class="absolute inset-x-0 bottom-0 flex justify-center pb-4">
                                <button type="button" @click="capturePhoto()"
                                        class="flex h-14 w-14 items-center justify-center rounded-full bg-white shadow-lg transition hover:bg-emerald-50"
                                        title="Jepret Foto Live">
                                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-600">
                                        <i class="bi bi-camera-fill text-white text-lg"></i>
                                    </span>
                                </button>
                            </div>
                        </div>
                        <button type="button" @click="stopCamera()" class="mt-2 text-xs font-medium text-slate-400 hover:text-slate-600">
                            <i class="bi bi-x-circle"></i> Batalkan kamera
                        </button>
                    </div>

                    {{-- Tombol Buka Kamera Live --}}
                    <div x-show="!cameraActive && !capturedPhoto" class="mt-3">
                        <button type="button" @click="startCamera()"
                                class="inline-flex items-center gap-2 rounded-xl border-2 border-dashed border-emerald-400 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700 transition hover:border-emerald-600 hover:bg-emerald-100">
                            <i class="bi bi-camera-fill text-xl"></i>
                            Buka Kamera & Ambil Foto Live Kelas
                        </button>
                        <p class="mt-2 text-xs text-rose-500 font-medium">* Wajib mengambil foto live di dalam kelas untuk dapat mengirim jurnal.</p>
                    </div>
                </div>
            </div>

            {{-- ================================================= --}}
            {{-- PRESENSI KEHADIRAN SISWA --}}
            {{-- ================================================= --}}
            <div class="rounded-2xl bg-white p-5 shadow-md sm:p-6">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between border-b border-slate-100 pb-4 mb-4">
                    <div>
                        <h3 class="text-base font-bold text-slate-800">
                            Presensi Kehadiran Siswa
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Daftar siswa otomatis menyesuaikan kelas yang Anda pilih di atas. Default: Hadir (H).
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span> H = Hadir
                        </span>
                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">
                            <span class="h-2 w-2 rounded-full bg-amber-500"></span> S = Sakit
                        </span>
                        <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700">
                            <span class="h-2 w-2 rounded-full bg-blue-500"></span> I = Izin
                        </span>
                        <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-2.5 py-1 text-xs font-semibold text-rose-700">
                            <span class="h-2 w-2 rounded-full bg-rose-500"></span> A = Alpa
                        </span>
                    </div>
                </div>

                {{-- INFO KELAS TERPILIH --}}
                <div x-show="!selectedKelas" class="rounded-xl border border-dashed border-slate-200 bg-slate-50 p-6 text-center text-xs text-slate-500">
                    <i class="bi bi-info-circle text-lg text-slate-400 block mb-1"></i>
                    Silakan pilih kelas terlebih dahulu pada data form di atas untuk menampilkan daftar siswa.
                </div>

                {{-- CONTAINER SCROLLABLE MANDIRI UNTUK DAFTAR SISWA --}}
                <div
                    x-show="selectedKelas"
                    class="max-h-80 overflow-y-auto pr-1 space-y-2.5 custom-scrollbar border border-slate-100 rounded-xl p-3 bg-slate-50/60"
                >
                    @forelse($siswas as $siswa)
                        <div
                            x-show="selectedKelas == '{{ $siswa->kelas_id }}'"
                            class="flex flex-col gap-2 rounded-xl border border-slate-200/80 bg-white p-3.5 sm:flex-row sm:items-center sm:justify-between shadow-xs transition hover:border-emerald-300"
                        >
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-bold text-slate-800 truncate">
                                    {{ $siswa->nama }}
                                </p>
                                <p class="text-xs text-slate-400">
                                    NIS: {{ $siswa->nis ?? '-' }} | Gender: {{ $siswa->jenis_kelamin }}
                                </p>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                {{-- HADIR --}}
                                <label class="cursor-pointer">
                                    <input
                                        type="radio"
                                        name="absensi[{{ $siswa->id }}]"
                                        value="Hadir"
                                        checked
                                        class="peer sr-only"
                                    >
                                    <span class="flex h-8 min-w-9 items-center justify-center rounded-lg border text-xs font-bold transition peer-checked:border-emerald-600 peer-checked:bg-emerald-600 peer-checked:text-white border-slate-200 bg-white text-slate-600 hover:bg-slate-50">
                                        H
                                    </span>
                                </label>

                                {{-- SAKIT --}}
                                <label class="cursor-pointer">
                                    <input
                                        type="radio"
                                        name="absensi[{{ $siswa->id }}]"
                                        value="Sakit"
                                        class="peer sr-only"
                                    >
                                    <span class="flex h-8 min-w-9 items-center justify-center rounded-lg border text-xs font-bold transition peer-checked:border-amber-500 peer-checked:bg-amber-500 peer-checked:text-white border-slate-200 bg-white text-slate-600 hover:bg-slate-50">
                                        S
                                    </span>
                                </label>

                                {{-- IZIN --}}
                                <label class="cursor-pointer">
                                    <input
                                        type="radio"
                                        name="absensi[{{ $siswa->id }}]"
                                        value="Izin"
                                        class="peer sr-only"
                                    >
                                    <span class="flex h-8 min-w-9 items-center justify-center rounded-lg border text-xs font-bold transition peer-checked:border-blue-500 peer-checked:bg-blue-500 peer-checked:text-white border-slate-200 bg-white text-slate-600 hover:bg-slate-50">
                                        I
                                    </span>
                                </label>

                                {{-- ALPA --}}
                                <label class="cursor-pointer">
                                    <input
                                        type="radio"
                                        name="absensi[{{ $siswa->id }}]"
                                        value="Alpa"
                                        class="peer sr-only"
                                    >
                                    <span class="flex h-8 min-w-9 items-center justify-center rounded-lg border text-xs font-bold transition peer-checked:border-rose-500 peer-checked:bg-rose-500 peer-checked:text-white border-slate-200 bg-white text-slate-600 hover:bg-slate-50">
                                        A
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
                        placeholder="Tulis catatan penting atau kendala selama pembelajaran berlangsung..."
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
                Kirim Logbook &amp; Presensi Siswa
            </button>
        </form>
    </section>

</div>

@endsection
