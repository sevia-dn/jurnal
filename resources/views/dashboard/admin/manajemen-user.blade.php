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
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manajemen User</h1>
            <p class="mt-1 text-sm text-gray-500">Kelola daftar akun pengguna, perbarui informasi pribadi, dan reset password.</p>
        </div>
        <a href="{{ route('tambah-akun') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#1BA886] px-4 py-2.5 text-sm font-medium text-white !no-underline shadow-sm transition-colors hover:bg-[#15896d]">
            <i class="bi bi-person-plus text-base"></i>
            <span>Tambah Akun Baru</span>
        </a>
    </div>

    <div class="mb-6 rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <div class="flex flex-col gap-3 md:flex-row">
            <div class="relative flex-1">
                <i class="bi bi-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input x-model="search" type="search" placeholder="Cari nama, username, email, atau NIP..." class="w-full rounded-lg border border-gray-300 py-2.5 pl-10 pr-4 text-sm outline-none transition-all focus:border-[#1BA886] focus:ring-2 focus:ring-[#1BA886]/20">
            </div>
            <div class="relative md:w-56">
                <i class="bi bi-funnel pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <select x-model="roleFilter" class="w-full appearance-none rounded-lg border border-gray-300 bg-white py-2.5 pl-10 pr-9 text-sm outline-none transition-all focus:border-[#1BA886] focus:ring-2 focus:ring-[#1BA886]/20">
                    <option value="">Semua Role</option>
                    <option value="Admin">Admin</option>
                    <option value="Waka">Waka</option>
                    <option value="Guru Piket">Guru Piket</option>
                    <option value="Pengurus Kelas">Pengurus Kelas</option>
                </select>
                <i class="bi bi-chevron-down pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400"></i>
            </div>
        </div>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-[960px] w-full text-left text-sm">
                <thead class="border-b border-gray-200 bg-slate-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="w-16 px-5 py-4">No</th>
                        <th class="px-5 py-4">Nama Lengkap</th>
                        <th class="px-5 py-4">Username / NIP</th>
                        <th class="px-5 py-4">Role</th>
                        <th class="px-5 py-4">No. Handphone</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    <template x-for="(user, index) in filteredUsers" :key="user.id">
                        <tr class="transition-colors hover:bg-slate-50/70">
                            <td class="px-5 py-4 text-gray-500" x-text="index + 1"></td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#1BA886]/10 text-xs font-bold text-[#0f766e]" x-text="initials(user.name)"></span>
                                    <span class="font-medium text-gray-900" x-text="user.name"></span>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-gray-600" x-text="user.identifier"></td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold" :class="user.roleClass" x-text="user.role"></span>
                            </td>
                            <td class="px-5 py-4 text-gray-600" x-text="user.phone"></td>
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Aktif
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex justify-center gap-2">
                                    <button type="button" @click="openEdit(user)" class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-gray-400 transition-colors hover:bg-[#1BA886]/10 hover:text-[#0f766e]" title="Edit user" aria-label="Edit user">
                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg>
                                    </button>
                                    <button type="button" @click="openDelete(user)" class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-gray-400 transition-colors hover:bg-red-50 hover:text-red-600" title="Hapus user" aria-label="Hapus user">
                                        <i class="bi bi-trash3 text-base"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="filteredUsers.length === 0" x-cloak>
                        <td colspan="7" class="px-5 py-10 text-center text-sm text-gray-500">Tidak ada user yang sesuai dengan pencarian.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div x-show="editModalOpen" x-cloak x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-slate-900/50 p-4 backdrop-blur-sm" @keydown.escape.window="closeEdit()" @click.self="closeEdit()">
        <div x-show="editModalOpen" x-transition class="my-8 w-full max-w-2xl rounded-xl border border-gray-100 bg-white shadow-2xl">
            <div class="flex items-center justify-between border-b border-gray-100 px-6 py-5">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Edit Informasi User &amp; Password</h2>
                    <p class="mt-1 text-sm text-gray-500">Perbarui data akun sesuai kebutuhan.</p>
                </div>
                <button type="button" @click="closeEdit()" class="rounded-lg p-2 text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-700" aria-label="Tutup modal">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <form @submit.prevent="closeEdit()" class="p-6">
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <label for="edit-role" class="mb-1.5 block text-sm font-medium text-gray-700">Role / Tipe Akun</label>
                        <select id="edit-role" x-model="selectedUser.role" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm outline-none transition-all focus:border-[#1BA886] focus:ring-2 focus:ring-[#1BA886]/20">
                            <option>Admin</option>
                            <option>Waka</option>
                            <option>Guru Piket</option>
                            <option>Pengurus Kelas</option>
                        </select>
                    </div>
                    <div>
                        <label for="edit-name" class="mb-1.5 block text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <input id="edit-name" x-model="selectedUser.name" type="text" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none transition-all focus:border-[#1BA886] focus:ring-2 focus:ring-[#1BA886]/20">
                    </div>
                    <div>
                        <label for="edit-identifier" class="mb-1.5 block text-sm font-medium text-gray-700">Username (atau NIP)</label>
                        <input id="edit-identifier" x-model="selectedUser.identifier" type="text" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none transition-all focus:border-[#1BA886] focus:ring-2 focus:ring-[#1BA886]/20">
                    </div>
                    <div>
                        <label for="edit-phone" class="mb-1.5 block text-sm font-medium text-gray-700">Nomor Handphone</label>
                        <input id="edit-phone" x-model="selectedUser.phone" type="tel" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none transition-all focus:border-[#1BA886] focus:ring-2 focus:ring-[#1BA886]/20">
                    </div>
                </div>

                <div class="my-7 flex items-center gap-3">
                    <div class="h-px flex-1 bg-gray-200"></div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-gray-400">Keamanan &amp; Reset Password</span>
                    <div class="h-px flex-1 bg-gray-200"></div>
                </div>

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <label for="new-password" class="mb-1.5 block text-sm font-medium text-gray-700">Password Baru</label>
                        <input id="new-password" x-model="newPassword" type="text" placeholder="Isi jika ingin mengganti password..." class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none transition-all focus:border-[#1BA886] focus:ring-2 focus:ring-[#1BA886]/20">
                    </div>
                    <div>
                        <label for="confirm-password" class="mb-1.5 block text-sm font-medium text-gray-700">Konfirmasi Password Baru</label>
                        <input id="confirm-password" x-model="confirmPassword" type="text" placeholder="Ulangi password baru..." class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none transition-all focus:border-[#1BA886] focus:ring-2 focus:ring-[#1BA886]/20">
                    </div>
                </div>
                <button type="button" @click="generatePassword()" class="mt-3 inline-flex items-center gap-2 rounded-lg bg-[#1BA886]/10 px-3 py-2 text-xs font-semibold text-[#0f766e] transition-colors hover:bg-[#1BA886]/20">
                    <i class="bi bi-key"></i> Generate Auto Password
                </button>

                <div class="mt-8 flex justify-end gap-3 border-t border-gray-100 pt-5">
                    <button type="button" @click="closeEdit()" class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50">Batal</button>
                    <button type="submit" class="rounded-lg bg-[#1BA886] px-5 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-[#15896d]">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="deleteModalOpen" x-cloak x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4 backdrop-blur-sm" @keydown.escape.window="closeDelete()" @click.self="closeDelete()">
        <div x-show="deleteModalOpen" x-transition class="w-full max-w-md rounded-xl border border-gray-100 bg-white p-6 text-center shadow-2xl">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-red-100 text-red-600">
                <i class="bi bi-trash3 text-xl"></i>
            </div>
            <h2 class="text-lg font-bold text-gray-900">Hapus Akun User?</h2>
            <p class="mt-2 text-sm leading-6 text-gray-500">Apakah Anda yakin ingin menghapus akun <span class="font-semibold text-gray-700" x-text="selectedUser.name"></span>? Akses user ini akan dicabut permanen.</p>
            <div class="mt-6 flex justify-center gap-3">
                <button type="button" @click="closeDelete()" class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50">Batal</button>
                <button type="button" @click="closeDelete()" class="rounded-lg bg-red-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-red-700">Ya, Hapus Akun</button>
            </div>
        </div>
    </div>
</div>

<script>
    function userManagement() {
        return {
            search: '',
            roleFilter: '',
            editModalOpen: false,
            deleteModalOpen: false,
            newPassword: '',
            confirmPassword: '',
            selectedUser: { name: '', role: '', identifier: '', phone: '' },
            users: [
                { id: 1, name: 'Ahmad Subagja, S.Kom', identifier: 'admin', role: 'Admin', phone: '081234567890', roleClass: 'bg-purple-100 text-purple-700' },
                { id: 2, name: 'Drs. Bambang Hariyanto, M.Pd', identifier: '197508122002121001', role: 'Waka', phone: '081298765432', roleClass: 'bg-amber-100 text-amber-700' },
                { id: 3, name: 'Siti Aminah, S.Pd', identifier: '198203152009012003', role: 'Guru Piket', phone: '081311223344', roleClass: 'bg-teal-100 text-teal-700' },
                { id: 4, name: 'Pengurus Kelas XI RPL 1', identifier: 'sekretaris.xirpl1', role: 'Pengurus Kelas', phone: '085712344321', roleClass: 'bg-sky-100 text-sky-700' },
            ],
            get filteredUsers() {
                const term = this.search.toLowerCase().trim();

                return this.users.filter((user) => {
                    const matchesRole = !this.roleFilter || user.role === this.roleFilter;
                    const matchesSearch = !term || [user.name, user.identifier, user.phone].some((value) => value.toLowerCase().includes(term));

                    return matchesRole && matchesSearch;
                });
            },
            initials(name) {
                return name.split(' ').filter(Boolean).slice(0, 2).map((part) => part.charAt(0)).join('').toUpperCase();
            },
            openEdit(user) {
                this.selectedUser = { ...user };
                this.newPassword = '';
                this.confirmPassword = '';
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
            generatePassword() {
                const password = `JurnalKita${Math.random().toString(36).slice(-6)}!`;
                this.newPassword = password;
                this.confirmPassword = password;
            },
        };
    }
</script>
@endsection
