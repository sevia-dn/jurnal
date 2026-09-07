@extends('layouts.app')

@section('content')
<div class="min-h-[80vh] flex flex-col items-center justify-center">
    <div class="w-full max-w-xl">
        <div class="mb-6">
            <a href="/preview/kelas/siswa" class="text-emerald-600 hover:underline text-sm font-medium flex items-center gap-1 mb-2">&larr; Kembali ke Daftar Siswa</a>
            <h1 class="text-2xl font-bold text-gray-900">Edit Data Siswa</h1>
            <p class="text-sm text-gray-500">Silakan perbarui informasi siswa di bawah ini.</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <form action="/preview/kelas/siswa/update" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="nama_siswa" value="Ahmad Fauzi" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Induk Siswa (NIS)</label>
                    <input type="text" name="nis" value="10021" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                    <input type="text" name="kelas" value="XI RPL 1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                </div>
                <div class="pt-4 flex justify-end gap-2">
                    <a href="/preview/kelas/siswa" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition text-sm">Batal</a>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition shadow-sm text-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection