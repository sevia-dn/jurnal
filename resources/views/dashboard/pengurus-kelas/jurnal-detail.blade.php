@extends('layouts.app')

@section('sidebar', view('layouts.pengurus-kelas.sidebar'))
@section('navbar', view('layouts.pengurus-kelas.navbar'))
@section('content')
<div class="mx-auto w-full max-w-3xl px-4 py-6 pb-10 sm:px-6 lg:px-8">

    <header class="rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm sm:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-emerald-700 text-lg font-extrabold text-white" aria-label="Inisial guru SR">SR</span>
                <div>
                    <p class="text-sm font-semibold text-emerald-700">Logbook Guru</p>
                    <h1 class="text-xl font-extrabold text-slate-900">Ibu Siti Rahma, S.Pd.</h1>
                    <p class="mt-1 text-sm text-slate-500">Matematika</p>
                </div>
            </div>
            <span class="inline-flex w-fit items-center gap-1.5 rounded-full bg-amber-100 px-3 py-1.5 text-xs font-bold text-amber-700"><i class="bi bi-hourglass-split" aria-hidden="true"></i> Menunggu Persetujuan</span>
        </div>
    </header>

    <div class="mt-5 space-y-4">

        {{-- (a) Tanggal & Jam Pelajaran --}}
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-base font-extrabold text-slate-900">Jadwal & Waktu</h2>
            <dl class="mt-4 grid gap-4 sm:grid-cols-3">
                <div class="rounded-xl bg-slate-50 p-4">
                    <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Tanggal</dt>
                    <dd class="mt-1 text-sm font-bold text-slate-800">24 Agustus 2023</dd>
                </div>
                <div class="rounded-xl bg-slate-50 p-4">
                    <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Waktu</dt>
                    <dd class="mt-1 text-sm font-bold text-slate-800">07:00 - 08:30</dd>
                </div>
                <div class="rounded-xl bg-slate-50 p-4">
                    <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Jam Pelajaran</dt>
                    <dd class="mt-1 text-sm font-bold text-slate-800">Jam ke 1-2</dd>
                </div>
            </dl>
        </section>

        {{-- (b) Mata Pelajaran & Kelas --}}
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-base font-extrabold text-slate-900">Mata Pelajaran & Kelas</h2>
            <dl class="mt-4 grid gap-4 sm:grid-cols-2">
                <div class="rounded-xl bg-emerald-50 p-4">
                    <dt class="text-xs font-bold uppercase tracking-wide text-emerald-700">Mata Pelajaran</dt>
                    <dd class="mt-1 text-sm font-bold text-slate-900">Matematika</dd>
                </div>
                <div class="rounded-xl bg-emerald-50 p-4">
                    <dt class="text-xs font-bold uppercase tracking-wide text-emerald-700">Kelas</dt>
                    <dd class="mt-1 text-sm font-bold text-slate-900">XI MIPA 2</dd>
                </div>
            </dl>
        </section>

        {{-- (c) Ringkasan Materi --}}
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-base font-extrabold text-slate-900">Ringkasan Materi</h2>
            <div class="mt-4 rounded-xl bg-slate-50 p-4">
                <ol class="space-y-3 border-l-2 border-emerald-200 pl-5">
                    <li class="relative text-sm leading-6 text-slate-600">
                        <span class="absolute -left-[25px] top-2 h-2.5 w-2.5 rounded-full bg-emerald-600" aria-hidden="true"></span>
                        <strong class="text-slate-800">Pendahuluan teori:</strong> mengulas konsep dasar persamaan kuadrat dan tujuan pembelajaran.
                    </li>
                    <li class="relative text-sm leading-6 text-slate-600">
                        <span class="absolute -left-[25px] top-2 h-2.5 w-2.5 rounded-full bg-emerald-600" aria-hidden="true"></span>
                        <strong class="text-slate-800">Latihan terbimbing:</strong> siswa menyelesaikan contoh soal bersama guru.
                    </li>
                    <li class="relative text-sm leading-6 text-slate-600">
                        <span class="absolute -left-[25px] top-2 h-2.5 w-2.5 rounded-full bg-emerald-600" aria-hidden="true"></span>
                        <strong class="text-slate-800">Refleksi:</strong> membahas jawaban dan memberikan tugas latihan mandiri.
                    </li>
                </ol>
            </div>
        </section>

        {{-- (d) Catatan Khusus / Hambatan di Kelas — DITAMBAHKAN, background amber sesuai standar --}}
        <section class="rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm">
            <div class="flex items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill text-amber-600" aria-hidden="true"></i>
                <h2 class="text-base font-extrabold text-amber-900">Catatan Khusus / Hambatan di Kelas</h2>
            </div>
            <div class="mt-4 rounded-xl border border-amber-100 bg-white p-4">
                <p class="text-sm leading-relaxed text-amber-800">
                    Beberapa siswa masih mengalami kesulitan dalam memahami diskriminan persamaan kuadrat.
                    Perlu diadakan review singkat pada pertemuan berikutnya. Proyektor sempat tidak berfungsi
                    selama 10 menit di awal pelajaran.
                </p>
            </div>
        </section>

        {{-- (e) Rincian Kehadiran Siswa 1-per-1 — DITAMBAHKAN --}}
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-base font-extrabold text-slate-900">Rincian Kehadiran Siswa</h2>
                <span class="w-fit rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">XI MIPA 2 · 36 Siswa</span>
            </div>
            <div class="mt-3 flex flex-wrap gap-2 text-xs font-semibold">
                <span class="rounded-full bg-emerald-100 px-3 py-1 text-emerald-700">33 Hadir</span>
                <span class="rounded-full bg-amber-100 px-3 py-1 text-amber-700">1 Sakit</span>
                <span class="rounded-full bg-sky-100 px-3 py-1 text-sky-700">1 Izin</span>
                <span class="rounded-full bg-rose-100 px-3 py-1 text-rose-700">1 Alpa</span>
            </div>
            <div class="mt-4 max-h-64 overflow-y-auto rounded-xl border border-slate-100">
                <div class="divide-y divide-slate-100">
                    <div class="flex items-center justify-between gap-3 px-4 py-3">
                        <div class="flex items-center gap-3">
                            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-xs font-bold text-emerald-700">01</span>
                            <span class="text-sm font-medium text-slate-700">Ahmad Fauzan</span>
                        </div>
                        <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-[10px] font-bold text-emerald-700">Hadir</span>
                    </div>
                    <div class="flex items-center justify-between gap-3 px-4 py-3">
                        <div class="flex items-center gap-3">
                            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-xs font-bold text-emerald-700">02</span>
                            <span class="text-sm font-medium text-slate-700">Aisyah Putri Ramadhani</span>
                        </div>
                        <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-[10px] font-bold text-emerald-700">Hadir</span>
                    </div>
                    <div class="flex items-center justify-between gap-3 px-4 py-3">
                        <div class="flex items-center gap-3">
                            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-xs font-bold text-emerald-700">03</span>
                            <span class="text-sm font-medium text-slate-700">Alvin Maulana</span>
                        </div>
                        <span class="rounded-full bg-amber-100 px-2.5 py-1 text-[10px] font-bold text-amber-700">Sakit</span>
                    </div>
                    <div class="flex items-center justify-between gap-3 px-4 py-3">
                        <div class="flex items-center gap-3">
                            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-xs font-bold text-emerald-700">04</span>
                            <span class="text-sm font-medium text-slate-700">Amanda Safitri</span>
                        </div>
                        <span class="rounded-full bg-sky-100 px-2.5 py-1 text-[10px] font-bold text-sky-700">Izin</span>
                    </div>
                    <div class="flex items-center justify-between gap-3 px-4 py-3">
                        <div class="flex items-center gap-3">
                            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-xs font-bold text-emerald-700">05</span>
                            <span class="text-sm font-medium text-slate-700">Andika Pratama</span>
                        </div>
                        <span class="rounded-full bg-rose-100 px-2.5 py-1 text-[10px] font-bold text-rose-700">Alpa</span>
                    </div>
                    <div class="flex items-center gap-3 bg-slate-50/60 px-4 py-3">
                        <span class="text-xs italic font-medium text-slate-400">… dan 31 siswa lainnya (Hadir semua)</span>
                    </div>
                </div>
            </div>
        </section>

        {{-- (f) Dokumentasi / Bukti Foto --}}
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-base font-extrabold text-slate-900">Dokumentasi / Bukti Foto</h2>
            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                <div class="flex items-center gap-3 rounded-xl border border-dashed border-slate-300 bg-slate-50 p-4">
                    <i class="bi bi-file-earmark-pdf-fill text-2xl text-red-500" aria-hidden="true"></i>
                    <div>
                        <p class="text-sm font-bold text-slate-800">Modul Persamaan Kuadrat.pdf</p>
                        <p class="mt-1 text-xs text-slate-500">1,2 MB · PDF</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 rounded-xl border border-dashed border-slate-300 bg-slate-50 p-4">
                    <i class="bi bi-file-earmark-image-fill text-2xl text-emerald-600" aria-hidden="true"></i>
                    <div>
                        <p class="text-sm font-bold text-slate-800">Dokumentasi Kelas.jpg</p>
                        <p class="mt-1 text-xs text-slate-500">824 KB · JPG</p>
                    </div>
                </div>
            </div>
        </section>

    </div>

    {{-- Action Footer — Revisi kiri, Setujui kanan (standar UI) --}}
    <section class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="p-5">
            <label for="feedback" class="text-base font-extrabold text-slate-900">
                Umpan Balik / Catatan <span class="text-sm font-normal text-slate-500">(opsional)</span>
            </label>
            <textarea id="feedback" rows="3" placeholder="Tulis catatan untuk guru..."
                class="mt-3 w-full resize-none rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 placeholder:text-slate-400 focus:border-emerald-600 focus:outline-none focus:ring-4 focus:ring-emerald-100"></textarea>
        </div>
        <div class="flex flex-col-reverse gap-3 border-t border-slate-100 bg-slate-50 px-5 py-4 sm:flex-row sm:justify-end">
            <button type="button"
                class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-emerald-700 transition hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:ring-offset-2">
                <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i> Minta Revisi
            </button>
            <button type="button"
                class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:ring-offset-2">
                <i class="bi bi-check-lg text-lg" aria-hidden="true"></i> Setujui Logbook
            </button>
        </div>
    </section>

</div>
@endsection
