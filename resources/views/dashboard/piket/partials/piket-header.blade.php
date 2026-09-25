@php
    $currentPiketPage = $piketPage ?? 'utama';
    $title = $title ?? 'Monitoring Jurnal Real-time';
    $subtitle = $subtitle ?? 'Piket KBM';
    $description = $description ?? 'Pantau kegiatan piket hari ini.';
@endphp

<header class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <div class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-[0.16em] text-emerald-700">
            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
            <span>{{ $subtitle }}</span>
        </div>
        <h1 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">{{ $title }}</h1>
        <p class="mt-1 hidden text-sm text-slate-500 sm:block">{{ $description }}</p>
    </div>

    <div class="hidden items-center gap-2 sm:flex">
        <a href="{{ route('dashboard.piket') }}" class="rounded-xl px-3 py-2 text-xs font-bold {{ $currentPiketPage === 'utama' ? 'bg-emerald-700 text-white' : 'bg-white text-slate-600 ring-1 ring-slate-200' }}">Jurnal</a>
        <a href="{{ route('piket.kehadiran') }}" class="rounded-xl px-3 py-2 text-xs font-bold {{ $currentPiketPage === 'kehadiran' ? 'bg-emerald-700 text-white' : 'bg-white text-slate-600 ring-1 ring-slate-200' }}">Guru</a>
        <a href="{{ route('piket.kehadiran-siswa') }}" class="rounded-xl px-3 py-2 text-xs font-bold {{ $currentPiketPage === 'kehadiran-siswa' ? 'bg-emerald-700 text-white' : 'bg-white text-slate-600 ring-1 ring-slate-200' }}">Siswa</a>
        <a href="{{ route('piket.dispensasi.form') }}" class="rounded-xl px-3 py-2 text-xs font-bold {{ $currentPiketPage === 'dispensasi' ? 'bg-emerald-700 text-white' : 'bg-white text-slate-600 ring-1 ring-slate-200' }}">Dispensasi</a>
    </div>
</header>
