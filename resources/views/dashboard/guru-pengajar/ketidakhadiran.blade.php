@extends('layouts.app')

@section('title', 'Ajukan Ketidakhadiran - JurnalKita')

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
                        <h1 class="text-lg font-bold text-slate-900">Ajukan Ketidakhadiran</h1>
                        <p class="mt-1 text-xs text-slate-500">Isi form berikut untuk mengajukan izin atau sakit. Pengajuan akan diproses oleh Guru Piket.</p>
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
                        <ul class="mt-1 list-inside list-disc">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if($existing)
                    <div class="mt-5 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                        <p class="font-bold">Pengajuan Aktif Ditemukan</p>
                        <p class="mt-1">Anda sudah memiliki pengajuan untuk tanggal ini dengan status:
                            <span class="font-semibold @if($existing->status === 'disetujui') text-emerald-700 @elseif($existing->status === 'ditolak') text-red-600 @else text-amber-700 @endif">
                                {{ ucfirst($existing->status) }}
                            </span>
                            ({{ $existing->label_alasan }}).
                        </p>
                        <p class="mt-1 text-xs text-amber-700">Anda bisa mengubah pengajuan ini dengan mengirim ulang form di bawah.</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('guru.ketidakhadiran.store') }}" enctype="multipart/form-data" class="mt-5 space-y-4">
                    @csrf

                    <label class="block">
                        <span class="text-xs font-bold text-slate-700">Tanggal <span class="text-rose-500">*</span></span>
                        <input
                            type="date"
                            name="tanggal"
                            required
                            value="{{ old('tanggal', $tanggal) }}"
                            max="{{ now('Asia/Jakarta')->toDateString() }}"
                            class="mt-1.5 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-700 outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100"
                        >
                    </label>

                    <div>
                        <p class="text-xs font-bold text-slate-700">Alasan <span class="text-rose-500">*</span></p>
                        <div class="mt-2 grid grid-cols-2 gap-3">
                            <label
                                x-data
                                class="flex cursor-pointer items-center gap-3 rounded-xl border p-3.5 transition"
                                :class="$refs.izin.checked ? 'border-amber-500 bg-amber-50' : 'border-slate-200'"
                            >
                                <input type="radio" name="alasan" value="izin" x-ref="izin" @change="$el.parentElement.classList.toggle('border-amber-500'); $el.parentElement.classList.toggle('bg-amber-50')" {{ old('alasan', $existing?->alasan) === 'izin' ? 'checked' : '' }} required class="sr-only">
                                <i class="bi bi-calendar2-x text-2xl text-amber-500"></i>
                                <div>
                                    <p class="text-sm font-bold text-slate-800">Izin</p>
                                    <p class="text-[11px] text-slate-500">Keperluan mendesak</p>
                                </div>
                            </label>
                            <label
                                x-data
                                class="flex cursor-pointer items-center gap-3 rounded-xl border p-3.5 transition"
                                :class="$refs.sakit.checked ? 'border-rose-400 bg-rose-50' : 'border-slate-200'"
                            >
                                <input type="radio" name="alasan" value="sakit" x-ref="sakit" @change="$el.parentElement.classList.toggle('border-rose-400'); $el.parentElement.classList.toggle('bg-rose-50')" {{ old('alasan', $existing?->alasan) === 'sakit' ? 'checked' : '' }} class="sr-only">
                                <i class="bi bi-thermometer-half text-2xl text-rose-500"></i>
                                <div>
                                    <p class="text-sm font-bold text-slate-800">Sakit</p>
                                    <p class="text-[11px] text-slate-500">Kondisi kesehatan</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <label class="block">
                        <span class="text-xs font-bold text-slate-700">Keterangan</span>
                        <textarea
                            name="keterangan"
                            rows="3"
                            placeholder="Tambahkan keterangan tambahan jika diperlukan..."
                            class="mt-1.5 w-full resize-y rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-700 outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100"
                        >{{ old('keterangan', $existing?->keterangan) }}</textarea>
                    </label>

                    <label class="block">
                        <span class="text-xs font-bold text-slate-700">Lampiran <span class="text-slate-400 font-normal">(opsional, maks 4 MB)</span></span>
                        <div class="mt-1.5">
                            <input
                                type="file"
                                name="lampiran"
                                accept=".jpg,.jpeg,.png,.pdf"
                                class="block w-full text-sm text-slate-700 file:mr-3 file:rounded-lg file:border-0 file:bg-emerald-50 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-emerald-700 hover:file:bg-emerald-100"
                            >
                            @if($existing?->lampiran)
                                <p class="mt-1 text-xs text-slate-500">Lampiran sebelumnya: <a href="{{ asset('storage/'.$existing->lampiran) }}" target="_blank" class="text-emerald-600 hover:underline">Lihat file</a></p>
                            @endif
                        </div>
                    </label>

                    <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-amber-500 px-4 py-3 text-sm font-bold text-white transition hover:bg-amber-600 focus:outline-none focus:ring-4 focus:ring-amber-100">
                        <i class="bi bi-send-fill"></i> Kirim Pengajuan ke Guru Piket
                    </button>
                </form>
            </section>
        </div>
    </div>
@endsection
