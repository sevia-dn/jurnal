@extends('layouts.app')

@section('sidebar', view('layouts.pengurus-kelas.sidebar'))
@section('navbar', view('layouts.pengurus-kelas.navbar'))

@section('content')
<div class="mx-auto w-full max-w-4xl px-4 py-6 sm:px-6 lg:px-8">

    <header class="mb-6">
        <p class="text-sm font-semibold text-emerald-700">Kamis, 24 Agustus 2023</p>
        <h1 class="mt-1 text-2xl font-extrabold text-slate-900 sm:text-3xl">Status Kehadiran Guru</h1>
        <p class="mt-2 text-sm text-slate-500">Pantau kehadiran guru yang mengajar hari ini.</p>
    </header>

    <div class="grid gap-4 sm:grid-cols-2" aria-label="Daftar kehadiran guru">
        <article class="flex items-center gap-4 rounded-2xl border border-emerald-200 bg-white p-4 shadow-sm">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-emerald-100 font-extrabold text-emerald-700" aria-label="Inisial SR">SR</span>
            <div class="min-w-0 flex-1">
                <h2 class="truncate text-sm font-extrabold text-slate-900">Ibu Siti Rahma, S.Pd.</h2>
                <p class="mt-1 truncate text-sm text-slate-500">Matematika</p>
            </div>
            <span class="shrink-0 rounded-full bg-emerald-600 px-3 py-1.5 text-xs font-bold text-white">Hadir</span>
        </article>

        <article class="flex items-center gap-4 rounded-2xl border border-emerald-200 bg-white p-4 shadow-sm">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-emerald-100 font-extrabold text-emerald-700" aria-label="Inisial AP">AP</span>
            <div class="min-w-0 flex-1">
                <h2 class="truncate text-sm font-extrabold text-slate-900">Bapak Arif Pratama, S.Pd.</h2>
                <p class="mt-1 truncate text-sm text-slate-500">Bahasa Indonesia</p>
            </div>
            <span class="shrink-0 rounded-full bg-emerald-600 px-3 py-1.5 text-xs font-bold text-white">Hadir</span>
        </article>

        <article class="flex items-center gap-4 rounded-2xl border border-red-200 bg-white p-4 shadow-sm">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-red-100 font-extrabold text-red-700" aria-label="Inisial NP">NP</span>
            <div class="min-w-0 flex-1">
                <h2 class="truncate text-sm font-extrabold text-slate-900">Ibu Nanda Putri, S.Pd.</h2>
                <p class="mt-1 truncate text-sm text-slate-500">Bahasa Inggris</p>
            </div>
            <span class="shrink-0 rounded-full bg-red-100 px-3 py-1.5 text-xs font-bold text-red-700">Sakit</span>
        </article>

        <article class="flex items-center gap-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-slate-100 font-extrabold text-slate-600" aria-label="Inisial DS">DS</span>
            <div class="min-w-0 flex-1">
                <h2 class="truncate text-sm font-extrabold text-slate-900">Bapak Dimas Saputra, S.Kom.</h2>
                <p class="mt-1 truncate text-sm text-slate-500">Informatika</p>
            </div>
            <span class="shrink-0 rounded-full bg-slate-100 px-3 py-1.5 text-xs font-bold text-slate-600">Belum Masuk</span>
        </article>
    </div>
</div>
@endsection
