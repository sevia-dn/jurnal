@extends('layouts.app')

@section('title', 'Pengaturan Tenggat Jurnal - Admin')

@section('sidebar')
    @include('layouts.admin.sidebar')
@endsection

@section('navbar')
    @include('layouts.admin.navbar')
@endsection

@section('content')
<div class="p-6 sm:p-10 font-sans max-w-5xl">
    {{-- Header Halaman --}}
    <div class="mb-8">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-xl shadow-xs">
                <i class="bi bi-clock-history"></i>
            </div>
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900">Kebijakan Tenggat Pengisian Jurnal</h1>
                <p class="text-sm text-slate-500 mt-0.5">Konfigurasi batas waktu dan toleransi pengisian jurnal mengajar bagi seluruh guru di sekolah.</p>
            </div>
        </div>
    </div>

    {{-- Alert Flash Message --}}
    @if(session('success'))
        <div class="mb-6 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-900 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill text-xl text-emerald-600 shrink-0"></i>
            <span class="text-sm font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 flex items-center gap-3 rounded-xl border border-rose-200 bg-rose-50 px-5 py-4 text-rose-900 shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill text-xl text-rose-600 shrink-0"></i>
            <span class="text-sm font-semibold">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Form Pengaturan --}}
    <form action="{{ route('admin.pengaturan-jurnal.update') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-sm">
            <h2 class="text-base font-bold text-slate-800 mb-2">Pilih Kebijakan Waktu Pengisian</h2>
            <p class="text-xs text-slate-500 mb-6">Pilih salah satu aturan di bawah ini yang akan diberlakukan secara langsung ke seluruh akun guru.</p>

            <div class="space-y-4">
                {{-- Opsi 1: Ketat (jam_mengajar) --}}
                <label class="relative flex cursor-pointer flex-col sm:flex-row items-start sm:items-center justify-between gap-4 rounded-xl border-2 p-5 transition hover:border-emerald-300 {{ old('kebijakan_tenggat', $pengaturan->kebijakan_tenggat) === 'jam_mengajar' ? 'border-emerald-500 bg-emerald-50/40 ring-2 ring-emerald-100' : 'border-slate-200 bg-white' }}">
                    <div class="flex items-start gap-4">
                        <input
                            type="radio"
                            name="kebijakan_tenggat"
                            value="jam_mengajar"
                            class="mt-1 text-emerald-600 focus:ring-emerald-500 h-4 w-4"
                            {{ old('kebijakan_tenggat', $pengaturan->kebijakan_tenggat) === 'jam_mengajar' ? 'checked' : '' }}
                        >
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-slate-900 text-sm sm:text-base">Ketat: Hanya Saat Jam Mengajar Berlangsung</span>
                                <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-[11px] font-bold text-emerald-800 border border-emerald-200">
                                    Default
                                </span>
                            </div>
                            <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                                Guru hanya dapat mengisi jurnal saat jam mengajar mata pelajaran tersebut sedang berjalan (contoh: Jam ke 1-3 pukul 07:00 - 09:00). Setelah jam sesi selesai, pengisian jurnal terkunci otomatis.
                            </p>
                            <div class="mt-2 flex items-center gap-2 text-[11px] font-semibold text-emerald-700">
                                <i class="bi bi-shield-check"></i>
                                <span>Menjamin pengisian jurnal berlangsung real-time di dalam kelas.</span>
                            </div>
                        </div>
                    </div>
                    <div class="shrink-0 self-end sm:self-center">
                        <span class="inline-flex items-center gap-1 rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600">
                            <i class="bi bi-clock"></i> Slot Waktu Sesi
                        </span>
                    </div>
                </label>

                {{-- Opsi 2: Fleksibel Hari Ini (hari_ini) --}}
                <label class="relative flex cursor-pointer flex-col sm:flex-row items-start sm:items-center justify-between gap-4 rounded-xl border-2 p-5 transition hover:border-emerald-300 {{ old('kebijakan_tenggat', $pengaturan->kebijakan_tenggat) === 'hari_ini' ? 'border-emerald-500 bg-emerald-50/40 ring-2 ring-emerald-100' : 'border-slate-200 bg-white' }}">
                    <div class="flex items-start gap-4">
                        <input
                            type="radio"
                            name="kebijakan_tenggat"
                            value="hari_ini"
                            class="mt-1 text-emerald-600 focus:ring-emerald-500 h-4 w-4"
                            {{ old('kebijakan_tenggat', $pengaturan->kebijakan_tenggat) === 'hari_ini' ? 'checked' : '' }}
                        >
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-slate-900 text-sm sm:text-base">Fleksibel: Bebas Sepanjang Hari H</span>
                            </div>
                            <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                                Guru dapat mengisi jurnal kapan saja sepanjang hari mengajar yang bersangkutan (00:00 hingga 23:59 WIB). Guru yang memiliki jadwal pagi tetap bisa mengisi jurnal di siang atau sore hari.
                            </p>
                            <div class="mt-2 flex items-center gap-2 text-[11px] font-semibold text-amber-700">
                                <i class="bi bi-calendar-event"></i>
                                <span>Cocok jika guru merekap seluruh jurnal setelah jam pelajaran usai.</span>
                            </div>
                        </div>
                    </div>
                    <div class="shrink-0 self-end sm:self-center">
                        <span class="inline-flex items-center gap-1 rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600">
                            <i class="bi bi-calendar-day"></i> Hingga 23:59 WIB
                        </span>
                    </div>
                </label>

                {{-- Opsi 3: Longgar (longgar) --}}
                <label class="relative flex cursor-pointer flex-col sm:flex-row items-start sm:items-center justify-between gap-4 rounded-xl border-2 p-5 transition hover:border-emerald-300 {{ old('kebijakan_tenggat', $pengaturan->kebijakan_tenggat) === 'longgar' ? 'border-emerald-500 bg-emerald-50/40 ring-2 ring-emerald-100' : 'border-slate-200 bg-white' }}">
                    <div class="flex items-start gap-4">
                        <input
                            type="radio"
                            name="kebijakan_tenggat"
                            value="longgar"
                            class="mt-1 text-emerald-600 focus:ring-emerald-500 h-4 w-4"
                            {{ old('kebijakan_tenggat', $pengaturan->kebijakan_tenggat) === 'longgar' ? 'checked' : '' }}
                        >
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-slate-900 text-sm sm:text-base">Longgar: Hari H dan Toleransi H-1 (Kemarin)</span>
                            </div>
                            <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                                Memberikan batas toleransi susulan pengisian jurnal hingga H-1 (kemarin). Guru yang berhalangan mengisi jurnal kemarin masih diperbolehkan mengisi hari ini.
                            </p>
                            <div class="mt-2 flex items-center gap-2 text-[11px] font-semibold text-blue-700">
                                <i class="bi bi-info-circle"></i>
                                <span>Pengisian tanggal H-2 ke belakang atau tanggal masa depan tetap ditolak.</span>
                            </div>
                        </div>
                    </div>
                    <div class="shrink-0 self-end sm:self-center">
                        <span class="inline-flex items-center gap-1 rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600">
                            <i class="bi bi-hourglass-split"></i> Maksimal H-1
                        </span>
                    </div>
                </label>
            </div>

            {{-- Metadata Info --}}
            <div class="mt-8 pt-5 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-xs text-slate-500">
                <div class="flex items-center gap-1.5">
                    <i class="bi bi-person-badge"></i>
                    <span>Terakhir diubah oleh: <strong>{{ $pengaturan->pengubah->name ?? 'Administrator' }}</strong></span>
                </div>
                <div>
                    <span>Waktu pembaruan: <strong>{{ $pengaturan->updated_at ? $pengaturan->updated_at->translatedFormat('d F Y, H:i') . ' WIB' : 'Pengaturan Bawaan' }}</strong></span>
                </div>
            </div>
        </div>

        {{-- Tombol Simpan --}}
        <div class="flex items-center justify-end gap-3">
            <button
                type="submit"
                class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
            >
                <i class="bi bi-check2-circle text-lg"></i>
                Simpan Kebijakan Jurnal
            </button>
        </div>
    </form>
</div>
@endsection
