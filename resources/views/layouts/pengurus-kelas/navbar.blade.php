@php
    $user = Auth::user();
    $rawName = $user?->name ?? 'Kelas';
    $namaKelas = trim(preg_replace('/^Pengurus Kelas\s+/i', '', $rawName));
    $inisialKelas = collect(explode(' ', $namaKelas))->take(3)->map(fn($w) => strtoupper(substr($w, 0, 1)))->implode('');

    // Ambil kelas pengurus untuk filter notifikasi berdasarkan kelas
    $kelasPengurusNavbar = \App\Models\Kelas::where('nama_kelas', $namaKelas)
        ->orWhere('nama_kelas', $rawName)
        ->first();

    // Notifikasi: dispensasi disetujui + guru tidak hadir + kehadiran piket
    $notifNavbarPengurus = \App\Models\Notifikasi::where(function ($query) use ($user, $kelasPengurusNavbar) {
            $query->where('id_user', $user?->id);
            if ($kelasPengurusNavbar) {
                $query->orWhere('id_kelas', $kelasPengurusNavbar->id_kelas);
            }
        })
        ->latest()
        ->take(10)
        ->get();

    $unreadCountPengurus = $notifNavbarPengurus->where('is_read', false)->count();
@endphp

{{-- TOP NAVBAR --}}
<div class="px-4 md:px-8 py-3.5 flex justify-between items-center h-[72px] w-full bg-white border-b border-slate-200">

    {{-- Title Section: Mobile hanya profil kelas + tulisan kelas (contoh: XI RPL 2) tanpa tulisan "Pengurus Harian Kelas" --}}
    <div class="flex items-center gap-2.5">
        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#0D6B5A] text-white text-xs font-extrabold shadow-sm">
            <span>{{ $inisialKelas ?: 'K' }}</span>
        </div>
        <div>
            <h1 class="font-bold text-slate-800 text-sm md:text-lg leading-tight">
                <span class="md:hidden uppercase tracking-wider font-extrabold">{{ $namaKelas }}</span>
                <span class="hidden md:inline">Pengurus Harian Kelas {{ $namaKelas }}</span>
            </h1>
            <p class="text-xs text-slate-400 hidden md:block">Kelola jurnal dan absensi harian kelas</p>
        </div>
    </div>

    {{-- Right Section: Notification Bell Dropdown --}}
    <div class="flex items-center" x-data="{ openNotif: false }" @keydown.escape.window="openNotif = false">
        <div class="relative">
            <button
                type="button"
                @click.stop="openNotif = !openNotif"
                @click.outside="openNotif = false"
                class="relative flex h-10 w-10 items-center justify-center rounded-full text-slate-500 transition hover:bg-emerald-50 hover:text-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:ring-offset-2 cursor-pointer"
                aria-label="Notifikasi"
                :aria-expanded="openNotif"
            >
                <i class="bi bi-bell text-xl" aria-hidden="true"></i>
                @if($unreadCountPengurus > 0)
                    <span class="absolute right-1.5 top-1.5 flex h-4 min-w-4 items-center justify-center rounded-full border-2 border-white bg-rose-500 px-1 text-[9px] font-bold text-white">
                        {{ $unreadCountPengurus > 9 ? '9+' : $unreadCountPengurus }}
                    </span>
                @endif
            </button>

            <div
                x-show="openNotif"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-2"
                x-cloak
                class="absolute right-0 mt-3 w-[340px] max-w-[calc(100vw-2rem)] overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg z-50"
            >
                <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
                    <h3 class="text-sm font-bold text-slate-800">Notifikasi</h3>
                    @if($unreadCountPengurus > 0)
                        <form action="{{ route('pengurus-kelas.notifikasi.read-all') }}" method="POST" class="m-0 inline">
                            @csrf
                            <button type="submit" class="text-xs font-medium text-emerald-600 hover:text-emerald-700 cursor-pointer">
                                Tandai sudah dibaca
                            </button>
                        </form>
                    @endif
                </div>

                <div class="max-h-96 overflow-y-auto p-2">
                    @forelse($notifNavbarPengurus as $notif)
                        @php
                            $isGuruTidakHadir = $notif->tipe === 'guru_tidak_hadir';
                            $isKehadiranPiket = $notif->tipe === 'kehadiran_siswa_piket';
                            $badgeColor = $isGuruTidakHadir
                                ? 'bg-amber-100 text-amber-600'
                                : ($isKehadiranPiket ? 'bg-purple-100 text-purple-700' : 'bg-indigo-100 text-indigo-600');
                            $borderColor = $notif->is_read
                                ? 'border-slate-100 bg-slate-50/40'
                                : ($isGuruTidakHadir ? 'border-amber-100 bg-amber-50/40' : ($isKehadiranPiket ? 'border-purple-100 bg-purple-50/40' : 'border-indigo-100 bg-indigo-50/40'));
                            $iconClass = $isGuruTidakHadir
                                ? 'bi-person-x-fill'
                                : ($isKehadiranPiket ? 'bi-clipboard-check-fill' : 'bi-patch-check-fill');
                        @endphp
                        <div class="rounded-xl border p-3 mb-2 {{ $borderColor }}">
                            <div class="flex items-start gap-3">
                                <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-full {{ $badgeColor }}">
                                    <i class="bi {{ $iconClass }} text-base" aria-hidden="true"></i>
                                </span>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center justify-between gap-1">
                                        <p class="text-sm font-bold text-slate-800">{{ $notif->judul }}</p>
                                        @if(!$notif->is_read)
                                            <span class="h-2 w-2 shrink-0 rounded-full bg-rose-500"></span>
                                        @endif
                                    </div>
                                    <p class="mt-1 text-xs leading-relaxed text-slate-600">{{ $notif->pesan }}</p>
                                    <p class="mt-1 text-[10px] text-slate-400">{{ $notif->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-xl border border-slate-100 bg-slate-50 p-5 text-center">
                            <i class="bi bi-bell-slash text-2xl text-slate-300"></i>
                            <p class="mt-2 text-xs text-slate-400">Belum ada notifikasi.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

{{-- BOTTOM NAVBAR (Mobile Only) --}}
<nav class="md:hidden fixed bottom-0 left-0 w-full h-[72px] bg-white border-t border-slate-200 grid grid-cols-4 items-center z-50 shadow-[0_-4px_20px_rgba(0,0,0,0.05)]">

    {{-- 1. UTAMA --}}
    <a href="{{ route('pengurus-kelas.dashboard') }}" class="flex flex-col items-center justify-center h-full w-full {{ request()->routeIs('pengurus-kelas.dashboard') ? 'text-emerald-600 font-bold' : 'text-slate-400 hover:text-emerald-600 transition-colors' }}">
        <i class="bi bi-grid-1x2-fill text-xl mb-1"></i>
        <span class="text-[10px] leading-none">Utama</span>
    </a>

    {{-- 2. RIWAYAT LOGBOOK (Gantikan icon jadwal) --}}
    <a href="{{ route('pengurus-kelas.jurnal-detail') }}" class="flex flex-col items-center justify-center h-full w-full {{ request()->routeIs('pengurus-kelas.jurnal-detail') ? 'text-emerald-600 font-bold' : 'text-slate-400 hover:text-emerald-600 transition-colors' }}">
        <i class="bi bi-journal-check text-xl mb-1"></i>
        <span class="text-[10px] leading-none">Riwayat</span>
    </a>

    {{-- 3. ABSENSI SISWA --}}
    <a href="{{ route('pengurus-kelas.kehadiran-siswa') }}" class="flex flex-col items-center justify-center h-full w-full {{ request()->routeIs('pengurus-kelas.kehadiran-siswa') ? 'text-emerald-600 font-bold' : 'text-slate-400 hover:text-emerald-600 transition-colors' }}">
        <i class="bi bi-people-fill text-xl mb-1"></i>
        <span class="text-[10px] leading-none">Absensi</span>
    </a>

    {{-- 4. LOG OUT --}}
    <form action="{{ route('logout') }}" method="POST" class="m-0 p-0 h-full w-full">
        @csrf
        <button type="submit" class="flex flex-col items-center justify-center h-full w-full text-slate-400 hover:text-rose-600 transition-colors cursor-pointer">
            <i class="bi bi-box-arrow-right text-xl mb-1"></i>
            <span class="text-[10px] leading-none">Keluar</span>
        </button>
    </form>

</nav>
