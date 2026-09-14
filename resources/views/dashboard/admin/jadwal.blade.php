@extends('layouts.app')

@section('title', 'Dashboard Admin - Jadwal Pelajaran')

@section('sidebar')
    @include('layouts.admin.sidebar')
@endsection

@section('navbar')
    @include('layouts.admin.navbar')
@endsection

@section('content')
<div class="p-6 sm:p-10 font-sans">

    <!-- Notifikasi Flash Message -->
    @if(session('success'))
        <div class="mb-6 flex items-center justify-between rounded-lg bg-emerald-50 border border-emerald-200 p-4 text-sm text-emerald-800">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 rounded-lg bg-red-50 border border-red-200 p-4 text-sm text-red-800">
            <div class="font-semibold mb-1">Terjadi kesalahan input:</div>
            <ul class="list-disc list-inside space-y-1 text-red-700">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $seninKegiatan = $jadwals->where('hari', 'Senin')->first(function($item) {
            return str_contains(strtolower($item->mapel), 'upacara');
        });
        $isSeninMaju = $seninKegiatan && $seninKegiatan->status === 'ditiadakan';

        $jumatKegiatan = $jadwals->where('hari', 'Jumat')->first(function($item) {
            return str_contains(strtolower($item->mapel), 'pembiasaan');
        });
        $isJumatMaju = $jumatKegiatan && $jumatKegiatan->status === 'ditiadakan';
    @endphp

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Manajemen Jadwal Pelajaran</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola pembagian jam efektif, sesi Upacara & Pembiasaan Jum'at, jarak waktu jam pelajaran, dan mode jam maju.</p>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <!-- Kolom Kiri: Form Tambah Jadwal -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 sticky top-6">
                <div class="mb-5 flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#155d50] text-white shadow-sm">
                        <i class="bi bi-calendar3 text-lg"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-slate-800">Tambah Jadwal</h2>
                        <p class="text-xs text-slate-500">Atur sesi & jam pelajaran per hari</p>
                    </div>
                </div>

                <form action="{{ route('dashboard.jadwal.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700">Pilih Kelas <span class="text-red-500">*</span></label>
                        <select id="formAddKelasId" name="kelas_id" required class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-[#155d50] focus:ring-2 focus:ring-[#155d50]/10 cursor-pointer">
                            @foreach($kelases as $k)
                                <option value="{{ $k->id_kelas }}" {{ (old('kelas_id', optional($selectedKelas)->id_kelas) == $k->id_kelas) ? 'selected' : '' }}>
                                    {{ $k->nama_kelas }} (Wali: {{ $k->wali_kelas ?? 'Belum ada' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700">Pilih Hari <span class="text-red-500">*</span></label>
                            <select id="formAddHari" name="hari" required class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-[#155d50] focus:ring-2 focus:ring-[#155d50]/10 cursor-pointer">
                                @foreach($hariList as $hari)
                                    <option value="{{ $hari }}" {{ old('hari', $selectedHari ?? 'Senin') == $hari ? 'selected' : '' }}>{{ $hari }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700">Jam Pelajaran Ke- <span class="text-red-500">*</span></label>
                            <select id="formAddJamKe" name="jam_ke" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-[#155d50] focus:ring-2 focus:ring-[#155d50]/10 cursor-pointer">
                                <option value="0" {{ old('jam_ke') === '0' ? 'selected' : '' }}>Jam Ke-0 (Khusus Kegiatan / Istirahat / Upacara)</option>
                                @for($i = 1; $i <= 13; $i++)
                                    <option value="{{ $i }}" {{ old('jam_ke') == $i ? 'selected' : '' }}>Jam Ke-{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700">Jam Mulai <span class="text-red-500">*</span></label>
                            <input type="time" id="formAddJamMulai" name="jam_mulai" value="{{ old('jam_mulai', '07:30') }}" required class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-[#155d50] focus:ring-2 focus:ring-[#155d50]/10" />
                        </div>

                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700">Jam Selesai <span class="text-red-500">*</span></label>
                            <input type="time" id="formAddJamSelesai" name="jam_selesai" value="{{ old('jam_selesai', '09:00') }}" required class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-[#155d50] focus:ring-2 focus:ring-[#155d50]/10" />
                        </div>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700">Mata Pelajaran / Kegiatan <span class="text-red-500">*</span></label>
                        <select name="mapel_id" id="formAddMapelId" required onchange="handleMapelSelection(this, 'add')" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-[#155d50] focus:ring-2 focus:ring-[#155d50]/10 cursor-pointer">
                            <option value="">-- Pilih Mata Pelajaran / Kegiatan --</option>
                            @if($mapels->where('kategori', 'kegiatan')->isNotEmpty())
                                <optgroup label="--- Kegiatan Khusus Sekolah (Upacara, Istirahat, Pembiasaan) ---">
                                    @foreach($mapels->where('kategori', 'kegiatan') as $m)
                                        <option value="{{ $m->id }}" data-kategori="kegiatan" {{ old('mapel_id') == $m->id ? 'selected' : '' }}>{{ $m->nama_mapel }}</option>
                                    @endforeach
                                </optgroup>
                            @endif
                            <optgroup label="--- Mapel Jurusan ---">
                                @foreach($mapels->where('kategori', 'jurusan') as $m)
                                    <option value="{{ $m->id }}" data-kategori="jurusan" {{ old('mapel_id') == $m->id ? 'selected' : '' }}>{{ $m->nama_mapel }}</option>
                                @endforeach
                            </optgroup>
                            <optgroup label="--- Mapel Biasa ---">
                                @foreach($mapels->whereNotIn('kategori', ['jurusan', 'kegiatan']) as $m)
                                    <option value="{{ $m->id }}" data-kategori="biasa" {{ old('mapel_id') == $m->id ? 'selected' : '' }}>{{ $m->nama_mapel }}</option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700">Guru Pengampu <span class="text-xs text-slate-400 font-normal">(Wajib untuk mapel, opsional untuk kegiatan)</span></label>
                        <select name="guru_id" id="formAddGuruId" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-[#155d50] focus:ring-2 focus:ring-[#155d50]/10 cursor-pointer">
                            <option value="">-- Tanpa Guru / Khusus Kegiatan --</option>
                            @foreach($gurus as $g)
                                <option value="{{ $g->id }}" {{ old('guru_id') == $g->id ? 'selected' : '' }}>{{ $g->name }} ({{ $g->nip ?? 'Guru' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="w-full rounded-lg bg-[#155d50] px-4 py-3 text-sm font-semibold text-white shadow-sm transition duration-200 hover:bg-[#0b2b24] cursor-pointer inline-flex items-center justify-center gap-2">
                        <i class="bi bi-plus-circle"></i>
                        <span>Simpan Jadwal Pelajaran</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Kolom Kanan: Tampilan Interaktif Per Hari & Tabel Jadwal -->
        <div class="lg:col-span-2 space-y-4">

            <!-- Banner Mode Jam Maju Khusus Hari Senin (Upacara) -->
            <div id="bannerSenin" class="{{ $selectedHari === 'Senin' ? '' : 'hidden' }}">
                @if($isSeninMaju)
                    <div class="rounded-xl border border-emerald-300 bg-emerald-50 p-4 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-emerald-600 text-white shadow-sm text-base font-bold">
                                <i class="bi bi-lightning-charge-fill"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-emerald-900">Mode Jam Maju Aktif (Upacara Bendera Ditiadakan)</h3>
                                <p class="text-xs text-emerald-700 mt-0.5">Seluruh jam pelajaran hari Senin telah dimajukan 40 menit (mulai 07:00). Jam pulang 40 menit lebih awal.</p>
                            </div>
                        </div>
                        <button type="button" onclick="openShiftModal('Senin', 'normal', 'Upacara Bendera')" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-xs font-semibold shadow-sm transition cursor-pointer shrink-0">
                            <i class="bi bi-arrow-counterclockwise"></i>
                            <span>Kembalikan Jam Normal</span>
                        </button>
                    </div>
                @else
                    <div class="rounded-xl border border-amber-200 bg-amber-50/80 p-4 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-amber-500 text-white shadow-sm text-base">
                                <i class="bi bi-flag-fill"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-amber-900">Upacara Bendera: Status Normal (Dilaksanakan)</h3>
                                <p class="text-xs text-amber-700 mt-0.5">Upacara terjadwal pukul 07:00 – 07:40. Pelajaran Jam Ke-2 dimulai pukul 07:40.</p>
                            </div>
                        </div>
                        <button type="button" onclick="openShiftModal('Senin', 'maju', 'Upacara Bendera')" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-xs font-semibold shadow-sm transition cursor-pointer shrink-0">
                            <i class="bi bi-lightning-charge-fill"></i>
                            <span>Upacara Ditiadakan (Majukan Jam 40 Menit)</span>
                        </button>
                    </div>
                @endif
            </div>

            <!-- Banner Mode Jam Maju Khusus Hari Jum'at (Pembiasaan) -->
            <div id="bannerJumat" class="{{ $selectedHari === 'Jumat' ? '' : 'hidden' }}">
                @if($isJumatMaju)
                    <div class="rounded-xl border border-emerald-300 bg-emerald-50 p-4 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-emerald-600 text-white shadow-sm text-base font-bold">
                                <i class="bi bi-lightning-charge-fill"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-emerald-900">Mode Jam Maju Aktif (Pembiasaan Jum'at Ditiadakan)</h3>
                                <p class="text-xs text-emerald-700 mt-0.5">Seluruh jam pelajaran hari Jum'at telah dimajukan 30 menit (mulai 07:00). Jam pulang 30 menit lebih awal.</p>
                            </div>
                        </div>
                        <button type="button" onclick="openShiftModal('Jumat', 'normal', 'Pembiasaan Jum\'at')" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-xs font-semibold shadow-sm transition cursor-pointer shrink-0">
                            <i class="bi bi-arrow-counterclockwise"></i>
                            <span>Kembalikan Jam Normal</span>
                        </button>
                    </div>
                @else
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50/70 p-4 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-[#155d50] text-white shadow-sm text-base">
                                <i class="bi bi-heart-pulse-fill"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-800">Pembiasaan Jum'at: Status Normal (Dilaksanakan)</h3>
                                <p class="text-xs text-slate-600 mt-0.5">Ibadah / Dhuha / Senam terjadwal pukul 07:00 – 07:30. Pelajaran Jam Ke-1 dimulai pukul 07:30.</p>
                            </div>
                        </div>
                        <button type="button" onclick="openShiftModal('Jumat', 'maju', 'Pembiasaan Jum\'at')" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-[#155d50] hover:bg-[#0b2b24] text-white rounded-lg text-xs font-semibold shadow-sm transition cursor-pointer shrink-0">
                            <i class="bi bi-lightning-charge-fill"></i>
                            <span>Pembiasaan Ditiadakan (Majukan Jam 30 Menit)</span>
                        </button>
                    </div>
                @endif
            </div>

            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                
                <!-- Header Card & Filter Kelas -->
                <div class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between bg-white">
                    <div>
                        <h2 class="text-lg font-bold text-slate-800">Daftar Jadwal: <span class="text-emerald-700">{{ optional($selectedKelas)->nama_kelas ?? 'Pilih Kelas' }}</span></h2>
                        <p class="text-xs text-slate-500 mt-0.5">Wali Kelas: {{ optional($selectedKelas)->wali_kelas ?? '-' }}</p>
                    </div>

                    <form method="GET" action="{{ route('dashboard.jadwal') }}" class="w-full sm:w-auto">
                        <input type="hidden" name="hari" id="filterInputHari" value="{{ $selectedHari ?? 'Senin' }}">
                        <label class="sr-only">Pilih Kelas</label>
                        <select name="kelas_id" onchange="this.form.submit()" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 outline-none transition focus:border-[#155d50] focus:ring-2 focus:ring-[#155d50]/10 sm:w-56 cursor-pointer">
                            @foreach($kelases as $k)
                                <option value="{{ $k->id_kelas }}" {{ optional($selectedKelas)->id_kelas == $k->id_kelas ? 'selected' : '' }}>
                                    Kelas {{ $k->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>

                <!-- Navigasi Tab Hari Interaktif (Senin - Jumat & Semua) -->
                <div class="flex flex-wrap items-center gap-1.5 border-b border-slate-100 bg-slate-50/80 p-3">
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider mr-1 text-[11px]">Pilih Hari:</span>
                    @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $hari)
                        @php
                            $countHari = $jadwals->where('hari', $hari)->count();
                        @endphp
                        <button type="button" 
                                onclick="switchHariTab('{{ $hari }}')" 
                                id="tab-btn-{{ $hari }}"
                                class="day-tab-btn px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all flex items-center gap-1.5 cursor-pointer border border-transparent">
                            <span>{{ $hari }}</span>
                            <span id="tab-badge-{{ $hari }}" class="day-badge px-1.5 py-0.2 rounded-full text-[10px] font-bold">
                                {{ $countHari }}
                            </span>
                        </button>
                    @endforeach
                    <button type="button" 
                            onclick="switchHariTab('Semua')" 
                            id="tab-btn-Semua"
                            class="day-tab-btn px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all flex items-center gap-1.5 cursor-pointer border border-transparent">
                        <span>Semua Hari</span>
                        <span id="tab-badge-Semua" class="day-badge px-1.5 py-0.2 rounded-full text-[10px] font-bold">
                            {{ $jadwals->count() }}
                        </span>
                    </button>
                </div>

                <!-- Info Header Hari Aktif -->
                <div class="px-5 py-3 bg-emerald-50/50 border-b border-emerald-100/60 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-clock-history text-[#155d50] text-sm"></i>
                        <span id="activeHariTitle" class="text-xs font-bold text-slate-700">Jadwal Hari {{ $selectedHari ?? 'Senin' }}</span>
                        <span id="activeHariSubtitle" class="text-xs text-slate-500"></span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-slate-500">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white border border-slate-200 text-[11px] text-slate-600 shadow-2xs">
                            <i class="bi bi-sliders text-[#155d50]"></i> Atur jam pelajaran & kegiatan bebas secara manual
                        </span>
                    </div>
                </div>

                <!-- Tabel Jadwal Pelajaran -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-gray-50/70">
                            <tr>
                                <th class="px-4 py-3.5 text-center text-xs font-bold text-slate-500 uppercase tracking-wider w-28">Sesi</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Waktu Pelaksanaan</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Mata Pelajaran / Kegiatan</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Guru Pengampu</th>
                                <th class="px-5 py-3.5 text-center text-xs font-bold text-slate-500 uppercase tracking-wider w-24">Aksi</th>
                            </tr>
                        </thead>

                        <tbody id="jadwalTableBody" class="divide-y divide-slate-100 bg-white text-sm text-slate-700">
                            @forelse($jadwals as $j)
                                @php
                                    $startSec = strtotime($j->jam_mulai);
                                    $endSec = strtotime($j->jam_selesai);
                                    $durationMinutes = ($startSec && $endSec) ? max(0, round(($endSec - $startSec) / 60)) : 0;
                                    $isIstirahat = str_contains(strtolower($j->mapel), 'istirahat');
                                    $isUpacara = str_contains(strtolower($j->mapel), 'upacara');
                                    $isPembiasaan = str_contains(strtolower($j->mapel), 'pembiasaan');
                                    $isMbg = str_contains(strtolower($j->mapel), 'mbg');
                                    $isKegiatan = $isIstirahat || $isUpacara || $isPembiasaan || $isMbg || optional($j->mapelItem)->kategori === 'kegiatan' || $j->jam_ke == 0;
                                    $isDitiadakan = $j->status === 'ditiadakan';
                                @endphp

                                <tr class="schedule-row transition {{ $isDitiadakan ? 'bg-red-50/30 opacity-75' : ($isIstirahat ? 'bg-amber-50/40 hover:bg-amber-50/80' : 'hover:bg-emerald-50/20') }}" data-hari="{{ $j->hari }}">
                                    <!-- Kolom Jam Ke / Sesi -->
                                    <td class="px-4 py-4 text-center">
                                        <div class="inline-flex flex-col items-center">
                                            @if($isDitiadakan)
                                                <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-red-100 text-red-700 border border-red-200 whitespace-nowrap">
                                                    Ditiadakan
                                                </span>
                                            @elseif($isIstirahat)
                                                <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300 whitespace-nowrap inline-flex items-center gap-1">
                                                    <i class="bi bi-cup-hot-fill text-amber-600"></i> Istirahat
                                                </span>
                                            @elseif($isUpacara)
                                                <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300 whitespace-nowrap inline-flex items-center gap-1">
                                                    <i class="bi bi-flag-fill text-amber-600"></i> Upacara
                                                </span>
                                            @elseif($isPembiasaan)
                                                <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-emerald-100 text-[#155d50] border border-emerald-300 whitespace-nowrap inline-flex items-center gap-1">
                                                    <i class="bi bi-heart-pulse-fill text-[#155d50]"></i> Pembiasaan
                                                </span>
                                            @elseif($isKegiatan || $j->jam_ke == 0)
                                                <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300 whitespace-nowrap">
                                                    Kegiatan
                                                </span>
                                            @else
                                                <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-[#155d50]/10 text-[#155d50] border border-[#155d50]/20 whitespace-nowrap">
                                                    Jam Ke-{{ $j->jam_ke }}
                                                </span>
                                            @endif
                                            <span class="text-[10px] text-slate-400 mt-1 font-semibold">{{ $j->hari }}</span>
                                        </div>
                                    </td>

                                    <!-- Kolom Waktu & Durasi -->
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-2">
                                            <span class="font-mono text-xs font-bold {{ $isDitiadakan ? 'line-through text-slate-400' : ($isIstirahat ? 'text-amber-900 bg-amber-100/60 border-amber-200' : 'text-slate-800 bg-slate-100 border-slate-200') }} px-2 py-1 rounded border">
                                                {{ date('H:i', strtotime($j->jam_mulai)) }} - {{ date('H:i', strtotime($j->jam_selesai)) }}
                                            </span>
                                            @if(!$isDitiadakan)
                                                <span class="text-[11px] {{ $isIstirahat ? 'text-amber-700 font-medium' : 'text-slate-500' }}">
                                                    ({{ $durationMinutes }} Menit)
                                                </span>
                                            @else
                                                <span class="text-[11px] text-red-600 font-semibold">(Sesi Maju)</span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Kolom Mapel / Kegiatan -->
                                    <td class="px-5 py-4">
                                        <div class="font-semibold text-slate-900 flex items-center gap-2">
                                            @if($isDitiadakan)
                                                <span class="line-through text-slate-400">{{ $j->mapel }}</span>
                                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-red-100 text-red-700 border border-red-200">Ditiadakan (Jam Maju)</span>
                                            @else
                                                <span class="{{ $isIstirahat ? 'text-amber-950 font-bold' : '' }}">{{ $j->mapel }}</span>
                                                @if($isIstirahat)
                                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-amber-100 text-amber-800 border border-amber-200">Waktu Istirahat</span>
                                                @elseif($isKegiatan)
                                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-amber-100 text-amber-800 border border-amber-200">Kegiatan Sekolah</span>
                                                @elseif(optional($j->mapelItem)->kategori === 'jurusan')
                                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-blue-50 text-blue-700 border border-blue-200">Jurusan</span>
                                                @else
                                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">Biasa</span>
                                                @endif
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Kolom Guru Pengampu -->
                                    <td class="px-5 py-4">
                                        @if($j->guru)
                                            <div class="text-sm font-medium text-emerald-900">
                                                {{ $j->guru->name }}
                                            </div>
                                            @if($j->guru->nip)
                                                <div class="text-[11px] font-mono text-slate-400">NIP: {{ $j->guru->nip }}</div>
                                            @endif
                                        @else
                                            <div class="text-xs text-slate-400 italic">Tanpa Guru / Khusus Kegiatan</div>
                                        @endif
                                    </td>

                                    <!-- Kolom Aksi -->
                                    <td class="px-5 py-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <!-- Tombol Edit -->
                                            <button type="button" onclick="openEditModal(this)"
                                                    data-id="{{ $j->id_jadwal }}"
                                                    data-kelas-id="{{ $j->id_kelas }}"
                                                    data-hari="{{ $j->hari }}"
                                                    data-jam-ke="{{ $j->jam_ke }}"
                                                    data-mapel-id="{{ $j->id_mapel }}"
                                                    data-guru-id="{{ $j->id_user ?? '' }}"
                                                    data-jam-mulai="{{ date('H:i', strtotime($j->jam_mulai)) }}"
                                                    data-jam-selesai="{{ date('H:i', strtotime($j->jam_selesai)) }}"
                                                    class="text-gray-400 hover:text-amber-600 transition p-1 cursor-pointer" title="Edit Jadwal & Waktu">
                                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                                </svg>
                                            </button>
                                            <!-- Tombol Hapus (Nonaktifkan) -->
                                            <button type="button" onclick="openDeleteModal('{{ $j->id_jadwal }}', '{{ addslashes($j->mapel) }} ({{ $j->hari }} - {{ $j->jam_ke > 0 ? 'Jam Ke-' . $j->jam_ke : 'Kegiatan' }})')"
                                                    class="text-gray-400 hover:text-red-600 transition p-1 cursor-pointer" title="Nonaktifkan Jadwal">
                                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr id="rowEmptyAll">
                                    <td colspan="5" class="px-5 py-12 text-center text-slate-400 text-sm">
                                        <i class="bi bi-calendar-x text-3xl mb-2 block text-slate-300"></i>
                                        Belum ada jadwal pelajaran untuk kelas ini.<br>
                                        <span class="text-xs text-slate-400">Silakan tambahkan jadwal baru menggunakan form di sebelah kiri.</span>
                                    </td>
                                </tr>
                            @endforelse

                            <!-- Row kosong jika filter hari tidak ada sesi -->
                            <tr id="rowEmptyFiltered" class="hidden">
                                <td colspan="5" class="px-5 py-12 text-center text-slate-400 text-sm">
                                    <i class="bi bi-calendar-minus text-3xl mb-2 block text-slate-300"></i>
                                    Belum ada jadwal pelajaran untuk hari <strong id="emptyDayName"></strong> pada kelas ini.<br>
                                    <button type="button" onclick="setAddFormDay()" class="mt-3 inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#155d50] text-white rounded-lg text-xs font-semibold hover:bg-[#0b2b24] transition">
                                        <i class="bi bi-plus-lg"></i>
                                        <span>Tambah Jadwal untuk Hari <span id="btnDayName"></span></span>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="bg-gray-50 p-4 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-center text-sm text-gray-500 gap-2">
                    <span id="footerCountText">Total <strong>{{ $jadwals->count() }}</strong> sesi pelajaran aktif</span>
                    <span class="text-xs text-gray-400">Senin &bull; Selasa &bull; Rabu &bull; Kamis &bull; Jumat</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= MODAL EDIT JADWAL ================= --}}
    <div id="modalEditJadwal" class="fixed inset-0 z-50 hidden items-center justify-center overflow-y-auto bg-gray-900/50 p-4 backdrop-blur-sm">
        <div role="dialog" aria-modal="true" aria-labelledby="modalEditJadwalTitle" class="relative w-full max-w-lg rounded-xl border border-gray-100 bg-white p-6 shadow-xl">
            <div class="mb-5 flex items-center justify-between border-b border-gray-100 pb-4">
                <div>
                    <h2 id="modalEditJadwalTitle" class="text-lg font-bold text-gray-900">Edit Jadwal Pelajaran</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Perbarui urutan jam ke, jarak waktu, mapel, atau guru pengampu</p>
                </div>
                <button type="button" onclick="closeEditModal()" aria-label="Tutup modal" class="text-gray-400 transition hover:text-gray-600 p-1">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="formEditJadwal" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="editKelasId" class="mb-1.5 block text-sm font-medium text-gray-700">Pilih Kelas <span class="text-red-500">*</span></label>
                    <select id="editKelasId" name="kelas_id" required class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-400/20 cursor-pointer">
                        @foreach($kelases as $k)
                            <option value="{{ $k->id_kelas }}">{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div>
                        <label for="editHari" class="mb-1.5 block text-sm font-medium text-gray-700">Pilih Hari <span class="text-red-500">*</span></label>
                        <select id="editHari" name="hari" required class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-400/20 cursor-pointer">
                            @foreach($hariList as $hari)
                                <option value="{{ $hari }}">{{ $hari }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="editJamKe" class="mb-1.5 block text-sm font-medium text-gray-700">Jam Pelajaran Ke- <span class="text-red-500">*</span></label>
                        <select id="editJamKe" name="jam_ke" required class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-400/20 cursor-pointer">
                            <option value="0">Jam Ke-0 (Khusus Kegiatan / Istirahat / Upacara)</option>
                            @for($i = 1; $i <= 13; $i++)
                                <option value="{{ $i }}">Jam Ke-{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div>
                        <label for="editJamMulai" class="mb-1.5 block text-sm font-medium text-gray-700">Jam Mulai <span class="text-red-500">*</span></label>
                        <input id="editJamMulai" name="jam_mulai" type="time" required class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-400/20">
                    </div>
                    <div>
                        <label for="editJamSelesai" class="mb-1.5 block text-sm font-medium text-gray-700">Jam Selesai <span class="text-red-500">*</span></label>
                        <input id="editJamSelesai" name="jam_selesai" type="time" required class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-400/20">
                    </div>
                </div>

                <div>
                    <label for="editMapelId" class="mb-1.5 block text-sm font-medium text-gray-700">Mata Pelajaran / Kegiatan <span class="text-red-500">*</span></label>
                    <select id="editMapelId" name="mapel_id" required onchange="handleMapelSelection(this, 'edit')" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-400/20 cursor-pointer">
                        <option value="">-- Pilih Mata Pelajaran / Kegiatan --</option>
                        @if($mapels->where('kategori', 'kegiatan')->isNotEmpty())
                            <optgroup label="--- Kegiatan Khusus Sekolah (Upacara, Istirahat, Pembiasaan) ---">
                                @foreach($mapels->where('kategori', 'kegiatan') as $m)
                                    <option value="{{ $m->id }}" data-kategori="kegiatan">{{ $m->nama_mapel }}</option>
                                @endforeach
                            </optgroup>
                        @endif
                        <optgroup label="--- Mapel Jurusan ---">
                            @foreach($mapels->where('kategori', 'jurusan') as $m)
                                <option value="{{ $m->id }}" data-kategori="jurusan">{{ $m->nama_mapel }}</option>
                            @endforeach
                        </optgroup>
                        <optgroup label="--- Mapel Biasa ---">
                            @foreach($mapels->whereNotIn('kategori', ['jurusan', 'kegiatan']) as $m)
                                <option value="{{ $m->id }}" data-kategori="biasa">{{ $m->nama_mapel }}</option>
                            @endforeach
                        </optgroup>
                    </select>
                </div>

                <div>
                    <label for="editGuruId" class="mb-1.5 block text-sm font-medium text-gray-700">Guru Pengampu <span class="text-xs text-slate-400 font-normal">(Wajib untuk mapel, opsional untuk kegiatan)</span></label>
                    <select id="editGuruId" name="guru_id" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-400/20 cursor-pointer">
                        <option value="">-- Tanpa Guru / Khusus Kegiatan --</option>
                        @foreach($gurus as $g)
                            <option value="{{ $g->id }}">{{ $g->name }} ({{ $g->nip ?? 'Guru' }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end gap-3 border-t border-gray-100 pt-5">
                    <button type="button" onclick="closeEditModal()" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 cursor-pointer">Batal</button>
                    <button type="submit" class="rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-emerald-700 cursor-pointer inline-flex items-center gap-2">
                        <i class="bi bi-check2-circle"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ================= MODAL HAPUS / NONAKTIFKAN JADWAL ================= --}}
    <div id="modalHapusJadwal" class="fixed inset-0 z-50 hidden bg-gray-900/60 backdrop-blur-sm overflow-y-auto h-full w-full items-center justify-center transition-opacity p-4">
        <div class="bg-white rounded-xl shadow-xl border border-gray-100 p-6 sm:p-8 max-w-md w-full text-center relative">
            
            <button type="button" onclick="closeDeleteModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition p-1">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </div>
            
            <h2 class="text-xl font-bold text-gray-900 mb-2">Nonaktifkan Jadwal Pelajaran?</h2>
            <p class="text-sm text-gray-500 mb-4">
                Apakah Anda yakin ingin menonaktifkan jadwal <span id="hapusJadwalTitle" class="font-bold text-gray-800"></span>? Jadwal ini akan dinonaktifkan di sistem tanpa menghapus permanen riwayat data.
            </p>

            <form id="formHapusJadwal" method="POST" class="text-left space-y-4">
                @csrf
                @method('DELETE')

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alasan Penonaktifan <span class="text-red-500">*</span></label>
                    <textarea name="alasan" rows="3" required placeholder="Tuliskan alasan menonaktifkan jadwal ini..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm"></textarea>
                </div>
                
                <div class="flex justify-center gap-3 pt-4">
                    <button type="button" onclick="closeDeleteModal()" class="px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition text-sm font-medium w-full cursor-pointer">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg transition shadow-sm text-sm font-medium w-full cursor-pointer">Ya, Nonaktifkan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ================= MODAL SHIFT TIME (MODE JAM MAJU / NORMAL) ================= --}}
    <div id="modalShiftTime" class="fixed inset-0 z-50 hidden bg-gray-900/60 backdrop-blur-sm overflow-y-auto h-full w-full items-center justify-center transition-opacity p-4">
        <div class="bg-white rounded-xl shadow-xl border border-gray-100 p-6 sm:p-8 max-w-md w-full text-center relative">
            
            <button type="button" onclick="closeShiftModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition p-1">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            <div id="shiftIconBg" class="w-16 h-16 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold">
                <i id="shiftIcon" class="bi bi-lightning-charge-fill"></i>
            </div>
            
            <h2 id="shiftModalTitle" class="text-xl font-bold text-gray-900 mb-2">Konfirmasi Penyesuaian Jadwal</h2>
            <p id="shiftModalDesc" class="text-sm text-gray-600 mb-5 leading-relaxed">
                Deskripsi aksi pergeseran waktu...
            </p>

            <form action="{{ route('dashboard.jadwal.shift-time') }}" method="POST" class="text-left space-y-4">
                @csrf
                <input type="hidden" name="kelas_id" id="shiftKelasId" value="{{ optional($selectedKelas)->id_kelas }}">
                <input type="hidden" name="hari" id="shiftHari" value="Senin">
                <input type="hidden" name="mode" id="shiftMode" value="maju">
                <input type="hidden" name="minutes" id="shiftMinutes" value="45">

                <div class="bg-slate-50 border border-slate-200 rounded-lg p-3">
                    <label class="flex items-center gap-2.5 cursor-pointer text-xs font-semibold text-slate-700">
                        <input type="checkbox" name="apply_all" value="1" class="rounded border-slate-300 text-[#155d50] focus:ring-[#155d50] h-4 w-4">
                        <span>Terapkan perubahan untuk SEMUA KELAS di sekolah</span>
                    </label>
                    <p class="text-[11px] text-slate-400 ml-6 mt-0.5">Jika dicentang, seluruh kelas serentak menerapkan penyesuaian jadwal ini.</p>
                </div>
                
                <div class="flex justify-center gap-3 pt-3">
                    <button type="button" onclick="closeShiftModal()" class="px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition text-sm font-medium w-full cursor-pointer">Batal</button>
                    <button type="submit" id="shiftSubmitBtn" class="px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-lg transition shadow-sm text-sm font-semibold w-full cursor-pointer">
                        Lanjutkan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
    let currentSelectedHari = "{{ $selectedHari ?? 'Senin' }}";

    function switchHariTab(hari) {
        currentSelectedHari = hari;

        // Update hidden input filter
        const filterInput = document.getElementById('filterInputHari');
        if (filterInput) filterInput.value = hari;

        // Tampilkan/sembunyikan banner mode jam maju khusus Senin & Jumat
        const bannerSenin = document.getElementById('bannerSenin');
        const bannerJumat = document.getElementById('bannerJumat');
        if (bannerSenin) {
            if (hari === 'Senin') bannerSenin.classList.remove('hidden');
            else bannerSenin.classList.add('hidden');
        }
        if (bannerJumat) {
            if (hari === 'Jumat') bannerJumat.classList.remove('hidden');
            else bannerJumat.classList.add('hidden');
        }

        // Update styling tombol tab aktif
        const allButtons = document.querySelectorAll('.day-tab-btn');
        allButtons.forEach(btn => {
            btn.classList.remove('bg-[#155d50]', 'text-white', 'shadow-sm');
            btn.classList.add('bg-white', 'text-slate-600', 'hover:bg-slate-100', 'border-slate-200');
        });

        const allBadges = document.querySelectorAll('.day-badge');
        allBadges.forEach(badge => {
            badge.classList.remove('bg-white/25', 'text-white');
            badge.classList.add('bg-slate-100', 'text-slate-600');
        });

        const activeBtn = document.getElementById('tab-btn-' + hari);
        const activeBadge = document.getElementById('tab-badge-' + hari);
        if (activeBtn) {
            activeBtn.classList.remove('bg-white', 'text-slate-600', 'hover:bg-slate-100', 'border-slate-200');
            activeBtn.classList.add('bg-[#155d50]', 'text-white', 'shadow-sm');
        }
        if (activeBadge) {
            activeBadge.classList.remove('bg-slate-100', 'text-slate-600');
            activeBadge.classList.add('bg-white/25', 'text-white');
        }

        // Filter baris dalam tabel
        const rows = document.querySelectorAll('.schedule-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const rowHari = row.getAttribute('data-hari');
            if (hari === 'Semua' || rowHari === hari) {
                row.classList.remove('hidden');
                visibleCount++;
            } else {
                row.classList.add('hidden');
            }
        });

        // Tampilkan/sembunyikan empty state
        const emptyFiltered = document.getElementById('rowEmptyFiltered');
        const emptyDayName = document.getElementById('emptyDayName');
        const btnDayName = document.getElementById('btnDayName');

        if (visibleCount === 0 && rows.length > 0) {
            if (emptyFiltered) {
                emptyFiltered.classList.remove('hidden');
                if (emptyDayName) emptyDayName.textContent = hari;
                if (btnDayName) btnDayName.textContent = hari;
            }
        } else {
            if (emptyFiltered) emptyFiltered.classList.add('hidden');
        }

        // Update judul header & footer teks
        const titleEl = document.getElementById('activeHariTitle');
        const subTitleEl = document.getElementById('activeHariSubtitle');
        const footerText = document.getElementById('footerCountText');

        if (titleEl) {
            titleEl.textContent = hari === 'Semua' ? 'Semua Jadwal Pelajaran' : 'Jadwal Hari ' + hari;
        }
        if (subTitleEl) {
            subTitleEl.textContent = '(' + visibleCount + ' Sesi Jam Pelajaran)';
        }
        if (footerText) {
            footerText.innerHTML = 'Menampilkan <strong>' + visibleCount + '</strong> sesi pelajaran aktif';
        }

        // Sinkronkan form Tambah Jadwal jika bukan 'Semua'
        if (hari !== 'Semua') {
            const addHariSelect = document.getElementById('formAddHari');
            if (addHariSelect) addHariSelect.value = hari;
        }
    }

    function setAddFormDay() {
        if (currentSelectedHari !== 'Semua') {
            const addHariSelect = document.getElementById('formAddHari');
            if (addHariSelect) addHariSelect.value = currentSelectedHari;
        }
        const addForm = document.getElementById('formAddHari');
        if (addForm) {
            addForm.focus();
            addForm.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    function openShiftModal(hari, mode, namaKegiatan) {
        document.getElementById('shiftHari').value = hari;
        document.getElementById('shiftMode').value = mode;

        const minutes = (hari === 'Jumat') ? 30 : 40;
        const normalRange = (hari === 'Jumat') ? '07:00 – 07:30' : '07:00 – 07:40';
        const normalStartJam1 = (hari === 'Jumat') ? '07:30' : '07:40';

        const minutesInput = document.getElementById('shiftMinutes');
        if (minutesInput) minutesInput.value = minutes;

        const titleEl = document.getElementById('shiftModalTitle');
        const descEl = document.getElementById('shiftModalDesc');
        const btnEl = document.getElementById('shiftSubmitBtn');
        const iconBg = document.getElementById('shiftIconBg');
        const icon = document.getElementById('shiftIcon');

        if (mode === 'maju') {
            titleEl.textContent = 'Aktifkan Mode Jam Maju (' + namaKegiatan + ' Ditiadakan)?';
            descEl.innerHTML = 'Kegiatan <strong>' + namaKegiatan + '</strong> akan ditandai ditiadakan. Seluruh jam pelajaran hari ' + hari + ' otomatis <strong>dimajukan ' + minutes + ' menit</strong> (mulai pukul 07:00), dan waktu pulang siswa otomatis <strong>maju ' + minutes + ' menit lebih awal</strong>.';
            btnEl.textContent = 'Ya, Aktifkan Jam Maju';
            btnEl.className = 'px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-lg transition shadow-sm text-sm font-semibold w-full cursor-pointer';
            iconBg.className = 'w-16 h-16 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold';
            icon.className = 'bi bi-lightning-charge-fill';
        } else {
            titleEl.textContent = 'Kembalikan ke Jadwal Normal (' + namaKegiatan + ' Dilaksanakan)?';
            descEl.innerHTML = 'Kegiatan <strong>' + namaKegiatan + '</strong> akan diaktifkan kembali pukul ' + normalRange + '. Seluruh jam pelajaran hari ' + hari + ' otomatis <strong>dikembalikan ke waktu normal</strong> (Jam Ke-1 mulai ' + normalStartJam1 + ').';
            btnEl.textContent = 'Ya, Kembalikan ke Normal';
            btnEl.className = 'px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-lg transition shadow-sm text-sm font-semibold w-full cursor-pointer';
            iconBg.className = 'w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold';
            icon.className = 'bi bi-arrow-counterclockwise';
        }

        const modal = document.getElementById('modalShiftTime');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeShiftModal() {
        const modal = document.getElementById('modalShiftTime');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
    }

    function handleMapelSelection(selectElement, type) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const kategori = selectedOption ? selectedOption.getAttribute('data-kategori') : '';
        const text = selectedOption ? selectedOption.textContent.toLowerCase() : '';

        const jamKeEl = (type === 'add') ? document.getElementById('formAddJamKe') : document.getElementById('editJamKe');
        const guruEl = (type === 'add') ? document.getElementById('formAddGuruId') : document.getElementById('editGuruId');

        if (kategori === 'kegiatan' || text.includes('istirahat') || text.includes('upacara') || text.includes('pembiasaan')) {
            if (jamKeEl) jamKeEl.value = '0';
            if (guruEl) guruEl.value = '';
        }
    }

    function openEditModal(button) {
        const id = button.getAttribute('data-id');
        const kelasId = button.getAttribute('data-kelas-id');
        const hari = button.getAttribute('data-hari');
        const jamKe = button.getAttribute('data-jam-ke');
        const mapelId = button.getAttribute('data-mapel-id');
        const guruId = button.getAttribute('data-guru-id');
        const jamMulai = button.getAttribute('data-jam-mulai');
        const jamSelesai = button.getAttribute('data-jam-selesai');

        const form = document.getElementById('formEditJadwal');
        form.action = "{{ url('dashboard/jadwal') }}/" + id;

        if (kelasId) document.getElementById('editKelasId').value = kelasId;
        if (hari) document.getElementById('editHari').value = hari;
        if (jamKe !== null && jamKe !== undefined) document.getElementById('editJamKe').value = jamKe;
        if (mapelId) document.getElementById('editMapelId').value = mapelId;
        if (guruId !== null && guruId !== undefined && guruId !== '') {
            document.getElementById('editGuruId').value = guruId;
        } else {
            document.getElementById('editGuruId').value = '';
        }
        if (jamMulai) document.getElementById('editJamMulai').value = jamMulai;
        if (jamSelesai) document.getElementById('editJamSelesai').value = jamSelesai;

        const modal = document.getElementById('modalEditJadwal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeEditModal() {
        const modal = document.getElementById('modalEditJadwal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
    }

    function openDeleteModal(id, title) {
        const form = document.getElementById('formHapusJadwal');
        form.action = "{{ url('dashboard/jadwal') }}/" + id;

        document.getElementById('hapusJadwalTitle').textContent = title;

        const modal = document.getElementById('modalHapusJadwal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden'; 
    }

    function closeDeleteModal() {
        const modal = document.getElementById('modalHapusJadwal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto'; 
    }

    // Inisialisasi tab aktif saat pertama kali dimuat
    document.addEventListener('DOMContentLoaded', function() {
        switchHariTab(currentSelectedHari);
    });

    // Tutup modal jika klik di luar box
    window.addEventListener('click', function(e) {
        const editModal = document.getElementById('modalEditJadwal');
        const deleteModal = document.getElementById('modalHapusJadwal');
        const shiftModal = document.getElementById('modalShiftTime');
        if (e.target === editModal) closeEditModal();
        if (e.target === deleteModal) closeDeleteModal();
        if (e.target === shiftModal) closeShiftModal();
    });
</script>
@endsection
