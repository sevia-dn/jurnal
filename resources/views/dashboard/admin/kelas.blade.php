@extends('layouts.app')

@section('title', 'Dashboard Admin')

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

    {{-- ================= HEADER UTAMA ================= --}}
    <div id="viewHeaderKelas" class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Data Kelas</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola informasi kelas, nama guru, dan jumlah siswa aktif.</p>
        </div>
        <button onclick="openModal('modalTambahKelas')" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg flex items-center gap-2 text-sm font-medium transition shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Tambah Kelas Baru
        </button>
    </div>

    {{-- ================= VIEW 1: DATA KELAS (Tabel Utama) ================= --}}
    <div id="viewKelas" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-gray-700 border-b border-gray-200 text-sm">
                <tr>
                    <th class="p-4 w-16 text-center">No</th>
                    <th class="p-4">Nama Kelas</th>
                    <th class="p-4">Nama Guru</th>
                    <th class="p-4">Jumlah siswa</th>
                    <th class="p-4 text-center w-24">Aksi</th>
                </tr>
            </thead>
                
                <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                    @forelse($kelasList as $index => $k)
                        <tr onclick="showStudents('{{ addslashes($k->nama_kelas) }}', '{{ $k->id_kelas }}')" class="hover:bg-emerald-50/30 cursor-pointer transition">
                            <td class="p-4 text-center">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</td>
                            <td class="p-4 font-medium text-gray-900">{{ $k->nama_kelas }}</td>
                            <td class="p-4">{{ $k->wali_kelas ?? '-' }}</td>
                            <td class="p-4">{{ $k->jumlah_siswa ?? 0 }}</td>
                            <td class="p-4 text-center space-x-2">
                                <button type="button" onclick="event.stopPropagation(); openEditModal('modalEditKelas', this)" 
                                        data-edit-id="{{ $k->id_kelas }}" 
                                        data-edit-nama="{{ $k->nama_kelas }}" 
                                        data-edit-guru="{{ $k->wali_kelas }}" 
                                        data-edit-jumlah-siswa="{{ $k->jumlah_siswa }}" 
                                        class="text-gray-400 hover:text-amber-600 transition" title="Edit">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg>
                                </button>
                                <button type="button" onclick="event.stopPropagation(); openDeleteModal('modalHapusKelas', '{{ $k->id_kelas }}', '{{ addslashes($k->nama_kelas) }}')" 
                                        class="text-gray-400 hover:text-red-600 transition" title="Hapus">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-6 text-center text-gray-400">Belum ada data kelas aktif.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Footer -->
        <div class="bg-gray-50 p-4 border-t border-gray-100 flex justify-between items-center text-sm text-gray-500">
            <span>Menampilkan {{ count($kelasList) }} kelas</span>
        </div>
    </div>


    {{-- ================= VIEW 2: DATA SISWA (Tersembunyi secara default) ================= --}}
    <div id="viewSiswa" class="hidden">
        
        <!-- Tombol Kembali & Judul (Sejajar & Mepet Kanan) -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-4">
            <h2 class="text-2xl font-bold text-gray-900">Data Siswa: <span id="namaKelasTitle" class="text-emerald-700"></span></h2>
            <button onclick="hideStudents()" class="inline-flex items-center gap-2 text-sm font-medium text-[#1BA886] bg-[#1BA886]/10 px-4 py-2.5 rounded-lg hover:bg-[#1BA886] hover:text-white transition-colors !no-underline">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </button>
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
                        <select id="selectKelasTambahSiswa" name="kelas_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm outline-none transition-all bg-white">
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
            <div class="bg-gray-50/50 p-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-semibold text-gray-700">Daftar Siswa Terdaftar</h3>
                <div class="relative">
                    <svg class="w-4 h-4 absolute left-3 top-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" id="cariSiswaKelasInput" onkeyup="filterSiswaKelas()" placeholder="Cari siswa (Nama / NIS)..." class="pl-9 pr-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none w-64 transition-all">
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
                    <tbody id="tbodySiswaKelas" class="divide-y divide-gray-100 text-sm">
                        @forelse($allActiveSiswas as $s)
                            <tr class="row-siswa-item hover:bg-gray-50/50 transition-colors"
                                data-kelas-id="{{ $s->kelas_id }}"
                                data-nama="{{ strtolower($s->nama) }}"
                                data-nis="{{ $s->nis }}">
                                <td class="p-4 text-center text-gray-500 row-siswa-no"></td>
                                <td class="p-4 text-gray-900 font-medium nama-siswa-text">{{ $s->nama }}</td>
                                <td class="p-4 text-gray-500 nis-siswa-text">{{ $s->nis }}</td>
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
                        @endforelse
                        <tr id="emptySiswaRow" class="hidden">
                            <td colspan="5" class="p-6 text-center text-gray-400">Tidak ada data siswa yang cocok.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- Modal ini melayani tabel siswa yang tampil dari halaman kelas. --}}
