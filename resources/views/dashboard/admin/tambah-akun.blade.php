@extends('layouts.app')

@section('title', 'Tambah Akun')

@section('sidebar')
    @include('layouts.admin.sidebar')
@endsection

@section('navbar')
    @include('layouts.admin.navbar')
@endsection

@section('content')
<div class="p-6 sm:p-10 font-sans">
    
    <!-- Header Halaman -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Tambah Akun Baru</h1>
            <p class="text-sm text-gray-500 mt-1">Tambahkan pengguna dan tentukan hak akses ke dalam sistem.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-sm font-medium text-[#1BA886] bg-[#1BA886]/10 px-4 py-2.5 rounded-lg hover:bg-[#1BA886] hover:text-white transition-colors !no-underline">
            <i class="bi bi-arrow-left"></i> 
            <span>Kembali ke Dashboard</span>
        </a>
    </div>

    <!-- Container Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-8 mb-6">
        <form action="#" method="POST" class="space-y-6">
            @csrf

            <!-- Baris 1: Role & Nama -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Role / Tipe Akun</label>
                    <select id="role_select" name="role" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1BA886] focus:border-[#1BA886] text-sm outline-none transition-all bg-white">
                        <option value="akun_kelas">Akun Kelas</option>
                        <option value="guru_pns">Guru PNS (NIP)</option>
                        <option value="guru_honorer">Guru Honorer / Non-NIP (Username)</option>
                        <option value="guru_piket">Guru Piket</option>
                        <option value="wakasek">Wakasek</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap</label>
                    <input type="text" name="nama" required placeholder="Masukkan nama lengkap pengguna..." class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1BA886] focus:border-[#1BA886] text-sm outline-none transition-all">
                </div>
            </div>

            <!-- Baris 2: Identifier & Nomor HP -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div id="email_or_username_wrap">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5"> Username (atau NIP)</label>
                    <input type="text" name="identifier" placeholder="Isi username, atau NIP sesuai role..." class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1BA886] focus:border-[#1BA886] text-sm outline-none transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nomor Handphone</label>
                    <input type="tel" name="phone" placeholder="Contoh: 081234567890" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1BA886] focus:border-[#1BA886] text-sm outline-none transition-all">
                </div>
            </div>

            <!-- Baris 3: Password & NIP -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Password Awal</label>
                    <input type="password" name="password" required placeholder="Buat password untuk akun ini..." class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1BA886] focus:border-[#1BA886] text-sm outline-none transition-all">
                </div>

                <div id="nip_wrap" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">NIP (Khusus Guru PNS)</label>
                    <input type="text" name="nip" placeholder="Masukkan 18 digit NIP..." class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1BA886] focus:border-[#1BA886] text-sm outline-none transition-all">
                </div>
            </div>

            <!-- Baris 4: Mapel (Full Width) -->
            <div id="mata_pelajaran_wrap" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Mata Pelajaran yang Diampu</label>
                <input type="text" name="mata_pelajaran" placeholder="Contoh: Matematika, Bahasa Indonesia" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1BA886] focus:border-[#1BA886] text-sm outline-none transition-all">
                <p class="text-xs text-gray-500 mt-2 flex items-center gap-1">
                    <i class="bi bi-info-circle"></i> Isi hanya untuk role guru pengajar. Pisahkan dengan koma (,) jika lebih dari satu.
                </p>
            </div>

            <!-- Divider -->
            <div class="border-t border-gray-100 my-8"></div>

            <!-- Submit Button -->
            <div class="flex items-center justify-end">
                <button type="submit" class="px-6 py-2.5 bg-[#1BA886] hover:bg-[#15896d] text-white font-medium rounded-lg transition-colors shadow-sm text-sm inline-flex items-center gap-2"> Simpan Akun
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    (function(){
        const roleSelect = document.getElementById('role_select');
        const nipWrap = document.getElementById('nip_wrap');
        const mataPelajaranWrap = document.getElementById('mata_pelajaran_wrap');

        function toggleFields(){
            const role = roleSelect.value;

            // Show NIP only for Guru PNS
            nipWrap.classList.toggle('hidden', role !== 'guru_pns');

            // Show mata pelajaran for teacher roles (guru_pns, guru_honorer)
            const isTeacher = role === 'guru_pns' || role === 'guru_honorer';
            mataPelajaranWrap.classList.toggle('hidden', !isTeacher);
        }

        roleSelect.addEventListener('change', toggleFields);
        // initialize
        toggleFields();
    })();
</script>
@endpush
@endsection