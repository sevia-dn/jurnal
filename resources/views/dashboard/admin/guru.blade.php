@extends('layouts.app')

@section('title', 'Manajemen Data Guru - JurnalKita')

@section('sidebar')
    @include('layouts.admin.sidebar')
@endsection

@section('navbar')
    @include('layouts.admin.navbar')
@endsection

@section('content')

<div class="p-6 sm:p-10 font-sans">

    {{-- Alert Notifikasi --}}
    @if(session('success'))
        <div class="mb-6 flex items-center justify-between rounded-xl bg-emerald-50 border border-emerald-200 p-4 text-sm text-emerald-800 shadow-xs">
            <div class="flex items-center gap-2">
                <i class="bi bi-check-circle-fill text-emerald-600 text-lg"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 cursor-pointer">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 flex items-center justify-between rounded-xl bg-rose-50 border border-rose-200 p-4 text-sm text-rose-800 shadow-xs">
            <div class="flex items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill text-rose-600 text-lg"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 cursor-pointer">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 rounded-xl bg-rose-50 border border-rose-200 p-4 text-sm text-rose-800 shadow-xs">
            <div class="font-semibold mb-1 flex items-center gap-2">
                <i class="bi bi-exclamation-circle-fill text-rose-600"></i>
                <span>Terjadi kesalahan input:</span>
            </div>
            <ul class="list-disc list-inside space-y-1 text-rose-700 text-xs ml-4">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Header Halaman --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Data Guru</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola data pengajar, import via Excel, dan edit masal (batch action).</p>
        </div>
        <div class="flex items-center gap-2.5 flex-wrap">
            {{-- Tombol Download Template Excel --}}
            <a href="{{ route('dashboard.guru.download-template') }}" class="px-3.5 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition shadow-2xs flex items-center gap-1.5 !no-underline" title="Unduh format file Excel/CSV">
                <i class="bi bi-download text-sm text-emerald-600"></i>
                <span>Template Excel</span>
            </a>

            {{-- Tombol Import Guru (Excel) --}}
            <button type="button" onclick="openModal('modalImportGuru')" class="px-4 py-2 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl transition shadow-2xs flex items-center gap-1.5 cursor-pointer">
                <i class="bi bi-file-earmark-excel-fill text-sm"></i>
                <span>Import Guru</span>
            </button>

            {{-- Search Bar --}}
            <form method="GET" action="{{ route('dashboard.guru') }}" autocomplete="off" class="flex items-center gap-1">
                <div class="relative">
                    <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                    <input type="text" name="search" value="{{ request('search') }}" onkeyup="filterAndSortTableGuru(this.value)" placeholder="Cari NIP, nama..." autocomplete="off"
                           class="border border-gray-300 rounded-xl pl-8 pr-3 py-2 text-xs w-48 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                </div>
                @if(request('search'))
                    <a href="{{ route('dashboard.guru') }}" class="text-xs text-rose-500 hover:underline px-1">Reset</a>
                @endif
            </form>
        </div>
    </div>

    {{-- ================= FORM TAMBAH GURU BARU ================= --}}
    <div class="bg-white border border-gray-200 rounded-2xl shadow-xs p-6 mb-6">
        <h2 class="text-base font-bold text-gray-800 mb-4 flex items-center gap-2">
            <i class="bi bi-person-plus-fill text-emerald-600"></i>
            <span>Tambah Data Guru Baru</span>
        </h2>

        <form method="POST" action="{{ route('dashboard.guru.store') }}" autocomplete="off" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-start">
            @csrf
            <div class="flex flex-col">
                <label class="text-xs font-medium text-gray-700 mb-1">NIP (Opsional)</label>
                <input type="text" name="nip" value="{{ old('nip') }}" placeholder="Contoh: 198501012010011001" autocomplete="off"
                       class="w-full h-10 border border-gray-300 rounded-xl px-3 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-400">
            </div>

            <div class="flex flex-col">
                <label class="text-xs font-medium text-gray-700 mb-1">Nama Guru <span class="text-rose-500">*</span></label>
                <input type="text" name="nama" value="{{ old('nama') }}" required placeholder="Contoh: Budi Santoso, S.Pd" autocomplete="off"
                       class="w-full h-10 border border-gray-300 rounded-xl px-3 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-400">
            </div>

            <div class="flex flex-col">
                <label class="text-xs font-medium text-gray-700 mb-1">Mata Pelajaran (Opsional)</label>
                <select name="mapel_id"
                        class="w-full h-10 border border-gray-300 rounded-xl px-3 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-400 bg-white cursor-pointer">
                    <option value="">-- Pilih Mata Pelajaran --</option>
                    @foreach($mapels ?? [] as $mapel)
                        <option value="{{ $mapel->id }}" {{ old('mapel_id') == $mapel->id ? 'selected' : '' }}>
                            {{ $mapel->nama_mapel }} ({{ $mapel->kode_mapel }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col">
                <label class="text-xs font-medium text-gray-700 mb-1">No. HP / WhatsApp</label>
                <input type="text" name="no_hp" value="{{ old('no_hp') }}" placeholder="Contoh: 081234567890" autocomplete="off"
                       class="w-full h-10 border border-gray-300 rounded-xl px-3 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-400">
            </div>

            <div class="md:col-span-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pt-1">
                <p class="text-[11px] text-gray-500 flex items-center gap-1.5">
                    <i class="bi bi-info-circle-fill text-emerald-600"></i>
                    <span>Mata pelajaran utama bersifat opsional. Guru dapat mengampu banyak mapel otomatis mengikuti jadwal KBM.</span>
                </p>
                <button type="submit"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-5 py-2.5 rounded-xl transition shadow-xs cursor-pointer flex items-center gap-1.5 shrink-0">
                    <i class="bi bi-plus-lg"></i>
                    <span>Simpan Guru Baru</span>
                </button>
            </div>
        </form>
    </div>

    {{-- ================= FLOATING/STICKY BATCH ACTION BAR ================= --}}
    <div id="batchActionBar" class="hidden mb-4 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex flex-wrap items-center justify-between gap-3 shadow-sm transition-all">
        <div class="flex items-center gap-2">
            <span class="w-6 h-6 rounded-full bg-emerald-600 text-white text-xs font-bold flex items-center justify-center" id="batchCountBadge">0</span>
            <span class="text-sm font-semibold text-emerald-900" id="batchCountText">0 guru dipilih</span>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            {{-- Batch Edit Mapel Dropdown / Button --}}
            <button type="button" onclick="openModal('modalBatchEditMapel')" class="px-3.5 py-2 text-xs font-semibold text-emerald-800 bg-white border border-emerald-300 hover:bg-emerald-100 rounded-xl transition shadow-2xs flex items-center gap-1.5 cursor-pointer">
                <i class="bi bi-pencil-square text-emerald-700"></i>
                <span>Ubah Mapel Terpilih</span>
            </button>

            {{-- Batch Delete Button --}}
            <button type="button" onclick="confirmBatchDelete()" class="px-3.5 py-2 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 rounded-xl transition shadow-2xs flex items-center gap-1.5 cursor-pointer">
                <i class="bi bi-trash-fill"></i>
                <span>Hapus Terpilih</span>
            </button>

            {{-- Batal Pilihan --}}
            <button type="button" onclick="clearAllSelections()" class="px-3 py-2 text-xs font-medium text-gray-500 hover:text-gray-700 cursor-pointer">
                Batal
            </button>
        </div>
    </div>

    {{-- ================= TABEL DAFTAR GURU ================= --}}
    <div class="bg-white border border-gray-200 rounded-2xl shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50/80 text-gray-700 border-b border-gray-200 text-xs uppercase tracking-wider font-semibold">
                    <tr>
                        <th class="px-4 py-3.5 w-12 text-center">
                            <input type="checkbox" id="selectAllGuru" class="rounded text-emerald-600 focus:ring-emerald-500 cursor-pointer w-4 h-4">
                        </th>
                        <th class="px-4 py-3.5">NIP</th>
                        <th class="px-4 py-3.5">
                            <div class="flex items-center gap-1.5">
                                <span>Nama Guru</span>
                                <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800" title="Terurut otomatis A-Z">
                                    <i class="bi bi-sort-alpha-down"></i> A-Z
                                </span>
                            </div>
                        </th>
                        <th class="px-4 py-3.5">Mata Pelajaran</th>
                        <th class="px-4 py-3.5">No. HP</th>
                        <th class="px-4 py-3.5 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($users ?? [] as $user)
                        <tr class="hover:bg-gray-50/60 transition-colors" id="row-guru-{{ $user->id }}">
                            <td class="px-4 py-3.5 text-center">
                                <input type="checkbox" value="{{ $user->id }}" class="guru-checkbox rounded text-emerald-600 focus:ring-emerald-500 cursor-pointer w-4 h-4" onchange="updateBatchState()">
                            </td>
                            <td class="px-4 py-3.5 font-mono text-xs text-gray-600">{{ $user->nip ?? '-' }}</td>
                            <td class="px-4 py-3.5 flex items-center gap-2.5">
                                <span class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-800 text-xs flex items-center justify-center font-bold shrink-0">
                                    {{ collect(explode(' ', $user->name))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->implode('') }}
                                </span>
                                <span class="font-semibold text-gray-900">{{ $user->name }}</span>
                            </td>
                            <td class="px-4 py-3.5">
                                @php
                                    $mapelList = $user->all_mapel_names ?? ($user->mapel ? [$user->mapel->nama_mapel] : []);
                                @endphp
                                @if(!empty($mapelList))
                                    <div class="flex flex-wrap gap-1.5 max-w-xs">
                                        @foreach($mapelList as $mName)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                <i class="bi bi-book-half text-emerald-600 text-[10px]"></i>
                                                <span>{{ $mName }}</span>
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-gray-400 italic text-xs">- Belum ditentukan -</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-xs text-gray-600 font-mono">{{ $user->no_hp ?? '-' }}</td>
                            <td class="px-4 py-3.5 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    {{-- Edit Button --}}
                                    <button type="button" onclick="openEditGuruModal({{ $user->id }}, '{{ addslashes($user->nip ?? '') }}', '{{ addslashes($user->name) }}', '{{ $user->mapel_id ?? '' }}', '{{ addslashes($user->no_hp ?? '') }}')"
                                            class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition" title="Edit Guru">
                                        <i class="bi bi-pencil-square text-base"></i>
                                    </button>

                                    {{-- Delete Button --}}
                                    <button type="button" onclick="openDeleteModal('{{ route('dashboard.guru.destroy', $user->id) }}', '{{ addslashes($user->name) }}')"
                                            class="p-1.5 text-gray-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Hapus Guru">
                                        <i class="bi bi-trash text-base"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-gray-400 text-sm">
                                <i class="bi bi-inbox text-3xl block mb-2 text-gray-300"></i>
                                Belum ada data guru yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- ================= MODAL IMPORT GURU (EXCEL) ================= --}}
