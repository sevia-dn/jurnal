@extends('layouts.app')

@section('title', 'Pengajuan Ketidakhadiran Guru - JurnalKita')

@section('sidebar')
    @include('layouts.guru-pengajar.sidebar', ['activePage' => 'beranda'])
@endsection

@section('navbar')
    @include('layouts.guru-pengajar.navbar', ['activePage' => 'beranda'])
@endsection

@section('content')
    <div class="min-h-full bg-slate-50 p-4 pb-24 font-sans sm:p-6 lg:p-8">
        <div class="mx-auto max-w-xl">
            <div class="mb-4 flex items-center justify-between gap-2">
                <a href="{{ route('guru.utama') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-700 transition hover:text-emerald-800">
                    <i class="bi bi-arrow-left"></i> Kembali ke beranda
                </a>
            </div>

            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
                <div class="flex items-start gap-3 border-b border-slate-100 pb-4">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-xl text-amber-600">
                        <i class="bi bi-calendar-x-fill"></i>
                    </span>
                    <div>
                        <h1 class="text-lg font-bold text-slate-900">Form Ketidakhadiran Guru</h1>
                        <p class="mt-1 text-xs text-slate-500">Pilih status ketidakhadiran (Izin atau Sakit) untuk dikirim dan diverifikasi oleh Guru Piket.</p>
                    </div>
                </div>

                @if(session('success'))
                    <div class="mt-5 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800">
                        <i class="bi bi-check-circle-fill"></i>{{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="mt-5 rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
                        <p class="font-bold">Pengajuan belum dapat disimpan.</p>
                        <ul class="mt-1 list-inside list-disc text-xs">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if($existing)
                    <div class="mt-5 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                        <div class="flex items-start gap-2">
                            <i class="bi bi-info-circle-fill text-amber-600 text-base shrink-0 mt-0.5"></i>
                            <div>
                                <p class="font-bold">Pengajuan Aktif Ditemukan</p>
                                <p class="mt-1 text-xs">Anda sudah memiliki pengajuan untuk tanggal ini dengan status:
                                    <span class="font-semibold uppercase tracking-wider px-2 py-0.5 rounded-full text-[10px] @if($existing->status === 'disetujui') bg-emerald-100 text-emerald-800 @elseif($existing->status === 'ditolak') bg-red-100 text-red-800 @else bg-amber-200 text-amber-900 @endif">
                                        {{ $existing->status }}
                                    </span>
                                    ({{ $existing->label_alasan }}).
                                </p>
                                <p class="mt-1 text-[11px] text-amber-700">Anda dapat memperbarui data dengan mengirim ulang form di bawah.</p>
                            </div>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('guru.ketidakhadiran.store') }}" enctype="multipart/form-data" class="mt-5 space-y-4" x-data="{ alasan: '{{ old('alasan', $existing?->alasan ?? 'izin') }}', isSubmitting: false }" @submit="if (isSubmitting) { $event.preventDefault(); return false; } isSubmitting = true;">
                    @csrf

                    {{-- INFORMASI GURU (READ ONLY) --}}
                    <div class="rounded-xl bg-slate-50 border border-slate-200/80 p-3.5 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Nama Guru</p>
                            <p class="text-sm font-bold text-slate-800 truncate">{{ auth()->user()->name }}</p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">NIP / Username</p>
                            <p class="text-xs font-mono font-medium text-slate-600">{{ auth()->user()->nip ?? auth()->user()->username ?? '-' }}</p>
                        </div>
                    </div>

                    {{-- TANGGAL --}}
                    <label class="block">
                        <span class="text-xs font-bold text-slate-700">Tanggal Ketidakhadiran <span class="text-rose-500">*</span></span>
                        <input
                            type="date"
                            name="tanggal"
                            required
                            value="{{ old('tanggal', $tanggal) }}"
                            max="{{ now('Asia/Jakarta')->toDateString() }}"
                            class="mt-1.5 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-700 outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100"
                        >
                    </label>

                    {{-- PILIHAN KEHADIRAN (HANYA IZIN DAN SAKIT) --}}
                    <div>
                        <span class="text-xs font-bold text-slate-700">Pilihan Kehadiran <span class="text-rose-500">*</span></span>
                        <p class="text-[11px] text-slate-500 mt-0.5">Pilih status kehadiran Anda (hanya Izin atau Sakit):</p>
                        
                        <div class="mt-2.5 grid grid-cols-2 gap-3.5">
                            {{-- OPSI 1: IZIN --}}
                            <label
                                class="flex cursor-pointer flex-col rounded-2xl border-2 p-4 transition-all duration-150 select-none"
                                :class="alasan === 'izin' 
                                    ? 'border-amber-500 bg-amber-50/80 shadow-xs ring-2 ring-amber-400/30' 
                                    : 'border-slate-200 bg-white hover:border-amber-300 hover:bg-slate-50'"
                            >
                                <input type="radio" name="alasan" value="izin" x-model="alasan" class="sr-only" required>
                                <div class="flex items-center justify-between">
                                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-500 text-white text-xl shadow-xs">
                                        <i class="bi bi-calendar2-event-fill"></i>
                                    </span>
                                    <i class="bi bi-check-circle-fill text-xl text-amber-600 transition-opacity" :class="alasan === 'izin' ? 'opacity-100' : 'opacity-0'"></i>
                                </div>
                                <div class="mt-3">
                                    <p class="text-base font-bold text-slate-900">Izin</p>
                                    <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">Keperluan dinas, urusan keluarga, atau acara mendesak</p>
                                </div>
                            </label>

                            {{-- OPSI 2: SAKIT --}}
                            <label
                                class="flex cursor-pointer flex-col rounded-2xl border-2 p-4 transition-all duration-150 select-none"
                                :class="alasan === 'sakit' 
                                    ? 'border-rose-500 bg-rose-50/80 shadow-xs ring-2 ring-rose-400/30' 
                                    : 'border-slate-200 bg-white hover:border-rose-300 hover:bg-slate-50'"
                            >
                                <input type="radio" name="alasan" value="sakit" x-model="alasan" class="sr-only" required>
                                <div class="flex items-center justify-between">
                                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-rose-500 text-white text-xl shadow-xs">
                                        <i class="bi bi-heart-pulse-fill"></i>
                                    </span>
                                    <i class="bi bi-check-circle-fill text-xl text-rose-600 transition-opacity" :class="alasan === 'sakit' ? 'opacity-100' : 'opacity-0'"></i>
                                </div>
                                <div class="mt-3">
                                    <p class="text-base font-bold text-slate-900">Sakit</p>
                                    <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">Kondisi kesehatan kurang baik / istirahat medis</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- KETERANGAN --}}
                    <label class="block">
                        <span class="text-xs font-bold text-slate-700">Keterangan / Alasan Lengkap</span>
                        <textarea
                            name="keterangan"
                            rows="3"
                            placeholder="Tuliskan keterangan detail mengenai izin / sakit Anda..."
                            class="mt-1.5 w-full resize-y rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-700 outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100"
                        >{{ old('keterangan', $existing?->keterangan) }}</textarea>
                    </label>

                    {{-- LAMPIRAN BUKTI --}}
                    <label class="block">
                        <span class="text-xs font-bold text-slate-700">Lampiran Bukti <span class="text-slate-400 font-normal">(opsional: surat dokter / surat izin, maks 4 MB)</span></span>
                        <div class="mt-1.5">
                            <input
                                type="file"
                                name="lampiran"
                                accept=".jpg,.jpeg,.png,.pdf"
                                class="block w-full text-sm text-slate-700 file:mr-3 file:rounded-xl file:border-0 file:bg-emerald-50 file:px-3.5 file:py-2 file:text-xs file:font-bold file:text-emerald-700 hover:file:bg-emerald-100"
                            >
                            @if($existing?->lampiran)
                                <p class="mt-1 text-xs text-slate-500">Lampiran sebelumnya: <a href="{{ asset('storage/'.$existing->lampiran) }}" target="_blank" class="text-emerald-600 font-semibold hover:underline">Lihat Berkas <i class="bi bi-box-arrow-up-right text-[10px]"></i></a></p>
                            @endif
                        </div>
                    </label>

                    {{-- INFO PENERIMA --}}
                    <div class="rounded-xl border border-blue-200 bg-blue-50/80 p-3 text-xs text-blue-900 flex items-start gap-2.5">
                        <i class="bi bi-shield-check text-blue-600 text-base shrink-0 mt-0.5"></i>
                        <div>
                            <p class="font-semibold">Terkirim Langsung ke Guru Piket</p>
                            <p class="text-[11px] text-blue-700 mt-0.5">Laporan ini akan langsung masuk ke dashboard Guru Piket bertugas untuk diverifikasi dan disetujui.</p>
                        </div>
                    </div>

                    {{-- TOMBOL KIRIM --}}
                    <button
                        type="submit"
                        :disabled="isSubmitting"
                        :class="isSubmitting ? 'opacity-70 cursor-not-allowed pointer-events-none' : ''"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-3 text-sm font-bold text-white transition hover:bg-emerald-700 shadow-sm focus:outline-none focus:ring-4 focus:ring-emerald-200 cursor-pointer"
                    >
                        <span x-show="!isSubmitting" class="inline-flex items-center gap-2">
                            <i class="bi bi-send-fill"></i> Kirim ke Guru Piket
                        </span>
                        <span x-show="isSubmitting" x-cloak class="inline-flex items-center gap-2">
                            <svg class="h-4 w-4 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Sedang mengirim...
                        </span>
                    </button>
                </form>
            </section>
        </div>
    </div>
@endsection
