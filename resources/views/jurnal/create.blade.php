@extends('layouts.app')

@section('content')
<!-- Header Mobile -->
<div class="flex md:hidden justify-between items-center mb-4">
    <div class="flex items-center gap-2">
        <div class="bg-[#3db892] text-white p-2 rounded-xl flex items-center justify-center">
            <i class="bi bi-mortarboard-fill text-xl"></i>
        </div>
        <h1 class="text-xl font-bold text-[#0d6e59]">JurnalKita</h1>
    </div>
    <a href="{{ route('pengurus-kelas.notifikasi') }}" class="text-[#0d6e59] text-xl p-1 relative">
        <i class="bi bi-bell"></i>
        <span class="absolute top-1 right-1 bg-red-500 w-2 h-2 rounded-full"></span>
    </a>
</div>

<!-- Deskripsi Halaman -->
<div class="mb-6">
    <div class="flex items-center gap-2 mb-1">
        <span class="bg-[#0d6e59] text-white text-[10px] font-bold px-2.5 py-0.5 rounded-md">Sekretaris</span>
    </div>
    <h2 class="text-2xl font-extrabold text-[#0f3d32]">Konfirmasi Kehadiran Guru</h2>
    <p class="text-xs md:text-sm text-[#5e7e75] font-medium leading-relaxed mt-1">
        Verifikasi kehadiran guru pengampu pada jam pelajaran yang berlangsung.
    </p>
</div>

<!-- Form Container -->
<form action="#" method="POST" class="space-y-5 max-w-2xl">
    @csrf

    <!-- Card 1: Informasi Sesi Pelajaran -->
    <div class="bg-white p-5 rounded-2xl border-l-4 border-[#0d6e59] shadow-sm">
        <h3 class="font-bold text-base text-[#0f3d32] mb-4">Informasi Sesi</h3>

        <div class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Kelas</label>
                <select class="w-full text-xs md:text-sm bg-white border border-gray-200 rounded-xl p-3 text-gray-700 focus:ring-[#0d6e59] focus:border-[#0d6e59] outline-none">
                    <option value="" disabled selected>Pilih Kelas</option>
                    <option value="XI RPL 1">XI RPL 1</option>
                    <option value="XI RPL 2">XI RPL 2</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Mata Pelajaran</label>
                <select class="w-full text-xs md:text-sm bg-white border border-gray-200 rounded-xl p-3 text-gray-700 focus:ring-[#0d6e59] focus:border-[#0d6e59] outline-none">
                    <option selected disabled>Pilih Mata Pelajaran</option>
                    <option>Pemrograman Web</option>
                    <option>Pemrograman Berorientasi Objek</option>
                    <option>Basis Data</option>
                    <option>Matematika</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Nama Guru Pengampu</label>
                <input type="text" placeholder="Contoh: Bpk. Budi Santoso, S.Pd" class="w-full text-xs md:text-sm bg-white border border-gray-200 rounded-xl p-3 text-gray-700 focus:ring-[#0d6e59] focus:border-[#0d6e59] outline-none">
            </div>
        </div>
    </div>

    <!-- Card 2: Status Kehadiran Guru -->
    <div class="bg-white p-5 rounded-2xl border-l-4 border-[#0d6e59] shadow-sm">
        <h3 class="font-bold text-base text-[#0f3d32] mb-4">Status Kehadiran Guru</h3>

        <div class="grid grid-cols-2 gap-3">
            <label class="flex items-center justify-center p-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-[#0d6e59] transition text-xs font-bold text-gray-700 has-[:checked]:border-[#0d6e59] has-[:checked]:bg-[#e4f3ed] has-[:checked]:text-[#0d6e59]">
                <input type="radio" name="status_guru" value="Hadir" class="hidden" checked>
                <i class="bi bi-check-circle-fill mr-2 text-base"></i> Guru Hadir
            </label>

            <label class="flex items-center justify-center p-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-red-500 transition text-xs font-bold text-gray-700 has-[:checked]:border-red-500 has-[:checked]:bg-red-50 has-[:checked]:text-red-600">
                <input type="radio" name="status_guru" value="Tidak Hadir" class="hidden">
                <i class="bi bi-x-circle-fill mr-2 text-base"></i> Tidak Hadir / Inal
            </label>
        </div>

        <div class="mt-4">
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Catatan / Keterangan Tambahan</label>
            <textarea rows="3" placeholder="Contoh: Guru memberi tugas via WhatsApp / Guru terlambat 15 menit" class="w-full text-xs md:text-sm bg-white border border-gray-200 rounded-xl p-3 text-gray-700 focus:ring-[#0d6e59] focus:border-[#0d6e59] outline-none"></textarea>
        </div>
    </div>

    <!-- Submit Button -->
    <button type="submit" class="w-full bg-[#0d6e59] hover:bg-[#095243] text-white py-3.5 rounded-2xl font-bold text-sm shadow-sm transition">
        Simpan Verifikasi
    </button>
</form>
@endsection