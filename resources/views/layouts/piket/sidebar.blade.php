<!-- SIDEBAR PIKET DESKTOP -->
<aside class="hidden md:flex flex-col w-64 bg-[#0D6B5A] h-screen fixed left-0 top-0 font-sans border-r border-[#17826E] z-40">
  
  <!-- Brand Logo -->
  <div class="px-6 pt-10 pb-8 flex items-center gap-3">
    <div class="w-11 h-11 bg-[#1BA886]/30 text-[#4dbd9f] rounded-xl flex items-center justify-center text-xl shadow-sm">
      <i class="bi bi-mortarboard-fill"></i>
    </div>
    <div>
      <div class="text-[22px] font-bold text-white leading-none mb-1">JurnalKita</div>
      <div class="text-[11px] text-[#8EBEB2] font-semibold uppercase tracking-wider">Portal Piket</div>
    </div>
  </div>

  <!-- Menu Navigasi Utama -->
  <nav class="flex-1 px-4 overflow-y-auto space-y-1.5">
    
    <!-- Halaman Utama -->
    <a href="{{ route('dashboard.piket') }}" 
       class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors !no-underline {{ request()->routeIs('dashboard.piket') ? 'bg-[#1BA886] !text-white' : '!text-[#8EBEB2] hover:bg-[#1BA886]/10 hover:!text-white' }}">
      <i class="bi bi-grid-1x2-fill text-lg"></i>
      <span>Halaman Utama</span>
    </a>

    <!-- Kehadiran Guru -->
    <a href="{{ url('/dashboard/piket/kehadiran') }}"
       class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors !no-underline {{ request()->routeIs('piket.kehadiran') ? 'bg-[#1BA886] !text-white' : '!text-[#8EBEB2] hover:bg-[#1BA886]/10 hover:!text-white' }}">
      <i class="bi bi-person-check-fill text-lg"></i>
      <span>Kehadiran Guru</span>
    </a>

    <!-- Kehadiran Siswa (Disamakan stylenya dengan menu di atas) -->
    <a href="{{ url('/dashboard/piket/kehadiran-siswa') }}" 
       class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors !no-underline {{ request()->is('dashboard/piket/kehadiran-siswa*') ? 'bg-[#1BA886] !text-white' : '!text-[#8EBEB2] hover:bg-[#1BA886]/10 hover:!text-white' }}">
      <i class="bi bi-people-fill text-lg"></i>
      <span>Kehadiran Siswa</span>
    </a>

    <!-- Pengajuan Dispensasi -->
    <a href="{{ url('/dashboard/piket/dispensasi') }}"
       class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors !no-underline {{ request()->routeIs('piket.dispensasi') ? 'bg-[#1BA886] !text-white' : '!text-[#8EBEB2] hover:bg-[#1BA886]/10 hover:!text-white' }}">
      <i class="bi bi-file-earmark-text-fill text-lg"></i>
      <span>Pengajuan Dispensasi</span>
    </a>



  </nav>

  <!-- Footer Area -->
  <div class="mt-auto px-5 pb-8 pt-4 flex flex-col">
    <!-- Garis Pemisah (Divider) -->
    <div class="h-px w-full bg-[#17826E] mb-3"></div>
    
    <!-- Form Keluar / Logout -->
    <form action="{{ route('logout') }}" method="POST" class="w-full m-0 p-0">
        @csrf
        <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-sm font-medium !text-[#F05252] hover:!text-red-400 !no-underline transition-colors bg-transparent border-0 text-left cursor-pointer">
            <i class="bi bi-box-arrow-right text-lg"></i>
            <span>Keluar</span>
        </button>
    </form>
  </div>
  
</aside>