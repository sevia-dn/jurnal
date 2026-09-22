@extends('layouts.app')

@section('title', 'Pengaturan Sistem - JurnalKita')

@section('sidebar')
    @include('layouts.admin.sidebar')
@endsection

@section('navbar')
    @include('layouts.admin.navbar')
@endsection

@section('content')
<div class="p-6 sm:p-10 font-sans max-w-6xl mx-auto space-y-6">

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

    <!-- Header Halaman -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200 pb-5">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-emerald-700 mb-1">
                <i class="bi bi-gear-fill"></i>
                <span>Pengaturan Sistem</span>
                <span class="text-slate-400">/</span>
                <span class="text-slate-600">Kebijakan & Jadwal</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">Pengaturan Sistem</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola kebijakan batas waktu pengisian jurnal guru dan penyesuaian jam pelajaran sekolah.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard.jadwal') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition shadow-xs">
                <i class="bi bi-calendar-week"></i>
                <span>Lihat Jadwal Pelajaran</span>
            </a>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- KARTU UTAMA: KEBIJAKAN TENGGAT WAKTU PENGISIAN JURNAL GURU (ON/OFF & 3 OPSI) -->
    <!-- ========================================================================= -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 sm:p-7 border-b border-slate-100">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-[#0d6e59] flex items-center justify-center text-2xl font-bold shrink-0">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-lg font-bold text-slate-900">Kebijakan Tenggat Waktu Pengisian Jurnal</h2>
                            @if($tenggatStatus == 1)
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                    <i class="bi bi-shield-check"></i> AKTIF
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-900 border border-amber-300">
                                    <i class="bi bi-shield-slash"></i> NONAKTIF (MODE BEBAS)
                                </span>
                            @endif
                        </div>
                        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                            Kontrol hak akses pengisian logbook guru: kunci tepat waktu sesuai jam, fleksibel harian, atau buka mode susulan jika ada guru yang lupa.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.pengaturan.update') }}" method="POST" class="p-6 sm:p-7 space-y-6">
            @csrf
            <input type="hidden" name="action_type" value="tenggat">

            <!-- Switch Master ON / OFF -->
            <div class="p-4 rounded-xl border {{ $tenggatStatus == 1 ? 'border-emerald-200 bg-emerald-50/40' : 'border-amber-200 bg-amber-50/40' }} flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition-colors">
                <div class="flex items-start sm:items-center gap-3">
                    <div class="w-10 h-10 rounded-xl {{ $tenggatStatus == 1 ? 'bg-emerald-600 text-white' : 'bg-amber-500 text-white' }} flex items-center justify-center shrink-0 text-lg shadow-xs">
                        <i class="bi {{ $tenggatStatus == 1 ? 'bi-toggle-on' : 'bi-toggle-off' }}"></i>
                    </div>
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider {{ $tenggatStatus == 1 ? 'text-emerald-800' : 'text-amber-800' }}">Status Pembatasan Waktu</span>
                        <h3 class="text-sm font-bold text-slate-900">
                            {{ $tenggatStatus == 1 ? 'Tenggat Waktu Diaktifkan (Ketat / Fleksibel Sesuai Opsi)' : 'Tenggat Waktu Dinonaktifkan (Mode Bebas / Susulan Terbuka)' }}
                        </h3>
                        <p class="text-xs text-slate-600 mt-0.5">
                            Jika dinonaktifkan, guru dapat mengisi jurnal untuk <strong>hari ini atau kemarin (H-1)</strong> tanpa terkunci jam mengajar.
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3.5 self-end sm:self-center shrink-0">
                    <span class="text-xs font-extrabold px-3 py-1 rounded-full select-none transition-colors {{ $tenggatStatus == 1 ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700' }}" id="labelToggleTenggat">
                        {{ $tenggatStatus == 1 ? 'AKTIF' : 'NONAKTIF' }}
                    </span>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="tenggat_status" value="1" {{ $tenggatStatus == 1 ? 'checked' : '' }} class="sr-only peer" id="toggleTenggat">
                        <div class="w-14 h-7 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[4px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-emerald-600"></div>
                    </label>
                </div>
            </div>

            <!-- Pilihan 3 Opsi Kebijakan Tenggat Waktu (Radio Cards) -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-3">
                    Pilih Aturan Kebijakan Pengisian Jurnal Guru <span class="text-rose-500">*</span>
                </label>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                    <!-- OPSI 1: TERBATAS JAM -->
                    <label class="relative flex flex-col justify-between p-4 rounded-xl border-2 cursor-pointer transition-all hover:border-emerald-500 {{ $tenggatOpsi === 'terbatas_jam' ? 'border-emerald-600 bg-emerald-50/50 shadow-xs' : 'border-slate-200 bg-white' }}">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                    STANDAR SISTEM
                                </span>
                                <input type="radio" name="tenggat_opsi" value="terbatas_jam" {{ $tenggatOpsi === 'terbatas_jam' ? 'checked' : '' }} class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 border-slate-300">
                            </div>
                            <div class="flex items-center gap-2 mb-1.5">
                                <i class="bi bi-clock-fill text-emerald-600 text-base"></i>
                                <h4 class="text-sm font-bold text-slate-900">Terbatas Jam Mengajar</h4>
                            </div>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                Guru <strong>hanya bisa mengisi</strong> pada saat jam mengajarnya sedang berlangsung (disertai toleransi keterlambatan).
                            </p>
                        </div>
                        <div class="mt-3 pt-3 border-t border-slate-200/80 text-[11px] font-semibold text-emerald-900 flex items-center gap-1.5">
                            <i class="bi bi-shield-check"></i>
                            <span>Disiplin Ketat & Realtime</span>
                        </div>
                    </label>

                    <!-- OPSI 2: HARI INI -->
                    <label class="relative flex flex-col justify-between p-4 rounded-xl border-2 cursor-pointer transition-all hover:border-teal-500 {{ $tenggatOpsi === 'hari_ini' ? 'border-teal-600 bg-teal-50/50 shadow-xs' : 'border-slate-200 bg-white' }}">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-teal-100 text-teal-800 border border-teal-300">
                                    FLEKSIBEL HARIAN
                                </span>
                                <input type="radio" name="tenggat_opsi" value="hari_ini" {{ $tenggatOpsi === 'hari_ini' ? 'checked' : '' }} class="w-4 h-4 text-teal-600 focus:ring-teal-500 border-slate-300">
                            </div>
                            <div class="flex items-center gap-2 mb-1.5">
                                <i class="bi bi-calendar-day-fill text-teal-600 text-base"></i>
                                <h4 class="text-sm font-bold text-slate-900">Yang Penting Hari Itu</h4>
                            </div>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                Guru bebas mengisi jurnal kapan saja selama <strong>masih di hari yang sama</strong> (hingga pukul 23:59 WIB).
                            </p>
                        </div>
                        <div class="mt-3 pt-3 border-t border-slate-200/80 text-[11px] font-semibold text-teal-900 flex items-center gap-1.5">
                            <i class="bi bi-check-circle"></i>
                            <span>Bebas Jam, Wajib Hari Ini</span>
                        </div>
                    </label>

                    <!-- OPSI 3: LOS / BEBAS -->
                    <label class="relative flex flex-col justify-between p-4 rounded-xl border-2 cursor-pointer transition-all hover:border-amber-500 {{ $tenggatOpsi === 'los' ? 'border-amber-600 bg-amber-50/50 shadow-xs' : 'border-slate-200 bg-white' }}">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-amber-100 text-amber-900 border border-amber-300">
                                    SUSULAN DIIZINKAN
                                </span>
                                <input type="radio" name="tenggat_opsi" value="los" {{ $tenggatOpsi === 'los' ? 'checked' : '' }} class="w-4 h-4 text-amber-600 focus:ring-amber-500 border-slate-300">
                            </div>
                            <div class="flex items-center gap-2 mb-1.5">
                                <i class="bi bi-unlock-fill text-amber-600 text-base"></i>
                                <h4 class="text-sm font-bold text-slate-900">Mode Bebas / Los (Maksimal Kemarin)</h4>
                            </div>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                Guru diperbolehkan mengisi jurnal untuk <strong>hari ini atau kemarin (H-1)</strong> jika lupa. Tanggal masa depan dilarang.
                            </p>
                        </div>
                        <div class="mt-3 pt-3 border-t border-slate-200/80 text-[11px] font-semibold text-amber-900 flex items-center gap-1.5">
                            <i class="bi bi-info-circle"></i>
                            <span>Hari Ini & Kemarin (H-1)</span>
                        </div>
                    </label>

                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm transition shadow-xs cursor-pointer">
                    <i class="bi bi-floppy-fill"></i>
                    <span>Simpan Kebijakan Tenggat Waktu</span>
                </button>
            </div>
        </form>
    </div>

    <!-- ========================================================================= -->
    <!-- GRID 2 KOLOM: PENGATURAN DURASI PEMAJUAN JAM & KONTROL MODE JAM MAJU -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- KARTU 1: PENGATURAN DURASI MENIT PEMAJUAN -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col justify-between">
            <div class="p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-lg font-bold">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Durasi Pemajuan Jam</h2>
                        <p class="text-xs text-slate-500">Tentukan berapa menit jam pelajaran dimajukan saat kegiatan ditiadakan.</p>
                    </div>
                </div>

                <form action="{{ route('admin.pengaturan.update') }}" method="POST" class="space-y-5 pt-2">
                    @csrf
                    <input type="hidden" name="action_type" value="durasi_shift">

                    <!-- Setting Hari Senin -->
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/60 space-y-2">
                        <div class="flex items-center justify-between">
                            <label for="shift_senin_minutes" class="text-sm font-bold text-slate-800 flex items-center gap-2">
                                <i class="bi bi-flag-fill text-amber-600"></i>
                                <span>Hari Senin (Upacara Bendera)</span>
                            </label>
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-amber-100 text-amber-800">
                                Normal: 07:00 – 07:40
                            </span>
                        </div>
                        <p class="text-xs text-slate-500">Jika upacara ditiadakan (hujan, dsb), jam pelajaran berikutnya otomatis dimajukan:</p>
                        <div class="flex items-center gap-3 pt-1">
                            <div class="relative w-36">
                                <input type="number" name="shift_senin_minutes" id="shift_senin_minutes"
                                    value="{{ old('shift_senin_minutes', $shiftSenin) }}"
                                    min="5" max="180" required
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-bold text-slate-900 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                            </div>
                            <span class="text-sm font-semibold text-slate-700">Menit</span>
                            <span class="text-xs text-slate-400">(Default: 40 menit)</span>
                        </div>
                    </div>

                    <!-- Setting Hari Jumat -->
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/60 space-y-2">
                        <div class="flex items-center justify-between">
                            <label for="shift_jumat_minutes" class="text-sm font-bold text-slate-800 flex items-center gap-2">
                                <i class="bi bi-heart-pulse-fill text-emerald-600"></i>
                                <span>Hari Jum'at (Pembiasaan / Dhuha)</span>
                            </label>
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">
                                Normal: 07:00 – 07:30
                            </span>
                        </div>
                        <p class="text-xs text-slate-500">Jika pembiasaan ditiadakan, jam pelajaran berikutnya otomatis dimajukan:</p>
                        <div class="flex items-center gap-3 pt-1">
                            <div class="relative w-36">
                                <input type="number" name="shift_jumat_minutes" id="shift_jumat_minutes"
                                    value="{{ old('shift_jumat_minutes', $shiftJumat) }}"
                                    min="5" max="180" required
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-bold text-slate-900 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                            </div>
                            <span class="text-sm font-semibold text-slate-700">Menit</span>
                            <span class="text-xs text-slate-400">(Default: 30 menit)</span>
                        </div>
                    </div>

                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm transition shadow-xs cursor-pointer">
                        <i class="bi bi-floppy-fill"></i>
                        <span>Simpan Pengaturan Durasi</span>
                    </button>
                </form>
            </div>
            <div class="px-6 py-3 bg-slate-50 border-t border-slate-100 text-xs text-slate-500">
                Otomatis disinkronkan ke seluruh <strong>{{ $totalKelas }} kelas</strong> di sekolah.
            </div>
        </div>

        <!-- KARTU 2: KONTROL CEPAT STATUS JAM MAJU (SEKOLAH SERENTAK) -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col justify-between">
            <div class="p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-teal-100 text-[#0d6e59] flex items-center justify-center text-lg font-bold">
                        <i class="bi bi-toggle2-on"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Kontrol Mode Jam Maju Saat Ini</h2>
                        <p class="text-xs text-slate-500">Aktifkan atau kembalikan jadwal normal seluruh kelas secara langsung di sini.</p>
                    </div>
                </div>

                <div class="space-y-4 pt-2">

                    <!-- Kontrol Hari Senin -->
                    <div class="p-4 rounded-xl border {{ $isSeninMaju ? 'border-emerald-300 bg-emerald-50/70' : 'border-slate-200 bg-white' }} space-y-3 transition">
                        <div class="flex items-start justify-between gap-3">
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
                                <p class="text-xs text-slate-600 mt-1">
                                    @if($isSeninMaju)
                                        Upacara ditandai ditiadakan. Seluruh jam pelajaran hari Senin telah dimajukan {{ $seninShiftedMinutes }} menit (Jam Ke-2 mulai 07:00).
                                    @else
                                        Upacara terjadwal normal pukul 07:00 – 07:40. Pelajaran Jam Ke-2 mulai 07:40.
                                    @endif
                                </p>
                            </div>
                        </div>

                        <form action="{{ route('dashboard.jadwal.shift-time') }}" method="POST" class="pt-1">
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
                                    <span>Aktifkan Jam Maju (Ditiadakan - Majukan {{ $shiftSenin }} Menit untuk Seluruh Kelas)</span>
                                </button>
                            @endif
                        </form>
                    </div>

                    <!-- Kontrol Hari Jumat -->
                    <div class="p-4 rounded-xl border {{ $isJumatMaju ? 'border-emerald-300 bg-emerald-50/70' : 'border-slate-200 bg-white' }} space-y-3 transition">
                        <div class="flex items-start justify-between gap-3">
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
                                <p class="text-xs text-slate-600 mt-1">
                                    @if($isJumatMaju)
                                        Pembiasaan ditandai ditiadakan. Seluruh jam pelajaran hari Jum'at telah dimajukan {{ $jumatShiftedMinutes }} menit (Jam Ke-1 mulai 07:00).
                                    @else
                                        Pembiasaan terjadwal normal pukul 07:00 – 07:30. Pelajaran Jam Ke-1 mulai 07:30.
                                    @endif
                                </p>
                            </div>
                        </div>

                        <form action="{{ route('dashboard.jadwal.shift-time') }}" method="POST" class="pt-1">
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
                                    <span>Aktifkan Jam Maju (Ditiadakan - Majukan {{ $shiftJumat }} Menit untuk Seluruh Kelas)</span>
                                </button>
                            @endif
                        </form>
                    </div>

                </div>
            </div>
            <div class="px-6 py-3 bg-slate-50 border-t border-slate-100 text-xs text-slate-500">
                Status jadwal di halaman Pengaturan ini terpusat dan berlaku serentak untuk seluruh sekolah.
            </div>
        </div>

    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggle = document.getElementById('toggleTenggat');
        const label = document.getElementById('labelToggleTenggat');
        if (toggle && label) {
            toggle.addEventListener('change', function() {
                if (this.checked) {
                    label.textContent = 'AKTIF';
                    label.className = 'text-xs font-extrabold px-3 py-1 rounded-full select-none transition-colors bg-emerald-100 text-emerald-800';
                } else {
                    label.textContent = 'NONAKTIF';
                    label.className = 'text-xs font-extrabold px-3 py-1 rounded-full select-none transition-colors bg-slate-200 text-slate-700';
                }
            });
        }
    });
</script>
@endsection
