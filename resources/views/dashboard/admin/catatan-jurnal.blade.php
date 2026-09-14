@extends('layouts.app')

@section('title', 'Dashboard Admin - Catatan Jurnal')

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

    @if($errors->any())
        <div class="mb-6 rounded-xl bg-red-50 border border-red-200 p-4 text-sm text-red-800 shadow-xs">
            <div class="font-bold mb-1">Terjadi kesalahan:</div>
            <ul class="list-disc list-inside space-y-1 text-red-700 text-xs">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Header Halaman -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
      <div>
        <h1 class="text-2xl font-extrabold text-slate-900">Monitoring Jurnal Real-time</h1>
        <p class="text-sm text-slate-500 mt-1">Pantau status laporan kelas kejuruan dan kehadiran guru pengampu hari ini.</p>
      </div>
      <div class="flex flex-wrap items-center gap-2.5">
        <!-- Badge Tanggal Dipilih -->
        <div class="inline-flex items-center gap-2 px-3.5 py-2 bg-slate-100 border border-slate-200 rounded-full text-xs font-bold text-slate-700 shadow-xs">
          <i class="bi bi-calendar3 text-emerald-700"></i>
          <span>{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d M Y') }}</span>
        </div>
        <!-- Badge Jam Real-time -->
        <div class="inline-flex items-center gap-2 px-3.5 py-2 bg-white border border-slate-200 rounded-full text-xs font-bold text-[#155d50] shadow-xs">
          <i class="bi bi-clock"></i>
          <span id="liveClock">{{ now()->format('H:i') }} WIB</span>
        </div>
      </div>
    </div>

    <!-- 4 Kartu Statistik -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      
      <!-- Card 1: Total Kelas & Progress Lapor -->
      <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs flex flex-col justify-between hover:border-[#155d50]/40 transition">
        <div class="flex justify-between items-start gap-3">
          <div>
            <div class="text-[11px] uppercase tracking-wider font-bold text-slate-400">TOTAL KELAS</div>
            <div class="text-3xl font-extrabold text-slate-900 mt-2">{{ $totalKelas }}</div>
          </div>
          <div class="w-10 h-10 rounded-lg bg-[#155d50]/10 text-[#155d50] flex items-center justify-center text-lg shadow-xs">
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

      <!-- Card 2: Guru Hadir -->
      <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs flex flex-col justify-between hover:border-emerald-400 transition">
        <div class="flex justify-between items-start gap-3">
          <div>
            <div class="text-[11px] uppercase tracking-wider font-bold text-slate-400">GURU HADIR</div>
            <div class="text-3xl font-extrabold text-slate-900 mt-2">{{ $guruHadir }}</div>
          </div>
          <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg shadow-xs">
            <i class="bi bi-person-check-fill"></i>
          </div>
        </div>
        <div class="mt-4">
          <span class="text-xs font-bold text-emerald-600 flex items-center gap-1">
            <i class="bi bi-check2-circle"></i> Hadir Mengajar Hari Ini
          </span>
        </div>
      </div>

      <!-- Card 3: Guru Absen / Izin -->
      <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs flex flex-col justify-between hover:border-red-300 transition">
        <div class="flex justify-between items-start gap-3">
          <div>
            <div class="text-[11px] uppercase tracking-wider font-bold text-slate-400">GURU ABSEN / IZIN</div>
            <div class="text-3xl font-extrabold {{ $guruAbsen > 0 ? 'text-red-600' : 'text-slate-900' }} mt-2">{{ $guruAbsen }}</div>
          </div>
          <div class="w-10 h-10 rounded-lg bg-red-50 text-red-600 flex items-center justify-center text-lg shadow-xs">
            <i class="bi bi-person-slash"></i>
          </div>
        </div>
        <div class="mt-4">
          <span class="text-xs font-semibold text-red-600 flex items-center gap-1">
            <i class="bi bi-exclamation-triangle"></i> Membutuhkan Guru Inval
          </span>
        </div>
      </div>

      <!-- Card 4: Menunggu Validasi -->
      <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs flex flex-col justify-between hover:border-blue-300 transition">
        <div class="flex justify-between items-start gap-3">
          <div>
            <div class="text-[11px] uppercase tracking-wider font-bold text-slate-400">MENUNGGU VALIDASI</div>
            <div class="text-3xl font-extrabold {{ $menungguValidasi > 0 ? 'text-blue-600' : 'text-slate-900' }} mt-2">{{ $menungguValidasi }}</div>
          </div>
          <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-lg shadow-xs">
            <i class="bi bi-journal-text"></i>
          </div>
        </div>
        <div class="mt-4">
          <span class="text-xs font-semibold text-blue-600 flex items-center gap-1">
            <i class="bi bi-hourglass-split"></i> Jurnal Sekretaris Kelas
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
          <a href="{{ route('catatan-jurnal', array_merge(request()->query(), ['tab' => 'all'])) }}"
             class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition whitespace-nowrap {{ ($tab ?? 'all') === 'all' ? 'bg-[#E3F2ED] !text-[#0D6B5A]' : 'text-slate-500 hover:text-[#0D6B5A] hover:bg-slate-50' }} !no-underline flex items-center gap-1.5">
            <span>Semua Kelas</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ ($tab ?? 'all') === 'all' ? 'bg-[#0D6B5A]/15 text-[#0D6B5A]' : 'bg-slate-200 text-slate-600' }}">{{ $countSemua }}</span>
          </a>

          <a href="{{ route('catatan-jurnal', array_merge(request()->query(), ['tab' => 'belum_validasi'])) }}"
             class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition whitespace-nowrap {{ ($tab ?? '') === 'belum_validasi' ? 'bg-blue-50 !text-blue-700 border border-blue-200' : 'text-slate-500 hover:text-blue-700 hover:bg-blue-50/50' }} !no-underline flex items-center gap-1.5">
            <span>Belum Validasi</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ ($tab ?? '') === 'belum_validasi' ? 'bg-blue-200 text-blue-800' : 'bg-slate-200 text-slate-600' }}">{{ $countBelumValidasi }}</span>
          </a>

          <a href="{{ route('catatan-jurnal', array_merge(request()->query(), ['tab' => 'guru_absen'])) }}"
             class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition whitespace-nowrap {{ ($tab ?? '') === 'guru_absen' ? 'bg-red-50 !text-red-700 border border-red-200' : 'text-slate-500 hover:text-red-700 hover:bg-red-50/50' }} !no-underline flex items-center gap-1.5">
            <span>Guru Absen</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ ($tab ?? '') === 'guru_absen' ? 'bg-red-200 text-red-800' : 'bg-slate-200 text-slate-600' }}">{{ $countGuruAbsen }}</span>
          </a>
        </div>

        <!-- Tombol Toggle Filter Panel -->
        <button type="button" 
                onclick="toggleFilterPanel()" 
                id="btnFilterToggle"
                class="!text-[#0D6B5A] hover:!text-[#08483C] bg-[#E3F2ED]/60 hover:bg-[#E3F2ED] px-3.5 py-1.5 rounded-lg text-xs font-bold flex items-center gap-2 transition cursor-pointer border border-[#DCEBE5] self-end md:self-auto">
          <i class="bi bi-sliders text-sm"></i>
          <span>Filter Data</span>
          @if(request()->filled('kelas_id') || request()->filled('kehadiran') || request()->filled('validasi') || request()->filled('search') || request()->query('tanggal') != now()->toDateString())
            <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
          @endif
        </button>
      </div>

      <!-- Panel Form Filter Dropdown / Collapsible -->
      <div id="filterDataPanel" class="{{ (request()->filled('kelas_id') || request()->filled('kehadiran') || request()->filled('validasi') || request()->filled('search') || (request()->filled('tanggal') && request()->query('tanggal') != now()->toDateString())) ? '' : 'hidden' }} bg-[#F6FAF8] border-b border-[#E8F2EE] p-5 transition-all">
        <form method="GET" action="{{ route('catatan-jurnal') }}" class="space-y-4">
          <input type="hidden" name="tab" value="{{ $tab ?? 'all' }}">

          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5">
            <!-- Filter Tanggal -->
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">Pilih Tanggal</label>
              <input type="date" name="tanggal" value="{{ $tanggal }}" class="w-full text-xs bg-white border border-slate-300 rounded-lg px-3 py-2 text-slate-700 outline-none focus:border-[#155d50] focus:ring-1 focus:ring-[#155d50]">
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

            <!-- Filter Status Guru -->
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">Status Kehadiran Guru</label>
              <select name="kehadiran" class="w-full text-xs bg-white border border-slate-300 rounded-lg px-3 py-2 text-slate-700 outline-none focus:border-[#155d50] focus:ring-1 focus:ring-[#155d50] cursor-pointer">
                <option value="all">Semua Status Guru</option>
                <option value="Hadir" {{ ($kehadiran === 'Hadir') ? 'selected' : '' }}>Hadir</option>
                <option value="Izin" {{ ($kehadiran === 'Izin') ? 'selected' : '' }}>Izin</option>
                <option value="Sakit" {{ ($kehadiran === 'Sakit') ? 'selected' : '' }}>Sakit</option>
                <option value="Tanpa Keterangan" {{ ($kehadiran === 'Tanpa Keterangan') ? 'selected' : '' }}>Tanpa Keterangan</option>
              </select>
            </div>

            <!-- Filter Validasi -->
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">Status Validasi</label>
              <select name="validasi" class="w-full text-xs bg-white border border-slate-300 rounded-lg px-3 py-2 text-slate-700 outline-none focus:border-[#155d50] focus:ring-1 focus:ring-[#155d50] cursor-pointer">
                <option value="all">Semua Status Validasi</option>
                <option value="Selesai" {{ ($validasi === 'Selesai') ? 'selected' : '' }}>Selesai</option>
                <option value="Menunggu" {{ ($validasi === 'Menunggu') ? 'selected' : '' }}>Menunggu</option>
              </select>
            </div>

            <!-- Cari Kata Kunci -->
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">Cari Guru / Mapel / Materi</label>
              <div class="relative">
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Ketik kata kunci..." class="w-full text-xs bg-white border border-slate-300 rounded-lg pl-8 pr-3 py-2 text-slate-700 outline-none focus:border-[#155d50] focus:ring-1 focus:ring-[#155d50]">
                <i class="bi bi-search absolute left-2.5 top-2.5 text-slate-400 text-xs"></i>
              </div>
            </div>
          </div>

          <!-- Tombol Terapkan & Reset -->
          <div class="flex justify-end gap-2 pt-1">
            <a href="{{ route('catatan-jurnal', ['tab' => $tab ?? 'all']) }}" class="px-4 py-1.5 rounded-lg border border-slate-300 text-slate-600 hover:bg-white text-xs font-semibold transition !no-underline flex items-center gap-1">
              <i class="bi bi-arrow-counterclockwise"></i> Reset Filter
            </a>
            <button type="submit" class="px-5 py-1.5 rounded-lg bg-[#155d50] hover:bg-[#0f463c] text-white text-xs font-bold transition shadow-xs flex items-center gap-1.5 cursor-pointer">
              <i class="bi bi-check2"></i> Terapkan Filter
            </button>
          </div>
        </form>
      </div>

      <!-- Tabel Data Jurnal Mengajar -->
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-[#F2F8F5] text-[#4F6F66] text-xs font-semibold border-b border-[#E8F2EE]">
              <th class="px-6 py-4">Waktu</th>
              <th class="px-6 py-4">Kelas</th>
              <th class="px-6 py-4">Guru & Mata Pelajaran</th>
              <th class="px-6 py-4">Status Kehadiran</th>
              <th class="px-6 py-4">Validasi Guru Piket</th>
              <th class="px-6 py-4 text-center w-28">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#E8F2EE] text-sm text-slate-700">
            @forelse($jurnals as $idx => $item)
              @php
                $timeDisplay = $item->jam_mulai ? substr($item->jam_mulai, 0, 5) . ' WIB' : '07:30 WIB';
                $jamLabel = $item->jam_ke > 0 ? 'Jam ke-' . $item->jam_ke : 'Sesi Khusus';
                if ($item->jam_mulai && $item->jam_selesai) {
                    $jamRange = substr($item->jam_mulai, 0, 5) . ' - ' . substr($item->jam_selesai, 0, 5);
                } else {
                    $jamRange = $timeDisplay;
                }
              @endphp
              <tr class="{{ $idx % 2 === 0 ? 'bg-white' : 'bg-[#F6FAF8]' }} hover:bg-[#E3F2ED]/25 transition">
                <!-- Waktu -->
                <td class="px-6 py-4 text-[#4F6F66] font-medium whitespace-nowrap">
                  <div>{{ $timeDisplay }}</div>
                  <div class="text-[11px] text-slate-400 mt-0.5">{{ $jamLabel }}</div>
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
                    @if($item->guru_inval_id)
                      <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200" title="Guru Inval / Pengganti">
                        Inval: {{ optional($item->guruInval)->name }}
                      </span>
                    @endif
                  </div>
                  <div class="text-xs text-[#5C7E74] mt-0.5 font-medium">
                    {{ optional($item->mapel)->nama_mapel ?? 'Mata Pelajaran' }} ({{ $jamLabel }})
                  </div>
                  @if($item->materi)
                    <div class="text-[11px] text-slate-500 mt-1 line-clamp-1 italic">
                      "{{ $item->materi }}"
                    </div>
                  @endif
                </td>

                <!-- Status Kehadiran Guru -->
                <td class="px-6 py-4 whitespace-nowrap">
                  @if($item->status_kehadiran_guru === 'Hadir')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#E3F2ED] text-[#0D6B5A] border border-[#0D6B5A]/20">
                      <span class="w-1.5 h-1.5 rounded-full bg-[#0D6B5A]"></span>Hadir
                    </span>
                  @elseif($item->status_kehadiran_guru === 'Izin')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                      <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Izin
                    </span>
                  @elseif($item->status_kehadiran_guru === 'Sakit')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                      <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>Sakit
                    </span>
                  @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#FDE2E2] text-[#E02424] border border-[#E02424]/20">
                      <span class="w-1.5 h-1.5 rounded-full bg-[#E02424]"></span>{{ $item->status_kehadiran_guru }}
                    </span>
                  @endif
                </td>

                <!-- Validasi Sekretaris -->
                <td class="px-6 py-4 whitespace-nowrap">
                  @if($item->status_validasi === 'Selesai')
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold text-[#0D6B5A] bg-[#E3F2ED]/60 border border-[#0D6B5A]/15">
                      <i class="bi bi-check-circle-fill text-emerald-600 text-xs"></i> Selesai
                    </span>
                  @else
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-[#E0F2FE] text-[#0284C7] border border-[#0284C7]/20">
                      <i class="bi bi-clock text-xs"></i> Menunggu
                    </span>
                  @endif
                </td>

                <!-- Aksi -->
                <td class="px-6 py-4 text-center whitespace-nowrap">
                  <div class="flex items-center justify-center gap-1.5">
                    <!-- Tombol Detail -->
                    <button type="button" 
                            onclick='openDetailJurnalModal(@json($item))'
                            class="p-1.5 rounded-lg text-slate-500 hover:text-[#155d50] hover:bg-[#155d50]/10 transition cursor-pointer" 
                            title="Lihat Rincian Jurnal">
                      <i class="bi bi-eye text-base"></i>
                    </button>

                    <!-- Tombol Cepat Validasi (jika Menunggu) -->
                    @if($item->status_validasi === 'Menunggu')
                      <form action="{{ route('catatan-jurnal.validasi', $item->id_jurnal) }}" method="POST" class="inline" onsubmit="return confirm('Validasi jurnal mengajar kelas {{ optional($item->kelas)->nama_kelas }} sekarang?')">
                        @csrf
                        <button type="submit" class="p-1.5 rounded-lg text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50 transition cursor-pointer" title="Validasi Sekarang">
                          <i class="bi bi-check-lg text-base"></i>
                        </button>
                      </form>
                    @endif

                    <!-- Tombol Cepat Inval (jika guru absen & belum inval) -->
                    @if($item->status_kehadiran_guru !== 'Hadir')
                      <button type="button" 
                              onclick="openInvalModal('{{ $item->id_jurnal }}', '{{ addslashes(optional($item->kelas)->nama_kelas) }}', '{{ addslashes(optional($item->guru)->name) }}')" 
                              class="p-1.5 rounded-lg text-amber-600 hover:text-amber-700 hover:bg-amber-50 transition cursor-pointer" 
                              title="Tugaskan Guru Inval">
                        <i class="bi bi-person-plus text-base"></i>
                      </button>
                    @endif
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="p-12 text-center text-gray-400">
                  <div class="max-w-sm mx-auto">
                    <i class="bi bi-journal-x text-4xl text-slate-300 mb-2 block"></i>
                    <p class="font-bold text-slate-700 text-base">Tidak ada catatan jurnal mengajar</p>
                    <p class="text-xs text-slate-400 mt-1">
                      Belum ada sesi jurnal yang cocok dengan filter tanggal <strong>{{ \Carbon\Carbon::parse($tanggal)->format('d/m/Y') }}</strong>.
                    </p>
                    <div class="mt-4">
                      <a href="{{ route('catatan-jurnal') }}" class="text-xs font-bold text-[#155d50] hover:underline !no-underline">
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

      <!-- Pagination / Footer Status Bawah -->
      <div class="flex flex-col sm:flex-row items-center justify-between px-6 py-4 bg-[#F2F8F5] text-xs text-[#4F6F66] gap-2 border-t border-[#E8F2EE]">
        <span class="font-medium">
          Menampilkan {{ count($jurnals) }} catatan jurnal dari {{ $totalKelas }} kelas aktif
        </span>
        <div class="flex items-center gap-2">
          <span class="text-[11px] text-slate-400">Terakhir diperbarui: {{ now()->format('H:i') }} WIB</span>
        </div>
      </div>

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

            <!-- Status Kehadiran Guru & Inval -->
            <div class="p-3.5 rounded-xl border flex items-center justify-between" id="detailKehadiranBox">
              <div>
                <span class="text-slate-500 font-medium block text-[11px]">Status Kehadiran Guru:</span>
                <span id="detailKehadiranGuru" class="font-bold text-sm">-</span>
              </div>
              <div id="detailInvalBadge" class="hidden">
                <span class="px-2.5 py-1 rounded-md text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                  <i class="bi bi-person-badge"></i> <span id="detailInvalName">-</span>
                </span>
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

            <!-- Status Validasi -->
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between">
              <div class="flex items-center gap-2">
                <i class="bi bi-shield-check text-base text-[#155d50]"></i>
                <span class="font-medium text-slate-600">Status Validasi Guru Piket:</span>
              </div>
              <span id="detailStatusValidasi" class="font-bold">-</span>
            </div>

        </div>

        <!-- Footer / Action Buttons -->
        <div class="mt-6 flex flex-wrap items-center justify-between gap-3 border-t border-gray-100 pt-4">
            <button type="button" onclick="closeModal('modalDetailJurnal')" class="rounded-xl border border-gray-300 px-4 py-2 text-xs font-semibold text-gray-700 transition hover:bg-gray-50 cursor-pointer">
              Tutup
            </button>

            <div class="flex items-center gap-2">
              <!-- Form Validasi Sekarang -->
              <form id="formDetailValidasi" method="POST" action="" class="hidden">
                @csrf
                <button type="submit" class="rounded-xl bg-emerald-600 px-4 py-2 text-xs font-bold text-white shadow-xs transition hover:bg-emerald-700 flex items-center gap-1.5 cursor-pointer">
                  <i class="bi bi-check2-circle text-sm"></i> Validasi Sekarang
                </button>
              </form>

              <!-- Tombol Tugaskan Inval -->
              <button type="button" id="btnDetailInval" onclick="triggerInvalFromDetail()" class="hidden rounded-xl bg-amber-600 px-4 py-2 text-xs font-bold text-white shadow-xs transition hover:bg-amber-700 flex items-center gap-1.5 cursor-pointer">
                <i class="bi bi-person-plus text-sm"></i> Tugaskan Inval
              </button>
            </div>
        </div>

    </div>
</div>

{{-- ================= MODAL TUGASKAN GURU INVAL ================= --}}
<div id="modalInvalGuru" class="fixed inset-0 z-50 hidden items-center justify-center overflow-y-auto bg-gray-900/50 p-4 backdrop-blur-xs">
    <div role="dialog" aria-modal="true" class="relative w-full max-w-md rounded-2xl border border-gray-100 bg-white p-6 shadow-xl">
        
        <div class="mb-4 flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2.5">
              <div class="w-9 h-9 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-base">
                <i class="bi bi-person-plus-fill"></i>
              </div>
              <div>
                <h3 class="font-bold text-slate-900 text-base">Tugaskan Guru Inval</h3>
                <p class="text-[11px] text-slate-500">Guru pengganti untuk KBM kelas yang berhalangan</p>
              </div>
            </div>
            <button type="button" onclick="closeModal('modalInvalGuru')" class="text-gray-400 hover:text-gray-600 cursor-pointer">
              <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form id="formInvalGuru" method="POST" action="" class="space-y-4 text-xs">
          @csrf
          <div class="p-3 bg-amber-50/70 border border-amber-200 rounded-xl text-amber-900">
            <span class="block font-semibold">Target Sesi:</span>
            <span id="invalKelasNama" class="font-bold text-sm block mt-0.5">Kelas -</span>
            <span id="invalGuruAsli" class="text-[11px] text-amber-700 block mt-0.5">Guru Asli: -</span>
          </div>

          <div>
            <label class="block font-semibold text-slate-700 mb-1">Pilih Guru Inval / Pengganti <span class="text-red-500">*</span></label>
            <select name="guru_inval_id" required class="w-full text-xs bg-white border border-slate-300 rounded-xl p-2.5 text-slate-700 outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 cursor-pointer">
              <option value="" disabled selected>-- Pilih Guru Pengganti / Guru Piket --</option>
              @foreach($gurus as $g)
                <option value="{{ $g->id }}">{{ $g->name }} ({{ strtoupper($g->role) }})</option>
              @endforeach
            </select>
          </div>

          <div>
            <label class="block font-semibold text-slate-700 mb-1">Catatan Penugasan Inval (Opsional)</label>
            <textarea name="catatan_inval" rows="3" placeholder="Contoh: Mengawasi latihan soal modul bab 3 di lab komputer..." class="w-full text-xs bg-white border border-slate-300 rounded-xl p-2.5 text-slate-700 outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500"></textarea>
          </div>

          <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
            <button type="button" onclick="closeModal('modalInvalGuru')" class="px-4 py-2 border border-slate-300 rounded-xl text-slate-600 font-semibold hover:bg-slate-50 transition cursor-pointer">
              Batal
            </button>
            <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-xl shadow-xs transition cursor-pointer">
              Simpan & Tugaskan Inval
            </button>
          </div>
        </form>

    </div>
</div>

{{-- ================= JAVASCRIPT ================= --}}
<script>
    let currentActiveJurnal = null;

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
        const jamMulai = jurnal.jam_mulai ? jurnal.jam_mulai.substring(0, 5) : '07:30';
        const jamSelesai = jurnal.jam_selesai ? jurnal.jam_selesai.substring(0, 5) : '09:00';
        const jamKe = jurnal.jam_ke > 0 ? 'Jam ke-' + jurnal.jam_ke : 'Sesi Khusus';

        document.getElementById('detailSubtitle').textContent = `Sesi KBM ${kelasNama} • ${jamKe}`;
        document.getElementById('detailKelas').textContent = `${kelasNama} (Wali: ${waliKelas})`;
        document.getElementById('detailWaktu').textContent = `${jamMulai} - ${jamSelesai} WIB (${jamKe})`;
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

        // Status Kehadiran Guru Box
        const statusBox = document.getElementById('detailKehadiranBox');
        const statusText = document.getElementById('detailKehadiranGuru');
        statusText.textContent = jurnal.status_kehadiran_guru || 'Hadir';

        if (jurnal.status_kehadiran_guru === 'Hadir') {
            statusBox.className = 'p-3.5 rounded-xl border bg-emerald-50/70 border-emerald-200 text-emerald-900 flex items-center justify-between';
        } else if (jurnal.status_kehadiran_guru === 'Izin') {
            statusBox.className = 'p-3.5 rounded-xl border bg-amber-50/70 border-amber-200 text-amber-900 flex items-center justify-between';
        } else {
            statusBox.className = 'p-3.5 rounded-xl border bg-red-50/70 border-red-200 text-red-900 flex items-center justify-between';
        }

        // Inval Badge
        const invalBadge = document.getElementById('detailInvalBadge');
        if (jurnal.guru_inval) {
            invalBadge.classList.remove('hidden');
            document.getElementById('detailInvalName').textContent = 'Inval: ' + jurnal.guru_inval.name;
        } else {
            invalBadge.classList.add('hidden');
        }

        // Status Validasi
        const validasiSpan = document.getElementById('detailStatusValidasi');
        const formValidasi = document.getElementById('formDetailValidasi');
        const btnInval = document.getElementById('btnDetailInval');

        if (jurnal.status_validasi === 'Selesai') {
            validasiSpan.innerHTML = '<span class="text-emerald-700 bg-emerald-100 px-2.5 py-1 rounded-md text-xs font-bold"><i class="bi bi-check-circle-fill"></i> Sudah Divalidasi</span>';
            formValidasi.classList.add('hidden');
        } else {
            validasiSpan.innerHTML = '<span class="text-blue-700 bg-blue-100 px-2.5 py-1 rounded-md text-xs font-bold"><i class="bi bi-clock"></i> Menunggu Validasi</span>';
            formValidasi.classList.remove('hidden');
            formValidasi.action = "{{ url('dashboard/catatan-jurnal/validasi') }}/" + jurnal.id_jurnal;
        }

        // Inval Action Button
        if (jurnal.status_kehadiran_guru !== 'Hadir') {
            btnInval.classList.remove('hidden');
        } else {
            btnInval.classList.add('hidden');
        }

        openModal('modalDetailJurnal');
    }

    function triggerInvalFromDetail() {
        if (!currentActiveJurnal) return;
        closeModal('modalDetailJurnal');
        openInvalModal(
            currentActiveJurnal.id_jurnal,
            currentActiveJurnal.kelas ? currentActiveJurnal.kelas.nama_kelas : '',
            currentActiveJurnal.guru ? currentActiveJurnal.guru.name : ''
        );
    }

    function openInvalModal(id, kelasNama, guruNama) {
        const form = document.getElementById('formInvalGuru');
        form.action = "{{ url('dashboard/catatan-jurnal/inval') }}/" + id;
        document.getElementById('invalKelasNama').textContent = 'Kelas ' + kelasNama;
        document.getElementById('invalGuruAsli').textContent = 'Guru Pengampu: ' + guruNama;

        openModal('modalInvalGuru');
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
