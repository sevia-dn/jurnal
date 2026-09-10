@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('sidebar')
    @include('layouts.admin.sidebar')
@endsection

@section('navbar')
    @include('layouts.admin.navbar')
@endsection

@section('content')

{{-- ================= DATA DUMMY (Nantinya dipindah ke Controller) ================= --}}
@php
    $gurus = [
        (object)['id' => 1, 'nama' => 'Budi Santoso, S.Kom'],
        (object)['id' => 2, 'nama' => 'Siti Aminah, S.T.'],
        (object)['id' => 3, 'nama' => 'Ahmad Fauzi, M.Kom'],
        (object)['id' => 4, 'nama' => 'Drs. Joko Widodo'],
    ];

    $mapels = [
        ['kode' => 'RPL-PWEB', 'nama' => 'Pemrograman Web dan Perangkat Bergerak', 'guru' => 'Budi Santoso, S.Kom'],
        ['kode' => 'RPL-BD', 'nama' => 'Basis Data', 'guru' => 'Siti Aminah, S.T.'],
        ['kode' => 'RPL-PBO', 'nama' => 'Pemrograman Berorientasi Objek', 'guru' => 'Ahmad Fauzi, M.Kom'],
        ['kode' => 'UM-MTK', 'nama' => 'Matematika Terapan', 'guru' => 'Drs. Joko Widodo'],
    ];
@endphp
{{-- ============================================================================== --}}