<div id="modalImportGuru" class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/60 backdrop-blur-xs p-4">
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 max-w-lg w-full p-6 relative animate-in fade-in zoom-in duration-150">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                    <i class="bi bi-file-earmark-excel-fill text-lg"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">Import Data Guru (Excel / CSV)</h3>
                    <p class="text-xs text-gray-500">Unggah file spreadsheet untuk menambahkan guru masal.</p>
                </div>
            </div>
            <button type="button" onclick="closeModal('modalImportGuru')" class="text-gray-400 hover:text-gray-600 p-1 cursor-pointer">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form action="{{ route('dashboard.guru.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            
            <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-600 leading-relaxed">
                <div class="font-bold text-slate-800 mb-1 flex items-center gap-1.5">
                    <i class="bi bi-info-circle-fill text-emerald-600"></i>
                    <span>Panduan Format Kolom:</span>
                </div>
                <p>Gunakan format kolom header pada baris pertama:</p>
                <div class="mt-2 font-mono bg-white p-2 rounded-lg border border-slate-200 text-slate-700">
                    nip, nama, mapel, no_hp
                </div>
                <div class="mt-2 text-right">
                    <a href="{{ route('dashboard.guru.download-template') }}" class="text-emerald-700 font-bold hover:underline inline-flex items-center gap-1">
                        <i class="bi bi-download"></i> Unduh Contoh File Template (.csv)
                    </a>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Pilih File Spreadsheet <span class="text-rose-500">*</span></label>
                <input type="file" name="file" required accept=".xlsx,.xls,.csv"
                       class="w-full text-xs text-gray-700 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-600 file:text-white hover:file:bg-emerald-700 file:cursor-pointer border border-gray-300 rounded-xl cursor-pointer">
                <span class="text-[11px] text-gray-400 mt-1 block">Mendukung format .xlsx, .xls, atau .csv (Maksimal 10 MB).</span>
            </div>

            <div class="flex justify-end gap-2.5 pt-3 border-t border-gray-100">
                <button type="button" onclick="closeModal('modalImportGuru')" class="px-4 py-2.5 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl transition shadow-xs flex items-center gap-1.5 cursor-pointer">
                    <i class="bi bi-cloud-arrow-up-fill"></i>
                    <span>Proses Import</span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ================= MODAL BATCH EDIT MAPEL ================= --}}
