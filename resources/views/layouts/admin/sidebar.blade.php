<style>
  #batchActionBar,
  #batchActionBarKelas,
  #batchActionBarSiswa,
  #batchActionBarMapel,
  #batchActionBarJadwal {
    display: none !important;
  }

  th:has(#selectAllGuru), td:has(.guru-checkbox),
  th:has(#selectAllKelas), td:has(.kelas-checkbox),
  th:has(#selectAllSiswa), td:has(.siswa-checkbox),
  th:has(#selectAllMapel), td:has(.mapel-checkbox),
  th:has(#selectAllJadwal), td:has(.jadwal-checkbox) {
    display: none !important;
  }
</style>

<aside class="sticky top-0 flex h-screen w-64 flex-col border-r border-[#17826E] bg-[#0D6B5A] font-sans">
  <div class="flex items-center gap-3 px-6 pb-8 pt-10">
    <img src="{{ asset('img/logo-rounded.png') }}" alt="Logo JurnalKita" class="h-11 w-11 rounded-xl shadow-sm border border-emerald-400/20 object-cover">
    <div>
      <div class="mb-1 text-[22px] font-bold leading-none text-white">JurnalKita</div>
      <div class="text-[11px] font-medium tracking-wide text-[#8EBEB2]">Management System</div>
    </div>
  </div>

  <nav x-data="{
          openMaster: @js(request()->routeIs('dashboard.guru*', 'dashboard.kelas*', 'dashboard.siswa*', 'dashboard.mapel*')),
          openJadwal: @js(request()->routeIs('dashboard.jadwal', 'dashboard.jadwal.*', 'dashboard.jadwal-penugasan', 'jadwal.create'))
        }"
        class="flex-1 space-y-2 overflow-y-auto px-4 pb-4">
    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-medium transition-colors !no-underline {{ request()->routeIs('dashboard') ? 'bg-[#1BA886] !text-white' : '!text-[#8EBEB2] hover:bg-[#1BA886]/10 hover:!text-white' }}">
      <i class="bi bi-grid text-lg"></i><span>Dashboard</span>
    </a>

    <section>
      <button type="button" @click="openMaster = !openMaster" :aria-expanded="openMaster.toString()"
        class="flex w-full items-center justify-between rounded-lg px-4 py-2.5 text-sm font-medium transition-colors {{ request()->routeIs('dashboard.guru*', 'dashboard.kelas*', 'dashboard.siswa*', 'dashboard.mapel*') ? 'bg-[#1BA886] !text-white' : '!text-[#8EBEB2] hover:bg-[#1BA886]/10 hover:!text-white' }}">
        <span class="flex items-center gap-3"><i class="bi bi-database text-lg"></i><span>Data Master</span></span>
        <i class="bi bi-chevron-down text-xs transition-transform duration-200" :class="{ 'rotate-180': openMaster }"></i>
      </button>
      <div x-cloak x-show="openMaster" x-transition class="mt-1.5 space-y-1.5">
        <a href="{{ route('dashboard.guru') }}" class="flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-medium transition-colors !no-underline {{ request()->routeIs('dashboard.guru*') ? 'bg-[#1BA886] !text-white' : '!text-[#8EBEB2] hover:bg-[#1BA886]/10 hover:!text-white' }}">
          <i class="bi bi-person-workspace text-lg"></i><span>Data Guru</span>
        </a>
        <a href="{{ route('dashboard.kelas') }}" class="flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-medium transition-colors !no-underline {{ request()->routeIs('dashboard.kelas*') ? 'bg-[#1BA886] !text-white' : '!text-[#8EBEB2] hover:bg-[#1BA886]/10 hover:!text-white' }}">
          <i class="bi bi-door-closed text-lg"></i><span>Data Kelas</span>
        </a>
        <a href="{{ route('dashboard.siswa') }}" class="flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-medium transition-colors !no-underline {{ request()->routeIs('dashboard.siswa*') ? 'bg-[#1BA886] !text-white' : '!text-[#8EBEB2] hover:bg-[#1BA886]/10 hover:!text-white' }}">
          <i class="bi bi-mortarboard text-lg"></i><span>Data Siswa</span>
        </a>
        <a href="{{ route('dashboard.mapel') }}" class="flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-medium transition-colors !no-underline {{ request()->routeIs('dashboard.mapel*') ? 'bg-[#1BA886] !text-white' : '!text-[#8EBEB2] hover:bg-[#1BA886]/10 hover:!text-white' }}">
          <i class="bi bi-journal-bookmark text-lg"></i><span>Mata Pelajaran</span>
        </a>
      </div>
    </section>

    <section>
      <button type="button" @click="openJadwal = !openJadwal" :aria-expanded="openJadwal.toString()"
        class="flex w-full items-center justify-between rounded-lg px-4 py-2.5 text-sm font-medium transition-colors {{ request()->routeIs('dashboard.jadwal', 'dashboard.jadwal.*', 'dashboard.jadwal-penugasan', 'jadwal.create') ? 'bg-[#1BA886] !text-white' : '!text-[#8EBEB2] hover:bg-[#1BA886]/10 hover:!text-white' }}">
        <span class="flex items-center gap-3"><i class="bi bi-calendar3 text-lg"></i><span>Jadwal</span></span>
        <i class="bi bi-chevron-down text-xs transition-transform duration-200" :class="{ 'rotate-180': openJadwal }"></i>
      </button>
      <div x-cloak x-show="openJadwal" x-transition class="mt-1.5 space-y-1.5">
        <a href="{{ route('dashboard.jadwal') }}" class="flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-medium transition-colors !no-underline {{ request()->routeIs('dashboard.jadwal', 'dashboard.jadwal.*', 'jadwal.create') ? 'bg-[#1BA886] !text-white' : '!text-[#8EBEB2] hover:bg-[#1BA886]/10 hover:!text-white' }}">
          <i class="bi bi-calendar-week text-lg"></i><span>Jadwal Pelajaran</span>
        </a>
        <a href="{{ route('dashboard.jadwal-penugasan') }}" class="flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-medium transition-colors !no-underline {{ request()->routeIs('dashboard.jadwal-penugasan') ? 'bg-[#1BA886] !text-white' : '!text-[#8EBEB2] hover:bg-[#1BA886]/10 hover:!text-white' }}">
          <i class="bi bi-shield-check text-lg"></i><span>Jadwal Penugasan</span>
        </a>
      </div>
    </section>

    <a href="{{ route('dashboard.rekap-jurnal') }}" class="flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-medium transition-colors !no-underline {{ request()->routeIs('catatan-jurnal', 'dashboard.rekap-jurnal') ? 'bg-[#1BA886] !text-white' : '!text-[#8EBEB2] hover:bg-[#1BA886]/10 hover:!text-white' }}">
      <i class="bi bi-clipboard-data text-lg"></i><span>Rekap Jurnal</span>
    </a>
  </nav>

  <div class="mt-auto flex flex-col gap-3 px-5 pb-6 pt-4">
    <a href="{{ route('admin.manajemen-user') }}" class="flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-medium transition-colors !no-underline {{ request()->routeIs('admin.manajemen-user*') ? 'bg-[#1BA886] !text-white' : '!text-[#8EBEB2] hover:bg-[#1BA886]/10 hover:!text-white' }}">
      <i class="bi bi-people text-lg"></i><span>Manajemen Akun</span>
    </a>

    <div class="h-px w-full bg-[#17826E]"></div>

    @php
      $adminName = Auth::user()?->name ?? 'Administrator';
      $adminInitials = collect(preg_split('/\s+/', trim($adminName)))
        ->filter()
        ->take(2)
        ->map(fn ($word) => strtoupper(mb_substr($word, 0, 1)))
        ->implode('');
    @endphp

    <div class="flex items-center gap-2 rounded-xl border border-[#17826E] bg-[#0A594B] px-3 py-3 shadow-sm">
      <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#1BA886] text-xs font-bold text-white">{{ $adminInitials ?: 'A' }}</div>
      <div class="min-w-0 flex-1">
        <p class="truncate text-sm font-semibold text-white">{{ $adminName }}</p>
        <p class="text-[11px] text-[#8EBEB2]">Administrator</p>
      </div>
      <form action="{{ route('logout') }}" method="POST" class="m-0 shrink-0">
        @csrf
        <button type="submit" class="flex h-9 w-9 items-center justify-center rounded-lg border-0 bg-transparent text-rose-200 transition-colors hover:bg-rose-500/20 hover:text-white" title="Keluar">
          <i class="bi bi-box-arrow-right text-lg"></i>
        </button>
      </form>
    </div>
  </div>
</aside>
