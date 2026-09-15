@extends('layouts.app')

@section('title', 'Kehadiran Guru')

@section('sidebar')
    @include('layouts.piket.sidebar')
@endsection

@section('navbar')
    @include('layouts.piket.navbar')
@endsection

@section('content')
    <div id="attendance-page" class="min-h-full bg-slate-50 p-5 pb-24 sm:p-8 lg:p-10">
        <div class="mx-auto max-w-7xl">
            <header class="mb-7 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div><p class="mb-2 text-xs font-bold uppercase tracking-[0.16em] text-emerald-700">Monitoring presensi</p><h1 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Rekap Kehadiran Guru</h1><p class="mt-2 text-sm text-slate-500">Lihat ringkasan kehadiran guru berdasarkan tanggal pilihan.</p></div>
                <div class="flex flex-col gap-3 sm:flex-row">
                    <label class="relative"><span class="sr-only">Pilih tanggal</span><i class="bi bi-calendar3 pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-emerald-700" aria-hidden="true"></i><input id="attendance-date" type="date" class="w-full rounded-xl border border-slate-300 bg-white py-2.5 pl-10 pr-3 text-sm font-semibold text-slate-700 shadow-sm outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100"></label>
                    <button id="export-data" type="button" class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-700 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-800 focus:outline-none focus:ring-4 focus:ring-emerald-200"><i class="bi bi-download" aria-hidden="true"></i> Export Data</button>
                </div>
            </header>

            <section class="mb-6 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5" aria-label="Pencarian dan filter kehadiran">
                <div class="flex flex-col gap-3 md:flex-row">
                    <label class="relative flex-1"><span class="sr-only">Cari nama atau NIP</span><i class="bi bi-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" aria-hidden="true"></i><input id="teacher-search" type="search" placeholder="Cari nama atau NIP guru..." class="w-full rounded-xl border border-slate-300 bg-white py-3 pl-10 pr-4 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100"></label>
                    <label class="relative md:w-52"><span class="sr-only">Filter status</span><select id="attendance-filter" class="w-full appearance-none rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-slate-700 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100"><option value="Semua">Semua Status</option><option value="Hadir">Hadir</option><option value="Izin">Izin</option><option value="Sakit">Sakit</option><option value="Alfa">Alfa</option></select><i class="bi bi-chevron-down pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-slate-400" aria-hidden="true"></i></label>
                </div>
            </section>

            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm" aria-labelledby="attendance-table-title">
                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-5 sm:px-6"><div><h2 id="attendance-table-title" class="font-bold text-slate-800">Daftar Kehadiran</h2><p id="attendance-summary" class="mt-1 text-sm text-slate-500" aria-live="polite"></p></div><span class="hidden rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-700 sm:inline-flex">Hari ini</span></div>
                <div class="overflow-x-auto">
                    <table class="min-w-[780px] w-full text-left">
                        <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500"><tr><th class="px-6 py-4">Nama</th><th class="px-6 py-4">NIP</th><th class="px-6 py-4">Check-In</th><th class="px-6 py-4">Status</th><th class="px-6 py-4 text-right">Aksi</th></tr></thead>
                        <tbody id="attendance-table" class="divide-y divide-slate-100 text-sm"></tbody>
                    </table>
                </div>
                <div id="attendance-empty" class="hidden px-6 py-14 text-center"><i class="bi bi-search text-3xl text-slate-300" aria-hidden="true"></i><p class="mt-3 font-semibold text-slate-700">Data guru tidak ditemukan.</p><button id="clear-search" type="button" class="mt-2 text-sm font-bold text-emerald-700 hover:text-emerald-800">Reset pencarian</button></div>
            </section>
        </div>

        <div id="attendance-modal" class="fixed inset-0 z-[60] hidden items-end bg-slate-950/60 p-4 opacity-0 backdrop-blur-sm transition-opacity duration-200 sm:items-center sm:justify-center" role="dialog" aria-modal="true" aria-labelledby="attendance-modal-title" aria-hidden="true">
            <div id="attendance-modal-panel" class="w-full max-w-md translate-y-4 rounded-2xl bg-white shadow-2xl transition duration-200 sm:translate-y-0 sm:scale-95">
                <div class="flex items-start justify-between border-b border-slate-100 px-5 py-5"><div><p class="text-xs font-bold uppercase tracking-wider text-emerald-700">Detail presensi</p><h2 id="attendance-modal-title" class="mt-1 text-xl font-extrabold text-slate-900">Detail Kehadiran</h2></div><button type="button" data-close-detail class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100" aria-label="Tutup"><i class="bi bi-x-lg" aria-hidden="true"></i></button></div>
                <dl class="space-y-4 px-5 py-6"><div class="rounded-xl bg-slate-50 p-4"><dt class="text-xs font-semibold text-slate-400">Nama Guru</dt><dd id="detail-name" class="mt-1 font-bold text-slate-800"></dd></div><div class="grid grid-cols-2 gap-4"><div class="rounded-xl bg-slate-50 p-4"><dt class="text-xs font-semibold text-slate-400">NIP</dt><dd id="detail-nip" class="mt-1 font-bold text-slate-800"></dd></div><div class="rounded-xl bg-slate-50 p-4"><dt class="text-xs font-semibold text-slate-400">Check-In</dt><dd id="detail-time" class="mt-1 font-bold text-slate-800"></dd></div></div><div class="rounded-xl bg-slate-50 p-4"><dt class="text-xs font-semibold text-slate-400">Status</dt><dd id="detail-status" class="mt-1 font-bold text-slate-800"></dd></div></dl>
                <div class="border-t border-slate-100 bg-slate-50 px-5 py-4 text-right"><button type="button" data-close-detail class="rounded-xl bg-emerald-700 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-emerald-800">Tutup</button></div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
    const teachers = @json($kehadirans->map(fn($k) => [
        'id' => $k->id,
        'name' => $k->guru->name,
        'nip' => $k->guru->nip ?? '-',
        'checkIn' => $k->jam_masuk ?? '—',
        'status' => $k->status,
        'verified' => $k->diverifikasi_at !== null,
    ]));
    // ... sisa kode JS tetap sama
            const search = document.getElementById('teacher-search');
            const filter = document.getElementById('attendance-filter');
            const table = document.getElementById('attendance-table');
            const empty = document.getElementById('attendance-empty');
            const modal = document.getElementById('attendance-modal');
            const panel = document.getElementById('attendance-modal-panel');
            let activeId = null;
            let trigger = null;

            const badge = (status) => ({ Hadir: 'bg-emerald-50 text-emerald-700', Izin: 'bg-amber-50 text-amber-700', Sakit: 'bg-amber-50 text-amber-700', Alfa: 'bg-red-50 text-red-700' }[status]);
            function visibleTeachers() {
                const query = search.value.trim().toLowerCase();
                return teachers.filter((teacher) => (filter.value === 'Semua' || teacher.status === filter.value) && (!query || teacher.name.toLowerCase().includes(query) || teacher.nip.toLowerCase().includes(query)));
            }
            function render() {
                const items = visibleTeachers();
                table.innerHTML = items.map((teacher, index) => `<tr class="${index % 2 ? 'bg-slate-50/60' : 'bg-white'} transition hover:bg-emerald-50/50"><td class="px-6 py-4 font-bold text-slate-800">${teacher.name}</td><td class="px-6 py-4 font-mono text-xs text-slate-500">${teacher.nip}</td><td class="px-6 py-4 font-semibold text-slate-600">${teacher.checkIn}</td><td class="px-6 py-4"><span class="rounded-full px-3 py-1 text-xs font-bold ${badge(teacher.status)}">${teacher.status}</span></td><td class="px-6 py-4 text-right"><button type="button" data-detail-id="${teacher.id}" class="rounded-lg border border-emerald-200 px-3 py-2 text-xs font-bold text-emerald-700 transition hover:bg-emerald-50 focus:outline-none focus:ring-4 focus:ring-emerald-100">Detail</button></td></tr>`).join('');
                empty.classList.toggle('hidden', items.length !== 0);
                document.getElementById('attendance-summary').textContent = `Menampilkan ${items.length} dari ${teachers.length} guru.`;
            }
            function closeModal() { modal.classList.add('opacity-0'); panel.classList.add('translate-y-4', 'sm:scale-95'); modal.setAttribute('aria-hidden', 'true'); document.body.classList.remove('overflow-hidden'); window.setTimeout(() => modal.classList.add('hidden'), 200); trigger?.focus(); }
            table.addEventListener('click', (event) => {
                const button = event.target.closest('[data-detail-id]'); if (!button) return;
                const teacher = teachers.find((item) => item.id === Number(button.dataset.detailId)); if (!teacher) return;
                activeId = teacher.id; trigger = button;
                document.getElementById('detail-name').textContent = teacher.name; document.getElementById('detail-nip').textContent = teacher.nip; document.getElementById('detail-time').textContent = teacher.checkIn; document.getElementById('detail-status').textContent = teacher.status;
                modal.classList.remove('hidden'); modal.setAttribute('aria-hidden', 'false'); document.body.classList.add('overflow-hidden'); requestAnimationFrame(() => { modal.classList.remove('opacity-0'); panel.classList.remove('translate-y-4', 'sm:scale-95'); });
            });
            [search, filter].forEach((control) => control.addEventListener(control === search ? 'input' : 'change', render));
            document.getElementById('clear-search').addEventListener('click', () => { search.value = ''; filter.value = 'Semua'; render(); });
            document.querySelectorAll('[data-close-detail]').forEach((button) => button.addEventListener('click', closeModal));
            modal.addEventListener('click', (event) => { if (event.target === modal) closeModal(); });
            document.addEventListener('keydown', (event) => { if (event.key === 'Escape' && !modal.classList.contains('hidden')) closeModal(); });
            document.getElementById('export-data').addEventListener('click', () => alert('Mockup frontend: data siap diekspor setelah integrasi backend.'));
            document.getElementById('attendance-date').value = new Date().toISOString().slice(0, 10);
            render();
        });
    </script>
@endsection
