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

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Manajemen Jadwal Pelajaran</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola informasi Jadwal, jam efektif, guru pembimbing.</p>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-5">
                <div class="mb-5 flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#155d50] text-white shadow-sm">
                        <i class="bi bi-calendar3 text-lg"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-slate-800">Tambah Jadwal</h2>
                        <p class="text-xs text-slate-500">Atur jadwal pelajaran baru</p>
                    </div>
                </div>

                <form class="space-y-4">
                    @csrf

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700">Pilih Kelas</label>
                        <select class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-[#155d50] focus:ring-2 focus:ring-[#155d50]/10">
                            <option selected>X MIPA 1</option>
                            <option>X MIPA 2</option>
                            <option>XI MIPA 1</option>
                            <option>XI IPS 1</option>
                            <option>XII IPA 1</option>
                        </select>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700">Pilih Hari</label>
                        <select class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-[#155d50] focus:ring-2 focus:ring-[#155d50]/10">
                            <option selected>Senin</option>
                            <option>Selasa</option>
                            <option>Rabu</option>
                            <option>Kamis</option>
                            <option>Jumat</option>
                        </select>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700">Mata Pelajaran</label>
                        <input type="text" value="Matematika" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-[#155d50] focus:ring-2 focus:ring-[#155d50]/10" placeholder="Masukkan mata pelajaran" />
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700">Nama Guru</label>
                        <input type="text" value="Siti Aminah, S.Pd" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-[#155d50] focus:ring-2 focus:ring-[#155d50]/10" placeholder="Masukkan nama guru" />
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700">Jam Mulai</label>
                            <input type="time" value="07:30" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-[#155d50] focus:ring-2 focus:ring-[#155d50]/10" />
                        </div>

                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700">Jam Selesai</label>
                            <input type="time" value="08:15" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-[#155d50] focus:ring-2 focus:ring-[#155d50]/10" />
                        </div>
                    </div>

                    <button type="submit" class="w-full rounded-lg bg-[#155d50] px-4 py-3 text-sm font-semibold text-white shadow-sm transition duration-200 hover:bg-[#0b2b24]">
                        Simpan Jadwal
                    </button>
                </form>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="overflow-hidden rounded-xl border border-slate-100 bg-white shadow-sm">
                <div class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-800">Daftar Jadwal Pelajaran</h2>
                    </div>

                    <div class="w-full sm:w-auto">
                        <label class="sr-only">Pilih Kelas</label>
                        <select class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none transition focus:border-[#155d50] focus:ring-2 focus:ring-[#155d50]/10 sm:w-52">
                            <option selected>X MIPA 1</option>
                            <option>X MIPA 2</option>
                            <option>XI MIPA 1</option>
                            <option>XI IPS 1</option>
                            <option>XII IPA 1</option>
                        </select>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-white">
                            <tr>
                                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.12em] text-slate-400">Hari</th>
                                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.12em] text-slate-400">Waktu</th>
                                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.12em] text-slate-400">Mata Pelajaran</th>
                                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.12em] text-slate-400">Guru</th>
                                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.12em] text-slate-400">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100 bg-white text-sm text-slate-700">
                            <tr class="transition hover:bg-slate-50/60">
                                <td class="px-4 py-4 font-medium text-slate-700">Senin</td>
                                <td class="px-4 py-4">07:30 - 08:15</td>
                                <td class="px-4 py-4 font-medium text-slate-800">Matematika</td>
                                <td class="px-4 py-4">Siti Aminah, S.Pd</td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-2">
                                        <button type="button" onclick="openEditModal('modalEditJadwal', this)" data-edit-kelas="X MIPA 1" data-edit-hari="Senin" data-edit-mapel="Matematika" data-edit-guru="Siti Aminah, S.Pd" data-edit-jam-mulai="07:30" data-edit-jam-selesai="08:15" class="text-gray-400 hover:text-amber-600 transition" title="Edit">
                                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                            </svg>
                                        </button>
                                        <button type="button" onclick="openModal('modalHapusJadwal')" class="text-gray-400 hover:text-red-600 transition" title="Hapus">
                                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr class="transition hover:bg-slate-50/60">
                                <td class="px-4 py-4 font-medium text-slate-700">Selasa</td>
                                <td class="px-4 py-4">08:15 - 09:00</td>
                                <td class="px-4 py-4 font-medium text-slate-800">Fisika</td>
                                <td class="px-4 py-4">Rizky Pratama, S.Si</td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-2">
                                        <button type="button" onclick="openEditModal('modalEditJadwal', this)" data-edit-kelas="X MIPA 1" data-edit-hari="Selasa" data-edit-mapel="Fisika" data-edit-guru="Rizky Pratama, S.Si" data-edit-jam-mulai="08:15" data-edit-jam-selesai="09:00" class="text-gray-400 hover:text-amber-600 transition" title="Edit">
                                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                            </svg>
                                        </button>
                                        <button type="button" onclick="openModal('modalHapusJadwal')" class="text-gray-400 hover:text-red-600 transition" title="Hapus">
                                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr class="transition hover:bg-slate-50/60">
                                <td class="px-4 py-4 font-medium text-slate-700">Rabu</td>
                                <td class="px-4 py-4">09:15 - 10:00</td>
                                <td class="px-4 py-4 font-medium text-slate-800">Biologi</td>
                                <td class="px-4 py-4">Dewi Lestari, M.Pd</td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-2">
                                        <button type="button" onclick="openEditModal('modalEditJadwal', this)" data-edit-kelas="X MIPA 1" data-edit-hari="Rabu" data-edit-mapel="Biologi" data-edit-guru="Dewi Lestari, M.Pd" data-edit-jam-mulai="09:15" data-edit-jam-selesai="10:00" class="text-gray-400 hover:text-amber-600 transition" title="Edit">
                                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                            </svg>
                                        </button>
                                        <button type="button" onclick="openModal('modalHapusJadwal')" class="text-gray-400 hover:text-red-600 transition" title="Hapus">
                                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr class="transition hover:bg-slate-50/60">
                                <td class="px-4 py-4 font-medium text-slate-700">Kamis</td>
                                <td class="px-4 py-4">07:00 - 07:45</td>
                                <td class="px-4 py-4 font-medium text-slate-800">Bahasa Indonesia</td>
                                <td class="px-4 py-4">Nadya Rahma, S.Pd</td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-2">
                                        <button type="button" onclick="openEditModal('modalEditJadwal', this)" data-edit-kelas="X MIPA 1" data-edit-hari="Kamis" data-edit-mapel="Bahasa Indonesia" data-edit-guru="Nadya Rahma, S.Pd" data-edit-jam-mulai="07:00" data-edit-jam-selesai="07:45" class="text-gray-400 hover:text-amber-600 transition" title="Edit">
                                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                            </svg>
                                        </button>
                                        <button type="button" onclick="openModal('modalHapusJadwal')" class="text-gray-400 hover:text-red-600 transition" title="Hapus">
                                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr class="transition hover:bg-slate-50/60">
                                <td class="px-4 py-4 font-medium text-slate-700">Jumat</td>
                                <td class="px-4 py-4">09:00 - 09:45</td>
                                <td class="px-4 py-4 font-medium text-slate-800">Informatika</td>
                                <td class="px-4 py-4">Budi Hartono, S.Kom</td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-2">
                                        <button type="button" onclick="openEditModal('modalEditJadwal', this)" data-edit-kelas="X MIPA 1" data-edit-hari="Jumat" data-edit-mapel="Informatika" data-edit-guru="Budi Hartono, S.Kom" data-edit-jam-mulai="09:00" data-edit-jam-selesai="09:45" class="text-gray-400 hover:text-amber-600 transition" title="Edit">
                                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                            </svg>
                                        </button>
                                        <button type="button" onclick="openModal('modalHapusJadwal')" class="text-gray-400 hover:text-red-600 transition" title="Hapus">
                                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= MODAL EDIT JADWAL ================= --}}
    <div id="modalEditJadwal" class="fixed inset-0 z-50 hidden items-center justify-center overflow-y-auto bg-gray-900/50 p-4 backdrop-blur-sm">
        <div role="dialog" aria-modal="true" aria-labelledby="modalEditJadwalTitle" class="relative w-full max-w-lg rounded-xl border border-gray-100 bg-white p-6 shadow-lg">
            <div class="mb-5 flex items-center justify-between">
                <h2 id="modalEditJadwalTitle" class="text-xl font-bold text-gray-900">Edit Jadwal Pelajaran</h2>
                <button type="button" onclick="closeEditModal('modalEditJadwal')" aria-label="Tutup modal" class="text-gray-400 transition hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form onsubmit="event.preventDefault(); closeEditModal('modalEditJadwal');" class="space-y-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="editJadwalKelas" class="mb-1 block text-sm font-medium text-gray-700">Pilih Kelas</label>
                        <select id="editJadwalKelas" data-edit-field="kelas" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-400">
                            <option>X MIPA 1</option>
                            <option>X MIPA 2</option>
                            <option>XI MIPA 1</option>
                            <option>XI IPS 1</option>
                            <option>XII IPA 1</option>
                        </select>
                    </div>
                    <div>
                        <label for="editJadwalHari" class="mb-1 block text-sm font-medium text-gray-700">Pilih Hari</label>
                        <select id="editJadwalHari" data-edit-field="hari" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-400">
                            <option>Senin</option>
                            <option>Selasa</option>
                            <option>Rabu</option>
                            <option>Kamis</option>
                            <option>Jumat</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label for="editJadwalMapel" class="mb-1 block text-sm font-medium text-gray-700">Mata Pelajaran</label>
                    <input id="editJadwalMapel" data-edit-field="mapel" type="text" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-400">
                </div>
                <div>
                    <label for="editJadwalGuru" class="mb-1 block text-sm font-medium text-gray-700">Nama Guru</label>
                    <input id="editJadwalGuru" data-edit-field="guru" type="text" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-400">
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="editJadwalJamMulai" class="mb-1 block text-sm font-medium text-gray-700">Jam Mulai</label>
                        <input id="editJadwalJamMulai" data-edit-field="jamMulai" type="time" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-400">
                    </div>
                    <div>
                        <label for="editJadwalJamSelesai" class="mb-1 block text-sm font-medium text-gray-700">Jam Selesai</label>
                        <input id="editJadwalJamSelesai" data-edit-field="jamSelesai" type="time" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-400">
                    </div>
                </div>
                <div class="flex justify-end gap-3 border-t border-gray-100 pt-5">
                    <button type="button" onclick="closeEditModal('modalEditJadwal')" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50">Batal</button>
                    <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-emerald-700">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ================= MODAL HAPUS JADWAL ================= --}}
    <div id="modalHapusJadwal" class="fixed inset-0 z-50 hidden bg-gray-900/60 backdrop-blur-sm overflow-y-auto h-full w-full items-center justify-center transition-opacity">
        <div class="bg-white rounded-xl shadow-xl border border-gray-100 p-8 max-w-md w-full mx-4 text-center relative">
            
            <!-- Tombol Close (X) Pojok Kanan Atas -->
            <button type="button" onclick="closeModal('modalHapusJadwal')" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </div>
            
            <h1 class="text-xl font-bold text-gray-900 mb-2">Hapus Jadwal Pelajaran?</h1>
            <p class="text-sm text-gray-500 mb-6">Apakah Anda yakin ingin menghapus jadwal ini dari sistem? Tindakan ini tidak dapat dibatalkan.</p>

            <form action="#" method="POST" class="text-left space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alasan Penghapusan <span class="text-red-500">*</span></label>
                    <textarea name="alasan" rows="3" required placeholder="Tuliskan alasan menghapus jadwal ini..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm"></textarea>
                </div>
                
                <div class="flex justify-center gap-3 pt-4">
                    <button type="button" onclick="closeModal('modalHapusJadwal')" class="px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition text-sm font-medium w-full">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg transition shadow-sm text-sm font-medium w-full">Ya, Hapus Jadwal</button>
                </div>
            </form>
        </div>
    </div>

</div>

{{-- Script untuk mengatur buka tutup modal --}}
<script>
    function openEditModal(modalId, trigger) {
        const modal = document.getElementById(modalId);

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

    function openModal(modalId) {
        const modal = document.getElementById(modalId);
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
