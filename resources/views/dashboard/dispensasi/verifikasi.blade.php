@extends('layouts.dispensasi-approval')

@section('title', 'Bukti Verifikasi Dispensasi Siswa - JurnalKita')

@section('content')
    <main class="flex min-h-full items-center justify-center bg-slate-100/80 p-4 sm:p-6 font-sans">
        <section class="w-full max-w-xl overflow-hidden rounded-3xl border border-emerald-200 bg-white shadow-2xl">
            {{-- Header Bukti Resmi --}}
            <div class="bg-gradient-to-br from-emerald-600 to-teal-800 px-6 py-8 text-center text-white relative overflow-hidden">
                <div class="absolute -right-8 -top-8 w-32 h-32 rounded-full bg-white/10 blur-xl"></div>
                <div class="absolute -left-8 -bottom-8 w-32 h-32 rounded-full bg-black/10 blur-xl"></div>
                
                <img src="{{ asset('img/logo-rounded.png') }}" alt="Logo JurnalKita" class="mx-auto h-16 w-16 rounded-2xl shadow-md border-2 border-white/30 object-cover mb-3">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-500/30 text-emerald-100 border border-emerald-400/40 uppercase tracking-wider mb-1">
                    <i class="bi bi-shield-fill-check text-emerald-300"></i> Dokumen Terverifikasi Sah
                </span>
                <h1 class="mt-1 text-2xl font-black tracking-tight">Dispensasi Siswa Valid</h1>
                <p class="text-xs text-emerald-100/90 mt-1 font-mono">Bukti Persetujuan Resmi &bull; ID: #DISP-{{ $dispensasi->id }} &bull; Token: {{ substr($dispensasi->token_verifikasi, 0, 16) }}...</p>
            </div>

            <div class="space-y-6 p-6 sm:p-7 text-slate-700">
                {{-- Banner Status --}}
                <div class="flex items-center gap-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-emerald-900">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-600 text-white text-xl shadow-xs">
                        <i class="bi bi-check2-circle"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-emerald-950">Dispensasi Resmi Disetujui</h2>
                        <p class="text-xs text-emerald-800 mt-0.5">Siswa bersangkutan telah memperoleh izin resmi dari pihak sekolah.</p>
                    </div>
                </div>

                {{-- Daftar Siswa --}}
                @php
                    $siswas = $dispensasi->siswas->isNotEmpty() ? $dispensasi->siswas : collect([$dispensasi->siswa])->filter();
                @endphp
                <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4 sm:p-5 space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1.5">
                        <i class="bi bi-people-fill text-slate-600"></i> Siswa yang Mendapatkan Dispensasi ({{ $siswas->count() }})
                    </h3>
                    <div class="divide-y divide-slate-200/80">
                        @foreach($siswas as $s)
                            <div class="py-2.5 first:pt-0 last:pb-0 flex items-center justify-between gap-3">
                                <div>
                                    <p class="text-sm font-bold text-slate-900">{{ $s->nama }}</p>
                                    <p class="text-xs text-slate-500">NISN / NIS: {{ $s->nis ?? '-' }}</p>
                                </div>
                                <span class="shrink-0 px-2.5 py-1 rounded-lg text-xs font-bold bg-white border border-slate-200 text-slate-700 shadow-2xs">
                                    {{ $s->kelas?->nama_kelas ?? '-' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Detail Izin Dispensasi --}}
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 text-sm">
                    <div class="rounded-xl border border-slate-200 p-3.5 bg-white space-y-1">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Jenis Dispensasi</p>
                        <p class="font-bold text-slate-900">{{ ucfirst($dispensasi->jenis_dispensasi ?? '-') }}</p>
                    </div>
                    <div class="rounded-xl border border-slate-200 p-3.5 bg-white space-y-1">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Waktu & Durasi</p>
                        <p class="font-semibold text-slate-800 text-xs sm:text-sm">{{ $dispensasi->deskripsi_waktu }}</p>
                    </div>
                </div>

                {{-- Alasan Dispensasi --}}
                <div class="rounded-xl border border-slate-200 p-4 bg-white space-y-1">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Keterangan / Alasan</p>
                    <p class="text-sm font-medium text-slate-800 leading-relaxed italic">"{{ $dispensasi->alasan ?: '-' }}"</p>
                </div>

                {{-- Lampiran Bukti jika ada --}}
                @if($dispensasi->bukti)
                    <div class="rounded-xl border border-slate-200 p-4 bg-white flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <i class="bi bi-paperclip text-lg text-slate-500 shrink-0"></i>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-slate-800 truncate">Lampiran Bukti Pengajuan</p>
                                <p class="text-[11px] text-slate-400 truncate">{{ basename($dispensasi->bukti) }}</p>
                            </div>
                        </div>
                        <a href="{{ asset('storage/' . $dispensasi->bukti) }}" target="_blank" class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 text-xs font-bold transition">
                            <i class="bi bi-eye-fill"></i> Buka Lampiran
                        </a>
                    </div>
                @endif

                {{-- Pengesahan & Validasi --}}
                <div class="rounded-2xl border border-emerald-100 bg-emerald-50/40 p-4 sm:p-5 space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-emerald-900 flex items-center gap-1.5">
                        <i class="bi bi-patch-check-fill text-emerald-600"></i> Pengesahan Pejabat Sekolah
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div class="space-y-0.5">
                            <p class="text-slate-500">Wakasek Kesiswaan (Penyetuju):</p>
                            <p class="font-bold text-slate-900 text-sm">{{ $dispensasi->pemroses?->name ?? 'Wakasek Kesiswaan' }}</p>
                            <p class="text-slate-500">NIP: {{ $dispensasi->pemroses?->nip ?? '-' }}</p>
                        </div>
                        <div class="space-y-0.5">
                            <p class="text-slate-500">Guru Piket (Pengaju):</p>
                            <p class="font-bold text-slate-900 text-sm">{{ $dispensasi->pembuat?->name ?? 'Guru Piket' }}</p>
                            <p class="text-slate-500">NIP: {{ $dispensasi->pembuat?->nip ?? '-' }}</p>
                        </div>
                    </div>
                    @if($dispensasi->catatan_waka)
                        <div class="pt-2 border-t border-emerald-200/60 text-xs text-slate-700">
                            <span class="font-bold text-slate-600">Catatan Waka:</span> {{ $dispensasi->catatan_waka }}
                        </div>
                    @endif
                    <div class="pt-2 border-t border-emerald-200/60 text-[11px] text-slate-500 flex items-center justify-between">
                        <span>Disahkan pada:</span>
                        <span class="font-bold text-slate-700 font-mono">{{ $dispensasi->diproses_at?->translatedFormat('d F Y, H:i') ?? '-' }} WIB</span>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="pt-2 flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('dispensasi.cetak', $dispensasi->id) }}" target="_blank" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold py-3 text-xs sm:text-sm transition shadow-sm">
                        <i class="bi bi-printer-fill"></i> Cetak Surat Dispensasi Resmi
                    </a>
                </div>

                <p class="text-center text-[11px] text-slate-400">
                    Dokumen elektronik ini sah dan diakui secara resmi dalam Sistem KBM & Piket JurnalKita SMKN 1 Boyolangu.
                </p>
            </div>
        </section>
    </main>
@endsection
