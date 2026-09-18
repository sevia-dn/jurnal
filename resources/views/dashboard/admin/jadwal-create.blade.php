@extends('layouts.app')

@section('title', 'Tambah Jadwal Mengajar')

@section('sidebar')
    @include('layouts.admin.sidebar')
@endsection

@section('navbar')
    @include('layouts.admin.navbar')
@endsection

@section('content')
<div class="p-6 sm:p-10 font-sans">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Tambah Jadwal Mengajar</h1>
        <p class="text-sm text-gray-500 mt-1">Isi form berikut untuk menambah jadwal mengajar baru.</p>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 p-4 text-sm text-red-700">
            <ul class="list-disc pl-4">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="max-w-2xl">
        <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-5">
            <div class="mb-5 flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#155d50] text-white shadow-sm">
                    <i class="bi bi-calendar3 text-lg"></i>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-slate-800">Form Jadwal Mengajar</h2>
                    <p class="text-xs text-slate-500">Atur jadwal mengajar guru</p>
                </div>
            </div>

            <form action="{{ route('jadwal.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700">Guru Pengajar</label>
                    <select name="id_user" required class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-[#155d50] focus:ring-2 focus:ring-[#155d50]/10">
                        <option value="">-- Pilih Guru --</option>
                        @foreach($guru as $g)
                            <option value="{{ $g->id }}" @selected(old('id_user') == $g->id)>{{ $g->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700">Kelas</label>
                    <select name="id_kelas" required class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-[#155d50] focus:ring-2 focus:ring-[#155d50]/10">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id_kelas }}" @selected(old('id_kelas') == $k->id_kelas)>{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700">Mata Pelajaran</label>
                    <select name="id_mapel" required class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-[#155d50] focus:ring-2 focus:ring-[#155d50]/10">
                        <option value="">-- Pilih Mata Pelajaran --</option>
                        @foreach($mapels as $m)
                            <option value="{{ $m->id }}" @selected(old('id_mapel') == $m->id)>{{ $m->nama_mapel }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700">Hari</label>
                    <select name="hari" required class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-[#155d50] focus:ring-2 focus:ring-[#155d50]/10">
                        <option value="">-- Pilih Hari --</option>
                        @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $hari)
                            <option value="{{ $hari }}" @selected(old('hari') == $hari)>{{ $hari }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700">Jam Mulai (ke-)</label>
                        <input type="number" name="jam_mulai" value="{{ old('jam_mulai') }}" min="1" max="13" required class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-[#155d50] focus:ring-2 focus:ring-[#155d50]/10" placeholder="1" />
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700">Jam Selesai (ke-)</label>
                        <input type="number" name="jam_selesai" value="{{ old('jam_selesai') }}" min="1" max="13" required class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-[#155d50] focus:ring-2 focus:ring-[#155d50]/10" placeholder="2" />
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="flex-1 rounded-lg bg-[#155d50] px-4 py-3 text-sm font-semibold text-white shadow-sm transition duration-200 hover:bg-[#0b2b24]">
                        Simpan Jadwal
                    </button>
                    <a href="{{ route('dashboard.jadwal') }}" class="rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 shadow-sm transition duration-200 hover:bg-slate-50">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