<div id="modalEditSiswaDariKelas" class="fixed inset-0 z-50 hidden items-center justify-center overflow-y-auto bg-gray-900/50 p-4 backdrop-blur-sm">
    <div role="dialog" aria-modal="true" aria-labelledby="modalEditSiswaDariKelasTitle" class="relative w-full max-w-lg rounded-xl border border-gray-100 bg-white p-6 shadow-lg">
        <div class="mb-5 flex items-center justify-between">
            <h2 id="modalEditSiswaDariKelasTitle" class="text-xl font-bold text-gray-900">Edit Data Siswa</h2>
            <button type="button" onclick="closeEditModal('modalEditSiswaDariKelas')" aria-label="Tutup modal" class="text-gray-400 transition hover:text-gray-600">
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
                <label for="editSiswaKelasId" class="mb-1 block text-sm font-medium text-gray-700">Kelas <span class="text-red-500">*</span></label>
                <select id="editSiswaKelasId" name="kelas_id" required class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-400">
                    @foreach($kelasList as $itemKelas)
                        <option value="{{ $itemKelas->id_kelas }}">{{ $itemKelas->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex justify-end gap-3 border-t border-gray-100 pt-5">
                <button type="button" onclick="closeEditModal('modalEditSiswaDariKelas')" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50">Batal</button>
                <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-emerald-700">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

{{-- ================= MODAL EDIT KELAS ================= --}}
<div id="modalEditKelas" class="fixed inset-0 z-50 hidden items-center justify-center overflow-y-auto bg-gray-900/50 p-4 backdrop-blur-sm">
    <div role="dialog" aria-modal="true" aria-labelledby="modalEditKelasTitle" class="relative w-full max-w-lg rounded-xl border border-gray-100 bg-white p-6 shadow-lg">
        <div class="mb-5 flex items-center justify-between">
            <h2 id="modalEditKelasTitle" class="text-xl font-bold text-gray-900">Edit Data Kelas</h2>
            <button type="button" onclick="closeEditModal('modalEditKelas')" aria-label="Tutup modal" class="text-gray-400 transition hover:text-gray-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form id="formEditKelas" method="POST" action="" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label for="editKelasNama" class="mb-1 block text-sm font-medium text-gray-700">Nama Kelas <span class="text-red-500">*</span></label>
                <input id="editKelasNama" name="nama_kelas" data-edit-field="nama" required type="text" placeholder="XI RPL 1" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-400">
            </div>
            <div>
                <label for="editKelasGuru" class="mb-1 block text-sm font-medium text-gray-700">Wali Kelas / Nama Guru</label>
                <select id="editKelasGuru" name="nama_guru" data-edit-field="guru" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-400">
                    <option value="">-- Pilih Guru / Wali Kelas --</option>
                    @foreach($gurus ?? [] as $g)
                        <option value="{{ $g->name }}">{{ $g->name }} ({{ $g->nip ?? 'Guru' }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="editKelasJumlahSiswa" class="mb-1 block text-sm font-medium text-gray-700">Jumlah Siswa</label>
                <input id="editKelasJumlahSiswa" name="jumlah_siswa" data-edit-field="jumlahSiswa" type="number" min="0" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-400">
            </div>
            <div class="flex justify-end gap-3 border-t border-gray-100 pt-5">
                <button type="button" onclick="closeEditModal('modalEditKelas')" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50">Batal</button>
                <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-emerald-700">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>


{{-- ================= MODAL TAMBAH KELAS ================= --}}
<div id="modalTambahKelas" class="fixed inset-0 z-50 hidden bg-gray-900/60 backdrop-blur-sm overflow-y-auto h-full w-full items-center justify-center transition-opacity">
    <div class="bg-white rounded-xl shadow-xl border border-gray-100 p-6 max-w-md w-full mx-4 relative">
        <div class="flex justify-between items-center mb-5">
            <h2 class="text-xl font-bold text-gray-900">Tambah Kelas Baru</h2>
            <button onclick="closeModal('modalTambahKelas')" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <form action="{{ route('dashboard.kelas.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kelas <span class="text-red-500">*</span></label>
                <input type="text" name="nama_kelas" placeholder="Cth: XI RPL 1" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm outline-none transition">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Guru / Wali Kelas</label>
                <select name="nama_guru" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm outline-none transition bg-white">
                    <option value="">-- Pilih Guru / Wali Kelas --</option>
                    @foreach($gurus ?? [] as $g)
                        <option value="{{ $g->name }}">{{ $g->name }} ({{ $g->nip ?? 'Guru' }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Siswa</label>
                <input type="number" name="jumlah_siswa" min="0" placeholder="Cth: 32" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm outline-none transition">
            </div>
            <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-100">
                <button type="button" onclick="closeModal('modalTambahKelas')" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition text-sm font-medium">Batal</button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition shadow-sm text-sm font-medium">Simpan Kelas</button>
            </div>
        </form>
    </div>
</div>

{{-- ================= MODAL HAPUS KELAS ================= --}}
<div id="modalHapusKelas" class="fixed inset-0 z-50 hidden bg-gray-900/60 backdrop-blur-sm overflow-y-auto h-full w-full items-center justify-center transition-opacity">
    <div class="bg-white rounded-xl shadow-xl border border-gray-100 p-8 max-w-md w-full mx-4 text-center relative">
        <button type="button" onclick="closeModal('modalHapusKelas')" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
        </div>
        <h1 class="text-xl font-bold text-gray-900 mb-2">Hapus Data Kelas?</h1>
        <p class="text-sm text-gray-500 mb-4">Apakah Anda yakin ingin mengeluarkan kelas <strong id="deleteKelasNama" class="text-gray-800"></strong> dari sistem? Status kelas akan dinonaktifkan.</p>

        <form id="formHapusKelas" action="" method="POST" class="text-left space-y-4">
            @csrf
            @method('DELETE')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Alasan Penghapusan <span class="text-red-500">*</span></label>
                <textarea id="deleteAlasanKelas" name="alasan" rows="3" required placeholder="Tuliskan alasan mengeluarkan/menonaktifkan kelas ini..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm"></textarea>
            </div>
            <div class="flex justify-center gap-3 pt-2">
                <button type="button" onclick="closeModal('modalHapusKelas')" class="px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition text-sm font-medium w-full">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg transition shadow-sm text-sm font-medium w-full">Ya, Hapus Kelas</button>
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
    let currentActiveKelasId = null;

    // FUNGSI UNTUK PINDAH VIEW (Dari Tabel Kelas ke Tabel Siswa)
    function showStudents(className, classId) {
        currentActiveKelasId = classId;

        // Sembunyikan Tabel Kelas dan Header Utama
        document.getElementById('viewKelas').classList.add('hidden');
        document.getElementById('viewHeaderKelas').classList.add('hidden');
        
        // Tampilkan View Siswa
        document.getElementById('viewSiswa').classList.remove('hidden');
        
        // Ubah Judul Kelas di View Siswa
        document.getElementById('namaKelasTitle').innerText = className;
        
        // Otomatis pilih kelas pada select di form tambah siswa
        const selectKelas = document.getElementById('selectKelasTambahSiswa');
        if (selectKelas && classId) {
            selectKelas.value = classId;
        }

        // Reset input cari siswa
        const inputCari = document.getElementById('cariSiswaKelasInput');
        if (inputCari) {
            inputCari.value = '';
        }

        // Filter dan tampilkan hanya siswa untuk kelas ini
        filterSiswaKelas();
    }

    // FUNGSI UNTUK KEMBALI KE TABEL KELAS
    function hideStudents() {
        currentActiveKelasId = null;

        // Tampilkan Tabel Kelas dan Header Utama
        document.getElementById('viewKelas').classList.remove('hidden');
        document.getElementById('viewHeaderKelas').classList.remove('hidden');
        document.getElementById('viewHeaderKelas').classList.add('flex');
        
        // Sembunyikan View Siswa
        document.getElementById('viewSiswa').classList.add('hidden');
    }

    // FILTER SISWA INSTAN BERDASARKAN NAMA DAN NIS
    function filterSiswaKelas() {
        const query = (document.getElementById('cariSiswaKelasInput')?.value || '').toLowerCase().trim();
        const rows = document.querySelectorAll('#tbodySiswaKelas .row-siswa-item');
        const emptyRow = document.getElementById('emptySiswaRow');
        let visibleCount = 0;

        rows.forEach(row => {
            const rowKelasId = row.dataset.kelasId;
            const nama = row.dataset.nama || '';
            const nis = row.dataset.nis || '';

            const matchesClass = !currentActiveKelasId || String(rowKelasId) === String(currentActiveKelasId);
            const matchesQuery = !query || nama.includes(query) || nis.includes(query);

            if (matchesClass && matchesQuery) {
                row.style.display = '';
                visibleCount++;
                const noCell = row.querySelector('.row-siswa-no');
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
    }

    function openEditSiswaModal(id, nama, nis, kelasId) {
        const form = document.getElementById('formEditSiswa');
        form.action = "{{ url('dashboard/siswa') }}/" + id;
        document.getElementById('editSiswaNama').value = nama;
        document.getElementById('editSiswaNis').value = nis;
        const selectKelas = document.getElementById('editSiswaKelasId');
        if (selectKelas) {
            selectKelas.value = kelasId;
        }

        openModal('modalEditSiswaDariKelas');
    }

    function openDeleteSiswaModal(id, nama) {
        const form = document.getElementById('formHapusSiswa');
        form.action = "{{ url('dashboard/siswa') }}/" + id;
        document.getElementById('deleteSiswaNama').textContent = nama;
        const textarea = document.getElementById('deleteAlasanSiswa');
        if (textarea) textarea.value = '';

        openModal('modalHapusSiswa');
    }

    function openEditModal(modalId, trigger) {
        const modal = document.getElementById(modalId);
        if (modalId === 'modalEditKelas') {
            const form = document.getElementById('formEditKelas');
            const id = trigger.dataset.editId;
            form.action = "{{ url('dashboard/kelas') }}/" + id;
        }

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

    function openDeleteModal(modalId, id, name) {
        const modal = document.getElementById(modalId);
        const form = document.getElementById('formHapusKelas');
        form.action = "{{ url('dashboard/kelas') }}/" + id;
        document.getElementById('deleteKelasNama').textContent = name;
        const textarea = document.getElementById('deleteAlasanKelas');
        if (textarea) textarea.value = '';

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    // FUNGSI UNTUK MODAL UMUM
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

    // AUTO-OPEN JIKA SELESAI TAMBAH / EDIT / HAPUS SISWA ATAU PARAMETER KELAS_ID
    document.addEventListener('DOMContentLoaded', function () {
        @php
            $targetKelasId = session('open_kelas_id', request('kelas_id'));
            $targetKelas = $targetKelasId ? $kelasList->firstWhere('id_kelas', $targetKelasId) : null;
        @endphp
        @if($targetKelas)
            showStudents('{{ addslashes($targetKelas->nama_kelas) }}', '{{ $targetKelas->id_kelas }}');
        @endif
    });
</script>

@endsection
