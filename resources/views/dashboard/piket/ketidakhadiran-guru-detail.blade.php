<div>
    <!-- Simplicity is an acquired taste. - Katharine Gerould -->
</div>
@extends('layouts.app')

@section('title', 'Detail Ketidakhadiran Guru - JurnalKita')

@section('sidebar')
    @include('layouts.guru-pengajar.sidebar', ['activePage' => 'piket'])
@endsection

@section('navbar')
    @include('layouts.guru-pengajar.navbar', ['activePage' => 'piket'])
@endsection

@section('content')
    <div class="min-h-full bg-slate-50 p-4 pb-24 font-sans sm:p-6 lg:p-8">
        <main class="mx-auto max-w-2xl">
            <a href="{{ route('piket.ketidakhadiran-guru.index', ['tanggal' => $ketidakhadiran->tanggal->toDateString(), 'status' => 'all']) }}" class="mb-4 inline-flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-700 transition hover:bg-emerald-100">
                <i class="bi bi-arrow-left"></i>Kembali
            </a>

            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <header class="flex items-start gap-4 border-b border-slate-100 p-5 sm:p-6">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl {{ $ketidakhadiran->alasan === 'sakit' ? 'bg-rose-100 text-rose-600' : 'bg-amber-100 text-amber-600' }} text-xl">
                        <i class="bi {{ $ketidakhadiran->alasan === 'sakit' ? 'bi-thermometer-half' : 'bi-calendar2-x' }}"></i>
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Detail pengajuan ketidakhadiran</p>
                        <h1 class="mt-1 text-lg font-extrabold text-slate-900">{{ $ketidakhadiran->guru?->name ?? 'Guru' }}</h1>
                        <p class="mt-1 text-xs text-slate-500">{{ $ketidakhadiran->tanggal->translatedFormat('l, d F Y') }}</p>
                    </div>
                    <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $ketidakhadiran->status === 'disetujui' ? 'bg-emerald-100 text-emerald-700' : ($ketidakhadiran->status === 'ditolak' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700') }}">{{ ucfirst($ketidakhadiran->status) }}</span>
                </header>

                <dl class="grid gap-4 p-5 text-sm sm:grid-cols-2 sm:p-6">
                    <div><dt class="text-xs font-semibold text-slate-400">Jenis ketidakhadiran</dt><dd class="mt-1 font-bold text-slate-800">{{ $ketidakhadiran->label_alasan }}</dd></div>
                    <div><dt class="text-xs font-semibold text-slate-400">NIP</dt><dd class="mt-1 font-bold text-slate-800">{{ $ketidakhadiran->guru?->nip ?? '-' }}</dd></div>
                    <div class="sm:col-span-2"><dt class="text-xs font-semibold text-slate-400">Keterangan guru</dt><dd class="mt-1 leading-relaxed text-slate-700">{{ $ketidakhadiran->keterangan ?: '-' }}</dd></div>
                    @if($ketidakhadiran->lampiran)
                        <div class="sm:col-span-2"><dt class="text-xs font-semibold text-slate-400">Lampiran</dt><dd class="mt-1"><a href="{{ asset('storage/'.$ketidakhadiran->lampiran) }}" target="_blank" class="inline-flex items-center gap-1.5 font-bold text-emerald-700 hover:underline"><i class="bi bi-paperclip"></i> Buka lampiran pengajuan</a></dd></div>
                    @endif
                    @if($ketidakhadiran->handler)
                        <div class="sm:col-span-2"><dt class="text-xs font-semibold text-slate-400">Diproses oleh</dt><dd class="mt-1 text-slate-700">{{ $ketidakhadiran->handler->name }}{{ $ketidakhadiran->handled_at ? ' · '.$ketidakhadiran->handled_at->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i').' WIB' : '' }}</dd></div>
                    @endif
                </dl>

                @if($ketidakhadiran->status === 'pending')
                    <footer class="flex flex-wrap gap-2 border-t border-slate-100 bg-slate-50 p-5 sm:p-6" x-data="{ reject: false }">
                        <form method="POST" action="{{ route('piket.ketidakhadiran-guru.approve', $ketidakhadiran) }}">@csrf<button type="submit" class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-bold text-white hover:bg-emerald-700"><i class="bi bi-check-lg mr-1"></i> Setujui pengajuan</button></form>
                        <button type="button" @click="reject = !reject" class="rounded-xl border border-rose-300 px-4 py-2 text-sm font-bold text-rose-600 hover:bg-rose-50"><i class="bi bi-x-lg mr-1"></i> Tolak</button>
                        <form x-show="reject" x-cloak method="POST" action="{{ route('piket.ketidakhadiran-guru.reject', $ketidakhadiran) }}" class="w-full pt-2">@csrf<textarea name="catatan_piket" rows="2" placeholder="Alasan penolakan (opsional)" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none focus:border-rose-400"></textarea><button type="submit" class="mt-2 rounded-lg bg-rose-600 px-3 py-2 text-xs font-bold text-white">Konfirmasi penolakan</button></form>
                    </footer>
                @endif
            </section>
        </main>
    </div>
@endsection
