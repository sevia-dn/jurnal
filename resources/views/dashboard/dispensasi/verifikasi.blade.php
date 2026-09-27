@extends('layouts.app')

@section('title', 'Verifikasi Dispensasi Siswa - JurnalKita')

@section('content')
    <main class="flex min-h-full items-center justify-center bg-slate-50 p-4 sm:p-6">
        <section class="w-full max-w-lg overflow-hidden rounded-3xl border border-emerald-200 bg-white shadow-xl">
            <div class="bg-emerald-600 px-6 py-7 text-center text-white">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-white/20 text-3xl">
                    <i class="bi bi-patch-check-fill"></i>
                </div>
                <p class="mt-3 text-sm font-semibold uppercase tracking-[0.18em] text-emerald-100">JurnalKita</p>
                <h1 class="mt-1 text-xl font-extrabold">Dispensasi Siswa Valid</h1>
            </div>

            <div class="space-y-5 p-6 text-slate-700">
                <p class="rounded-xl bg-emerald-50 px-4 py-3 text-center text-sm font-semibold text-emerald-800">
                    Siswa berikut telah memperoleh dispensasi yang disetujui Wakasek Kesiswaan.
                </p>

                <dl class="grid grid-cols-1 gap-4 text-sm sm:grid-cols-2">
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-400">Nama siswa</dt>
                        <dd class="mt-1 font-bold text-slate-800">{{ $dispensasi->siswa?->nama ?? $dispensasi->nama }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-400">Kelas</dt>
                        <dd class="mt-1 font-bold text-slate-800">{{ $dispensasi->siswa?->kelas?->nama_kelas ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-400">Tanggal</dt>
                        <dd class="mt-1 font-semibold">{{ $dispensasi->tanggal?->translatedFormat('d F Y') ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-400">Jenis</dt>
                        <dd class="mt-1 font-semibold">{{ ucfirst($dispensasi->jenis_dispensasi ?? '-') }}</dd>
                    </div>
                </dl>

                <div class="border-t border-slate-100 pt-4">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Alasan</p>
                    <p class="mt-1 text-sm leading-relaxed">{{ $dispensasi->alasan ?: '-' }}</p>
                </div>

                <p class="text-center text-xs text-slate-400">Divalidasi pada {{ $dispensasi->diproses_at?->translatedFormat('d F Y, H:i') ?? '-' }} WIB</p>
            </div>
        </section>
    </main>
@endsection
