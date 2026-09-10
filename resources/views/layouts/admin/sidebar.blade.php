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
    <a href="{{ route('catatan-jurnal') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors !no-underline {{ request()->routeIs('catatan-jurnal*') ? 'bg-[#1BA886] !text-white' : '!text-[#8EBEB2] hover:bg-[#1BA886]/10 hover:!text-white' }}">
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
  <div class="mt-auto px-5 pb-8 pt-4 flex flex-col">
       <!-- Tambah Akun (Admin) -->
    <a href="{{ route('tambah-akun') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors !no-underline {{ request()->routeIs('tambah-akun') ? 'bg-[#1BA886] !text-white' : '!text-[#8EBEB2] hover:bg-[#1BA886]/10 hover:!text-white' }}">
      <i class="bi bi-person-plus text-lg"></i>
      <span>Tambah Akun</span>
    </a>
    <!-- Garis Pemisah (Divider) -->
    <div class="h-px w-full bg-[#17826E] mb-3"></div>
    
    
    <!-- Keluar -->
    <a href="{{ route('login') }}" class="flex items-center gap-3 px-4 py-2 text-sm font-medium !text-[#F05252] hover:!text-red-400 !no-underline transition-colors">
      <i class="bi bi-box-arrow-right text-lg"></i>
      <span>Keluar</span>
    </a>

  </div>
  
</aside>
