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

    {{-- Datalist Autocomplete Guru --}}
    <datalist id="listGuruSuggestions">
        @foreach($gurus as $guru)
            <option value="{{ $guru->name }}">
        @endforeach
    </datalist>

    {{-- Header Halaman --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Mata Pelajaran</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola seluruh mata pelajaran jurusan dan mapel biasa di sekolah.</p>
        </div>
        <form method="GET" action="{{ route('dashboard.mapel') }}" class="flex items-center gap-2">
            @if(request('kategori'))
                <input type="hidden" name="kategori" value="{{ request('kategori') }}">
            @endif
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode, nama, guru..."
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

        <form method="POST" action="{{ route('dashboard.mapel.store') }}" class="space-y-4" onsubmit="flushGuruInputBeforeSubmit('tambah')">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="flex flex-col">
                    <label class="text-sm font-medium text-gray-700 mb-1">Kode Mapel <span class="text-red-500">*</span></label>
                    <input type="text" name="kode_mapel" value="{{ old('kode_mapel') }}" required placeholder="Cth: MJ-RPL, MU-BIN"
                           class="border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm transition-all bg-white">
                </div>

                <div class="flex flex-col md:col-span-2">
                    <label class="text-sm font-medium text-gray-700 mb-1">Nama Mapel <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_mapel" value="{{ old('nama_mapel') }}" required placeholder="Cth: Rekayasa Perangkat Lunak (RPL)"
                           class="border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm transition-all bg-white">
                </div>

                <div class="flex flex-col">
                    <label class="text-sm font-medium text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                    <select id="kategoriTambah" name="kategori" required onchange="handleKategoriChange('tambah')"
                            class="border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm transition-all bg-white cursor-pointer">
                        <option value="biasa" {{ old('kategori', 'biasa') == 'biasa' ? 'selected' : '' }}>Mapel Biasa (1 Guru Pengampu)</option>
                        <option value="jurusan" {{ old('kategori') == 'jurusan' ? 'selected' : '' }}>Mapel Jurusan (Maksimal 10 Guru)</option>
                    </select>
                </div>
            </div>

            {{-- Input Guru Pengampu Berbentuk Ketik Tag / Chip --}}
            <div class="flex flex-col">
                <div class="flex items-center justify-between mb-1.5">
                    <label class="text-sm font-medium text-gray-700">
                        Guru Pengampu
                        <span id="hintGuruTambah" class="text-xs text-gray-500 font-normal">(Ketik nama guru, mapel biasa hanya diampu oleh 1 guru)</span>
                    </label>
                    <span class="text-xs font-semibold text-emerald-700" id="counterTambahGuru">0 / 1 Guru</span>
                </div>

                <div class="border border-gray-300 rounded-lg p-2.5 bg-white flex flex-wrap gap-2 items-center min-h-[48px] focus-within:ring-2 focus-within:ring-emerald-500 focus-within:border-emerald-500 transition-all shadow-sm">
                    <div id="chipsTambahGuru" class="flex flex-wrap gap-2 items-center">
                        {{-- Tag chip akan muncul di sini --}}
                    </div>
                    <div class="flex-1 flex items-center gap-2 min-w-[240px]">
                        <input type="text" id="inputKetikTambahGuru" list="listGuruSuggestions" placeholder="Ketik nama guru di sini..."
                               class="w-full text-sm outline-none px-2 py-1 text-gray-800 placeholder-gray-400 bg-transparent"
                               onkeydown="handleGuruKeyDown(event, 'tambah')">
                        <button type="button" onclick="addGuruFromInput('tambah')" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-md text-xs font-semibold shrink-0 transition shadow-sm flex items-center gap-1">
                            <i class="bi bi-plus-lg"></i> Tambah
                        </button>
                    </div>
                </div>
                <input type="hidden" name="guru_names" id="hiddenGuruNamesTambah" value="">
                <p class="text-[11px] text-gray-500 mt-1 flex items-center gap-1" id="keteranganGuruTambah">
                    <i class="bi bi-info-circle text-emerald-600"></i> Ketik nama guru (atau pilih dari saran autocomplete saat mengetik), lalu tekan Enter.
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

    {{-- ================= TABEL DAFTAR MAPEL ================= --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-gray-700 border-b border-gray-200 text-sm">
                <tr>
                    <th class="p-4 w-12 text-center">No</th>
                    <th class="p-4 w-32">Kode</th>
                    <th class="p-4 w-40">Kategori</th>
                    <th class="p-4">Nama Mapel</th>
                    <th class="p-4">Guru Pengampu</th>
                    <th class="p-4 text-center w-28">Aksi</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                    @forelse($mapels as $i => $mapel)
                        <tr class="hover:bg-emerald-50/30 transition-colors">
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
                                    $pengampus = $mapel->pengampu->count() > 0 ? $mapel->pengampu : ($mapel->guru ? collect([$mapel->guru]) : collect());
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
                            <td colspan="6" class="p-8 text-center text-gray-400">
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
            <span class="text-xs text-gray-400">Total: 10 Jurusan (hingga 10 guru per jurusan) &bull; 25 Mapel Biasa (1 guru pengampu)</span>
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

            <form id="formEditMapel" method="POST" action="" class="space-y-4" onsubmit="flushGuruInputBeforeSubmit('edit')">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="editMapelKode" class="mb-1 block text-sm font-medium text-gray-700">Kode Mapel <span class="text-red-500">*</span></label>
                        <input id="editMapelKode" name="kode_mapel" type="text" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-400">
                    </div>
                    <div>
                        <label for="editMapelKategori" class="mb-1 block text-sm font-medium text-gray-700">Kategori <span class="text-red-500">*</span></label>
                        <select id="editMapelKategori" name="kategori" required onchange="handleKategoriChange('edit')" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-400 cursor-pointer">
                            <option value="biasa">Mapel Biasa (1 Guru Pengampu)</option>
                            <option value="jurusan">Mapel Jurusan (Maksimal 10 Guru)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="editMapelNama" class="mb-1 block text-sm font-medium text-gray-700">Nama Mapel <span class="text-red-500">*</span></label>
                    <input id="editMapelNama" name="nama_mapel" type="text" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-400">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-sm font-medium text-gray-700">
                            Guru Pengampu
                            <span id="hintGuruEdit" class="text-xs text-gray-500 font-normal">(Ketik nama guru, mapel biasa hanya diampu oleh 1 guru)</span>
                        </label>
                        <span class="text-xs font-semibold text-emerald-700" id="counterEditGuru">0 / 1 Guru</span>
                    </div>

                    <div class="border border-gray-300 rounded-lg p-2.5 bg-white flex flex-wrap gap-2 items-center min-h-[48px] focus-within:ring-2 focus-within:ring-emerald-500 focus-within:border-emerald-500 transition-all shadow-sm">
                        <div id="chipsEditGuru" class="flex flex-wrap gap-2 items-center">
                            {{-- Chip edit guru --}}
                        </div>
                        <div class="flex-1 flex items-center gap-2 min-w-[240px]">
                            <input type="text" id="inputKetikEditGuru" list="listGuruSuggestions" placeholder="Ketik nama guru..."
                                   class="w-full text-sm outline-none px-2 py-1 text-gray-800 placeholder-gray-400 bg-transparent"
                                   onkeydown="handleGuruKeyDown(event, 'edit')">
                            <button type="button" onclick="addGuruFromInput('edit')" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-md text-xs font-semibold shrink-0 transition shadow-sm flex items-center gap-1">
                                <i class="bi bi-plus-lg"></i> Tambah
                            </button>
                        </div>
                    </div>
                    <input type="hidden" name="guru_names" id="hiddenGuruNamesEdit" value="">
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alasan Penghapusan <span class="text-red-500">*</span></label>
                    <textarea id="deleteAlasanMapel" name="alasan" rows="3" required placeholder="Tuliskan alasan menonaktifkan mata pelajaran ini..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm"></textarea>
                </div>
                
                <div class="flex justify-center gap-3 pt-4">
                    <button type="button" onclick="closeModal('modalHapusMapel')" class="px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition text-sm font-medium w-full">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg transition shadow-sm text-sm font-medium w-full">Ya, Nonaktifkan Mapel</button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
    let guruTagsTambah = [];
    let guruTagsEdit = [];

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function getMaxGuru(type) {
        const isTambah = type === 'tambah';
        const select = document.getElementById(isTambah ? 'kategoriTambah' : 'editMapelKategori');
        const kat = select ? select.value : 'biasa';
        return kat === 'jurusan' ? 10 : 1;
    }

    function handleKategoriChange(type) {
        const max = getMaxGuru(type);
        const isTambah = type === 'tambah';
        const hint = document.getElementById(isTambah ? 'hintGuruTambah' : 'hintGuruEdit');
        const list = isTambah ? guruTagsTambah : guruTagsEdit;

        if (hint) {
            hint.textContent = (max === 10)
                ? '(Ketik nama guru lalu tekan Enter atau klik + Tambah, maksimal 10 guru)'
                : '(Ketik nama guru, mapel biasa hanya diampu oleh 1 guru)';
        }

        // Jika diubah ke mapel biasa dan guru > 1, simpan hanya 1 guru pertama
        if (max === 1 && list.length > 1) {
            list.splice(1);
        }

        renderGuruChips(type);
    }

    function renderGuruChips(type) {
        const isTambah = type === 'tambah';
        const list = isTambah ? guruTagsTambah : guruTagsEdit;
        const container = document.getElementById(isTambah ? 'chipsTambahGuru' : 'chipsEditGuru');
        const hiddenInput = document.getElementById(isTambah ? 'hiddenGuruNamesTambah' : 'hiddenGuruNamesEdit');
        const counter = document.getElementById(isTambah ? 'counterTambahGuru' : 'counterEditGuru');
        const max = getMaxGuru(type);
        
        if (!container || !hiddenInput) return;

        container.innerHTML = '';
        list.forEach((name, idx) => {
            const chip = document.createElement('span');
            chip.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-300 shadow-2xs';
            chip.innerHTML = `
                <i class="bi bi-person-check text-emerald-600"></i>
                <span>${escapeHtml(name)}</span>
                <button type="button" onclick="removeGuruChip('${type}', ${idx})" class="text-emerald-700 hover:text-red-600 transition font-bold text-sm leading-none ml-1">&times;</button>
            `;
            container.appendChild(chip);
        });

        hiddenInput.value = JSON.stringify(list);
        if (counter) {
            counter.textContent = `${list.length} / ${max} Guru`;
        }
    }

    function addGuruFromInput(type) {
        const isTambah = type === 'tambah';
        const input = document.getElementById(isTambah ? 'inputKetikTambahGuru' : 'inputKetikEditGuru');
        if (!input) return;

        const name = (input.value || '').trim();
        if (!name) return;

        const list = isTambah ? guruTagsTambah : guruTagsEdit;
        const max = getMaxGuru(type);

        if (list.length >= max) {
            if (max === 1) {
                alert('Mapel biasa hanya dapat diampu oleh 1 guru pengampu. Hapus guru yang ada terlebih dahulu jika ingin menggantinya.');
            } else {
                alert('Maksimal 10 guru pengampu untuk mapel jurusan.');
            }
            return;
        }

        if (list.some(n => n.toLowerCase() === name.toLowerCase())) {
            alert('Guru "' + name + '" sudah ditambahkan.');
            input.value = '';
            return;
        }

        list.push(name);
        input.value = '';
        renderGuruChips(type);
        input.focus();
    }

    function flushGuruInputBeforeSubmit(type) {
        const isTambah = type === 'tambah';
        const input = document.getElementById(isTambah ? 'inputKetikTambahGuru' : 'inputKetikEditGuru');
        if (input && input.value.trim()) {
            addGuruFromInput(type);
        }
    }

    function handleGuruKeyDown(event, type) {
        if (event.key === 'Enter') {
            event.preventDefault();
            addGuruFromInput(type);
        }
    }

    function removeGuruChip(type, index) {
        const list = type === 'tambah' ? guruTagsTambah : guruTagsEdit;
        list.splice(index, 1);
        renderGuruChips(type);
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
        openEditMapelModal(id, kode, nama, kategori, guruNames);
    }

    function openEditMapelModal(id, kode, nama, kategori, guruNames) {
        const form = document.getElementById('formEditMapel');
        form.action = "{{ url('dashboard/mapel') }}/" + id;
        document.getElementById('editMapelKode').value = kode;
        document.getElementById('editMapelNama').value = nama;
        
        const kat = (kategori === 'jurusan') ? 'jurusan' : 'biasa';
        document.getElementById('editMapelKategori').value = kat;
        
        guruTagsEdit = Array.isArray(guruNames) ? [...guruNames] : [];
        if (kat === 'biasa' && guruTagsEdit.length > 1) {
            guruTagsEdit.splice(1);
        }
        handleKategoriChange('edit');

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
</script>
@endsection