<div class="p-6 sm:p-10 font-sans">

    {{-- Header Halaman --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Manajemen Mata Pelajaran</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola daftar mata pelajaran yang diajarkan di sekolah.</p>
    </div>

    {{-- ================= FORM TAMBAH MAPEL ================= --}}
    <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-6 mb-8">
        <h2 class="text-lg font-semibold text-emerald-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Tambah Mata Pelajaran Baru
        </h2>

        <form method="POST" action="#" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            @csrf

            <div class="flex flex-col">
                <label class="text-sm font-medium text-gray-700 mb-1">Kode Mapel</label>
                <input type="text" name="kode_mapel" placeholder="Cth: RPL-PWEB"
                       class="border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm transition-all bg-white">
            </div>

            <div class="flex flex-col md:col-span-2">
                <label class="text-sm font-medium text-gray-700 mb-1">Nama Mapel</label>
                <input type="text" name="nama_mapel" placeholder="Cth: Pemrograman Web"
                       class="border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm transition-all bg-white">
            </div>

            <div class="flex flex-col">
                <label class="text-sm font-medium text-gray-700 mb-1">Guru Pengampu</label>
                <select name="guru_id"
                        class="border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm transition-all bg-white cursor-pointer">
                    <option value="" disabled selected>-- Pilih Guru --</option>
                    @foreach($gurus ?? [] as $guru)
                        <option value="{{ is_array($guru) ? $guru['id'] : $guru->id }}">
                            {{ is_array($guru) ? $guru['nama'] : $guru->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-4 flex justify-end mt-2">
                <button type="submit"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-6 py-2.5 rounded-lg transition-colors shadow-sm">
                    Simpan Mapel
                </button>
            </div>
        </form>
    </div>

    {{-- ================= TABEL DAFTAR MAPEL ================= --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-gray-700 border-b border-gray-200 text-sm">
                <tr>
                        <th class="p-4 w-16 text-center">No</th>
                        <th class="p-4 w-32">Kode</th>
                        <th class="p-4">Nama Mapel</th>
                        <th class="p-4">Guru Pengampu</th>
                        <th class="p-4 text-center w-28">Aksi</th>
                </tr>
                </thead>
              
                <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                    @forelse($mapels ?? [] as $i => $mapel)
                        <tr class="hover:bg-emerald-50/30 transition-colors">
                            <td class="p-4 text-center text-gray-500">{{ $i + 1 }}</td>
                            <td class="p-4 font-medium text-emerald-700">
                                <span class="px-2.5 py-1 bg-emerald-100/50 rounded-md border border-emerald-200 text-xs">
                                    {{ $mapel['kode'] }}
                                </span>
                            </td>
                            <td class="p-4 font-medium text-gray-900">{{ $mapel['nama'] }}</td>
                            <td class="p-4">{{ $mapel['guru'] }}</td>
                            <td class="p-4 text-center space-x-2">
                                <button type="button" onclick="openEditModal('modalEditMapel', this)" data-edit-kode="{{ $mapel['kode'] }}" data-edit-nama="{{ $mapel['nama'] }}" data-edit-guru="{{ $mapel['guru'] }}" class="text-gray-400 hover:text-amber-600 transition" title="Edit">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg>
                                </button>
                                <!-- Memanggil openModal dengan ID modalHapusMapel -->
                                <button onclick="openModal('modalHapusMapel')" class="text-gray-400 hover:text-red-600 transition" title="Hapus">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Belum ada data mata pelajaran yang ditambahkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>


    {{-- ================= MODAL EDIT MAPEL ================= --}}
    <div id="modalEditMapel" class="fixed inset-0 z-50 hidden items-center justify-center overflow-y-auto bg-gray-900/50 p-4 backdrop-blur-sm">
        <div role="dialog" aria-modal="true" aria-labelledby="modalEditMapelTitle" class="relative w-full max-w-lg rounded-xl border border-gray-100 bg-white p-6 shadow-lg">
            <div class="mb-5 flex items-center justify-between">
                <h2 id="modalEditMapelTitle" class="text-xl font-bold text-gray-900">Edit Mata Pelajaran</h2>
                <button type="button" onclick="closeEditModal('modalEditMapel')" aria-label="Tutup modal" class="text-gray-400 transition hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form onsubmit="event.preventDefault(); closeEditModal('modalEditMapel');" class="space-y-4">
                <div>
                    <label for="editMapelKode" class="mb-1 block text-sm font-medium text-gray-700">Kode Mapel</label>
                    <input id="editMapelKode" data-edit-field="kode" type="text" placeholder="RPL-PWEB" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-400">
                </div>
                <div>
                    <label for="editMapelNama" class="mb-1 block text-sm font-medium text-gray-700">Nama Mapel</label>
                    <input id="editMapelNama" data-edit-field="nama" type="text" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-400">
                </div>
                <div>
                    <label for="editMapelGuru" class="mb-1 block text-sm font-medium text-gray-700">Guru Pengampu</label>
                    <select id="editMapelGuru" data-edit-field="guru" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-400">
                        @foreach($gurus ?? [] as $guru)
                            <option value="{{ is_array($guru) ? $guru['nama'] : $guru->nama }}">{{ is_array($guru) ? $guru['nama'] : $guru->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-end gap-3 border-t border-gray-100 pt-5">
                    <button type="button" onclick="closeEditModal('modalEditMapel')" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50">Batal</button>
                    <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-emerald-700">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ================= MODAL HAPUS MAPEL ================= --}}
    <!-- ID Modal sudah diubah menjadi modalHapusMapel agar sinkron dengan tombol -->
    <div id="modalHapusMapel" class="fixed inset-0 z-50 hidden bg-gray-900/60 backdrop-blur-sm overflow-y-auto h-full w-full items-center justify-center transition-opacity">
        <div class="bg-white rounded-xl shadow-xl border border-gray-100 p-8 max-w-md w-full mx-4 text-center relative">
            
            <!-- Tombol Close (X) Pojok Kanan Atas -->
            <button type="button" onclick="closeModal('modalHapusMapel')" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </div>
            
            <h1 class="text-xl font-bold text-gray-900 mb-2">Hapus Mata Pelajaran?</h1>
            <p class="text-sm text-gray-500 mb-6">Apakah Anda yakin ingin menghapus mata pelajaran ini dari sistem? Tindakan ini tidak dapat dibatalkan.</p>

            <form action="#" method="POST" class="text-left space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alasan Penghapusan <span class="text-red-500">*</span></label>
                    <textarea name="alasan" rows="3" required placeholder="Tuliskan alasan menghapus mata pelajaran ini..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm"></textarea>
                </div>
                
                <div class="flex justify-center gap-3 pt-4">
                    <button type="button" onclick="closeModal('modalHapusMapel')" class="px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition text-sm font-medium w-full">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg transition shadow-sm text-sm font-medium w-full">Ya, Hapus Mapel</button>
                </div>
            </form>
        </div>
    </div>

</div> <!-- Penutup dari div class="p-6 sm:p-10 font-sans" -->

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
        // Prevent scrolling on background
        document.body.style.overflow = 'hidden'; 
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        // Restore scrolling
        document.body.style.overflow = 'auto'; 
    }
</script>
@endsection
