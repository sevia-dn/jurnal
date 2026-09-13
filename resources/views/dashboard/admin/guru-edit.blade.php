@extends('layouts.app')

@section('title', 'Edit Data Guru')

@section('content')
<div class="p-6 sm:p-10 font-sans">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-emerald-800">Edit Data Guru</h1>
        <p class="text-sm text-gray-500 mt-1">Perbarui informasi guru.</p>
    </div>

    <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-6 max-w-2xl">
        <form method="POST" action="{{ route('admin.guru.update', $user->id) }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf
            @method('PUT')

            <div class="flex flex-col">
                <label class="text-sm font-medium text-gray-700 mb-1">NIP</label>
                <input type="text" name="nip" value="{{ old('nip', $user->nip) }}"
                    class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-400">
            </div>

            <div class="flex flex-col">
                <label class="text-sm font-medium text-gray-700 mb-1">Username</label>
                <input type="text" name="username" value="{{ old('username', $user->username) }}" required
                    class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-400">
            </div>

            <div class="flex flex-col">
                <label class="text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                <input type="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah"
                    class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-400">
            </div>

            <div class="flex flex-col">
                <label class="text-sm font-medium text-gray-700 mb-1">Nama Guru</label>
                <input type="text" name="nama" value="{{ old('nama', $user->name) }}" required
                    class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-400">
            </div>

            <div class="flex flex-col">
                <label class="text-sm font-medium text-gray-700 mb-1">Mata Pelajaran</label>
                <select name="mapel_id"
                    class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                    <option value="">Pilih Mapel</option>
                    @foreach($mapels as $mapel)
                    <option value="{{ $mapel->id }}" {{ (int) old('mapel_id', $user->mapel_id) === (int) $mapel->id ? 'selected' : '' }}>
                        {{ $mapel->nama_mapel }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col">
                <label class="text-sm font-medium text-gray-700 mb-1">No. HP</label>
                <input type="text" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}"
                    class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-400">
            </div>

            <div class="md:col-span-2 flex gap-3">
                <button type="submit"
                    class="bg-emerald-700 hover:bg-emerald-800 text-white font-medium px-5 py-2 rounded-lg transition">
                    Simpan Perubahan
                </button>
                <a href="{{ route('admin.guru') }}"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium px-5 py-2 rounded-lg transition !no-underline">
                    Batal
                </a>
            </div>
        </form>
    </div>

</div>
@endsection