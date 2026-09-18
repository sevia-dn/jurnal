@extends('layouts.app')

@section('sidebar', view('layouts.pengurus-kelas.sidebar'))
@section('navbar', view('layouts.pengurus-kelas.navbar'))

@section('content')
<div class="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
    <div class="mb-6">
        <p class="text-sm font-semibold text-emerald-700">Dashboard Pengurus Kelas</p>
        <h1 class="mt-1 text-2xl font-extrabold leading-tight text-slate-900 sm:text-3xl">Selamat datang, Pengurus</h1>
        <p class="mt-2 text-sm text-slate-500">Kamis, 24 Agustus 2023</p>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <a href="{{ route('pengurus-kelas.jadwal') }}"
            class="group flex min-h-44 justify-between rounded-2xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-emerald-400 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:ring-offset-2">
            <div>
                <h2 class="text-sm font-bold text-slate-800 transition group-hover:text-emerald-700">Kelas Hari Ini</h2>
                <p class="mt-6 text-4xl font-extrabold text-emerald-700">10</p>
                <p class="mt-1 flex items-center gap-1 text-xs font-medium text-slate-500">Total sesi kelas <i class="bi bi-arrow-right" aria-hidden="true"></i></p>
            </div>
            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-200 text-lg text-emerald-700"><i class="bi bi-calendar-week-fill" aria-hidden="true"></i></span>
        </a>

        <a href="{{ route('pengurus-kelas.jurnal-detail') }}"
            class="group flex min-h-44 justify-between rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-amber-400 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2">
            <div>
                <h2 class="text-sm font-bold text-slate-800 transition group-hover:text-amber-700">Perlu Persetujuan</h2>
                <p class="mt-6 text-4xl font-extrabold text-amber-600">3</p>
                <p class="mt-1 flex items-center gap-1 text-xs font-medium text-slate-500">Jurnal menunggu validasi <i class="bi bi-arrow-right" aria-hidden="true"></i></p>
            </div>
            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-200 text-lg text-amber-700"><i class="bi bi-clipboard-check" aria-hidden="true"></i></span>
        </a>

        <a href="{{ route('pengurus-kelas.kehadiran-guru') }}"
            class="group flex min-h-44 justify-between rounded-2xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-emerald-400 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:ring-offset-2">
            <div>
                <h2 class="text-sm font-bold text-slate-800 transition group-hover:text-emerald-700">Kehadiran Guru</h2>
                <p class="mt-6 text-4xl font-extrabold text-emerald-700">3/4</p>
                <p class="mt-1 flex items-center gap-1 text-xs font-medium text-slate-500">Guru telah tercatat <i class="bi bi-arrow-right" aria-hidden="true"></i></p>
            </div>
            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-200 text-lg text-emerald-700"><i class="bi bi-person-badge-fill" aria-hidden="true"></i></span>
        </a>

        <a href="{{ route('pengurus-kelas.kehadiran-siswa') }}"
            class="group flex min-h-44 justify-between rounded-2xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-emerald-400 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:ring-offset-2">
            <div>
                <h2 class="text-sm font-bold text-slate-800 transition group-hover:text-emerald-700">Kehadiran Siswa</h2>
                <p class="mt-6 text-4xl font-extrabold text-emerald-700">30/36</p>
                <p class="mt-1 flex items-center gap-1 text-xs font-medium text-slate-500">Siswa telah diabsen <i class="bi bi-arrow-right" aria-hidden="true"></i></p>
            </div>
            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-200 text-lg text-emerald-700"><i class="bi bi-people-fill" aria-hidden="true"></i></span>
        </a>
    </div>
</div>
@endsection
