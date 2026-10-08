@extends('layouts.app')

@section('title', 'Rekap Kehadiran Siswa - Piket JurnalKita')

@section('sidebar')
    @include('layouts.guru-pengajar.sidebar', ['activePage' => 'piket'])
@endsection

@section('navbar')
    @include('layouts.guru-pengajar.navbar', ['activePage' => 'piket'])
@endsection

@section('content')
<div id="student-attendance-page" class="min-h-full bg-slate-50 p-4 pb-24 font-sans sm:p-6 lg:p-8">
    <div class="mx-auto max-w-7xl">
        <a href="{{ route('dashboard.piket') }}" class="mb-4 inline-flex items-center gap-2 rounded-xl border border-sky-200 bg-sky-50 px-3 py-2 text-xs font-bold text-sky-700 transition hover:bg-sky-100">
            <i class="bi bi-arrow-left"></i>Kembali
        </a>
        
        <!-- Flash Message -->
        @if(session('success'))
            <div class="mb-5 flex items-center justify-between rounded-xl bg-emerald-50 p-4 border border-emerald-200 text-emerald-800 text-sm font-semibold shadow-xs">
                <div class="flex items-center gap-2">
                    <i class="bi bi-check-circle-fill text-lg text-emerald-600"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700"><i class="bi bi-x-lg"></i></button>
            </div>
        @endif

        <!-- Top Filter & Controls Card: BERSAMPINGAN DI MOBILE -->
        <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between rounded-2xl bg-white p-3.5 border border-slate-200 shadow-xs">
            <div>
                <p class="text-xs font-bold text-slate-700">Filter Kelas &amp; Tanggal</p>
                <p class="hidden text-[11px] text-slate-400 sm:block">Pilih kelas dan tanggal untuk memuat data absensi siswa</p>
            </div>

            <!-- Action Controls: grid-cols-2 di Mobile, flex di Desktop -->
            <div class="grid grid-cols-2 gap-2 w-full sm:w-auto sm:flex sm:items-center sm:gap-3">
                <!-- Dropdown Pilih Kelas -->
                <div class="relative w-full sm:w-48">
                    <select id="class-select" class="w-full appearance-none rounded-xl border border-slate-300 bg-white px-3 py-2 pr-8 text-xs sm:text-sm font-bold text-slate-700 shadow-xs focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-100 cursor-pointer">
                        @foreach($kelasList as $kelas)
                            <option value="{{ $kelas->id_kelas }}" {{ $selectedKelasId == $kelas->id_kelas ? 'selected' : '' }}>
                                {{ $kelas->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                    <i class="bi bi-chevron-down absolute right-2.5 top-1/2 -translate-y-1/2 text-xs text-slate-400 pointer-events-none"></i>
                </div>

                <!-- Date Picker -->
                <div class="relative flex items-center w-full sm:w-auto rounded-xl border border-slate-300 bg-white px-2.5 py-1.5 text-xs sm:text-sm font-semibold text-slate-700 shadow-xs">
                    <i class="bi bi-calendar3 text-emerald-600 mr-2 shrink-0 text-xs"></i>
                    <input type="date" id="student-attendance-date" value="{{ $tanggal }}" class="w-full bg-transparent font-medium focus:outline-none cursor-pointer text-xs sm:text-sm">
                </div>
            </div>
        </div>

        <!-- Ringkasan Status dalam satu kartu -->
        <section class="mb-4 rounded-2xl border border-slate-200 bg-white p-3.5 sm:p-4 shadow-xs">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Ringkasan Kehadiran</p>
                    <p class="mt-0.5 text-xs sm:text-sm font-bold text-slate-700">{{ $selectedKelas?->nama_kelas ?? 'Kelas' }} · {{ $totalSiswa }} siswa</p>
                </div>
                <div class="flex flex-wrap gap-1.5 text-xs font-bold">
                    <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-emerald-800">{{ $totalHadir }} Hadir</span>
                    <span class="rounded-full bg-amber-100 px-2.5 py-1 text-amber-800">{{ $totalSakit }} Sakit</span>
                    <span class="rounded-full bg-sky-100 px-2.5 py-1 text-sky-800">{{ $totalIzin }} Izin</span>
                    <span class="rounded-full bg-indigo-100 px-2.5 py-1 text-indigo-800">{{ $totalDispen }} Dispensasi</span>
                    <span class="rounded-full bg-rose-100 px-2.5 py-1 text-rose-800">{{ $totalAlfa }} Alpa</span>
                </div>
            </div>
        </section>

        <!-- Pencarian & Filter Cepat -->
        <section class="mb-4">
            <div class="relative max-w-md">
                <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                <input type="text" id="search-input" placeholder="Cari nama atau NIS siswa..." class="w-full rounded-xl border border-slate-300 bg-white py-2 pl-9 pr-4 text-xs sm:text-sm font-medium text-slate-800 shadow-xs placeholder:text-slate-400 focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-100">
            </div>
        </section>

        <!-- Main Table Container -->
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xs">
            {{-- HEADER KARTU (STICKY DI DALAM KARTU) --}}
            <div class="sticky top-0 z-10 flex flex-wrap items-center justify-between gap-2.5 border-b border-slate-100 bg-white px-4 py-3 sm:px-6">
                {{-- Judul & Keterangan --}}
                <div class="flex-1 min-w-[180px]">
                    <div class="flex items-center gap-2">
                        <h2 class="font-bold text-slate-800 text-sm sm:text-base">Daftar Presensi Siswa</h2>
                        <span id="student-count-badge" class="rounded-full bg-slate-100 px-2.5 py-0.5 text-[11px] font-bold text-slate-600">
                            {{ $totalSiswa }} Siswa
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-0.5">
                        Kelas <span id="current-class-label" class="font-bold text-emerald-700">{{ $selectedKelas?->nama_kelas }}</span> • {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}
                    </p>
                </div>

                <div class="flex items-center gap-2 ml-auto">
                    {{-- Filter Status --}}
                    <label class="flex items-center gap-1.5 text-xs font-bold text-slate-600">
                        <span class="hidden sm:inline">Status:</span>
                        <select id="status-filter" class="rounded-xl border border-slate-300 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-700 shadow-xs focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-100">
                            <option value="all">Semua Status</option>
                            <option value="Hadir">Hadir</option>
                            <option value="Sakit">Sakit</option>
                            <option value="Izin">Izin</option>
                            <option value="Dispensasi">Dispensasi</option>
                            <option value="Alfa">Alfa</option>
                        </select>
                    </label>

                    {{-- Tombol Izin/Sakit Multi-Hari --}}
                    <button type="button" onclick="openMultiDayModal()"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-bold text-indigo-700 shadow-xs transition hover:bg-indigo-100">
                        <i class="bi bi-calendar-range"></i>
                        <span>Multi-Hari</span>
                    </button>

                    {{-- Tombol Simpan --}}
                    @if($isEditableDate)
                        <button form="student-attendance-form" type="submit"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-4 py-1.5 text-xs font-bold text-white shadow-xs transition hover:bg-emerald-700 active:scale-95">
                            <i class="bi bi-floppy"></i>
                            <span>Simpan</span>
                        </button>
                    @endif
                </div>
            </div>

            @unless($isEditableDate)
                <div class="bg-amber-50 border-b border-amber-100 px-4 py-2 text-xs font-semibold text-amber-800 flex items-center gap-2">
                    <i class="bi bi-eye-fill text-amber-600"></i>
                    <span>Mode pemantauan: status absensi hanya dapat diubah pada tanggal hari ini.</span>
                </div>
            @endunless

            {{-- HEADER TABEL DESKTOP --}}
            <div class="hidden sm:grid sm:grid-cols-12 gap-3 bg-slate-50/90 px-4 py-2 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100">
                <div class="col-span-5 flex items-center gap-3">
                    <span class="w-7 text-center">No</span>
                    <span>Siswa</span>
                </div>
                <div class="col-span-3 text-center">
                    <span>Status Presensi</span>
                </div>
                <div class="col-span-4">
                    <span>Keterangan</span>
                </div>
            </div>

            <form id="student-attendance-form" method="POST" action="{{ route('piket.kehadiran-siswa.update') }}" class="text-sm">
                @csrf
                <input type="hidden" name="bulk_attendance" value="1">
                <input type="hidden" name="kelas_id" value="{{ $selectedKelasId }}">
                <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                
                {{-- SCROLL CONTAINER: DIBATASI TINGGINYA AGAR TIDAK MENGGULUNG HALAMAN UTAMA --}}
                <div id="student-table-body" class="max-h-[58vh] sm:max-h-[520px] overflow-y-auto overscroll-contain divide-y divide-slate-100">
                    <!-- Content rendered via JS from real backend data -->
                </div>
            </form>
        <!-- Modal Form Input Ketidakhadiran Multi-Hari -->
        <div id="multi-day-modal" class="fixed inset-0 z-50 hidden items-center justify-center overflow-y-auto bg-slate-900/60 p-4 backdrop-blur-xs">
            <div class="relative w-full max-w-lg rounded-2xl bg-white p-5 shadow-2xl sm:p-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-100 text-indigo-700 text-sm">
                            <i class="bi bi-calendar-range"></i>
                        </span>
                        <h3 class="text-base font-bold text-slate-800">Catat Ketidakhadiran Multi-Hari</h3>
                    </div>
                    <button type="button" onclick="closeMultiDayModal()" class="text-slate-400 hover:text-slate-600">
                        <i class="bi bi-x-lg text-sm"></i>
                    </button>
                </div>

                <form method="POST" action="{{ route('piket.kehadiran-siswa.multi-day') }}" enctype="multipart/form-data" class="mt-4 space-y-3.5 text-xs sm:text-sm">
                    @csrf
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Pilih Siswa (Kelas {{ $selectedKelas?->nama_kelas }})</label>
                        <select name="siswa_id" required class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs sm:text-sm font-semibold text-slate-800 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                            <option value="">-- Pilih Siswa --</option>
                            @foreach($studentsData as $st)
                                <option value="{{ $st['id'] }}">{{ $st['name'] }} (NIS: {{ $st['nis'] ?? '-' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" value="{{ $tanggal }}" required class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs sm:text-sm font-medium text-slate-800 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" value="{{ $tanggal }}" required class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs sm:text-sm font-medium text-slate-800 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Status Kehadiran</label>
                        <select name="status" required class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs sm:text-sm font-semibold text-slate-800 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                            <option value="Sakit">Sakit</option>
                            <option value="Izin">Izin</option>
                            <option value="Alfa">Alfa</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Keterangan / Alasan</label>
                        <textarea name="alasan" rows="2" placeholder="Contoh: Dirawat di RS, isolasi mandiri, keperluan keluarga penting..." class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs sm:text-sm text-slate-800 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"></textarea>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Unggah Surat Izin / Surat Dokter (Opsional)</label>
                        <input type="file" name="dokumen" accept=".jpg,.jpeg,.png,.pdf" class="w-full text-xs text-slate-500 file:mr-3 file:rounded-xl file:border-0 file:bg-indigo-50 file:px-3 file:py-1.5 file:text-xs file:font-bold file:text-indigo-700 hover:file:bg-indigo-100">
                        <p class="mt-1 text-[11px] text-slate-400">Format: JPG, PNG, atau PDF (maks. 4 MB).</p>
                    </div>

                    <div class="flex items-center justify-end gap-2 border-t border-slate-100 pt-3">
                        <button type="button" onclick="closeMultiDayModal()" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-bold text-slate-600 transition hover:bg-slate-50">
                            Batal
                        </button>
                        <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2 text-xs font-bold text-white shadow-xs transition hover:bg-indigo-700">
                            <i class="bi bi-check-lg mr-1"></i> Terapkan Rentang Hari
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Data Riil Siswa dari Database Controller
    const studentsData = @json($studentsData);
    const canEditAttendance = @js($isEditableDate);

    const tableBody = document.getElementById('student-table-body');
    const classSelect = document.getElementById('class-select');
    const dateInput = document.getElementById('student-attendance-date');
    const searchInput = document.getElementById('search-input');
    const statusFilter = document.getElementById('status-filter');
    const attendanceForm = document.getElementById('student-attendance-form');

    // Status classes map
    const STATUS_MAP = {
        'Hadir': ['bg-emerald-600', 'border-emerald-600', 'text-white'],
        'Sakit': ['bg-amber-500', 'border-amber-500', 'text-white'],
        'Izin':  ['bg-sky-600', 'border-sky-600', 'text-white'],
        'Alfa':  ['bg-rose-600', 'border-rose-600', 'text-white'],
        'Alpa':  ['bg-rose-600', 'border-rose-600', 'text-white'],
        'D':     ['bg-indigo-600', 'border-indigo-600', 'text-white'],
        'Dispensasi': ['bg-indigo-600', 'border-indigo-600', 'text-white'],
    };
    const ALL_ACTIVE_CLASSES = [
        'bg-emerald-600', 'border-emerald-600',
        'bg-amber-500', 'border-amber-500',
        'bg-sky-600', 'border-sky-600',
        'bg-rose-600', 'border-rose-600',
        'bg-indigo-600', 'border-indigo-600',
        'text-white',
    ];
    const INACTIVE_CLS = ['bg-white', 'border-slate-200', 'text-slate-400'];

    // Apply active/inactive styling to all labels in a fieldset based on which radio is checked
    function syncRadioStyles(fieldset) {
        const inputs = fieldset.querySelectorAll('input[type="radio"]');
        inputs.forEach((input) => {
            const label = fieldset.querySelector(`label[for="${input.id}"]`);
            if (!label) { return; }
            ALL_ACTIVE_CLASSES.forEach(c => label.classList.remove(c));
            INACTIVE_CLS.forEach(c => label.classList.remove(c));

            if (input.checked) {
                const activeClasses = STATUS_MAP[input.value] || STATUS_MAP['Hadir'];
                activeClasses.forEach(c => label.classList.add(c));
            } else {
                INACTIVE_CLS.forEach(c => label.classList.add(c));
            }
        });
    }

    function displayStatus(status) {
        if (status === 'D') { return 'Dispensasi'; }
        if (status === 'Alpa') { return 'Alfa'; }
        return status;
    }

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>'"]/g, (c) => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;',
        })[c]);
    }

    function renderTable() {
        const searchQuery  = searchInput.value.toLowerCase().trim();
        const selectedStatus = statusFilter.value;

        const filtered = studentsData.filter(s => {
            const matchSearch = !searchQuery
                || String(s.name || '').toLowerCase().includes(searchQuery)
                || String(s.nis || '').toLowerCase().includes(searchQuery)
                || String(s.nisn || '').toLowerCase().includes(searchQuery);
            const matchStatus = selectedStatus === 'all' || displayStatus(s.status) === selectedStatus;
            return matchSearch && matchStatus;
        });

        if (filtered.length === 0) {
            tableBody.innerHTML = `<div class="px-6 py-12 text-center font-medium text-slate-400 text-xs">Tidak ada siswa yang sesuai pencarian atau filter.</div>`;
            return;
        }

        tableBody.innerHTML = filtered.map((s, index) => {
            // Default to 'Hadir' if no status recorded
            const storedStatus = s.status || 'Hadir';
            const isLocked = s.is_dispen || !canEditAttendance;

            const OPTIONS = [
                { label: 'H', value: 'Hadir', title: 'Hadir', activeClass: 'bg-emerald-600 border-emerald-600 text-white' },
                { label: 'S', value: 'Sakit', title: 'Sakit', activeClass: 'bg-amber-500 border-amber-500 text-white' },
                { label: 'I', value: 'Izin',  title: 'Izin',  activeClass: 'bg-sky-600 border-sky-600 text-white' },
                { label: 'A', value: 'Alfa',  title: 'Alfa',  activeClass: 'bg-rose-600 border-rose-600 text-white' },
                { label: 'D', value: 'D',     title: 'Dispensasi', activeClass: 'bg-indigo-600 border-indigo-600 text-white' },
            ];

            const buttons = OPTIONS.map(({ label, value, title, activeClass }) => {
                const inputId = `att-${s.id}-${value}`;
                const isChecked = storedStatus === value
                    || (value === 'Alfa' && storedStatus === 'Alpa')
                    || (value === 'D' && (storedStatus === 'Dispensasi' || storedStatus === 'D'));

                const inactiveCls = 'bg-white border-slate-200 text-slate-400 hover:border-slate-300 hover:bg-slate-50 hover:text-slate-600';
                const stateCls    = isChecked ? activeClass : inactiveCls;
                const disabledCls = isLocked  ? 'cursor-not-allowed opacity-60' : 'cursor-pointer';

                return `
                    <input id="${inputId}" type="radio"
                        name="absensi[${s.id}]" value="${value}"
                        data-group="${s.id}"
                        class="sr-only"
                        ${isChecked ? 'checked' : ''}
                        ${isLocked  ? 'disabled' : ''}>
                    <label for="${inputId}" title="${title}"
                        class="flex h-7 w-7 sm:h-8 sm:w-8 select-none items-center justify-center rounded-lg border text-xs font-bold transition shadow-2xs ${stateCls} ${disabledCls}">
                        ${label}
                    </label>`;
            }).join('');

            return `
                <div data-attendance-card class="p-3 sm:px-4 sm:py-2.5 transition hover:bg-slate-50/80">
                    <div class="flex flex-col sm:grid sm:grid-cols-12 sm:items-center gap-2.5 sm:gap-3">
                        <!-- Siswa & No -->
                        <div class="sm:col-span-5 flex items-center gap-3 min-w-0">
                            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-emerald-50 border border-emerald-200 text-xs font-bold text-emerald-800">
                                ${String(index + 1).padStart(2, '0')}
                            </span>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <p class="truncate text-xs sm:text-sm font-bold text-slate-800">${escapeHtml(s.name)}</p>
                                    ${s.is_multi_day && s.multi_day_info ? `
                                        <span class="inline-flex items-center gap-1 rounded-md bg-indigo-100 px-1.5 py-0.5 text-[10px] font-bold text-indigo-700" title="Periode: ${escapeHtml(s.multi_day_info.tanggal_mulai)} s/d ${escapeHtml(s.multi_day_info.tanggal_selesai)} (${escapeHtml(s.multi_day_info.alasan || 'Izin/Sakit')})">
                                            <i class="bi bi-calendar-range text-[9px]"></i> Multi-Hari
                                        </span>
                                    ` : ''}
                                </div>
                                <p class="text-[11px] text-slate-400 truncate">NIS: ${escapeHtml(s.nis || '-')} &bull; ${escapeHtml(s.gender || '-')}</p>
                            </div>
                        </div>

                        <!-- Status Buttons -->
                        <div class="sm:col-span-3 flex items-center justify-between sm:justify-center">
                            <fieldset data-student-id="${s.id}" class="flex items-center gap-1.5 shrink-0" aria-label="Status ${escapeHtml(s.name)}">
                                ${buttons}
                            </fieldset>
                        </div>

                        <!-- Catatan Input -->
                        <div class="sm:col-span-4 min-w-0">
                            <input type="text" name="absensi_catatan[${s.id}]" maxlength="255"
                                value="${escapeHtml(s.note === '-' ? '' : s.note)}"
                                ${isLocked ? 'readonly' : ''}
                                placeholder="Keterangan jika tidak hadir..."
                                class="w-full rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-1.5 text-xs text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-100 ${isLocked ? 'cursor-not-allowed opacity-60' : ''}">
                        </div>
                    </div>
                </div>`;
        }).join('');
    }

    // Ganti Kelas & Tanggal -> reload URL
    classSelect.addEventListener('change', function () {
        window.location.href = `{{ route('piket.kehadiran-siswa') }}?kelas_id=${this.value}&tanggal=${dateInput.value}`;
    });
    dateInput.addEventListener('change', function () {
        window.location.href = `{{ route('piket.kehadiran-siswa') }}?kelas_id=${classSelect.value}&tanggal=${this.value}`;
    });

    searchInput.addEventListener('input', renderTable);
    statusFilter.addEventListener('change', renderTable);

    // Delegated listener: update label styles when any radio changes
    tableBody.addEventListener('change', (event) => {
        const input = event.target;
        if (input.type !== 'radio') { return; }

        // Mark card as dirty
        input.closest('[data-attendance-card]')?.setAttribute('data-dirty', 'true');

        // Re-sync styles for this fieldset
        const fieldset = input.closest('fieldset[data-student-id]');
        if (fieldset) { syncRadioStyles(fieldset); }
    });

    tableBody.addEventListener('input', (event) => {
        event.target.closest('[data-attendance-card]')?.setAttribute('data-dirty', 'true');
    });

    attendanceForm.addEventListener('submit', () => {
        tableBody.querySelectorAll('[data-attendance-card]').forEach((card) => {
            if (card.dataset.dirty !== 'true') {
                card.querySelectorAll('input[name^="absensi["], input[name^="absensi_catatan["]').forEach((inp) => {
                    inp.disabled = true;
                });
            }
        });
    });

    renderTable();
});

function openMultiDayModal() {
    const modal = document.getElementById('multi-day-modal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

function closeMultiDayModal() {
    const modal = document.getElementById('multi-day-modal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}
</script>
@endsection
