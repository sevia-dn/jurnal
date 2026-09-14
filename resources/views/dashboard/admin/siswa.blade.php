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
    
    @if(session('success'))
        <div class="mb-6 flex items-center justify-between rounded-lg bg-emerald-50 border border-emerald-200 p-4 text-sm text-emerald-800">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 rounded-lg bg-red-50 border border-red-200 p-4 text-sm text-red-800">
            <div class="font-semibold mb-1">Terjadi kesalahan input:</div>
            <ul class="list-disc list-inside space-y-1 text-red-700">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Header Halaman -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Manajemen Data Siswa</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola informasi siswa secara menyeluruh di semua kelas.</p>
    </div>

    <!-- Form Tambah Siswa -->
    <div class="bg-white rounded-xl shadow-sm border border-emerald-100 p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
            </svg>
            Tambah Data Siswa Baru
        </h3>
        
        <form action="{{ route('dashboard.siswa.store') }}" method="POST" class="space-y-4">
            @csrf
            
            <!-- Grid 3 kolom untuk Nama, NIS, dan Kelas -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                
                <!-- Input Nama -->
                <div class="w-full">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_siswa" required placeholder="Masukkan nama siswa..." class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm outline-none transition-all">
                </div>
                
                <!-- Input NIS -->
                <div class="w-full">
                    <label class="block text-sm font-medium text-gray-700 mb-1">NIS <span class="text-red-500">*</span></label>
                    <input type="text" name="nis" required placeholder="Cth: 100021" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm outline-none transition-all">
                </div>
                
                <!-- Select Kelas -->
                <div class="w-full">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kelas <span class="text-red-500">*</span></label>
                    <select name="kelas_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm outline-none transition-all bg-white">
                        <option value="" disabled selected>-- Pilih Kelas --</option>
                        @foreach($kelasList as $itemKelas)
                            <option value="{{ $itemKelas->id_kelas }}">{{ $itemKelas->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>

            </div>
            
            <!-- Tombol Simpan di Bawah (Pojok Kanan) -->
            <div class="flex justify-end pt-2">
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-colors shadow-sm text-sm font-medium">
                    Simpan
                </button>
            </div>

        </form>
    </div>

    <!-- Tabel Daftar Siswa -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gray-50/50 p-4 border-b border-gray-100 flex flex-col md:flex-row justify-between items-stretch md:items-center gap-3">
            <h3 class="font-semibold text-gray-700">Daftar Siswa Terdaftar</h3>
            
            <div class="flex flex-col sm:flex-row items-center gap-2.5 w-full md:w-auto">
                <!-- Dropdown Filter Kelas -->
                <div class="w-full sm:w-48">
                    <select id="filterKelasSelect" onchange="filterSiswaTable()" class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none transition-all bg-white cursor-pointer text-gray-700 font-medium">
                        <option value="all">Semua Kelas</option>
                        @foreach($kelasList as $k)
                            <option value="{{ $k->id_kelas }}" {{ (isset($kelasId) && $kelasId == $k->id_kelas) ? 'selected' : '' }}>
                                Kelas {{ $k->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Input Pencarian -->
                <div class="relative w-full sm:w-72">
                    <svg class="w-4 h-4 absolute left-3 top-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" id="cariSiswaInput" onkeyup="filterSiswaTable()" value="{{ $search ?? '' }}" placeholder="Cari siswa (Nama / NIS / Kelas)..." class="w-full pl-9 pr-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none transition-all">
                </div>
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
                <tbody id="tbodySiswaAll" class="divide-y divide-gray-100 text-sm">
                    @forelse($siswas as $index => $s)
                        <tr class="row-siswa-all hover:bg-gray-50/50 transition-colors"
                            data-nama="{{ strtolower($s->nama) }}"
                            data-nis="{{ $s->nis }}"
                            data-kelas="{{ strtolower($s->kelas->nama_kelas ?? '') }}"
                            data-kelas-id="{{ $s->kelas_id }}">
                            <td class="p-4 text-center text-gray-500 row-siswa-all-no">{{ $index + 1 }}</td>
                            <td class="p-4 text-gray-900 font-medium">{{ $s->nama }}</td>
                            <td class="p-4 text-gray-500">{{ $s->nis }}</td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 rounded-md font-medium text-xs">{{ $s->kelas->nama_kelas ?? '-' }}</span>
                            </td>
                            <td class="p-4 text-center space-x-2">
                                <button type="button" onclick="openEditSiswaModal('{{ $s->id }}', '{{ addslashes($s->nama) }}', '{{ $s->nis }}', '{{ $s->kelas_id }}')" class="text-gray-400 hover:text-amber-600 transition" title="Edit">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg>
                                </button>
                                <button type="button" onclick="openDeleteSiswaModal('{{ $s->id }}', '{{ addslashes($s->nama) }}')" class="text-gray-400 hover:text-red-600 transition" title="Hapus">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-6 text-center text-gray-400">Belum ada data siswa aktif.</td>
                        </tr>
                    @endforelse
                    <tr id="emptySiswaAllRow" class="hidden">
                        <td colspan="5" class="p-6 text-center text-gray-400">Tidak ada data siswa yang cocok.</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="bg-gray-50 p-4 border-t border-gray-100 flex justify-between items-center text-sm text-gray-500">
            <span id="totalSiswaCount">Total {{ count($siswas) }} siswa aktif</span>
        </div>
    </div>
</div>

{{-- ================= MODAL EDIT SISWA ================= --}}
<div id="modalEditSiswa" class="fixed inset-0 z-50 hidden items-center justify-center overflow-y-auto bg-gray-900/50 p-4 backdrop-blur-sm">
    <div role="dialog" aria-modal="true" aria-labelledby="modalEditSiswaTitle" class="relative w-full max-w-lg rounded-xl border border-gray-100 bg-white p-6 shadow-lg">
        <div class="mb-5 flex items-center justify-between">
            <h2 id="modalEditSiswaTitle" class="text-xl font-bold text-gray-900">Edit Data Siswa</h2>
            <button type="button" onclick="closeModal('modalEditSiswa')" aria-label="Tutup modal" class="text-gray-400 transition hover:text-gray-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form id="formEditSiswa" method="POST" action="" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label for="editSiswaNama" class="mb-1 block text-sm font-medium text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                <input id="editSiswaNama" name="nama" type="text" required class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-400">
            </div>
            <div>
                <label for="editSiswaNis" class="mb-1 block text-sm font-medium text-gray-700">NIS <span class="text-red-500">*</span></label>
                <input id="editSiswaNis" name="nis" type="text" required class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-400">
            </div>
            <div>
                <label for="editSiswaKelas" class="mb-1 block text-sm font-medium text-gray-700">Kelas <span class="text-red-500">*</span></label>
                <select id="editSiswaKelas" name="kelas_id" required class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-400">
                    @foreach($kelasList as $itemKelas)
                        <option value="{{ $itemKelas->id_kelas }}">{{ $itemKelas->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex justify-end gap-3 border-t border-gray-100 pt-5">
                <button type="button" onclick="closeModal('modalEditSiswa')" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50">Batal</button>
                <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-emerald-700">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

{{-- ================= MODAL HAPUS SISWA ================= --}}
<div id="modalHapusSiswa" class="fixed inset-0 z-50 hidden bg-gray-900/60 backdrop-blur-sm overflow-y-auto h-full w-full items-center justify-center transition-opacity">
    <div class="bg-white rounded-xl shadow-xl border border-gray-100 p-8 max-w-md w-full mx-4 text-center relative">
        <button type="button" onclick="closeModal('modalHapusSiswa')" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
        </div>
        <h1 class="text-xl font-bold text-gray-900 mb-2">Hapus Data Siswa?</h1>
        <p class="text-sm text-gray-500 mb-6">Apakah Anda yakin ingin menghapus siswa <strong id="deleteSiswaNama" class="text-gray-800"></strong> dari kelas? Status siswa akan dinonaktifkan.</p>

        <form id="formHapusSiswa" action="" method="POST" class="text-left space-y-4">
            @csrf
            @method('DELETE')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Alasan Penghapusan Siswa <span class="text-red-500">*</span></label>
                <textarea id="deleteAlasanSiswa" name="alasan" rows="3" required placeholder="Tuliskan alasan menghapus data siswa ini..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm"></textarea>
            </div>
            <div class="flex justify-center gap-3 pt-4">
                <button type="button" onclick="closeModal('modalHapusSiswa')" class="px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition text-sm font-medium w-full">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg transition shadow-sm text-sm font-medium w-full">Ya, Hapus Siswa</button>
            </div>
        </form>
    </div>
</div>

{{-- ================= SCRIPT JAVASCRIPT ================= --}}
<script>
    function filterSiswaTable() {
        const rawQuery = (document.getElementById('cariSiswaInput')?.value || '').toLowerCase().trim();
        const cleanQuery = rawQuery.replace(/[^a-z0-9]/g, '');
        const selectedKelas = (document.getElementById('filterKelasSelect')?.value || 'all');
        const rows = document.querySelectorAll('#tbodySiswaAll .row-siswa-all');
        const emptyRow = document.getElementById('emptySiswaAllRow');
        let visibleCount = 0;

        rows.forEach(row => {
            const nama = row.dataset.nama || '';
            const nis = row.dataset.nis || '';
            const kelas = row.dataset.kelas || '';
            const kelasId = row.dataset.kelasId || '';

            const cleanNama = nama.replace(/[^a-z0-9]/g, '');
            const cleanNis = nis.replace(/[^a-z0-9]/g, '');
            const cleanKelas = kelas.replace(/[^a-z0-9]/g, '');

            const matchesQuery = !cleanQuery || 
                nama.includes(rawQuery) || 
                nis.includes(rawQuery) || 
                kelas.includes(rawQuery) ||
                cleanNama.includes(cleanQuery) ||
                cleanNis.includes(cleanQuery) ||
                cleanKelas.includes(cleanQuery);

            const matchesKelas = (selectedKelas === 'all' || kelasId === selectedKelas);

            if (matchesQuery && matchesKelas) {
                row.style.display = '';
                visibleCount++;
                const noCell = row.querySelector('.row-siswa-all-no');
                if (noCell) {
                    noCell.textContent = visibleCount;
                }
            } else {
                row.style.display = 'none';
            }
        });

        if (emptyRow) {
            emptyRow.style.display = visibleCount === 0 ? '' : 'none';
        }

        const countSpan = document.getElementById('totalSiswaCount');
        if (countSpan) {
            if (rawQuery || selectedKelas !== 'all') {
                countSpan.textContent = `Menampilkan ${visibleCount} dari ${rows.length} siswa aktif`;
            } else {
                countSpan.textContent = `Total ${rows.length} siswa aktif`;
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('cariSiswaInput')?.value || (document.getElementById('filterKelasSelect')?.value && document.getElementById('filterKelasSelect')?.value !== 'all')) {
            filterSiswaTable();
        }
    });

    function openEditSiswaModal(id, nama, nis, kelasId) {
        const form = document.getElementById('formEditSiswa');
        form.action = "{{ url('dashboard/siswa') }}/" + id;
        document.getElementById('editSiswaNama').value = nama;
        document.getElementById('editSiswaNis').value = nis;
        const selectKelas = document.getElementById('editSiswaKelas');
        if (selectKelas) {
            selectKelas.value = kelasId;
        }

        openModal('modalEditSiswa');
    }

    function openDeleteSiswaModal(id, nama) {
        const form = document.getElementById('formHapusSiswa');
        form.action = "{{ url('dashboard/siswa') }}/" + id;
        document.getElementById('deleteSiswaNama').textContent = nama;
        const textarea = document.getElementById('deleteAlasanSiswa');
        if (textarea) textarea.value = '';

        openModal('modalHapusSiswa');
    }

    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden'; 
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto'; 
    }
</script>
@endsection
