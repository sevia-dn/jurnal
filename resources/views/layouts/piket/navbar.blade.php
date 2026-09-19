<!-- TOP NAVBAR DESKTOP & MOBILE HEADER -->
<header class="bg-white border-b border-slate-200 sticky top-0 z-30 px-4 md:px-8 py-3.5 flex justify-between items-center">
    
    <!-- Title Section -->
    <div class="flex items-center gap-3">
        <!-- Logo Kecil Khusus Mobile -->
        <div class="md:hidden w-9 h-9 bg-emerald-600 text-white rounded-lg flex items-center justify-center text-base">
            <i class="bi bi-mortarboard-fill"></i>
        </div>
        <div>
            <h1 class="font-bold text-slate-800 text-base md:text-lg">Guru Piket</h1>
            <p class="text-xs text-slate-400 hidden md:block">Kelola jurnal harian dan presensi presisi real-time</p>
        </div>
    </div>

    <!-- User Profile Badge -->

</header>

<!-- BOTTOM NAVBAR KHUSUS MOBILE (Sembunyi di Desktop) -->
<nav class="md:hidden fixed bottom-0 left-0 w-full bg-white border-t border-slate-200 flex justify-around items-center h-16 px-2 z-50 shadow-[0_-4px_20px_rgba(0,0,0,0.05)]">
    
    <!-- Halaman Utama -->
    <a href="{{ route('dashboard.piket') }}" 
       class="flex flex-col items-center justify-center w-full py-1 {{ request()->routeIs('dashboard.piket') ? 'text-emerald-600 font-bold' : 'text-slate-400 hover:text-slate-600' }}">
        <i class="bi bi-grid-1x2-fill text-lg"></i>
        <span class="text-[10px] mt-0.5">Utama</span>
    </a>

    <!-- Kehadiran Guru -->
    <a href="{{ url('/dashboard/piket/kehadiran') }}" 
       class="flex flex-col items-center justify-center w-full py-1 {{ request()->routeIs('piket.kehadiran') ? 'text-emerald-600 font-bold' : 'text-slate-400 hover:text-slate-600' }}">
        <i class="bi bi-person-check-fill text-lg"></i>
        <span class="text-[10px] mt-0.5">Kehadiran</span>
    </a>

    <!-- Dispensasi -->
    <a href="{{ url('/dashboard/piket/dispensasi') }}" 
       class="flex flex-col items-center justify-center w-full py-1 {{ request()->routeIs('piket.dispensasi') ? 'text-emerald-600 font-bold' : 'text-slate-400 hover:text-slate-600' }}">
        <i class="bi bi-file-earmark-text-fill text-lg"></i>
        <span class="text-[10px] mt-0.5">Dispensasi</span>
    </a>

    <!-- Kehadiran Siswa (Sudah disamakan ukurannya) -->
    <a href="{{ url('/dashboard/piket/kehadiran-siswa') }}" 
       class="flex flex-col items-center justify-center w-full py-1 {{ request()->is('dashboard/piket/kehadiran-siswa*') ? 'text-emerald-600 font-bold' : 'text-slate-400 hover:text-slate-600' }}">
        <i class="bi bi-people-fill text-lg"></i>
        <span class="text-[10px] mt-0.5">Siswa</span>
    </a>

</nav>