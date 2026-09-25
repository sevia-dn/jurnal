@extends('layouts.app')

@section('title', 'Daftar Kehadiran Guru - Piket JurnalKita')

@section('sidebar')
    @include('layouts.guru-pengajar.sidebar', ['activePage' => 'piket'])
@endsection

@section('navbar')
    @include('layouts.guru-pengajar.navbar', ['activePage' => 'piket'])
@endsection

@section('content')
<div class="min-h-full bg-slate-50 p-4 pb-24 font-sans sm:p-6 lg:p-8">
    <div class="mx-auto max-w-5xl">

        {{-- BACK BUTTON & HEADER --}}
        <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <a href="{{ route('dashboard.piket') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-700 transition hover:text-emerald-800">
                    <i class="bi bi-arrow-left"></i>Kembali ke halaman utama piket
                </a>
                <h1 class="mt-2 text-2xl font-extrabold text-slate-900">Daftar Kehadiran Guru</h1>
                <p class="mt-0.5 text-xs text-slate-500">Daftar guru yang hadir atau tidak hadir pada hari ini berdasarkan jurnal dan laporan piket.</p>
            </div>

            {{-- DATE PICKER --}}
            <div class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-sm font-semibold text-slate-700 shadow-sm w-fit">
                <i class="bi bi-calendar3 text-emerald-600"></i>
                <input type="date" id="attendance-date" value="{{ $tanggal }}" class="bg-transparent text-xs font-bold text-slate-700 outline-none cursor-pointer">
            </div>
        </div>

        {{-- NOTIFIKASI SUCCESS --}}
        @if(session('success'))
            <div class="mb-5 flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 p-3.5 text-sm font-medium text-emerald-800">
                <i class="bi bi-check-circle-fill text-emerald-600 text-lg"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- TABEL & FILTER KEHADIRAN GURU --}}
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            {{-- SEARCH BAR & FILTER STATUS KEHADIRAN --}}
            <div class="border-b border-slate-100 p-4 sm:p-5">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    {{-- SEARCH BAR --}}
                    <div class="relative flex-1">
                        <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                        <input
                            type="search"
                            id="teacher-search"
                            placeholder="Cari nama atau NIP guru..."
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-9 pr-8 text-xs text-slate-700 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100"
                        >
                        <button type="button" id="teacher-search-clear" class="absolute right-2.5 top-1/2 -translate-y-1/2 hidden text-slate-400 hover:text-slate-600">
                            <i class="bi bi-x-circle-fill text-xs"></i>
                        </button>
                    </div>

                    {{-- FILTER STATUS KEHADIRAN GURU --}}
                    <div class="flex items-center gap-2">
                        <select id="status-dropdown-filter" class="w-full sm:w-auto rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-xs font-bold text-slate-700 outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 cursor-pointer">
                            <option value="all">Semua Status Kehadiran</option>
                            <option value="Hadir">Hadir</option>
                            <option value="Belum Hadir">Belum Hadir</option>
                            <option value="Izin">Izin</option>
                            <option value="Sakit">Sakit</option>
                        </select>
                    </div>
                </div>

                <div class="mt-3 flex items-center justify-between text-xs text-slate-500">
                    <p id="teacher-table-summary">Memuat data guru...</p>
                    <button type="button" id="reset-teacher-filter" class="hidden text-xs font-semibold text-emerald-700 hover:underline">
                        Reset filter
                    </button>
                </div>
            </div>

            {{-- TABEL DAFTAR GURU --}}
            <div class="overflow-x-auto max-h-[38rem] overflow-y-auto">
                <table class="w-full min-w-[600px] text-left">
                    <thead class="sticky top-0 z-10 border-b border-slate-100 bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th scope="col" class="px-5 py-3">Nama Guru</th>
                            <th scope="col" class="px-4 py-3">NIP</th>
                            <th scope="col" class="px-4 py-3">Status Kehadiran</th>
                            <th scope="col" class="px-4 py-3">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody id="teacher-table-body" class="divide-y divide-slate-100 text-xs">
                        {{-- Rendered dynamically via JavaScript --}}
                    </tbody>
                </table>
            </div>

            <div id="teacher-empty" class="hidden px-6 py-12 text-center text-xs text-slate-400">
                <i class="bi bi-person-x text-2xl text-slate-300 block mb-1"></i>
                Tidak ada guru yang sesuai dengan pencarian atau filter status.
            </div>
        </section>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const teachers = {!! json_encode($teachersData) !!};
    const tbody = document.getElementById('teacher-table-body');
    const searchInput = document.getElementById('teacher-search');
    const searchClear = document.getElementById('teacher-search-clear');
    const statusSelect = document.getElementById('status-dropdown-filter');
    const emptyState = document.getElementById('teacher-empty');
    const summaryText = document.getElementById('teacher-table-summary');
    const resetBtn = document.getElementById('reset-teacher-filter');
    const dateInput = document.getElementById('attendance-date');

    let activeFilter = 'all';

    function badge(status) {
        switch(status) {
            case 'Hadir':
                return 'bg-emerald-100 text-emerald-800 border-emerald-200';
            case 'Izin':
                return 'bg-amber-100 text-amber-800 border-amber-200';
            case 'Sakit':
                return 'bg-rose-100 text-rose-800 border-rose-200';
            default:
                return 'bg-slate-100 text-slate-600 border-slate-200';
        }
    }

    function render() {
        const q = searchInput.value.toLowerCase().trim();
        const filtered = teachers.filter(t => {
            const matchStatus = activeFilter === 'all' || t.status === activeFilter;
            const matchQuery = !q || t.name.toLowerCase().includes(q) || String(t.nip).toLowerCase().includes(q);
            return matchStatus && matchQuery;
        });

        searchClear.classList.toggle('hidden', !q);
        resetBtn.classList.toggle('hidden', activeFilter === 'all' && !q);
        emptyState.classList.toggle('hidden', filtered.length > 0);
        summaryText.textContent = `Menampilkan ${filtered.length} dari ${teachers.length} guru.`;

        tbody.innerHTML = filtered.map((t, idx) => `
            <tr class="${idx % 2 ? 'bg-slate-50/50' : 'bg-white'} hover:bg-emerald-50/40 transition">
                <td class="px-5 py-3.5">
                    <p class="font-bold text-slate-800">${t.name}</p>
                </td>
                <td class="px-4 py-3.5 font-mono text-slate-500">${t.nip}</td>
                <td class="px-4 py-3.5">
                    <span class="inline-flex rounded-full border px-2.5 py-0.5 text-[11px] font-bold ${badge(t.status)}">
                        ${t.status}
                    </span>
                </td>
                <td class="px-4 py-3.5 text-slate-600">
                    <p class="font-medium">${t.checkIn}</p>
                    ${t.keterangan && t.keterangan !== '-' ? `<p class="mt-0.5 text-[11px] text-slate-400 italic">${t.keterangan}</p>` : ''}
                </td>
            </tr>
        `).join('');
    }

    statusSelect.addEventListener('change', () => {
        activeFilter = statusSelect.value;
        render();
    });

    searchInput.addEventListener('input', render);
    searchClear.addEventListener('click', () => {
        searchInput.value = '';
        render();
        searchInput.focus();
    });

    resetBtn.addEventListener('click', () => {
        activeFilter = 'all';
        statusSelect.value = 'all';
        searchInput.value = '';
        render();
    });

    dateInput.addEventListener('change', () => {
        window.location.href = `{{ route('piket.kehadiran') }}?tanggal=${dateInput.value}`;
    });

    render();
});
</script>
@endsection
