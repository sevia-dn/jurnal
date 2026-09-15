@php
    $activePage = $activePage ?? 'utama';
    $homeUrl = route('guru.utama');
    $historyUrl = route('guru.riwayat');
@endphp

<!-- PERUBAHAN: Menambahkan z-50 di sini agar sidebar selalu di atas efek blur -->
<aside class="sticky top-0 z-50 flex h-screen w-64 flex-col border-r border-[#17826E] bg-[#0D6B5A] font-sans">
    <div class="flex items-center gap-3 px-6 pb-8 pt-10">
        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#1BA886]/30 text-xl text-[#B9F1E1]">
            <i class="bi bi-mortarboard-fill" aria-hidden="true"></i>
        </div>
        <div>
            <div class="text-[22px] font-bold leading-none text-white">JurnalKita</div>
            <div class="mt-1 text-[11px] font-medium tracking-wide text-[#AEE5D4]">Guru Pengajar</div>
        </div>
    </div>

    <nav class="flex-1 space-y-1.5 overflow-y-auto px-4">
        <a href="{{ $homeUrl }}"
           class="flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-medium transition-colors !no-underline {{ request()->routeIs('guru.utama') || $activePage === 'utama' ? 'bg-[#1BA886] !text-white shadow-sm' : '!text-[#D9F7EE] hover:bg-[#1BA886]/10 hover:!text-white' }}">
            <i class="bi bi-grid text-lg" aria-hidden="true"></i>
            <span>Halaman Utama</span>
        </a>

        <a href="{{ $historyUrl }}"
           class="flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-medium transition-colors !no-underline {{ request()->routeIs('guru.riwayat') || $activePage === 'riwayat' ? 'bg-[#1BA886] !text-white shadow-sm' : '!text-[#D9F7EE] hover:bg-[#1BA886]/10 hover:!text-white' }}">
            <i class="bi bi-clock-history text-lg" aria-hidden="true"></i>
            <span>Riwayat &amp; Rekap</span>
        </a>
    </nav>

    <div class="mt-auto border-t border-[#17826E] px-4 pb-6 pt-4">
        <div class="flex items-center justify-between gap-3">
            <div class="flex min-w-0 items-center gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#DFFAF3] text-xs font-bold text-[#0D6B5A]">
                    AF
                </span>
                <div class="min-w-0">
                    <p class="truncate text-sm font-semibold text-white">Ahmad Fauzi, S.Pd</p>
                    <p class="truncate text-[11px] text-[#AEE5D4]">Guru Pengajar</p>
                </div>
            </div>

            <form action="{{ route('logout') }}" method="POST" class="m-0 p-0">
                @csrf
                <button type="submit"
                        class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-[#9BE0CF] bg-transparent px-2.5 py-2 text-[11px] font-medium text-[#F2FFFB] transition hover:border-[#D8F9EF] hover:bg-white/5">
                    <i class="bi bi-box-arrow-right text-sm" aria-hidden="true"></i>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </div>
</aside>