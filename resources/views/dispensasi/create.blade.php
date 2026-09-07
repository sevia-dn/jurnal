{{--
    resources/views/dispensasi/create.blade.php
    Form Pengajuan Dispensasi (diisi oleh guru/siswa)
--}}
@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto px-4 py-8">

    <div class="bg-white border-l-4 border-emerald-700 rounded-xl shadow-sm p-6">

        <h1 class="text-2xl font-bold text-emerald-800 mb-6">Pengajuan Dispensasi</h1>

        <form method="POST" action="#" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label class="text-sm font-medium text-gray-700 mb-1 block">Nama</label>
                <input type="text" name="nama" placeholder="Nama lengkap"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-400">
            </div>

            <div>
                <label class="text-sm font-medium text-gray-700 mb-1 block">Jenis Dispensasi</label>
                <select name="jenis_dispensasi"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                    <option value="">Pilih jenis dispensasi...</option>
                    <option value="sakit">Sakit</option>
                    <option value="izin_keluarga">Izin Keperluan Keluarga</option>
                    <option value="acara_sekolah">Mengikuti Acara/Lomba Sekolah</option>
                    <option value="lainnya">Lainnya</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-1 block">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-1 block">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                </div>
            </div>

            <div>
                <label class="text-sm font-medium text-gray-700 mb-1 block">Alasan</label>
                <textarea name="alasan" rows="3" placeholder="Jelaskan alasan pengajuan dispensasi..."
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-400"></textarea>
            </div>

            <div>
                <label class="text-sm font-medium text-gray-700 mb-1 block">Bukti Pendukung</label>
                <label for="bukti"
                       class="flex flex-col items-center justify-center border-2 border-dashed border-emerald-200 rounded-xl py-8 cursor-pointer hover:bg-emerald-50 transition">
                    <span class="text-emerald-700 font-medium">Unggah Foto / Dokumen</span>
                    <span class="text-xs text-gray-400 mt-1">PNG, JPG, PDF hingga 10MB</span>
                    <input id="bukti" type="file" name="bukti" class="hidden">
                </label>
            </div>

            <button type="submit"
                    class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-medium py-3 rounded-lg transition">
                Kirim Pengajuan
            </button>
        </form>
    </div>

</div>
@endsection