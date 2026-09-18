<aside class="hidden md:flex flex-col w-64 bg-[#0D6B5A] h-screen fixed left-0 top-0 font-sans border-r border-[#17826E] z-40">
    <!-- Logo -->
    <div class="px-6 pt-10 pb-8 flex items-center gap-3">
        <div class="w-11 h-11 bg-white/20 text-white rounded-xl flex items-center justify-center text-xl shadow-sm">
            <i class="bi bi-journal-text"></i>
        </div>
        <div>
            <div class="text-[22px] font-bold text-white leading-none mb-1">JurnalKita</div>
            <div class="text-[11px] text-emerald-200 font-semibold uppercase tracking-wider">Pengurus Kelas</div>
        </div>
    </div>

    <!-- Menus -->
    <nav class="flex-1 px-4 space-y-1.5">
        <a href="{{ route('pengurus-kelas.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors !no-underline {{ request()->routeIs('pengurus-kelas.dashboard') ? 'bg-[#1BA886] !text-white' : '!text-[#8EBEB2] hover:bg-[#1BA886]/10 hover:!text-white' }}">
            <i class="bi bi-grid-1x2-fill text-lg"></i><span>Dashboard</span>
        </a>
        <a href="{{ route('pengurus-kelas.jadwal') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors !no-underline {{ request()->routeIs('pengurus-kelas.jadwal', 'pengurus-kelas.jurnal-detail', 'pengurus-kelas.kehadiran-guru') ? 'bg-[#1BA886] !text-white' : '!text-[#8EBEB2] hover:bg-[#1BA886]/10 hover:!text-white' }}">
            <i class="bi bi-calendar-week-fill text-lg"></i><span>Jadwal & Jurnal</span>
        </a>
        <a href="{{ route('pengurus-kelas.kehadiran-siswa') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors !no-underline {{ request()->routeIs('pengurus-kelas.kehadiran-siswa') ? 'bg-[#1BA886] !text-white' : '!text-[#8EBEB2] hover:bg-[#1BA886]/10 hover:!text-white' }}">
            <i class="bi bi-people-fill text-lg"></i><span>Absensi Siswa</span>
        </a>
    </nav>

    <!-- Footer / Logout -->
    <div class="mt-auto px-5 pb-8 pt-4 flex flex-col">
        <div class="h-px w-full bg-[#17826E] mb-3"></div>
        <form action="{{ route('logout') }}" method="POST" class="w-full m-0 p-0">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-sm font-medium !text-[#F05252] hover:!text-red-400 !no-underline transition-colors bg-transparent border-0 text-left cursor-pointer">
                <i class="bi bi-box-arrow-right text-lg"></i><span>Keluar</span>
            </button>
        </form>
    </div>
</aside>