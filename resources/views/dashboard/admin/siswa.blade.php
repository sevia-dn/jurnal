@extends('layouts.app')

@section('title', 'Manajemen Data Siswa - JurnalKita')

@section('sidebar')
    @include('layouts.admin.sidebar')
@endsection

@section('navbar')
    @include('layouts.admin.navbar')
@endsection

@section('content')
<div class="p-6 sm:p-10 font-sans">

    {{-- Alerts --}}
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

    {{-- Toast Notifikasi AJAX --}}
    <div id="ajaxToastNotification" class="hidden mb-6 items-center justify-between rounded-xl bg-emerald-50 border border-emerald-200 p-4 text-sm text-emerald-800 shadow-xs transition-all">
        <div class="flex items-center gap-2">
            <i class="bi bi-check-circle-fill text-emerald-600 text-lg"></i>
            <span id="ajaxToastMessage"></span>
        </div>
        <button type="button" onclick="document.getElementById('ajaxToastNotification').classList.add('hidden')" class="text-emerald-500 hover:text-emerald-700 cursor-pointer">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <div id="ajaxErrorNotification" class="hidden mb-6 rounded-xl bg-rose-50 border border-rose-200 p-4 text-sm text-rose-800 shadow-xs transition-all">
        <div class="flex items-center justify-between">
            <div>
                <div class="font-semibold mb-1 flex items-center gap-2">
                    <i class="bi bi-exclamation-circle-fill text-rose-600"></i>
                    <span>Terjadi kesalahan input:</span>
                </div>
                <div id="ajaxErrorMessage" class="text-rose-700 text-xs ml-6"></div>
            </div>
            <button type="button" onclick="document.getElementById('ajaxErrorNotification').classList.add('hidden')" class="text-rose-500 hover:text-rose-700 cursor-pointer">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    </div>

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

    <!-- Header Halaman -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Data Siswa</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola data siswa, filter per kelas, dan lakukan aksi masal (batch action).</p>
        </div>
        <form method="GET" action="{{ route('dashboard.siswa') }}" class="flex items-center gap-2 flex-wrap">
            <select name="kelas_id" onchange="this.form.submit()" class="border border-gray-300 rounded-xl px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-400 bg-white">
                <option value="">-- Semua Kelas --</option>
                @foreach($kelasList as $itemKelas)
                    <option value="{{ $itemKelas->id_kelas }}" {{ request('kelas_id') == $itemKelas->id_kelas ? 'selected' : '' }}>
                        {{ $itemKelas->nama_kelas }}
                    </option>
                @endforeach
            </select>
            <div class="relative">
                <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                <input type="text" name="search" value="{{ request('search') }}" onkeyup="filterAndSortTableSiswa(this.value)" placeholder="Cari NISN, nama..." autocomplete="off"
                       class="border border-gray-300 rounded-xl pl-8 pr-3 py-2 text-xs w-48 focus:outline-none focus:ring-2 focus:ring-emerald-400">
            </div>
            @if(request('search') || request('kelas_id'))
                <a href="{{ route('dashboard.siswa') }}" class="text-xs text-rose-500 hover:underline px-1">Reset</a>
            @endif
        </form>
    </div>

    <!-- Form Tambah Siswa -->
    <div class="bg-white rounded-2xl shadow-xs border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
            <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                <i class="bi bi-person-plus-fill text-emerald-600"></i>
                <span>Tambah Data Siswa Baru</span>
            </h3>
        </div>

        <form id="formTambahSiswa" action="{{ route('dashboard.siswa.store') }}" method="POST" onsubmit="handleTambahSiswaAjax(event)" autocomplete="off" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                    <input type="text" id="tambahNamaSiswa" name="nama" value="{{ old('nama') }}" required placeholder="Contoh: Ahmad Fauzi" autocomplete="off" class="w-full px-3 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-400 text-xs outline-none transition">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">NISN <span class="text-rose-500">*</span></label>
                    <input type="text" id="tambahNisSiswa" name="nisn" value="{{ old('nisn') }}" required placeholder="Contoh: 0105292765" autocomplete="off" class="w-full px-3 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-400 text-xs outline-none transition">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Kelas <span class="text-rose-500">*</span></label>
                    <select id="tambahKelasSiswa" name="kelas_id" required class="w-full px-3 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-400 text-xs outline-none bg-white transition">
                        <option value="" disabled {{ !old('kelas_id') && !request('kelas_id') ? 'selected' : '' }}>-- Pilih Kelas --</option>
                        @foreach($kelasList as $itemKelas)
                            <option value="{{ $itemKelas->id_kelas }}" {{ old('kelas_id', request('kelas_id')) == $itemKelas->id_kelas ? 'selected' : '' }}>{{ $itemKelas->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                    <select id="tambahJkSiswa" name="jenis_kelamin" class="w-full px-3 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-400 text-xs outline-none bg-white transition">
                        <option value="L" {{ old('jenis_kelamin') != 'P' ? 'selected' : '' }}>Laki-laki (L)</option>
                        <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan (P)</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" id="btnSubmitTambahSiswa" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl transition shadow-xs text-xs font-bold flex items-center gap-1.5 cursor-pointer">
                    <i class="bi bi-plus-lg"></i>
                    <span>Simpan Siswa</span>
                </button>
            </div>
        </form>
    </div>

    {{-- ================= FLOATING BATCH ACTION BAR ================= --}}
    <div id="batchActionBarSiswa" class="hidden mb-4 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex flex-wrap items-center justify-between gap-3 shadow-sm transition-all">
        <div class="flex items-center gap-2">
            <span class="w-6 h-6 rounded-full bg-emerald-600 text-white text-xs font-bold flex items-center justify-center" id="batchBadgeSiswa">0</span>
            <span class="text-sm font-semibold text-emerald-900" id="batchTextSiswa">0 siswa dipilih</span>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            {{-- Batch Edit Kelas Dropdown / Button --}}
            <button type="button" onclick="openModal('modalBatchEditKelas')" class="px-3.5 py-2 text-xs font-semibold text-emerald-800 bg-white border border-emerald-300 hover:bg-emerald-100 rounded-xl transition shadow-2xs flex items-center gap-1.5 cursor-pointer">
                <i class="bi bi-door-closed text-emerald-700"></i>
                <span>Pindah Kelas Terpilih</span>
            </button>

            {{-- Batch Delete Button --}}
            <button type="button" onclick="confirmBatchDeleteSiswa()" class="px-3.5 py-2 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 rounded-xl transition shadow-2xs flex items-center gap-1.5 cursor-pointer">
                <i class="bi bi-trash-fill"></i>
                <span>Hapus Terpilih</span>
            </button>

            <button type="button" onclick="clearSiswaSelections()" class="px-3 py-2 text-xs font-medium text-gray-500 hover:text-gray-700 cursor-pointer">
                Batal
            </button>
        </div>
    </div>

    <!-- Tabel Daftar Siswa -->
    <div class="bg-white rounded-2xl shadow-xs border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50/80 text-gray-700 border-b border-gray-200 text-xs uppercase tracking-wider font-semibold">
                    <tr>
                        <th class="px-4 py-3.5 w-12 text-center">
                            <input type="checkbox" id="selectAllSiswa" class="rounded text-emerald-600 focus:ring-emerald-500 cursor-pointer w-4 h-4">
                        </th>
                        <th class="px-4 py-3.5">NISN</th>
                        <th class="px-4 py-3.5">
                            <div class="flex items-center gap-1.5">
                                <span>Nama Siswa</span>
                                <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800" title="Terurut otomatis A-Z">
                                    <i class="bi bi-sort-alpha-down"></i> A-Z
                                </span>
                            </div>
                        </th>
                        <th class="px-4 py-3.5">Kelas</th>
                        <th class="px-4 py-3.5 text-center">L/P</th>
                        <th class="px-4 py-3.5 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    <tr id="emptyStateRowSiswa" class="{{ count($siswas ?? []) > 0 ? 'hidden' : '' }}">
                        <td colspan="6" class="px-4 py-10 text-center text-gray-400 text-sm">
                            <i class="bi bi-people text-3xl block mb-2 text-gray-300"></i>
                            Belum ada data siswa yang tercatat.
                        </td>
                    </tr>
                    @foreach($siswas ?? [] as $siswa)
                        <tr class="row-siswa-table-item hover:bg-gray-50/60 transition-colors" data-kelas-id="{{ $siswa->kelas_id }}">
                            <td class="px-4 py-3.5 text-center">
                                <input type="checkbox" value="{{ $siswa->id }}" class="siswa-checkbox rounded text-emerald-600 focus:ring-emerald-500 cursor-pointer w-4 h-4" onchange="updateBatchStateSiswa()">
                            </td>
                            <td class="px-4 py-3.5 font-mono text-xs text-gray-600 font-semibold">{{ $siswa->nisn }}</td>
                            <td class="px-4 py-3.5 flex items-center gap-2.5">
                                <span class="w-8 h-8 rounded-full bg-slate-100 text-slate-700 text-xs flex items-center justify-center font-bold shrink-0">
                                    {{ collect(explode(' ', $siswa->nama))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->implode('') }}
                                </span>
                                <span class="font-semibold text-gray-900">{{ $siswa->nama }}</span>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                    {{ $siswa->kelas->nama_kelas ?? 'Belum ada kelas' }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-center text-xs font-bold text-gray-600">
                                {{ $siswa->jenis_kelamin ?? 'L' }}
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button type="button" onclick="openEditSiswaModal({{ $siswa->id }}, '{{ addslashes($siswa->nisn) }}', '{{ addslashes($siswa->nama) }}', '{{ $siswa->kelas_id }}', '{{ $siswa->jenis_kelamin ?? 'L' }}')"
                                            class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition cursor-pointer" title="Edit Siswa">
                                        <i class="bi bi-pencil-square text-base"></i>
                                    </button>
                                    <button type="button" onclick="openDeleteModal('{{ route('dashboard.siswa.destroy', $siswa->id) }}', '{{ addslashes($siswa->nama) }}')"
                                            class="p-1.5 text-gray-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition cursor-pointer" title="Hapus Siswa">
                                        <i class="bi bi-trash text-base"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- ================= MODAL BATCH EDIT KELAS ================= --}}
<div id="modalBatchEditKelas" class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/60 backdrop-blur-xs p-4">
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 max-w-md w-full p-6 relative">
        <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-100">
            <h3 class="text-base font-bold text-gray-900">Pindah Kelas untuk Siswa Terpilih</h3>
            <button type="button" onclick="closeModal('modalBatchEditKelas')" class="text-gray-400 hover:text-gray-600 p-1 cursor-pointer">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form id="formBatchEditSiswa" action="{{ route('dashboard.siswa.batch-edit') }}" method="POST" class="space-y-4">
            @csrf
            <div id="batchEditSiswaIdsContainer"></div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Pilih Kelas Tujuan <span class="text-rose-500">*</span></label>
                <select name="kelas_id" required class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-xs focus:ring-2 focus:ring-emerald-400 focus:outline-none bg-white">
                    <option value="" disabled selected>-- Pilih Kelas Tujuan --</option>
                    @foreach($kelasList as $itemKelas)
                        <option value="{{ $itemKelas->id_kelas }}">{{ $itemKelas->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-end gap-2.5 pt-3 border-t border-gray-100">
                <button type="button" onclick="closeModal('modalBatchEditKelas')" class="px-4 py-2 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-xs cursor-pointer">
                    Pindahkan Siswa
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ================= FORM HIDDEN UNTUK BATCH DELETE SISWA ================= --}}
<form id="formBatchDeleteSiswa" action="{{ route('dashboard.siswa.batch-delete') }}" method="POST" class="hidden">
    @csrf
    <div id="batchDeleteSiswaIdsContainer"></div>
</form>

{{-- ================= MODAL EDIT SISWA ================= --}}
<div id="modalEditSiswa" class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/60 backdrop-blur-xs p-4">
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 max-w-lg w-full p-6 relative">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
            <h3 class="text-base font-bold text-gray-900">Edit Data Siswa</h3>
            <button type="button" onclick="closeModal('modalEditSiswa')" class="text-gray-400 hover:text-gray-600 p-1 cursor-pointer">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form id="formEditSiswa" method="POST" action="" autocomplete="off" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">NISN <span class="text-rose-500">*</span></label>
                    <input type="text" id="editSiswaNis" name="nisn" required autocomplete="off" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-xs focus:ring-2 focus:ring-emerald-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Jenis Kelamin</label>
                    <select id="editSiswaJk" name="jenis_kelamin" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-xs focus:ring-2 focus:ring-emerald-400 focus:outline-none bg-white">
                        <option value="L">Laki-laki (L)</option>
                        <option value="P">Perempuan (P)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Siswa <span class="text-rose-500">*</span></label>
                <input type="text" id="editSiswaNama" name="nama" required autocomplete="off" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-xs focus:ring-2 focus:ring-emerald-400 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Kelas <span class="text-rose-500">*</span></label>
                <select id="editSiswaKelasId" name="kelas_id" required class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-xs focus:ring-2 focus:ring-emerald-400 focus:outline-none bg-white">
                    @foreach($kelasList as $itemKelas)
                        <option value="{{ $itemKelas->id_kelas }}">{{ $itemKelas->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-end gap-2.5 pt-3 border-t border-gray-100">
                <button type="button" onclick="closeModal('modalEditSiswa')" class="px-4 py-2 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl cursor-pointer">
                    Batal
                </button>
                <button type="submit" id="btnSubmitEditSiswa" class="px-5 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-xs cursor-pointer">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ================= MODAL KONFIRMASI HAPUS SINGLE SISWA ================= --}}
<div id="modalHapusSingleSiswa" class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/60 backdrop-blur-xs p-4">
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 max-w-sm w-full p-6 text-center relative">
        <div class="w-12 h-12 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto mb-3 text-xl">
            <i class="bi bi-trash-fill"></i>
        </div>
        <h4 class="text-base font-bold text-gray-900 mb-1">Hapus Data Siswa?</h4>
        <p class="text-xs text-gray-500 mb-5">Apakah Anda yakin ingin menghapus data siswa <span id="deleteSiswaName" class="font-bold text-gray-800"></span>? Tindakan ini tidak dapat dibatalkan.</p>

        <form id="formDeleteSingleSiswa" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="flex justify-center gap-2">
                <button type="button" onclick="closeModal('modalHapusSingleSiswa')" class="px-4 py-2 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl w-full cursor-pointer">
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
    async function handleTambahSiswaAjax(event) {
        event.preventDefault();
        const form = event.target;
        const btnSubmit = document.getElementById('btnSubmitTambahSiswa');
        const namaInput = document.getElementById('tambahNamaSiswa');
        const nisInput = document.getElementById('tambahNisSiswa');
        const toastEl = document.getElementById('ajaxToastNotification');
        const errorEl = document.getElementById('ajaxErrorNotification');

        if (toastEl) toastEl.classList.add('hidden');
        if (errorEl) errorEl.classList.add('hidden');

        const originalBtnHtml = btnSubmit ? btnSubmit.innerHTML : 'Simpan Siswa';
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
                let errorMsg = 'Gagal menambahkan data siswa.';
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
                const s = data.siswa;
                const tableBody = document.querySelector('table tbody');
                const emptyRow = document.getElementById('emptyStateRowSiswa');

                if (emptyRow) {
                    emptyRow.classList.add('hidden');
                    emptyRow.style.display = 'none';
                }

                const urlParams = new URLSearchParams(window.location.search);
                const currentFilterKelasId = urlParams.get('kelas_id');
                const shouldDisplayInCurrentTable = !currentFilterKelasId || String(currentFilterKelasId) === String(s.kelas_id);

                if (shouldDisplayInCurrentTable && tableBody) {
                    const initials = (s.nama || '').split(' ').filter(Boolean).slice(0, 2).map(w => w[0].toUpperCase()).join('');

                    const tr = document.createElement('tr');
                    tr.className = 'row-siswa-table-item hover:bg-gray-50/60 transition-colors';
                    tr.setAttribute('data-kelas-id', s.kelas_id);
                    const escapedNama = (s.nama || '').replace(/'/g, "\\'");
                    const escapedNis = (s.nisn || s.nis || '').replace(/'/g, "\\'");

                    tr.innerHTML = `
                        <td class="px-4 py-3.5 text-center">
                            <input type="checkbox" value="${s.id}" class="siswa-checkbox rounded text-emerald-600 focus:ring-emerald-500 cursor-pointer w-4 h-4" onchange="updateBatchStateSiswa()">
                        </td>
                        <td class="px-4 py-3.5 font-mono text-xs text-gray-600 font-semibold">${s.nisn || s.nis}</td>
                        <td class="px-4 py-3.5 flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-full bg-slate-100 text-slate-700 text-xs flex items-center justify-center font-bold shrink-0">
                                ${initials}
                            </span>
                            <span class="font-semibold text-gray-900">${s.nama}</span>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                ${s.nama_kelas}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-center text-xs font-bold text-gray-600">
                            ${s.jenis_kelamin || 'L'}
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button type="button" onclick="openEditSiswaModal(${s.id}, '${escapedNis}', '${escapedNama}', '${s.kelas_id}', '${s.jenis_kelamin || 'L'}')"
                                        class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition cursor-pointer" title="Edit Siswa">
                                    <i class="bi bi-pencil-square text-base"></i>
                                </button>
                                <button type="button" onclick="openDeleteModal('/dashboard/siswa/' + ${s.id}, '${escapedNama}')"
                                        class="p-1.5 text-gray-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition cursor-pointer" title="Hapus Siswa">
                                    <i class="bi bi-trash text-base"></i>
                                </button>
                            </div>
                        </td>
                    `;

                    tableBody.appendChild(tr);
                }

                const searchInput = document.querySelector('input[name="search"]');
                filterAndSortTableSiswa(searchInput ? searchInput.value : '');

                if (namaInput) namaInput.value = '';
                if (nisInput) nisInput.value = '';
                if (namaInput) namaInput.focus();

                if (toastEl) {
                    document.getElementById('ajaxToastMessage').innerText = data.message || `Data siswa ${s.nama} berhasil ditambahkan!`;
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

    function openEditSiswaModal(id, nisn, nama, kelasId, jk) {
        document.getElementById('formEditSiswa').action = '/dashboard/siswa/' + id;
        document.getElementById('editSiswaNis').value = nisn;
        document.getElementById('editSiswaNama').value = nama;
        document.getElementById('editSiswaKelasId').value = kelasId;
        document.getElementById('editSiswaJk').value = jk;

        const btnSubmit = document.getElementById('btnSubmitEditSiswa');
        if (btnSubmit) btnSubmit.disabled = false;

        openModal('modalEditSiswa');
    }

    function openDeleteModal(actionUrl, name) {
        document.getElementById('formDeleteSingleSiswa').action = actionUrl;
        document.getElementById('deleteSiswaName').innerText = name;
        openModal('modalHapusSingleSiswa');
    }

    // ================= BATCH ACTION SISWA =================
    const selectAllSiswaEl = document.getElementById('selectAllSiswa');
    const checkboxesSiswa = () => document.querySelectorAll('.siswa-checkbox');
    const batchBarSiswa = document.getElementById('batchActionBarSiswa');
    const batchBadgeSiswa = document.getElementById('batchBadgeSiswa');
    const batchTextSiswa = document.getElementById('batchTextSiswa');

    if (selectAllSiswaEl) {
        selectAllSiswaEl.addEventListener('change', function() {
            checkboxesSiswa().forEach(cb => {
                cb.checked = selectAllSiswaEl.checked;
            });
            updateBatchStateSiswa();
        });
    }

    function updateBatchStateSiswa() {
        const selected = Array.from(checkboxesSiswa()).filter(cb => cb.checked);
        const count = selected.length;

        if (count > 0) {
            batchBarSiswa.classList.remove('hidden');
            batchBadgeSiswa.innerText = count;
            batchTextSiswa.innerText = count + ' siswa dipilih';
        } else {
            batchBarSiswa.classList.add('hidden');
            if (selectAllSiswaEl) selectAllSiswaEl.checked = false;
        }
    }

    function clearSiswaSelections() {
        checkboxesSiswa().forEach(cb => cb.checked = false);
        if (selectAllSiswaEl) selectAllSiswaEl.checked = false;
        updateBatchStateSiswa();
    }

    function getSelectedSiswaIds() {
        return Array.from(checkboxesSiswa()).filter(cb => cb.checked).map(cb => cb.value);
    }

    function confirmBatchDeleteSiswa() {
        const ids = getSelectedSiswaIds();
        if (ids.length === 0) return;

        if (confirm(`Yakin ingin menghapus ${ids.length} siswa yang dipilih?`)) {
            const container = document.getElementById('batchDeleteSiswaIdsContainer');
            container.innerHTML = '';
            ids.forEach(id => {
                const inp = document.createElement('input');
                inp.type = 'hidden';
                inp.name = 'ids[]';
                inp.value = id;
                container.appendChild(inp);
            });
            document.getElementById('formBatchDeleteSiswa').submit();
        }
    }

    const formBatchEditSiswa = document.getElementById('formBatchEditSiswa');
    if (formBatchEditSiswa) {
        formBatchEditSiswa.addEventListener('submit', function() {
            const ids = getSelectedSiswaIds();
            const container = document.getElementById('batchEditSiswaIdsContainer');
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
    function filterAndSortTableSiswa(term) {
        const tableBody = document.querySelector('table tbody');
        if (!tableBody) return;
        const rows = Array.from(tableBody.querySelectorAll('tr.row-siswa-table-item'));
        const emptyRow = document.getElementById('emptyStateRowSiswa');
        const lowerTerm = (term || '').toLowerCase().trim();

        let visibleCount = 0;
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (!lowerTerm || text.includes(lowerTerm)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Re-sort visible rows alphabetically by nama A-Z
        rows.sort((a, b) => {
            const namaA = (a.querySelector('td:nth-child(3)')?.innerText || '').trim();
            const namaB = (b.querySelector('td:nth-child(3)')?.innerText || '').trim();
            return namaA.localeCompare(namaB, 'id', { sensitivity: 'base' });
        });

        rows.forEach(row => tableBody.appendChild(row));

        if (emptyRow) {
            if (visibleCount === 0) {
                emptyRow.classList.remove('hidden');
                emptyRow.style.display = '';
                tableBody.appendChild(emptyRow);
            } else {
                emptyRow.classList.add('hidden');
                emptyRow.style.display = 'none';
            }
        }
    }
</script>
@endsection
