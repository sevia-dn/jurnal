@extends('layouts.app')

@section('title', 'Dashboard Piket')

@section('sidebar')
    @include('layouts.piket.sidebar')
@endsection

@section('navbar')
    @include('layouts.piket.navbar')
@endsection

@section('content')
    <div id="piket-dashboard" class="min-h-full bg-slate-50 p-5 pb-24 font-sans sm:p-8 lg:p-10">
        <div class="mx-auto max-w-7xl">
            <!-- Header -->
            <header class="mb-7 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="mb-2 flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.18em] text-emerald-700">
                        <span class="h-2 w-2 rounded-full bg-emerald-500 shadow-[0_0_0_4px_rgba(16,185,129,0.12)]"></span>
                        Live monitoring
                    </div>
                    <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Monitoring Jurnal Real-time</h1>
                    <p class="mt-2 text-sm text-slate-500">Pantau laporan kelas dan kehadiran guru hari ini.</p>
                </div>

                <div class="inline-flex w-fit items-center gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                        <i class="bi bi-clock" aria-hidden="true"></i>
                    </span>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Waktu sekarang</p>
                        <time id="realtime-clock" class="font-mono text-base font-bold tabular-nums text-slate-800" aria-live="polite">--:--:-- WIB</time>
                    </div>
                </div>
            </header>

            <!-- Summary Cards -->
            <section aria-label="Ringkasan jurnal" class="mb-7 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <!-- Card 1: Total Kelas -->
                <button type="button" data-filter="all" aria-pressed="true" class="summary-card group min-h-44 rounded-2xl border border-emerald-200 bg-white p-5 text-left shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-emerald-100 ring-4 ring-emerald-50">
                    <div class="flex items-start justify-between gap-4">
                        <div><p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Kelas</p><p class="mt-2 text-3xl font-extrabold text-slate-900">36</p></div>
                        <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-100 text-lg text-emerald-700 transition group-hover:scale-110"><i class="bi bi-building" aria-hidden="true"></i></span>
                    </div>
                    <div class="mt-6"><div class="flex items-center justify-between text-xs font-semibold text-emerald-700"><span>Semua laporan</span><span>100%</span></div><div class="mt-2 h-2 overflow-hidden rounded-full bg-emerald-100"><div class="h-full w-full rounded-full bg-emerald-600"></div></div></div>
                </button>

                <!-- Card 2: Guru Hadir -->
                <button type="button" data-filter="hadir" aria-pressed="false" class="summary-card group min-h-44 rounded-2xl border border-slate-200 bg-white p-5 text-left shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-emerald-200 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-emerald-100">
                    <div class="flex items-start justify-between gap-4">
                        <div><p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Guru Hadir</p><p class="mt-2 text-3xl font-extrabold text-slate-900">34</p></div>
                        <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-lg text-emerald-600 transition group-hover:scale-110"><i class="bi bi-person-check" aria-hidden="true"></i></span>
                    </div>
                    <p class="mt-6 text-xs font-semibold text-emerald-600"><i class="bi bi-arrow-up-short" aria-hidden="true"></i> Pantauan aman</p>
                </button>

                <!-- Card 3: Guru Absen/Izin (Fokus Piket) -->
                <button type="button" data-filter="absen" aria-pressed="false" class="summary-card group min-h-44 rounded-2xl border border-slate-200 bg-white p-5 text-left shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-red-200 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-red-100">
                    <div class="flex items-start justify-between gap-4">
                        <div><p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Guru Absen / Izin</p><p class="mt-2 text-3xl font-extrabold text-slate-900">2</p></div>
                        <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-red-50 text-lg text-red-600 transition group-hover:scale-110"><i class="bi bi-person-slash" aria-hidden="true"></i></span>
                    </div>
                    <p class="mt-6 text-xs font-semibold text-red-600">Butuh validasi izin dari piket</p>
                </button>

                <!-- Card 4: Menunggu Validasi Jurnal (Hanya Pantauan) -->
                <button type="button" data-filter="menunggu_sekre" aria-pressed="false" class="summary-card group min-h-44 rounded-2xl border border-slate-200 bg-white p-5 text-left shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-amber-200 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-amber-100">
                    <div class="flex items-start justify-between gap-4">
                        <div><p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Menunggu Sekre</p><p id="pending-sekre-count" class="mt-2 text-3xl font-extrabold text-slate-900">3</p></div>
                        <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50 text-lg text-amber-600 transition group-hover:scale-110"><i class="bi bi-journal-x" aria-hidden="true"></i></span>
                    </div>
                    <p class="mt-6 text-xs font-semibold text-amber-600">Jurnal menunggu validasi kelas</p>
                </button>
            </section>

            <!-- Quick Action: Dispensasi Siswa -->
            <section class="mb-7 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <a href="{{ route('piket.dispensasi.form') }}"
                   class="group flex items-center gap-4 rounded-2xl border border-emerald-200 bg-gradient-to-br from-emerald-50 to-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-emerald-600 text-white text-2xl shadow group-hover:bg-emerald-700 transition">
                        <i class="bi bi-file-earmark-plus-fill"></i>
                    </div>
                    <div>
                        <p class="font-extrabold text-slate-900 text-base">Dispensasi Siswa</p>
                        <p class="text-xs text-slate-500 mt-0.5">Input pengajuan izin & pantau status persetujuan Waka</p>
                    </div>
                    <i class="bi bi-chevron-right ml-auto text-slate-300 group-hover:text-emerald-600 transition text-lg"></i>
                </a>

                <a href="{{ route('piket.kehadiran') }}"
                   class="group flex items-center gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-amber-500 text-white text-2xl shadow group-hover:bg-amber-600 transition">
                        <i class="bi bi-person-check-fill"></i>
                    </div>
                    <div>
                        <p class="font-extrabold text-slate-900 text-base">Kehadiran Guru</p>
                        <p class="text-xs text-slate-500 mt-0.5">Rekap dan verifikasi kehadiran guru hari ini</p>
                    </div>
                    <i class="bi bi-chevron-right ml-auto text-slate-300 group-hover:text-amber-600 transition text-lg"></i>
                </a>
            </section>

            <!-- Table Section -->
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm" aria-labelledby="journal-table-title">
                <div class="flex flex-col gap-4 border-b border-slate-100 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                    <div><h2 id="journal-table-title" class="font-bold text-slate-800">Aktivitas Jurnal & Kehadiran Hari Ini</h2><p id="filter-description" class="mt-1 text-sm text-slate-500" aria-live="polite">Menampilkan semua data terbaru.</p></div>
                    <button id="reset-filter" type="button" class="hidden items-center gap-2 self-start rounded-lg px-3 py-2 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-50 focus:outline-none focus:ring-4 focus:ring-emerald-100 sm:self-auto"><i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i> Reset filter</button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-[850px] w-full text-left">
                        <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            <tr><th scope="col" class="px-6 py-4">Waktu</th><th scope="col" class="px-6 py-4">Kelas</th><th scope="col" class="px-6 py-4">Guru & Mata Pelajaran</th><th scope="col" class="px-6 py-4">Status Kehadiran</th><th scope="col" class="px-6 py-4">Validasi Sekretaris</th></tr>
                        </thead>
                        <tbody id="journal-table-body" class="divide-y divide-slate-100 text-sm"></tbody>
                    </table>
                </div>

                <!-- Empty State -->
                <div id="empty-state" class="hidden px-6 py-14 text-center">
                    <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-xl text-slate-400"><i class="bi bi-inbox" aria-hidden="true"></i></span>
                    <p class="mt-3 font-semibold text-slate-700">Tidak ada data pada filter ini.</p>
                    <button type="button" data-filter="all" class="mt-3 text-sm font-bold text-emerald-700 hover:text-emerald-800">Tampilkan semua data</button>
                </div>
            </section>
        </div>

        <!-- MODAL VALIDASI IZIN GURU (Milik Piket) -->
        <div id="leave-modal" class="fixed inset-0 z-[60] hidden items-end bg-slate-950/60 p-4 opacity-0 backdrop-blur-sm transition-opacity duration-200 sm:items-center sm:justify-center" role="dialog" aria-modal="true" aria-labelledby="modal-title" aria-hidden="true">
            <div id="modal-panel" class="max-h-[90vh] w-full max-w-lg translate-y-4 overflow-y-auto rounded-2xl bg-white shadow-2xl transition duration-200 sm:translate-y-0 sm:scale-95">
                <div class="flex items-start justify-between gap-5 border-b border-slate-100 px-5 py-5 sm:px-6">
                    <div><p class="text-xs font-bold uppercase tracking-wider text-emerald-600">Persetujuan Piket</p><h2 id="modal-title" class="mt-1 text-xl font-extrabold text-slate-900">Validasi Izin Guru</h2></div>
                    <button id="close-modal" type="button" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus:ring-4 focus:ring-slate-100"><i class="bi bi-x-lg" aria-hidden="true"></i></button>
                </div>

                <div class="space-y-5 px-5 py-6 sm:px-6">
                    <div class="rounded-xl bg-slate-50 p-4">
                        <p class="text-xs font-semibold text-slate-400">Nama Guru</p>
                        <p id="modal-teacher" class="mt-1 font-bold text-slate-800"></p>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="rounded-xl bg-slate-50 p-4"><p class="text-xs font-semibold text-slate-400">Jadwal Mengajar</p><p id="modal-time" class="mt-1 font-bold text-slate-800"></p></div>
                        <div class="rounded-xl bg-slate-50 p-4"><p class="text-xs font-semibold text-slate-400">Kelas</p><p id="modal-class" class="mt-1 font-bold text-slate-800"></p></div>
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-slate-400">Keterangan / Alasan Izin</p>
                        <p id="modal-reason" class="mt-2 rounded-xl border border-slate-200 bg-white p-4 text-sm font-medium leading-relaxed text-slate-700"></p>
                    </div>
                    
                    <div>
                        <p class="text-xs font-semibold text-slate-400">Surat Bukti Izin / Dinas</p>
                        <div class="mt-2 flex min-h-32 flex-col items-center justify-center rounded-xl border-2 border-dashed border-slate-200 bg-slate-50 text-center text-slate-400"><i class="bi bi-file-earmark-text text-3xl" aria-hidden="true"></i><p class="mt-2 text-xs font-medium">Klik untuk melihat dokumen</p></div>
                    </div>
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-slate-100 bg-slate-50 px-5 py-4 sm:flex-row sm:justify-end sm:px-6">
                    <button id="cancel-modal" type="button" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-100 focus:outline-none focus:ring-4 focus:ring-slate-100">Batal</button>
                    <button id="confirm-validation" type="button" class="rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-emerald-600/20 transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-200"><i class="bi bi-shield-check mr-1" aria-hidden="true"></i> Setujui Izin</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Data Dummy yang sudah disesuaikan dengan alur bisnis yang benar
            const journals = [
                { id: 1, time: '07:45', className: 'X MIPA 1', teacher: 'Drs. Budi Santoso', subject: 'Matematika Wajib', period: 'Jam 1–2', attendance: 'Hadir', validation: 'Selesai', reason: '' },
                { id: 2, time: '07:42', className: 'XI IPS 2', teacher: 'Dra. Siti Aminah, M.Pd', subject: 'Sejarah Indonesia', period: 'Jam 1–2', attendance: 'Hadir', validation: 'Menunggu Sekre', reason: '' },
                { id: 3, time: '07:30', className: 'XII MIPA 3', teacher: 'Agus Setiawan, S.Si', subject: 'Fisika', period: 'Jam 1–3', attendance: 'Menunggu Izin', validation: '-', reason: 'Sakit, surat dokter menyusul via WA.' },
                { id: 4, time: '07:28', className: 'X Bahasa', teacher: 'Rina Melati, S.Pd', subject: 'Bahasa Inggris', period: 'Jam 1–2', attendance: 'Hadir', validation: 'Menunggu Sekre', reason: '' },
                { id: 5, time: '07:25', className: 'XI MIPA 2', teacher: 'Ir. Wahyu Pratama', subject: 'Biologi', period: 'Jam 1–2', attendance: 'Disetujui Piket', validation: 'Selesai', reason: 'Dinas luar kota.' },
                { id: 6, time: '07:15', className: 'XII IPS 1', teacher: 'Maya Puspitasari, S.Pd', subject: 'Ekonomi', period: 'Jam 1–2', attendance: 'Menunggu Izin', validation: '-', reason: 'Izin kepentingan keluarga mendadak.' },
            ];

            const tableBody = document.getElementById('journal-table-body');
            const emptyState = document.getElementById('empty-state');
            const pendingSekreCount = document.getElementById('pending-sekre-count');
            const filterDescription = document.getElementById('filter-description');
            const resetFilter = document.getElementById('reset-filter');
            const modal = document.getElementById('leave-modal');
            const modalPanel = document.getElementById('modal-panel');
            const confirmButton = document.getElementById('confirm-validation');
            let activeFilter = 'all';
            let activeLeaveId = null;

            const escapeHtml = (value) => String(value).replace(/[&<>'"]/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;' }[c]));

            // Kolom Status Kehadiran (Di sini piket bekerja)
            function attendanceCell(journal) {
                if (journal.attendance === 'Menunggu Izin') {
                    // Ini tombol Aksi untuk Piket
                    return `<button type="button" data-validate-id="${journal.id}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-3 py-2 text-xs font-bold text-white shadow-sm shadow-blue-600/20 transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-100"><i class="bi bi-shield-exclamation" aria-hidden="true"></i> Validasi Izin</button>`;
                } 
                
                // Jika Hadir atau Izin sudah disetujui, tampilkan label biasa
                const isHadir = journal.attendance === 'Hadir';
                const bg = isHadir ? 'bg-emerald-50 text-emerald-700 ring-emerald-100' : 'bg-red-50 text-red-700 ring-red-100';
                const dot = isHadir ? 'bg-emerald-500' : 'bg-red-500';
                
                return `<span class="inline-flex items-center gap-1.5 rounded-full ${bg} px-3 py-1 text-xs font-bold ring-1"><span class="h-1.5 w-1.5 rounded-full ${dot}"></span>${escapeHtml(journal.attendance)}</span>`;
            }

            // Kolom Validasi Sekretaris (Piket Cuma Mantau, Gak Bisa Diklik)
            function validationCell(journal) {
                if (journal.validation === 'Selesai') {
                    return '<span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1.5 text-[11px] font-bold text-emerald-700"><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Selesai</span>';
                } else if (journal.validation === 'Menunggu Sekre') {
                    return '<span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1.5 text-[11px] font-bold text-amber-700"><i class="bi bi-clock-fill" aria-hidden="true"></i> Menunggu Sekre</span>';
                }
                return '<span class="text-xs font-medium text-slate-300">-</span>'; // Jika guru belum dikonfirmasi piket, sekre belum bisa validasi jurnal
            }

            // Logika Penyortiran
            function currentJournals() {
                if (activeFilter === 'hadir') return journals.filter(j => j.attendance === 'Hadir');
                if (activeFilter === 'absen') return journals.filter(j => j.attendance !== 'Hadir');
                if (activeFilter === 'menunggu_sekre') return journals.filter(j => j.validation === 'Menunggu Sekre');
                return journals;
            }

            function renderTable() {
                const data = currentJournals();
                tableBody.innerHTML = data.map((journal, index) => `
                    <tr class="${index % 2 === 0 ? 'bg-white' : 'bg-slate-50/60'} transition hover:bg-emerald-50/50">
                        <td class="whitespace-nowrap px-6 py-4 font-mono text-xs font-semibold text-slate-500">${escapeHtml(journal.time)}</td>
                        <td class="whitespace-nowrap px-6 py-4 font-bold text-slate-800">${escapeHtml(journal.className)}</td>
                        <td class="px-6 py-4"><p class="font-bold text-slate-800">${escapeHtml(journal.teacher)}</p><p class="mt-1 text-xs text-slate-500">${escapeHtml(journal.subject)} <span class="text-slate-300">•</span> ${escapeHtml(journal.period)}</p></td>
                        <td class="px-6 py-4">${attendanceCell(journal)}</td>
                        <td class="px-6 py-4">${validationCell(journal)}</td>
                    </tr>
                `).join('');

                emptyState.classList.toggle('hidden', data.length !== 0);
                pendingSekreCount.textContent = journals.filter(j => j.validation === 'Menunggu Sekre').length;
                
                resetFilter.classList.toggle('hidden', activeFilter === 'all');
                resetFilter.classList.toggle('inline-flex', activeFilter !== 'all');

                document.querySelectorAll('[data-filter]').forEach(btn => {
                    const isActive = btn.dataset.filter === activeFilter;
                    btn.classList.toggle('ring-4', isActive);
                    btn.classList.toggle('border-emerald-300', isActive && btn.dataset.filter === 'all');
                });
            }

            // Fungsi Modal Izin
            function openModal(id) {
                const journal = journals.find(j => j.id === id);
                if (!journal) return;
                activeLeaveId = id;
                document.getElementById('modal-teacher').textContent = journal.teacher;
                document.getElementById('modal-time').textContent = `${journal.time} WIB • ${journal.period}`;
                document.getElementById('modal-class').textContent = journal.className;
                document.getElementById('modal-reason').textContent = journal.reason;
                
                modal.classList.remove('hidden');
                setTimeout(() => {
                    modal.classList.remove('opacity-0');
                    modalPanel.classList.remove('translate-y-4', 'sm:scale-95');
                }, 10);
            }

            function closeModal() {
                modal.classList.add('opacity-0');
                modalPanel.classList.add('translate-y-4', 'sm:scale-95');
                setTimeout(() => modal.classList.add('hidden'), 200);
            }

            // Event Listeners
            document.getElementById('piket-dashboard').addEventListener('click', (e) => {
                const filterBtn = e.target.closest('[data-filter]');
                if (filterBtn) { activeFilter = filterBtn.dataset.filter; renderTable(); }
                
                const validateBtn = e.target.closest('[data-validate-id]');
                if (validateBtn) openModal(Number(validateBtn.dataset.validateId));
            });

            document.getElementById('close-modal').addEventListener('click', closeModal);
            document.getElementById('cancel-modal').addEventListener('click', closeModal);
            resetFilter.addEventListener('click', () => { activeFilter = 'all'; renderTable(); });
            
            // Ketika Piket Setuju Izin
            confirmButton.addEventListener('click', () => {
                const journal = journals.find(j => j.id === activeLeaveId);
                if (journal) {
                    journal.attendance = 'Disetujui Piket';
                    // Optional: Jurnal bisa otomatis dianggap 'Selesai' atau diteruskan ke sekre
                    journal.validation = 'Selesai'; 
                }
                closeModal();
                renderTable();
            });

            // Jam Realtime
            setInterval(() => {
                document.getElementById('realtime-clock').textContent = new Intl.DateTimeFormat('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }).format(new Date()).replace(/\./g, ':') + ' WIB';
            }, 1000);

            renderTable();
        });
    </script>
@endsection