<div id="modalBatchEditMapel" class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/60 backdrop-blur-xs p-4">
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 max-w-md w-full p-6 relative">
        <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-100">
            <h3 class="text-base font-bold text-gray-900">Ubah Mata Pelajaran Terpilih</h3>
            <button type="button" onclick="closeModal('modalBatchEditMapel')" class="text-gray-400 hover:text-gray-600 p-1 cursor-pointer">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form id="formBatchEdit" action="{{ route('dashboard.guru.batch-edit') }}" method="POST" class="space-y-4">
            @csrf
            <div id="batchEditIdsContainer"></div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Pilih Mata Pelajaran Baru <span class="text-rose-500">*</span></label>
                <select name="mapel_id" required class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-xs focus:ring-2 focus:ring-emerald-400 focus:outline-none bg-white">
                    <option value="" disabled selected>-- Pilih Mapel --</option>
                    @foreach($mapels ?? [] as $mapel)
                        <option value="{{ $mapel->id }}">{{ $mapel->nama_mapel }} ({{ $mapel->kode_mapel }})</option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-end gap-2.5 pt-3 border-t border-gray-100">
                <button type="button" onclick="closeModal('modalBatchEditMapel')" class="px-4 py-2 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-xs cursor-pointer">
                    Terapkan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ================= FORM HIDDEN UNTUK BATCH DELETE ================= --}}
