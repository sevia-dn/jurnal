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
        
        <!-- Header & Top Filter -->
        <header class="mb-7 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <div class="mb-2 flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.18em] text-emerald-700">
                    <span class="h-2 w-2 rounded-full bg-emerald-500 shadow-[0_0_0_4px_rgba(16,185,129,0.12)]"></span>
                    Monitoring Presensi Siswa
                </div>
                <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Rekap Kehadiran Siswa</h1>
                <p class="mt-1 text-sm text-slate-500">Lihat dan kelola ringkasan presensi harian siswa tiap kelas.</p>
            </div>

            <!-- Action Controls -->
            <div class="flex flex-wrap items-center gap-3">
                <!-- Dropdown Pilih Kelas -->
                <div class="relative min-w-[160px]">
                    <select id="class-select" class="w-full appearance-none rounded-xl border border-slate-200 bg-white px-4 py-2.5 pr-10 text-sm font-bold text-slate-700 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-100">
                        <option value="XI RPL 1">XI RPL 1</option>
                        <option value="XI RPL 2">XI RPL 2</option>
                        <option value="X RPL 1">X RPL 1</option>
                        <option value="XII RPL 1">XII RPL 1</option>
                    </select>
                    <i class="bi bi-chevron-down absolute right-3 top.1/2 top-3 text-slate-400 pointer-events-none"></i>
                </div>

                <!-- Date Picker -->
                <div class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-sm font-semibold text-slate-700 shadow-sm">
                    <i class="bi bi-calendar3 text-emerald-600"></i>
                    <input type="date" value="{{ date('Y-m-d') }}" class="bg-transparent font-medium focus:outline-none">
                </div>

                <!-- Export Data -->
                <button type="button" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm shadow-emerald-600/20 transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-100">
                    <i class="bi bi-download"></i>
                    <span>Export Data</span>
                </button>
            </div>
        </header>

        <!-- Summary Cards -->
        <section class="mb-7 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
            <!-- Card Total -->
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Siswa</p>
                <p id="stat-total" class="mt-2 text-2xl font-extrabold text-slate-900">36</p>
                <p class="mt-2 text-xs font-semibold text-slate-500">Siswa terdaftar</p>
            </div>
            <!-- Card Hadir -->
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50/50 p-4 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-wider text-emerald-700">Hadir</p>
                <p id="stat-hadir" class="mt-2 text-2xl font-extrabold text-emerald-800">32</p>
                <p class="mt-2 text-xs font-semibold text-emerald-600">88.8% kehadiran</p>
            </div>
            <!-- Card Sakit -->
            <div class="rounded-2xl border border-amber-200 bg-amber-50/50 p-4 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-wider text-amber-700">Sakit</p>
                <p id="stat-sakit" class="mt-2 text-2xl font-extrabold text-amber-800">2</p>
                <p class="mt-2 text-xs font-semibold text-amber-600">Surat terlampir</p>
            </div>
            <!-- Card Izin -->
            <div class="rounded-2xl border border-orange-200 bg-orange-50/50 p-4 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-wider text-orange-700">Izin</p>
                <p id="stat-izin" class="mt-2 text-2xl font-extrabold text-orange-800">1</p>
                <p class="mt-2 text-xs font-semibold text-orange-600">Keperluan keluarga</p>
            </div>
            <!-- Card Alfa -->
            <div class="rounded-2xl border border-red-200 bg-red-50/50 p-4 shadow-sm col-span-2 sm:col-span-1">
                <p class="text-[11px] font-bold uppercase tracking-wider text-red-700">Alfa</p>
                <p id="stat-alfa" class="mt-2 text-2xl font-extrabold text-red-800">1</p>
                <p class="mt-2 text-xs font-semibold text-red-600">Tanpa keterangan</p>
            </div>
        </section>

        <!-- Search & Filter Controls -->
        <section class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="relative flex-1 max-w-md">
                <i class="bi bi-search absolute left-4 top-3.5 text-slate-400"></i>
                <input type="text" id="search-input" placeholder="Cari nama atau NIS siswa..." class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-11 pr-4 text-sm font-medium text-slate-800 shadow-sm placeholder:text-slate-400 focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-100">
            </div>

            <div class="flex items-center gap-2">
                <select id="status-filter" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-100">
                    <option value="all">Semua Status</option>
                    <option value="Hadir">Hadir</option>
                    <option value="Sakit">Sakit</option>
                    <option value="Izin">Izin</option>
                    <option value="Alfa">Alfa</option>
                </select>
            </div>
        </section>

        <!-- Main Table Container -->
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-6 py-4 flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-slate-800">Daftar Presensi Siswa</h2>
                    <p class="text-xs text-slate-500">Menampilkan daftar siswa di kelas <span id="current-class-label" class="font-bold text-emerald-700">XI RPL 1</span></p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[700px] text-left">
                    <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500">
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
                        <!-- Content rendered via JS -->
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <!-- MODAL EDIT / DETAIL PRESENSI SISWA -->
    <div id="student-modal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-slate-950/60 p-4 opacity-0 backdrop-blur-sm transition-opacity duration-200" role="dialog" aria-modal="true">
        <div id="modal-panel" class="w-full max-w-md translate-y-4 rounded-2xl bg-white p-6 shadow-2xl transition duration-200 sm:translate-y-0">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <h3 class="text-lg font-bold text-slate-900">Ubah Status Presensi</h3>
                <button type="button" id="close-modal" class="text-slate-400 hover:text-slate-600"><i class="bi bi-x-lg"></i></button>
            </div>

            <div class="mt-4 space-y-4">
                <div>
                    <p class="text-xs font-semibold text-slate-400">Nama Siswa</p>
                    <p id="modal-student-name" class="font-bold text-slate-800 text-base"></p>
                    <p id="modal-student-nis" class="text-xs text-slate-500"></p>
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-400">Status Kehadiran</label>
                    <div class="mt-2 grid grid-cols-2 gap-2">
                        <label class="flex items-center gap-2 rounded-xl border border-slate-200 p-3 cursor-pointer hover:bg-emerald-50">
                            <input type="radio" name="modal_status" value="Hadir" class="accent-emerald-600">
                            <span class="text-xs font-bold text-slate-700">Hadir</span>
                        </label>
                        <label class="flex items-center gap-2 rounded-xl border border-slate-200 p-3 cursor-pointer hover:bg-amber-50">
                            <input type="radio" name="modal_status" value="Sakit" class="accent-amber-600">
                            <span class="text-xs font-bold text-slate-700">Sakit</span>
                        </label>
                        <label class="flex items-center gap-2 rounded-xl border border-slate-200 p-3 cursor-pointer hover:bg-orange-50">
                            <input type="radio" name="modal_status" value="Izin" class="accent-orange-600">
                            <span class="text-xs font-bold text-slate-700">Izin</span>
                        </label>
                        <label class="flex items-center gap-2 rounded-xl border border-slate-200 p-3 cursor-pointer hover:bg-red-50">
                            <input type="radio" name="modal_status" value="Alfa" class="accent-red-600">
                            <span class="text-xs font-bold text-slate-700">Alfa</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-400">Catatan / Alasan</label>
                    <textarea id="modal-note" rows="3" placeholder="Tambahkan keterangan (opsional)..." class="mt-1.5 w-full rounded-xl border border-slate-200 p-3 text-xs font-medium text-slate-800 focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-100"></textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-2 border-t border-slate-100 pt-4">
                <button type="button" id="cancel-modal" class="rounded-xl px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100">Batal</button>
                <button type="button" id="save-modal" class="rounded-xl bg-emerald-600 px-5 py-2 text-xs font-bold text-white shadow-md shadow-emerald-600/20 hover:bg-emerald-700">Simpan Perubahan</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Data Dummy Siswa Per Kelas
    const studentsData = [
        { nis: '20241001', name: 'Aditya Pratama', gender: 'L', class: 'XI RPL 1', status: 'Hadir', note: '-' },
        { nis: '20241002', name: 'Alya Nuraini', gender: 'P', class: 'XI RPL 1', status: 'Hadir', note: '-' },
        { nis: '20241003', name: 'Bagus Setyo', gender: 'L', class: 'XI RPL 1', status: 'Sakit', note: 'Surat dokter terlampir' },
        { nis: '20241004', name: 'Citra Dewi', gender: 'P', class: 'XI RPL 1', status: 'Hadir', note: '-' },
        { nis: '20241005', name: 'Dian Ananda', gender: 'P', class: 'XI RPL 1', status: 'Izin', note: 'Acara keluarga' },
        { nis: '20241006', name: 'Eko Prasetyo', gender: 'L', class: 'XI RPL 1', status: 'Alfa', note: 'Tanpa konfirmasi' },
        { nis: '20242001', name: 'Fahri Ramadhan', gender: 'L', class: 'XI RPL 2', status: 'Hadir', note: '-' },
        { nis: '20242002', name: 'Gita Gutawa', gender: 'P', class: 'XI RPL 2', status: 'Sakit', note: 'Demam tinggi' },
    ];

    const tableBody = document.getElementById('student-table-body');
    const classSelect = document.getElementById('class-select');
    const searchInput = document.getElementById('search-input');
    const statusFilter = document.getElementById('status-filter');
    const modal = document.getElementById('student-modal');
    const modalPanel = document.getElementById('modal-panel');
    let selectedStudent = null;

    function getBadgeClass(status) {
        switch(status) {
            case 'Hadir': return 'bg-emerald-50 text-emerald-700 ring-emerald-100';
            case 'Sakit': return 'bg-amber-50 text-amber-700 ring-amber-100';
            case 'Izin': return 'bg-orange-50 text-orange-700 ring-orange-100';
            case 'Alfa': return 'bg-red-50 text-red-700 ring-red-100';
            default: return 'bg-slate-50 text-slate-700 ring-slate-100';
        }
    }

    function renderTable() {
        const selectedClass = classSelect.value;
        const searchQuery = searchInput.value.toLowerCase();
        const selectedStatus = statusFilter.value;

        document.getElementById('current-class-label').textContent = selectedClass;

        const filtered = studentsData.filter(s => {
            const matchClass = s.class === selectedClass;
            const matchSearch = s.name.toLowerCase().includes(searchQuery) || s.nis.includes(searchQuery);
            const matchStatus = selectedStatus === 'all' || s.status === selectedStatus;
            return matchClass && matchSearch && matchStatus;
        });

        // Update Stat Cards
        const classStudents = studentsData.filter(s => s.class === selectedClass);
        document.getElementById('stat-total').textContent = classStudents.length;
        document.getElementById('stat-hadir').textContent = classStudents.filter(s => s.status === 'Hadir').length;
        document.getElementById('stat-sakit').textContent = classStudents.filter(s => s.status === 'Sakit').length;
        document.getElementById('stat-izin').textContent = classStudents.filter(s => s.status === 'Izin').length;
        document.getElementById('stat-alfa').textContent = classStudents.filter(s => s.status === 'Alfa').length;

        if (filtered.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="6" class="px-6 py-8 text-center text-slate-400 font-medium">Tidak ada data siswa ditemukan.</td></tr>`;
            return;
        }

        tableBody.innerHTML = filtered.map(s => `
            <tr class="transition hover:bg-slate-50">
                <td class="whitespace-nowrap px-6 py-4 font-mono text-xs text-slate-500">${s.nis}</td>
                <td class="px-6 py-4 font-bold text-slate-800">${s.name}</td>
                <td class="px-6 py-4 font-semibold text-slate-500">${s.gender}</td>
                <td class="px-6 py-4">
                    <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold ring-1 ${getBadgeClass(s.status)}">
                        ${s.status}
                    </span>
                </td>
                <td class="px-6 py-4 text-xs text-slate-500">${s.note}</td>
                <td class="px-6 py-4 text-center">
                    <button type="button" onclick="openEditModal('${s.nis}')" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-bold text-slate-700 transition hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-200">
                        Ubah Status
                    </button>
                </td>
            </tr>
        `).join('');
    }

    window.openEditModal = function(nis) {
        selectedStudent = studentsData.find(s => s.nis === nis);
        if (!selectedStudent) return;

        document.getElementById('modal-student-name').textContent = selectedStudent.name;
        document.getElementById('modal-student-nis').textContent = `NIS: ${selectedStudent.nis} • Kelas: ${selectedStudent.class}`;
        document.getElementById('modal-note').value = selectedStudent.note === '-' ? '' : selectedStudent.note;
        
        const radios = document.getElementsByName('modal_status');
        radios.forEach(r => { r.checked = (r.value === selectedStudent.status); });

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

    document.getElementById('save-modal').addEventListener('click', () => {
        if (!selectedStudent) return;
        const checkedStatus = document.querySelector('input[name="modal_status"]:checked');
        if (checkedStatus) {
            selectedStudent.status = checkedStatus.value;
            selectedStudent.note = document.getElementById('modal-note').value.trim() || '-';
        }
        closeModal();
        renderTable();
    });

    classSelect.addEventListener('change', renderTable);
    searchInput.addEventListener('input', renderTable);
    statusFilter.addEventListener('change', renderTable);

    renderTable();
});
</script>
@endsection