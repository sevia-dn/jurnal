@extends('layouts.app')

@section('title', 'Kehadiran & Izin Guru - Piket JurnalKita')

@section('sidebar')
    @include('layouts.guru-pengajar.sidebar', ['activePage' => 'piket'])
@endsection

@section('navbar')
    @include('layouts.guru-pengajar.navbar', ['activePage' => 'piket'])
@endsection

@section('content')
<div class="min-h-full bg-slate-50 p-4 pb-24 font-sans sm:p-6 lg:p-8">
    <main class="mx-auto max-w-6xl">
        <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <a href="{{ route('dashboard.piket') }}" class="inline-flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-700 transition hover:bg-emerald-100"><i class="bi bi-arrow-left"></i>Kembali</a>
                <h1 class="mt-2 text-lg font-extrabold text-slate-900">Kehadiran & Persetujuan Izin Guru</h1>
                <p class="mt-1 text-xs text-slate-500">Pantau kehadiran, lalu proses pengajuan izin atau sakit guru yang belum sempat dibuka dari notifikasi.</p>
            </div>
            <label class="inline-flex w-fit items-center gap-2 rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-sm font-semibold text-slate-700 shadow-sm"><i class="bi bi-calendar3 text-emerald-600"></i><input type="date" id="attendance-date" value="{{ $tanggal }}" class="bg-transparent text-xs font-bold outline-none"></label>
        </div>

        @if(session('success'))
            <div class="mb-5 flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 p-3.5 text-sm font-medium text-emerald-800"><i class="bi bi-check-circle-fill text-lg"></i>{{ session('success') }}</div>
        @endif

        <section class="mb-5 overflow-hidden rounded-2xl border border-amber-200 bg-white shadow-sm" aria-labelledby="approval-title">
            <header class="flex flex-wrap items-center justify-between gap-3 border-b border-amber-100 bg-amber-50 px-4 py-3.5 sm:px-5">
                <div class="flex items-center gap-2.5"><span class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-100 text-amber-700"><i class="bi bi-person-fill-exclamation"></i></span><div><h2 id="approval-title" class="text-sm font-extrabold text-amber-950">Menunggu Persetujuan Izin Guru</h2><p class="text-[11px] text-amber-800">{{ $pendingAbsenceReports->count() }} pengajuan pada {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}</p></div></div>
                <a href="{{ route('piket.ketidakhadiran-guru.index', ['tanggal' => $tanggal, 'status' => 'all']) }}" class="text-xs font-bold text-amber-800 hover:underline">Lihat seluruh riwayat <i class="bi bi-arrow-right"></i></a>
            </header>
            @forelse($pendingAbsenceReports as $absence)
                <article class="flex flex-col gap-3 border-b border-slate-100 px-4 py-4 last:border-0 sm:flex-row sm:items-center sm:justify-between sm:px-5">
                    <div class="min-w-0"><div class="flex flex-wrap items-center gap-2"><p class="font-bold text-slate-800">{{ $absence->guru?->name ?? 'Guru' }}</p><span class="rounded-full px-2 py-0.5 text-[10px] font-extrabold {{ $absence->alasan === 'sakit' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-800' }}">{{ $absence->label_alasan }}</span></div><p class="mt-1 line-clamp-1 text-xs text-slate-500">{{ $absence->keterangan ?: 'Tidak ada keterangan tambahan.' }}</p></div>
                    <div class="flex flex-wrap items-center gap-2"><a href="{{ route('piket.ketidakhadiran-guru.show', $absence) }}" class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-bold text-slate-600 hover:bg-slate-50"><i class="bi bi-eye mr-1"></i>Periksa</a><form method="POST" action="{{ route('piket.ketidakhadiran-guru.approve', $absence) }}">@csrf<button type="submit" class="rounded-lg bg-emerald-600 px-3 py-2 text-xs font-extrabold text-white hover:bg-emerald-700"><i class="bi bi-check-lg mr-1"></i>Setujui</button></form></div>
                </article>
            @empty
                <div class="px-5 py-6 text-center text-sm text-slate-500"><i class="bi bi-check2-circle mr-1 text-emerald-600"></i>Tidak ada pengajuan izin atau sakit yang menunggu.</div>
            @endforelse
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm" aria-labelledby="attendance-title">
            <header class="border-b border-slate-100 p-4 sm:p-5"><div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"><div><h2 id="attendance-title" class="font-extrabold text-slate-800">Pemantauan Kehadiran Guru</h2><p id="teacher-table-summary" class="mt-1 text-xs text-slate-500">{{ $totalGuru }} guru terdaftar.</p></div><div class="flex w-full gap-2 sm:w-auto"><div class="relative flex-1 sm:w-64"><i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i><input type="search" id="teacher-search" placeholder="Cari nama atau NIP..." class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-9 pr-3 text-xs outline-none focus:border-emerald-500"></div><select id="status-dropdown-filter" class="rounded-xl border border-slate-300 bg-white px-3 text-xs font-bold text-slate-700"><option value="all">Semua</option><option value="Hadir">Hadir</option><option value="Belum Hadir">Belum</option><option value="Izin">Izin</option><option value="Sakit">Sakit</option></select></div></div></header>

            <div id="teacher-empty" class="hidden px-6 py-12 text-center text-sm text-slate-400"><i class="bi bi-person-x block text-2xl"></i><p class="mt-1">Tidak ada guru yang sesuai.</p></div>

            <div class="hidden overflow-x-auto md:block"><table class="w-full min-w-[680px] text-left text-xs"><thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500"><tr><th class="px-5 py-3">Nama Guru</th><th class="px-4 py-3">NIP</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Keterangan</th></tr></thead><tbody class="divide-y divide-slate-100">@foreach($teachersData as $teacher)@php($badgeClass = match($teacher['status']) { 'Hadir' => 'bg-emerald-100 text-emerald-800 border-emerald-200', 'Izin' => 'bg-amber-100 text-amber-800 border-amber-200', 'Sakit' => 'bg-rose-100 text-rose-800 border-rose-200', default => 'bg-slate-100 text-slate-600 border-slate-200' })<tr data-teacher-entry data-name="{{ strtolower($teacher['name'].' '.$teacher['nip']) }}" data-status="{{ $teacher['status'] }}" class="hover:bg-emerald-50/40"><td class="px-5 py-3.5 font-bold text-slate-800">{{ $teacher['name'] }}</td><td class="px-4 py-3.5 font-mono text-slate-500">{{ $teacher['nip'] }}</td><td class="px-4 py-3.5"><span class="inline-flex rounded-full border px-2.5 py-0.5 text-[11px] font-bold {{ $badgeClass }}">{{ $teacher['status'] }}</span></td><td class="px-4 py-3.5 text-slate-600"><p class="font-medium">{{ $teacher['checkIn'] }}</p>@if($teacher['keterangan'])<p class="mt-0.5 text-[11px] italic text-slate-400">{{ $teacher['keterangan'] }}</p>@endif</td></tr>@endforeach</tbody></table></div>

            <div class="divide-y divide-slate-100 md:hidden">@foreach($teachersData as $teacher)@php($badgeClass = match($teacher['status']) { 'Hadir' => 'bg-emerald-100 text-emerald-800 border-emerald-200', 'Izin' => 'bg-amber-100 text-amber-800 border-amber-200', 'Sakit' => 'bg-rose-100 text-rose-800 border-rose-200', default => 'bg-slate-100 text-slate-600 border-slate-200' })<article data-teacher-entry data-name="{{ strtolower($teacher['name'].' '.$teacher['nip']) }}" data-status="{{ $teacher['status'] }}" class="p-4"><div class="flex items-start justify-between gap-3"><div class="min-w-0"><p class="truncate font-bold text-slate-800">{{ $teacher['name'] }}</p><p class="mt-0.5 text-[11px] font-mono text-slate-400">NIP: {{ $teacher['nip'] }}</p></div><span class="inline-flex rounded-full border px-2.5 py-0.5 text-[11px] font-bold {{ $badgeClass }}">{{ $teacher['status'] }}</span></div><div class="mt-3 rounded-lg bg-slate-50 p-2.5"><p class="text-xs font-semibold text-slate-700">{{ $teacher['checkIn'] }}</p>@if($teacher['keterangan'])<p class="mt-1 text-xs leading-relaxed text-slate-500">{{ $teacher['keterangan'] }}</p>@endif</div></article>@endforeach</div>
        </section>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const entries = [...document.querySelectorAll('[data-teacher-entry]')];
    const search = document.getElementById('teacher-search');
    const status = document.getElementById('status-dropdown-filter');
    const empty = document.getElementById('teacher-empty');
    const summary = document.getElementById('teacher-table-summary');
    const apply = () => { const query = search.value.toLowerCase().trim(); let visible = 0; entries.forEach((entry) => { const show = (!query || entry.dataset.name.includes(query)) && (status.value === 'all' || entry.dataset.status === status.value); entry.classList.toggle('hidden', !show); if (show) visible++; }); empty.classList.toggle('hidden', visible > 0); summary.textContent = `Menampilkan ${visible / 2} dari {{ $totalGuru }} guru.`; };
    search.addEventListener('input', apply); status.addEventListener('change', apply);
    document.getElementById('attendance-date').addEventListener('change', (event) => { window.location.href = `{{ route('piket.kehadiran') }}?tanggal=${event.target.value}`; });
    apply();
});
</script>
@endsection
