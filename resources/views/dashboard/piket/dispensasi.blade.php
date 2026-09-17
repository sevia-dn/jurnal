@extends('layouts.app')

@section('title', 'Pengajuan Dispensasi')

@section('sidebar')
    @include('layouts.piket.sidebar')
@endsection

@section('navbar')
    @include('layouts.piket.navbar')
@endsection

@section('content')
<div class="mx-auto w-full max-w-xl px-4 py-6 sm:px-6 lg:px-8">

    <header class="mb-6">
        <p class="text-sm font-semibold text-emerald-700">Guru Piket</p>
        <h1 class="mt-1 text-2xl font-extrabold text-slate-900 sm:text-3xl">Pengajuan Dispensasi</h1>
        <p class="mt-2 text-sm text-slate-500">Isi form berikut untuk mengajukan dispensasi siswa.</p>
    </header>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <form method="POST" action="#" enctype="multipart/form-data">
            @csrf
            <div class="space-y-5 p-5 sm:p-6">

                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">Nama</span>
                    <input type="text" name="nama" placeholder="Nama lengkap"
                        class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-emerald-600 focus:ring-4 focus:ring-emerald-100">
                </label>

                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">Jenis Dispensasi</span>
                    <select name="jenis_dispensasi"
                        class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-emerald-600 focus:ring-4 focus:ring-emerald-100">
                        <option value="">Pilih jenis dispensasi...</option>
                        <option value="sakit">Sakit</option>
                        <option value="izin_keluarga">Izin Keperluan Keluarga</option>
                        <option value="acara_sekolah">Mengikuti Acara / Lomba Sekolah</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </label>

                <div class="grid grid-cols-2 gap-4">
                    <label class="block">
                        <span class="text-sm font-semibold text-slate-700">Tanggal Mulai</span>
                        <input type="date" name="tanggal_mulai"
                            class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-emerald-600 focus:ring-4 focus:ring-emerald-100">
                    </label>
                    <label class="block">
                        <span class="text-sm font-semibold text-slate-700">Tanggal Selesai</span>
                        <input type="date" name="tanggal_selesai"
                            class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-emerald-600 focus:ring-4 focus:ring-emerald-100">
                    </label>
                </div>

                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">Alasan</span>
                    <textarea name="alasan" rows="3" placeholder="Jelaskan alasan pengajuan dispensasi..."
                        class="mt-2 w-full resize-none rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-emerald-600 focus:ring-4 focus:ring-emerald-100"></textarea>
                </label>

                <div>
                    <span class="text-sm font-semibold text-slate-700">Bukti Pendukung</span>
                    <label for="bukti"
                        class="mt-2 flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-emerald-200 bg-emerald-50 py-8 text-center transition hover:bg-emerald-100">
                        <i class="bi bi-cloud-arrow-up-fill text-3xl text-emerald-700" aria-hidden="true"></i>
                        <span class="mt-3 text-sm font-bold text-emerald-800">Unggah Foto / Dokumen</span>
                        <span class="mt-1 text-xs text-emerald-600">PNG, JPG, PDF hingga 10MB</span>
                        <input id="bukti" type="file" name="bukti" class="sr-only">
                    </label>
                </div>

            </div>

            {{-- Sticky footer — tombol submit di kanan bawah, konsisten dengan standar modal --}}
            <div class="flex justify-end border-t border-slate-100 bg-slate-50 px-5 py-4">
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:ring-offset-2">
                    <i class="bi bi-send-fill" aria-hidden="true"></i>
                    Kirim Pengajuan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection