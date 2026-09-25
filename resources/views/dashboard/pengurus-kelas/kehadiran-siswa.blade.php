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
        matches(nama, nis, no) {
            if (!this.searchQuery.trim()) return true;
            const q = this.searchQuery.toLowerCase();
            return nama.toLowerCase().includes(q) || nis.toLowerCase().includes(q) || String(no).includes(q);
        }
    }"
    class="mx-auto w-full max-w-5xl px-4 py-6 pb-24 sm:px-6 lg:px-8"
>

    <header class="mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <p class="text-xs font-bold text-emerald-700 uppercase tracking-wider">{{ $tanggalFormatted }}</p>
            <h1 class="mt-1 text-xl font-extrabold text-slate-900 sm:text-2xl">
                Data Presensi Siswa {{ $kelas->nama_kelas ?? '' }}
                <span class="text-sm font-semibold text-slate-500">({{ $siswas->count() }} Siswa)</span>
            </h1>
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

    <div class="mb-4 flex flex-wrap items-center gap-x-4 gap-y-2 rounded-xl border border-emerald-100 bg-emerald-50/60 px-4 py-2.5 text-xs text-slate-700">
        <span class="font-bold text-emerald-800">Status Kehadiran:</span>
        <span class="inline-flex items-center gap-1 font-semibold text-emerald-700"><span class="flex h-5 w-5 items-center justify-center rounded-full bg-emerald-100 text-[10px] font-bold">H</span> Hadir</span>
        <span class="inline-flex items-center gap-1 font-semibold text-amber-700"><span class="flex h-5 w-5 items-center justify-center rounded-full bg-amber-100 text-[10px] font-bold">S</span> Sakit</span>
        <span class="inline-flex items-center gap-1 font-semibold text-blue-700"><span class="flex h-5 w-5 items-center justify-center rounded-full bg-blue-100 text-[10px] font-bold">I</span> Izin</span>
        <span class="inline-flex items-center gap-1 font-semibold text-indigo-700"><span class="flex h-5 w-5 items-center justify-center rounded-full bg-indigo-100 text-[10px] font-bold">D</span> Dispensasi</span>
        <span class="inline-flex items-center gap-1 font-semibold text-rose-700"><span class="flex h-5 w-5 items-center justify-center rounded-full bg-rose-100 text-[10px] font-bold">A</span> Alpa</span>
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

                    // Override: jika ada dispensasi aktif & disetujui hari ini → paksa status Dispensasi
                    $dispensasiSiswa = $dispensasiAktifHariIni->get($siswa->id);
                    if ($dispensasiSiswa) {
                        $status = 'Dispensasi';
                        $catatanAbsen = 'Dispensasi disetujui: ' . $dispensasiSiswa->alasan;
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
                            @if($catatanAbsen)
                                <p class="text-[11px] text-slate-500 italic mt-0.5">Ket: {{ $catatanAbsen }}</p>
                            @endif
                        </div>
                    </div>

                    <fieldset class="flex shrink-0 items-center gap-1" aria-label="Status absensi {{ $siswa->nama }}">
                        @foreach(['Hadir' => 'H', 'Sakit' => 'S', 'Izin' => 'I', 'Alpa' => 'A', 'Dispensasi' => 'D'] as $option => $label)
                            <label class="cursor-not-allowed">
                                <input type="radio" name="status_{{ $siswa->id }}" value="{{ $option }}" @checked($status === $option) disabled class="peer sr-only">
                                <span class="flex h-7 w-7 items-center justify-center rounded-lg border border-slate-200 bg-white text-[11px] font-bold text-slate-400 transition peer-checked:border-emerald-600 peer-checked:bg-emerald-600 peer-checked:text-white peer-checked:shadow-sm">{{ $label }}</span>
                            </label>
                        @endforeach
                    </fieldset>
                </li>
            @endforeach
        </ul>
    @endif

</div>
@endsection
