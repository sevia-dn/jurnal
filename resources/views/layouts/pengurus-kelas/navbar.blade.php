{{-- TOP NAVBAR --}}
<div class="px-4 md:px-8 py-3.5 flex justify-between items-center h-[72px] w-full bg-white border-b border-slate-200">

    {{-- Title Section --}}
    <div class="flex items-center gap-3">
        <div class="md:hidden w-9 h-9 bg-[#0D6B5A] text-white rounded-lg flex items-center justify-center text-base">
            <i class="bi bi-journal-text"></i>
        </div>
        <div>
            <h1 class="font-bold text-slate-800 text-base md:text-lg">Pengurus Harian Kelas</h1>
            <p class="text-xs text-slate-400 hidden md:block">Kelola jurnal dan absensi harian kelas</p>
        </div>
    </div>

    {{-- Right Section: Notification Bell only --}}
    <div class="flex items-center">
        <button class="relative p-2 text-slate-400 hover:text-emerald-600 transition-colors focus:outline-none cursor-pointer" aria-label="Notifikasi">
            <i class="bi bi-bell-fill text-xl"></i>
            <span class="absolute top-1.5 right-1.5 flex h-2.5 w-2.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500 border-2 border-white"></span>
            </span>
        </button>
    </div>
</div>

{{-- BOTTOM NAVBAR (Mobile Only) --}}
<nav class="md:hidden fixed bottom-0 left-0 w-full h-[72px] bg-white border-t border-slate-200 grid grid-cols-3 items-center z-50 shadow-[0_-4px_20px_rgba(0,0,0,0.05)]">

    <a href="{{ route('pengurus-kelas.dashboard') }}" class="flex flex-col items-center justify-center h-full w-full {{ request()->routeIs('pengurus-kelas.dashboard') ? 'text-emerald-600 font-bold' : 'text-slate-400 hover:text-emerald-600 transition-colors' }}">
        <i class="bi bi-grid-1x2-fill text-2xl mb-1"></i>
        <span class="text-[10px] leading-none">Utama</span>
    </a>

    <a href="{{ route('pengurus-kelas.jadwal') }}" class="flex flex-col items-center justify-center h-full w-full {{ request()->routeIs('pengurus-kelas.jadwal', 'pengurus-kelas.jurnal-detail', 'pengurus-kelas.kehadiran-guru') ? 'text-emerald-600 font-bold' : 'text-slate-400 hover:text-emerald-600 transition-colors' }}">
        <i class="bi bi-calendar-week-fill text-2xl mb-1"></i>
        <span class="text-[10px] leading-none">Jadwal</span>
    </a>

    <a href="{{ route('pengurus-kelas.kehadiran-siswa') }}" class="flex flex-col items-center justify-center h-full w-full {{ request()->routeIs('pengurus-kelas.kehadiran-siswa') ? 'text-emerald-600 font-bold' : 'text-slate-400 hover:text-emerald-600 transition-colors' }}">
        <i class="bi bi-people-fill text-2xl mb-1"></i>
        <span class="text-[10px] leading-none">Absensi</span>
    </a>

</nav>