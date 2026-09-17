@php
    $activePage = $activePage ?? 'utama';
    $homeUrl = route('guru.utama');
    $historyUrl = route('guru.riwayat');
    $isHomeActive = request()->routeIs('guru.utama');
    $isHistoryActive = request()->routeIs('guru.riwayat');
@endphp

<div class="hidden items-center justify-between border-b border-slate-200 bg-white px-6 py-3 md:flex">
    <p class="text-base font-bold text-slate-800">Bapak/Ibu Guru Pengajar</p>

    <div x-data="{ openNotif: false }" class="relative">
        @php
            $notifBadgeCount = \App\Models\JurnalMengajar::where('id_user', Auth::id())->where('status_validasi', 'disetujui')->count();
        @endphp
        <button type="button"
                @click="openNotif = !openNotif"
                @click.outside="openNotif = false"
                class="relative flex h-10 w-10 items-center justify-center rounded-full text-slate-500 transition hover:bg-emerald-50 hover:text-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:ring-offset-2"
                aria-label="Notifikasi persetujuan dan revisi logbook"
                aria-expanded="openNotif">
            <i class="bi bi-bell text-xl" aria-hidden="true"></i>
            @if($notifBadgeCount > 0)
                <span class="absolute right-1.5 top-1.5 flex h-4 min-w-4 items-center justify-center rounded-full border-2 border-white bg-rose-500 px-1 text-[9px] font-bold text-white">{{ $notifBadgeCount }}</span>
            @endif
        </button>

        <div x-show="openNotif"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-2"
             x-cloak
             class="absolute right-0 mt-3 w-[360px] overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg">
            <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
                <h3 class="text-sm font-bold text-slate-800">Notifikasi</h3>
                <button type="button" class="text-xs font-medium text-emerald-600 hover:text-emerald-700">
                    Tandai sudah dibaca
                </button>
            </div>

            <div class="max-h-96 overflow-y-auto p-2">
    @php
        $notifNavbar = \App\Models\JurnalMengajar::with(['kelas', 'mapel'])
            ->where('id_user', Auth::id())
            ->where('status_validasi', 'disetujui')
            ->orderBy('id_jurnal', 'desc')
            ->take(5)
            ->get();
    @endphp
    @forelse($notifNavbar as $notif)
        <div class="rounded-xl border border-emerald-100 bg-emerald-50/40 p-3 mb-2">
            <div class="flex items-start gap-3">
                <span class="mt-0.5 flex h-9 w-9 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                    <i class="bi bi-check-circle-fill text-base" aria-hidden="true"></i>
                </span>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-bold text-slate-800">Logbook Disetujui</p>
                    <p class="mt-1 text-xs leading-relaxed text-slate-600">
                        Logbook {{ $notif->mapel->nama_mapel ?? '-' }} kelas {{ $notif->kelas->nama_kelas ?? '-' }} telah divalidasi oleh Pengurus Kelas.
                    </p>
                    <a href="{{ route('guru.riwayat') }}"
                        class="mt-2 inline-flex items-center rounded-md border border-emerald-200 bg-white px-2.5 py-1.5 text-[11px] font-semibold text-emerald-700 transition hover:bg-emerald-50"
                    >
                        Lihat 
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="rounded-xl border border-slate-100 bg-slate-50 p-4 text-center text-xs text-slate-400">
            Belum ada notifikasi persetujuan logbook.
        </div>
    @endforelse
</div>
        </div>
    </div>
</div>

