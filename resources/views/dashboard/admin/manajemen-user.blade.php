@extends('layouts.app')

@section('title', 'Manajemen User')

@section('sidebar')
    @include('layouts.admin.sidebar')
@endsection

@section('navbar')
    @include('layouts.admin.navbar')
@endsection

@section('content')
<style>[x-cloak] { display: none !important; }</style>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<div x-data="userManagement()" class="p-6 sm:p-10 font-sans">
    
    @if($errors->any())
        <div class="mb-6 rounded-xl bg-red-50 border border-red-200 p-4 text-sm text-red-800 shadow-2xs">
            <div class="font-semibold mb-1 flex items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill text-red-600"></i>
                <span>Terjadi kesalahan input:</span>
            </div>
            <ul class="list-disc list-inside space-y-1 text-red-700 pl-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="mb-6 flex items-center justify-between rounded-xl bg-emerald-50 border border-emerald-200 p-4 text-sm text-emerald-800 shadow-2xs">
            <div class="flex items-center gap-2.5">
                <i class="bi bi-check-circle-fill text-emerald-600 text-lg"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 cursor-pointer">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    @endif

    <!-- Header & Action -->
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Pengguna Sistem</h1>
            <p class="mt-1 text-sm text-gray-500">Kelola seluruh role akun pengguna: Admin, Waka, Guru Piket, Guru Pengajar, dan Sekretaris Kelas.</p>
        </div>
        <button type="button" @click="openAdd()" class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#1BA886] px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#15896d] cursor-pointer">
            <i class="bi bi-person-plus text-base"></i>
            <span>Tambah Akun Baru</span>
        </button>
    </div>

    <!-- Filter & Search Controls -->
    <div class="mb-6 rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <div class="flex flex-col gap-3 md:flex-row">
            <!-- Search -->
            <div class="relative flex-1">
                <i class="bi bi-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input x-model="search" type="search" placeholder="Cari nama, username, NIP, kelas, atau no HP..." class="w-full rounded-lg border border-gray-300 py-2.5 pl-10 pr-4 text-sm outline-none transition focus:border-[#1BA886] focus:ring-2 focus:ring-[#1BA886]/20">
            </div>

            <!-- Role Filter -->
            <div class="relative md:w-56">
                <i class="bi bi-funnel pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <select x-model="roleFilter" class="w-full appearance-none rounded-lg border border-gray-300 bg-white py-2.5 pl-10 pr-9 text-sm outline-none transition focus:border-[#1BA886] focus:ring-2 focus:ring-[#1BA886]/20 cursor-pointer">
                    <option value="">Semua 5 Role</option>
                    <option value="Admin">Admin</option>
                    <option value="Waka">Waka</option>
                    <option value="Guru Piket">Guru Piket</option>
                    <option value="Guru Pengajar">Guru Pengajar</option>
                    <option value="Sekretaris Kelas">Sekretaris Kelas</option>
                </select>
                <i class="bi bi-chevron-down pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400"></i>
            </div>

            <!-- Status Filter -->
            <div class="relative md:w-44">
                <i class="bi bi-toggle-on pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <select x-model="statusFilter" class="w-full appearance-none rounded-lg border border-gray-300 bg-white py-2.5 pl-10 pr-9 text-sm outline-none transition focus:border-[#1BA886] focus:ring-2 focus:ring-[#1BA886]/20 cursor-pointer">
                    <option value="">Semua Status</option>
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>
                <i class="bi bi-chevron-down pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400"></i>
            </div>
        </div>
    </div>

    <!-- Tabel Pengguna -->
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-[960px] w-full text-left text-sm">
                <thead class="border-b border-gray-200 bg-slate-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="w-16 px-5 py-4 text-center">No</th>
                        <th class="px-5 py-4">Nama Lengkap</th>
                        <th class="px-5 py-4">Username / NIP</th>
                        <th class="px-5 py-4">Role Sistem</th>
                        <th class="px-5 py-4">No. Handphone</th>
                        <th class="px-5 py-4 text-center">Status</th>
                        <th class="px-5 py-4 text-center w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    <template x-for="(user, index) in filteredUsers" :key="user.id">
                        <tr class="transition-colors hover:bg-slate-50/70" :class="user.status === 'nonaktif' ? 'bg-red-50/20' : ''">
                            <td class="px-5 py-4 text-center text-gray-500" x-text="index + 1"></td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#1BA886]/10 text-xs font-bold text-[#0f766e]" x-text="initials(user.name)"></span>
                                    <div>
                                        <span class="font-semibold text-gray-900 block" x-text="user.name"></span>
                                        <template x-if="user.nama_kelas">
                                            <span class="inline-flex items-center gap-1 text-[11px] font-medium text-sky-700 bg-sky-50 px-2 py-0.5 rounded border border-sky-200 mt-0.5">
                                                <i class="bi bi-door-open"></i> Kelas <span x-text="user.nama_kelas"></span>
                                            </span>
                                        </template>
                                        <template x-if="user.nama_mapel && user.role === 'Guru Pengajar'">
                                            <span class="inline-flex items-center gap-1 text-[11px] font-medium text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200 mt-0.5">
                                                <i class="bi bi-book"></i> <span x-text="user.nama_mapel"></span>
                                            </span>
                                        </template>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 font-mono text-gray-600">
                                <div x-text="user.identifier"></div>
                                <template x-if="user.nip && user.nip !== user.identifier">
                                    <div class="text-[11px] text-gray-400">NIP: <span x-text="user.nip"></span></div>
                                </template>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold" :class="user.roleClass" x-text="user.role"></span>
                            </td>
                            <td class="px-5 py-4 text-gray-600" x-text="user.phone"></td>
                            <td class="px-5 py-4 text-center">
                                <template x-if="user.status === 'aktif'">
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Aktif
                                    </span>
                                </template>
                                <template x-if="user.status === 'nonaktif'">
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700" :title="user.alasan_hapus ? 'Alasan: ' + user.alasan_hapus : 'Nonaktif'">
                                        <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span> Nonaktif
                                    </span>
                                </template>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <!-- Tombol Edit -->
                                    <button type="button" @click="openEdit(user)" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 transition hover:bg-[#1BA886]/10 hover:text-[#0f766e] cursor-pointer" title="Edit Akun">
                                        <i class="bi bi-pencil text-sm"></i>
                                    </button>

                                    <!-- Tombol Nonaktifkan (jika aktif) -->
                                    <template x-if="user.status === 'aktif'">
                                        <button type="button" @click="openDelete(user)" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 transition hover:bg-red-50 hover:text-red-600 cursor-pointer" title="Nonaktifkan Akun">
                                            <i class="bi bi-trash3 text-sm"></i>
                                        </button>
                                    </template>

                                    <!-- Tombol Aktifkan Kembali (jika nonaktif) -->
                                    <template x-if="user.status === 'nonaktif'">
                                        <form :action="'{{ url('dashboard/admin/manajemen-user') }}/' + user.id + '/restore'" method="POST" class="inline" onsubmit="return confirm('Aktifkan kembali akun pengguna ini?')">
                                            @csrf
                                            <button type="submit" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-emerald-600 hover:bg-emerald-50 transition cursor-pointer" title="Aktifkan Kembali">
                                                <i class="bi bi-arrow-counterclockwise text-sm font-bold"></i>
                                            </button>
                                        </form>
                                    </template>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="filteredUsers.length === 0" x-cloak>
                        <td colspan="7" class="px-5 py-12 text-center text-sm text-gray-400">
                            <i class="bi bi-people text-3xl mb-2 block text-gray-300"></i>
                            Tidak ada pengguna yang sesuai dengan pencarian atau filter.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Footer Summary Table -->
        <div class="bg-gray-50 p-4 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-2 text-xs text-gray-500">
            <div>
                Total <strong class="text-gray-800" x-text="users.length"></strong> pengguna terdaftar 
                (<span class="text-emerald-700 font-semibold" x-text="activeCount + ' Aktif'"></span>, 
                <span class="text-red-600 font-semibold" x-text="inactiveCount + ' Nonaktif'"></span>)
            </div>
            <div class="text-gray-400 flex items-center gap-2">
                <span class="inline-block w-2 h-2 rounded-full bg-purple-500"></span> Admin
                <span class="inline-block w-2 h-2 rounded-full bg-amber-500 ml-1"></span> Waka
                <span class="inline-block w-2 h-2 rounded-full bg-teal-500 ml-1"></span> Guru Piket
                <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 ml-1"></span> Guru Pengajar
                <span class="inline-block w-2 h-2 rounded-full bg-sky-500 ml-1"></span> Sekretaris Kelas
            </div>
        </div>
    </div>

    {{-- ================= MODAL TAMBAH USER ================= --}}
    <div x-show="addModalOpen" x-cloak x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-slate-900/50 p-4 backdrop-blur-xs" @keydown.escape.window="closeAdd()" @click.self="closeAdd()">
        <div x-show="addModalOpen" x-transition class="my-8 w-full max-w-xl rounded-2xl border border-gray-100 bg-white shadow-2xl">
            <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#1BA886]/10 text-[#1BA886] flex items-center justify-center text-lg">
                        <i class="bi bi-person-plus-fill"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Tambah Akun Pengguna Baru</h2>
                        <p class="text-xs text-gray-500">Tentukan role dan data akses login ke dalam sistem.</p>
                    </div>
                </div>
                <button type="button" @click="closeAdd()" class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-700 cursor-pointer">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <form action="{{ route('admin.manajemen-user.store') }}" method="POST" class="p-6 text-xs sm:text-sm">
                @csrf
                
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <!-- Role Sistem -->
                    <div>
                        <label class="mb-1.5 block font-semibold text-gray-700">Role Sistem <span class="text-red-500">*</span></label>
                        <select name="role" x-model="addRole" @change="onAddRoleChange()" required class="w-full rounded-xl border border-gray-300 bg-white px-3.5 py-2.5 outline-none transition focus:border-[#1BA886] focus:ring-2 focus:ring-[#1BA886]/20 cursor-pointer text-sm">
                            <option value="admin">Admin (Administrator)</option>
                            <option value="waka">Waka (Kurikulum, Kesiswaan, Sarpras, Humas, Mutu)</option>
                            <option value="piket">Guru Piket</option>
                            <option value="guru">Guru Pengajar</option>
                            <option value="sekretaris">Sekretaris Kelas</option>
                        </select>
                    </div>

                    <!-- Pilihan Bidang Waka (Jika Role Waka) -->
                    <div x-show="addRole === 'waka'" x-cloak>
                        <label class="mb-1.5 block font-semibold text-gray-700">Bidang Waka</label>
                        <select x-model="addWakaBidang" @change="onAddWakaChange()" class="w-full rounded-xl border border-gray-300 bg-white px-3.5 py-2.5 outline-none transition focus:border-[#1BA886] focus:ring-2 focus:ring-[#1BA886]/20 cursor-pointer text-sm">
                            <option value="Kurikulum">Waka Kurikulum</option>
                            <option value="Kesiswaan">Waka Kesiswaan</option>
                            <option value="Sarana & Prasarana">Waka Sarana & Prasarana</option>
                            <option value="Humas & Hubungan Industri">Waka Humas & Hubungan Industri</option>
                            <option value="Manajemen Mutu & SDM">Waka Manajemen Mutu & SDM</option>
                        </select>
                    </div>

                    <!-- Pilihan Kelas (Jika Role Sekretaris) -->
                    <div x-show="addRole === 'sekretaris'" x-cloak>
                        <label class="mb-1.5 block font-semibold text-gray-700">Target Kelas <span class="text-red-500">*</span></label>
                        <select name="id_kelas" x-model="addIdKelas" @change="onAddKelasChange()" :required="addRole === 'sekretaris'" class="w-full rounded-xl border border-gray-300 bg-white px-3.5 py-2.5 outline-none transition focus:border-[#1BA886] focus:ring-2 focus:ring-[#1BA886]/20 cursor-pointer text-sm">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelases as $k)
                                <option value="{{ $k->id_kelas }}" data-nama="{{ $k->nama_kelas }}">Kelas {{ $k->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Pilihan Mapel (Jika Role Guru Pengajar) -->
                    <div x-show="addRole === 'guru'" x-cloak>
                        <label class="mb-1.5 block font-semibold text-gray-700">Mata Pelajaran (Opsional)</label>
                        <select name="mapel_id" class="w-full rounded-xl border border-gray-300 bg-white px-3.5 py-2.5 outline-none transition focus:border-[#1BA886] focus:ring-2 focus:ring-[#1BA886]/20 cursor-pointer text-sm">
                            <option value="">-- Pilih Mata Pelajaran --</option>
                            @foreach($mapels as $m)
                                <option value="{{ $m->id }}">{{ $m->nama_mapel }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Username / NIP Login -->
                    <div :class="(addRole !== 'sekretaris' && addRole !== 'guru') ? 'col-span-1' : ''">
                        <label class="mb-1.5 block font-semibold text-gray-700">Username Login <span class="text-red-500">*</span></label>
                        <input name="identifier" x-model="addUsername" type="text" required placeholder="Contoh: admin2, sekre_xbd1" class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 outline-none transition focus:border-[#1BA886] focus:ring-2 focus:ring-[#1BA886]/20 text-sm">
                    </div>
                </div>

                <!-- Nama Lengkap -->
                <div class="mt-4">
                    <label class="mb-1.5 block font-semibold text-gray-700">Nama Lengkap Akun <span class="text-red-500">*</span></label>
                    <input name="nama" x-model="addNama" type="text" required placeholder="Masukkan nama lengkap pengguna..." class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 outline-none transition focus:border-[#1BA886] focus:ring-2 focus:ring-[#1BA886]/20 text-sm">
                </div>

                <!-- Kontak & NIP -->
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 mt-4">
                    <div>
                        <label class="mb-1.5 block font-semibold text-gray-700">Nomor Handphone</label>
                        <input name="phone" type="tel" value="{{ old('phone') }}" placeholder="Contoh: 081234567890" class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 outline-none transition focus:border-[#1BA886] focus:ring-2 focus:ring-[#1BA886]/20 text-sm">
                    </div>
                    <div>
                        <label class="mb-1.5 block font-semibold text-gray-700">NIP (Jika Guru / ASN)</label>
                        <input name="nip" type="text" value="{{ old('nip') }}" placeholder="18 digit NIP..." class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 outline-none transition focus:border-[#1BA886] focus:ring-2 focus:ring-[#1BA886]/20 text-sm">
                    </div>
                </div>

                <!-- Password Awal -->
                <div class="mt-4">
                    <label class="mb-1.5 block font-semibold text-gray-700">Password Awal <span class="text-red-500">*</span></label>
                    <input name="password" type="password" required placeholder="Minimal 6 karakter..." class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 outline-none transition focus:border-[#1BA886] focus:ring-2 focus:ring-[#1BA886]/20 text-sm">
                </div>

                <!-- Actions -->
                <div class="mt-6 flex justify-end gap-3 border-t border-gray-100 pt-4">
                    <button type="button" @click="closeAdd()" class="rounded-xl border border-gray-300 px-5 py-2.5 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition cursor-pointer">Batal</button>
                    <button type="submit" class="rounded-xl bg-[#1BA886] px-5 py-2.5 text-xs font-bold text-white shadow-xs hover:bg-[#15896d] transition inline-flex items-center gap-2 cursor-pointer">
                        <i class="bi bi-check2-circle"></i>
                        <span>Simpan Akun</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ================= MODAL EDIT USER ================= --}}
    <div x-show="editModalOpen" x-cloak x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-slate-900/50 p-4 backdrop-blur-xs" @keydown.escape.window="closeEdit()" @click.self="closeEdit()">
        <div x-show="editModalOpen" x-transition class="my-8 w-full max-w-xl rounded-2xl border border-gray-100 bg-white shadow-2xl">
            <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg">
                        <i class="bi bi-pencil-square"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Edit Akun Pengguna</h2>
                        <p class="text-xs text-gray-500">Perbarui data login, role, status aktif, dan nomor kontak.</p>
                    </div>
                </div>
                <button type="button" @click="closeEdit()" class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-700 cursor-pointer">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <form :action="'{{ url('dashboard/admin/manajemen-user') }}/' + selectedUser.id" method="POST" class="p-6 text-xs sm:text-sm">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block font-semibold text-gray-700">Role Sistem <span class="text-red-500">*</span></label>
                        <select name="role" x-model="selectedUser.role" required class="w-full rounded-xl border border-gray-300 bg-white px-3.5 py-2.5 outline-none transition focus:border-[#1BA886] focus:ring-2 focus:ring-[#1BA886]/20 cursor-pointer text-sm">
                            <option value="Admin">Admin</option>
                            <option value="Waka">Waka</option>
                            <option value="Guru Piket">Guru Piket</option>
                            <option value="Guru Pengajar">Guru Pengajar</option>
                            <option value="Sekretaris Kelas">Sekretaris Kelas</option>
                        </select>
                    </div>

                    <!-- Kelas (jika role Sekretaris) -->
                    <div x-show="selectedUser.role === 'Sekretaris Kelas' || selectedUser.raw_role === 'sekretaris'">
                        <label class="mb-1.5 block font-semibold text-gray-700">Target Kelas</label>
                        <select name="id_kelas" x-model="selectedUser.id_kelas" class="w-full rounded-xl border border-gray-300 bg-white px-3.5 py-2.5 outline-none transition focus:border-[#1BA886] focus:ring-2 focus:ring-[#1BA886]/20 cursor-pointer text-sm">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelases as $k)
                                <option value="{{ $k->id_kelas }}">Kelas {{ $k->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Mapel (jika role Guru Pengajar) -->
                    <div x-show="selectedUser.role === 'Guru Pengajar' || selectedUser.raw_role === 'guru'">
                        <label class="mb-1.5 block font-semibold text-gray-700">Mata Pelajaran</label>
                        <select name="mapel_id" x-model="selectedUser.mapel_id" class="w-full rounded-xl border border-gray-300 bg-white px-3.5 py-2.5 outline-none transition focus:border-[#1BA886] focus:ring-2 focus:ring-[#1BA886]/20 cursor-pointer text-sm">
                            <option value="">-- Pilih Mata Pelajaran --</option>
                            @foreach($mapels as $m)
                                <option value="{{ $m->id }}">{{ $m->nama_mapel }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="mb-1.5 block font-semibold text-gray-700">Username Login <span class="text-red-500">*</span></label>
                        <input name="username" x-model="selectedUser.username" type="text" required class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 outline-none transition focus:border-[#1BA886] focus:ring-2 focus:ring-[#1BA886]/20 text-sm">
                    </div>
                </div>

                <div class="mt-4">
                    <label class="mb-1.5 block font-semibold text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input name="name" x-model="selectedUser.name" type="text" required class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 outline-none transition focus:border-[#1BA886] focus:ring-2 focus:ring-[#1BA886]/20 text-sm">
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 mt-4">
                    <div>
                        <label class="mb-1.5 block font-semibold text-gray-700">Nomor Handphone</label>
                        <input name="phone" x-model="selectedUser.phone" type="tel" class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 outline-none transition focus:border-[#1BA886] focus:ring-2 focus:ring-[#1BA886]/20 text-sm">
                    </div>
                    <div>
                        <label class="mb-1.5 block font-semibold text-gray-700">Status Akun</label>
                        <select name="status" x-model="selectedUser.status" class="w-full rounded-xl border border-gray-300 bg-white px-3.5 py-2.5 outline-none transition focus:border-[#1BA886] focus:ring-2 focus:ring-[#1BA886]/20 cursor-pointer text-sm">
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>

                <!-- Ganti Password -->
                <div class="my-5 flex items-center gap-3">
                    <div class="h-px flex-1 bg-gray-200"></div>
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Ganti Password (Opsional)</span>
                    <div class="h-px flex-1 bg-gray-200"></div>
                </div>

                <div>
                    <label class="mb-1.5 block font-semibold text-gray-700">Password Baru</label>
                    <input name="password" x-model="newPassword" type="password" placeholder="Kosongkan jika tidak ingin mengubah password" class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 outline-none transition focus:border-[#1BA886] focus:ring-2 focus:ring-[#1BA886]/20 text-sm">
                </div>

                <div class="mt-6 flex justify-end gap-3 border-t border-gray-100 pt-4">
                    <button type="button" @click="closeEdit()" class="rounded-xl border border-gray-300 px-5 py-2.5 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition cursor-pointer">Batal</button>
                    <button type="submit" class="rounded-xl bg-[#1BA886] px-5 py-2.5 text-xs font-bold text-white shadow-xs hover:bg-[#15896d] transition cursor-pointer">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ================= MODAL NONAKTIFKAN USER ================= --}}
    <div x-show="deleteModalOpen" x-cloak x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4 backdrop-blur-xs" @keydown.escape.window="closeDelete()" @click.self="closeDelete()">
        <div x-show="deleteModalOpen" x-transition class="w-full max-w-md rounded-2xl border border-gray-100 bg-white p-6 shadow-2xl">
            <div class="mb-4 flex items-center gap-3 border-b border-slate-100 pb-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-100 text-red-600 shrink-0">
                    <i class="bi bi-person-x-fill text-lg"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-900">Nonaktifkan Akun Pengguna</h2>
                    <p class="text-xs text-gray-500">Pengguna tidak akan dapat login ke sistem.</p>
                </div>
            </div>

            <div class="mb-4 p-3.5 bg-red-50/70 border border-red-200 rounded-xl text-xs text-red-900">
                <span class="block text-gray-500">Target Akun:</span>
                <span class="font-bold text-sm block mt-0.5 text-gray-900" x-text="selectedUser.name"></span>
                <span class="text-red-700 block mt-0.5" x-text="selectedUser.role + ' • ' + selectedUser.identifier"></span>
            </div>

            <form :action="'{{ url('dashboard/admin/manajemen-user') }}/' + selectedUser.id" method="POST" class="space-y-4 text-xs">
                @csrf
                @method('DELETE')
                
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Alasan Penonaktifan <span class="text-red-500">*</span></label>
                    <textarea name="alasan" required rows="3" placeholder="Contoh: Rotasi pengurus kelas, mutasi dinas, atau dinonaktifkan sementara..." class="w-full rounded-xl border border-gray-300 p-3 text-gray-700 outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500"></textarea>
                </div>

                <div class="flex justify-end gap-2.5 pt-2 border-t border-slate-100">
                    <button type="button" @click="closeDelete()" class="rounded-xl border border-gray-300 px-4 py-2 font-semibold text-gray-700 hover:bg-gray-50 transition cursor-pointer">Batal</button>
                    <button type="submit" class="rounded-xl bg-red-600 px-4 py-2 font-bold text-white shadow-xs hover:bg-red-700 transition cursor-pointer flex items-center gap-1.5">
                        <i class="bi bi-trash3"></i> Ya, Nonaktifkan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function userManagement() {
        return {
            search: '',
            roleFilter: '',
            statusFilter: '',
            addModalOpen: {{ (request('tambah') || $errors->any()) ? 'true' : 'false' }},
            editModalOpen: false,
            deleteModalOpen: false,
            newPassword: '',
            addRole: 'guru',
            addIdKelas: '',
            addNama: '',
            addUsername: '',
            selectedUser: { id: null, name: '', role: '', identifier: '', phone: '', status: 'aktif', id_kelas: '', mapel_id: '', username: '' },
            users: @json($usersForJs ?? []),
            get activeCount() {
                return this.users.filter(u => u.status === 'aktif').length;
            },
            get inactiveCount() {
                return this.users.filter(u => u.status === 'nonaktif').length;
            },
            get filteredUsers() {
                const term = this.search.toLowerCase().trim();

                return this.users.filter((user) => {
                    const matchesRole = !this.roleFilter || user.role === this.roleFilter;
                    const matchesStatus = !this.statusFilter || user.status === this.statusFilter;
                    const matchesSearch = !term || [
                        user.name, 
                        user.identifier, 
                        user.phone, 
                        user.nama_kelas,
                        user.nama_mapel,
                        user.nip
                    ].some((val) => String(val || '').toLowerCase().includes(term));

                    return matchesRole && matchesStatus && matchesSearch;
                });
            },
            initials(name) {
                return (name || '').split(' ').filter(Boolean).slice(0, 2).map((part) => part.charAt(0)).join('').toUpperCase();
            },
            openAdd() {
                this.addRole = 'guru';
                this.addWakaBidang = 'Kurikulum';
                this.addIdKelas = '';
                this.addNama = '';
                this.addUsername = '';
                this.addModalOpen = true;
            },
            closeAdd() {
                this.addModalOpen = false;
            },
            onAddWakaChange() {
                if (this.addRole === 'waka') {
                    this.addNama = 'Waka ' + this.addWakaBidang;
                    let slug = 'waka_' + this.addWakaBidang.toLowerCase().replace(/[^a-z0-9]/g, '');
                    if (this.addWakaBidang === 'Kesiswaan') slug = 'waka';
                    this.addUsername = slug;
                }
            },
            onAddRoleChange() {
                if (this.addRole === 'sekretaris') {
                    if (this.addIdKelas) {
                        this.onAddKelasChange();
                    } else {
                        this.addNama = 'Sekretaris Kelas ';
                        this.addUsername = 'sekre_';
                    }
                } else if (this.addRole === 'guru') {
                    this.addNama = '';
                    this.addUsername = '';
                } else if (this.addRole === 'piket') {
                    this.addNama = 'Petugas Guru Piket';
                    this.addUsername = 'piket';
                } else if (this.addRole === 'waka') {
                    this.onAddWakaChange();
                } else if (this.addRole === 'admin') {
                    this.addNama = 'Administrator';
                    this.addUsername = 'admin';
                }
            },
            onAddKelasChange() {
                const select = document.querySelector('select[name="id_kelas"]');
                if (!select) return;
                const option = select.options[select.selectedIndex];
                if (option && option.dataset && option.dataset.nama) {
                    const kNama = option.dataset.nama;
                    this.addNama = 'Sekretaris Kelas ' + kNama;
                    this.addUsername = 'sekre_' + kNama.toLowerCase().replace(/\s+/g, '');
                }
            },
            openEdit(user) {
                this.selectedUser = { ...user };
                this.newPassword = '';
                this.editModalOpen = true;
            },
            closeEdit() {
                this.editModalOpen = false;
            },
            openDelete(user) {
                this.selectedUser = { ...user };
                this.deleteModalOpen = true;
            },
            closeDelete() {
                this.deleteModalOpen = false;
            },
        };
    }
</script>
@endsection
