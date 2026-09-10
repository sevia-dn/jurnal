@extends('layouts.app')

@section('title', 'Data Siswa')

@section('sidebar')
    @include('layouts.admin.sidebar')
@endsection

@section('navbar')
    @include('layouts.admin.navbar')
@endsection

@section('content')
<div class="p-6 sm:p-10 font-sans">
    
    <!-- Header Halaman -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Manajemen Data Siswa</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola informasi siswa secara menyeluruh.</p>
    </div>

    <!-- Form Tambah Siswa -->
    <div class="bg-white rounded-xl shadow-sm border border-emerald-100 p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
            Tambah Data Siswa Baru
        </h3>
        
        <form action="#" method="POST" class="space-y-4">
            @csrf
            
            <!-- Grid 3 kolom untuk Nama, NIS, dan Kelas -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                
                <!-- Input Nama -->
                <div class="w-full">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="nama_siswa" placeholder="Masukkan nama siswa..." class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm outline-none transition-all">
                </div>
                
                <!-- Input NIS -->
                <div class="w-full">
                    <label class="block text-sm font-medium text-gray-700 mb-1">NIS</label>
                    <input type="text" name="nis" placeholder="Cth: 10021" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm outline-none transition-all">
                </div>
                
                <!-- Select Kelas -->
                <div class="w-full">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                    <select name="kelas_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm outline-none transition-all bg-white">
                        <option value="" disabled selected>-- Pilih Kelas --</option>
                        <option value="1">X RPL 1</option>
                        <option value="2">XI RPL 1</option>
                        <option value="3">XI RPL 2</option>
                        <option value="4">XII RPL 1</option>
                    </select>
                </div>

            </div>
            
            <!-- Tombol Simpan di Bawah (Pojok Kanan) -->
            <div class="md:col-span-4">
                <button type="submit" class="bg-emerald-700 hover:bg-emerald-800 text-white font-medium px-5 py-2 rounded-lg transition">
                    Simpan
                </button>
            </div>

        </form>
    </div>

    <!-- Tabel Daftar Siswa -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gray-50/50 p-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-semibold text-gray-700">Daftar Siswa Terdaftar</h3>
            <div class="relative">
                <svg class="w-4 h-4 absolute left-3 top-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input type="text" placeholder="Cari siswa..." class="pl-9 pr-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none w-64 transition-all">
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-emerald-50/50 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="p-4 w-16 text-center">No</th>
                        <th class="p-4">Nama Siswa</th>
                        <th class="p-4">NIS</th>
                        <th class="p-4">Kelas</th>
                        <th class="p-4 text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="p-4 text-center text-gray-500">1</td>
                        <td class="p-4 text-gray-900 font-medium">Ahmad Fauzi</td>
                        <td class="p-4 text-gray-500">10021</td>
                        <td class="p-4"><span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 rounded-md font-medium text-xs">XI RPL 2</span></td>
                        <td class="p-4 text-center space-x-2">
                            <button type="button" onclick="openEditModal('modalEditSiswa', this)" data-edit-nama="Ahmad Fauzi" data-edit-nis="10021" data-edit-kelas="XI RPL 2" class="text-gray-400 hover:text-amber-600 transition" title="Edit"><svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></button>
                            <button onclick="openModal('modalHapusSiswa')" class="text-gray-400 hover:text-red-600 transition" title="Hapus"><svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ================= MODAL EDIT SISWA ================= --}}
<div id="modalEditSiswa" class="fixed inset-0 z-50 hidden items-center justify-center overflow-y-auto bg-gray-900/50 p-4 backdrop-blur-sm">
    <div role="dialog" aria-modal="true" aria-labelledby="modalEditSiswaTitle" class="relative w-full max-w-lg rounded-xl border border-gray-100 bg-white p-6 shadow-lg">
        <div class="mb-5 flex items-center justify-between">
            <h2 id="modalEditSiswaTitle" class="text-xl font-bold text-gray-900">Edit Data Siswa</h2>
            <button type="button" onclick="closeEditModal('modalEditSiswa')" aria-label="Tutup modal" class="text-gray-400 transition hover:text-gray-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form onsubmit="event.preventDefault(); closeEditModal('modalEditSiswa');" class="space-y-4">
            <div>
                <label for="editSiswaNama" class="mb-1 block text-sm font-medium text-gray-700">Nama Lengkap</label>
                <input id="editSiswaNama" data-edit-field="nama" type="text" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-400">
            </div>
            <div>
                <label for="editSiswaNis" class="mb-1 block text-sm font-medium text-gray-700">NIS</label>
                <input id="editSiswaNis" data-edit-field="nis" type="text" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-400">
            </div>
            <div>
                <label for="editSiswaKelas" class="mb-1 block text-sm font-medium text-gray-700">Kelas</label>
                <select id="editSiswaKelas" data-edit-field="kelas" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-400">
                    <option>X RPL 1</option>
                    <option>XI RPL 1</option>
                    <option>XI RPL 2</option>
                    <option>XII RPL 1</option>
                </select>
            </div>
            <div class="flex justify-end gap-3 border-t border-gray-100 pt-5">
                <button type="button" onclick="closeEditModal('modalEditSiswa')" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50">Batal</button>
                <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-emerald-700">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

{{-- ================= MODAL HAPUS SISWA ================= --}}
<div id="modalHapusSiswa" class="fixed inset-0 z-50 hidden bg-gray-900/60 backdrop-blur-sm overflow-y-auto h-full w-full items-center justify-center transition-opacity">
    <div class="bg-white rounded-xl shadow-xl border border-gray-100 p-8 max-w-md w-full mx-4 text-center relative">
        <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
        </div>
        <h1 class="text-xl font-bold text-gray-900 mb-2">Hapus Data Siswa?</h1>
        <p class="text-sm text-gray-500 mb-6">Apakah Anda yakin ingin mengeluarkan siswa ini dari sistem? Tindakan ini tidak dapat dibatalkan.</p>

        <form action="#" method="POST" class="text-left space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Alasan Penghapusan Siswa <span class="text-red-500">*</span></label>
                <textarea name="alasan" rows="3" required placeholder="Tuliskan alasan menghapus data siswa ini..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm"></textarea>
            </div>
            <div class="flex justify-center gap-3 pt-4">
                <button type="button" onclick="closeModal('modalHapusSiswa')" class="px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition text-sm font-medium">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg transition shadow-sm text-sm font-medium">Ya, Hapus Siswa</button>
            </div>
        </form>
    </div>
</div>

{{-- ================= SCRIPT JAVASCRIPT ================= --}}
<script>
    function openEditModal(modalId, trigger) {
        const modal = document.getElementById(modalId);

        modal.querySelectorAll('[data-edit-field]').forEach((field) => {
            const fieldName = field.dataset.editField;
            const value = trigger.dataset[`edit${fieldName.charAt(0).toUpperCase()}${fieldName.slice(1)}`];

            if (value !== undefined) {
                field.value = value;
            }
        });

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeEditModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
    }

    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden'; 
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto'; 
    }
</script>
@endsection