<div class="flex items-center justify-between border-b border-slate-200 bg-white px-4 py-3 md:hidden">
    <a href="{{ $homeUrl }}" class="flex items-center gap-2" aria-label="JurnalKita, Halaman Utama">
        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-600 text-base text-white">
            <i class="bi bi-journal-check" aria-hidden="true"></i>
        </span>
        <span class="text-base font-bold tracking-tight text-slate-800">JurnalKita</span>
    </a>

    <div class="flex items-center gap-3">
        <div class="flex items-center gap-2">
            @php
                $namaParts = explode(' ', Auth::user()->name);
                $initials = strtoupper(substr($namaParts[0], 0, 1));
                if (count($namaParts) > 1) {
                    $initials .= strtoupper(substr($namaParts[1], 0, 1));
                }
            @endphp
            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-100 text-[10px] font-bold text-emerald-800">{{ $initials }}</span>
            <span class="max-w-24 truncate text-xs font-semibold text-slate-700">{{ Auth::user()->name }}</span>
        </div>

        <div x-data="{ openNotif: false }" class="relative">
            <button type="button"
                    @click="openNotif = !openNotif"
                    @click.outside="openNotif = false"
                    class="relative flex h-9 w-9 items-center justify-center rounded-full text-slate-500 transition hover:bg-emerald-50 hover:text-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-600"
                    aria-label="Notifikasi persetujuan dan revisi logbook"
                    aria-expanded="openNotif">
                <i class="bi bi-bell text-lg" aria-hidden="true"></i>
                @if($notifBadgeCount > 0)
                    <span class="absolute right-1 top-1 flex h-3.5 min-w-3.5 items-center justify-center rounded-full border border-white bg-rose-500 px-0.5 text-[8px] font-bold text-white">{{ $notifBadgeCount }}</span>
                @endif
            </button>

            <div x-show="openNotif"
                 x-cloak
                 class="absolute right-0 mt-3 w-72 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg">
                <div class="flex items-center justify-between border-b border-slate-100 px-3 py-2">
                    <h3 class="text-xs font-bold text-slate-800">Notifikasi</h3>
                    <button type="button" class="text-[10px] font-medium text-emerald-600">
                        Tandai sudah dibaca
                    </button>
                </div>

<div class="p-2">
    @forelse($notifNavbar ?? \App\Models\JurnalMengajar::with(['kelas', 'mapel'])->where('id_user', Auth::id())->where('status_validasi', 'disetujui')->orderBy('id_jurnal', 'desc')->take(5)->get() as $notifM)
        <div class="rounded-lg border border-emerald-100 bg-emerald-50/40 p-2.5 mb-2">
            <div class="flex items-start gap-2">
                <span class="mt-0.5 flex h-7 w-7 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                    <i class="bi bi-check-circle-fill text-sm" aria-hidden="true"></i>
                </span>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold text-slate-800">Logbook Disetujui</p>
                    <p class="mt-1 text-[11px] leading-relaxed text-slate-600">
                        Logbook {{ $notifM->mapel->nama_mapel ?? '-' }} {{ $notifM->kelas->nama_kelas ?? '-' }} telah divalidasi.
                    </p>
                    <a href="{{ route('guru.riwayat') }}"
                        class="mt-2 inline-flex items-center rounded-md border border-emerald-200 bg-white px-2.5 py-1.5 text-[11px] font-semibold text-emerald-700 transition hover:bg-emerald-50"
                    >
                        Lihat 
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="rounded-lg border border-slate-100 bg-slate-50 p-3 text-center text-[11px] text-slate-400">
            Belum ada notifikasi.
        </div>
    @endforelse
</div>
            </div>
        </div>
    </div>
</div>

<nav class="fixed bottom-0 left-0 right-0 z-50 flex items-center justify-around border-t border-slate-200 bg-white py-2 text-xs md:hidden" aria-label="Navigasi mobile">
    <a href="{{ $homeUrl }}"
       class="flex min-w-20 flex-col items-center gap-1 rounded-lg px-3 py-1.5 font-medium {{ $isHomeActive || $activePage === 'utama' ? 'text-emerald-700' : 'text-slate-400' }}"
       @if ($isHomeActive || $activePage === 'utama') aria-current="page" @endif>
        <i class="bi bi-house-door-fill text-lg" aria-hidden="true"></i>
        <span>Beranda</span>
    </a>

    <a href="{{ $historyUrl }}"
       class="flex min-w-20 flex-col items-center gap-1 rounded-lg px-3 py-1.5 font-medium {{ $isHistoryActive || $activePage === 'riwayat' ? 'text-emerald-700' : 'text-slate-400' }}"
       @if ($isHistoryActive || $activePage === 'riwayat') aria-current="page" @endif>
        <i class="bi bi-clock-history text-lg" aria-hidden="true"></i>
        <span>Riwayat</span>
    </a>

    <form action="{{ route('logout') }}" method="POST" class="m-0 p-0">
        @csrf
        <button type="submit"
                class="flex min-w-20 flex-col items-center gap-1 rounded-lg px-3 py-1.5 font-medium text-slate-400 transition hover:text-rose-500">
            <i class="bi bi-box-arrow-right text-lg" aria-hidden="true"></i>
            <span>Keluar</span>
        </button>
    </form>
