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

    @php
        $targetKelasId = session('open_kelas_id', request('kelas_id'));
        $targetKelas = $targetKelasId ? $kelasList->firstWhere('id_kelas', $targetKelasId) : null;
        $isSiswaViewOpen = !is_null($targetKelas);
    @endphp

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

    {{-- Toast Notifikasi AJAX (Realtime Tambah Siswa) --}}
    <div id="ajaxToastNotification" class="hidden mb-6 items-center justify-between rounded-lg bg-emerald-50 border border-emerald-200 p-4 text-sm text-emerald-800 transition-all">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span id="ajaxToastMessage"></span>
        </div>
        <button type="button" onclick="document.getElementById('ajaxToastNotification').classList.add('hidden')" class="text-emerald-500 hover:text-emerald-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <div id="ajaxErrorNotification" class="hidden mb-6 rounded-lg bg-red-50 border border-red-200 p-4 text-sm text-red-800 transition-all">
        <div class="flex items-center justify-between">
            <div>
                <div class="font-semibold mb-1">Gagal menambahkan siswa:</div>
                <div id="ajaxErrorMessage" class="text-red-700"></div>
            </div>
            <button type="button" onclick="document.getElementById('ajaxErrorNotification').classList.add('hidden')" class="text-red-500 hover:text-red-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>

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
    <div id="viewHeaderKelas" class="{{ $isSiswaViewOpen ? 'hidden' : 'flex' }} flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Data Kelas</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola informasi kelas, nama guru, dan jumlah siswa aktif.</p>
        </div>
        <div class="flex items-center gap-2.5 flex-wrap">
            {{-- Search Bar Kelas --}}
            <form method="GET" action="{{ route('dashboard.kelas') }}" autocomplete="off" class="flex items-center gap-1">
                <div class="relative">
                    <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                    <input type="text" name="search" value="{{ request('search') }}" onkeyup="filterAndSortTableKelas(this.value)" placeholder="Cari kelas, wali..." autocomplete="off"
                           class="border border-gray-300 rounded-xl pl-8 pr-3 py-2 text-xs w-48 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                </div>
                @if(request('search'))
                    <a href="{{ route('dashboard.kelas') }}" class="text-xs text-rose-500 hover:underline px-1">Reset</a>
                @endif
            </form>

            <button onclick="openModal('modalTambahKelas')" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg flex items-center gap-2 text-sm font-medium transition shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Tambah Kelas Baru
            </button>
        </div>
    </div>

    {{-- ================= FLOATING BATCH ACTION BAR ================= --}}
    <div id="batchActionBarKelas" class="hidden mb-4 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex flex-wrap items-center justify-between gap-3 shadow-sm transition-all">
        <div class="flex items-center gap-2">
            <span class="w-6 h-6 rounded-full bg-emerald-600 text-white text-xs font-bold flex items-center justify-center" id="batchBadgeKelas">0</span>
            <span class="text-sm font-semibold text-emerald-900" id="batchTextKelas">0 kelas dipilih</span>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <button type="button" onclick="confirmBatchDeleteKelas()" class="px-3.5 py-2 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 rounded-xl transition shadow-2xs flex items-center gap-1.5 cursor-pointer">
                <i class="bi bi-trash-fill"></i>
                <span>Hapus Terpilih</span>
            </button>
            <button type="button" onclick="clearKelasSelections()" class="px-3 py-2 text-xs font-medium text-gray-500 hover:text-gray-700 cursor-pointer">
                Batal
            </button>
        </div>
    </div>

    {{-- ================= VIEW 1: DATA KELAS (Tabel Utama) ================= --}}
    <div id="viewKelas" class="{{ $isSiswaViewOpen ? 'hidden' : '' }} bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-gray-700 border-b border-gray-200 text-sm">
                <tr>
                    <th class="p-4 w-10 text-center">
                        <input type="checkbox" id="selectAllKelas" class="rounded text-emerald-600 focus:ring-emerald-500 cursor-pointer w-4 h-4">
                    </th>
                    <th class="p-4 w-16 text-center">No</th>
                    <th class="p-4">
                        <div class="flex items-center gap-1.5">
                            <span>Nama Kelas</span>
                            <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800" title="Terurut otomatis A-Z">
                                <i class="bi bi-sort-alpha-down"></i> A-Z
                            </span>
                        </div>
                    </th>
                    <th class="p-4">Nama Guru</th>
                    <th class="p-4">Jumlah siswa</th>
                    <th class="p-4 text-center w-24">Aksi</th>
                </tr>
            </thead>
                
                <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                    @forelse($kelasList as $index => $k)
                        <tr onclick="showStudents('{{ addslashes($k->nama_kelas) }}', '{{ $k->id_kelas }}')" class="hover:bg-emerald-50/30 cursor-pointer transition">
                            <td class="p-4 text-center" onclick="event.stopPropagation();">
                                <input type="checkbox" value="{{ $k->id_kelas }}" class="kelas-checkbox rounded text-emerald-600 focus:ring-emerald-500 cursor-pointer w-4 h-4" onchange="updateBatchStateKelas()">
                            </td>
                            <td class="p-4 text-center">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</td>
                            <td class="p-4 font-medium text-gray-900">{{ $k->nama_kelas }}</td>
                            <td class="p-4">{{ $k->wali_kelas ?? '-' }}</td>
                            <td class="p-4" id="kelas-jumlah-{{ $k->id_kelas }}">{{ $k->siswas_count ?: ($k->jumlah_siswa ?? 0) }}</td>
                            <td class="p-4 text-center space-x-2">
                                <button type="button" onclick="event.stopPropagation(); openEditModal('modalEditKelas', this)" 
                                        data-edit-id="{{ $k->id_kelas }}" 
                                        data-edit-nama="{{ $k->nama_kelas }}" 
                                        data-edit-guru="{{ $k->wali_kelas }}" 
                                        data-edit-jumlah-siswa="{{ $k->siswas_count ?: ($k->jumlah_siswa ?? 0) }}" 
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


    {{-- ================= VIEW 2: DATA SISWA ================= --}}
    <div id="viewSiswa" class="{{ $isSiswaViewOpen ? '' : 'hidden' }}">
        
        <!-- Tombol Kembali & Judul (Sejajar & Mepet Kanan) -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-4">
            <h2 class="text-2xl font-bold text-gray-900">Data Siswa: <span id="namaKelasTitle" class="text-emerald-700">{{ $targetKelas ? $targetKelas->nama_kelas : '' }}</span></h2>
            <button onclick="hideStudents()" class="inline-flex items-center gap-2 text-sm font-medium text-[#1BA886] bg-[#1BA886]/10 px-4 py-2.5 rounded-lg hover:bg-[#1BA886] hover:text-white transition-colors !no-underline cursor-pointer">
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
            
            <form id="formTambahSiswaKelas" action="{{ route('dashboard.siswa.store') }}" method="POST" onsubmit="handleTambahSiswaAjax(event)" autocomplete="off" class="space-y-4">
                @csrf
                <input type="hidden" name="from_kelas" value="1">
                
                <!-- Grid 4 kolom untuk Nama, NIS, Kelas, dan Jenis Kelamin -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    
                    <!-- Input Nama -->
                    <div class="w-full">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" id="tambahNamaSiswa" name="nama_siswa" required placeholder="Contoh: Ahmad Fauzi" autocomplete="off" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm outline-none transition-all">
                    </div>
                    
                    <!-- Input NISN -->
                    <div class="w-full">
                        <label class="block text-sm font-medium text-gray-700 mb-1">NISN <span class="text-red-500">*</span></label>
                        <input type="text" id="tambahNisSiswa" name="nisn" required placeholder="Contoh: 0105292765" autocomplete="off" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm outline-none transition-all">
                    </div>
                    
                    <!-- Select Kelas -->
                    <div class="w-full">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kelas <span class="text-red-500">*</span></label>
                        <select id="selectKelasTambahSiswa" name="kelas_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm outline-none transition-all bg-white">
                            <option value="" disabled {{ !$targetKelas ? 'selected' : '' }}>-- Pilih Kelas --</option>
                            @foreach($kelasList as $itemKelas)
                                <option value="{{ $itemKelas->id_kelas }}" {{ ($targetKelas && $targetKelas->id_kelas == $itemKelas->id_kelas) ? 'selected' : '' }}>{{ $itemKelas->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Select Jenis Kelamin -->
                    <div class="w-full">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                        <select id="selectJkTambahSiswa" name="jenis_kelamin" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm outline-none transition-all bg-white">
                            <option value="L">Laki-laki (L)</option>
                            <option value="P">Perempuan (P)</option>
                        </select>
                    </div>

                </div>
                
                <!-- Tombol Simpan di Bawah (Pojok Kanan) -->
                <div class="flex justify-end pt-2">
                    <button type="submit" id="btnSubmitTambahSiswa" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-colors shadow-sm text-sm font-medium flex items-center gap-2 cursor-pointer">
                        <span>Simpan</span>
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
                    <input type="text" id="cariSiswaKelasInput" onkeyup="filterSiswaKelas()" placeholder="Cari siswa (Nama / NISN)..." autocomplete="off" class="pl-9 pr-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none w-64 transition-all">
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-emerald-50/50 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="p-4 w-16 text-center">No</th>
                            <th class="p-4">
                                <div class="flex items-center gap-1.5">
                                    <span>Nama Siswa</span>
                                    <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800" title="Terurut otomatis A-Z">
                                        <i class="bi bi-sort-alpha-down"></i> A-Z
                                    </span>
                                </div>
                            </th>
                            <th class="p-4">NISN</th>
                            <th class="p-4">Kelas</th>
                            <th class="p-4 text-center w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tbodySiswaKelas" class="divide-y divide-gray-100 text-sm">
                        @php
                            $ssrVisibleIndex = 0;
                        @endphp
                        @forelse($allActiveSiswas as $s)
                            @php
                                $isMatchCurrent = ($isSiswaViewOpen && $targetKelas && (string)$s->kelas_id === (string)$targetKelas->id_kelas);
                                if ($isMatchCurrent) {
                                    $ssrVisibleIndex++;
                                }
                            @endphp
                            <tr class="row-siswa-item hover:bg-gray-50/50 transition-colors"
                                data-id="{{ $s->id }}"
                                data-kelas-id="{{ $s->kelas_id }}"
                                data-nama="{{ strtolower($s->nama) }}"
                                data-nis="{{ $s->nisn }}"
                                style="{{ $isMatchCurrent ? '' : 'display: none;' }}">
                                <td class="p-4 text-center text-gray-500 row-siswa-no">{{ $isMatchCurrent ? $ssrVisibleIndex : '' }}</td>
                                <td class="p-4 text-gray-900 font-medium nama-siswa-text">{{ $s->nama }}</td>
                                <td class="p-4 text-gray-500 nis-siswa-text">{{ $s->nisn }}</td>
                                <td class="p-4">
                                    <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 rounded-md font-medium text-xs">{{ $s->kelas->nama_kelas ?? '-' }}</span>
                                </td>
                                <td class="p-4 text-center space-x-2">
                                    <button type="button" onclick="openEditSiswaModal('{{ $s->id }}', '{{ addslashes($s->nama) }}', '{{ $s->nisn }}', '{{ $s->kelas_id }}', '{{ $s->jenis_kelamin ?? 'L' }}')" class="text-gray-400 hover:text-amber-600 transition" title="Edit">
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

        <form id="formEditSiswa" method="POST" action="" onsubmit="handleEditSiswaAjax(event)" autocomplete="off" class="space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="from_kelas" value="1">
            <div>
                <label for="editSiswaNama" class="mb-1 block text-sm font-medium text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                <input id="editSiswaNama" name="nama" type="text" required autocomplete="off" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-400">
            </div>
            <div>
                <label for="editSiswaNis" class="mb-1 block text-sm font-medium text-gray-700">NISN <span class="text-red-500">*</span></label>
                <input id="editSiswaNis" name="nisn" type="text" required autocomplete="off" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-400">
            </div>
            <div>
                <label for="editSiswaKelasId" class="mb-1 block text-sm font-medium text-gray-700">Kelas <span class="text-red-500">*</span></label>
                <select id="editSiswaKelasId" name="kelas_id" required class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-400">
                    @foreach($kelasList as $itemKelas)
                        <option value="{{ $itemKelas->id_kelas }}">{{ $itemKelas->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="editSiswaJenisKelamin" class="mb-1 block text-sm font-medium text-gray-700">Jenis Kelamin <span class="text-red-500">*</span></label>
                <select id="editSiswaJenisKelamin" name="jenis_kelamin" required class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-400">
                    <option value="L">Laki-laki (L)</option>
                    <option value="P">Perempuan (P)</option>
                </select>
            </div>
            <div class="flex justify-end gap-3 border-t border-gray-100 pt-5">
                <button type="button" onclick="closeEditModal('modalEditSiswaDariKelas')" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 cursor-pointer">Batal</button>
                <button type="submit" id="btnSubmitEditSiswa" class="rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-emerald-700 flex items-center gap-2 cursor-pointer">
                    <span>Simpan Perubahan</span>
                </button>
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

        <form id="formEditKelas" method="POST" action="" autocomplete="off" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label for="editKelasNama" class="mb-1 block text-sm font-medium text-gray-700">Nama Kelas <span class="text-red-500">*</span></label>
                <input id="editKelasNama" name="nama_kelas" data-edit-field="nama" required type="text" placeholder="XI RPL 1" autocomplete="off" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-400">
            </div>
            <div>
                <label for="editKelasGuru" class="mb-1 block text-sm font-medium text-gray-700">Wali Kelas / Nama Guru</label>
                <div class="relative">
                    <input id="editKelasGuru" name="wali_kelas" data-edit-field="guru" list="listGuruWali" oninput="validateWaliGuru(this, 'edit')" type="text" placeholder="Ketik nama guru wali kelas..." autocomplete="off" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-400">
                </div>
                <div id="editWaliStatus" class="mt-1.5 text-xs font-medium min-h-[18px]"></div>
            </div>
            <div>
                <label for="editKelasJumlahSiswa" class="mb-1 block text-sm font-medium text-gray-700">Jumlah Siswa</label>
                <input id="editKelasJumlahSiswa" name="jumlah_siswa" data-edit-field="jumlahSiswa" type="number" min="0" autocomplete="off" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-400">
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
        
        <form action="{{ route('dashboard.kelas.store') }}" method="POST" autocomplete="off" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kelas <span class="text-red-500">*</span></label>
                <input type="text" name="nama_kelas" placeholder="Cth: XI RPL 1 (Hanya Kelas 10 & 11)" required autocomplete="off" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm outline-none transition">
                <p class="text-[11px] text-gray-400 mt-1">Catatan: Kelas 12 sedang PKL, hanya kelas 10 dan 11 yang aktif di jurnal.</p>
            </div>
            <div>
                <label for="tambahKelasGuru" class="block text-sm font-medium text-gray-700 mb-1">Nama Guru / Wali Kelas</label>
                <div class="relative">
                    <input id="tambahKelasGuru" name="wali_kelas" list="listGuruWali" oninput="validateWaliGuru(this, 'tambah')" type="text" placeholder="Ketik nama guru wali kelas..." autocomplete="off" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm outline-none transition">
                </div>
                <div id="tambahWaliStatus" class="mt-1.5 text-xs font-medium min-h-[18px]"></div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Siswa</label>
                <input type="number" name="jumlah_siswa" min="0" placeholder="Cth: 32" autocomplete="off" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm outline-none transition">
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
            <input type="hidden" name="from_kelas" value="1">
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
    let currentActiveKelasId = {{ $targetKelas ? "'" . $targetKelas->id_kelas . "'" : 'null' }};

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

        // Sinkronkan URL agar saat reload tetap berada di kelas ini
        if (window.history && window.history.replaceState) {
            const url = new URL(window.location);
            url.searchParams.set('kelas_id', classId);
            window.history.replaceState({}, '', url);
        }
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

        // Hapus parameter kelas_id dari URL
        if (window.history && window.history.replaceState) {
            const url = new URL(window.location);
            url.searchParams.delete('kelas_id');
            window.history.replaceState({}, '', url);
        }
    }

    // TAMBAH SISWA VIA AJAX (TANPA RELOAD/FLASH KE DAFTAR KELAS)
    async function handleTambahSiswaAjax(event) {
        event.preventDefault();
        const form = event.target;
        const btnSubmit = document.getElementById('btnSubmitTambahSiswa');
        const namaInput = document.getElementById('tambahNamaSiswa');
        const nisInput = document.getElementById('tambahNisSiswa');
        
        // Sembunyikan notifikasi lama
        const toastEl = document.getElementById('ajaxToastNotification');
        const errorEl = document.getElementById('ajaxErrorNotification');
        if (toastEl) toastEl.classList.add('hidden');
        if (errorEl) errorEl.classList.add('hidden');

        const originalBtnHtml = btnSubmit ? btnSubmit.innerHTML : 'Simpan';
        if (btnSubmit) {
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = `<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> Menyimpan...`;
        }

        try {
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (!response.ok) {
                let errorMsg = 'Gagal menambahkan siswa.';
                if (data.errors) {
                    errorMsg = Object.values(data.errors).flat().join('<br>');
                } else if (data.message) {
                    errorMsg = data.message;
                }
                if (errorEl) {
                    document.getElementById('ajaxErrorMessage').innerHTML = errorMsg;
                    errorEl.classList.remove('hidden');
                } else {
                    alert(errorMsg);
                }
                return;
            }

            if (data.success && data.siswa) {
                const siswa = data.siswa;
                const tbody = document.getElementById('tbodySiswaKelas');
                const emptyRow = document.getElementById('emptySiswaRow');

                // Buat baris baru untuk tabel siswa
                const tr = document.createElement('tr');
                tr.className = 'row-siswa-item hover:bg-gray-50/50 transition-colors';
                tr.dataset.id = siswa.id;
                tr.dataset.kelasId = siswa.kelas_id;
                tr.dataset.nama = (siswa.nama || '').toLowerCase();
                tr.dataset.nis = siswa.nisn || siswa.nis || '';

                const escapedNama = (siswa.nama || '').replace(/'/g, "\\'");

                tr.innerHTML = `
                    <td class="p-4 text-center text-gray-500 row-siswa-no"></td>
                    <td class="p-4 text-gray-900 font-medium nama-siswa-text">${siswa.nama}</td>
                    <td class="p-4 text-gray-500 nis-siswa-text">${siswa.nisn || siswa.nis}</td>
                    <td class="p-4">
                        <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 rounded-md font-medium text-xs">${siswa.nama_kelas}</span>
                    </td>
                    <td class="p-4 text-center space-x-2">
                        <button type="button" onclick="openEditSiswaModal('${siswa.id}', '${escapedNama}', '${siswa.nisn || siswa.nis}', '${siswa.kelas_id}', '${siswa.jenis_kelamin || 'L'}')" class="text-gray-400 hover:text-amber-600 transition cursor-pointer" title="Edit">
                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                            </svg>
                        </button>
                        <button type="button" onclick="openDeleteSiswaModal('${siswa.id}', '${escapedNama}')" class="text-gray-400 hover:text-red-600 transition cursor-pointer" title="Hapus">
                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </td>
                `;

                if (emptyRow) {
                    tbody.insertBefore(tr, emptyRow);
                } else {
                    tbody.appendChild(tr);
                }

                // Re-sort dan perbarui penomoran otomatis A-Z
                filterSiswaKelas();

                // Update jumlah siswa di tabel kelas
                const countCell = document.getElementById('kelas-jumlah-' + siswa.kelas_id);
                if (countCell) {
                    const currentCount = parseInt(countCell.innerText) || 0;
                    countCell.innerText = currentCount + 1;
                }

                // Kosongkan input nama dan NISN (tetap pertahankan kelas)
                if (namaInput) namaInput.value = '';
                if (nisInput) nisInput.value = '';
                if (namaInput) namaInput.focus();

                // Tampilkan toast notifikasi berhasil
                if (toastEl) {
                    document.getElementById('ajaxToastMessage').innerText = data.message || `Data siswa ${siswa.nama} berhasil ditambahkan!`;
                    toastEl.classList.remove('hidden');
                    toastEl.classList.add('flex');
                    setTimeout(() => {
                        toastEl.classList.add('hidden');
                        toastEl.classList.remove('flex');
                    }, 5000);
                }
            }
        } catch (err) {
            console.error(err);
            alert('Terjadi kesalahan saat memproses data.');
        } finally {
            if (btnSubmit) {
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = originalBtnHtml;
            }
        }
    }

    // EDIT SISWA VIA AJAX (TANPA RELOAD/FLASH)
    async function handleEditSiswaAjax(event) {
        event.preventDefault();
        const form = event.target;
        const btnSubmit = document.getElementById('btnSubmitEditSiswa');
        const toastEl = document.getElementById('ajaxToastNotification');
        const errorEl = document.getElementById('ajaxErrorNotification');
        if (toastEl) toastEl.classList.add('hidden');
        if (errorEl) errorEl.classList.add('hidden');

        const originalBtnHtml = btnSubmit ? btnSubmit.innerHTML : 'Simpan Perubahan';
        if (btnSubmit) {
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = `<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> Menyimpan...`;
        }

        try {
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (!response.ok) {
                let errorMsg = 'Gagal memperbarui data siswa.';
                if (data.errors) {
                    errorMsg = Object.values(data.errors).flat().join('\n');
                } else if (data.message) {
                    errorMsg = data.message;
                }
                alert(errorMsg);
                return;
            }

            if (data.success && data.siswa) {
                const s = data.siswa;
                const row = document.querySelector(`.row-siswa-item[data-id="${s.id}"]`);
                if (row) {
                    row.dataset.nama = (s.nama || '').toLowerCase();
                    row.dataset.nis = s.nisn || s.nis || '';
                    const oldKelasId = row.dataset.kelasId;
                    row.dataset.kelasId = s.kelas_id;

                    const namaEl = row.querySelector('.nama-siswa-text');
                    if (namaEl) namaEl.textContent = s.nama;

                    const nisEl = row.querySelector('.nis-siswa-text');
                    if (nisEl) nisEl.textContent = s.nisn || s.nis;

                    const badgeEl = row.querySelector('td:nth-child(4) span');
                    if (badgeEl && s.nama_kelas) badgeEl.textContent = s.nama_kelas;

                    const escapedNama = (s.nama || '').replace(/'/g, "\\'");
                    const editBtn = row.querySelector('button[onclick*="openEditSiswaModal"]');
                    if (editBtn) {
                        editBtn.setAttribute('onclick', `openEditSiswaModal('${s.id}', '${escapedNama}', '${s.nisn || s.nis}', '${s.kelas_id}', '${s.jenis_kelamin || 'L'}')`);
                    }
                    const delBtn = row.querySelector('button[onclick*="openDeleteSiswaModal"]');
                    if (delBtn) {
                        delBtn.setAttribute('onclick', `openDeleteSiswaModal('${s.id}', '${escapedNama}')`);
                    }

                    if (oldKelasId && String(oldKelasId) !== String(s.kelas_id)) {
                        const oldCountCell = document.getElementById('kelas-jumlah-' + oldKelasId);
                        if (oldCountCell) oldCountCell.innerText = Math.max(0, (parseInt(oldCountCell.innerText) || 0) - 1);
                        const newCountCell = document.getElementById('kelas-jumlah-' + s.kelas_id);
                        if (newCountCell) newCountCell.innerText = (parseInt(newCountCell.innerText) || 0) + 1;
                    }
                }

                closeEditModal('modalEditSiswaDariKelas');
                filterSiswaKelas();

                if (toastEl) {
                    document.getElementById('ajaxToastMessage').innerText = data.message || `Data siswa ${s.nama} berhasil diperbarui!`;
                    toastEl.classList.remove('hidden');
                    toastEl.classList.add('flex');
                    setTimeout(() => {
                        toastEl.classList.add('hidden');
                        toastEl.classList.remove('flex');
                    }, 5000);
                }
            }
        } catch (err) {
            console.error(err);
            alert('Terjadi kesalahan saat memproses data.');
        } finally {
            if (btnSubmit) {
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = originalBtnHtml;
            }
        }
    }

    // FILTER SISWA INSTAN BERDASARKAN NAMA DAN NIS DENGAN SORT A-Z
    function filterSiswaKelas() {
        const query = (document.getElementById('cariSiswaKelasInput')?.value || '').toLowerCase().trim();
        const tbody = document.getElementById('tbodySiswaKelas');
        const rows = Array.from(document.querySelectorAll('#tbodySiswaKelas .row-siswa-item'));
        const emptyRow = document.getElementById('emptySiswaRow');
        let visibleCount = 0;

        // Sort all rows alphabetically by nama A-Z
        rows.sort((a, b) => {
            const namaA = (a.dataset.nama || '').trim();
            const namaB = (b.dataset.nama || '').trim();
            return namaA.localeCompare(namaB, 'id', { sensitivity: 'base' });
        });

        rows.forEach(row => {
            const rowKelasId = row.dataset.kelasId;
            const nama = row.dataset.nama || '';
            const nis = row.dataset.nis || '';

            const matchesClass = Boolean(currentActiveKelasId) && String(rowKelasId) === String(currentActiveKelasId);
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
            if (tbody) tbody.appendChild(row);
        });

        if (emptyRow && tbody) {
            tbody.appendChild(emptyRow);
            emptyRow.style.display = visibleCount === 0 ? '' : 'none';
        }
    }

    // LIVE SEARCH & SORT A-Z KELAS
    function filterAndSortTableKelas(term) {
        const tableBody = document.querySelector('#viewKelas table tbody');
        if (!tableBody) return;
        const rows = Array.from(tableBody.querySelectorAll('tr[onclick*="showStudents"]'));
        const lowerTerm = (term || '').toLowerCase().trim();

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (!lowerTerm || text.includes(lowerTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });

        // Re-sort visible rows alphabetically by nama_kelas A-Z
        rows.sort((a, b) => {
            const namaA = (a.querySelector('td:nth-child(3)')?.innerText || '').trim();
            const namaB = (b.querySelector('td:nth-child(3)')?.innerText || '').trim();
            return namaA.localeCompare(namaB, 'id', { numeric: true, sensitivity: 'base' });
        });

        let visibleIndex = 1;
        rows.forEach(row => {
            if (row.style.display !== 'none') {
                const noCell = row.querySelector('td:nth-child(2)');
                if (noCell) noCell.textContent = String(visibleIndex).padStart(2, '0');
                visibleIndex++;
            }
            tableBody.appendChild(row);
        });
    }

    function openEditSiswaModal(id, nama, nisn, kelasId, jenisKelamin) {
        const form = document.getElementById('formEditSiswa');
        form.action = "{{ url('dashboard/siswa') }}/" + id;
        document.getElementById('editSiswaNama').value = nama;
        document.getElementById('editSiswaNis').value = nisn;
        const selectKelas = document.getElementById('editSiswaKelasId');
        if (selectKelas) {
            selectKelas.value = kelasId;
        }
        const selectJk = document.getElementById('editSiswaJenisKelamin');
        if (selectJk) {
            selectJk.value = jenisKelamin || 'L';
        }

        const btnSubmit = document.getElementById('btnSubmitEditSiswa');
        if (btnSubmit) btnSubmit.disabled = false;

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

    const daftarNamaGuru = @json(($gurus ?? collect())->pluck('name'));

    function validateWaliGuru(input, mode) {
        const statusEl = document.getElementById(mode + 'WaliStatus');
        const val = (input.value || '').trim();
        if (!val) {
            if (statusEl) statusEl.innerHTML = '';
            return;
        }

        const match = daftarNamaGuru.find(name => name.toLowerCase() === val.toLowerCase());
        if (match) {
            if (statusEl) {
                statusEl.innerHTML = `<span class="text-emerald-600 font-semibold flex items-center gap-1"><i class="bi bi-check-circle-fill"></i> Guru terdaftar: ${match}</span>`;
            }
        } else {
            if (statusEl) {
                statusEl.innerHTML = `<span class="text-rose-600 font-semibold flex items-center gap-1"><i class="bi bi-exclamation-triangle-fill"></i> Guru tidak ada</span>`;
            }
        }
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

        if (modalId === 'modalEditKelas') {
            const editWaliInput = document.getElementById('editKelasGuru');
            if (editWaliInput) {
                validateWaliGuru(editWaliInput, 'edit');
            }
        }

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
    if (currentActiveKelasId) {
        filterSiswaKelas();
    }
    document.addEventListener('DOMContentLoaded', function () {
        if (currentActiveKelasId) {
            filterSiswaKelas();
        }
    });

    // ================= BATCH ACTION KELAS =================
    const selectAllKelasEl = document.getElementById('selectAllKelas');
    const checkboxesKelas = () => document.querySelectorAll('.kelas-checkbox');
    const batchBarKelas = document.getElementById('batchActionBarKelas');
    const batchBadgeKelas = document.getElementById('batchBadgeKelas');
    const batchTextKelas = document.getElementById('batchTextKelas');

    if (selectAllKelasEl) {
        selectAllKelasEl.addEventListener('change', function() {
            checkboxesKelas().forEach(cb => {
                cb.checked = selectAllKelasEl.checked;
            });
            updateBatchStateKelas();
        });
    }

    function updateBatchStateKelas() {
        const selected = Array.from(checkboxesKelas()).filter(cb => cb.checked);
        const count = selected.length;

        if (count > 0) {
            batchBarKelas.classList.remove('hidden');
            batchBadgeKelas.innerText = count;
            batchTextKelas.innerText = count + ' kelas dipilih';
        } else {
            batchBarKelas.classList.add('hidden');
            if (selectAllKelasEl) selectAllKelasEl.checked = false;
        }
    }

    function clearKelasSelections() {
        checkboxesKelas().forEach(cb => cb.checked = false);
        if (selectAllKelasEl) selectAllKelasEl.checked = false;
        updateBatchStateKelas();
    }

    function confirmBatchDeleteKelas() {
        const selected = Array.from(checkboxesKelas()).filter(cb => cb.checked).map(cb => cb.value);
        if (selected.length === 0) return;

        if (confirm(`Yakin ingin menghapus ${selected.length} kelas terpilih? Data siswa dan jadwal terkait kelas ini dapat terpengaruh.`)) {
            const container = document.getElementById('batchDeleteKelasContainer');
            container.innerHTML = '';
            selected.forEach(id => {
                const inp = document.createElement('input');
                inp.type = 'hidden';
                inp.name = 'ids[]';
                inp.value = id;
                container.appendChild(inp);
            });
            document.getElementById('formBatchDeleteKelas').submit();
        }
    }
</script>

<form id="formBatchDeleteKelas" action="{{ route('dashboard.kelas.batch-delete') }}" method="POST" class="hidden">
    @csrf
    <div id="batchDeleteKelasContainer"></div>
</form>

<datalist id="listGuruWali">
    @foreach($gurus ?? [] as $g)
        <option value="{{ $g->name }}">{{ $g->name }} ({{ $g->nip ?? 'Guru' }})</option>
    @endforeach
</datalist>

@endsection
