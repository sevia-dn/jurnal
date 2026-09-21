@extends('layouts.app')

@section('title', 'Dashboard Admin - Rekap Jurnal')

@section('sidebar')
    @include('layouts.admin.sidebar')
@endsection

@section('navbar')
    @include('layouts.admin.navbar')
@endsection

@section('content')
<div class="p-6 sm:p-10 font-sans">
    
    <!-- Notifikasi Alert -->
    @if(session('success'))
        <div class="mb-6 flex items-center justify-between rounded-xl bg-emerald-50 border border-emerald-200 p-4 text-sm text-emerald-800 shadow-xs">
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 p-1 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="mb-6 rounded-xl bg-red-50 border border-red-200 p-4 text-sm text-red-800 shadow-xs">
            <div class="font-bold mb-1">Terjadi kesalahan:</div>
            <ul class="list-disc list-inside space-y-1 text-red-700 text-xs">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
      $namaBulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
      ];
      $currentBulanNama = $namaBulan[$bulan] ?? 'Bulan ' . $bulan;
    @endphp

    <!-- Header Halaman -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
      <div>
        <h1 class="text-2xl font-extrabold text-slate-900">Rekap Jurnal Mengajar</h1>
        <p class="text-sm text-slate-500 mt-1">Pantau riwayat mengajar guru yang telah lalu, status keterisian jurnal, dan jam kosong.</p>
      </div>
      <div class="flex flex-wrap items-center gap-2.5">
        <!-- Badge Periode Terpilih -->
        <div class="inline-flex items-center gap-2 px-3.5 py-2 bg-emerald-50 border border-emerald-200 rounded-full text-xs font-bold text-emerald-800 shadow-xs">
          <i class="bi bi-calendar-check text-emerald-700"></i>
          @if(($periode ?? 'harian') === 'bulanan')
            <span>Periode: {{ $currentBulanNama }} {{ $tahun }}</span>
          @else
            <span>{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d M Y') }}</span>
          @endif
        </div>
        <!-- Badge Jam Real-time -->
        <div class="inline-flex items-center gap-2 px-3.5 py-2 bg-white border border-slate-200 rounded-full text-xs font-bold text-[#155d50] shadow-xs">
          <i class="bi bi-clock"></i>
          <span id="liveClock">{{ now()->format('H:i') }} WIB</span>
        </div>
      </div>
    </div>

    <!-- Banner Jadwal Piket & Waka Backup Hari Ini -->
    <div class="mb-6 bg-linear-to-r from-emerald-50 via-teal-50 to-white border border-emerald-200 rounded-2xl p-4 sm:p-5 shadow-xs">
      <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div class="flex items-start sm:items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-[#155d50] text-white flex items-center justify-center shrink-0 shadow-xs">
            <i class="bi bi-shield-check text-xl"></i>
          </div>
          <div>
            <div class="flex items-center gap-2">
              <h3 class="text-sm font-bold text-slate-800">Petugas Piket Hari Ini ({{ $namaHari ?? 'Hari Ini' }})</h3>
              <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">Aktif</span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">Jadwal piket guru & waka yang bertugas mengawasi presensi dan ketertiban sekolah.</p>
          </div>
        </div>

        <div class="flex flex-wrap items-center gap-3 text-xs">
          <!-- Guru Piket -->
          <div class="bg-white px-3.5 py-2 rounded-xl border border-slate-200 shadow-2xs flex items-center gap-2">
            <span class="text-slate-400 font-semibold">Guru Piket:</span>
            <span class="font-bold text-slate-800">{{ optional(optional($piketGuru)->user)->name ?? 'Belum Diatur' }}</span>
          </div>

          <!-- Waka Piket -->
          <div class="bg-white px-3.5 py-2 rounded-xl border border-slate-200 shadow-2xs flex items-center gap-2">
            <span class="text-slate-400 font-semibold">Waka Piket:</span>
            <span class="font-bold text-emerald-800">{{ optional(optional($piketWaka)->user)->name ?? 'Belum Diatur' }}</span>
          </div>

          <!-- Waka Backup -->
          <div class="bg-amber-50 px-3.5 py-2 rounded-xl border border-amber-200 shadow-2xs flex items-center gap-1.5" title="Jika waka utama berhalangan, waka lain otomatis dapat membackup validasi/dispensasi">
            <i class="bi bi-people-fill text-amber-700"></i>
            <span class="text-amber-800 font-semibold">Backup Waka:</span>
            <span class="font-bold text-amber-900">
              @if(isset($backupWakas) && $backupWakas->count() > 0)
                {{ $backupWakas->pluck('name')->join(', ') }}
              @else
                Siap untuk semua Waka
              @endif
            </span>
          </div>

          <!-- Tombol Atur Penugasan Piket & Waka -->
          <button type="button" onclick="openModal('modalKelolaPiket')" class="px-3.5 py-2 bg-[#155d50] hover:bg-[#0f463c] text-white rounded-xl shadow-2xs font-bold transition flex items-center gap-1.5 cursor-pointer">
            <i class="bi bi-calendar2-week"></i>
            <span>Atur Penugasan Piket</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Alert Notifikasi Permohonan Dispensasi (Hanya muncul jika ada request) -->
    @if(isset($requestDispensasi) && $requestDispensasi->count() > 0)
      <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3 rounded-2xl bg-amber-50 border border-amber-300 p-4 text-xs sm:text-sm text-amber-900 shadow-xs">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-xl bg-amber-200 text-amber-800 flex items-center justify-center text-lg shrink-0">
            <i class="bi bi-bell-fill"></i>
          </div>
          <div>
            <span class="font-bold block text-slate-800">Terdapat {{ $requestDispensasi->count() }} Permohonan Dispensasi Siswa Menunggu Approval</span>
            <span class="text-xs text-amber-700">Permohonan dispensasi siswa yang diajukan hari ini membutuhkan approval guru piket & waka.</span>
          </div>
        </div>
        <a href="{{ url('/dashboard/piket/dispensasi') }}" class="px-3.5 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-bold shadow-xs transition !no-underline flex items-center gap-1 self-start sm:self-auto">
          <span>Tinjau Dispensasi</span>
          <i class="bi bi-arrow-right"></i>
        </a>
      </div>
    @endif

    <!-- 5 Kartu Statistik -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5 mb-6">
      
      <!-- Card 1: Total Kelas & Progress Lapor -->
      <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col justify-between hover:border-[#155d50]/40 transition">
        <div class="flex justify-between items-start gap-3">
          <div>
            <div class="text-[11px] uppercase tracking-wider font-bold text-slate-400">TOTAL KELAS</div>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2">{{ $totalKelas }}</div>
          </div>
          <div class="w-9 h-9 rounded-lg bg-[#155d50]/10 text-[#155d50] flex items-center justify-center text-base shadow-xs">
            <i class="bi bi-building"></i>
          </div>
        </div>
        <div class="mt-4">
          <div class="text-xs font-semibold text-[#155d50] flex justify-between">
            <span>{{ $kelasLapor }} / {{ $totalKelas }} Lapor</span>
            <span class="text-slate-400 font-normal">{{ $totalKelas > 0 ? round(($kelasLapor / $totalKelas) * 100) : 0 }}%</span>
          </div>
          <div class="w-full h-2 bg-[#155d50]/10 rounded-full overflow-hidden mt-2">
            <div class="h-full bg-[#155d50] rounded-full transition-all duration-500" style="width: {{ $totalKelas > 0 ? min(round(($kelasLapor / $totalKelas) * 100), 100) : 0 }}%"></div>
          </div>
        </div>
      </div>

      <!-- Card 2: Sesi Terisi (Jurnal Terisi) -->
      <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col justify-between hover:border-emerald-400 transition">
        <div class="flex justify-between items-start gap-3">
          <div>
            <div class="text-[11px] uppercase tracking-wider font-bold text-slate-400">SESI TERISI</div>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2">{{ $totalTerisi }}</div>
          </div>
          <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-base shadow-xs">
            <i class="bi bi-journal-check"></i>
          </div>
        </div>
        <div class="mt-4">
          <span class="text-xs font-bold text-emerald-600 flex items-center gap-1">
            <i class="bi bi-check2-circle"></i> Sesi Mengajar Terisi
          </span>
        </div>
      </div>

      <!-- Card 3: Sesi Kosong (Jam Kosong) -->
      <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col justify-between hover:border-amber-400 transition">
        <div class="flex justify-between items-start gap-3">
          <div>
            <div class="text-[11px] uppercase tracking-wider font-bold text-slate-400">SESI KOSONG</div>
            <div class="text-2xl sm:text-3xl font-extrabold {{ $totalKosong > 0 ? 'text-amber-600' : 'text-emerald-600' }} mt-2">{{ $totalKosong }}</div>
          </div>
          <div class="w-9 h-9 rounded-lg {{ $totalKosong > 0 ? 'bg-amber-50 text-amber-600' : 'bg-emerald-50 text-emerald-600' }} flex items-center justify-center text-base shadow-xs">
            <i class="bi {{ $totalKosong > 0 ? 'bi-calendar-x' : 'bi-check-all' }}"></i>
          </div>
        </div>
        <div class="mt-4">
          <span class="text-xs font-bold {{ $totalKosong > 0 ? 'text-amber-600' : 'text-emerald-600' }} flex items-center gap-1">
            @if(($periode ?? 'harian') === 'bulanan')
              <i class="bi bi-info-circle"></i> Bulan {{ $currentBulanNama }} Kosong {{ $totalKosong }}x
            @else
              <i class="bi bi-info-circle"></i> Hari Ini Kosong {{ $totalKosong }} Sesi
            @endif
          </span>
        </div>
      </div>

      <!-- Card 4: Guru Terlambat -->
      <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col justify-between hover:border-amber-400 transition">
        <div class="flex justify-between items-start gap-3">
          <div>
            <div class="text-[11px] uppercase tracking-wider font-bold text-slate-400">GURU TERLAMBAT</div>
            <div class="text-2xl sm:text-3xl font-extrabold {{ ($guruTerlambat ?? 0) > 0 ? 'text-amber-600' : 'text-slate-900' }} mt-2">{{ $guruTerlambat ?? 0 }}</div>
          </div>
          <div class="w-9 h-9 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-base shadow-xs">
            <i class="bi bi-clock-history"></i>
          </div>
        </div>
        <div class="mt-4">
          <span class="text-xs font-semibold text-amber-600 flex items-center gap-1">
            <i class="bi bi-exclamation-circle"></i> Melewati Jam Sesi
          </span>
        </div>
      </div>

      <!-- Card 5: Total Guru Terdaftar -->
      <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col justify-between hover:border-blue-300 transition">
        <div class="flex justify-between items-start gap-3">
          <div>
            <div class="text-[11px] uppercase tracking-wider font-bold text-slate-400">GURU AKTIF</div>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2">{{ $gurus->count() }}</div>
          </div>
          <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-base shadow-xs">
            <i class="bi bi-people-fill"></i>
          </div>
        </div>
        <div class="mt-4">
          <span class="text-xs font-semibold text-blue-600 flex items-center gap-1">
            <i class="bi bi-person-lines-fill"></i> Total Guru Pengampu
          </span>
        </div>
      </div>

    </div>

    <!-- Panel Utama Tabel & Tabs -->
    <div class="bg-white rounded-2xl border border-[#DCEBE5] shadow-sm overflow-hidden">
      
      <!-- Baris Tabs & Filter -->
      <div class="flex flex-col md:flex-row md:items-center justify-between px-6 py-3.5 bg-white border-b border-[#E8F2EE] gap-3">
        <!-- Tab Navigation -->
        <div class="flex items-center gap-2 text-sm overflow-x-auto pb-1 md:pb-0">
          <a href="{{ route('dashboard.rekap-jurnal', array_merge(request()->query(), ['tab' => 'jurnal'])) }}"
             class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition whitespace-nowrap {{ in_array($tab ?? 'jurnal', ['jurnal', 'all']) ? 'bg-[#E3F2ED] !text-[#0D6B5A]' : 'text-slate-500 hover:text-[#0D6B5A] hover:bg-slate-50' }} !no-underline flex items-center gap-1.5">
            <i class="bi bi-journals"></i>
            <span>Riwayat Jurnal Mengajar</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ in_array($tab ?? 'jurnal', ['jurnal', 'all']) ? 'bg-[#0D6B5A]/15 text-[#0D6B5A]' : 'bg-slate-200 text-slate-600' }}">{{ count($jurnals) }}</span>
          </a>

          <a href="{{ route('dashboard.rekap-jurnal', array_merge(request()->query(), ['tab' => 'rekap_guru'])) }}"
             class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition whitespace-nowrap {{ ($tab ?? '') === 'rekap_guru' ? 'bg-[#E3F2ED] !text-[#0D6B5A]' : 'text-slate-500 hover:text-[#0D6B5A] hover:bg-slate-50' }} !no-underline flex items-center gap-1.5">
            <i class="bi bi-person-lines-fill"></i>
            <span>Rekapitulasi Per Guru</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ ($tab ?? '') === 'rekap_guru' ? 'bg-[#0D6B5A]/15 text-[#0D6B5A]' : 'bg-slate-200 text-slate-600' }}">{{ $rekapGuru->count() }}</span>
          </a>
        </div>

        <!-- Tombol Toggle Filter Panel -->
        <button type="button" 
                onclick="toggleFilterPanel()" 
                id="btnFilterToggle"
                class="!text-[#0D6B5A] hover:!text-[#08483C] bg-[#E3F2ED]/60 hover:bg-[#E3F2ED] px-3.5 py-1.5 rounded-lg text-xs font-bold flex items-center gap-2 transition cursor-pointer border border-[#DCEBE5] self-end md:self-auto">
          <i class="bi bi-sliders text-sm"></i>
          <span>Filter Data</span>
          @if(request()->filled('kelas_id') || request()->filled('guru_id') || request()->filled('keterlambatan') || request()->filled('search') || request()->filled('bulan') || (request()->filled('tanggal') && request()->query('tanggal') != now()->toDateString()))
            <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
          @endif
        </button>
      </div>

      <!-- Panel Form Filter Dropdown / Collapsible -->
      <div id="filterDataPanel" class="{{ (request()->filled('kelas_id') || request()->filled('guru_id') || request()->filled('keterlambatan') || request()->filled('search') || request()->filled('bulan') || (request()->filled('tanggal') && request()->query('tanggal') != now()->toDateString())) ? '' : 'hidden' }} bg-[#F6FAF8] border-b border-[#E8F2EE] p-5 transition-all">
        <form method="GET" action="{{ route('dashboard.rekap-jurnal') }}" autocomplete="off" class="space-y-4">
          <input type="hidden" name="tab" value="{{ $tab ?? 'jurnal' }}">

          <!-- Pilihan Mode Periode: Harian vs Bulanan -->
          <div class="flex items-center gap-4 pb-2 border-b border-slate-200/80 text-xs">
            <span class="font-bold text-slate-700">Mode Periode:</span>
            <label class="inline-flex items-center gap-1.5 cursor-pointer font-medium text-slate-700">
              <input type="radio" name="periode" value="harian" {{ ($periode ?? 'harian') === 'harian' ? 'checked' : '' }} onchange="togglePeriodeMode('harian')" class="text-[#155d50] focus:ring-[#155d50]">
              <span>Harian (Cari Tanggal)</span>
            </label>
            <label class="inline-flex items-center gap-1.5 cursor-pointer font-medium text-slate-700">
              <input type="radio" name="periode" value="bulanan" {{ ($periode ?? '') === 'bulanan' ? 'checked' : '' }} onchange="togglePeriodeMode('bulanan')" class="text-[#155d50] focus:ring-[#155d50]">
              <span>Bulanan (Rekap & Cek Kosong)</span>
            </label>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3.5">
            <!-- Filter Tanggal (Harian) -->
            <div id="filterTanggalGroup" class="{{ ($periode ?? 'harian') === 'bulanan' ? 'hidden' : '' }} col-span-1 lg:col-span-2">
              <label class="block text-xs font-semibold text-slate-600 mb-1">Cari / Pilih Tanggal</label>
              <input type="date" name="tanggal" value="{{ $tanggal }}" class="w-full text-xs bg-white border border-slate-300 rounded-lg px-3 py-2 text-slate-700 outline-none focus:border-[#155d50] focus:ring-1 focus:ring-[#155d50]">
            </div>

            <!-- Filter Bulan (Bulanan) -->
            <div id="filterBulanGroup" class="{{ ($periode ?? 'harian') === 'bulanan' ? '' : 'hidden' }}">
              <label class="block text-xs font-semibold text-slate-600 mb-1">Pilih Bulan</label>
              <select name="bulan" class="w-full text-xs bg-white border border-slate-300 rounded-lg px-3 py-2 text-slate-700 outline-none focus:border-[#155d50] focus:ring-1 focus:ring-[#155d50] cursor-pointer">
                @foreach($namaBulan as $bNum => $bNama)
                  <option value="{{ $bNum }}" {{ ($bulan == $bNum) ? 'selected' : '' }}>
                    {{ $bNama }}
                  </option>
                @endforeach
              </select>
            </div>

            <!-- Filter Tahun (Bulanan) -->
            <div id="filterTahunGroup" class="{{ ($periode ?? 'harian') === 'bulanan' ? '' : 'hidden' }}">
              <label class="block text-xs font-semibold text-slate-600 mb-1">Tahun</label>
              <select name="tahun" class="w-full text-xs bg-white border border-slate-300 rounded-lg px-3 py-2 text-slate-700 outline-none focus:border-[#155d50] focus:ring-1 focus:ring-[#155d50] cursor-pointer">
                @for($y = 2026; $y <= max(2030, now()->year + 2); $y++)
                  <option value="{{ $y }}" {{ ($tahun == $y) ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
              </select>
            </div>

            <!-- Filter Guru -->
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">Pilih Guru</label>
              <select name="guru_id" class="w-full text-xs bg-white border border-slate-300 rounded-lg px-3 py-2 text-slate-700 outline-none focus:border-[#155d50] focus:ring-1 focus:ring-[#155d50] cursor-pointer">
                <option value="all">Semua Guru</option>
                @foreach($gurus as $g)
                  <option value="{{ $g->id }}" {{ ($guruId == $g->id) ? 'selected' : '' }}>
                    {{ $g->name }}
                  </option>
                @endforeach
              </select>
            </div>

            <!-- Filter Kelas -->
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">Kelas</label>
              <select name="kelas_id" class="w-full text-xs bg-white border border-slate-300 rounded-lg px-3 py-2 text-slate-700 outline-none focus:border-[#155d50] focus:ring-1 focus:ring-[#155d50] cursor-pointer">
                <option value="all">Semua Kelas</option>
                @foreach($kelases as $k)
                  <option value="{{ $k->id_kelas }}" {{ ($kelasId == $k->id_kelas) ? 'selected' : '' }}>
                    Kelas {{ $k->nama_kelas }}
                  </option>
                @endforeach
              </select>
            </div>

            <!-- Filter Keterlambatan -->
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">Ketepatan Waktu</label>
              <select name="keterlambatan" class="w-full text-xs bg-white border border-slate-300 rounded-lg px-3 py-2 text-slate-700 outline-none focus:border-[#155d50] focus:ring-1 focus:ring-[#155d50] cursor-pointer">
                <option value="all">Semua</option>
                <option value="terlambat" {{ ($keterlambatan === 'terlambat') ? 'selected' : '' }}>Terlambat</option>
                <option value="tepat_waktu" {{ ($keterlambatan === 'tepat_waktu') ? 'selected' : '' }}>Tepat Waktu</option>
              </select>
            </div>

            <!-- Cari Kata Kunci / Tanggal -->
            <div class="col-span-1 lg:col-span-2">
              <label class="block text-xs font-semibold text-slate-600 mb-1">Cari Tanggal / Guru / Mapel / Materi</label>
              <div class="relative">
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Ketik kata kunci atau tanggal (YYYY-MM-DD)..." autocomplete="off" class="w-full text-xs bg-white border border-slate-300 rounded-lg pl-8 pr-3 py-2 text-slate-700 outline-none focus:border-[#155d50] focus:ring-1 focus:ring-[#155d50]">
                <i class="bi bi-search absolute left-2.5 top-2.5 text-slate-400 text-xs"></i>
              </div>
            </div>
          </div>

          <!-- Tombol Terapkan & Reset -->
          <div class="flex justify-end gap-2 pt-1">
            <a href="{{ route('dashboard.rekap-jurnal', ['tab' => $tab ?? 'jurnal']) }}" class="px-4 py-1.5 rounded-lg border border-slate-300 text-slate-600 hover:bg-white text-xs font-semibold transition !no-underline flex items-center gap-1">
              <i class="bi bi-arrow-counterclockwise"></i> Reset Filter
            </a>
            <button type="submit" class="px-5 py-1.5 rounded-lg bg-[#155d50] hover:bg-[#0f463c] text-white text-xs font-bold transition shadow-xs flex items-center gap-1.5 cursor-pointer">
              <i class="bi bi-check2"></i> Terapkan Filter
            </button>
          </div>
        </form>
      </div>

      {{-- ================= TAB 1: DAFTAR RIWAYAT JURNAL MENGAJAR ================= --}}
      @if(($tab ?? 'jurnal') !== 'rekap_guru')
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-[#F2F8F5] text-[#4F6F66] text-xs font-semibold border-b border-[#E8F2EE]">
                <th class="px-6 py-4">Waktu & Tanggal</th>
                <th class="px-6 py-4">Kelas</th>
                <th class="px-6 py-4">Guru & Mata Pelajaran</th>
                <th class="px-6 py-4">Status Ketepatan</th>
                <th class="px-6 py-4">Presensi Siswa</th>
                <th class="px-6 py-4 text-center w-28">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#E8F2EE] text-sm text-slate-700">
              @forelse($jurnals as $idx => $item)
                @php
                  $jamLabel = $item->jam_ke > 0 ? 'Jam ke-' . $item->jam_ke : 'Sesi Khusus';
                @endphp
                <tr class="{{ $idx % 2 === 0 ? 'bg-white' : 'bg-[#F6FAF8]' }} hover:bg-[#E3F2ED]/25 transition">
                  <!-- Waktu & Tanggal -->
                  <td class="px-6 py-4 text-[#4F6F66] font-medium whitespace-nowrap">
                    <div class="font-bold text-slate-800">{{ $jamLabel }}</div>
                    <div class="text-[11px] text-slate-500 mt-0.5 font-semibold">
                      <i class="bi bi-calendar3"></i> {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}
                    </div>
                  </td>

                  <!-- Kelas -->
                  <td class="px-6 py-4 font-bold text-slate-800 whitespace-nowrap">
                    <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                      {{ optional($item->kelas)->nama_kelas ?? '-' }}
                    </span>
                  </td>

                  <!-- Guru & Mata Pelajaran -->
                  <td class="px-6 py-4">
                    <div class="font-bold text-slate-800 flex items-center gap-1.5">
                      <span>{{ optional($item->guru)->name ?? 'Guru Pengampu' }}</span>
                    </div>
                    <div class="text-xs text-[#5C7E74] mt-0.5 font-medium">
                      {{ optional($item->mapel)->nama_mapel ?? 'Mata Pelajaran' }}
                    </div>
                    @if($item->materi)
                      <div class="text-[11px] text-slate-500 mt-1 line-clamp-1 italic">
                        "{{ $item->materi }}"
                      </div>
                    @endif
                  </td>

                  <!-- Ketepatan Waktu -->
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex flex-col gap-1 items-start">
                      @if(($item->menit_keterlambatan ?? 0) > 0)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300">
                          <i class="bi bi-clock-history"></i> Telat {{ $item->menit_keterlambatan }}m
                        </span>
                      @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                          <i class="bi bi-check2"></i> Tepat Waktu
                        </span>
                      @endif
                    </div>
                  </td>

                  <!-- Presensi Siswa -->
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center gap-1.5 text-xs">
                      <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold" title="Hadir: {{ $item->jumlah_hadir ?? 0 }}">H: {{ $item->jumlah_hadir ?? 0 }}</span>
                      <span class="px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 border border-blue-200 font-bold" title="Sakit: {{ $item->jumlah_sakit ?? 0 }}">S: {{ $item->jumlah_sakit ?? 0 }}</span>
                      <span class="px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 border border-amber-200 font-bold" title="Izin: {{ $item->jumlah_izin ?? 0 }}">I: {{ $item->jumlah_izin ?? 0 }}</span>
                      <span class="px-2 py-0.5 rounded-md bg-red-50 text-red-700 border border-red-200 font-bold" title="Alpa: {{ $item->jumlah_alpa ?? 0 }}">A: {{ $item->jumlah_alpa ?? 0 }}</span>
                    </div>
                  </td>

                  <!-- Aksi -->
                  <td class="px-6 py-4 text-center whitespace-nowrap">
                    <div class="flex items-center justify-center gap-1.5">
                      <!-- Tombol Detail -->
                      <button type="button" 
                              onclick='openDetailJurnalModal(@json($item))'
                              class="px-2.5 py-1.5 rounded-lg text-xs font-semibold text-[#155d50] bg-[#155d50]/10 hover:bg-[#155d50]/20 transition cursor-pointer flex items-center gap-1" 
                              title="Lihat Rincian Jurnal">
                        <i class="bi bi-eye"></i>
                        <span>Rincian</span>
                      </button>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="p-12 text-center text-gray-400">
                    <div class="max-w-sm mx-auto">
                      <i class="bi bi-journal-x text-4xl text-slate-300 mb-2 block"></i>
                      <p class="font-bold text-slate-700 text-base">Tidak ada catatan riwayat jurnal</p>
                      <p class="text-xs text-slate-400 mt-1">
                        Belum ada sesi jurnal yang cocok dengan filter yang dipilih.
                      </p>
                      <div class="mt-4">
                        <a href="{{ route('dashboard.rekap-jurnal') }}" class="text-xs font-bold text-[#155d50] hover:underline !no-underline">
                          <i class="bi bi-arrow-clockwise"></i> Reset Semua Filter
                        </a>
                      </div>
                    </div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <!-- Footer Status Bawah -->
        <div class="flex flex-col sm:flex-row items-center justify-between px-6 py-4 bg-[#F2F8F5] text-xs text-[#4F6F66] gap-2 border-t border-[#E8F2EE]">
          <span class="font-medium">
            Menampilkan {{ count($jurnals) }} catatan riwayat jurnal
          </span>
          <div class="flex items-center gap-2">
            <span class="text-[11px] text-slate-400">Terakhir diperbarui: {{ now()->format('H:i') }} WIB</span>
          </div>
        </div>

      {{-- ================= TAB 2: REKAPITULASI PER GURU ================= --}}
      @else
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-[#F2F8F5] text-[#4F6F66] text-xs font-semibold border-b border-[#E8F2EE]">
                <th class="px-6 py-4 w-12 text-center">No</th>
                <th class="px-6 py-4">Guru Pengampu</th>
                <th class="px-6 py-4 text-center">Sesi Terjadwal</th>
                <th class="px-6 py-4 text-center">Sesi Terisi</th>
                <th class="px-6 py-4 text-center">Sesi Kosong</th>
                <th class="px-6 py-4">Keterisian</th>
                <th class="px-6 py-4">Riwayat Terakhir</th>
                <th class="px-6 py-4 text-center w-32">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#E8F2EE] text-sm text-slate-700">
              @forelse($rekapGuru as $idx => $rg)
                <tr class="{{ $idx % 2 === 0 ? 'bg-white' : 'bg-[#F6FAF8]' }} hover:bg-[#E3F2ED]/25 transition">
                  <!-- No -->
                  <td class="px-6 py-4 text-center text-xs text-slate-400 font-semibold">
                    {{ $loop->iteration }}
                  </td>

                  <!-- Guru Pengampu -->
                  <td class="px-6 py-4">
                    <div class="font-bold text-slate-800">{{ $rg->name }}</div>
                    @if($rg->nip)
                      <div class="text-[11px] text-slate-400">NIP: {{ $rg->nip }}</div>
                    @endif
                  </td>

                  <!-- Sesi Terjadwal -->
                  <td class="px-6 py-4 text-center whitespace-nowrap font-bold text-slate-800">
                    {{ $rg->terjadwal }}
                  </td>

                  <!-- Sesi Terisi -->
                  <td class="px-6 py-4 text-center whitespace-nowrap font-bold text-emerald-700">
                    {{ $rg->terisi }}
                  </td>

                  <!-- Sesi Kosong -->
                  <td class="px-6 py-4 text-center whitespace-nowrap">
                    @if($rg->kosong > 0)
                      <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-extrabold bg-amber-100 text-amber-800 border border-amber-300" title="Kosong {{ $rg->kosong }} kali pada periode ini">
                        <i class="bi bi-exclamation-triangle-fill text-amber-600"></i> Kosong {{ $rg->kosong }}x
                      </span>
                    @else
                      <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <i class="bi bi-check2"></i> Lengkap (0 Kosong)
                      </span>
                    @endif
                  </td>

                  <!-- Keterisian Progress -->
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center gap-2">
                      <div class="w-20 h-2 bg-slate-200 rounded-full overflow-hidden">
                        <div class="h-full {{ $rg->persentase >= 80 ? 'bg-emerald-500' : ($rg->persentase >= 50 ? 'bg-amber-500' : 'bg-red-400') }} rounded-full transition-all" style="width: {{ $rg->persentase }}%"></div>
                      </div>
                      <span class="text-xs font-bold text-slate-700">{{ $rg->persentase }}%</span>
                    </div>
                  </td>

                  <!-- Riwayat Terakhir -->
                  <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-600">
                    @if($rg->terakhir)
                      <span class="font-medium text-slate-800">{{ \Carbon\Carbon::parse($rg->terakhir)->format('d M Y') }}</span>
                    @else
                      <span class="text-slate-400 italic">Belum ada jurnal</span>
                    @endif
                  </td>

                  <!-- Aksi -->
                  <td class="px-6 py-4 text-center whitespace-nowrap">
                    <a href="{{ route('dashboard.rekap-jurnal', ['tab' => 'jurnal', 'guru_id' => $rg->id, 'periode' => $periode ?? 'harian', 'bulan' => $bulan, 'tahun' => $tahun]) }}" 
                       class="px-2.5 py-1.5 rounded-lg text-xs font-semibold text-[#155d50] bg-[#155d50]/10 hover:bg-[#155d50]/20 transition !no-underline inline-flex items-center gap-1"
                       title="Lihat riwayat mengajar guru ini">
                      <i class="bi bi-clock-history"></i>
                      <span>Riwayat</span>
                    </a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="8" class="p-12 text-center text-gray-400">
                    <div class="max-w-sm mx-auto">
                      <i class="bi bi-person-x text-4xl text-slate-300 mb-2 block"></i>
                      <p class="font-bold text-slate-700 text-base">Tidak ada data guru ditemukan</p>
                      <p class="text-xs text-slate-400 mt-1">Coba sesuaikan kata kunci pencarian Anda.</p>
                    </div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <!-- Footer Rekap Guru -->
        <div class="flex flex-col sm:flex-row items-center justify-between px-6 py-4 bg-[#F2F8F5] text-xs text-[#4F6F66] gap-2 border-t border-[#E8F2EE]">
          <span class="font-medium">
            Menampilkan {{ $rekapGuru->count() }} guru pengampu pada periode 
            @if(($periode ?? 'harian') === 'bulanan')
              {{ $currentBulanNama }} {{ $tahun }}
            @else
              {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d M Y') }}
            @endif
          </span>
          <div class="flex items-center gap-2">
            <span class="text-[11px] text-slate-400">Terakhir diperbarui: {{ now()->format('H:i') }} WIB</span>
          </div>
        </div>
      @endif

    </div>

</div>

{{-- ================= MODAL DETAIL JURNAL ================= --}}
<div id="modalDetailJurnal" class="fixed inset-0 z-50 hidden items-center justify-center overflow-y-auto bg-gray-900/50 p-4 backdrop-blur-xs">
    <div role="dialog" aria-modal="true" class="relative w-full max-w-xl rounded-2xl border border-gray-100 bg-white p-6 shadow-xl my-8">
        
        <!-- Header Modal -->
        <div class="mb-5 flex items-center justify-between border-b border-slate-100 pb-4">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl bg-[#155d50]/10 text-[#155d50] flex items-center justify-center text-lg">
                <i class="bi bi-journal-richtext"></i>
              </div>
              <div>
                <h2 class="text-lg font-bold text-slate-900">Rincian Jurnal Mengajar</h2>
                <p id="detailSubtitle" class="text-xs text-slate-500 mt-0.5">Detail sesi pembelajaran dan presensi siswa</p>
              </div>
            </div>
            <button type="button" onclick="closeModal('modalDetailJurnal')" class="text-gray-400 transition hover:text-gray-600 p-1 cursor-pointer">
                <i class="bi bi-x-lg text-base"></i>
            </button>
        </div>

        <!-- Konten Detail -->
        <div class="space-y-4 text-xs">
            
            <!-- Grid Identitas Sesi -->
            <div class="grid grid-cols-2 gap-3 p-3.5 bg-slate-50 rounded-xl border border-slate-100">
              <div>
                <span class="text-slate-400 font-medium block text-[11px]">Kelas & Wali:</span>
                <span id="detailKelas" class="font-bold text-slate-800 text-sm">-</span>
              </div>
              <div>
                <span class="text-slate-400 font-medium block text-[11px]">Waktu Sesi:</span>
                <span id="detailWaktu" class="font-bold text-slate-800 text-sm">-</span>
              </div>
              <div>
                <span class="text-slate-400 font-medium block text-[11px]">Guru Pengampu:</span>
                <span id="detailGuru" class="font-semibold text-slate-800 text-xs">-</span>
              </div>
              <div>
                <span class="text-slate-400 font-medium block text-[11px]">Mata Pelajaran:</span>
                <span id="detailMapel" class="font-semibold text-slate-800 text-xs">-</span>
              </div>
            </div>

            <!-- Status Mengajar Box -->
            <div class="p-3.5 rounded-xl border bg-emerald-50/70 border-emerald-200 text-emerald-900 flex items-center justify-between" id="detailKehadiranBox">
              <div class="flex items-center gap-2">
                <i class="bi bi-check-circle-fill text-emerald-600 text-base"></i>
                <div>
                  <span class="text-slate-500 font-medium block text-[11px]">Status KBM:</span>
                  <span id="detailKehadiranGuru" class="font-bold text-sm text-emerald-800">Telah Mengajar (Jurnal Terisi)</span>
                </div>
              </div>
              <div id="detailKetepatanBadge" class="text-right">
                <!-- Telat / Tepat waktu badge dimasukkan lewat JS -->
              </div>
            </div>

            <!-- Materi Pembelajaran -->
            <div>
              <label class="block font-bold text-slate-700 text-xs mb-1">Materi yang Diajarkan:</label>
              <div id="detailMateri" class="p-3 bg-white border border-slate-200 rounded-xl text-slate-800 text-xs font-medium leading-relaxed">
                -
              </div>
            </div>

            <!-- Keterangan & Catatan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="block font-bold text-slate-700 text-xs mb-1">Keterangan / Metode:</label>
                <div id="detailKeterangan" class="p-2.5 bg-slate-50 border border-slate-100 rounded-xl text-slate-700 text-xs">
                  -
                </div>
              </div>
              <div>
                <label class="block font-bold text-slate-700 text-xs mb-1">Catatan Tambahan:</label>
                <div id="detailCatatan" class="p-2.5 bg-slate-50 border border-slate-100 rounded-xl text-slate-700 text-xs">
                  -
                </div>
              </div>
            </div>

            <!-- Rincian Presensi Siswa -->
            <div>
              <label class="block font-bold text-slate-700 text-xs mb-1.5">Rekapitulasi Kehadiran Siswa:</label>
              <div class="grid grid-cols-5 gap-2 text-center">
                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-2.5">
                  <span class="text-[10px] uppercase font-bold text-emerald-700 block">Hadir</span>
                  <span id="detailHadir" class="text-base font-extrabold text-emerald-800 mt-0.5 block">0</span>
                </div>
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-2.5">
                  <span class="text-[10px] uppercase font-bold text-blue-700 block">Sakit</span>
                  <span id="detailSakit" class="text-base font-extrabold text-blue-800 mt-0.5 block">0</span>
                </div>
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-2.5">
                  <span class="text-[10px] uppercase font-bold text-amber-700 block">Izin</span>
                  <span id="detailIzin" class="text-base font-extrabold text-amber-800 mt-0.5 block">0</span>
                </div>
                <div class="bg-red-50 border border-red-200 rounded-xl p-2.5">
                  <span class="text-[10px] uppercase font-bold text-red-700 block">Alpa</span>
                  <span id="detailAlpa" class="text-base font-extrabold text-red-800 mt-0.5 block">0</span>
                </div>
                <div class="bg-purple-50 border border-purple-200 rounded-xl p-2.5">
                  <span class="text-[10px] uppercase font-bold text-purple-700 block">Dispensasi</span>
                  <span id="detailDispensasi" class="text-base font-extrabold text-purple-800 mt-0.5 block">0</span>
                </div>
              </div>
            </div>

        </div>

        <!-- Footer / Action Buttons -->
        <div class="mt-6 flex items-center justify-end gap-3 border-t border-gray-100 pt-4">
            <button type="button" onclick="closeModal('modalDetailJurnal')" class="rounded-xl border border-gray-300 px-4 py-2 text-xs font-semibold text-gray-700 transition hover:bg-gray-50 cursor-pointer">
              Tutup
            </button>
        </div>

    </div>
</div>

{{-- ================= MODAL KELOLA PENUGASAN PIKET & WAKA ================= --}}
<div id="modalKelolaPiket" class="fixed inset-0 z-50 hidden items-center justify-center overflow-y-auto bg-gray-900/50 p-4 backdrop-blur-xs">
    <div role="dialog" aria-modal="true" class="relative w-full max-w-2xl rounded-2xl border border-gray-100 bg-white p-6 shadow-xl my-8">
        
        <!-- Header Modal -->
        <div class="mb-5 flex items-center justify-between border-b border-slate-100 pb-4">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl bg-[#155d50]/10 text-[#155d50] flex items-center justify-center text-lg">
                <i class="bi bi-calendar2-week-fill"></i>
              </div>
              <div>
                <h2 class="text-lg font-bold text-slate-900">Atur Penugasan Guru Piket & Waka</h2>
                <p class="text-xs text-slate-500 mt-0.5">Tentukan guru pengampu yang bertugas piket dan waka pengawas setiap hari.</p>
              </div>
            </div>
            <button type="button" onclick="closeModal('modalKelolaPiket')" class="text-gray-400 transition hover:text-gray-600 p-1 cursor-pointer">
                <i class="bi bi-x-lg text-base"></i>
            </button>
        </div>

        <!-- Form Penugasan -->
        <form action="{{ route('dashboard.rekap-jurnal.penugasan-piket') }}" method="POST" class="space-y-4">
          @csrf

          <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
              <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase tracking-wider font-bold">
                  <th class="py-2.5 px-3 w-28">Hari</th>
                  <th class="py-2.5 px-3">Guru Piket</th>
                  <th class="py-2.5 px-3">Waka Pengawas Piket</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                @php
                  $listHari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                @endphp
                @foreach($listHari as $h)
                  @php
                    $jadwalHari = isset($allPiketJadwals[$h]) ? $allPiketJadwals[$h] : collect();
                    $assignedGuruId = optional($jadwalHari->firstWhere('tipe', 'guru'))->user_id;
                    $assignedWakaId = optional($jadwalHari->firstWhere('tipe', 'waka'))->user_id;
                  @endphp
                  <tr class="hover:bg-slate-50/70 transition">
                    <td class="py-3 px-3 font-bold text-slate-800 flex items-center gap-1.5">
                      <span class="w-2 h-2 rounded-full {{ ($namaHari ?? '') === $h ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                      <span>{{ $h }}</span>
                      @if(($namaHari ?? '') === $h)
                        <span class="text-[10px] font-extrabold text-emerald-700 bg-emerald-100 px-1.5 py-0.2 rounded">Hari Ini</span>
                      @endif
                    </td>
                    <td class="py-3 px-3">
                      <select name="piket[{{ $h }}]" class="w-full rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs text-slate-700 outline-none focus:border-[#155d50] focus:ring-1 focus:ring-[#155d50] cursor-pointer">
                        <option value="">-- Pilih Guru Piket --</option>
                        @foreach($gurus as $g)
                          <option value="{{ $g->id }}" {{ $assignedGuruId == $g->id ? 'selected' : '' }}>
                            {{ $g->name }}
                          </option>
                        @endforeach
                      </select>
                    </td>
                    <td class="py-3 px-3">
                      <select name="waka[{{ $h }}]" class="w-full rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs text-slate-700 outline-none focus:border-[#155d50] focus:ring-1 focus:ring-[#155d50] cursor-pointer">
                        <option value="">-- Pilih Waka Pengawas --</option>
                        @foreach($gurus as $g)
                          <option value="{{ $g->id }}" {{ $assignedWakaId == $g->id ? 'selected' : '' }}>
                            {{ $g->name }}
                          </option>
                        @endforeach
                      </select>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          <div class="mt-6 flex items-center justify-end gap-3 border-t border-gray-100 pt-4 text-xs">
            <button type="button" onclick="closeModal('modalKelolaPiket')" class="rounded-xl border border-gray-300 px-4 py-2 font-semibold text-gray-700 transition hover:bg-gray-50 cursor-pointer">
              Batal
            </button>
            <button type="submit" class="rounded-xl bg-[#155d50] hover:bg-[#0f463c] px-5 py-2 font-bold text-white shadow-xs transition flex items-center gap-1.5 cursor-pointer">
              <i class="bi bi-check2"></i>
              <span>Simpan Penugasan</span>
            </button>
          </div>
        </form>

    </div>
</div>

{{-- ================= JAVASCRIPT ================= --}}
<script>
    let currentActiveJurnal = null;

    function togglePeriodeMode(mode) {
        const tanggalGroup = document.getElementById('filterTanggalGroup');
        const bulanGroup = document.getElementById('filterBulanGroup');
        const tahunGroup = document.getElementById('filterTahunGroup');

        if (mode === 'bulanan') {
            if (tanggalGroup) tanggalGroup.classList.add('hidden');
            if (bulanGroup) bulanGroup.classList.remove('hidden');
            if (tahunGroup) tahunGroup.classList.remove('hidden');
        } else {
            if (tanggalGroup) tanggalGroup.classList.remove('hidden');
            if (bulanGroup) bulanGroup.classList.add('hidden');
            if (tahunGroup) tahunGroup.classList.add('hidden');
        }
    }

    function toggleFilterPanel() {
        const panel = document.getElementById('filterDataPanel');
        if (!panel) return;
        panel.classList.toggle('hidden');
    }

    function openDetailJurnalModal(jurnal) {
        currentActiveJurnal = jurnal;

        const kelasNama = jurnal.kelas ? jurnal.kelas.nama_kelas : '-';
        const waliKelas = jurnal.kelas && jurnal.kelas.wali_kelas ? jurnal.kelas.wali_kelas : '-';
        const guruNama = jurnal.guru ? jurnal.guru.name : 'Guru Pengampu';
        const mapelNama = jurnal.mapel ? jurnal.mapel.nama_mapel : 'Mata Pelajaran';
        const jamKe = jurnal.jam_ke > 0 ? 'Jam ke-' + jurnal.jam_ke : 'Sesi Khusus';

        document.getElementById('detailSubtitle').textContent = `Sesi KBM ${kelasNama} • ${jamKe}`;
        document.getElementById('detailKelas').textContent = `${kelasNama} (Wali: ${waliKelas})`;
        document.getElementById('detailWaktu').textContent = `${jamKe} (${jurnal.tanggal || '-'})`;
        document.getElementById('detailGuru').textContent = guruNama;
        document.getElementById('detailMapel').textContent = mapelNama;
        document.getElementById('detailMateri').textContent = jurnal.materi || 'Tidak ada catatan materi.';
        document.getElementById('detailKeterangan').textContent = jurnal.keterangan || '-';
        document.getElementById('detailCatatan').textContent = jurnal.catatan || '-';

        // Rekap Siswa
        document.getElementById('detailHadir').textContent = jurnal.jumlah_hadir ?? 0;
        document.getElementById('detailSakit').textContent = jurnal.jumlah_sakit ?? 0;
        document.getElementById('detailIzin').textContent = jurnal.jumlah_izin ?? 0;
        document.getElementById('detailAlpa').textContent = jurnal.jumlah_alpa ?? 0;
        document.getElementById('detailDispensasi').textContent = jurnal.jumlah_dispensasi ?? 0;

        // Ketepatan waktu badge
        const badgeContainer = document.getElementById('detailKetepatanBadge');
        if (badgeContainer) {
            if ((jurnal.menit_keterlambatan || 0) > 0) {
                badgeContainer.innerHTML = `<span class="px-2.5 py-1 rounded-md text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-300"><i class="bi bi-clock-history"></i> Telat ${jurnal.menit_keterlambatan}m</span>`;
            } else {
                badgeContainer.innerHTML = `<span class="px-2.5 py-1 rounded-md text-[11px] font-semibold bg-emerald-100 text-emerald-800 border border-emerald-300"><i class="bi bi-check2"></i> Tepat Waktu</span>`;
            }
        }

        openModal('modalDetailJurnal');
    }

    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
    }

    // Jam Real-time di Header
    setInterval(() => {
        const clockElem = document.getElementById('liveClock');
        if (clockElem) {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            clockElem.textContent = `${hours}:${minutes} WIB`;
        }
    }, 1000);
</script>
@endsection
