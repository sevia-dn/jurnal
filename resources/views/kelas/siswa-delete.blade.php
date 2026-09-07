@extends('layouts.app')

@section('content')
<div class="min-h-[80vh] flex flex-col items-center justify-center">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 max-w-md w-full text-center">
        <div class="w-12 h-12 bg-red-50 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4 text-xl">
            🗑️
        </div>
        
        <h1 class="text-xl font-bold text-gray-900 mb-2">Hapus Data Siswa?</h1>
        <p class="text-sm text-gray-500 mb-6">Apakah Anda yakin ingin menghapus data siswa ini? Tindakan ini tidak dapat dibatalkan.</p>

        <form action="/preview/kelas/siswa/destroy" method="POST" class="space-y-4 text-left">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Alasan Penghapusan Siswa <span class="text-red-500">*</span></label>
                <textarea name="alasan" rows="3" required placeholder="Masukkan alasan mengapa siswa ini dihapus..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 text-sm"></textarea>
            </div>

            <div class="flex justify-center gap-3 pt-2">
                <a href="/preview/kelas/siswa" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition text-sm font-medium">Batal</a>
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition shadow-sm text-sm font-medium">Ya, Hapus</button>
            </div>
        </form>
    </div>
</div>
@endsection