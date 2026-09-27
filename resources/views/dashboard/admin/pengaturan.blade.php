@extends('layouts.app')

@section('title', 'Pengaturan Sistem - JurnalKita')

@section('sidebar')
    @include('layouts.admin.sidebar')
@endsection

@section('navbar')
    @include('layouts.admin.navbar')
@endsection

@section('content')
<div class="mx-auto max-w-7xl space-y-6 p-6 font-sans sm:p-10 lg:p-8 xl:p-10">

    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4 text-sm text-emerald-800 flex items-center gap-3 shadow-xs">
            <i class="bi bi-check-circle-fill text-emerald-600 text-lg"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('info'))
        <div class="rounded-xl bg-sky-50 border border-sky-200 p-4 text-sm text-sky-800 flex items-center gap-3 shadow-xs">
            <i class="bi bi-info-circle-fill text-sky-600 text-lg"></i>
            <span class="font-medium">{{ session('info') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-xl bg-red-50 border border-red-200 p-4 text-sm text-red-800 shadow-xs">
            <div class="font-semibold mb-1 flex items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill text-red-600"></i>
                <span>Terjadi kesalahan input:</span>
            </div>
            <ul class="list-disc list-inside space-y-1 text-red-700 pl-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <!-- KARTU UTAMA: KEBIJAKAN TENGGAT WAKTU PENGISIAN JURNAL GURU -->
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <!-- Header Card -->
    <div class="p-5 sm:p-6 border-b border-slate-100 bg-slate-50/50">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-emerald-100 text-[#0d6e59] flex items-center justify-center text-xl font-bold shrink-0">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <div>
                <h2 class="text-base sm:text-lg font-bold text-slate-900">Kebijakan Tenggat Waktu Pengisian Jurnal</h2>
                <p class="text-xs text-slate-500 mt-0.5">Atur batasan waktu bagi guru dalam menginput jurnal harian mengajar.</p>
            </div>
        </div>
    </div>

    <!-- Form Konten -->
    <form action="{{ route('admin.pengaturan.update') }}" method="POST" class="p-5 sm:p-6 space-y-6">
        @csrf
        <input type="hidden" name="action_type" value="tenggat">

        <!-- Pilihan 3 Opsi Kebijakan Tenggat Waktu (Radio Cards) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            <!-- OPSI 1: TERBATAS JAM -->
            <label class="relative flex flex-col justify-between p-4 rounded-xl border-2 cursor-pointer transition-all hover:border-emerald-500 {{ $tenggatOpsi === 'terbatas_jam' ? 'border-emerald-600 bg-emerald-50/50 shadow-xs' : 'border-slate-200 bg-white' }}">
                <div class="space-y-2">
                    <div class="flex items-center justify-between gap-2">
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">
                            STANDAR SISTEM
                        </span>
                        <input type="radio" name="tenggat_opsi" value="terbatas_jam" {{ $tenggatOpsi === 'terbatas_jam' ? 'checked' : '' }} class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 border-slate-300">
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="bi bi-clock-fill text-emerald-600 text-base"></i>
                        <h3 class="text-sm font-bold text-slate-900">Terbatas Jam Mengajar</h3>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Guru <strong>hanya bisa mengisi</strong> pada saat jam mengajarnya sedang berlangsung.
                    </p>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-200/80 text-[11px] font-semibold text-emerald-900 flex items-center gap-1.5">
                    <i class="bi bi-shield-check"></i>
                    <span>Disiplin & Realtime</span>
                </div>
            </label>

            <!-- OPSI 2: HARI INI -->
            <label class="relative flex flex-col justify-between p-4 rounded-xl border-2 cursor-pointer transition-all hover:border-teal-500 {{ $tenggatOpsi === 'hari_ini' ? 'border-teal-600 bg-teal-50/50 shadow-xs' : 'border-slate-200 bg-white' }}">
                <div class="space-y-2">
                    <div class="flex items-center justify-between gap-2">
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-teal-100 text-teal-800 border border-teal-300">
                            FLEKSIBEL HARIAN
                        </span>
                        <input type="radio" name="tenggat_opsi" value="hari_ini" {{ $tenggatOpsi === 'hari_ini' ? 'checked' : '' }} class="w-4 h-4 text-teal-600 focus:ring-teal-500 border-slate-300">
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="bi bi-calendar-day-fill text-teal-600 text-base"></i>
                        <h3 class="text-sm font-bold text-slate-900">Terbatas Harian</h3>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Guru bisa mengisi jurnal kapan saja selama <strong>masih di hari yang sama</strong> (hingga pukul 23:59 WIB).
                    </p>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-200/80 text-[11px] font-semibold text-teal-900 flex items-center gap-1.5">
                    <i class="bi bi-check-circle"></i>
                    <span>Bebas Jam, Wajib Hari Ini</span>
                </div>
            </label>

            <!-- OPSI 3: LOS / BEBAS -->
            <label class="relative flex flex-col justify-between p-4 rounded-xl border-2 cursor-pointer transition-all hover:border-amber-500 {{ $tenggatOpsi === 'los' ? 'border-amber-600 bg-amber-50/50 shadow-xs' : 'border-slate-200 bg-white' }}">
                <div class="space-y-2">
                    <div class="flex items-center justify-between gap-2">
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-amber-100 text-amber-900 border border-amber-300">
                            SUSULAN DIIZINKAN
                        </span>
                        <input type="radio" name="tenggat_opsi" value="los" {{ $tenggatOpsi === 'los' ? 'checked' : '' }} class="w-4 h-4 text-amber-600 focus:ring-amber-500 border-slate-300">
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="bi bi-unlock-fill text-amber-600 text-base"></i>
                        <h3 class="text-sm font-bold text-slate-900">Bebas (Maksimal Kemarin)</h3>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Guru diperbolehkan mengisi jurnal untuk <strong>hari ini atau kemarin (H-1)</strong> jika lupa.
                    </p>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-200/80 text-[11px] font-semibold text-amber-900 flex items-center gap-1.5">
                    <i class="bi bi-info-circle"></i>
                    <span>Hari Ini & Kemarin (H-1)</span>
                </div>
            </label>

        </div>

        <!-- Tombol Aksi -->
        <div class="flex justify-end pt-4 border-t border-slate-100">
            <button type="submit" class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold text-sm transition shadow-xs cursor-pointer">
                <i class="bi bi-floppy-fill"></i>
                <span>Simpan Kebijakan Tenggat Waktu</span>
            </button>
        </div>
    </form>
</div>


<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col justify-between">
    <div class="p-6">
        <!-- Header Card -->
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-teal-100 text-[#0d6e59] flex items-center justify-center text-lg font-bold">
                <i class="bi bi-toggle2-on"></i>
            </div>
            <div>
                <h2 class="text-base font-bold text-slate-900">Kontrol Mode Jam Maju Saat Ini</h2>
                <p class="text-xs text-slate-500">Aktifkan atau kembalikan jadwal normal seluruh kelas secara langsung di sini.</p>
            </div>
        </div>

        <!-- Layout Grid Berdampingan (Senin & Jumat) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <!-- Kontrol Hari Senin -->
            <div class="p-4 rounded-xl border {{ $isSeninMaju ? 'border-emerald-300 bg-emerald-50/70' : 'border-slate-200 bg-white' }} space-y-3 transition flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-sm font-bold text-slate-900">Hari Senin (Upacara Bendera)</h3>
                        @if($isSeninMaju)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-200 text-emerald-900 border border-emerald-300">
                                MODE MAJU AKTIF ({{ $seninShiftedMinutes }} Menit)
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-100 text-slate-700 border border-slate-300">
                                STATUS NORMAL
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-600 mt-2">
                        @if($isSeninMaju)
                            Upacara ditandai ditiadakan. Seluruh jam pelajaran hari Senin telah dimajukan {{ $seninShiftedMinutes }} menit (Jam Ke-2 mulai 07:00).
                        @else
                            Upacara terjadwal normal pukul 07:00 – 07:40. Pelajaran Jam Ke-2 mulai 07:40.
                        @endif
                    </p>
                </div>

                <form action="{{ route('dashboard.jadwal.shift-time') }}" method="POST" class="pt-2">
                    @csrf
                    <input type="hidden" name="hari" value="Senin">
                    <input type="hidden" name="minutes" value="{{ $shiftSenin }}">

                    @if($isSeninMaju)
                        <input type="hidden" name="mode" value="normal">
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-semibold text-xs transition shadow-xs cursor-pointer">
                            <i class="bi bi-arrow-counterclockwise"></i>
                            <span>Kembalikan ke Jadwal Normal (Upacara Dilaksanakan)</span>
                        </button>
                    @else
                        <input type="hidden" name="mode" value="maju">
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-semibold text-xs transition shadow-xs cursor-pointer">
                            <i class="bi bi-lightning-charge-fill"></i>
                            <span>Aktifkan Jam Maju (Ditiadakan - Majukan {{ $shiftSenin }} Menit)</span>
                        </button>
                    @endif
                </form>
            </div>

            <!-- Kontrol Hari Jumat -->
            <div class="p-4 rounded-xl border {{ $isJumatMaju ? 'border-emerald-300 bg-emerald-50/70' : 'border-slate-200 bg-white' }} space-y-3 transition flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-sm font-bold text-slate-900">Hari Jum'at (Pembiasaan)</h3>
                        @if($isJumatMaju)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-200 text-emerald-900 border border-emerald-300">
                                MODE MAJU AKTIF ({{ $jumatShiftedMinutes }} Menit)
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-100 text-slate-700 border border-slate-300">
                                STATUS NORMAL
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-600 mt-2">
                        @if($isJumatMaju)
                            Pembiasaan ditandai ditiadakan. Seluruh jam pelajaran hari Jum'at telah dimajukan {{ $jumatShiftedMinutes }} menit (Jam Ke-1 mulai 07:00).
                        @else
                            Pembiasaan terjadwal normal pukul 07:00 – 07:30. Pelajaran Jam Ke-1 mulai 07:30.
                        @endif
                    </p>
                </div>

                <form action="{{ route('dashboard.jadwal.shift-time') }}" method="POST" class="pt-2">
                    @csrf
                    <input type="hidden" name="hari" value="Jumat">
                    <input type="hidden" name="minutes" value="{{ $shiftJumat }}">

                    @if($isJumatMaju)
                        <input type="hidden" name="mode" value="normal">
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-semibold text-xs transition shadow-xs cursor-pointer">
                            <i class="bi bi-arrow-counterclockwise"></i>
                            <span>Kembalikan ke Jadwal Normal (Pembiasaan Dilaksanakan)</span>
                        </button>
                    @else
                        <input type="hidden" name="mode" value="maju">
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-[#155d50] hover:bg-[#0b2b24] text-white font-semibold text-xs transition shadow-xs cursor-pointer">
                            <i class="bi bi-lightning-charge-fill"></i>
                            <span>Aktifkan Jam Maju (Ditiadakan - Majukan {{ $shiftJumat }} Menit)</span>
                        </button>
                    @endif
                </form>
            </div>

        </div>
    </div>
</div>

    </div>

</div>

@endsection