<form id="formBatchDelete" action="{{ route('dashboard.guru.batch-delete') }}" method="POST" class="hidden">
    @csrf
    <div id="batchDeleteIdsContainer"></div>
</form>

{{-- ================= MODAL EDIT GURU ================= --}}
<div id="modalEditGuru" class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/60 backdrop-blur-xs p-4">
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 max-w-lg w-full p-6 relative">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
            <h3 class="text-base font-bold text-gray-900">Edit Data Guru</h3>
            <button type="button" onclick="closeModal('modalEditGuru')" class="text-gray-400 hover:text-gray-600 p-1 cursor-pointer">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form id="formEditGuru" method="POST" action="" autocomplete="off" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">NIP</label>
                    <input type="text" id="editNip" name="nip" autocomplete="off" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-xs focus:ring-2 focus:ring-emerald-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">No. HP</label>
                    <input type="text" id="editNoHp" name="no_hp" autocomplete="off" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-xs focus:ring-2 focus:ring-emerald-400 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Guru <span class="text-rose-500">*</span></label>
                <input type="text" id="editNama" name="nama" required autocomplete="off" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-xs focus:ring-2 focus:ring-emerald-400 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Mata Pelajaran (Opsional)</label>
                <select id="editMapelId" name="mapel_id" class="w-full h-10 border border-gray-300 rounded-xl px-3 text-xs focus:ring-2 focus:ring-emerald-400 focus:outline-none bg-white cursor-pointer">
                    <option value="">-- Pilih Mata Pelajaran --</option>
                    @foreach($mapels ?? [] as $mapel)
                        <option value="{{ $mapel->id }}">{{ $mapel->nama_mapel }} ({{ $mapel->kode_mapel }})</option>
                    @endforeach
                </select>
                <p class="text-[11px] text-gray-500 mt-1 flex items-center gap-1.5">
                    <i class="bi bi-info-circle-fill text-emerald-600"></i>
                    <span>Mata pelajaran utama. Guru otomatis dapat mengampu mapel lain sesuai jadwal KBM.</span>
                </p>
            </div>

            <div class="flex justify-end gap-2.5 pt-3 border-t border-gray-100">
                <button type="button" onclick="closeModal('modalEditGuru')" class="px-4 py-2 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-xs cursor-pointer">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ================= MODAL KONFIRMASI HAPUS SINGLE GURU ================= --}}
