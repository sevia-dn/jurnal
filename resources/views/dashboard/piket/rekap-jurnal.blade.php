@extends('layouts.app')

@section('title', 'Dashboard Piket - Rekap Jurnal')

@section('sidebar')
    @include('layouts.piket.sidebar')
@endsection

@section('navbar')
    @include('layouts.piket.navbar')
@endsection

@section('content')
<div class="p-6 font-sans sm:p-10 lg:p-8 xl:p-10">

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
      $opsiBulanRekap = collect($namaBulan)->map(fn ($nama, $nomor) => ['value' => (string) $nomor, 'label' => $nama]);
      $opsiGuruRekap = $gurus->map(fn ($guru) => ['value' => (string) $guru->id, 'label' => $guru->name.' ('.($guru->nip ?? 'Guru').')']);
      $opsiKelasRekap = $kelases->map(fn ($kelas) => ['value' => (string) $kelas->id_kelas, 'label' => 'Kelas '.$kelas->nama_kelas]);
    @endphp

    <!-- Header Halaman -->
    <div class="mb-6 flex flex-col items-start justify-between gap-4 border-b border-slate-200 pb-5 lg:flex-row lg:items-center lg:gap-6">
      <div class="flex flex-wrap items-center gap-2.5">
        <a href="{{ route('piket.rekap-jurnal.download-pdf', request()->query()) }}" class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3.5 py-2 text-xs font-bold text-slate-700 shadow-xs transition hover:bg-slate-50">
          <i class="bi bi-file-earmark-pdf text-rose-600"></i>
          <span>Unduh Rekap PDF</span>
        </a>
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

      <!-- Baris Tabs -->
      <div class="flex items-center gap-2 text-sm overflow-x-auto px-5 py-3.5 bg-white border-b border-[#E8F2EE]">
        <a href="{{ route('piket.rekap-jurnal', ['tab' => 'jurnal']) }}"
           class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition whitespace-nowrap {{ in_array($tab ?? 'jurnal', ['jurnal', 'all']) ? 'bg-[#E3F2ED] !text-[#0D6B5A]' : 'text-slate-500 hover:text-[#0D6B5A] hover:bg-slate-50' }} !no-underline flex items-center gap-1.5">
          <i class="bi bi-journals"></i>
          <span>Riwayat Jurnal Mengajar</span>
          <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ in_array($tab ?? 'jurnal', ['jurnal', 'all']) ? 'bg-[#0D6B5A]/15 text-[#0D6B5A]' : 'bg-slate-200 text-slate-600' }}">{{ count($jurnals) }}</span>
        </a>

        <a href="{{ route('piket.rekap-jurnal', ['tab' => 'rekap_guru']) }}"
           class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition whitespace-nowrap {{ ($tab ?? '') === 'rekap_guru' ? 'bg-[#E3F2ED] !text-[#0D6B5A]' : 'text-slate-500 hover:text-[#0D6B5A] hover:bg-slate-50' }} !no-underline flex items-center gap-1.5">
          <i class="bi bi-person-lines-fill"></i>
          <span>Rekapitulasi Per Guru</span>
          <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ ($tab ?? '') === 'rekap_guru' ? 'bg-[#0D6B5A]/15 text-[#0D6B5A]' : 'bg-slate-200 text-slate-600' }}">{{ $rekapGuru->count() }}</span>
        </a>

        <a href="{{ route('piket.rekap-jurnal', ['tab' => 'rekap_kelas']) }}"
           class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition whitespace-nowrap {{ ($tab ?? '') === 'rekap_kelas' ? 'bg-[#E3F2ED] !text-[#0D6B5A]' : 'text-slate-500 hover:text-[#0D6B5A] hover:bg-slate-50' }} !no-underline flex items-center gap-1.5">
          <i class="bi bi-people-fill"></i>
          <span>Rekapitulasi Per Kelas</span>
          <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ ($tab ?? '') === 'rekap_kelas' ? 'bg-[#0D6B5A]/15 text-[#0D6B5A]' : 'bg-slate-200 text-slate-600' }}">{{ $rekapJadwalKelas->count() }}</span>
        </a>
      </div>

      {{-- ================= TAB 1: RIWAYAT JURNAL MENGAJAR ================= --}}
      @if(in_array(($tab ?? 'jurnal'), ['jurnal', 'all'], true))
        {{-- Filter Inline Tab Jurnal --}}
        <div class="bg-[#F6FAF8] border-b border-[#E8F2EE] px-5 py-4">
          <form method="GET" action="{{ route('piket.rekap-jurnal') }}" autocomplete="off">
            <input type="hidden" name="tab" value="jurnal">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
              <div>
                <label class="block text-[11px] font-semibold text-slate-500 mb-1">Tanggal</label>
                <input type="date" name="tanggal" value="{{ $tanggal }}" class="w-full text-xs bg-white border border-slate-300 rounded-lg px-3 py-1.5 text-slate-700 outline-none focus:border-[#155d50]">
              </div>
              <div>
                <label class="block text-[11px] font-semibold text-slate-500 mb-1">Guru</label>
                <x-searchable-select name="guru_id" :options="$opsiGuruRekap" :selected="$guruId === 'all' ? null : $guruId" placeholder="Semua guru" />
              </div>
              <div>
                <label class="block text-[11px] font-semibold text-slate-500 mb-1">Kelas</label>
                <x-searchable-select name="kelas_id" :options="$opsiKelasRekap" :selected="$kelasId === 'all' ? null : $kelasId" placeholder="Semua kelas" />
              </div>
              <div>
                <label class="block text-[11px] font-semibold text-slate-500 mb-1">Ketepatan Waktu</label>
                <select name="keterlambatan" class="w-full text-xs bg-white border border-slate-300 rounded-lg px-3 py-1.5 text-slate-700 outline-none focus:border-[#155d50] cursor-pointer">
                  <option value="all">Semua</option>
                  <option value="terlambat" {{ ($keterlambatan === 'terlambat') ? 'selected' : '' }}>Terlambat</option>
                  <option value="tepat_waktu" {{ ($keterlambatan === 'tepat_waktu') ? 'selected' : '' }}>Tepat Waktu</option>
                </select>
              </div>
              <div class="col-span-2 sm:col-span-2">
                <label class="block text-[11px] font-semibold text-slate-500 mb-1">Cari Materi / Guru / Mapel</label>
                <div class="relative">
                  <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Kata kunci..." autocomplete="off" class="w-full text-xs bg-white border border-slate-300 rounded-lg pl-8 pr-3 py-1.5 text-slate-700 outline-none focus:border-[#155d50]">
                  <i class="bi bi-search absolute left-2.5 top-2 text-slate-400 text-xs"></i>
                </div>
              </div>
            </div>
            <div class="flex justify-end gap-2 mt-3">
              <a href="{{ route('piket.rekap-jurnal', ['tab' => 'jurnal']) }}" class="px-3.5 py-1.5 rounded-lg border border-slate-300 text-slate-600 hover:bg-white text-xs font-semibold transition !no-underline flex items-center gap-1">
                <i class="bi bi-arrow-counterclockwise"></i> Reset
              </a>
              <button type="submit" class="px-4 py-1.5 rounded-lg bg-[#155d50] hover:bg-[#0f463c] text-white text-xs font-bold transition shadow-xs flex items-center gap-1.5 cursor-pointer">
                <i class="bi bi-check2"></i> Terapkan
              </button>
            </div>
          </form>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-[#F2F8F5] text-[#4F6F66] text-xs font-semibold border-b border-[#E8F2EE]">
                <th class="px-6 py-4">Waktu & Tanggal</th>
                <th class="px-6 py-4">Kelas</th>
                <th class="px-6 py-4">Mata Pelajaran</th>
                <th class="px-6 py-4">Guru Pengampu</th>
                <th class="px-6 py-4">Materi</th>
                <th class="px-6 py-4 text-center">Keterlambatan</th>
                <th class="px-6 py-4 text-center w-28">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#E8F2EE] text-sm text-slate-700">
              @forelse($jurnals as $jurnal)
                <tr class="{{ $loop->iteration % 2 === 0 ? 'bg-[#F6FAF8]' : 'bg-white' }} hover:bg-[#E3F2ED]/25 transition">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="font-bold text-slate-800">{{ \Carbon\Carbon::parse($jurnal->tanggal)->translatedFormat('d M Y') }}</div>
                    <div class="text-[11px] text-slate-400">{{ $jurnal->jam_ke_formatted ?? "Jam ke-{$jurnal->jam_ke}" }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="font-semibold text-slate-800">{{ $jurnal->kelas?->nama_kelas ?? '-' }}</div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="font-medium text-slate-700">{{ $jurnal->mapel?->nama_mapel ?? '-' }}</div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="font-semibold text-slate-800">{{ $jurnal->guru?->name ?? '-' }}</div>
                    @if($jurnal->guru?->nip)
                      <div class="text-[11px] text-slate-400">{{ $jurnal->guru->nip }}</div>
                    @endif
                  </td>
                  <td class="px-6 py-4 max-w-xs">
                    <p class="text-slate-700 text-xs leading-relaxed line-clamp-2">{{ $jurnal->materi }}</p>
                  </td>
                  <td class="px-6 py-4 text-center whitespace-nowrap">
                    @if(($jurnal->menit_keterlambatan ?? 0) > 0)
                      <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-extrabold bg-amber-100 text-amber-800 border border-amber-300">
                        <i class="bi bi-clock-history"></i> +{{ $jurnal->menit_keterlambatan }}m
                      </span>
                    @else
                      <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <i class="bi bi-check2"></i> Tepat
                      </span>
                    @endif
                  </td>
                  <td class="px-6 py-4 text-center whitespace-nowrap">
                    <button type="button"
                            onclick="openDetailModal({{ $jurnal->id_jurnal }})"
                            class="px-2.5 py-1.5 rounded-lg text-xs font-semibold text-[#155d50] bg-[#155d50]/10 hover:bg-[#155d50]/20 transition inline-flex items-center gap-1 cursor-pointer"
                            title="Lihat detail jurnal">
                      <i class="bi bi-eye"></i>
                      <span>Detail</span>
                    </button>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="p-12 text-center text-gray-400">
                    <div class="max-w-sm mx-auto">
                      <i class="bi bi-journal-x text-4xl text-slate-300 mb-2 block"></i>
                      <p class="font-bold text-slate-700 text-base">Tidak ada jurnal ditemukan</p>
                      <p class="text-xs text-slate-400 mt-1">Coba sesuaikan filter atau pilih tanggal yang berbeda.</p>
                    </div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <!-- Footer Tab Jurnal -->
        <div class="flex flex-col sm:flex-row items-center justify-between px-6 py-4 bg-[#F2F8F5] text-xs text-[#4F6F66] gap-2 border-t border-[#E8F2EE]">
          <span class="font-medium">Menampilkan {{ count($jurnals) }} jurnal pada tanggal {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d M Y') }}</span>
          <span class="text-[11px] text-slate-400">Terakhir diperbarui: {{ now()->format('H:i') }} WIB</span>
        </div>

      {{-- ================= TAB 2: REKAPITULASI PER GURU ================= --}}
      @elseif(($tab ?? '') === 'rekap_guru')
        {{-- Filter Inline Tab Rekap Guru --}}
        <div class="bg-[#F6FAF8] border-b border-[#E8F2EE] px-5 py-4">
          <form method="GET" action="{{ route('piket.rekap-jurnal') }}" autocomplete="off">
            <input type="hidden" name="tab" value="rekap_guru">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
              <div>
                <label class="block text-[11px] font-semibold text-slate-500 mb-1">Periode</label>
                <select name="periode" onchange="this.form.submit()" class="w-full text-xs bg-white border border-slate-300 rounded-lg px-3 py-1.5 text-slate-700 outline-none focus:border-[#155d50] cursor-pointer">
                  <option value="harian" {{ ($periode ?? 'harian') === 'harian' ? 'selected' : '' }}>Harian</option>
                  <option value="bulanan" {{ ($periode ?? '') === 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                </select>
              </div>
              @if(($periode ?? 'harian') === 'bulanan')
                <div>
                  <label class="block text-[11px] font-semibold text-slate-500 mb-1">Bulan</label>
                  <x-searchable-select name="bulan" :options="$opsiBulanRekap" :selected="$bulan" placeholder="Pilih bulan" />
                </div>
                <div>
                  <label class="block text-[11px] font-semibold text-slate-500 mb-1">Tahun</label>
                  <input type="number" name="tahun" min="2020" max="2100" value="{{ $tahun }}" class="w-full text-xs bg-white border border-slate-300 rounded-lg px-3 py-1.5 text-slate-700 outline-none focus:border-[#155d50]">
                </div>
              @else
                <div>
                  <label class="block text-[11px] font-semibold text-slate-500 mb-1">Tanggal</label>
                  <input type="date" name="tanggal" value="{{ $tanggal }}" class="w-full text-xs bg-white border border-slate-300 rounded-lg px-3 py-1.5 text-slate-700 outline-none focus:border-[#155d50]">
                </div>
              @endif
              <div>
                <label class="block text-[11px] font-semibold text-slate-500 mb-1">Guru</label>
                <x-searchable-select name="guru_id" :options="$opsiGuruRekap" :selected="$guruId === 'all' ? null : $guruId" placeholder="Semua guru" />
              </div>
              <div>
                <label class="block text-[11px] font-semibold text-slate-500 mb-1">Cari Nama / NIP</label>
                <div class="relative">
                  <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Nama atau NIP..." autocomplete="off" class="w-full text-xs bg-white border border-slate-300 rounded-lg pl-8 pr-3 py-1.5 text-slate-700 outline-none focus:border-[#155d50]">
                  <i class="bi bi-search absolute left-2.5 top-2 text-slate-400 text-xs"></i>
                </div>
              </div>
            </div>
            <div class="flex justify-end gap-2 mt-3">
              <a href="{{ route('piket.rekap-jurnal', ['tab' => 'rekap_guru']) }}" class="px-3.5 py-1.5 rounded-lg border border-slate-300 text-slate-600 hover:bg-white text-xs font-semibold transition !no-underline flex items-center gap-1">
                <i class="bi bi-arrow-counterclockwise"></i> Reset
              </a>
              <button type="submit" class="px-4 py-1.5 rounded-lg bg-[#155d50] hover:bg-[#0f463c] text-white text-xs font-bold transition shadow-xs flex items-center gap-1.5 cursor-pointer">
                <i class="bi bi-check2"></i> Terapkan
              </button>
            </div>
          </form>
        </div>

        <div class="divide-y divide-[#E8F2EE]">
          @forelse($rekapGuru as $idx => $rg)
            <details class="group" {{ $rg->jurnals->isNotEmpty() ? 'open' : '' }}>
              <summary class="flex flex-wrap cursor-pointer items-center justify-between gap-3 px-5 py-4 bg-white hover:bg-[#F6FAF8] transition select-none list-none">
                <div class="flex items-center gap-3 min-w-0">
                  <div class="w-8 h-8 rounded-full bg-[#E3F2ED] flex items-center justify-center flex-shrink-0 text-[#155d50] text-xs font-bold">
                    {{ $loop->iteration }}
                  </div>
                  <div class="min-w-0">
                    <div class="font-bold text-slate-800 text-sm">{{ $rg->name }}</div>
                    @if($rg->nip)<div class="text-[11px] text-slate-400">NIP: {{ $rg->nip }}</div>@endif
                  </div>
                </div>
                <div class="flex flex-wrap items-center gap-2 text-[11px] font-bold">
                  <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-700">Terjadwal: {{ $rg->terjadwal }}</span>
                  <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800">Terisi: {{ $rg->terisi }}</span>
                  @if($rg->kosong > 0)
                    <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-800"><i class="bi bi-exclamation-triangle-fill"></i> Kosong: {{ $rg->kosong }}</span>
                  @else
                    <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700"><i class="bi bi-check2"></i> Lengkap</span>
                  @endif
                  <div class="flex items-center gap-1.5">
                    <div class="w-16 h-1.5 bg-slate-200 rounded-full overflow-hidden">
                      <div class="h-full {{ $rg->persentase >= 80 ? 'bg-emerald-500' : ($rg->persentase >= 50 ? 'bg-amber-500' : 'bg-red-400') }} rounded-full" style="width: {{ $rg->persentase }}%"></div>
                    </div>
                    <span class="text-slate-700">{{ $rg->persentase }}%</span>
                  </div>
                  <i class="bi bi-chevron-down text-slate-400 group-open:rotate-180 transition-transform ml-1"></i>
                </div>
              </summary>

              {{-- Daftar Jurnal per Guru --}}
              <div class="border-t border-[#E8F2EE] bg-[#F6FAF8]">
                @if($rg->jurnals->isNotEmpty())
                  <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                      <thead>
                        <tr class="bg-[#EEF7F3] text-[#4F6F66] font-semibold border-b border-[#E8F2EE]">
                          <th class="px-5 py-3">Tanggal</th>
                          <th class="px-5 py-3">Kelas</th>
                          <th class="px-5 py-3">Mata Pelajaran</th>
                          <th class="px-5 py-3">Jam ke-</th>
                          <th class="px-5 py-3">Materi</th>
                          <th class="px-5 py-3 text-center">Keterlambatan</th>
                          <th class="px-5 py-3 text-center">Aksi</th>
                        </tr>
                      </thead>
                      <tbody class="divide-y divide-[#E8F2EE]">
                        @foreach($rg->jurnals as $jrn)
                          <tr class="bg-white hover:bg-[#E3F2ED]/20 transition">
                            <td class="px-5 py-3 whitespace-nowrap font-medium text-slate-700">
                              {{ \Carbon\Carbon::parse($jrn->tanggal)->translatedFormat('d M Y') }}
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap text-slate-700">{{ $jrn->kelas?->nama_kelas ?? '-' }}</td>
                            <td class="px-5 py-3 text-slate-700">{{ $jrn->mapel?->nama_mapel ?? '-' }}</td>
                            <td class="px-5 py-3 whitespace-nowrap text-slate-600">{{ $jrn->jam_ke_formatted ?? "Jam ke-{$jrn->jam_ke}" }}</td>
                            <td class="px-5 py-3 max-w-xs">
                              <p class="text-slate-700 line-clamp-2">{{ $jrn->materi }}</p>
                            </td>
                            <td class="px-5 py-3 text-center whitespace-nowrap">
                              @if(($jrn->menit_keterlambatan ?? 0) > 0)
                                <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 font-bold">+{{ $jrn->menit_keterlambatan }}m</span>
                              @else
                                <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-semibold">Tepat</span>
                              @endif
                            </td>
                            <td class="px-5 py-3 text-center">
                              <button type="button" onclick="openDetailModal({{ $jrn->id_jurnal }})"
                                      class="px-2 py-1 rounded-lg text-[11px] font-semibold text-[#155d50] bg-[#155d50]/10 hover:bg-[#155d50]/20 transition inline-flex items-center gap-1 cursor-pointer">
                                <i class="bi bi-eye"></i> Detail
                              </button>
                            </td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                @else
                  <div class="px-5 py-6 text-center text-xs text-slate-400 italic">
                    <i class="bi bi-journal-x text-2xl text-slate-300 block mb-1"></i>
                    Belum ada jurnal yang diisi pada periode ini.
                  </div>
                @endif
              </div>
            </details>
          @empty
            <div class="p-12 text-center text-gray-400">
              <i class="bi bi-person-x text-4xl text-slate-300 mb-2 block"></i>
              <p class="font-bold text-slate-700 text-base">Tidak ada data guru ditemukan</p>
              <p class="text-xs text-slate-400 mt-1">Coba sesuaikan kata kunci pencarian.</p>
            </div>
          @endforelse
        </div>

        <!-- Footer Rekap Guru -->
        <div class="flex flex-col sm:flex-row items-center justify-between px-6 py-4 bg-[#F2F8F5] text-xs text-[#4F6F66] gap-2 border-t border-[#E8F2EE]">
          <span class="font-medium">
            Menampilkan {{ $rekapGuru->count() }} guru pada periode
            @if(($periode ?? 'harian') === 'bulanan')
              {{ $currentBulanNama }} {{ $tahun }}
            @else
              {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d M Y') }}
            @endif
          </span>
          <span class="text-[11px] text-slate-400">Terakhir diperbarui: {{ now()->format('H:i') }} WIB</span>
        </div>

      {{-- ================= TAB 3: REKAPITULASI PER KELAS ================= --}}
      @else
        {{-- Filter Inline Tab Rekap Kelas --}}
        <div class="bg-[#F6FAF8] border-b border-[#E8F2EE] px-5 py-4">
          <form method="GET" action="{{ route('piket.rekap-jurnal') }}" autocomplete="off">
            <input type="hidden" name="tab" value="rekap_kelas">
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
              <div>
                <label class="block text-[11px] font-semibold text-slate-500 mb-1">Tanggal</label>
                <input type="date" name="tanggal" value="{{ $tanggal }}" class="w-full text-xs bg-white border border-slate-300 rounded-lg px-3 py-1.5 text-slate-700 outline-none focus:border-[#155d50]">
              </div>
              <div>
                <label class="block text-[11px] font-semibold text-slate-500 mb-1">Kelas</label>
                <x-searchable-select name="kelas_id" :options="$opsiKelasRekap" :selected="$kelasId === 'all' ? null : $kelasId" placeholder="Semua kelas" />
              </div>
              <div class="flex items-end">
                <div class="flex gap-2 w-full">
                  <a href="{{ route('piket.rekap-jurnal', ['tab' => 'rekap_kelas']) }}" class="flex-1 px-3.5 py-1.5 rounded-lg border border-slate-300 text-slate-600 hover:bg-white text-xs font-semibold transition !no-underline flex items-center justify-center gap-1">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                  </a>
                  <button type="submit" class="flex-1 px-4 py-1.5 rounded-lg bg-[#155d50] hover:bg-[#0f463c] text-white text-xs font-bold transition shadow-xs flex items-center justify-center gap-1.5 cursor-pointer">
                    <i class="bi bi-check2"></i> Terapkan
                  </button>
                </div>
              </div>
            </div>
          </form>
        </div>

        <div class="space-y-0 divide-y divide-[#E8F2EE]">
          @forelse($rekapJadwalKelas as $rekapKls)
            <details class="group" open>
              <summary class="flex flex-wrap cursor-pointer items-center justify-between gap-3 px-5 py-4 bg-white hover:bg-[#F6FAF8] transition select-none list-none">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-xl bg-[#E3F2ED] flex items-center justify-center text-[#155d50] text-sm">
                    <i class="bi bi-people-fill"></i>
                  </div>
                  <div>
                    <div class="font-extrabold text-slate-800">{{ $rekapKls->nama_kelas }}</div>
                    @if($rekapKls->wali_kelas && $rekapKls->wali_kelas !== '-')
                      <div class="text-[11px] text-slate-400">Wali Kelas: {{ $rekapKls->wali_kelas }}</div>
                    @endif
                  </div>
                </div>
                <div class="flex flex-wrap items-center gap-2 text-[11px] font-bold">
                  <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-700">Total Sesi: {{ $rekapKls->total_sesi }}</span>
                  <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800">
                    <i class="bi bi-check2-circle"></i> Terisi: {{ $rekapKls->total_terisi }}
                  </span>
                  @if($rekapKls->total_kosong > 0)
                    <span class="px-2.5 py-1 rounded-full bg-rose-100 text-rose-800">
                      <i class="bi bi-x-circle"></i> Kosong: {{ $rekapKls->total_kosong }}
                    </span>
                  @else
                    <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700">
                      <i class="bi bi-check2-all"></i> Semua Terisi
                    </span>
                  @endif
                  <i class="bi bi-chevron-down text-slate-400 group-open:rotate-180 transition-transform ml-1"></i>
                </div>
              </summary>

              <div class="border-t border-[#E8F2EE] bg-[#F6FAF8]">
                @if($rekapKls->sesi_items->isNotEmpty())
                  <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                      <thead>
                        <tr class="bg-[#EEF7F3] text-[#4F6F66] font-semibold border-b border-[#E8F2EE]">
                          <th class="px-5 py-3">Jam Ke-</th>
                          <th class="px-5 py-3">Waktu</th>
                          <th class="px-5 py-3">Mata Pelajaran</th>
                          <th class="px-5 py-3">Guru Pengampu</th>
                          <th class="px-5 py-3 text-center">Status Jurnal</th>
                          <th class="px-5 py-3 text-center">Aksi</th>
                        </tr>
                      </thead>
                      <tbody class="divide-y divide-[#E8F2EE]">
                        @foreach($rekapKls->sesi_items as $sesi)
                          <tr class="{{ $sesi->is_terisi ? 'bg-white' : 'bg-rose-50/50' }} hover:bg-[#E3F2ED]/20 transition">
                            <td class="px-5 py-3 whitespace-nowrap font-bold text-slate-700">
                              {{ $sesi->jam_ke_formatted }}
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap text-slate-600">
                              @if($sesi->waktu_mulai)
                                {{ \Carbon\Carbon::parse($sesi->waktu_mulai)->format('H:i') }}
                                @if($sesi->waktu_selesai) – {{ \Carbon\Carbon::parse($sesi->waktu_selesai)->format('H:i') }} @endif
                              @else
                                <span class="text-slate-400">-</span>
                              @endif
                            </td>
                            <td class="px-5 py-3 font-medium text-slate-700">{{ $sesi->mapel }}</td>
                            <td class="px-5 py-3">
                              <div class="font-semibold text-slate-800">{{ $sesi->guru }}</div>
                              @if($sesi->guru_nip)<div class="text-[10px] text-slate-400">{{ $sesi->guru_nip }}</div>@endif
                            </td>
                            <td class="px-5 py-3 text-center whitespace-nowrap">
                              @if($sesi->is_terisi)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                  <i class="bi bi-check2-circle"></i> Sudah Terisi
                                </span>
                              @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-rose-100 text-rose-700 border border-rose-200">
                                  <i class="bi bi-x-circle"></i> Belum Terisi
                                </span>
                              @endif
                            </td>
                            <td class="px-5 py-3 text-center">
                              @if($sesi->is_terisi && $sesi->jurnal)
                                <button type="button" onclick="openDetailModal({{ $sesi->jurnal->id_jurnal }})"
                                        class="px-2 py-1 rounded-lg text-[11px] font-semibold text-[#155d50] bg-[#155d50]/10 hover:bg-[#155d50]/20 transition inline-flex items-center gap-1 cursor-pointer">
                                  <i class="bi bi-eye"></i> Lihat
                                </button>
                              @else
                                <span class="text-slate-400 text-[11px]">-</span>
                              @endif
                            </td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                @else
                  <div class="px-5 py-8 text-center text-xs text-slate-400 italic">
                    <i class="bi bi-calendar-x text-2xl text-slate-300 block mb-1"></i>
                    Tidak ada jadwal pelajaran terdaftar untuk kelas ini pada hari {{ $namaHari }}.
                  </div>
                @endif
              </div>
            </details>
          @empty
            <div class="p-12 text-center text-gray-400">
              <i class="bi bi-people text-4xl text-slate-300 mb-2 block"></i>
              <p class="font-bold text-slate-700 text-base">Tidak ada data kelas</p>
              <p class="text-xs text-slate-400 mt-1">Belum ada kelas yang terdaftar.</p>
            </div>
          @endforelse
        </div>

        <!-- Footer Rekap Kelas -->
        <div class="flex flex-col sm:flex-row items-center justify-between px-6 py-4 bg-[#F2F8F5] text-xs text-[#4F6F66] gap-2 border-t border-[#E8F2EE]">
          <span class="font-medium">
            Menampilkan jadwal hari <strong>{{ $namaHari }}</strong>,
            tanggal {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d M Y') }}
            — {{ $rekapJadwalKelas->count() }} kelas
          </span>
          <span class="text-[11px] text-slate-400">Terakhir diperbarui: {{ now()->format('H:i') }} WIB</span>
        </div>
      @endif

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

{{-- ================= JAVASCRIPT ================= --}}
<script>
    let currentActiveJurnal = null;

    // Digunakan di tab Rekap Guru & Rekap Kelas (pass id, fetch data via AJAX)
    async function openDetailModal(jurnalId) {
        try {
            const res = await fetch(`/piket/rekap-jurnal/detail/${jurnalId}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            if (!res.ok) throw new Error('Not found');
            const data = await res.json();
            openDetailJurnalModal(data);
        } catch (e) {
            openModal('modalDetailJurnal');
        }
    }

    // Digunakan langsung dengan objek jurnal (tab Riwayat Jurnal)
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

        document.getElementById('detailHadir').textContent = jurnal.jumlah_hadir ?? 0;
        document.getElementById('detailSakit').textContent = jurnal.jumlah_sakit ?? 0;
        document.getElementById('detailIzin').textContent = jurnal.jumlah_izin ?? 0;
        document.getElementById('detailAlpa').textContent = jurnal.jumlah_alpa ?? 0;
        document.getElementById('detailDispensasi').textContent = jurnal.jumlah_dispensasi ?? 0;

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

