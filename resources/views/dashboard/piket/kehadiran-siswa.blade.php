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
                    <a href="{{ route('dashboard.piket') }}" class="mb-4 inline-flex items-center gap-2 text-sm font-semibold text-emerald-700 transition hover:text-emerald-800">
                <i class="bi bi-arrow-left"></i>Kembali ke halaman utama piket
            </a>
        
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


        <!-- Top Filter & Controls Card -->
        <div class="mb-6 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between rounded-2xl bg-white p-4 border border-slate-200 shadow-2xs">

            <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Rekap Kehadiran Siswa</h1>
            <div>


                <p class="text-xs font-bold text-slate-700">Filter Kelas & Tanggal</p>
                <p class="hidden text-[11px] text-slate-400 sm:block">Pilih kelas dan tanggal untuk memuat data absensi siswa</p>
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

            </div>
        </div>

        <!-- Ringkasan Status dalam satu kartu -->
        <section class="mb-7 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div><p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Ringkasan Kehadiran</p><p class="mt-1 text-sm font-semibold text-slate-700">{{ $selectedKelas?->nama_kelas ?? 'Kelas' }} · {{ $totalSiswa }} siswa</p></div>
                <div class="flex flex-wrap gap-2 text-xs font-bold"><span class="rounded-full bg-emerald-100 px-3 py-1.5 text-emerald-800">{{ $totalHadir }} Hadir</span><span class="rounded-full bg-amber-100 px-3 py-1.5 text-amber-800">{{ $totalSakit }} Sakit</span><span class="rounded-full bg-orange-100 px-3 py-1.5 text-orange-800">{{ $totalIzin }} Izin</span><span class="rounded-full bg-indigo-100 px-3 py-1.5 text-indigo-800">{{ $totalDispen }} Dispensasi</span><span class="rounded-full bg-rose-100 px-3 py-1.5 text-rose-800">{{ $totalAlfa }} Alpa</span></div>
            </div>
        </section>

        <!-- Pencarian -->
        <section class="mb-5">
            <div class="relative max-w-xl">
                <i class="bi bi-search absolute left-4 top-3.5 text-slate-400"></i>
                <input type="text" id="search-input" placeholder="Cari nama atau NIS siswa..." class="w-full rounded-xl border border-slate-300 bg-white py-2.5 pl-11 pr-4 text-sm font-medium text-slate-800 shadow-sm placeholder:text-slate-400 focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-100">
            </div>

        </section>

        <!-- Main Table Container -->
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-wrap items-center gap-3 border-b border-slate-100 px-4 py-4 sm:px-6">
                {{-- Judul & Keterangan --}}
                <div class="flex-1 min-w-0">
                    <h2 class="font-bold text-slate-800">Daftar Presensi Siswa</h2>
                    <p class="hidden text-xs text-slate-500 sm:block">Kelas <span id="current-class-label" class="font-bold text-emerald-700">{{ $selectedKelas?->nama_kelas }}</span> • {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}</p>
                    @unless($isEditableDate)
                        <p class="mt-1 text-xs font-semibold text-amber-700"><i class="bi bi-eye-fill mr-1"></i>Mode pemantauan: status hanya dapat diubah pada tanggal hari ini.</p>
                    @endunless
                </div>

                {{-- Filter Status --}}
                <label class="flex shrink-0 items-center gap-2 text-xs font-bold text-slate-600">
                    <span>Status</span>
                    <select id="status-filter" class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-100">
                        <option value="all">Semua</option>
                        <option value="Hadir">Hadir</option>
                        <option value="Sakit">Sakit</option>
                        <option value="Izin">Izin</option>
                        <option value="Dispensasi">Dispensasi</option>
                        <option value="Alfa">Alfa</option>
                    </select>
                </label>

                {{-- Tombol Simpan (menempel kanan, terpisah dari dropdown) --}}
                @if($isEditableDate)
                    <button form="student-attendance-form" type="submit"
                        class="ml-auto shrink-0 inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-emerald-700 active:scale-95">
                        <i class="bi bi-floppy"></i>Simpan
                    </button>
                @endif
            </div>

            <form id="student-attendance-form" method="POST" action="{{ route('piket.kehadiran-siswa.update') }}" class="text-sm">
                @csrf
                <input type="hidden" name="bulk_attendance" value="1">
                <input type="hidden" name="kelas_id" value="{{ $selectedKelasId }}">
                <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                <div id="student-table-body" class="space-y-3 p-3 sm:p-4">
                    <!-- Content rendered via JS from real backend data -->
                </div>
            </form>
        </section>
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

    // CSS classes for active vs inactive radio label states
    const ACTIVE_CLS   = ['bg-emerald-600', 'border-emerald-600', 'text-white'];
    const INACTIVE_CLS = ['bg-white', 'border-slate-200', 'text-slate-500'];

    // Apply active/inactive styling to all labels in a fieldset based on which radio is checked
    function syncRadioStyles(fieldset) {
        const inputs = fieldset.querySelectorAll('input[type="radio"]');
        inputs.forEach((input) => {
            const label = fieldset.querySelector(`label[for="${input.id}"]`);
            if (!label) { return; }
            if (input.checked) {
                INACTIVE_CLS.forEach(c => label.classList.remove(c));
                ACTIVE_CLS.forEach(c => label.classList.add(c));
            } else {
                ACTIVE_CLS.forEach(c => label.classList.remove(c));
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
            const matchSearch = s.name.toLowerCase().includes(searchQuery) || s.nis.includes(searchQuery);
            const matchStatus = selectedStatus === 'all' || displayStatus(s.status) === selectedStatus;
            return matchSearch && matchStatus;
        });

        if (filtered.length === 0) {
            tableBody.innerHTML = `<div class="px-6 py-12 text-center font-medium text-slate-400">Tidak ada siswa yang sesuai filter.</div>`;
            return;
        }

        tableBody.innerHTML = filtered.map((s, index) => {
            // Default to 'Hadir' if no status recorded
            const storedStatus = s.status || 'Hadir';
            const isLocked = s.is_dispen || !canEditAttendance;

            const OPTIONS = [
                { label: 'H', value: 'Hadir' },
                { label: 'S', value: 'Sakit' },
                { label: 'I', value: 'Izin'  },
                { label: 'A', value: 'Alfa'  },
                { label: 'D', value: 'D'     },
            ];

            const buttons = OPTIONS.map(({ label, value }) => {
                const inputId = `att-${s.id}-${value}`;
                const isChecked = storedStatus === value
                    || (value === 'Alfa' && storedStatus === 'Alpa');

                // Inline style: active class applied directly at render time via JS classes string
                const activeCls   = 'bg-emerald-600 border-emerald-600 text-white';
                const inactiveCls = 'bg-white border-slate-200 text-slate-500';
                const stateCls    = isChecked ? activeCls : inactiveCls;
                const disabledCls = isLocked  ? 'cursor-not-allowed opacity-60' : 'cursor-pointer hover:border-emerald-400 hover:bg-emerald-50 hover:text-emerald-700';

                return `
                    <input id="${inputId}" type="radio"
                        name="absensi[${s.id}]" value="${value}"
                        data-group="${s.id}"
                        class="sr-only"
                        ${isChecked ? 'checked' : ''}
                        ${isLocked  ? 'disabled' : ''}>
                    <label for="${inputId}"
                        class="flex h-9 w-full select-none items-center justify-center rounded-lg border text-sm font-bold transition ${stateCls} ${disabledCls}">
                        ${label}
                    </label>`;
            }).join('');

            return `
                <div data-attendance-card class="space-y-2.5 rounded-xl border border-slate-200 bg-white p-3 shadow-sm transition hover:border-emerald-200 hover:shadow-md">
                    <div class="flex items-center gap-3">
                        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full border border-emerald-200 bg-emerald-50 text-[11px] font-bold text-emerald-800">${index + 1}</span>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-bold leading-tight text-slate-900">${escapeHtml(s.name)}</p>
                            <p class="mt-0.5 text-[11px] font-medium text-slate-400">NIS: ${escapeHtml(s.nis)} &bull; ${escapeHtml(s.gender)}</p>
                        </div>
                    </div>
                    <fieldset data-student-id="${s.id}" class="grid grid-cols-5 gap-2" aria-label="Status kehadiran ${escapeHtml(s.name)}">
                        ${buttons}
                    </fieldset>
                    <label class="block">
                        <span class="sr-only">Alasan / Keterangan</span>
                        <input type="text" name="absensi_catatan[${s.id}]" maxlength="255"
                            value="${escapeHtml(s.note === '-' ? '' : s.note)}"
                            ${isLocked ? 'readonly' : ''}
                            placeholder="Keterangan jika tidak hadir (opsional)"
                            class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-100 ${isLocked ? 'cursor-not-allowed opacity-60' : ''}">
                    </label>
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
</script>
@endsection
