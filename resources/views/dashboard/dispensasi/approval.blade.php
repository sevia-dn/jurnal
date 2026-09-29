@extends(($isPublicApproval ?? false) ? 'layouts.dispensasi-approval' : 'layouts.app')

@section('title', 'Detail Pengajuan Dispensasi - Waka Kesiswaan')

@if (! ($isPublicApproval ?? false))
    @section('sidebar')
        @include('layouts.guru-pengajar.sidebar', ['activePage' => 'utama'])
    @endsection

    @section('navbar')
        @include('layouts.guru-pengajar.navbar', ['activePage' => 'utama'])
    @endsection
@endif

@section('content')
<div class="mx-auto w-full max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
    {{-- Header Banner --}}
    <div class="rounded-2xl bg-gradient-to-r from-purple-900 to-indigo-900 p-6 text-white shadow-xl">
        <div class="flex items-center justify-between">
            <span class="rounded-full bg-purple-700/60 px-3 py-1 text-xs font-semibold text-purple-200 border border-purple-500/30">
                <i class="bi bi-shield-check me-1"></i> Ruang Persetujuan Waka
            </span>
            <span class="text-xs text-purple-300 font-mono">ID: #DISP-{{ $dispensasi->id }}</span>
        </div>
        <h1 class="mt-3 text-xl font-bold">Detail Pengajuan Dispensasi</h1>
        <p class="mt-1 text-sm text-purple-200">Tinjau pengajuan dari Guru Piket, lampiran pendukung, dan berikan keputusan.</p>
    </div>

    @if(session('success'))
        <div class="mt-4 rounded-xl bg-emerald-100 border border-emerald-300 p-4 text-emerald-900 font-semibold flex items-center gap-2">
            <i class="bi bi-check-circle-fill text-emerald-600 text-xl"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mt-4 rounded-xl bg-rose-100 border border-rose-300 p-4 text-rose-900 font-semibold flex items-center gap-2">
            <i class="bi bi-exclamation-triangle-fill text-rose-600 text-xl"></i>
            {{ session('error') }}
        </div>
    @endif

    {{-- Detail Dispensasi Card --}}
    <div class="mt-6 rounded-2xl bg-white p-6 shadow-md border border-slate-200 space-y-6">
        <div class="flex flex-wrap items-center justify-between border-b border-slate-100 pb-4 gap-2">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Nama Siswa</p>
                <h2 class="text-xl font-bold text-slate-900 mt-0.5">{{ $dispensasi->nama }} <span class="text-sm font-semibold text-purple-700 font-sans">({{ $dispensasi->siswa?->kelas?->nama_kelas ?? 'Umum' }})</span></h2>
            </div>
            <div>
                @if($dispensasi->status_waka === 'disetujui')
                    <span class="rounded-full bg-emerald-100 px-4 py-1.5 text-xs font-bold text-emerald-800 border border-emerald-200">
                        <i class="bi bi-check-circle-fill me-1"></i> Disetujui
                    </span>
                @elseif($dispensasi->status_waka === 'ditolak')
                    <span class="rounded-full bg-rose-100 px-4 py-1.5 text-xs font-bold text-rose-800 border border-rose-200">
                        <i class="bi bi-x-circle-fill me-1"></i> Ditolak
                    </span>
                @else
                    <span class="rounded-full bg-amber-100 px-4 py-1.5 text-xs font-bold text-amber-800 border border-amber-300 animate-pulse">
                        <i class="bi bi-clock-history me-1"></i> Menunggu Persetujuan Waka
                    </span>
                @endif
            </div>
        </div>

        <div class="grid gap-6 sm:grid-cols-2 text-sm">
            <div class="rounded-xl bg-slate-50 p-4 border border-slate-100">
                <p class="text-xs font-semibold text-slate-500 uppercase">Jenis Dispensasi</p>
                <p class="mt-1 font-bold text-slate-800 text-base">{{ $dispensasi->jenis_dispensasi }}</p>
            </div>
            <div class="rounded-xl bg-slate-50 p-4 border border-slate-100">
                <p class="text-xs font-semibold text-slate-500 uppercase">Waktu & Durasi Dispensasi</p>
                <p class="mt-1 font-bold text-slate-800 text-base">
                    {{ $dispensasi->deskripsi_waktu }}
                </p>
            </div>
            <div class="sm:col-span-2 rounded-xl bg-slate-50 p-4 border border-slate-100">
                <p class="text-xs font-semibold text-slate-500 uppercase">Alasan / Alasan Keperluan</p>
                <p class="mt-2 text-slate-800 leading-relaxed font-medium bg-white p-3 rounded-lg border border-slate-200">
                    "{{ $dispensasi->alasan }}"
                </p>
            </div>
            <div class="sm:col-span-2 rounded-xl bg-slate-50 p-4 border border-slate-100 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase">Diajukan Oleh Guru Piket</p>
                    <p class="mt-0.5 font-semibold text-slate-800">{{ $dispensasi->pembuat?->name ?? 'Guru Piket' }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs font-semibold text-slate-500 uppercase">Waktu Pengajuan</p>
                    <p class="mt-0.5 text-xs text-slate-600 font-mono">{{ $dispensasi->created_at ? $dispensasi->created_at->format('d/m/Y H:i') : '-' }}</p>
                </div>
            </div>
        </div>

        @if($dispensasi->bukti)
            <div class="rounded-xl border border-slate-200 p-4">
                <p class="text-xs font-semibold text-slate-500 uppercase mb-2">Dokumentasi / Bukti Pendukung</p>
                <a href="{{ asset('storage/' . $dispensasi->bukti) }}" target="_blank" class="inline-flex items-center gap-2 text-emerald-700 font-semibold hover:underline">
                    <i class="bi bi-file-earmark-pdf-fill text-xl"></i> Lihat Lampiran Bukti
                </a>
            </div>
        @endif

        {{-- Form Aksi Persetujuan Waka --}}
        @if($dispensasi->status_waka === 'menunggu')
            <form action="{{ ($isPublicApproval ?? false) ? route('waka.dispensasi.process', ['token' => $dispensasi->token_approval]) : route('dispensasi.process', $dispensasi->id) }}" method="POST" class="pt-4 border-t border-slate-100">
                @csrf
                @if ($isPublicApproval ?? false)
                    <label class="mb-4 block">
                        <span class="text-sm font-semibold text-slate-700">Waka Kesiswaan yang Memberikan Keputusan <span class="text-rose-600">*</span></span>
                        @if ($selectedWaka ?? false)
                            <input type="hidden" name="waka_id" value="{{ $selectedWaka->id }}">
                            <div class="mt-2 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-sm font-bold text-emerald-900">
                                <i class="bi bi-person-check-fill text-lg text-emerald-700"></i>
                                {{ $selectedWaka->name }}
                            </div>
                        @else
                            <select name="waka_id" required class="mt-2 w-full rounded-xl border border-slate-200 bg-white p-3 text-sm font-medium text-slate-800 outline-none transition focus:border-purple-600 focus:ring-4 focus:ring-purple-100">
                                <option value="">Pilih nama Waka Kesiswaan</option>
                                @foreach ($wakaKesiswaans as $waka)
                                    <option value="{{ $waka->id }}" @selected(old('waka_id') == $waka->id)>{{ $waka->name }}</option>
                                @endforeach
                            </select>
                        @endif
                        <span class="mt-1 block text-xs text-slate-500">Identitas ini akan dicantumkan pada bukti dispensasi dan QR siswa.</span>
                    </label>
                @endif
                <label class="block mb-4">
                    <span class="text-sm font-semibold text-slate-700">Catatan Waka (Opsional)</span>
                    <textarea name="catatan_waka" rows="3" placeholder="Tuliskan catatan atau instruksi tambahan jika ada..." class="mt-2 w-full rounded-xl border border-slate-200 p-3 text-sm focus:border-purple-600 focus:ring-4 focus:ring-purple-100 outline-none transition"></textarea>
                </label>

                <div class="flex flex-col sm:flex-row items-center gap-3">
                    <button type="submit" name="keputusan" value="disetujui" class="w-full sm:w-1/2 flex items-center justify-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3.5 shadow-md transition">
                        <i class="bi bi-check-circle-fill text-lg"></i> Setujui Dispensasi
                    </button>
                    <button type="submit" name="keputusan" value="ditolak" class="w-full sm:w-1/2 flex items-center justify-center gap-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold py-3.5 shadow-md transition">
                        <i class="bi bi-x-circle-fill text-lg"></i> Tolak Dispensasi
                    </button>
                </div>
            </form>
        @else
            <div class="pt-4 border-t border-slate-100 rounded-2xl bg-slate-50/80 p-5 sm:p-6 text-center space-y-4">
                @if($dispensasi->status_waka === 'disetujui')
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-700 text-2xl mb-1 shadow-xs">
                        <i class="bi bi-shield-fill-check"></i>
                    </div>
                    <div>
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300 uppercase tracking-wide">
                            Disetujui & Sah
                        </span>
                        <h3 class="text-base sm:text-lg font-bold text-slate-900 mt-2">Dispensasi Siswa Telah Disahkan</h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Diproses oleh <strong class="text-slate-700">{{ $dispensasi->pemroses?->name ?? 'Wakasek Kesiswaan' }}</strong> pada {{ $dispensasi->diproses_at ? $dispensasi->diproses_at->translatedFormat('d F Y - H:i') : '-' }} WIB
                        </p>
                    </div>

                    @if($dispensasi->catatan_waka)
                        <div class="p-3 rounded-xl bg-white border border-slate-200 text-xs text-slate-700 italic max-w-md mx-auto text-left">
                            <span class="font-bold not-italic text-slate-500 block text-[10px] uppercase mb-0.5">Catatan Waka:</span>
                            "{{ $dispensasi->catatan_waka }}"
                        </div>
                    @endif

                    @if($dispensasi->token_verifikasi)
                        <div class="p-5 rounded-2xl bg-white border border-emerald-200 shadow-xs max-w-sm mx-auto space-y-3">
                            <p class="text-xs font-bold uppercase tracking-wider text-emerald-800 flex items-center justify-center gap-1.5">
                                <i class="bi bi-qr-code-scan"></i> QR Code Bukti Approval
                            </p>
                            
                            @php
                                $verifyUrl = route('dispensasi.verify', ['token' => $dispensasi->token_verifikasi]);
                            @endphp
                            <div class="relative flex justify-center">
                                <img 
                                    src="https://api.qrserver.com/v1/create-qr-code/?size=400x400&margin=1&format=png&data={{ urlencode($verifyUrl) }}" 
                                    alt="QR Code Verifikasi Dispensasi"
                                    class="h-44 w-44 rounded-xl border border-slate-200 p-2 shadow-2xs bg-white"
                                >
                            </div>
                            <p class="text-[11px] text-slate-500 leading-tight">
                                Scan QR code di atas menggunakan kamera ponsel untuk melihat bukti verifikasi approval resmi.
                            </p>
                            <div class="pt-2 flex flex-col gap-2">
                                <a href="{{ $verifyUrl }}" target="_blank" class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 px-4 py-2.5 text-xs font-bold text-white transition shadow-xs">
                                    <i class="bi bi-box-arrow-up-right"></i> Buka Halaman Bukti Verifikasi
                                </a>
                                <a href="{{ ($isPublicApproval ?? false) ? route('waka.dispensasi.cetak', ['token' => $dispensasi->token_approval]) : route('dispensasi.cetak', $dispensasi->id) }}" target="_blank" class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-800 hover:bg-slate-900 px-4 py-2.5 text-xs font-bold text-white transition shadow-xs">
                                    <i class="bi bi-printer-fill"></i> Cetak Surat Dispensasi Resmi
                                </a>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-rose-100 text-rose-700 text-2xl mb-1 shadow-xs">
                        <i class="bi bi-x-circle-fill"></i>
                    </div>
                    <div>
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-extrabold bg-rose-100 text-rose-800 border border-rose-300 uppercase tracking-wide">
                            Pengajuan Ditolak
                        </span>
                        <h3 class="text-base font-bold text-slate-900 mt-2">Dispensasi Tidak Disetujui</h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Diproses oleh <strong class="text-slate-700">{{ $dispensasi->pemroses?->name ?? 'Wakasek Kesiswaan' }}</strong> pada {{ $dispensasi->diproses_at ? $dispensasi->diproses_at->translatedFormat('d F Y - H:i') : '-' }} WIB
                        </p>
                    </div>
                    @if($dispensasi->catatan_waka)
                        <div class="p-3 rounded-xl bg-white border border-slate-200 text-xs text-slate-700 italic max-w-md mx-auto text-left">
                            <span class="font-bold not-italic text-slate-500 block text-[10px] uppercase mb-0.5">Alasan Penolakan:</span>
                            "{{ $dispensasi->catatan_waka }}"
                        </div>
                    @endif
                @endif
            </div>
        @endif
    </div>

    @if (! ($isPublicApproval ?? false))
    <div class="mt-6 text-center">
        <a href="{{ route('guru') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-slate-900 transition">
            <i class="bi bi-arrow-left"></i> Kembali ke Dashboard Utama
        </a>
    </div>
    @endif
</div>
@endsection
