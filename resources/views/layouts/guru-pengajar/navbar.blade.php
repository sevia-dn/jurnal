@php
    $activePage = $activePage ?? 'utama';
    $homeUrl = route('guru.utama');
    $historyUrl = route('guru.riwayat');
    $isHomeActive = request()->routeIs('guru.utama');
    $isHistoryActive = request()->routeIs('guru.riwayat');
@endphp

<div class="hidden items-center justify-between border-b border-slate-200 bg-white px-6 py-3 md:flex">
    <div class="flex items-center gap-4">
        <p class="text-base font-bold text-slate-800">Bapak/Ibu Guru Pengajar</p>
        <span class="h-4 w-px bg-slate-200"></span>
        <div class="flex items-center gap-2 text-xs font-semibold text-slate-600">
            <i class="bi bi-calendar-event text-emerald-600"></i>
            <span>{{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('l, d F Y') }}</span>
        </div>
    </div>

    <div class="flex items-center gap-3">
        {{-- Live Clock --}}
        <div x-data="{
            timeStr: '{{ \Carbon\Carbon::now('Asia/Jakarta')->format('H:i:s') }}',
            init() {
                setInterval(() => {
                    const d = new Date();
                    this.timeStr = String(d.getHours()).padStart(2, '0') + ':' +
                                   String(d.getMinutes()).padStart(2, '0') + ':' +
                                   String(d.getSeconds()).padStart(2, '0');
                }, 1000);
            }
        }" class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-800">
            <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span x-text="timeStr + ' WIB'" class="font-mono font-bold text-emerald-950"></span>
        </div>

        @php
            $notifGuru = \App\Models\Notifikasi::query()
                ->where('id_user', Auth::id())
                ->where('is_read', false)
                ->latest()
                ->take(10)
                ->get();
            $notifBadgeCount = $notifGuru->count();
        @endphp
        <div x-data="{ openNotif: false }" class="relative">
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
                    @if($notifBadgeCount > 0)
                        <form method="POST" action="{{ route('guru.notifikasi.read-all') }}">
                            @csrf
                            <button type="submit" class="text-xs font-medium text-emerald-600 hover:text-emerald-700">Tandai sudah dibaca</button>
                        </form>
                    @endif
                </div>

                <div class="max-h-96 overflow-y-auto p-2">
                    @forelse($notifGuru as $notification)
                        <div class="mb-2 rounded-xl border border-emerald-100 bg-emerald-50/40 p-3">
                            <div class="flex items-start gap-3">
                                <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                                    <i class="bi bi-bell-fill text-base" aria-hidden="true"></i>
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-bold text-slate-800">{{ $notification->judul }}</p>
                                    <p class="mt-1 text-xs leading-relaxed text-slate-600">{{ $notification->pesan }}</p>
                                    <p class="mt-1 text-[10px] text-slate-400">{{ $notification->created_at?->diffForHumans() }}</p>
                                    <form method="POST" action="{{ route('guru.notifikasi.read', $notification) }}" class="mt-2">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center rounded-md border border-emerald-200 bg-white px-2.5 py-1.5 text-[11px] font-semibold text-emerald-700 transition hover:bg-emerald-50">Lihat</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-xl border border-slate-100 bg-slate-50 p-4 text-center text-xs text-slate-400">Belum ada notifikasi baru.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<div class="flex items-center justify-between border-b border-slate-200 bg-white px-4 py-2.5 md:hidden">
    {{-- Profil Guru (Nama & NIP di bawah nama) --}}
    <div class="flex items-center gap-2.5 min-w-0 flex-1 pr-2">
        @php
            $namaParts = explode(' ', Auth::user()->name);
            $initials = strtoupper(substr($namaParts[0], 0, 1));
            if (count($namaParts) > 1) {
                $initials .= strtoupper(substr($namaParts[1], 0, 1));
            }
        @endphp
        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-800 border border-emerald-200">
            {{ $initials }}
        </span>
        <div class="min-w-0 flex-1">
            <p class="truncate text-xs font-bold text-slate-800 leading-tight">{{ Auth::user()->name }}</p>
            <p class="truncate text-[10px] text-slate-400 font-medium leading-tight mt-0.5">NIP: {{ Auth::user()->nip ?? Auth::user()->username ?? '-' }}</p>
        </div>
    </div>

    {{-- Jam & Tanggal Mobile --}}
    <div x-data="{
        timeStr: '{{ \Carbon\Carbon::now('Asia/Jakarta')->format('H:i:s') }}',
        init() {
            setInterval(() => {
                const d = new Date();
                this.timeStr = String(d.getHours()).padStart(2, '0') + ':' +
                               String(d.getMinutes()).padStart(2, '0') + ':' +
                               String(d.getSeconds()).padStart(2, '0');
            }, 1000);
        }
    }" class="flex flex-col items-end shrink-0 pr-2">
        <span class="inline-flex items-center gap-1 font-mono text-[11px] font-bold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
            <span x-text="timeStr"></span>
        </span>
        <span class="text-[9px] text-slate-400 font-medium mt-0.5">{{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('d M Y') }}</span>
    </div>

    {{-- Bell Notifikasi --}}
    <div x-data="{ openNotif: false }" class="relative shrink-0">
        <button type="button"
                @click="openNotif = !openNotif"
                @click.outside="openNotif = false"
                class="relative flex h-9 w-9 items-center justify-center rounded-full text-slate-500 transition hover:bg-emerald-50 hover:text-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-600"
                aria-label="Notifikasi persetujuan dan revisi logbook"
                aria-expanded="openNotif">
            <i class="bi bi-bell text-lg" aria-hidden="true"></i>
            @if(($notifBadgeCount ?? 0) > 0)
                <span class="absolute right-1 top-1 flex h-3.5 min-w-3.5 items-center justify-center rounded-full border border-white bg-rose-500 px-0.5 text-[8px] font-bold text-white">{{ $notifBadgeCount }}</span>
            @endif
        </button>

        <div x-show="openNotif"
             x-cloak
             class="absolute right-0 mt-3 w-72 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg z-50">
            <div class="flex items-center justify-between border-b border-slate-100 px-3 py-2">
                <h3 class="text-xs font-bold text-slate-800">Notifikasi</h3>
                @if($notifBadgeCount > 0)
                    <form method="POST" action="{{ route('guru.notifikasi.read-all') }}">
                        @csrf
                        <button type="submit" class="text-[10px] font-medium text-emerald-600">Tandai sudah dibaca</button>
                    </form>
                @endif
            </div>

            <div class="max-h-80 overflow-y-auto p-2">
                @forelse($notifGuru as $notification)
                    <div class="mb-2 rounded-lg border border-emerald-100 bg-emerald-50/40 p-2.5">
                        <div class="flex items-start gap-2">
                            <span class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                                <i class="bi bi-bell-fill text-sm" aria-hidden="true"></i>
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-bold text-slate-800">{{ $notification->judul }}</p>
                                <p class="mt-1 text-[11px] leading-relaxed text-slate-600">{{ $notification->pesan }}</p>
                                <form method="POST" action="{{ route('guru.notifikasi.read', $notification) }}" class="mt-2">@csrf <button type="submit" class="text-[11px] font-semibold text-emerald-700">Lihat</button></form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-lg border border-slate-100 bg-slate-50 p-3 text-center text-[11px] text-slate-400">Belum ada notifikasi baru.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<nav class="fixed bottom-0 left-0 right-0 z-50 flex items-center justify-around border-t border-slate-200 bg-white py-2 text-xs md:hidden" aria-label="Navigasi mobile">
    <a href="{{ $homeUrl }}"
       class="flex min-w-16 flex-col items-center gap-1 rounded-lg px-2 py-1.5 font-medium {{ ($isHomeActive || $activePage === 'utama') && !request()->is('dashboard/piket*') && $activePage !== 'piket' ? 'text-emerald-700 font-bold' : 'text-slate-400' }}"
       @if (($isHomeActive || $activePage === 'utama') && !request()->is('dashboard/piket*') && $activePage !== 'piket') aria-current="page" @endif>
        <i class="bi bi-house-door-fill text-lg" aria-hidden="true"></i>
        <span>Beranda</span>
    </a>

    <a href="{{ $historyUrl }}"
       class="flex min-w-16 flex-col items-center gap-1 rounded-lg px-2 py-1.5 font-medium {{ $isHistoryActive || $activePage === 'riwayat' ? 'text-emerald-700 font-bold' : 'text-slate-400' }}"
       @if ($isHistoryActive || $activePage === 'riwayat') aria-current="page" @endif>
        <i class="bi bi-clock-history text-lg" aria-hidden="true"></i>
        <span>Riwayat</span>
    </a>

    <a href="{{ route('dashboard.piket') }}"
       class="relative flex min-w-16 flex-col items-center gap-1 rounded-lg px-2 py-1.5 font-medium {{ request()->is('dashboard/piket*') || request()->is('piket*') || $activePage === 'piket' ? 'text-emerald-700 font-bold' : 'text-slate-400' }}"
       @if (request()->is('dashboard/piket*') || request()->is('piket*') || $activePage === 'piket') aria-current="page" @endif>
        <i class="bi bi-shield-check text-lg" aria-hidden="true"></i>
        <span>{{ ($isPiketActive ?? auth()->user()?->isPiketActive()) ? 'Piket' : 'Anda sedang tidak piket' }}</span>
        @if($isPiketActive ?? auth()->user()?->isPiketActive())
            <span class="absolute top-1 right-2.5 h-2 w-2 rounded-full bg-amber-500 ring-2 ring-white"></span>
        @endif
    </a>

    <form action="{{ route('logout') }}" method="POST" class="m-0 p-0">
        @csrf
        <button type="submit"
                class="flex min-w-16 flex-col items-center gap-1 rounded-lg px-2 py-1.5 font-medium text-slate-400 transition hover:text-rose-500">
            <i class="bi bi-box-arrow-right text-lg" aria-hidden="true"></i>
            <span>Keluar</span>
        </button>
    </form>
</nav>
