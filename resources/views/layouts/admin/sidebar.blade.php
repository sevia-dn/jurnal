<style>
  /* Aksi massal tidak dipakai pada dashboard admin; setiap data dikelola per baris. */
  #batchActionBar,
  #batchActionBarKelas,
  #batchActionBarSiswa,
  #batchActionBarMapel,
  #batchActionBarJadwal {
    display: none !important;
  }

  th:has(#selectAllGuru), td:has(.guru-checkbox),
  th:has(#selectAllKelas), td:has(.kelas-checkbox),
  th:has(#selectAllSiswa), td:has(.siswa-checkbox),
  th:has(#selectAllMapel), td:has(.mapel-checkbox),
  th:has(#selectAllJadwal), td:has(.jadwal-checkbox) {
    display: none !important;
  }
</style>

<aside class="w-64 bg-[#0D6B5A] h-screen sticky top-0 flex flex-col font-sans border-r border-[#17826E]">
  
  <!-- Logo Area -->
  <div class="px-6 pt-10 pb-8 flex items-center gap-3">
    <div class="w-11 h-11 bg-[#1BA886]/30 text-[#4dbd9f] rounded-xl flex items-center justify-center text-xl">
      <i class="bi bi-mortarboard-fill"></i>
    </div>
    <div>
      <div class="text-[22px] font-bold text-white leading-none mb-1">JurnalKita</div>
      <div class="text-[11px] text-[#8EBEB2] font-medium tracking-wide">Management System</div>
    </div>
  </div>

  <!-- Navigation Area -->
  <nav class="flex-1 px-4 overflow-y-auto space-y-1.5">
    
    <!-- Halaman Utama -->
    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors !no-underline {{ request()->routeIs('dashboard') ? 'bg-[#1BA886] !text-white' : '!text-[#8EBEB2] hover:bg-[#1BA886]/10 hover:!text-white' }}">
      <i class="bi bi-grid text-lg"></i>
      <span>Halaman Utama</span>
    </a>

    <!-- Data Guru -->
    <a href="{{ route('dashboard.guru') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors !no-underline {{ request()->routeIs('dashboard.guru') ? 'bg-[#1BA886] !text-white' : '!text-[#8EBEB2] hover:bg-[#1BA886]/10 hover:!text-white' }}">  
      <i class="bi bi-person text-lg"></i>
      <span>Data Guru</span>
    </a>

    <!-- Data Kelas -->
    <a href="{{ route('dashboard.kelas') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors !no-underline {{ request()->routeIs('dashboard.kelas') ? 'bg-[#1BA886] !text-white' : '!text-[#8EBEB2] hover:bg-[#1BA886]/10 hover:!text-white' }}">
      <i class="bi bi-door-closed text-lg"></i>
      <span>Data Kelas</span>
    </a>

<!-- Data Siswa -->
    <a href="{{ route('dashboard.siswa') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors !no-underline {{ request()->routeIs('dashboard.siswa') ? 'bg-[#1BA886] !text-white' : '!text-[#8EBEB2] hover:bg-[#1BA886]/10 hover:!text-white' }}">
      <i class="bi bi-mortarboard text-lg"></i>
      <span>Data Siswa</span>
    </a>

    <!-- TAMBAHAN BARU: Data Mapel / Jadwal -->
    <a href="{{ route('dashboard.mapel') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors !no-underline {{ request()->routeIs('dashboard.mapel') ? 'bg-[#1BA886] !text-white' : '!text-[#8EBEB2] hover:bg-[#1BA886]/10 hover:!text-white' }}">
      <i class="bi bi-journal-bookmark text-lg"></i>
      <span>Mata Pelajaran</span>
    </a>

     <!-- Menu Jadwal Pelajaran -->
    <a href="{{ route('dashboard.jadwal') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors !no-underline {{ request()->routeIs('dashboard.jadwal') ? 'bg-[#1BA886] !text-white' : '!text-[#8EBEB2] hover:bg-[#1BA886]/10 hover:!text-white' }}">
      <i class="bi bi-calendar-week"></i>
      <span>Jadwal Pelajaran</span>
    </a>

    <!-- Catatan Jurnal -->
    <a href="{{ route('dashboard.rekap-jurnal') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors !no-underline {{ request()->routeIs('catatan-jurnal*', 'dashboard.rekap-jurnal') ? 'bg-[#1BA886] !text-white' : '!text-[#8EBEB2] hover:bg-[#1BA886]/10 hover:!text-white' }}">
      <i class="bi bi-book text-lg"></i>
      <span>Catatan Jurnal</span>
    </a>

    <!-- Manajemen User -->
    <a href="{{ route('admin.manajemen-user') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors !no-underline {{ request()->routeIs('admin.manajemen-user') || request()->is('dashboard/admin/manajemen-user') ? 'bg-[#1BA886] !text-white' : '!text-[#8EBEB2] hover:bg-[#1BA886]/10 hover:!text-white' }}">
      <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-4-4h-1m-4 6H3v-2a4 4 0 014-4h6a4 4 0 014 4v2zM10 10a4 4 0 100-8 4 4 0 000 8zm7-2a3 3 0 100-6" />
      </svg>
      <span>Manajemen User</span>
    </a>

    
  </nav>


  <!-- Footer Area -->
  <div class="mt-auto px-5 pb-6 pt-4 flex flex-col gap-3">
    <!-- Tambah Akun (Admin) -->
    <a href="{{ route('tambah-akun') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors !no-underline {{ request()->routeIs('tambah-akun') ? 'bg-[#1BA886] !text-white' : '!text-[#8EBEB2] hover:bg-[#1BA886]/10 hover:!text-white' }}">
      <i class="bi bi-person-plus text-lg"></i>
      <span>Tambah Akun</span>
    </a>

    <div class="h-px w-full bg-[#17826E]"></div>

    @php
      $adminName = Auth::user()?->name ?? 'Administrator';
      $adminInitials = collect(preg_split('/\s+/', trim($adminName)))
        ->filter()
        ->take(2)
        ->map(fn ($word) => strtoupper(mb_substr($word, 0, 1)))
        ->implode('');
    @endphp

    <div class="flex items-center gap-2 rounded-xl bg-[#0A594B] border border-[#17826E] px-3 py-3 shadow-sm">
      <div class="w-9 h-9 shrink-0 rounded-full bg-[#1BA886] text-white flex items-center justify-center font-bold text-xs">
        {{ $adminInitials ?: 'A' }}
      </div>
      <div class="min-w-0 flex-1">
        <p class="truncate text-sm font-semibold text-white">{{ $adminName }}</p>
        <p class="text-[11px] text-[#8EBEB2]">Administrator</p>
      </div>
      <form action="{{ route('logout') }}" method="POST" class="m-0 shrink-0">
        @csrf
        <button type="submit" class="w-9 h-9 rounded-lg flex items-center justify-center text-rose-200 hover:text-white hover:bg-rose-500/20 transition-colors bg-transparent border-0 cursor-pointer" title="Keluar">
            <i class="bi bi-box-arrow-right text-lg"></i>
        </button>
      </form>
    </div>

  </div>
  
</aside>
