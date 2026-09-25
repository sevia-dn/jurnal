<aside class="hidden md:flex flex-col w-64 bg-[#0D6B5A] h-screen fixed left-0 top-0 font-sans border-r border-[#17826E] z-40">
    {{-- Logo --}}
    <div class="px-6 pt-10 pb-8 flex items-center gap-3">
        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#1BA886]/30 text-xl text-[#B9F1E1]">
            <i class="bi bi-journal-text"></i>
        </div>
        <div>
            <div class="text-[22px] font-bold text-white leading-none">JurnalKita</div>
            <div class="text-[11px] text-emerald-200 font-semibold uppercase tracking-wider mt-1">Pengurus Kelas</div>
        </div>
    </div>

    {{-- Menus --}}
    <nav class="flex-1 px-4 space-y-1.5 overflow-y-auto">
        <a href="{{ route('pengurus-kelas.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors !no-underline {{ request()->routeIs('pengurus-kelas.dashboard') ? 'bg-[#1BA886] !text-white shadow-sm' : '!text-[#D9F7EE] hover:bg-[#1BA886]/10 hover:!text-white' }}">
            <i class="bi bi-grid-1x2-fill text-lg"></i><span>Dashboard</span>
        </a>
        <a href="{{ route('pengurus-kelas.jurnal-detail') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors !no-underline {{ request()->routeIs('pengurus-kelas.jurnal-detail') ? 'bg-[#1BA886] !text-white shadow-sm' : '!text-[#D9F7EE] hover:bg-[#1BA886]/10 hover:!text-white' }}">
            <i class="bi bi-clock-history text-lg"></i><span>Riwayat Logbook</span>
        </a>
        <a href="{{ route('pengurus-kelas.kehadiran-siswa') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors !no-underline {{ request()->routeIs('pengurus-kelas.kehadiran-siswa') ? 'bg-[#1BA886] !text-white shadow-sm' : '!text-[#D9F7EE] hover:bg-[#1BA886]/10 hover:!text-white' }}">
            <i class="bi bi-people-fill text-lg"></i><span>Absensi Siswa</span>
        </a>
    </nav>

    {{-- Profile & Logout --}}
    <div class="mt-auto border-t border-[#17826E] px-4 pb-6 pt-4">
        <div class="flex items-center justify-between gap-3">
            <div class="flex min-w-0 items-center gap-3">
                @php
                    $namaParts = explode(' ', Auth::user()->name);
                    $initials = strtoupper(substr($namaParts[0], 0, 1));
                    if (count($namaParts) > 1) {
                        $initials .= strtoupper(substr($namaParts[1], 0, 1));
                    }
                @endphp
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#DFFAF3] text-xs font-bold text-[#0D6B5A]">
                    {{ $initials }}
                </span>
                <div class="min-w-0">
                    <p class="truncate text-sm font-semibold text-white">{{ Auth::user()->name }}</p>
                    <p class="truncate text-[11px] text-[#AEE5D4]">Pengurus Kelas</p>
                </div>
            </div>

            <form action="{{ route('logout') }}" method="POST" class="m-0 p-0">
                @csrf
                <button type="submit"
                        title="Keluar"
                        class="inline-flex items-center justify-center p-1 text-white transition hover:text-slate-300 focus:outline-none focus:ring-2 focus:ring-white/50 rounded">
                    <i class="bi bi-box-arrow-right text-xl" aria-hidden="true"></i>
                </button>
            </form>
        </div>
    </div>
</aside>
