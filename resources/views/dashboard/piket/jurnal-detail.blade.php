@extends('layouts.app')

@section('title', 'Detail Jurnal - Piket JurnalKita')

@section('sidebar')
    @include('layouts.guru-pengajar.sidebar', ['activePage' => 'piket'])
@endsection

@section('navbar')
    @include('layouts.guru-pengajar.navbar', ['activePage' => 'piket'])
@endsection

@section('content')
    @php
        $validation = $jurnal->status_validasi ?? 'belum_divalidasi';
        $validationLabel = match ($validation) {
            'disetujui' => 'Sudah divalidasi',
            'ditolak' => 'Perlu revisi',
            default => 'Menunggu validasi',
        };
        $validationClass = match ($validation) {
            'disetujui' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'ditolak' => 'bg-rose-100 text-rose-800 border-rose-200',
            default => 'bg-amber-100 text-amber-800 border-amber-200',
        };
        $namaGuru = $jurnal->guru?->name ?? 'Guru Pengajar';
        $initials = collect(preg_split('/\s+/', trim($namaGuru)))->filter()->take(2)->map(fn ($name) => mb_strtoupper(mb_substr($name, 0, 1)))->implode('');
        $jamText = $jurnal->jam_selesai && $jurnal->jam_selesai !== $jurnal->jam_ke ? "Jam ke {$jurnal->jam_ke}–{$jurnal->jam_selesai}" : "Jam ke {$jurnal->jam_ke}";
        $lampiranUrl = $jurnal->lampiran ? asset('storage/'.$jurnal->lampiran) : null;
        $lampiranExtension = $jurnal->lampiran ? strtolower(pathinfo($jurnal->lampiran, PATHINFO_EXTENSION)) : null;
    @endphp

    <div class="mx-auto w-full max-w-5xl px-4 py-5 pb-24 sm:px-6 lg:px-8">
        <a href="{{ route('dashboard.piket') }}" class="mb-4 inline-flex items-center gap-2 text-sm font-semibold text-emerald-700 transition hover:text-emerald-800"><i class="bi bi-arrow-left"></i>Kembali ke aktivitas jurnal</a>

        <header class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div class="flex min-w-0 items-center gap-4"><span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-emerald-600 text-base font-extrabold text-white">{{ $initials ?: 'G' }}</span><div class="min-w-0"><p class="text-xs font-bold uppercase tracking-wider text-emerald-700">Detail logbook mengajar</p><h1 class="truncate text-xl font-extrabold text-slate-900">{{ $namaGuru }}</h1><p class="mt-1 text-sm text-slate-500">{{ $jurnal->mapel?->nama_mapel ?? '-' }} · {{ $jurnal->kelas?->nama_kelas ?? '-' }}</p></div></div>
                <span class="inline-flex w-fit items-center gap-1.5 rounded-full border px-3 py-1.5 text-xs font-bold {{ $validationClass }}"><i class="bi {{ $validation === 'disetujui' ? 'bi-check-circle-fill' : ($validation === 'ditolak' ? 'bi-x-circle-fill' : 'bi-hourglass-split') }}"></i>{{ $validationLabel }}</span>
            </div>
        </header>

        <div class="mt-5 space-y-4">
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><h2 class="text-sm font-extrabold uppercase tracking-wide text-slate-700">Jadwal &amp; Informasi Pengisian</h2><dl class="mt-3 grid gap-3 sm:grid-cols-2 lg:grid-cols-4"><div class="rounded-xl border border-slate-100 bg-slate-50 p-3.5"><dt class="text-[11px] font-bold uppercase text-slate-400">Tanggal</dt><dd class="mt-1 text-sm font-bold text-slate-800">{{ \Carbon\Carbon::parse($jurnal->tanggal)->translatedFormat('l, d F Y') }}</dd></div><div class="rounded-xl border border-slate-100 bg-slate-50 p-3.5"><dt class="text-[11px] font-bold uppercase text-slate-400">Waktu</dt><dd class="mt-1 text-sm font-bold text-slate-800">{{ $jamText }}</dd></div><div class="rounded-xl border border-slate-100 bg-slate-50 p-3.5"><dt class="text-[11px] font-bold uppercase text-slate-400">Kehadiran Guru</dt><dd class="mt-1 inline-flex items-center gap-1 text-sm font-bold text-emerald-700"><i class="bi bi-person-check-fill"></i>{{ $jurnal->status_kehadiran_guru ?? 'Hadir' }}</dd></div><div class="rounded-xl border border-slate-100 bg-slate-50 p-3.5"><dt class="text-[11px] font-bold uppercase text-slate-400">Tugas</dt><dd class="mt-1 text-sm font-bold text-slate-800">{{ $jurnal->ada_tugas ? 'Ada tugas diberikan' : 'Tidak ada tugas' }}</dd></div></dl></section>

            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><h2 class="text-sm font-extrabold uppercase tracking-wide text-slate-700">Materi / Pokok Pembahasan</h2><p class="mt-3 whitespace-pre-line rounded-xl border border-slate-100 bg-slate-50 p-4 text-sm leading-relaxed text-slate-700">{{ $jurnal->materi ?: 'Tidak ada materi yang dicatat.' }}</p><h3 class="mt-4 text-sm font-extrabold text-slate-700">Catatan Khusus / Hambatan</h3><p class="mt-2 whitespace-pre-line rounded-xl border border-amber-100 bg-amber-50/60 p-4 text-sm leading-relaxed {{ $jurnal->catatan ? 'text-amber-900' : 'italic text-slate-500' }}">{{ $jurnal->catatan ?: 'Tidak ada catatan khusus / hambatan.' }}</p></section>

            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">@php $siswaTidakHadir = $jurnal->absensis->filter(fn ($absensi) => trim(strtolower($absensi->status ?? '')) !== 'hadir'); @endphp <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between"><h2 class="text-sm font-extrabold uppercase tracking-wide text-slate-700">Absensi Siswa</h2><span class="w-fit rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700">{{ $siswaTidakHadir->count() }} siswa tidak hadir</span></div><div class="mt-3 flex flex-wrap gap-2 text-xs font-bold"><span class="rounded-full bg-emerald-100 px-3 py-1 text-emerald-800">{{ $jurnal->jumlah_hadir ?? 0 }} Hadir</span><span class="rounded-full bg-amber-100 px-3 py-1 text-amber-800">{{ $jurnal->jumlah_sakit ?? 0 }} Sakit</span><span class="rounded-full bg-blue-100 px-3 py-1 text-blue-800">{{ $jurnal->jumlah_izin ?? 0 }} Izin</span><span class="rounded-full bg-rose-100 px-3 py-1 text-rose-800">{{ $jurnal->jumlah_alpa ?? 0 }} Alpa</span><span class="rounded-full bg-indigo-100 px-3 py-1 text-indigo-800">{{ $jurnal->jumlah_dispensasi ?? 0 }} Dispensasi</span></div>
                <div class="mt-4 grid gap-2 sm:grid-cols-2">@forelse($siswaTidakHadir as $absensi) @php $normStatus = strtoupper(trim((string) $absensi->status)); $status = in_array($normStatus, ['D', 'DISPENSASI']) ? 'Dispensasi' : (in_array($normStatus, ['ALFA', 'ALPA']) ? 'Alpa' : $absensi->status); $statusClass = match ($status) {'Sakit' => 'bg-amber-100 text-amber-800', 'Izin' => 'bg-blue-100 text-blue-800', 'Alpa' => 'bg-rose-100 text-rose-800', default => 'bg-indigo-100 text-indigo-800'}; @endphp <div class="flex items-center justify-between gap-3 rounded-xl border border-slate-200 bg-slate-50 p-3"><div class="min-w-0"><p class="truncate text-sm font-bold text-slate-800">{{ $absensi->siswa?->nama ?? 'Siswa tidak ditemukan' }}</p><p class="mt-0.5 text-xs text-slate-500">NIS: {{ $absensi->siswa?->nis ?? '-' }}</p><p class="mt-1 text-xs text-slate-600">Alasan: {{ $absensi->catatan ?: 'Tidak ada keterangan yang diisi guru.' }}</p></div><span class="shrink-0 rounded-full px-2.5 py-1 text-[11px] font-bold {{ $statusClass }}">{{ $status }}</span></div> @empty <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-5 text-center text-sm font-medium text-emerald-800 sm:col-span-2">Nihil. Semua siswa tercatat hadir pada jurnal ini.</div> @endforelse</div>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><h2 class="text-sm font-extrabold uppercase tracking-wide text-slate-700">Lampiran Bukti Kehadiran</h2>@if($lampiranUrl)<div class="mt-3 overflow-hidden rounded-xl border border-slate-200 bg-slate-50">@if(in_array($lampiranExtension, ['jpg', 'jpeg', 'png', 'webp'], true))<a href="{{ $lampiranUrl }}" target="_blank"><img src="{{ $lampiranUrl }}" alt="Lampiran jurnal {{ $namaGuru }}" class="max-h-[32rem] w-full object-contain"></a>@else<a href="{{ $lampiranUrl }}" target="_blank" class="flex items-center gap-3 p-4 text-sm font-bold text-emerald-700 transition hover:bg-emerald-50"><i class="bi bi-file-earmark-pdf-fill text-2xl text-rose-600"></i>Buka lampiran jurnal</a>@endif</div>@else<div class="mt-3 rounded-xl border border-dashed border-slate-200 bg-slate-50 p-5 text-sm text-slate-500"><i class="bi bi-camera-video-off mr-1"></i>Tidak ada lampiran pada jurnal ini.</div>@endif</section>

            @if($validation === 'disetujui' || ($validation === 'ditolak' && $jurnal->catatan_validasi))<section class="rounded-2xl border {{ $validation === 'disetujui' ? 'border-emerald-200 bg-emerald-50/60' : 'border-rose-200 bg-rose-50/60' }} p-5 shadow-sm"><h2 class="text-sm font-extrabold {{ $validation === 'disetujui' ? 'text-emerald-800' : 'text-rose-800' }}">Informasi Validasi Pengurus Kelas</h2><p class="mt-2 text-sm {{ $validation === 'disetujui' ? 'text-emerald-800' : 'text-rose-800' }}">{{ $jurnal->catatan_validasi ?: 'Jurnal telah divalidasi oleh pengurus kelas.' }}</p>@if($jurnal->divalidasi_pada)<p class="mt-2 text-xs font-semibold text-slate-500">Divalidasi pada {{ \Carbon\Carbon::parse($jurnal->divalidasi_pada)->translatedFormat('d F Y, H:i') }} WIB.</p>@endif</section>@endif
        </div>
    </div>
@endsection
