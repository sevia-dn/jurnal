@extends('layouts.app')

@section('title', 'Dashboard Admin - Mata Pelajaran')

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

    <style>
        /* Sembunyikan segitiga/panah bawaan input browser */
        input::-webkit-calendar-picker-indicator {
            display: none !important;
            -webkit-appearance: none;
        }
    </style>

    {{-- Header Halaman --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Mata Pelajaran</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola seluruh mata pelajaran jurusan dan mapel biasa di sekolah.</p>
        </div>
        <form method="GET" action="{{ route('dashboard.mapel') }}" autocomplete="off" class="flex items-center gap-2">
            @if(request('kategori'))
                <input type="hidden" name="kategori" value="{{ request('kategori') }}">
            @endif
            <input type="text" name="search" value="{{ request('search') }}" onkeyup="filterAndSortTableMapel(this.value)" placeholder="Cari kode, nama, guru..." autocomplete="off"
                   class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-60 focus:outline-none focus:ring-2 focus:ring-emerald-400 bg-white shadow-sm">
            @if(request('search'))
                <a href="{{ route('dashboard.mapel', ['kategori' => request('kategori')]) }}" class="text-xs text-gray-500 hover:text-red-600">Reset</a>
            @endif
        </form>
    </div>

    {{-- Kategori Filter Tabs --}}
    <div class="flex flex-wrap items-center gap-2 mb-6">
        <a href="{{ route('dashboard.mapel', array_filter(['search' => request('search')])) }}"
           class="px-4 py-2 rounded-lg text-sm font-medium transition !no-underline {{ !request('kategori') ? 'bg-emerald-600 !text-white shadow-sm' : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200' }}">
            Semua Mapel ({{ $counts['total'] }})
        </a>
        <a href="{{ route('dashboard.mapel', array_filter(['kategori' => 'jurusan', 'search' => request('search')])) }}"
           class="px-4 py-2 rounded-lg text-sm font-medium transition !no-underline {{ request('kategori') === 'jurusan' ? 'bg-blue-600 !text-white shadow-sm' : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200' }}">
            <span class="inline-block w-2 h-2 rounded-full bg-blue-400 mr-1.5"></span> Mapel Jurusan ({{ $counts['jurusan'] }})
        </a>
        <a href="{{ route('dashboard.mapel', array_filter(['kategori' => 'biasa', 'search' => request('search')])) }}"
           class="px-4 py-2 rounded-lg text-sm font-medium transition !no-underline {{ in_array(request('kategori'), ['biasa', 'umum', 'pilihan']) ? 'bg-emerald-700 !text-white shadow-sm' : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200' }}">
            <span class="inline-block w-2 h-2 rounded-full bg-emerald-400 mr-1.5"></span> Mapel Biasa ({{ $counts['biasa'] }})
        </a>
    </div>

    {{-- ================= FORM TAMBAH MAPEL ================= --}}
    <div class="bg-emerald-50/70 border border-emerald-100 rounded-xl p-6 mb-8 shadow-sm">
        <h2 class="text-lg font-semibold text-emerald-900 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Tambah Mata Pelajaran Baru
        </h2>

        <form method="POST" action="{{ route('dashboard.mapel.store') }}" autocomplete="off" class="space-y-4" onsubmit="flushGuruInputBeforeSubmit('tambah')">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="flex flex-col">
                    <label class="text-sm font-medium text-gray-700 mb-1">Kode Mapel <span class="text-red-500">*</span></label>
                    <input type="text" name="kode_mapel" value="{{ old('kode_mapel') }}" required placeholder="Cth: MJ-RPL, MU-BIN" autocomplete="off"
                           class="border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm transition-all bg-white">
                </div>

                <div class="flex flex-col md:col-span-2">
                    <label class="text-sm font-medium text-gray-700 mb-1">Nama Mapel <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_mapel" value="{{ old('nama_mapel') }}" required placeholder="Cth: Rekayasa Perangkat Lunak (RPL)" autocomplete="off"
                           class="border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm transition-all bg-white">
                </div>

                <div class="flex flex-col">
                    <label class="text-sm font-medium text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                    <select id="kategoriTambah" name="kategori" required onchange="handleKategoriChange('tambah')"
                            class="border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm transition-all bg-white cursor-pointer">
                        <option value="biasa" {{ old('kategori', 'biasa') == 'biasa' ? 'selected' : '' }}>Mapel Biasa</option>
                        <option value="jurusan" {{ old('kategori') == 'jurusan' ? 'selected' : '' }}>Mapel Jurusan</option>
                    </select>
                </div>
            </div>

            {{-- Input Guru Pengampu --}}
            <div class="flex flex-col relative">
                <label class="text-sm font-medium text-gray-700 mb-1.5">Guru Pengampu</label>

                <div class="relative">
                    <div class="border border-gray-300 rounded-lg p-2.5 bg-white flex flex-wrap gap-2 items-center min-h-[48px] focus-within:ring-2 focus-within:ring-emerald-500 focus-within:border-emerald-500 transition-all shadow-sm cursor-text" onclick="document.getElementById('inputKetikTambahGuru').focus()">
                        <div id="chipsTambahGuru" class="flex flex-wrap gap-2 items-center">
                            {{-- Tag chip akan muncul di sini --}}
                        </div>
                        <input type="text" id="inputKetikTambahGuru" autocomplete="off" placeholder="Ketik nama guru..."
                               class="flex-1 min-w-[220px] text-sm outline-none px-2 py-1 text-gray-800 placeholder-gray-400 bg-transparent"
                               oninput="handleGuruInput(this, 'tambah')"
                               onfocus="handleGuruFocus('tambah')"
                               onblur="handleGuruBlur('tambah')"
                               onkeydown="handleGuruKeyDown(event, 'tambah')">
                    </div>

                    {{-- Custom Autocomplete Dropdown Menu --}}
                    <div id="dropdownGuruTambah" class="hidden absolute left-0 right-0 top-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg z-50 max-h-72 overflow-hidden flex flex-col divide-y divide-gray-100">
                    </div>
                </div>

                <input type="hidden" name="guru_names" id="hiddenGuruNamesTambah" value="">
                <p class="text-[11px] text-gray-500 mt-1 flex items-center gap-1" id="keteranganGuruTambah">
                    <i class="bi bi-info-circle text-emerald-600"></i> Ketik nama guru, pilih dari saran atau tekan Enter.
                </p>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-6 py-2.5 rounded-lg transition-colors shadow-sm inline-flex items-center gap-2">
                    <i class="bi bi-check2-circle"></i> Simpan Mapel
                </button>
            </div>
        </form>
    </div>

    {{-- ================= FLOATING BATCH ACTION BAR ================= --}}
    <div id="batchActionBarMapel" class="hidden mb-4 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex flex-wrap items-center justify-between gap-3 shadow-sm transition-all">
        <div class="flex items-center gap-2">
            <span class="w-6 h-6 rounded-full bg-emerald-600 text-white text-xs font-bold flex items-center justify-center" id="batchBadgeMapel">0</span>
            <span class="text-sm font-semibold text-emerald-900" id="batchTextMapel">0 mata pelajaran dipilih</span>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <button type="button" onclick="confirmBatchDeleteMapel()" class="px-3.5 py-2 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 rounded-xl transition shadow-2xs flex items-center gap-1.5 cursor-pointer">
                <i class="bi bi-trash-fill"></i>
                <span>Hapus Terpilih</span>
            </button>
            <button type="button" onclick="clearMapelSelections()" class="px-3 py-2 text-xs font-medium text-gray-500 hover:text-gray-700 cursor-pointer">
                Batal
            </button>
        </div>
    </div>

    {{-- ================= TABEL DAFTAR MAPEL ================= --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-gray-700 border-b border-gray-200 text-sm">
                <tr>
                    <th class="p-4 w-10 text-center">
                        <input type="checkbox" id="selectAllMapel" class="rounded text-emerald-600 focus:ring-emerald-500 cursor-pointer w-4 h-4">
                    </th>
                    <th class="p-4 w-12 text-center">No</th>
                    <th class="p-4 w-32">Kode</th>
                    <th class="p-4 w-40">Kategori</th>
                    <th class="p-4">
                        <div class="flex items-center gap-1.5">
                            <span>Nama Mapel</span>
                            <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800" title="Terurut otomatis A-Z">
                                <i class="bi bi-sort-alpha-down"></i> A-Z
                            </span>
                        </div>
                    </th>
                    <th class="p-4">Guru Pengampu</th>
                    <th class="p-4 text-center w-28">Aksi</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                    @forelse($mapels as $i => $mapel)
                        <tr class="row-mapel-item hover:bg-emerald-50/30 transition-colors">
                            <td class="p-4 text-center">
                                <input type="checkbox" value="{{ $mapel->id }}" class="mapel-checkbox rounded text-emerald-600 focus:ring-emerald-500 cursor-pointer w-4 h-4" onchange="updateBatchStateMapel()">
                            </td>
                            <td class="p-4 text-center text-gray-500">{{ $i + 1 }}</td>
                            <td class="p-4 font-mono font-medium text-emerald-800">
                                <span class="px-2.5 py-1 bg-emerald-50 rounded border border-emerald-200 text-xs">
                                    {{ $mapel->kode_mapel }}
                                </span>
                            </td>
                            <td class="p-4">
                                @if($mapel->kategori === 'jurusan')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> Mapel Jurusan
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Mapel Biasa
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 font-semibold text-gray-900">{{ $mapel->nama_mapel }}</td>
                            <td class="p-4">
                                @php
                                    $pengampus = $mapel->all_pengampus ?? $mapel->gurus ?? collect();
                                @endphp
                                @if($pengampus->count() > 0)
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($pengampus as $g)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-medium bg-slate-100 text-slate-800 border border-slate-200">
                                                <i class="bi bi-person-check text-emerald-600"></i>
                                                {{ $g->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-gray-400 italic text-xs">Belum ada guru pengampu</span>
                                @endif
                            </td>
                            <td class="p-4 text-center space-x-2">
                                <button type="button"
                                        data-id="{{ $mapel->id }}"
                                        data-kode="{{ $mapel->kode_mapel }}"
                                        data-nama="{{ $mapel->nama_mapel }}"
                                        data-kategori="{{ $mapel->kategori }}"
                                        data-pengampus='@json($pengampus->pluck("name"))'
                                        data-jadwal-gurus='@json($mapel->jadwal_gurus ?? [])'
                                        onclick="handleEditMapelBtn(this)"
                                        class="text-gray-400 hover:text-amber-600 transition" title="Edit Mapel">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg>
                                </button>
                                <button type="button"
                                        onclick="openDeleteMapelModal('{{ $mapel->id }}', '{{ addslashes($mapel->nama_mapel) }}')"
                                        class="text-gray-400 hover:text-red-600 transition" title="Hapus Mapel">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Belum ada data mata pelajaran yang sesuai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="bg-gray-50 p-4 border-t border-gray-100 flex justify-between items-center text-sm text-gray-500">
            <span>Menampilkan {{ count($mapels) }} mata pelajaran</span>
            <span class="text-xs text-gray-400">Total: {{ $counts['jurusan'] }} Mapel Jurusan &bull; {{ $counts['biasa'] }} Mapel Biasa</span>
        </div>
    </div>


    {{-- ================= MODAL EDIT MAPEL ================= --}}
    <div id="modalEditMapel" class="fixed inset-0 z-50 hidden items-center justify-center overflow-y-auto bg-gray-900/50 p-4 backdrop-blur-sm">
        <div role="dialog" aria-modal="true" aria-labelledby="modalEditMapelTitle" class="relative w-full max-w-xl rounded-xl border border-gray-100 bg-white p-6 shadow-xl">
            <div class="mb-5 flex items-center justify-between">
                <div>
                    <h2 id="modalEditMapelTitle" class="text-xl font-bold text-gray-900">Edit Mata Pelajaran</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Perbarui nama, kode, kategori, dan ketik guru pengampu.</p>
                </div>
                <button type="button" onclick="closeModal('modalEditMapel')" aria-label="Tutup modal" class="text-gray-400 transition hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="formEditMapel" method="POST" action="" autocomplete="off" class="space-y-4" onsubmit="flushGuruInputBeforeSubmit('edit')">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="editMapelKode" class="mb-1 block text-sm font-medium text-gray-700">Kode Mapel <span class="text-red-500">*</span></label>
                        <input id="editMapelKode" name="kode_mapel" type="text" required autocomplete="off" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-400">
                    </div>
                    <div>
                        <label for="editMapelKategori" class="mb-1 block text-sm font-medium text-gray-700">Kategori <span class="text-red-500">*</span></label>
                        <select id="editMapelKategori" name="kategori" required onchange="handleKategoriChange('edit')" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-400 cursor-pointer">
                            <option value="biasa">Mapel Biasa</option>
                            <option value="jurusan">Mapel Jurusan</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="editMapelNama" class="mb-1 block text-sm font-medium text-gray-700">Nama Mapel <span class="text-red-500">*</span></label>
                    <input id="editMapelNama" name="nama_mapel" type="text" required autocomplete="off" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-400">
                </div>

                <div class="flex flex-col relative">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Guru Pengampu</label>

                    {{-- Banner Rekomendasi Guru dari Jadwal Pelajaran --}}
                    <div id="rekomendasiJadwalContainer" class="hidden mb-2.5 p-2.5 bg-purple-50 border border-purple-200 rounded-xl text-xs text-purple-900 flex flex-col sm:flex-row sm:items-center justify-between gap-2 shadow-2xs">
                        <div class="flex items-center gap-2">
                            <i class="bi bi-magic text-purple-600 text-sm"></i>
                            <div>
                                <span class="font-bold">Guru Pengampu di Jadwal:</span>
                                <span id="rekomendasiJadwalNames" class="ml-1 text-purple-800"></span>
                            </div>
                        </div>
                        <button type="button" onclick="applyJadwalRekomendasi()" class="px-2.5 py-1 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-[11px] font-bold transition cursor-pointer shrink-0 self-start sm:self-auto inline-flex items-center gap-1 shadow-2xs">
                            <i class="bi bi-plus-circle"></i> Tambahkan Semua
                        </button>
                    </div>

                    <div class="relative">
                        <div class="border border-gray-300 rounded-lg p-2.5 bg-white flex flex-wrap gap-2 items-center min-h-[48px] focus-within:ring-2 focus-within:ring-emerald-500 focus-within:border-emerald-500 transition-all shadow-sm cursor-text" onclick="document.getElementById('inputKetikEditGuru').focus()">
                            <div id="chipsEditGuru" class="flex flex-wrap gap-2 items-center">
                                {{-- Chip edit guru --}}
                            </div>
                            <input type="text" id="inputKetikEditGuru" autocomplete="off" placeholder="Ketik nama guru..."
                                   class="flex-1 min-w-[220px] text-sm outline-none px-2 py-1 text-gray-800 placeholder-gray-400 bg-transparent"
                                   oninput="handleGuruInput(this, 'edit')"
                                   onfocus="handleGuruFocus('edit')"
                                   onblur="handleGuruBlur('edit')"
                                   onkeydown="handleGuruKeyDown(event, 'edit')">
                        </div>

                        {{-- Custom Autocomplete Dropdown --}}
                        <div id="dropdownGuruEdit" class="hidden absolute left-0 right-0 top-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg z-50 max-h-72 overflow-hidden flex flex-col divide-y divide-gray-100">
                        </div>
                    </div>

                    <input type="hidden" name="guru_names" id="hiddenGuruNamesEdit" value="">
                    <p class="text-[11px] text-gray-500 mt-1 flex items-center gap-1">
                        <i class="bi bi-info-circle text-emerald-600"></i> Ketik nama guru, pilih dari saran atau tekan Enter. (Dapat memilih lebih dari 1 guru).
                    </p>
                </div>

                <div class="flex justify-end gap-3 border-t border-gray-100 pt-5">
                    <button type="button" onclick="closeModal('modalEditMapel')" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50">Batal</button>
                    <button type="submit" class="rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-emerald-700">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ================= MODAL HAPUS MAPEL ================= --}}
    <div id="modalHapusMapel" class="fixed inset-0 z-50 hidden bg-gray-900/60 backdrop-blur-sm overflow-y-auto h-full w-full items-center justify-center transition-opacity">
        <div class="bg-white rounded-xl shadow-xl border border-gray-100 p-8 max-w-md w-full mx-4 text-center relative">
            
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
            <p class="text-sm text-gray-500 mb-6">Apakah Anda yakin ingin menghapus mata pelajaran <strong id="deleteMapelNama" class="text-gray-800"></strong>? Status mapel akan dinonaktifkan di sistem.</p>

            <form id="formHapusMapel" action="" method="POST" class="text-left space-y-4">
                @csrf
                @method('DELETE')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alasan Penghapusan (Opsional)</label>
                    <textarea id="deleteAlasanMapel" name="alasan" rows="3" placeholder="Tuliskan alasan menghapus mata pelajaran ini..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm"></textarea>
                </div>
                
                <div class="flex justify-center gap-3 pt-4">
                    <button type="button" onclick="closeModal('modalHapusMapel')" class="px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition text-sm font-medium w-full">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg transition shadow-sm text-sm font-medium w-full">Ya, Hapus Mapel</button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
    const allGuruList = @json($guruJson ?? []);
    let guruTagsTambah = [];
    let guruTagsEdit = [];
    let currentEditingMapelId = null;
    let currentEditingMapelJadwalGurus = [];
    let guruFilterTab = {
        'tambah': 'semua',
        'edit': 'semua'
    };

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function getMaxGuru(type) {
        return 25; // Izinkan banyak guru pengampu baik mapel biasa maupun kejuruan
    }

    function handleKategoriChange(type) {
        renderGuruChips(type);
    }

    function setGuruFilterTab(tab, type) {
        guruFilterTab[type] = tab;
        const input = document.getElementById(type === 'tambah' ? 'inputKetikTambahGuru' : 'inputKetikEditGuru');
        if (input) {
            handleGuruInput(input, type);
            input.focus();
        }
    }

    function renderGuruChips(type) {
        const isTambah = type === 'tambah';
        const list = isTambah ? guruTagsTambah : guruTagsEdit;
        const container = document.getElementById(isTambah ? 'chipsTambahGuru' : 'chipsEditGuru');
        const hiddenInput = document.getElementById(isTambah ? 'hiddenGuruNamesTambah' : 'hiddenGuruNamesEdit');
        const input = document.getElementById(isTambah ? 'inputKetikTambahGuru' : 'inputKetikEditGuru');
        const max = getMaxGuru(type);
        
        if (!container || !hiddenInput) return;

        container.innerHTML = '';
        list.forEach((name, idx) => {
            const chip = document.createElement('span');
            chip.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-300 shadow-2xs';
            chip.innerHTML = `
                <i class="bi bi-person-check text-emerald-600"></i>
                <span>${escapeHtml(name)}</span>
                <button type="button" onclick="removeGuruChip('${type}', ${idx})" class="text-emerald-700 hover:text-red-600 transition font-bold text-sm leading-none ml-1 cursor-pointer" title="Hapus guru">&times;</button>
            `;
            container.appendChild(chip);
        });

        hiddenInput.value = JSON.stringify(list);

        // Update banner rekomendasi jika di modal edit
        if (!isTambah && currentEditingMapelJadwalGurus && currentEditingMapelJadwalGurus.length > 0) {
            const missing = currentEditingMapelJadwalGurus.filter(jn => !list.some(n => n.toLowerCase() === jn.toLowerCase()));
            const banner = document.getElementById('rekomendasiJadwalContainer');
            const namesSpan = document.getElementById('rekomendasiJadwalNames');
            if (banner && namesSpan) {
                if (missing.length > 0) {
                    namesSpan.textContent = missing.join(', ');
                    banner.classList.remove('hidden');
                } else {
                    banner.classList.add('hidden');
                }
            }
        }

        if (input) {
            if (list.length >= max) {
                input.value = '';
                input.placeholder = 'Batas guru pengampu tercapai';
                input.disabled = true;
                input.classList.add('cursor-not-allowed', 'opacity-60');
                closeGuruDropdown(type);
            } else {
                input.placeholder = 'Ketik nama guru...';
                input.disabled = false;
                input.classList.remove('cursor-not-allowed', 'opacity-60');
            }
        }
    }

    function handleGuruFocus(type) {
        const input = document.getElementById(type === 'tambah' ? 'inputKetikTambahGuru' : 'inputKetikEditGuru');
        if (input && !input.disabled) {
            handleGuruInput(input, type);
        }
    }

    function handleGuruBlur(type) {
        setTimeout(() => {
            closeGuruDropdown(type);
        }, 250);
    }

    function closeGuruDropdown(type) {
        const dropdown = document.getElementById(type === 'tambah' ? 'dropdownGuruTambah' : 'dropdownGuruEdit');
        if (dropdown) dropdown.classList.add('hidden');
    }

    function handleGuruInput(input, type) {
        const query = (input.value || '').trim().toLowerCase();
        const list = (type === 'tambah') ? guruTagsTambah : guruTagsEdit;
        const dropdown = document.getElementById(type === 'tambah' ? 'dropdownGuruTambah' : 'dropdownGuruEdit');
        const max = getMaxGuru(type);
        const activeTab = guruFilterTab[type] || 'semua';
        const editingId = (type === 'edit') ? currentEditingMapelId : null;
        const jadwalList = (type === 'edit') ? currentEditingMapelJadwalGurus : [];

        if (!dropdown || list.length >= max) {
            if (dropdown) dropdown.classList.add('hidden');
            return;
        }

        const totalBelumAdaMapel = allGuruList.filter(g => !g.mapel_name).length;
        const totalJadwal = allGuruList.filter(g => jadwalList.some(jn => jn.toLowerCase() === g.name.toLowerCase())).length;

        // Filter kandidat
        let candidates = allGuruList.filter(g => {
            const alreadySelected = list.some(selectedName => selectedName.toLowerCase() === g.name.toLowerCase());
            if (alreadySelected) return false;

            if (activeTab === 'belum' && g.mapel_name) return false;
            if (activeTab === 'jadwal' && !jadwalList.some(jn => jn.toLowerCase() === g.name.toLowerCase())) return false;

            if (!query) return true;
            return g.name.toLowerCase().includes(query) || (g.nip && g.nip.toLowerCase().includes(query));
        });

        // Urutkan kandidat: 1) Dari jadwal mapel ini, 2) Belum ada mapel, 3) Alfabetis
        candidates.sort((a, b) => {
            const aInJadwal = jadwalList.some(jn => jn.toLowerCase() === a.name.toLowerCase()) ? 1 : 0;
            const bInJadwal = jadwalList.some(jn => jn.toLowerCase() === b.name.toLowerCase()) ? 1 : 0;
            if (aInJadwal !== bInJadwal) return bInJadwal - aInJadwal;

            const aNoMapel = !a.mapel_name ? 1 : 0;
            const bNoMapel = !b.mapel_name ? 1 : 0;
            if (aNoMapel !== bNoMapel) return bNoMapel - aNoMapel;

            return a.name.localeCompare(b.name);
        });

        // Tabs Header di dalam Dropdown
        const tabsHtml = `
            <div class="sticky top-0 bg-slate-50 border-b border-slate-200 px-3 py-2 flex items-center justify-between gap-1 text-[11px] font-semibold text-slate-600 z-10">
                <span class="text-slate-400 text-[10px] uppercase tracking-wider font-bold">Filter:</span>
                <div class="flex items-center gap-1">
                    <button type="button" onmousedown="event.preventDefault(); setGuruFilterTab('semua', '${type}')" class="px-2 py-0.5 rounded-md transition cursor-pointer ${activeTab === 'semua' ? 'bg-emerald-600 text-white shadow-2xs font-bold' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100'}">Semua</button>
                    <button type="button" onmousedown="event.preventDefault(); setGuruFilterTab('belum', '${type}')" class="px-2 py-0.5 rounded-md transition cursor-pointer ${activeTab === 'belum' ? 'bg-emerald-600 text-white shadow-2xs font-bold' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100'}">Belum Ada Mapel (${totalBelumAdaMapel})</button>
                    ${(jadwalList.length > 0) ? `<button type="button" onmousedown="event.preventDefault(); setGuruFilterTab('jadwal', '${type}')" class="px-2 py-0.5 rounded-md transition cursor-pointer ${activeTab === 'jadwal' ? 'bg-purple-600 text-white shadow-2xs font-bold' : 'bg-white border border-purple-200 text-purple-700 hover:bg-purple-50'}"><i class="bi bi-star-fill text-[9px]"></i> Dari Jadwal (${totalJadwal})</button>` : ''}
                </div>
            </div>
        `;

        if (candidates.length === 0) {
            dropdown.innerHTML = tabsHtml + '<div class="px-4 py-4 text-xs text-gray-400 text-center">Tidak ada guru yang sesuai filter</div>';
            dropdown.classList.remove('hidden');
            return;
        }

        const itemsHtml = candidates.slice(0, 15).map(g => {
            const isJadwal = jadwalList.some(jn => jn.toLowerCase() === g.name.toLowerCase());
            let badgeHtml = '';
            if (isJadwal) {
                badgeHtml = '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-800 border border-purple-200 shrink-0"><i class="bi bi-star-fill text-[9px] text-purple-600"></i> Mengajar di Jadwal</span>';
            } else if (!g.mapel_name) {
                badgeHtml = '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0"><i class="bi bi-check-circle-fill text-[10px] text-emerald-600"></i> Belum ada mapel</span>';
            } else if (editingId && g.mapel_id == editingId) {
                badgeHtml = '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200 shrink-0">Mapel Ini</span>';
            } else {
                badgeHtml = `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-600 border border-slate-200 shrink-0 max-w-[150px] truncate" title="Sudah mengampu: ${escapeHtml(g.mapel_name)}">Mengampu: ${escapeHtml(g.mapel_name)}</span>`;
            }

            return `
                <button type="button" onmousedown="selectGuruSuggestion('${escapeHtml(g.name).replace(/'/g, "\\'")}', '${type}')" class="w-full text-left px-4 py-2.5 hover:bg-emerald-50 transition flex items-center justify-between text-sm group cursor-pointer border-b border-gray-50 last:border-0">
                    <div class="flex items-center gap-2 min-w-0 pr-2">
                        <div class="w-7 h-7 rounded-full bg-slate-100 group-hover:bg-emerald-100 text-slate-600 group-hover:text-emerald-700 flex items-center justify-center shrink-0 text-xs">
                            <i class="bi bi-person"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="font-medium text-gray-800 group-hover:text-emerald-800 truncate text-xs sm:text-sm">${escapeHtml(g.name)}</div>
                            ${g.nip ? `<div class="text-[10px] text-gray-400 font-mono">${escapeHtml(g.nip)}</div>` : ''}
                        </div>
                    </div>
                    ${badgeHtml}
                </button>
            `;
        }).join('');

        dropdown.innerHTML = tabsHtml + `<div class="divide-y divide-gray-50 max-h-60 overflow-y-auto">${itemsHtml}</div>`;
        dropdown.classList.remove('hidden');
    }

    function selectGuruSuggestion(name, type) {
        const list = (type === 'tambah') ? guruTagsTambah : guruTagsEdit;
        const max = getMaxGuru(type);

        if (list.length >= max) {
            alert('Batas maksimal guru pengampu adalah 25 guru per mata pelajaran.');
            return;
        }

        if (!list.some(n => n.toLowerCase() === name.toLowerCase())) {
            list.push(name);
        }

        const input = document.getElementById(type === 'tambah' ? 'inputKetikTambahGuru' : 'inputKetikEditGuru');
        if (input) {
            input.value = '';
            if (list.length < max) {
                input.focus();
            }
        }

        closeGuruDropdown(type);
        renderGuruChips(type);
    }

    function applyJadwalRekomendasi() {
        if (!currentEditingMapelJadwalGurus || currentEditingMapelJadwalGurus.length === 0) return;
        currentEditingMapelJadwalGurus.forEach(name => {
            if (!guruTagsEdit.some(n => n.toLowerCase() === name.toLowerCase())) {
                guruTagsEdit.push(name);
            }
        });
        renderGuruChips('edit');
        const banner = document.getElementById('rekomendasiJadwalContainer');
        if (banner) banner.classList.add('hidden');
    }

    function handleGuruKeyDown(event, type) {
        const input = event.target;
        const val = (input.value || '').trim();
        const list = (type === 'tambah') ? guruTagsTambah : guruTagsEdit;

        if (event.key === 'Enter') {
            event.preventDefault();
            if (val) {
                const match = allGuruList.find(g => g.name.toLowerCase() === val.toLowerCase())
                    || allGuruList.find(g => !list.includes(g.name) && g.name.toLowerCase().includes(val.toLowerCase()));
                if (match) {
                    selectGuruSuggestion(match.name, type);
                } else {
                    alert(`Guru "${val}" tidak terdaftar di data guru sekolah.`);
                }
            }
        } else if (event.key === 'Backspace' && !val && list.length > 0) {
            list.pop();
            renderGuruChips(type);
        }
    }

    function flushGuruInputBeforeSubmit(type) {
        const isTambah = type === 'tambah';
        const input = document.getElementById(isTambah ? 'inputKetikTambahGuru' : 'inputKetikEditGuru');
        if (input && input.value.trim()) {
            const val = input.value.trim();
            const list = isTambah ? guruTagsTambah : guruTagsEdit;
            const match = allGuruList.find(g => g.name.toLowerCase() === val.toLowerCase());
            if (match && !list.includes(match.name) && list.length < getMaxGuru(type)) {
                list.push(match.name);
                renderGuruChips(type);
            }
        }
    }

    function removeGuruChip(type, index) {
        const list = type === 'tambah' ? guruTagsTambah : guruTagsEdit;
        list.splice(index, 1);
        renderGuruChips(type);
        const input = document.getElementById(type === 'tambah' ? 'inputKetikTambahGuru' : 'inputKetikEditGuru');
        if (input && !input.disabled) {
            input.focus();
        }
    }

    function handleEditMapelBtn(btn) {
        const id = btn.getAttribute('data-id');
        const kode = btn.getAttribute('data-kode');
        const nama = btn.getAttribute('data-nama');
        const kategori = btn.getAttribute('data-kategori');
        let guruNames = [];
        try {
            guruNames = JSON.parse(btn.getAttribute('data-pengampus') || '[]');
        } catch(e) {
            guruNames = [];
        }
        let jadwalGurus = [];
        try {
            jadwalGurus = JSON.parse(btn.getAttribute('data-jadwal-gurus') || '[]');
        } catch(e) {
            jadwalGurus = [];
        }
        openEditMapelModal(id, kode, nama, kategori, guruNames, jadwalGurus);
    }

    function openEditMapelModal(id, kode, nama, kategori, guruNames, jadwalGurus) {
        const form = document.getElementById('formEditMapel');
        form.action = "{{ url('dashboard/mapel') }}/" + id;
        document.getElementById('editMapelKode').value = kode;
        document.getElementById('editMapelNama').value = nama;
        
        const kat = (kategori === 'jurusan') ? 'jurusan' : 'biasa';
        document.getElementById('editMapelKategori').value = kat;
        
        currentEditingMapelId = id;
        currentEditingMapelJadwalGurus = Array.isArray(jadwalGurus) ? jadwalGurus : [];
        guruFilterTab['edit'] = 'semua';

        guruTagsEdit = Array.isArray(guruNames) ? [...guruNames] : [];
        renderGuruChips('edit');

        openModal('modalEditMapel');
    }

    function openDeleteMapelModal(id, nama) {
        const form = document.getElementById('formHapusMapel');
        form.action = "{{ url('dashboard/mapel') }}/" + id;
        document.getElementById('deleteMapelNama').textContent = nama;
        const textarea = document.getElementById('deleteAlasanMapel');
        if (textarea) {
            textarea.value = '';
        }

        openModal('modalHapusMapel');
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

    // Inisialisasi chips tambah saat halaman selesai dimuat
    document.addEventListener('DOMContentLoaded', function() {
        handleKategoriChange('tambah');
    });

    // ================= BATCH ACTION MAPEL =================
    const selectAllMapelEl = document.getElementById('selectAllMapel');
    const checkboxesMapel = () => document.querySelectorAll('.mapel-checkbox');
    const batchBarMapel = document.getElementById('batchActionBarMapel');
    const batchBadgeMapel = document.getElementById('batchBadgeMapel');
    const batchTextMapel = document.getElementById('batchTextMapel');

    if (selectAllMapelEl) {
        selectAllMapelEl.addEventListener('change', function() {
            checkboxesMapel().forEach(cb => {
                cb.checked = selectAllMapelEl.checked;
            });
            updateBatchStateMapel();
        });
    }

    function updateBatchStateMapel() {
        const selected = Array.from(checkboxesMapel()).filter(cb => cb.checked);
        const count = selected.length;

        if (count > 0) {
            batchBarMapel.classList.remove('hidden');
            batchBadgeMapel.innerText = count;
            batchTextMapel.innerText = count + ' mata pelajaran dipilih';
        } else {
            batchBarMapel.classList.add('hidden');
            if (selectAllMapelEl) selectAllMapelEl.checked = false;
        }
    }

    function clearMapelSelections() {
        checkboxesMapel().forEach(cb => cb.checked = false);
        if (selectAllMapelEl) selectAllMapelEl.checked = false;
        updateBatchStateMapel();
    }

    function confirmBatchDeleteMapel() {
        const selected = Array.from(checkboxesMapel()).filter(cb => cb.checked).map(cb => cb.value);
        if (selected.length === 0) return;

        if (confirm(`Yakin ingin menghapus ${selected.length} mata pelajaran terpilih?`)) {
            const container = document.getElementById('batchDeleteMapelContainer');
            container.innerHTML = '';
            selected.forEach(id => {
                const inp = document.createElement('input');
                inp.type = 'hidden';
                inp.name = 'ids[]';
                inp.value = id;
                container.appendChild(inp);
            });
            document.getElementById('formBatchDeleteMapel').submit();
        }
    }

    // Live search & sort A-Z handler
    function filterAndSortTableMapel(term) {
        const tableBody = document.querySelector('table tbody');
        if (!tableBody) return;
        const rows = Array.from(tableBody.querySelectorAll('tr.row-mapel-item'));
        const lowerTerm = (term || '').toLowerCase().trim();

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (!lowerTerm || text.includes(lowerTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });

        // Re-sort visible rows alphabetically by nama_mapel A-Z
        rows.sort((a, b) => {
            const namaA = (a.querySelector('td:nth-child(5)')?.innerText || '').trim();
            const namaB = (b.querySelector('td:nth-child(5)')?.innerText || '').trim();
            return namaA.localeCompare(namaB, 'id', { sensitivity: 'base' });
        });

        let visibleIndex = 1;
        rows.forEach(row => {
            if (row.style.display !== 'none') {
                const noCell = row.querySelector('td:nth-child(2)');
                if (noCell) noCell.textContent = visibleIndex;
                visibleIndex++;
            }
            tableBody.appendChild(row);
        });
    }
</script>

<form id="formBatchDeleteMapel" action="{{ route('dashboard.mapel.batch-delete') }}" method="POST" class="hidden">
    @csrf
    <div id="batchDeleteMapelContainer"></div>
</form>
@endsection
