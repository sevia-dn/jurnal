@extends('layouts.app')

@section('sidebar', view('layouts.pengurus-kelas.sidebar'))
@section('navbar', view('layouts.pengurus-kelas.navbar'))

@section('content')
<div class="mx-auto w-full max-w-5xl px-4 py-6 pb-32 sm:px-6 lg:px-8">

    <header class="mb-6">
        <p class="text-sm font-semibold text-[#0d6e59]">Kamis, 24 Agustus 2023</p>
        <h1 class="mt-1 text-2xl font-extrabold text-[#0f3d32] sm:text-3xl">Absensi Siswa <span class="whitespace-nowrap">(36 Siswa)</span></h1>
        <p class="mt-2 text-sm text-[#5e7e75]">Pilih satu status kehadiran untuk setiap siswa.</p>
    </header>

    <div class="mb-4 flex flex-wrap items-center gap-x-4 gap-y-2 rounded-xl border border-[#d6ebe3] bg-[#F0FDF4] px-4 py-3 text-xs text-[#52756b]">
        <span class="font-bold text-[#0f3d32]">Keterangan:</span>
        <span>H · Hadir</span>
        <span>S · Sakit</span>
        <span>I · Izin</span>
        <span>D · Dispensasi</span>
        <span>A · Alfa</span>
    </div>

    <form>
        <ul class="divide-y divide-[#e3efeb] overflow-hidden rounded-2xl border border-[#d6ebe3] bg-white shadow-sm" aria-label="Daftar absensi siswa">
        <li class="flex items-center justify-between gap-3 px-4 py-3.5 sm:px-5">
            <div class="min-w-0">
                <p class="truncate text-sm font-bold text-[#0f3d32]">Ahmad Fauzan</p>
                <p class="mt-0.5 text-xs text-[#729188]">No. 01</p>
            </div>
            <fieldset class="flex shrink-0 items-center gap-1.5 sm:gap-2">
                <legend class="sr-only">Status kehadiran Ahmad Fauzan</legend>
                <input id="student-1-h" name="student_1_attendance" type="radio" value="H" class="peer sr-only" checked>
                <label for="student-1-h" title="Hadir" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">H<span class="sr-only">Hadir</span></label>
                <input id="student-1-s" name="student_1_attendance" type="radio" value="S" class="peer sr-only">
                <label for="student-1-s" title="Sakit" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">S<span class="sr-only">Sakit</span></label>
                <input id="student-1-i" name="student_1_attendance" type="radio" value="I" class="peer sr-only">
                <label for="student-1-i" title="Izin" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">I<span class="sr-only">Izin</span></label>
                <input id="student-1-d" name="student_1_attendance" type="radio" value="D" class="peer sr-only">
                <label for="student-1-d" title="Dispensasi" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">D<span class="sr-only">Dispensasi</span></label>
                <input id="student-1-a" name="student_1_attendance" type="radio" value="A" class="peer sr-only">
                <label for="student-1-a" title="Alfa" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">A<span class="sr-only">Alfa</span></label>
            </fieldset>
        </li>

        <li class="flex items-center justify-between gap-3 px-4 py-3.5 sm:px-5">
            <div class="min-w-0">
                <p class="truncate text-sm font-bold text-[#0f3d32]">Aisyah Putri Ramadhani</p>
                <p class="mt-0.5 text-xs text-[#729188]">No. 02</p>
            </div>
            <fieldset class="flex shrink-0 items-center gap-1.5 sm:gap-2">
                <legend class="sr-only">Status kehadiran Aisyah Putri Ramadhani</legend>
                <input id="student-2-h" name="student_2_attendance" type="radio" value="H" class="peer sr-only" checked>
                <label for="student-2-h" title="Hadir" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">H<span class="sr-only">Hadir</span></label>
                <input id="student-2-s" name="student_2_attendance" type="radio" value="S" class="peer sr-only">
                <label for="student-2-s" title="Sakit" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">S<span class="sr-only">Sakit</span></label>
                <input id="student-2-i" name="student_2_attendance" type="radio" value="I" class="peer sr-only">
                <label for="student-2-i" title="Izin" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">I<span class="sr-only">Izin</span></label>
                <input id="student-2-d" name="student_2_attendance" type="radio" value="D" class="peer sr-only">
                <label for="student-2-d" title="Dispensasi" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">D<span class="sr-only">Dispensasi</span></label>
                <input id="student-2-a" name="student_2_attendance" type="radio" value="A" class="peer sr-only">
                <label for="student-2-a" title="Alfa" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">A<span class="sr-only">Alfa</span></label>
            </fieldset>
        </li>

        <li class="flex items-center justify-between gap-3 px-4 py-3.5 sm:px-5">
            <div class="min-w-0">
                <p class="truncate text-sm font-bold text-[#0f3d32]">Alvin Maulana</p>
                <p class="mt-0.5 text-xs text-[#729188]">No. 03</p>
            </div>
            <fieldset class="flex shrink-0 items-center gap-1.5 sm:gap-2">
                <legend class="sr-only">Status kehadiran Alvin Maulana</legend>
                <input id="student-3-h" name="student_3_attendance" type="radio" value="H" class="peer sr-only" checked>
                <label for="student-3-h" title="Hadir" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">H<span class="sr-only">Hadir</span></label>
                <input id="student-3-s" name="student_3_attendance" type="radio" value="S" class="peer sr-only">
                <label for="student-3-s" title="Sakit" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">S<span class="sr-only">Sakit</span></label>
                <input id="student-3-i" name="student_3_attendance" type="radio" value="I" class="peer sr-only">
                <label for="student-3-i" title="Izin" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">I<span class="sr-only">Izin</span></label>
                <input id="student-3-d" name="student_3_attendance" type="radio" value="D" class="peer sr-only">
                <label for="student-3-d" title="Dispensasi" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">D<span class="sr-only">Dispensasi</span></label>
                <input id="student-3-a" name="student_3_attendance" type="radio" value="A" class="peer sr-only">
                <label for="student-3-a" title="Alfa" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">A<span class="sr-only">Alfa</span></label>
            </fieldset>
        </li>

        <li class="flex items-center justify-between gap-3 px-4 py-3.5 sm:px-5">
            <div class="min-w-0">
                <p class="truncate text-sm font-bold text-[#0f3d32]">Amanda Safitri</p>
                <p class="mt-0.5 text-xs text-[#729188]">No. 04</p>
            </div>
            <fieldset class="flex shrink-0 items-center gap-1.5 sm:gap-2">
                <legend class="sr-only">Status kehadiran Amanda Safitri</legend>
                <input id="student-4-h" name="student_4_attendance" type="radio" value="H" class="peer sr-only" checked>
                <label for="student-4-h" title="Hadir" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">H<span class="sr-only">Hadir</span></label>
                <input id="student-4-s" name="student_4_attendance" type="radio" value="S" class="peer sr-only">
                <label for="student-4-s" title="Sakit" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">S<span class="sr-only">Sakit</span></label>
                <input id="student-4-i" name="student_4_attendance" type="radio" value="I" class="peer sr-only">
                <label for="student-4-i" title="Izin" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">I<span class="sr-only">Izin</span></label>
                <input id="student-4-d" name="student_4_attendance" type="radio" value="D" class="peer sr-only">
                <label for="student-4-d" title="Dispensasi" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">D<span class="sr-only">Dispensasi</span></label>
                <input id="student-4-a" name="student_4_attendance" type="radio" value="A" class="peer sr-only">
                <label for="student-4-a" title="Alfa" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">A<span class="sr-only">Alfa</span></label>
            </fieldset>
        </li>

        <li class="flex items-center justify-between gap-3 px-4 py-3.5 sm:px-5">
            <div class="min-w-0">
                <p class="truncate text-sm font-bold text-[#0f3d32]">Andika Pratama</p>
                <p class="mt-0.5 text-xs text-[#729188]">No. 05</p>
            </div>
            <fieldset class="flex shrink-0 items-center gap-1.5 sm:gap-2">
                <legend class="sr-only">Status kehadiran Andika Pratama</legend>
                <input id="student-5-h" name="student_5_attendance" type="radio" value="H" class="peer sr-only" checked>
                <label for="student-5-h" title="Hadir" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">H<span class="sr-only">Hadir</span></label>
                <input id="student-5-s" name="student_5_attendance" type="radio" value="S" class="peer sr-only">
                <label for="student-5-s" title="Sakit" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">S<span class="sr-only">Sakit</span></label>
                <input id="student-5-i" name="student_5_attendance" type="radio" value="I" class="peer sr-only">
                <label for="student-5-i" title="Izin" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">I<span class="sr-only">Izin</span></label>
                <input id="student-5-d" name="student_5_attendance" type="radio" value="D" class="peer sr-only">
                <label for="student-5-d" title="Dispensasi" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">D<span class="sr-only">Dispensasi</span></label>
                <input id="student-5-a" name="student_5_attendance" type="radio" value="A" class="peer sr-only">
                <label for="student-5-a" title="Alfa" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">A<span class="sr-only">Alfa</span></label>
            </fieldset>
        </li>

        <li class="flex items-center justify-between gap-3 px-4 py-3.5 sm:px-5">
            <div class="min-w-0">
                <p class="truncate text-sm font-bold text-[#0f3d32]">Annisa Nurhaliza</p>
                <p class="mt-0.5 text-xs text-[#729188]">No. 06</p>
            </div>
            <fieldset class="flex shrink-0 items-center gap-1.5 sm:gap-2">
                <legend class="sr-only">Status kehadiran Annisa Nurhaliza</legend>
                <input id="student-6-h" name="student_6_attendance" type="radio" value="H" class="peer sr-only" checked>
                <label for="student-6-h" title="Hadir" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">H<span class="sr-only">Hadir</span></label>
                <input id="student-6-s" name="student_6_attendance" type="radio" value="S" class="peer sr-only">
                <label for="student-6-s" title="Sakit" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">S<span class="sr-only">Sakit</span></label>
                <input id="student-6-i" name="student_6_attendance" type="radio" value="I" class="peer sr-only">
                <label for="student-6-i" title="Izin" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">I<span class="sr-only">Izin</span></label>
                <input id="student-6-d" name="student_6_attendance" type="radio" value="D" class="peer sr-only">
                <label for="student-6-d" title="Dispensasi" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">D<span class="sr-only">Dispensasi</span></label>
                <input id="student-6-a" name="student_6_attendance" type="radio" value="A" class="peer sr-only">
                <label for="student-6-a" title="Alfa" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">A<span class="sr-only">Alfa</span></label>
            </fieldset>
        </li>

        <li class="flex items-center justify-between gap-3 px-4 py-3.5 sm:px-5">
            <div class="min-w-0">
                <p class="truncate text-sm font-bold text-[#0f3d32]">Bagas Pradana</p>
                <p class="mt-0.5 text-xs text-[#729188]">No. 07</p>
            </div>
            <fieldset class="flex shrink-0 items-center gap-1.5 sm:gap-2">
                <legend class="sr-only">Status kehadiran Bagas Pradana</legend>
                <input id="student-7-h" name="student_7_attendance" type="radio" value="H" class="peer sr-only" checked>
                <label for="student-7-h" title="Hadir" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">H<span class="sr-only">Hadir</span></label>
                <input id="student-7-s" name="student_7_attendance" type="radio" value="S" class="peer sr-only">
                <label for="student-7-s" title="Sakit" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">S<span class="sr-only">Sakit</span></label>
                <input id="student-7-i" name="student_7_attendance" type="radio" value="I" class="peer sr-only">
                <label for="student-7-i" title="Izin" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">I<span class="sr-only">Izin</span></label>
                <input id="student-7-d" name="student_7_attendance" type="radio" value="D" class="peer sr-only">
                <label for="student-7-d" title="Dispensasi" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">D<span class="sr-only">Dispensasi</span></label>
                <input id="student-7-a" name="student_7_attendance" type="radio" value="A" class="peer sr-only">
                <label for="student-7-a" title="Alfa" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">A<span class="sr-only">Alfa</span></label>
            </fieldset>
        </li>

        <li class="flex items-center justify-between gap-3 px-4 py-3.5 sm:px-5">
            <div class="min-w-0">
                <p class="truncate text-sm font-bold text-[#0f3d32]">Citra Lestari</p>
                <p class="mt-0.5 text-xs text-[#729188]">No. 08</p>
            </div>
            <fieldset class="flex shrink-0 items-center gap-1.5 sm:gap-2">
                <legend class="sr-only">Status kehadiran Citra Lestari</legend>
                <input id="student-8-h" name="student_8_attendance" type="radio" value="H" class="peer sr-only" checked>
                <label for="student-8-h" title="Hadir" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">H<span class="sr-only">Hadir</span></label>
                <input id="student-8-s" name="student_8_attendance" type="radio" value="S" class="peer sr-only">
                <label for="student-8-s" title="Sakit" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">S<span class="sr-only">Sakit</span></label>
                <input id="student-8-i" name="student_8_attendance" type="radio" value="I" class="peer sr-only">
                <label for="student-8-i" title="Izin" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">I<span class="sr-only">Izin</span></label>
                <input id="student-8-d" name="student_8_attendance" type="radio" value="D" class="peer sr-only">
                <label for="student-8-d" title="Dispensasi" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">D<span class="sr-only">Dispensasi</span></label>
                <input id="student-8-a" name="student_8_attendance" type="radio" value="A" class="peer sr-only">
                <label for="student-8-a" title="Alfa" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">A<span class="sr-only">Alfa</span></label>
            </fieldset>
        </li>

        <li class="flex items-center justify-between gap-3 px-4 py-3.5 sm:px-5">
            <div class="min-w-0">
                <p class="truncate text-sm font-bold text-[#0f3d32]">Daffa Alfarizi</p>
                <p class="mt-0.5 text-xs text-[#729188]">No. 09</p>
            </div>
            <fieldset class="flex shrink-0 items-center gap-1.5 sm:gap-2">
                <legend class="sr-only">Status kehadiran Daffa Alfarizi</legend>
                <input id="student-9-h" name="student_9_attendance" type="radio" value="H" class="peer sr-only" checked>
                <label for="student-9-h" title="Hadir" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">H<span class="sr-only">Hadir</span></label>
                <input id="student-9-s" name="student_9_attendance" type="radio" value="S" class="peer sr-only">
                <label for="student-9-s" title="Sakit" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">S<span class="sr-only">Sakit</span></label>
                <input id="student-9-i" name="student_9_attendance" type="radio" value="I" class="peer sr-only">
                <label for="student-9-i" title="Izin" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">I<span class="sr-only">Izin</span></label>
                <input id="student-9-d" name="student_9_attendance" type="radio" value="D" class="peer sr-only">
                <label for="student-9-d" title="Dispensasi" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">D<span class="sr-only">Dispensasi</span></label>
                <input id="student-9-a" name="student_9_attendance" type="radio" value="A" class="peer sr-only">
                <label for="student-9-a" title="Alfa" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">A<span class="sr-only">Alfa</span></label>
            </fieldset>
        </li>

        <li class="flex items-center justify-between gap-3 px-4 py-3.5 sm:px-5">
            <div class="min-w-0">
                <p class="truncate text-sm font-bold text-[#0f3d32]">Dinda Maharani</p>
                <p class="mt-0.5 text-xs text-[#729188]">No. 10</p>
            </div>
            <fieldset class="flex shrink-0 items-center gap-1.5 sm:gap-2">
                <legend class="sr-only">Status kehadiran Dinda Maharani</legend>
                <input id="student-10-h" name="student_10_attendance" type="radio" value="H" class="peer sr-only" checked>
                <label for="student-10-h" title="Hadir" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">H<span class="sr-only">Hadir</span></label>
                <input id="student-10-s" name="student_10_attendance" type="radio" value="S" class="peer sr-only">
                <label for="student-10-s" title="Sakit" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">S<span class="sr-only">Sakit</span></label>
                <input id="student-10-i" name="student_10_attendance" type="radio" value="I" class="peer sr-only">
                <label for="student-10-i" title="Izin" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">I<span class="sr-only">Izin</span></label>
                <input id="student-10-d" name="student_10_attendance" type="radio" value="D" class="peer sr-only">
                <label for="student-10-d" title="Dispensasi" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">D<span class="sr-only">Dispensasi</span></label>
                <input id="student-10-a" name="student_10_attendance" type="radio" value="A" class="peer sr-only">
                <label for="student-10-a" title="Alfa" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">A<span class="sr-only">Alfa</span></label>
            </fieldset>
        </li>

        <li class="flex items-center justify-between gap-3 px-4 py-3.5 sm:px-5">
            <div class="min-w-0">
                <p class="truncate text-sm font-bold text-[#0f3d32]">Fahmi Ramadhan</p>
                <p class="mt-0.5 text-xs text-[#729188]">No. 11</p>
            </div>
            <fieldset class="flex shrink-0 items-center gap-1.5 sm:gap-2">
                <legend class="sr-only">Status kehadiran Fahmi Ramadhan</legend>
                <input id="student-11-h" name="student_11_attendance" type="radio" value="H" class="peer sr-only" checked>
                <label for="student-11-h" title="Hadir" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">H<span class="sr-only">Hadir</span></label>
                <input id="student-11-s" name="student_11_attendance" type="radio" value="S" class="peer sr-only">
                <label for="student-11-s" title="Sakit" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">S<span class="sr-only">Sakit</span></label>
                <input id="student-11-i" name="student_11_attendance" type="radio" value="I" class="peer sr-only">
                <label for="student-11-i" title="Izin" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">I<span class="sr-only">Izin</span></label>
                <input id="student-11-d" name="student_11_attendance" type="radio" value="D" class="peer sr-only">
                <label for="student-11-d" title="Dispensasi" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">D<span class="sr-only">Dispensasi</span></label>
                <input id="student-11-a" name="student_11_attendance" type="radio" value="A" class="peer sr-only">
                <label for="student-11-a" title="Alfa" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">A<span class="sr-only">Alfa</span></label>
            </fieldset>
        </li>

        <li class="flex items-center justify-between gap-3 px-4 py-3.5 sm:px-5">
            <div class="min-w-0">
                <p class="truncate text-sm font-bold text-[#0f3d32]">Farah Nabila</p>
                <p class="mt-0.5 text-xs text-[#729188]">No. 12</p>
            </div>
            <fieldset class="flex shrink-0 items-center gap-1.5 sm:gap-2">
                <legend class="sr-only">Status kehadiran Farah Nabila</legend>
                <input id="student-12-h" name="student_12_attendance" type="radio" value="H" class="peer sr-only" checked>
                <label for="student-12-h" title="Hadir" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">H<span class="sr-only">Hadir</span></label>
                <input id="student-12-s" name="student_12_attendance" type="radio" value="S" class="peer sr-only">
                <label for="student-12-s" title="Sakit" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">S<span class="sr-only">Sakit</span></label>
                <input id="student-12-i" name="student_12_attendance" type="radio" value="I" class="peer sr-only">
                <label for="student-12-i" title="Izin" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">I<span class="sr-only">Izin</span></label>
                <input id="student-12-d" name="student_12_attendance" type="radio" value="D" class="peer sr-only">
                <label for="student-12-d" title="Dispensasi" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">D<span class="sr-only">Dispensasi</span></label>
                <input id="student-12-a" name="student_12_attendance" type="radio" value="A" class="peer sr-only">
                <label for="student-12-a" title="Alfa" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">A<span class="sr-only">Alfa</span></label>
            </fieldset>
        </li>

        <li class="flex items-center justify-between gap-3 px-4 py-3.5 sm:px-5">
            <div class="min-w-0">
                <p class="truncate text-sm font-bold text-[#0f3d32]">Galang Prasetyo</p>
                <p class="mt-0.5 text-xs text-[#729188]">No. 13</p>
            </div>
            <fieldset class="flex shrink-0 items-center gap-1.5 sm:gap-2">
                <legend class="sr-only">Status kehadiran Galang Prasetyo</legend>
                <input id="student-13-h" name="student_13_attendance" type="radio" value="H" class="peer sr-only" checked>
                <label for="student-13-h" title="Hadir" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">H<span class="sr-only">Hadir</span></label>
                <input id="student-13-s" name="student_13_attendance" type="radio" value="S" class="peer sr-only">
                <label for="student-13-s" title="Sakit" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">S<span class="sr-only">Sakit</span></label>
                <input id="student-13-i" name="student_13_attendance" type="radio" value="I" class="peer sr-only">
                <label for="student-13-i" title="Izin" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">I<span class="sr-only">Izin</span></label>
                <input id="student-13-d" name="student_13_attendance" type="radio" value="D" class="peer sr-only">
                <label for="student-13-d" title="Dispensasi" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">D<span class="sr-only">Dispensasi</span></label>
                <input id="student-13-a" name="student_13_attendance" type="radio" value="A" class="peer sr-only">
                <label for="student-13-a" title="Alfa" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">A<span class="sr-only">Alfa</span></label>
            </fieldset>
        </li>

        <li class="flex items-center justify-between gap-3 px-4 py-3.5 sm:px-5">
            <div class="min-w-0">
                <p class="truncate text-sm font-bold text-[#0f3d32]">Gina Aprilia</p>
                <p class="mt-0.5 text-xs text-[#729188]">No. 14</p>
            </div>
            <fieldset class="flex shrink-0 items-center gap-1.5 sm:gap-2">
                <legend class="sr-only">Status kehadiran Gina Aprilia</legend>
                <input id="student-14-h" name="student_14_attendance" type="radio" value="H" class="peer sr-only" checked>
                <label for="student-14-h" title="Hadir" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">H<span class="sr-only">Hadir</span></label>
                <input id="student-14-s" name="student_14_attendance" type="radio" value="S" class="peer sr-only">
                <label for="student-14-s" title="Sakit" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">S<span class="sr-only">Sakit</span></label>
                <input id="student-14-i" name="student_14_attendance" type="radio" value="I" class="peer sr-only">
                <label for="student-14-i" title="Izin" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">I<span class="sr-only">Izin</span></label>
                <input id="student-14-d" name="student_14_attendance" type="radio" value="D" class="peer sr-only">
                <label for="student-14-d" title="Dispensasi" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">D<span class="sr-only">Dispensasi</span></label>
                <input id="student-14-a" name="student_14_attendance" type="radio" value="A" class="peer sr-only">
                <label for="student-14-a" title="Alfa" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">A<span class="sr-only">Alfa</span></label>
            </fieldset>
        </li>

        <li class="flex items-center justify-between gap-3 px-4 py-3.5 sm:px-5">
            <div class="min-w-0">
                <p class="truncate text-sm font-bold text-[#0f3d32]">Hafiz Maulana</p>
                <p class="mt-0.5 text-xs text-[#729188]">No. 15</p>
            </div>
            <fieldset class="flex shrink-0 items-center gap-1.5 sm:gap-2">
                <legend class="sr-only">Status kehadiran Hafiz Maulana</legend>
                <input id="student-15-h" name="student_15_attendance" type="radio" value="H" class="peer sr-only" checked>
                <label for="student-15-h" title="Hadir" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">H<span class="sr-only">Hadir</span></label>
                <input id="student-15-s" name="student_15_attendance" type="radio" value="S" class="peer sr-only">
                <label for="student-15-s" title="Sakit" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">S<span class="sr-only">Sakit</span></label>
                <input id="student-15-i" name="student_15_attendance" type="radio" value="I" class="peer sr-only">
                <label for="student-15-i" title="Izin" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">I<span class="sr-only">Izin</span></label>
                <input id="student-15-d" name="student_15_attendance" type="radio" value="D" class="peer sr-only">
                <label for="student-15-d" title="Dispensasi" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">D<span class="sr-only">Dispensasi</span></label>
                <input id="student-15-a" name="student_15_attendance" type="radio" value="A" class="peer sr-only">
                <label for="student-15-a" title="Alfa" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">A<span class="sr-only">Alfa</span></label>
            </fieldset>
        </li>

        <li class="flex items-center justify-between gap-3 px-4 py-3.5 sm:px-5">
            <div class="min-w-0">
                <p class="truncate text-sm font-bold text-[#0f3d32]">Intan Permata</p>
                <p class="mt-0.5 text-xs text-[#729188]">No. 16</p>
            </div>
            <fieldset class="flex shrink-0 items-center gap-1.5 sm:gap-2">
                <legend class="sr-only">Status kehadiran Intan Permata</legend>
                <input id="student-16-h" name="student_16_attendance" type="radio" value="H" class="peer sr-only" checked>
                <label for="student-16-h" title="Hadir" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">H<span class="sr-only">Hadir</span></label>
                <input id="student-16-s" name="student_16_attendance" type="radio" value="S" class="peer sr-only">
                <label for="student-16-s" title="Sakit" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">S<span class="sr-only">Sakit</span></label>
                <input id="student-16-i" name="student_16_attendance" type="radio" value="I" class="peer sr-only">
                <label for="student-16-i" title="Izin" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">I<span class="sr-only">Izin</span></label>
                <input id="student-16-d" name="student_16_attendance" type="radio" value="D" class="peer sr-only">
                <label for="student-16-d" title="Dispensasi" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">D<span class="sr-only">Dispensasi</span></label>
                <input id="student-16-a" name="student_16_attendance" type="radio" value="A" class="peer sr-only">
                <label for="student-16-a" title="Alfa" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">A<span class="sr-only">Alfa</span></label>
            </fieldset>
        </li>

        <li class="flex items-center justify-between gap-3 px-4 py-3.5 sm:px-5">
            <div class="min-w-0">
                <p class="truncate text-sm font-bold text-[#0f3d32]">Irfan Kurniawan</p>
                <p class="mt-0.5 text-xs text-[#729188]">No. 17</p>
            </div>
            <fieldset class="flex shrink-0 items-center gap-1.5 sm:gap-2">
                <legend class="sr-only">Status kehadiran Irfan Kurniawan</legend>
                <input id="student-17-h" name="student_17_attendance" type="radio" value="H" class="peer sr-only" checked>
                <label for="student-17-h" title="Hadir" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">H<span class="sr-only">Hadir</span></label>
                <input id="student-17-s" name="student_17_attendance" type="radio" value="S" class="peer sr-only">
                <label for="student-17-s" title="Sakit" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">S<span class="sr-only">Sakit</span></label>
                <input id="student-17-i" name="student_17_attendance" type="radio" value="I" class="peer sr-only">
                <label for="student-17-i" title="Izin" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">I<span class="sr-only">Izin</span></label>
                <input id="student-17-d" name="student_17_attendance" type="radio" value="D" class="peer sr-only">
                <label for="student-17-d" title="Dispensasi" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">D<span class="sr-only">Dispensasi</span></label>
                <input id="student-17-a" name="student_17_attendance" type="radio" value="A" class="peer sr-only">
                <label for="student-17-a" title="Alfa" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">A<span class="sr-only">Alfa</span></label>
            </fieldset>
        </li>

        <li class="flex items-center justify-between gap-3 px-4 py-3.5 sm:px-5">
            <div class="min-w-0">
                <p class="truncate text-sm font-bold text-[#0f3d32]">Jihan Aulia</p>
                <p class="mt-0.5 text-xs text-[#729188]">No. 18</p>
            </div>
            <fieldset class="flex shrink-0 items-center gap-1.5 sm:gap-2">
                <legend class="sr-only">Status kehadiran Jihan Aulia</legend>
                <input id="student-18-h" name="student_18_attendance" type="radio" value="H" class="peer sr-only" checked>
                <label for="student-18-h" title="Hadir" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">H<span class="sr-only">Hadir</span></label>
                <input id="student-18-s" name="student_18_attendance" type="radio" value="S" class="peer sr-only">
                <label for="student-18-s" title="Sakit" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">S<span class="sr-only">Sakit</span></label>
                <input id="student-18-i" name="student_18_attendance" type="radio" value="I" class="peer sr-only">
                <label for="student-18-i" title="Izin" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">I<span class="sr-only">Izin</span></label>
                <input id="student-18-d" name="student_18_attendance" type="radio" value="D" class="peer sr-only">
                <label for="student-18-d" title="Dispensasi" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">D<span class="sr-only">Dispensasi</span></label>
                <input id="student-18-a" name="student_18_attendance" type="radio" value="A" class="peer sr-only">
                <label for="student-18-a" title="Alfa" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">A<span class="sr-only">Alfa</span></label>
            </fieldset>
        </li>

        <li class="flex items-center justify-between gap-3 px-4 py-3.5 sm:px-5">
            <div class="min-w-0">
                <p class="truncate text-sm font-bold text-[#0f3d32]">Kevin Pratama</p>
                <p class="mt-0.5 text-xs text-[#729188]">No. 19</p>
            </div>
            <fieldset class="flex shrink-0 items-center gap-1.5 sm:gap-2">
                <legend class="sr-only">Status kehadiran Kevin Pratama</legend>
                <input id="student-19-h" name="student_19_attendance" type="radio" value="H" class="peer sr-only" checked>
                <label for="student-19-h" title="Hadir" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">H<span class="sr-only">Hadir</span></label>
                <input id="student-19-s" name="student_19_attendance" type="radio" value="S" class="peer sr-only">
                <label for="student-19-s" title="Sakit" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">S<span class="sr-only">Sakit</span></label>
                <input id="student-19-i" name="student_19_attendance" type="radio" value="I" class="peer sr-only">
                <label for="student-19-i" title="Izin" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">I<span class="sr-only">Izin</span></label>
                <input id="student-19-d" name="student_19_attendance" type="radio" value="D" class="peer sr-only">
                <label for="student-19-d" title="Dispensasi" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">D<span class="sr-only">Dispensasi</span></label>
                <input id="student-19-a" name="student_19_attendance" type="radio" value="A" class="peer sr-only">
                <label for="student-19-a" title="Alfa" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">A<span class="sr-only">Alfa</span></label>
            </fieldset>
        </li>

        <li class="flex items-center justify-between gap-3 px-4 py-3.5 sm:px-5">
            <div class="min-w-0">
                <p class="truncate text-sm font-bold text-[#0f3d32]">Laila Salsabila</p>
                <p class="mt-0.5 text-xs text-[#729188]">No. 20</p>
            </div>
            <fieldset class="flex shrink-0 items-center gap-1.5 sm:gap-2">
                <legend class="sr-only">Status kehadiran Laila Salsabila</legend>
                <input id="student-20-h" name="student_20_attendance" type="radio" value="H" class="peer sr-only" checked>
                <label for="student-20-h" title="Hadir" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">H<span class="sr-only">Hadir</span></label>
                <input id="student-20-s" name="student_20_attendance" type="radio" value="S" class="peer sr-only">
                <label for="student-20-s" title="Sakit" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">S<span class="sr-only">Sakit</span></label>
                <input id="student-20-i" name="student_20_attendance" type="radio" value="I" class="peer sr-only">
                <label for="student-20-i" title="Izin" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">I<span class="sr-only">Izin</span></label>
                <input id="student-20-d" name="student_20_attendance" type="radio" value="D" class="peer sr-only">
                <label for="student-20-d" title="Dispensasi" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">D<span class="sr-only">Dispensasi</span></label>
                <input id="student-20-a" name="student_20_attendance" type="radio" value="A" class="peer sr-only">
                <label for="student-20-a" title="Alfa" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">A<span class="sr-only">Alfa</span></label>
            </fieldset>
        </li>

        <li class="flex items-center justify-between gap-3 px-4 py-3.5 sm:px-5">
            <div class="min-w-0">
                <p class="truncate text-sm font-bold text-[#0f3d32]">M. Rizky Ananda</p>
                <p class="mt-0.5 text-xs text-[#729188]">No. 21</p>
            </div>
            <fieldset class="flex shrink-0 items-center gap-1.5 sm:gap-2">
                <legend class="sr-only">Status kehadiran M. Rizky Ananda</legend>
                <input id="student-21-h" name="student_21_attendance" type="radio" value="H" class="peer sr-only" checked>
                <label for="student-21-h" title="Hadir" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">H<span class="sr-only">Hadir</span></label>
                <input id="student-21-s" name="student_21_attendance" type="radio" value="S" class="peer sr-only">
                <label for="student-21-s" title="Sakit" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">S<span class="sr-only">Sakit</span></label>
                <input id="student-21-i" name="student_21_attendance" type="radio" value="I" class="peer sr-only">
                <label for="student-21-i" title="Izin" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">I<span class="sr-only">Izin</span></label>
                <input id="student-21-d" name="student_21_attendance" type="radio" value="D" class="peer sr-only">
                <label for="student-21-d" title="Dispensasi" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">D<span class="sr-only">Dispensasi</span></label>
                <input id="student-21-a" name="student_21_attendance" type="radio" value="A" class="peer sr-only">
                <label for="student-21-a" title="Alfa" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">A<span class="sr-only">Alfa</span></label>
            </fieldset>
        </li>

        <li class="flex items-center justify-between gap-3 px-4 py-3.5 sm:px-5">
            <div class="min-w-0">
                <p class="truncate text-sm font-bold text-[#0f3d32]">Maya Fitriani</p>
                <p class="mt-0.5 text-xs text-[#729188]">No. 22</p>
            </div>
            <fieldset class="flex shrink-0 items-center gap-1.5 sm:gap-2">
                <legend class="sr-only">Status kehadiran Maya Fitriani</legend>
                <input id="student-22-h" name="student_22_attendance" type="radio" value="H" class="peer sr-only" checked>
                <label for="student-22-h" title="Hadir" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">H<span class="sr-only">Hadir</span></label>
                <input id="student-22-s" name="student_22_attendance" type="radio" value="S" class="peer sr-only">
                <label for="student-22-s" title="Sakit" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">S<span class="sr-only">Sakit</span></label>
                <input id="student-22-i" name="student_22_attendance" type="radio" value="I" class="peer sr-only">
                <label for="student-22-i" title="Izin" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">I<span class="sr-only">Izin</span></label>
                <input id="student-22-d" name="student_22_attendance" type="radio" value="D" class="peer sr-only">
                <label for="student-22-d" title="Dispensasi" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">D<span class="sr-only">Dispensasi</span></label>
                <input id="student-22-a" name="student_22_attendance" type="radio" value="A" class="peer sr-only">
                <label for="student-22-a" title="Alfa" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">A<span class="sr-only">Alfa</span></label>
            </fieldset>
        </li>

        <li class="flex items-center justify-between gap-3 px-4 py-3.5 sm:px-5">
            <div class="min-w-0">
                <p class="truncate text-sm font-bold text-[#0f3d32]">Naufal Akbar</p>
                <p class="mt-0.5 text-xs text-[#729188]">No. 23</p>
            </div>
            <fieldset class="flex shrink-0 items-center gap-1.5 sm:gap-2">
                <legend class="sr-only">Status kehadiran Naufal Akbar</legend>
                <input id="student-23-h" name="student_23_attendance" type="radio" value="H" class="peer sr-only" checked>
                <label for="student-23-h" title="Hadir" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">H<span class="sr-only">Hadir</span></label>
                <input id="student-23-s" name="student_23_attendance" type="radio" value="S" class="peer sr-only">
                <label for="student-23-s" title="Sakit" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">S<span class="sr-only">Sakit</span></label>
                <input id="student-23-i" name="student_23_attendance" type="radio" value="I" class="peer sr-only">
                <label for="student-23-i" title="Izin" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">I<span class="sr-only">Izin</span></label>
                <input id="student-23-d" name="student_23_attendance" type="radio" value="D" class="peer sr-only">
                <label for="student-23-d" title="Dispensasi" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">D<span class="sr-only">Dispensasi</span></label>
                <input id="student-23-a" name="student_23_attendance" type="radio" value="A" class="peer sr-only">
                <label for="student-23-a" title="Alfa" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">A<span class="sr-only">Alfa</span></label>
            </fieldset>
        </li>

        <li class="flex items-center justify-between gap-3 px-4 py-3.5 sm:px-5">
            <div class="min-w-0">
                <p class="truncate text-sm font-bold text-[#0f3d32]">Nayla Zahra</p>
                <p class="mt-0.5 text-xs text-[#729188]">No. 24</p>
            </div>
            <fieldset class="flex shrink-0 items-center gap-1.5 sm:gap-2">
                <legend class="sr-only">Status kehadiran Nayla Zahra</legend>
                <input id="student-24-h" name="student_24_attendance" type="radio" value="H" class="peer sr-only" checked>
                <label for="student-24-h" title="Hadir" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">H<span class="sr-only">Hadir</span></label>
                <input id="student-24-s" name="student_24_attendance" type="radio" value="S" class="peer sr-only">
                <label for="student-24-s" title="Sakit" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">S<span class="sr-only">Sakit</span></label>
                <input id="student-24-i" name="student_24_attendance" type="radio" value="I" class="peer sr-only">
                <label for="student-24-i" title="Izin" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">I<span class="sr-only">Izin</span></label>
                <input id="student-24-d" name="student_24_attendance" type="radio" value="D" class="peer sr-only">
                <label for="student-24-d" title="Dispensasi" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">D<span class="sr-only">Dispensasi</span></label>
                <input id="student-24-a" name="student_24_attendance" type="radio" value="A" class="peer sr-only">
                <label for="student-24-a" title="Alfa" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">A<span class="sr-only">Alfa</span></label>
            </fieldset>
        </li>

        <li class="flex items-center justify-between gap-3 px-4 py-3.5 sm:px-5">
            <div class="min-w-0">
                <p class="truncate text-sm font-bold text-[#0f3d32]">Rafi Alamsyah</p>
                <p class="mt-0.5 text-xs text-[#729188]">No. 25</p>
            </div>
            <fieldset class="flex shrink-0 items-center gap-1.5 sm:gap-2">
                <legend class="sr-only">Status kehadiran Rafi Alamsyah</legend>
                <input id="student-25-h" name="student_25_attendance" type="radio" value="H" class="peer sr-only" checked>
                <label for="student-25-h" title="Hadir" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">H<span class="sr-only">Hadir</span></label>
                <input id="student-25-s" name="student_25_attendance" type="radio" value="S" class="peer sr-only">
                <label for="student-25-s" title="Sakit" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">S<span class="sr-only">Sakit</span></label>
                <input id="student-25-i" name="student_25_attendance" type="radio" value="I" class="peer sr-only">
                <label for="student-25-i" title="Izin" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">I<span class="sr-only">Izin</span></label>
                <input id="student-25-d" name="student_25_attendance" type="radio" value="D" class="peer sr-only">
                <label for="student-25-d" title="Dispensasi" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">D<span class="sr-only">Dispensasi</span></label>
                <input id="student-25-a" name="student_25_attendance" type="radio" value="A" class="peer sr-only">
                <label for="student-25-a" title="Alfa" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">A<span class="sr-only">Alfa</span></label>
            </fieldset>
        </li>

        <li class="flex items-center justify-between gap-3 px-4 py-3.5 sm:px-5">
            <div class="min-w-0">
                <p class="truncate text-sm font-bold text-[#0f3d32]">Rania Khairunnisa</p>
                <p class="mt-0.5 text-xs text-[#729188]">No. 26</p>
            </div>
            <fieldset class="flex shrink-0 items-center gap-1.5 sm:gap-2">
                <legend class="sr-only">Status kehadiran Rania Khairunnisa</legend>
                <input id="student-26-h" name="student_26_attendance" type="radio" value="H" class="peer sr-only" checked>
                <label for="student-26-h" title="Hadir" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">H<span class="sr-only">Hadir</span></label>
                <input id="student-26-s" name="student_26_attendance" type="radio" value="S" class="peer sr-only">
                <label for="student-26-s" title="Sakit" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">S<span class="sr-only">Sakit</span></label>
                <input id="student-26-i" name="student_26_attendance" type="radio" value="I" class="peer sr-only">
                <label for="student-26-i" title="Izin" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">I<span class="sr-only">Izin</span></label>
                <input id="student-26-d" name="student_26_attendance" type="radio" value="D" class="peer sr-only">
                <label for="student-26-d" title="Dispensasi" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">D<span class="sr-only">Dispensasi</span></label>
                <input id="student-26-a" name="student_26_attendance" type="radio" value="A" class="peer sr-only">
                <label for="student-26-a" title="Alfa" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">A<span class="sr-only">Alfa</span></label>
            </fieldset>
        </li>

        <li class="flex items-center justify-between gap-3 px-4 py-3.5 sm:px-5">
            <div class="min-w-0">
                <p class="truncate text-sm font-bold text-[#0f3d32]">Rangga Saputra</p>
                <p class="mt-0.5 text-xs text-[#729188]">No. 27</p>
            </div>
            <fieldset class="flex shrink-0 items-center gap-1.5 sm:gap-2">
                <legend class="sr-only">Status kehadiran Rangga Saputra</legend>
                <input id="student-27-h" name="student_27_attendance" type="radio" value="H" class="peer sr-only" checked>
                <label for="student-27-h" title="Hadir" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">H<span class="sr-only">Hadir</span></label>
                <input id="student-27-s" name="student_27_attendance" type="radio" value="S" class="peer sr-only">
                <label for="student-27-s" title="Sakit" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">S<span class="sr-only">Sakit</span></label>
                <input id="student-27-i" name="student_27_attendance" type="radio" value="I" class="peer sr-only">
                <label for="student-27-i" title="Izin" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">I<span class="sr-only">Izin</span></label>
                <input id="student-27-d" name="student_27_attendance" type="radio" value="D" class="peer sr-only">
                <label for="student-27-d" title="Dispensasi" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">D<span class="sr-only">Dispensasi</span></label>
                <input id="student-27-a" name="student_27_attendance" type="radio" value="A" class="peer sr-only">
                <label for="student-27-a" title="Alfa" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">A<span class="sr-only">Alfa</span></label>
            </fieldset>
        </li>

        <li class="flex items-center justify-between gap-3 px-4 py-3.5 sm:px-5">
            <div class="min-w-0">
                <p class="truncate text-sm font-bold text-[#0f3d32]">Rara Amalia</p>
                <p class="mt-0.5 text-xs text-[#729188]">No. 28</p>
            </div>
            <fieldset class="flex shrink-0 items-center gap-1.5 sm:gap-2">
                <legend class="sr-only">Status kehadiran Rara Amalia</legend>
                <input id="student-28-h" name="student_28_attendance" type="radio" value="H" class="peer sr-only" checked>
                <label for="student-28-h" title="Hadir" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">H<span class="sr-only">Hadir</span></label>
                <input id="student-28-s" name="student_28_attendance" type="radio" value="S" class="peer sr-only">
                <label for="student-28-s" title="Sakit" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">S<span class="sr-only">Sakit</span></label>
                <input id="student-28-i" name="student_28_attendance" type="radio" value="I" class="peer sr-only">
                <label for="student-28-i" title="Izin" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">I<span class="sr-only">Izin</span></label>
                <input id="student-28-d" name="student_28_attendance" type="radio" value="D" class="peer sr-only">
                <label for="student-28-d" title="Dispensasi" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">D<span class="sr-only">Dispensasi</span></label>
                <input id="student-28-a" name="student_28_attendance" type="radio" value="A" class="peer sr-only">
                <label for="student-28-a" title="Alfa" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">A<span class="sr-only">Alfa</span></label>
            </fieldset>
        </li>

        <li class="flex items-center justify-between gap-3 px-4 py-3.5 sm:px-5">
            <div class="min-w-0">
                <p class="truncate text-sm font-bold text-[#0f3d32]">Rizal Firmansyah</p>
                <p class="mt-0.5 text-xs text-[#729188]">No. 29</p>
            </div>
            <fieldset class="flex shrink-0 items-center gap-1.5 sm:gap-2">
                <legend class="sr-only">Status kehadiran Rizal Firmansyah</legend>
                <input id="student-29-h" name="student_29_attendance" type="radio" value="H" class="peer sr-only" checked>
                <label for="student-29-h" title="Hadir" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">H<span class="sr-only">Hadir</span></label>
                <input id="student-29-s" name="student_29_attendance" type="radio" value="S" class="peer sr-only">
                <label for="student-29-s" title="Sakit" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">S<span class="sr-only">Sakit</span></label>
                <input id="student-29-i" name="student_29_attendance" type="radio" value="I" class="peer sr-only">
                <label for="student-29-i" title="Izin" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">I<span class="sr-only">Izin</span></label>
                <input id="student-29-d" name="student_29_attendance" type="radio" value="D" class="peer sr-only">
                <label for="student-29-d" title="Dispensasi" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">D<span class="sr-only">Dispensasi</span></label>
                <input id="student-29-a" name="student_29_attendance" type="radio" value="A" class="peer sr-only">
                <label for="student-29-a" title="Alfa" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">A<span class="sr-only">Alfa</span></label>
            </fieldset>
        </li>

        <li class="flex items-center justify-between gap-3 px-4 py-3.5 sm:px-5">
            <div class="min-w-0">
                <p class="truncate text-sm font-bold text-[#0f3d32]">Salsa Billa</p>
                <p class="mt-0.5 text-xs text-[#729188]">No. 30</p>
            </div>
            <fieldset class="flex shrink-0 items-center gap-1.5 sm:gap-2">
                <legend class="sr-only">Status kehadiran Salsa Billa</legend>
                <input id="student-30-h" name="student_30_attendance" type="radio" value="H" class="peer sr-only" checked>
                <label for="student-30-h" title="Hadir" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">H<span class="sr-only">Hadir</span></label>
                <input id="student-30-s" name="student_30_attendance" type="radio" value="S" class="peer sr-only">
                <label for="student-30-s" title="Sakit" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">S<span class="sr-only">Sakit</span></label>
                <input id="student-30-i" name="student_30_attendance" type="radio" value="I" class="peer sr-only">
                <label for="student-30-i" title="Izin" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">I<span class="sr-only">Izin</span></label>
                <input id="student-30-d" name="student_30_attendance" type="radio" value="D" class="peer sr-only">
                <label for="student-30-d" title="Dispensasi" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">D<span class="sr-only">Dispensasi</span></label>
                <input id="student-30-a" name="student_30_attendance" type="radio" value="A" class="peer sr-only">
                <label for="student-30-a" title="Alfa" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">A<span class="sr-only">Alfa</span></label>
            </fieldset>
        </li>

        <li class="flex items-center justify-between gap-3 px-4 py-3.5 sm:px-5">
            <div class="min-w-0">
                <p class="truncate text-sm font-bold text-[#0f3d32]">Satria Wibowo</p>
                <p class="mt-0.5 text-xs text-[#729188]">No. 31</p>
            </div>
            <fieldset class="flex shrink-0 items-center gap-1.5 sm:gap-2">
                <legend class="sr-only">Status kehadiran Satria Wibowo</legend>
                <input id="student-31-h" name="student_31_attendance" type="radio" value="H" class="peer sr-only" checked>
                <label for="student-31-h" title="Hadir" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">H<span class="sr-only">Hadir</span></label>
                <input id="student-31-s" name="student_31_attendance" type="radio" value="S" class="peer sr-only">
                <label for="student-31-s" title="Sakit" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">S<span class="sr-only">Sakit</span></label>
                <input id="student-31-i" name="student_31_attendance" type="radio" value="I" class="peer sr-only">
                <label for="student-31-i" title="Izin" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">I<span class="sr-only">Izin</span></label>
                <input id="student-31-d" name="student_31_attendance" type="radio" value="D" class="peer sr-only">
                <label for="student-31-d" title="Dispensasi" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">D<span class="sr-only">Dispensasi</span></label>
                <input id="student-31-a" name="student_31_attendance" type="radio" value="A" class="peer sr-only">
                <label for="student-31-a" title="Alfa" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">A<span class="sr-only">Alfa</span></label>
            </fieldset>
        </li>

        <li class="flex items-center justify-between gap-3 px-4 py-3.5 sm:px-5">
            <div class="min-w-0">
                <p class="truncate text-sm font-bold text-[#0f3d32]">Siti Azzahra</p>
                <p class="mt-0.5 text-xs text-[#729188]">No. 32</p>
            </div>
            <fieldset class="flex shrink-0 items-center gap-1.5 sm:gap-2">
                <legend class="sr-only">Status kehadiran Siti Azzahra</legend>
                <input id="student-32-h" name="student_32_attendance" type="radio" value="H" class="peer sr-only" checked>
                <label for="student-32-h" title="Hadir" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">H<span class="sr-only">Hadir</span></label>
                <input id="student-32-s" name="student_32_attendance" type="radio" value="S" class="peer sr-only">
                <label for="student-32-s" title="Sakit" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">S<span class="sr-only">Sakit</span></label>
                <input id="student-32-i" name="student_32_attendance" type="radio" value="I" class="peer sr-only">
                <label for="student-32-i" title="Izin" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">I<span class="sr-only">Izin</span></label>
                <input id="student-32-d" name="student_32_attendance" type="radio" value="D" class="peer sr-only">
                <label for="student-32-d" title="Dispensasi" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">D<span class="sr-only">Dispensasi</span></label>
                <input id="student-32-a" name="student_32_attendance" type="radio" value="A" class="peer sr-only">
                <label for="student-32-a" title="Alfa" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">A<span class="sr-only">Alfa</span></label>
            </fieldset>
        </li>

        <li class="flex items-center justify-between gap-3 px-4 py-3.5 sm:px-5">
            <div class="min-w-0">
                <p class="truncate text-sm font-bold text-[#0f3d32]">Taufik Hidayat</p>
                <p class="mt-0.5 text-xs text-[#729188]">No. 33</p>
            </div>
            <fieldset class="flex shrink-0 items-center gap-1.5 sm:gap-2">
                <legend class="sr-only">Status kehadiran Taufik Hidayat</legend>
                <input id="student-33-h" name="student_33_attendance" type="radio" value="H" class="peer sr-only" checked>
                <label for="student-33-h" title="Hadir" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">H<span class="sr-only">Hadir</span></label>
                <input id="student-33-s" name="student_33_attendance" type="radio" value="S" class="peer sr-only">
                <label for="student-33-s" title="Sakit" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">S<span class="sr-only">Sakit</span></label>
                <input id="student-33-i" name="student_33_attendance" type="radio" value="I" class="peer sr-only">
                <label for="student-33-i" title="Izin" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">I<span class="sr-only">Izin</span></label>
                <input id="student-33-d" name="student_33_attendance" type="radio" value="D" class="peer sr-only">
                <label for="student-33-d" title="Dispensasi" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">D<span class="sr-only">Dispensasi</span></label>
                <input id="student-33-a" name="student_33_attendance" type="radio" value="A" class="peer sr-only">
                <label for="student-33-a" title="Alfa" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">A<span class="sr-only">Alfa</span></label>
            </fieldset>
        </li>

        <li class="flex items-center justify-between gap-3 px-4 py-3.5 sm:px-5">
            <div class="min-w-0">
                <p class="truncate text-sm font-bold text-[#0f3d32]">Tiara Anindita</p>
                <p class="mt-0.5 text-xs text-[#729188]">No. 34</p>
            </div>
            <fieldset class="flex shrink-0 items-center gap-1.5 sm:gap-2">
                <legend class="sr-only">Status kehadiran Tiara Anindita</legend>
                <input id="student-34-h" name="student_34_attendance" type="radio" value="H" class="peer sr-only" checked>
                <label for="student-34-h" title="Hadir" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">H<span class="sr-only">Hadir</span></label>
                <input id="student-34-s" name="student_34_attendance" type="radio" value="S" class="peer sr-only">
                <label for="student-34-s" title="Sakit" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">S<span class="sr-only">Sakit</span></label>
                <input id="student-34-i" name="student_34_attendance" type="radio" value="I" class="peer sr-only">
                <label for="student-34-i" title="Izin" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">I<span class="sr-only">Izin</span></label>
                <input id="student-34-d" name="student_34_attendance" type="radio" value="D" class="peer sr-only">
                <label for="student-34-d" title="Dispensasi" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">D<span class="sr-only">Dispensasi</span></label>
                <input id="student-34-a" name="student_34_attendance" type="radio" value="A" class="peer sr-only">
                <label for="student-34-a" title="Alfa" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">A<span class="sr-only">Alfa</span></label>
            </fieldset>
        </li>

        <li class="flex items-center justify-between gap-3 px-4 py-3.5 sm:px-5">
            <div class="min-w-0">
                <p class="truncate text-sm font-bold text-[#0f3d32]">Wahyu Setiawan</p>
                <p class="mt-0.5 text-xs text-[#729188]">No. 35</p>
            </div>
            <fieldset class="flex shrink-0 items-center gap-1.5 sm:gap-2">
                <legend class="sr-only">Status kehadiran Wahyu Setiawan</legend>
                <input id="student-35-h" name="student_35_attendance" type="radio" value="H" class="peer sr-only" checked>
                <label for="student-35-h" title="Hadir" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">H<span class="sr-only">Hadir</span></label>
                <input id="student-35-s" name="student_35_attendance" type="radio" value="S" class="peer sr-only">
                <label for="student-35-s" title="Sakit" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">S<span class="sr-only">Sakit</span></label>
                <input id="student-35-i" name="student_35_attendance" type="radio" value="I" class="peer sr-only">
                <label for="student-35-i" title="Izin" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">I<span class="sr-only">Izin</span></label>
                <input id="student-35-d" name="student_35_attendance" type="radio" value="D" class="peer sr-only">
                <label for="student-35-d" title="Dispensasi" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">D<span class="sr-only">Dispensasi</span></label>
                <input id="student-35-a" name="student_35_attendance" type="radio" value="A" class="peer sr-only">
                <label for="student-35-a" title="Alfa" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">A<span class="sr-only">Alfa</span></label>
            </fieldset>
        </li>

        <li class="flex items-center justify-between gap-3 px-4 py-3.5 sm:px-5">
            <div class="min-w-0">
                <p class="truncate text-sm font-bold text-[#0f3d32]">Zahra Aulia</p>
                <p class="mt-0.5 text-xs text-[#729188]">No. 36</p>
            </div>
            <fieldset class="flex shrink-0 items-center gap-1.5 sm:gap-2">
                <legend class="sr-only">Status kehadiran Zahra Aulia</legend>
                <input id="student-36-h" name="student_36_attendance" type="radio" value="H" class="peer sr-only" checked>
                <label for="student-36-h" title="Hadir" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">H<span class="sr-only">Hadir</span></label>
                <input id="student-36-s" name="student_36_attendance" type="radio" value="S" class="peer sr-only">
                <label for="student-36-s" title="Sakit" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">S<span class="sr-only">Sakit</span></label>
                <input id="student-36-i" name="student_36_attendance" type="radio" value="I" class="peer sr-only">
                <label for="student-36-i" title="Izin" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">I<span class="sr-only">Izin</span></label>
                <input id="student-36-d" name="student_36_attendance" type="radio" value="D" class="peer sr-only">
                <label for="student-36-d" title="Dispensasi" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">D<span class="sr-only">Dispensasi</span></label>
                <input id="student-36-a" name="student_36_attendance" type="radio" value="A" class="peer sr-only">
                <label for="student-36-a" title="Alfa" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 transition hover:bg-[#d2ebe2] peer-checked:bg-[#0d6e59] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0d6e59] peer-focus-visible:ring-offset-2">A<span class="sr-only">Alfa</span></label>
            </fieldset>
        </li>
        </ul>
    </form>
</div>

<div class="fixed bottom-0 left-0 z-30 w-full border-t border-[#d6ebe3] bg-white/95 p-4 shadow-[0_-8px_24px_rgba(15,61,50,0.08)] backdrop-blur md:left-64 md:w-[calc(100%-16rem)]">
    <div class="mx-auto w-full max-w-5xl">
        <button type="button" class="flex min-h-12 w-full items-center justify-center gap-2 rounded-xl bg-[#0d6e59] px-4 py-3 text-sm font-bold text-white transition hover:bg-[#0a5a49] focus:outline-none focus:ring-2 focus:ring-[#0d6e59] focus:ring-offset-2"><i class="bi bi-send-fill" aria-hidden="true"></i> Kirim Absensi</button>
    </div>
</div>
@endsection
