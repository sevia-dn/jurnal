@extends('layouts.app')

@section('title', 'Rekap Kehadiran Siswa')

@section('sidebar')
    @include('layouts.piket.sidebar')
@endsection

@section('navbar')
    @include('layouts.piket.navbar')
@endsection

@section('content')
<div id="student-attendance-page" class="min-h-full bg-slate-50 p-5 pb-24 font-sans sm:p-8 lg:p-10">
    <div class="mx-auto max-w-7xl">
        
        <!-- Flash Message -->
        @if(session('success'))
            <div class="mb-6 flex items-center justify-between rounded-xl bg-emerald-50 p-4 border border-emerald-200 text-emerald-800 text-sm font-semibold">
                <div class="flex items-center gap-2">
                    <i class="bi bi-check-circle-fill text-lg text-emerald-600"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700"><i class="bi bi-x-lg"></i></button>
            </div>
        @endif

        <!-- Header & Top Filter -->
        <header class="mb-7 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <div class="mb-2 flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.18em] text-emerald-700">
                    <span class="h-2 w-2 rounded-full bg-emerald-500 shadow-[0_0_0_4px_rgba(16,185,129,0.12)]"></span>
                    Monitoring Presensi Siswa Real-time
                </div>
                <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Rekap Kehadiran Siswa</h1>
                <p class="mt-1 text-sm text-slate-500">Presensi harian siswa per kelas terintegrasi otomatis dengan surat dispensasi.</p>
            </div>

            <!-- Action Controls -->
            <div class="flex flex-wrap items-center gap-3">
                <!-- Dropdown Pilih Kelas -->
                <div class="relative min-w-[160px]">
                    <select id="class-select" class="w-full appearance-none rounded-xl border border-slate-300 bg-white px-4 py-2.5 pr-10 text-sm font-bold text-slate-700 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-100 cursor-pointer">
                        @foreach($kelasList as $kelas)
                            <option value="{{ $kelas->id_kelas }}" {{ $selectedKelasId == $kelas->id_kelas ? 'selected' : '' }}>
                                {{ $kelas->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                    <i class="bi bi-chevron-down absolute right-3 top-3 text-slate-400 pointer-events-none"></i>
                </div>

                <!-- Date Picker -->
                <div class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-sm font-semibold text-slate-700 shadow-sm">
                    <i class="bi bi-calendar3 text-emerald-600"></i>
                    <input type="date" id="student-attendance-date" value="{{ $tanggal }}" class="bg-transparent font-medium focus:outline-none cursor-pointer">
                </div>

                <!-- Export Data -->
                <button type="button" id="export-student-data" class="inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-800 focus:outline-none focus:ring-4 focus:ring-emerald-200 cursor-pointer">
                    <i class="bi bi-download"></i>
                    <span>Export CSV</span>
                </button>
            </div>
        </header>

        <!-- Summary Cards -->
        <section class="mb-7 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
            <!-- Card Total -->
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Siswa</p>
                <p id="stat-total" class="mt-2 text-2xl font-extrabold text-slate-900">{{ $totalSiswa }}</p>
                <p class="mt-2 text-xs font-semibold text-slate-500">{{ $selectedKelas?->nama_kelas ?? 'Kelas' }}</p>
            </div>
            <!-- Card Hadir -->
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50/50 p-4 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-wider text-emerald-700">Hadir</p>
                <p id="stat-hadir" class="mt-2 text-2xl font-extrabold text-emerald-800">{{ $totalHadir }}</p>
                <p class="mt-2 text-xs font-semibold text-emerald-600">{{ $totalSiswa > 0 ? round(($totalHadir / $totalSiswa) * 100) : 0 }}% hadir</p>
            </div>
            <!-- Card Sakit -->
            <div class="rounded-2xl border border-amber-200 bg-amber-50/50 p-4 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-wider text-amber-700">Sakit</p>
                <p id="stat-sakit" class="mt-2 text-2xl font-extrabold text-amber-800">{{ $totalSakit }}</p>
                <p class="mt-2 text-xs font-semibold text-amber-600">Siswa sakit</p>
            </div>
            <!-- Card Izin -->
            <div class="rounded-2xl border border-orange-200 bg-orange-50/50 p-4 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-wider text-orange-700">Izin</p>
                <p id="stat-izin" class="mt-2 text-2xl font-extrabold text-orange-800">{{ $totalIzin }}</p>
                <p class="mt-2 text-xs font-semibold text-orange-600">Izin keluarga</p>
            </div>
            <!-- Card Dispensasi (Terintegrasi) -->
            <div class="rounded-2xl border border-purple-200 bg-purple-50/50 p-4 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-wider text-purple-700">Dispensasi</p>
                <p id="stat-dispen" class="mt-2 text-2xl font-extrabold text-purple-800">{{ $totalDispen }}</p>
                <p class="mt-2 text-xs font-semibold text-purple-600">Tugas / Lomba</p>
            </div>
            <!-- Card Alfa -->
            <div class="rounded-2xl border border-rose-200 bg-rose-50/50 p-4 shadow-sm col-span-2 sm:col-span-1">
                <p class="text-[11px] font-bold uppercase tracking-wider text-rose-700">Alfa</p>
                <p id="stat-alfa" class="mt-2 text-2xl font-extrabold text-rose-800">{{ $totalAlfa }}</p>
                <p class="mt-2 text-xs font-semibold text-rose-600">Tanpa keterangan</p>
            </div>
        </section>

        <!-- Search & Filter Controls -->
        <section class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="relative flex-1 max-w-md">
                <i class="bi bi-search absolute left-4 top-3.5 text-slate-400"></i>
                <input type="text" id="search-input" placeholder="Cari nama atau NIS siswa..." class="w-full rounded-xl border border-slate-300 bg-white py-2.5 pl-11 pr-4 text-sm font-medium text-slate-800 shadow-sm placeholder:text-slate-400 focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-100">
            </div>

            <div class="flex items-center gap-2">
                <select id="status-filter" class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-100">
                    <option value="all">Semua Status</option>
                    <option value="Hadir">Hadir</option>
                    <option value="Sakit">Sakit</option>
                    <option value="Izin">Izin</option>
                    <option value="Dispensasi">Dispensasi</option>
                    <option value="Alfa">Alfa</option>
                </select>
            </div>
        </section>

        <!-- Main Table Container -->
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-6 py-4 flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-slate-800">Daftar Presensi Siswa</h2>
                    <p class="text-xs text-slate-500">Kelas <span id="current-class-label" class="font-bold text-emerald-700">{{ $selectedKelas?->nama_kelas }}</span> • {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[700px] text-left">
                    <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100">
                        <tr>
                            <th scope="col" class="px-6 py-4">NIS</th>
                            <th scope="col" class="px-6 py-4">Nama Siswa</th>
                            <th scope="col" class="px-6 py-4">L/P</th>
                            <th scope="col" class="px-6 py-4">Status Kehadiran</th>
                            <th scope="col" class="px-6 py-4">Keterangan</th>
                            <th scope="col" class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="student-table-body" class="divide-y divide-slate-100 text-sm">
                        <!-- Content rendered via JS from real backend data -->
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <!-- MODAL UBAH STATUS PRESENSI SISWA -->
    <div id="student-modal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-slate-950/60 p-4 opacity-0 backdrop-blur-sm transition-opacity duration-200" role="dialog" aria-modal="true">
        <div id="modal-panel" class="w-full max-w-md translate-y-4 rounded-2xl bg-white p-6 shadow-2xl transition duration-200 sm:translate-y-0">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <h3 class="text-lg font-bold text-slate-900">Ubah Status Presensi Siswa</h3>
                <button type="button" id="close-modal" class="text-slate-400 hover:text-slate-600 cursor-pointer"><i class="bi bi-x-lg"></i></button>
            </div>

            <form method="POST" action="{{ route('piket.kehadiran-siswa.update') }}" class="mt-4 space-y-4">
                @csrf
                <input type="hidden" name="siswa_id" id="modal-student-id">
                <input type="hidden" name="kelas_id" value="{{ $selectedKelasId }}">
                <input type="hidden" name="tanggal" value="{{ $tanggal }}">

                <div>
                    <p class="text-xs font-semibold text-slate-400">Nama Siswa</p>
                    <p id="modal-student-name" class="font-bold text-slate-800 text-base"></p>
                    <p id="modal-student-nis" class="text-xs text-slate-500"></p>
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2 block">Pilih Status Kehadiran</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="flex items-center gap-2 rounded-xl border border-slate-200 p-2.5 cursor-pointer hover:bg-emerald-50">
                            <input type="radio" name="status" value="Hadir" class="accent-emerald-600">
                            <span class="text-xs font-bold text-slate-700">Hadir</span>
                        </label>
                        <label class="flex items-center gap-2 rounded-xl border border-slate-200 p-2.5 cursor-pointer hover:bg-amber-50">
                            <input type="radio" name="status" value="Sakit" class="accent-amber-600">
                            <span class="text-xs font-bold text-slate-700">Sakit</span>
                        </label>
                        <label class="flex items-center gap-2 rounded-xl border border-slate-200 p-2.5 cursor-pointer hover:bg-orange-50">
                            <input type="radio" name="status" value="Izin" class="accent-orange-600">
                            <span class="text-xs font-bold text-slate-700">Izin</span>
                        </label>
                        <label class="flex items-center gap-2 rounded-xl border border-slate-200 p-2.5 cursor-pointer hover:bg-rose-50">
                            <input type="radio" name="status" value="Alfa" class="accent-rose-600">
                            <span class="text-xs font-bold text-slate-700">Alfa</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-1 block">Catatan / Alasan</label>
                    <textarea name="catatan" id="modal-note" rows="3" placeholder="Tambahkan catatan izin atau surat dokter..." class="w-full rounded-xl border border-slate-300 p-3 text-xs font-medium text-slate-800 focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-100"></textarea>
                </div>

                <div class="mt-6 flex justify-end gap-2 border-t border-slate-100 pt-4">
                    <button type="button" id="cancel-modal" class="rounded-xl px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 cursor-pointer">Batal</button>
                    <button type="submit" class="rounded-xl bg-emerald-700 px-5 py-2 text-xs font-bold text-white shadow transition hover:bg-emerald-800 cursor-pointer">Simpan Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Data Riil Siswa dari Database Controller
    const studentsData = {!! json_encode($studentsData) !!};

    const tableBody = document.getElementById('student-table-body');
    const classSelect = document.getElementById('class-select');
    const dateInput = document.getElementById('student-attendance-date');
    const searchInput = document.getElementById('search-input');
    const statusFilter = document.getElementById('status-filter');
    const modal = document.getElementById('student-modal');
    const modalPanel = document.getElementById('modal-panel');

    function getBadgeClass(status) {
        switch(status) {
            case 'Hadir': return 'bg-emerald-50 text-emerald-700 ring-emerald-200';
            case 'Sakit': return 'bg-amber-50 text-amber-700 ring-amber-200';
            case 'Izin': return 'bg-orange-50 text-orange-700 ring-orange-200';
            case 'Dispensasi': return 'bg-purple-50 text-purple-700 ring-purple-200 font-bold';
            case 'Alfa':
            case 'Alpa': return 'bg-rose-50 text-rose-700 ring-rose-200';
            default: return 'bg-slate-50 text-slate-700 ring-slate-200';
        }
    }

    function renderTable() {
        const searchQuery = searchInput.value.toLowerCase().trim();
        const selectedStatus = statusFilter.value;

        const filtered = studentsData.filter(s => {
            const matchSearch = s.name.toLowerCase().includes(searchQuery) || s.nis.includes(searchQuery);
            const matchStatus = selectedStatus === 'all' || s.status === selectedStatus;
            return matchSearch && matchStatus;
        });

        if (filtered.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="6" class="px-6 py-12 text-center text-slate-400 font-medium">Tidak ada siswa yang sesuai filter.</td></tr>`;
            return;
        }

        tableBody.innerHTML = filtered.map((s, index) => `
            <tr class="${index % 2 ? 'bg-slate-50/60' : 'bg-white'} transition hover:bg-emerald-50/40">
                <td class="whitespace-nowrap px-6 py-4 font-mono text-xs text-slate-500 font-bold">${s.nis}</td>
                <td class="px-6 py-4 font-bold text-slate-800">${s.name}</td>
                <td class="px-6 py-4 font-semibold text-slate-500">${s.gender}</td>
                <td class="px-6 py-4">
                    <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold ring-1 ${getBadgeClass(s.status)}">
                        ${s.status === 'Dispensasi' ? '<i class="bi bi-ticket-perforated"></i> ' : ''}${s.status}
                    </span>
                </td>
                <td class="px-6 py-4 text-xs text-slate-500 max-w-xs truncate" title="${s.note}">${s.note}</td>
                <td class="px-6 py-4 text-center">
                    ${s.is_dispen ? `
                        <span class="text-[11px] font-semibold text-purple-700 bg-purple-50 border border-purple-200 rounded-lg px-2 py-1 inline-flex items-center gap-1" title="Dispensasi Disahkan Waka">
                            <i class="bi bi-shield-check"></i> Surat Waka
                        </span>
                    ` : `
                        <button type="button" onclick="openEditModal(${s.id})" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-bold text-slate-700 transition hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-200 cursor-pointer">
                            Ubah Status
                        </button>
                    `}
                </td>
            </tr>
        `).join('');
    }

    window.openEditModal = function(id) {
        const student = studentsData.find(s => s.id === id);
        if (!student) return;

        document.getElementById('modal-student-id').value = student.id;
        document.getElementById('modal-student-name').textContent = student.name;
        document.getElementById('modal-student-nis').textContent = `NIS: ${student.nis} • Kelas: ${student.class}`;
        document.getElementById('modal-note').value = student.note === '-' ? '' : student.note;
        
        const radios = document.getElementsByName('status');
        radios.forEach(r => { r.checked = (r.value === student.status); });

        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modalPanel.classList.remove('translate-y-4');
        }, 10);
    };

    function closeModal() {
        modal.classList.add('opacity-0');
        modalPanel.classList.add('translate-y-4');
        setTimeout(() => modal.classList.add('hidden'), 200);
    }

    document.getElementById('close-modal').addEventListener('click', closeModal);
    document.getElementById('cancel-modal').addEventListener('click', closeModal);

    // Ganti Kelas & Tanggal -> reload URL query parameter
    classSelect.addEventListener('change', function() {
        window.location.href = `{{ route('piket.kehadiran-siswa') }}?kelas_id=${this.value}&tanggal=${dateInput.value}`;
    });

    dateInput.addEventListener('change', function() {
        window.location.href = `{{ route('piket.kehadiran-siswa') }}?kelas_id=${classSelect.value}&tanggal=${this.value}`;
    });

    searchInput.addEventListener('input', renderTable);
    statusFilter.addEventListener('change', renderTable);

    // Export CSV Siswa
    document.getElementById('export-student-data').addEventListener('click', () => {
        const query = searchInput.value.toLowerCase().trim();
        const selectedStatus = statusFilter.value;
        const filtered = studentsData.filter(s => {
            const matchSearch = s.name.toLowerCase().includes(query) || s.nis.includes(query);
            const matchStatus = selectedStatus === 'all' || s.status === selectedStatus;
            return matchSearch && matchStatus;
        });

        if (filtered.length === 0) {
            alert('Tidak ada data presensi siswa untuk diekspor.');
            return;
        }

        const className = classSelect.options[classSelect.selectedIndex]?.text || 'Kelas';
        const dateVal = dateInput.value || 'Hari-Ini';

        const header = ['NIS', 'Nama Siswa', 'L/P', 'Kelas', 'Status Kehadiran', 'Keterangan', 'Tanggal'];
        const rows = filtered.map(s => [s.nis, s.name, s.gender, s.class, s.status, s.note, dateVal]);

        const csvContent = [header, ...rows]
            .map(row => row.map(cell => `"${String(cell).replace(/"/g, '""')}"`).join(','))
            .join('\n');

        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = `presensi-siswa-${className.replace(/\s+/g, '_')}-${dateVal}.csv`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    });

    renderTable();
});
</script>
@endsection