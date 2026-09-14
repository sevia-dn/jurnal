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

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Data Guru</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola informasi staf pengajar sekolah Anda.</p>
        </div>
        <form method="GET" action="{{ route('dashboard.guru') }}" class="flex items-center gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari NIP, nama guru..."
                   class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-56 focus:outline-none focus:ring-2 focus:ring-emerald-400">
            @if(request('search'))
                <a href="{{ route('dashboard.guru') }}" class="text-xs text-gray-500 hover:text-red-600">Reset</a>
            @endif
        </form>
    </div>

    {{-- ================= FORM TAMBAH USER ================= --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Tambah Guru Baru</h2>

        <form method="POST" action="{{ route('dashboard.guru.store') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            @csrf
            <div class="flex flex-col">
                <label class="text-sm font-medium text-gray-700 mb-1">NIP</label>
                <input type="text" name="nip" value="{{ old('nip') }}" placeholder="198005122005011002"
                       class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-400">
            </div>

            <div class="flex flex-col">
                <label class="text-sm font-medium text-gray-700 mb-1">Nama Guru <span class="text-red-500">*</span></label>
                <input type="text" name="nama" value="{{ old('nama') }}" required placeholder="Budi Waluyo, S.Pd"
                       class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-400">
            </div>

            <div class="flex flex-col">
                <label class="text-sm font-medium text-gray-700 mb-1">Mata Pelajaran</label>
                <select name="mapel_id"
                        class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-400 bg-white">
                    <option value="">Pilih Mapel</option>
                    <optgroup label="--- Mapel Jurusan ---">
                        @foreach(($mapels ?? collect())->where('kategori', 'jurusan') as $mapel)
                            <option value="{{ $mapel->id }}" {{ old('mapel_id') == $mapel->id ? 'selected' : '' }}>
                                {{ $mapel->nama_mapel }}
                            </option>
                        @endforeach
                    </optgroup>
                    <optgroup label="--- Mapel Biasa ---">
                        @foreach(($mapels ?? collect())->where('kategori', '!=', 'jurusan') as $mapel)
                            <option value="{{ $mapel->id }}" {{ old('mapel_id') == $mapel->id ? 'selected' : '' }}>
                                {{ $mapel->nama_mapel }}
                            </option>
                        @endforeach
                    </optgroup>
                </select>
            </div>

            <div class="flex flex-col">
                <label class="text-sm font-medium text-gray-700 mb-1">No. HP</label>
                <input type="text" name="no_hp" value="{{ old('no_hp') }}" placeholder="081234567890"
                       class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-400">
            </div>

            <div class="md:col-span-4 flex justify-start">
                <button type="submit"
                        class="bg-emerald-700 hover:bg-emerald-800 text-white font-medium px-5 py-2 rounded-lg transition">
                    + Tambah Guru Baru
                </button>
            </div>
        </form>
    </div>

    {{-- ================= TABEL DAFTAR USER/GURU ================= --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 text-gray-700 border-b border-gray-200 text-sm">
                <tr>
                    <th class="px-4 py-3">NIP</th>
                    <th class="px-4 py-3">Nama Guru</th>
                    <th class="px-4 py-3">Mata Pelajaran</th>
                    <th class="px-4 py-3">No. HP</th>
                    <th class="px-4 py-3 text-center w-28">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                @forelse($users ?? [] as $user)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-4 py-3 font-mono text-gray-600">{{ $user->nip ?? '-' }}</td>
                        <td class="px-4 py-3 flex items-center gap-2">
                            <span class="w-7 h-7 rounded-full bg-emerald-600 text-white text-xs flex items-center justify-center font-semibold shrink-0">
                                {{ collect(explode(' ', $user->name))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->implode('') }}
                            </span>
                            <span class="font-medium text-gray-900">{{ $user->name }}</span>
                        </td>
                        <td class="px-4 py-3">
                            @if($user->mapel)
                                <span class="text-gray-800 font-medium">{{ $user->mapel->nama_mapel }}</span>
                                @if($user->mapel->kategori === 'jurusan')
                                    <span class="ml-1.5 px-1.5 py-0.5 text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200 rounded">Jurusan</span>
                                @else
                                    <span class="ml-1.5 px-1.5 py-0.5 text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 rounded">Biasa</span>
                                @endif
                            @else
                                <span class="text-gray-400 italic">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $user->no_hp ?? '-' }}</td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button type="button" onclick="openEditModal('modalEditGuru', this)"
                                        data-edit-id="{{ $user->id }}"
                                        data-edit-nip="{{ $user->nip }}"
                                        data-edit-nama="{{ $user->name }}"
                                        data-edit-mapel="{{ $user->mapel_id }}"
                                        data-edit-no-hp="{{ $user->no_hp }}"
                                        class="text-gray-400 hover:text-amber-600 transition" title="Edit">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg>
                                </button>
                                <button type="button" onclick="openDeleteModal('modalHapusGuru', '{{ $user->id }}', '{{ addslashes($user->name) }}')"
                                        class="text-gray-400 hover:text-red-600 transition" title="Hapus">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-gray-400">Belum ada data guru.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

{{-- ================= MODAL EDIT GURU ================= --}}
<div id="modalEditGuru" class="fixed inset-0 z-50 hidden items-center justify-center overflow-y-auto bg-gray-900/50 p-4 backdrop-blur-sm">
    <div role="dialog" aria-modal="true" aria-labelledby="modalEditGuruTitle" class="relative w-full max-w-lg rounded-xl border border-gray-100 bg-white p-6 shadow-lg">
        <div class="mb-5 flex items-center justify-between">
            <h2 id="modalEditGuruTitle" class="text-xl font-bold text-gray-900">Edit Data Guru</h2>
            <button type="button" onclick="closeEditModal('modalEditGuru')" aria-label="Tutup modal" class="text-gray-400 transition hover:text-gray-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form id="formEditGuru" method="POST" action="" class="space-y-4">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="editGuruNip" class="mb-1 block text-sm font-medium text-gray-700">NIP</label>
                    <input id="editGuruNip" name="nip" data-edit-field="nip" type="text" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-400">
                </div>
                <div>
                    <label for="editGuruNoHp" class="mb-1 block text-sm font-medium text-gray-700">No. HP</label>
                    <input id="editGuruNoHp" name="no_hp" data-edit-field="noHp" type="text" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-400">
                </div>
            </div>
            <div>
                <label for="editGuruNama" class="mb-1 block text-sm font-medium text-gray-700">Nama Guru <span class="text-red-500">*</span></label>
                <input id="editGuruNama" name="nama" data-edit-field="nama" required type="text" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-400">
            </div>
            <div>
                <label for="editGuruMapel" class="mb-1 block text-sm font-medium text-gray-700">Mata Pelajaran</label>
                <select id="editGuruMapel" name="mapel_id" data-edit-field="mapel" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-400">
                    <option value="">Pilih Mapel</option>
                    <optgroup label="--- Mapel Jurusan ---">
                        @foreach(($mapels ?? collect())->where('kategori', 'jurusan') as $mapel)
                            <option value="{{ $mapel->id }}">{{ $mapel->nama_mapel }}</option>
                        @endforeach
                    </optgroup>
                    <optgroup label="--- Mapel Biasa ---">
                        @foreach(($mapels ?? collect())->where('kategori', '!=', 'jurusan') as $mapel)
                            <option value="{{ $mapel->id }}">{{ $mapel->nama_mapel }}</option>
                        @endforeach
                    </optgroup>
                </select>
            </div>
            <div class="flex justify-end gap-3 border-t border-gray-100 pt-5">
                <button type="button" onclick="closeEditModal('modalEditGuru')" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50">Batal</button>
                <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-emerald-700">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

{{-- ================= MODAL HAPUS GURU ================= --}}
<div id="modalHapusGuru" class="fixed inset-0 z-50 hidden bg-gray-900/60 backdrop-blur-sm overflow-y-auto h-full w-full items-center justify-center transition-opacity">
    <div class="bg-white rounded-xl shadow-xl border border-gray-100 p-8 max-w-md w-full mx-4 text-center relative">
        
        <!-- Tombol Close (X) Pojok Kanan Atas -->
        <button type="button" onclick="closeModal('modalHapusGuru')" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
        </div>
        <h1 class="text-xl font-bold text-gray-900 mb-2">Hapus Data Guru?</h1>
        <p class="text-sm text-gray-500 mb-4">Apakah Anda yakin ingin mengeluarkan guru <strong id="deleteGuruNama" class="text-gray-800"></strong> dari sistem? Data guru akan dinonaktifkan.</p>

        <form id="formHapusGuru" action="" method="POST" class="text-left space-y-4">
            @csrf
            @method('DELETE')
            <div>
                <label for="deleteAlasan" class="block text-sm font-medium text-gray-700 mb-1">
                    Alasan Penghapusan Guru <span class="text-red-500">*</span>
                </label>
                <textarea id="deleteAlasan" name="alasan" rows="3" required placeholder="Tuliskan alasan mengeluarkan atau menonaktifkan guru ini..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm"></textarea>
            </div>
            <div class="flex justify-center gap-3 pt-2">
                <button type="button" onclick="closeModal('modalHapusGuru')" class="px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition text-sm font-medium w-full">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg transition shadow-sm text-sm font-medium w-full">Ya, Hapus Guru</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditModal(modalId, trigger) {
        const modal = document.getElementById(modalId);
        const form = document.getElementById('formEditGuru');
        const id = trigger.dataset.editId;
        form.action = "{{ url('dashboard/guru') }}/" + id;

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
        const form = document.getElementById('formHapusGuru');
        form.action = "{{ url('dashboard/guru') }}/" + id;
        document.getElementById('deleteGuruNama').textContent = name;
        const alasanInput = document.getElementById('deleteAlasan');
        if (alasanInput) {
            alasanInput.value = '';
        }

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
