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

        selectedTeacherId: '',
        nipGuru: '',
        statusKehadiran: 'Hadir',
        selectedKelas: '',

        teachers: @js($teachers ?? []),

        updateNip() {
            let found = this.teachers.find(
                t => t.id == this.selectedTeacherId
            );

            this.nipGuru = found
                ? (found.nip ?? found.username ?? '-')
                : '';
        }
    }"
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

    {{-- NOTIFIKASI ERROR --}}
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
                Kelola kehadiran dan logbook pembelajaran Anda hari ini.
            </p>
        </div>

        <span class="w-fit rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700">
            SMKN 1 Boyolangu
        </span>

    </section>


    {{-- ========================================================= --}}
    {{-- SECTION 1 : KEHADIRAN GURU --}}
    {{-- ========================================================= --}}

    <section
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
                        Kehadiran Guru
                    </p>

                    <h2
                        id="lapor-kehadiran-guru"
                        class="mt-1 text-xl font-bold text-slate-900"
                    >
                        Lapor Kehadiran Guru
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Lakukan absen masuk sebelum memulai pembelajaran.
                    </p>
                </div>

            </div>

            <span
                x-show="!hasCheckedIn"
                class="w-fit rounded-full bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700"
            >
                Belum absen
            </span>

            <span
                x-cloak
                x-show="hasCheckedIn"
                class="w-fit rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700"
            >
                Sudah absen
            </span>

        </div>


        {{-- JADWAL --}}
        <div class="mt-5 divide-y divide-slate-100 rounded-xl border border-slate-100 bg-slate-50/50">

            @forelse($jadwals ?? [] as $index => $jadwal)

                <div class="flex flex-col gap-2 p-4 sm:flex-row sm:items-center sm:justify-between">

                    <div class="flex items-center gap-4">

                        <span class="min-w-[70px] text-xs font-bold text-emerald-700">
                            Jam {{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}
                        </span>

                        <div>

                            <h3 class="text-sm font-bold text-slate-800">
                                {{ $jadwal->kelas->nama_kelas ?? $jadwal->kelas_nama ?? 'Kelas' }}
                                -
                                {{ $jadwal->mapel->nama_mapel ?? $jadwal->mapel_nama ?? 'Mata Pelajaran' }}
                            </h3>

                        </div>

                    </div>

                    <div>

                        @if($index === 0)

                            <span class="rounded-md bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                Jadwal aktif
                            </span>

                        @elseif($index === 1)

                            <span class="text-xs font-medium text-slate-400">
                                Berikutnya
                            </span>

                        @else

                            <span class="text-xs font-medium text-slate-400">
                                Preview
                            </span>

                        @endif

                    </div>

                </div>

            @empty

                <div class="p-6 text-center text-sm text-slate-500">
                    Tidak ada jadwal mengajar untuk hari ini.
                </div>

            @endforelse

        </div>


        {{-- TOMBOL ABSEN --}}
        <div class="mt-5 flex flex-col gap-3 rounded-xl bg-slate-50 p-4 sm:flex-row sm:items-center sm:justify-between">

            <p class="flex items-start gap-2 text-xs leading-relaxed text-slate-500">

                <i class="bi bi-info-circle-fill mt-0.5 text-emerald-600"></i>

                <span>
                    Data absensi masuk akan diteruskan ke monitoring Piket dan Admin.
                </span>

            </p>

            <button
                type="button"
                @click="showForm = true"
                x-show="!hasCheckedIn"
                class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg bg-emerald-600 px-4 py-3 text-sm font-medium text-white shadow-sm transition hover:bg-emerald-700"
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
                Absen masuk tercatat
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
                            Lapor Kehadiran
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            Lengkapi laporan kehadiran Anda sebelum mengisi logbook.
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


                {{-- STATUS --}}
                <label for="status-kehadiran" class="block">

                    <span class="text-sm font-semibold text-slate-700">
                        Status Kehadiran
                    </span>

                    <select
                        id="status-kehadiran"
                        name="status_kehadiran_guru"
                        x-model="statusKehadiran"
                        class="mt-2 w-full rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700"
                    >

                        <option value="Hadir">Hadir</option>
                        <option value="Sakit">Sakit</option>
                        <option value="Izin">Izin</option>
                        <option value="Tanpa Keterangan">
                            Tanpa Keterangan
                        </option>

                    </select>

                </label>


                {{-- ALASAN --}}
                <label for="alasan-ketidakhadiran" class="block">

                    <span class="text-sm font-semibold text-slate-700">
                        Alasan / Keterangan Tambahan
                    </span>

                    <input
                        id="alasan-ketidakhadiran"
                        type="text"
                        name="reason"
                        placeholder="Opsional (jika izin/sakit)"
                        class="mt-2 w-full rounded-lg border border-slate-200 px-4 py-3 text-sm text-slate-700"
                    >

                </label>


                {{-- BUKTI --}}
                <label
                    for="bukti-kehadiran"
                    class="flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-emerald-200 bg-emerald-50 px-5 py-8 text-center sm:col-span-2"
                >

                    <i class="bi bi-cloud-arrow-up-fill text-3xl text-emerald-700"></i>

                    <span class="mt-3 text-sm font-bold text-emerald-800">
                        Unggah Bukti Kehadiran (Opsional)
                    </span>

                    <span class="mt-1 text-xs text-emerald-700">
                        Tambahkan foto atau dokumen pendukung.
                    </span>

                    <input
                        id="bukti-kehadiran"
                        type="file"
                        name="proof_file"
                        accept="image/*,.pdf"
                        class="sr-only"
                    >

                </label>

            </div>


            <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">

                <button
                    type="button"
                    @click="showForm = false"
                    class="rounded-lg px-5 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-100"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-emerald-600 px-5 py-3 text-sm font-semibold text-white"
                >
                    <i class="bi bi-send-fill"></i>
                    Kirim Laporan Absen
                </button>

            </div>

        </form>

    </section>


    {{-- ========================================================= --}}
    {{-- SECTION 2 : LOGBOOK --}}
    {{-- ========================================================= --}}

    <section
        class="mt-8"
        aria-labelledby="isi-logbook"
    >

        <div class="flex items-center gap-3">

            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                <i class="bi bi-journal-text"></i>
            </span>

            <div>

                <h2
                    id="isi-logbook"
                    class="text-xl font-bold text-slate-900"
                >
                    Isi Logbook Mengajar
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Jurnal pembelajaran harian guru.
                </p>

            </div>

        </div>


        {{-- BELUM ABSEN --}}
        <div
            x-show="!hasCheckedIn"
            class="mt-4 rounded-xl border border-amber-200 bg-amber-50 p-4 text-amber-900"
        >

            <div class="flex items-start gap-3">

                <i class="bi bi-lock-fill mt-0.5 text-amber-700"></i>

                <div>

                    <p class="text-sm font-bold">
                        Silakan Absen Masuk Terlebih Dahulu
                    </p>

                    <p class="mt-1 text-xs leading-relaxed text-amber-800">
                        Form logbook akan terbuka setelah kehadiran guru tercatat di atas.
                    </p>

                </div>

            </div>

        </div>


        {{-- SUDAH MENGISI JURNAL --}}
        @if($hasSubmittedJournal)

            <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-900">

                <div class="flex items-start gap-3">

                    <i class="bi bi-check-circle-fill mt-0.5 text-emerald-700"></i>

                    <div>

                        <p class="text-sm font-bold">
                            Jurnal Hari Ini Telah Terkirim
                        </p>

                        <p class="mt-1 text-xs leading-relaxed text-emerald-800">
                            Anda sudah mengisi jurnal pembelajaran untuk hari ini.
                        </p>

                    </div>

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
                class="mt-4 space-y-6"
            >

                @csrf


                {{-- DATA JURNAL --}}
                <div class="rounded-2xl bg-white p-5 shadow-md sm:p-6">

                    <div class="grid gap-5 sm:grid-cols-2">

                        {{-- MAPEL --}}
                        <label class="block">

                            <span class="text-sm font-semibold text-slate-700">
                                Mata Pelajaran
                            </span>

                            <select
                                name="id_mapel"
                                required
                                class="mt-2 w-full rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700"
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


                        {{-- KELAS --}}
                        <label class="block">

                            <span class="text-sm font-semibold text-slate-700">
                                Kelas
                            </span>

                            <select
                                name="id_kelas"
                                x-model="selectedKelas"
                                required
                                class="mt-2 w-full rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700"
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


                        {{-- JAM --}}
                        <label class="block">

                            <span class="text-sm font-semibold text-slate-700">
                                Jam Pelajaran Ke-
                            </span>

                            <input
                                type="number"
                                name="jam_ke"
                                min="1"
                                max="10"
                                required
                                placeholder="Contoh: 1"
                                class="mt-2 w-full rounded-lg border border-slate-200 px-4 py-3 text-sm text-slate-700"
                            >

                        </label>


                        {{-- TUGAS --}}
                        <label class="block">

                            <span class="text-sm font-semibold text-slate-700">
                                Ada Tugas?
                            </span>

                            <select
                                name="ada_tugas"
                                required
                                class="mt-2 w-full rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700"
                            >

                                <option value="Tidak">
                                    Tidak
                                </option>

                                <option value="Ya">
                                    Ya
                                </option>

                            </select>

                        </label>


                        {{-- MATERI --}}
                        <label class="block sm:col-span-2">

                            <span class="text-sm font-semibold text-slate-700">
                                Materi / Pembahasan
                            </span>

                            <input
                                type="text"
                                name="materi"
                                required
                                placeholder="Contoh: Pengenalan struktur data array"
                                class="mt-2 w-full rounded-lg border border-slate-200 px-4 py-3 text-sm text-slate-700"
                            >

                        </label>

                    </div>

                </div>


                {{-- ================================================= --}}
                {{-- ABSENSI SISWA --}}
                {{-- ================================================= --}}

                <div class="overflow-hidden rounded-2xl bg-white shadow-md">

                    <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 sm:px-6">

                        <div>

                            <h3 class="font-bold text-slate-900">
                                Absensi Siswa
                            </h3>

                            <p class="mt-1 text-xs text-slate-500">
                                Tentukan status kehadiran masing-masing siswa.
                            </p>

                        </div>

                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                            Daftar Siswa
                        </span>

                    </div>


                    <div class="max-h-72 overflow-y-auto custom-scrollbar divide-y divide-slate-100">

    @forelse($siswas as $index => $siswa)

        <div
            x-data="{ status: 'Hadir' }"
            x-show="selectedKelas !== '' && selectedKelas == '{{ $siswa->kelas_id }}'"
            class="flex items-center justify-between gap-3 px-5 py-4 sm:px-6"
        >

            <div class="flex min-w-0 items-center gap-3">

                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-xs font-bold text-emerald-700">
                    {{ $index + 1 }}
                </span>

                <div class="min-w-0">

                    <p class="truncate text-sm font-semibold text-slate-700">
                        {{ $siswa->nama }}
                    </p>

                    <p class="text-xs text-slate-400">
                        NIS: {{ $siswa->nis }}
                    </p>

                </div>

            </div>

            <input
                type="hidden"
                name="absensi[{{ $siswa->id }}]"
                x-model="status"
            >

            <div class="flex shrink-0 gap-1">

                <button
                    type="button"
                    @click="status = 'Hadir'"
                    :class="status === 'Hadir'
                        ? 'bg-emerald-600 text-white'
                        : 'bg-emerald-50 text-emerald-700'"
                    class="flex h-8 w-8 items-center justify-center rounded-full text-xs font-bold"
                >
                    H
                </button>

                <button
                    type="button"
                    @click="status = 'Sakit'"
                    :class="status === 'Sakit'
                        ? 'bg-amber-500 text-white'
                        : 'bg-amber-50 text-amber-700'"
                    class="flex h-8 w-8 items-center justify-center rounded-full text-xs font-bold"
                >
                    S
                </button>

                <button
                    type="button"
                    @click="status = 'Izin'"
                    :class="status === 'Izin'
                        ? 'bg-sky-500 text-white'
                        : 'bg-sky-50 text-sky-700'"
                    class="flex h-8 w-8 items-center justify-center rounded-full text-xs font-bold"
                >
                    I
                </button>

                <button
                    type="button"
                    @click="status = 'Alpa'"
                    :class="status === 'Alpa'
                        ? 'bg-rose-500 text-white'
                        : 'bg-rose-50 text-rose-700'"
                    class="flex h-8 w-8 items-center justify-center rounded-full text-xs font-bold"
                >
                    A
                </button>

            </div>

        </div>

    @empty

        <div class="p-6 text-center text-sm text-slate-500">
            Belum ada data siswa.
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
                            Catatan Khusus / Hambatan Kelas
                        </span>

                        <textarea
                            id="catatan-khusus"
                            name="catatan"
                            rows="4"
                            placeholder="Tulis catatan atau hambatan selama pembelajaran..."
                            class="mt-2 w-full resize-none rounded-lg border border-slate-200 px-4 py-3 text-sm text-slate-700"
                        ></textarea>

                    </label>

                </div>


                {{-- SUBMIT --}}
                <button
                    type="submit"
                    class="flex w-full items-center justify-center gap-2 rounded-lg bg-emerald-600 px-5 py-3.5 text-sm font-medium text-white shadow-sm transition hover:bg-emerald-700"
                >

                    <i class="bi bi-send-fill"></i>

                    Kirim Logbook & Absensi Siswa

                </button>

            </form>

        @endif

    </section>

</div>

@endsection