<div id="modalHapusSingle" class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/60 backdrop-blur-xs p-4">
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 max-w-sm w-full p-6 text-center relative">
        <div class="w-12 h-12 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto mb-3 text-xl">
            <i class="bi bi-trash-fill"></i>
        </div>
        <h4 class="text-base font-bold text-gray-900 mb-1">Hapus Data Guru?</h4>
        <p class="text-xs text-gray-500 mb-5">Apakah Anda yakin ingin menghapus data guru <span id="deleteGuruName" class="font-bold text-gray-800"></span>? Tindakan ini tidak dapat dibatalkan.</p>

        <form id="formDeleteSingle" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="flex justify-center gap-2">
                <button type="button" onclick="closeModal('modalHapusSingle')" class="px-4 py-2 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl w-full cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl w-full shadow-xs cursor-pointer">
                    Ya, Hapus
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Modal Helpers
    function openModal(id) {
        const m = document.getElementById(id);
        if (m) {
            m.classList.remove('hidden');
            m.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeModal(id) {
        const m = document.getElementById(id);
        if (m) {
            m.classList.add('hidden');
            m.classList.remove('flex');
            document.body.style.overflow = 'auto';
        }
    }

    function openEditGuruModal(id, nip, nama, mapelId, noHp) {
        document.getElementById('formEditGuru').action = '/dashboard/guru/' + id;
        document.getElementById('editNip').value = nip;
        document.getElementById('editNama').value = nama;
        document.getElementById('editMapelId').value = mapelId;
        document.getElementById('editNoHp').value = noHp;
        openModal('modalEditGuru');
    }

    function openDeleteModal(actionUrl, name) {
        document.getElementById('formDeleteSingle').action = actionUrl;
        document.getElementById('deleteGuruName').innerText = name;
        openModal('modalHapusSingle');
    }

    // ================= BATCH (BULK) ACTION LOGIC =================
    const selectAllEl = document.getElementById('selectAllGuru');
    const checkboxes = () => document.querySelectorAll('.guru-checkbox');
    const batchBar = document.getElementById('batchActionBar');
    const batchBadge = document.getElementById('batchCountBadge');
    const batchText = document.getElementById('batchCountText');

    if (selectAllEl) {
        selectAllEl.addEventListener('change', function() {
            checkboxes().forEach(cb => {
                cb.checked = selectAllEl.checked;
            });
            updateBatchState();
        });
    }

    function updateBatchState() {
        const selected = Array.from(checkboxes()).filter(cb => cb.checked);
        const count = selected.length;

        if (count > 0) {
            batchBar.classList.remove('hidden');
            batchBadge.innerText = count;
            batchText.innerText = count + ' guru dipilih';
        } else {
            batchBar.classList.add('hidden');
            if (selectAllEl) selectAllEl.checked = false;
        }
    }

    function clearAllSelections() {
        checkboxes().forEach(cb => cb.checked = false);
        if (selectAllEl) selectAllEl.checked = false;
        updateBatchState();
    }

    function getSelectedIds() {
        return Array.from(checkboxes()).filter(cb => cb.checked).map(cb => cb.value);
    }

    function confirmBatchDelete() {
        const ids = getSelectedIds();
        if (ids.length === 0) return;

        if (confirm(`Yakin ingin menghapus ${ids.length} data guru yang dipilih? Tindakan ini tidak dapat dibatalkan.`)) {
            const container = document.getElementById('batchDeleteIdsContainer');
            container.innerHTML = '';
            ids.forEach(id => {
                const inp = document.createElement('input');
                inp.type = 'hidden';
                inp.name = 'ids[]';
                inp.value = id;
                container.appendChild(inp);
            });
            document.getElementById('formBatchDelete').submit();
        }
    }

    // Sambungkan IDs ke modal batch edit mapel
    const formBatchEdit = document.getElementById('formBatchEdit');
    if (formBatchEdit) {
        formBatchEdit.addEventListener('submit', function() {
            const ids = getSelectedIds();
            const container = document.getElementById('batchEditIdsContainer');
            container.innerHTML = '';
            ids.forEach(id => {
                const inp = document.createElement('input');
                inp.type = 'hidden';
                inp.name = 'ids[]';
                inp.value = id;
                container.appendChild(inp);
            });
        });
    }

    // Live search & sort A-Z handler
    function filterAndSortTableGuru(term) {
        const tableBody = document.querySelector('table tbody');
        if (!tableBody) return;
        const rows = Array.from(tableBody.querySelectorAll('tr[id^="row-guru-"]'));
        const lowerTerm = (term || '').toLowerCase().trim();

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (!lowerTerm || text.includes(lowerTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });

        // Re-sort visible rows alphabetically by name A-Z
        rows.sort((a, b) => {
            const nameA = (a.querySelector('td:nth-child(3)')?.innerText || '').trim();
            const nameB = (b.querySelector('td:nth-child(3)')?.innerText || '').trim();
            return nameA.localeCompare(nameB, 'id', { sensitivity: 'base' });
        });

        rows.forEach(row => tableBody.appendChild(row));
    }
</script>

@endsection
