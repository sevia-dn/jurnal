@extends('layouts.app')

@section('title', 'Halaman Utama Guru - JurnalKita')

@section('sidebar')
    @include('layouts.guru-pengajar.sidebar', ['activePage' => 'utama'])
@endsection

@section('navbar')
    @include('layouts.guru-pengajar.navbar', ['activePage' => 'utama'])
@endsection

@section('content')
    @php
        $students = [
            ['number' => '01', 'name' => 'Aisyah Nurhaliza'],
            ['number' => '02', 'name' => 'Bagas Pratama'],
            ['number' => '03', 'name' => 'Citra Lestari'],
            ['number' => '04', 'name' => 'Dimas Saputra'],
            ['number' => '05', 'name' => 'Fajar Ramadhan'],
            ['number' => '06', 'name' => 'Gilang Maulana'],
        ];
    @endphp

    <style>[x-cloak] { display: none !important; }</style>

    <div x-data="{ hasCheckedIn: false, isWithinSchedule: true, showForm: false, statusKehadiran: 'hadir', namaGuru: '{{ auth()->user()->name ?? '' }}', nipGuru: '{{ auth()->user()->nip ?? '' }}' }" class="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <section class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-semibold text-emerald-700">{{ now()->isoFormat('D MMMM Y') }}</p>
                <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Halaman Utama Guru</h1>
                <p class="mt-2 text-sm text-slate-500">Selamat datang, {{ auth()->user()->name ?? 'Guru' }}. Kelola kehadiran, piket, dan logbook Anda.</p>
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
                <span class="w-fit rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700">SMKN 1 Boyolangu</span>
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
                                Anda tidak perlu absen mengajar terpisah karena absen piket Anda sudah mencatat kehadiran hari ini.
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

        {{-- LAPOR KEHADIRAN GURU --}}
        <section class="mt-6 rounded-2xl bg-white p-5 shadow-md sm:p-6" aria-labelledby="lapor-kehadiran-guru">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div class="flex items-start gap-3">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-xl text-emerald-700">
                        <i class="bi bi-person-check-fill" aria-hidden="true"></i>
                    </span>
                    <div>
                        <p class="text-sm font-semibold text-emerald-700">Kehadiran Guru</p>
                        <h2 id="lapor-kehadiran-guru" class="mt-1 text-xl font-bold text-slate-900">Lapor Kehadiran Guru</h2>
                        <p class="mt-1 text-sm text-slate-500">Lakukan absen masuk sebelum memulai pembelajaran atau sesi piket.</p>
                    </div>
                </div>
                @if(!$sudahAbsen)
                    <span class="w-fit rounded-full bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700">Belum absen</span>
                @else
                    <span class="w-fit rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700">Sudah absen</span>
                @endif
            </div>

            <div class="mt-6 overflow-hidden rounded-xl border border-slate-200">
                <div class="grid grid-cols-[96px_minmax(0,1fr)] border-b border-slate-100 bg-emerald-50/60 text-sm sm:grid-cols-[130px_minmax(0,1fr)_160px]">
                    <div class="flex items-center border-r border-emerald-100 px-4 py-4 font-bold text-emerald-800">Jam 1 - 2</div>
                    <div class="px-4 py-4"><p class="font-semibold text-slate-800">XI RPL 2 - Informatika</p><p class="mt-1 text-xs text-slate-500">07.00 - 08.20 WIB</p></div>
                    <span class="hidden items-center justify-center text-xs font-semibold text-emerald-700 sm:flex">Jadwal aktif</span>
                </div>
                <div class="grid grid-cols-[96px_minmax(0,1fr)] border-b border-slate-100 text-sm sm:grid-cols-[130px_minmax(0,1fr)_160px]">
                    <div class="flex items-center border-r border-slate-100 px-4 py-4 font-semibold text-slate-500">Jam 3 - 4</div>
                    <div class="px-4 py-4"><p class="font-medium text-slate-700">XI RPL 1 - Informatika</p><p class="mt-1 text-xs text-slate-500">08.20 - 09.40 WIB</p></div>
                    <span class="hidden items-center justify-center text-xs text-slate-400 sm:flex">Berikutnya</span>
                </div>
            </div>

            <div class="mt-5 flex flex-col gap-3 rounded-xl bg-slate-50 p-4 sm:flex-row sm:items-center sm:justify-between">
                <p class="flex items-start gap-2 text-xs leading-relaxed text-slate-500"><i class="bi bi-info-circle-fill mt-0.5 text-emerald-600" aria-hidden="true"></i><span>Data absensi masuk akan diteruskan ke monitoring Piket dan Admin.</span></p>
                @if(!$sudahAbsen)
                    <form action="{{ route('guru.absen-masuk') }}" method="POST" class="m-0 p-0">
                        @csrf
                        <button type="submit" class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg bg-emerald-600 px-4 py-3 text-sm font-medium text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:ring-offset-2 cursor-pointer">
                            <i class="bi bi-box-arrow-in-right" aria-hidden="true"></i>
                            Absen Masuk / Lapor Kehadiran
                        </button>
                    </form>
                @else
                    <span class="inline-flex shrink-0 items-center gap-2 rounded-lg bg-emerald-100 px-4 py-3 text-sm font-semibold text-emerald-700">
                        <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
                        Absen masuk tercatat ({{ substr($kehadiranHariIni?->jam_masuk ?? now()->format('H:i:s'), 0, 5) }} WIB)
                    </span>
                @endif
            </div>
        </section>

        {{-- LOGBOOK MENGAJAR (DIKUNCI JIKA sedang PIKET) --}}
        <section class="mt-8" aria-labelledby="isi-logbook">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700"><i class="bi bi-journal-text" aria-hidden="true"></i></span>
                <div>
                    <h2 id="isi-logbook" class="text-xl font-bold text-slate-900">Isi Logbook Mengajar</h2>
                    <p class="mt-1 text-sm text-slate-500">Jurnal pembelajaran untuk jadwal aktif Anda.</p>
                </div>
            </div>

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
            @else
                <div x-show="!hasCheckedIn" class="mt-4 rounded-xl border border-amber-200 bg-amber-50 p-4 text-amber-900" role="alert">
                    <div class="flex items-start gap-3"><i class="bi bi-lock-fill mt-0.5 text-amber-700" aria-hidden="true"></i><div><p class="text-sm font-bold">Silakan Absen Masuk Terlebih Dahulu</p><p class="mt-1 text-xs leading-relaxed text-amber-800">Form logbook akan terbuka setelah kehadiran guru tercatat pada jadwal aktif.</p></div></div>
                </div>

                <form x-cloak
                        x-show="hasCheckedIn && isWithinSchedule"
                        action="{{ route('guru.riwayat') }}"
                        method="GET"
                        class="mt-4 space-y-6"
                        @submit.prevent="window.location.href='{{ route('guru.riwayat') }}'">
                    <div class="rounded-2xl bg-white p-5 shadow-md sm:p-6">
                        <div class="grid gap-5 sm:grid-cols-2">
                            <label class="block"><span class="text-sm font-semibold text-slate-700">Mata Pelajaran</span><input type="text" value="Informatika" readonly class="mt-2 w-full cursor-not-allowed rounded-lg border border-slate-200 bg-slate-100 px-4 py-3 text-sm font-medium text-slate-600 outline-none"></label>
                            <label class="block"><span class="text-sm font-semibold text-slate-700">Kelas</span><input type="text" value="XI RPL 2" readonly class="mt-2 w-full cursor-not-allowed rounded-lg border border-slate-200 bg-slate-100 px-4 py-3 text-sm font-medium text-slate-600 outline-none"></label>
                            <label class="block"><span class="text-sm font-semibold text-slate-700">Jam Pelajaran</span><input type="text" value="Jam ke 1-2" readonly class="mt-2 w-full cursor-not-allowed rounded-lg border border-slate-200 bg-slate-100 px-4 py-3 text-sm font-medium text-slate-600 outline-none"></label>
                            <label class="block sm:col-span-2"><span class="text-sm font-semibold text-slate-700">Materi / Pembahasan</span><input type="text" placeholder="Contoh: Pengenalan struktur data array" class="mt-2 w-full rounded-lg border border-slate-200 px-4 py-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-emerald-600 focus:ring-4 focus:ring-emerald-100"></label>
                        </div>
                    </div>

                    <button type="submit"
                            class="flex w-full items-center justify-center gap-2 rounded-lg bg-emerald-600 px-5 py-3.5 text-sm font-medium text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:ring-offset-2">
                        <i class="bi bi-send-fill" aria-hidden="true"></i>
                        Kirim Logbook
                    </button>
                </form>
            @endif
        </section>
    </div>

@endsection