</nav>

<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

<div x-data="{ openDetailModal: false }" @open-detail-modal.window="openDetailModal = true">
    <div
        x-show="openDetailModal"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm"
        @click="openDetailModal = false"
    >
        <div
            x-show="openDetailModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            @click.stop
            class="flex max-h-[90vh] w-full max-w-2xl flex-col overflow-hidden rounded-2xl bg-white shadow-xl"
        >
            <div class="flex shrink-0 items-center justify-between border-b border-slate-200 px-4 py-3 sm:px-6 sm:py-4">
                <h3 class="text-base font-bold text-slate-900 sm:text-lg">Detail Logbook Mengajar</h3>
                <button
                    type="button"
                    @click="openDetailModal = false"
                    class="flex h-8 w-8 items-center justify-center rounded-full text-slate-500 transition hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    aria-label="Tutup detail logbook"
                >
                    <i class="bi bi-x-lg text-base" aria-hidden="true"></i>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-4 sm:p-6">
                <div class="space-y-4 sm:space-y-5">
                    <div class="flex items-center justify-between gap-3">
                        <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-1 text-[10px] font-semibold text-emerald-700 sm:text-xs">
                            Disetujui
                        </span>
                        <span class="text-[10px] font-medium text-slate-400 sm:text-xs">Senin, 14 Sep 2026</span>
                    </div>

                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 sm:gap-4">
                        <div class="rounded-xl bg-slate-50 p-3">
                            <p class="text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-500 sm:text-[11px]">Tanggal</p>
                            <p class="mt-2 text-sm font-bold text-slate-800 sm:text-base">14 Sep 2026</p>
                        </div>

                        <div class="rounded-xl bg-slate-50 p-3">
                            <p class="text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-500 sm:text-[11px]">Mata Pelajaran</p>
                            <p class="mt-2 text-sm font-bold text-slate-800 sm:text-base">Informatika</p>
                        </div>

                        <div class="rounded-xl bg-slate-50 p-3">
                            <p class="text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-500 sm:text-[11px]">Kelas</p>
                            <p class="mt-2 text-sm font-bold text-slate-800 sm:text-base">XI RPL 2</p>
                        </div>

                        <div class="rounded-xl bg-slate-50 p-3">
                            <p class="text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-500 sm:text-[11px]">Jam Pelajaran</p>
                            <p class="mt-2 text-sm font-bold text-slate-800 sm:text-base">Jam ke 1-2</p>
                        </div>
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-slate-700">Ringkasan Materi</p>
                        <div class="mt-2 rounded-xl bg-slate-50 p-3 sm:p-4">
                            <p class="text-sm leading-relaxed text-slate-600">
                                Materi yang dibahas hari ini adalah pengenalan struktur data array dan penerapannya dalam program sederhana. Siswa mengikuti praktek dengan antusias dan menyelesaikan latihan secara berkelompok.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between rounded-xl border border-emerald-100 bg-emerald-50 py-2 px-3 sm:px-4">
                        <span class="text-sm font-medium text-slate-700">Kehadiran Siswa</span>
                        <span class="text-sm font-bold text-emerald-700">34 Hadir, 1 Sakit</span>
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-slate-700">Dokumentasi / Bukti Foto</p>
                        <div class="mt-2 flex h-32 items-center justify-center rounded-xl border border-dashed border-slate-300 bg-slate-100 sm:h-48">
                            <div class="flex flex-col items-center text-slate-400">
                                <i class="bi bi-image text-3xl" aria-hidden="true"></i>
                                <span class="mt-2 text-sm font-medium">Foto Dokumentasi</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
