@extends('layouts.app')

@section('sidebar', view('layouts.pengurus-kelas.sidebar'))
@section('navbar', view('layouts.pengurus-kelas.navbar'))
@section('content')
<div class="mx-auto w-full max-w-4xl px-4 py-6 sm:px-6 lg:px-8">

    <header class="mb-8">
        <p class="text-sm font-semibold text-emerald-700">Kamis, 24 Agustus 2023</p>
        <h1 class="mt-1 text-2xl font-extrabold text-slate-900 sm:text-3xl">Jadwal Hari Ini</h1>
        <p class="mt-2 text-sm text-slate-500">4 mata pelajaran · 10 sesi pembelajaran</p>
    </header>

    <ol class="relative space-y-5 border-l-2 border-emerald-200 pl-6 sm:pl-8" aria-label="Jadwal pelajaran hari ini">
        <li class="relative">
            <span class="absolute -left-[35px] top-6 flex h-4 w-4 rounded-full border-4 border-emerald-50 bg-emerald-600 sm:-left-[43px]" aria-hidden="true"></span>
            <article class="rounded-2xl border border-emerald-200 bg-white p-4 shadow-sm sm:p-5">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <p class="text-sm font-bold text-emerald-700">07:00 - 08:30 <span class="font-medium text-slate-500">· Sesi 1-2</span></p>
                        <h2 class="mt-2 text-lg font-extrabold text-slate-900">Matematika</h2>
                        <p class="mt-1 text-sm text-slate-500">Ibu Siti Rahma, S.Pd.</p>
                        <p class="mt-3 inline-flex items-center gap-2 text-sm font-medium text-slate-500"><i class="bi bi-geo-alt" aria-hidden="true"></i> Ruang 101</p>
                    </div>
                    <span class="inline-flex w-fit items-center gap-1.5 rounded-full bg-emerald-600 px-3 py-1.5 text-xs font-bold text-white">
                        <i class="bi bi-check-circle-fill" aria-hidden="true"></i> Guru Hadir
                    </span>
                </div>
            </article>
        </li>

        <li class="relative">
            <span class="absolute -left-[35px] top-6 flex h-4 w-4 rounded-full border-4 border-emerald-50 bg-emerald-600 sm:-left-[43px]" aria-hidden="true"></span>
            <article class="rounded-2xl border border-emerald-200 bg-white p-4 shadow-sm sm:p-5">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <p class="text-sm font-bold text-emerald-700">08:30 - 10:00 <span class="font-medium text-slate-500">· Sesi 3-4</span></p>
                        <h2 class="mt-2 text-lg font-extrabold text-slate-900">Bahasa Indonesia</h2>
                        <p class="mt-1 text-sm text-slate-500">Bapak Arif Pratama, S.Pd.</p>
                        <p class="mt-3 inline-flex items-center gap-2 text-sm font-medium text-slate-500"><i class="bi bi-geo-alt" aria-hidden="true"></i> Ruang 101</p>
                    </div>
                    <span class="inline-flex w-fit items-center gap-1.5 rounded-full bg-emerald-600 px-3 py-1.5 text-xs font-bold text-white">
                        <i class="bi bi-check-circle-fill" aria-hidden="true"></i> Guru Hadir
                    </span>
                </div>
            </article>
        </li>

        <li class="relative">
            <span class="absolute -left-[35px] top-6 flex h-4 w-4 rounded-full border-4 border-red-50 bg-red-500 sm:-left-[43px]" aria-hidden="true"></span>
            <article class="rounded-2xl border border-red-200 bg-white p-4 shadow-sm sm:p-5">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <p class="text-sm font-bold text-emerald-700">10:30 - 12:45 <span class="font-medium text-slate-500">· Sesi 5-7</span></p>
                        <h2 class="mt-2 text-lg font-extrabold text-slate-900">Bahasa Inggris</h2>
                        <p class="mt-1 text-sm text-slate-500">Ibu Nanda Putri, S.Pd.</p>
                        <p class="mt-3 inline-flex items-center gap-2 text-sm font-medium text-slate-500"><i class="bi bi-geo-alt" aria-hidden="true"></i> Ruang 102</p>
                    </div>
                    <span class="inline-flex w-fit items-center gap-1.5 rounded-full border border-red-300 bg-white px-3 py-1.5 text-xs font-bold text-red-700">
                        <i class="bi bi-x-circle" aria-hidden="true"></i> Absen
                    </span>
                </div>
            </article>
        </li>

        <li class="relative">
            <span class="absolute -left-[35px] top-6 flex h-4 w-4 rounded-full border-4 border-emerald-50 bg-emerald-600 sm:-left-[43px]" aria-hidden="true"></span>
            <article class="rounded-2xl border border-emerald-200 bg-white p-4 shadow-sm sm:p-5">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <p class="text-sm font-bold text-emerald-700">13:00 - 15:00 <span class="font-medium text-slate-500">· Sesi 8-10</span></p>
                        <h2 class="mt-2 text-lg font-extrabold text-slate-900">Informatika</h2>
                        <p class="mt-1 text-sm text-slate-500">Bapak Dimas Saputra, S.Kom.</p>
                        <p class="mt-3 inline-flex items-center gap-2 text-sm font-medium text-slate-500"><i class="bi bi-geo-alt" aria-hidden="true"></i> Laboratorium Komputer</p>
                    </div>
                    <span class="inline-flex w-fit items-center gap-1.5 rounded-full bg-emerald-600 px-3 py-1.5 text-xs font-bold text-white">
                        <i class="bi bi-check-circle-fill" aria-hidden="true"></i> Guru Hadir
                    </span>
                </div>
            </article>
        </li>
    </ol>
</div>
@endsection
