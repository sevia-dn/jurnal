@extends('layouts.app')

@section('title', 'Manajemen Jadwal Pelajaran')

@section('content')
<div class="p-6 sm:p-10 font-sans">
    <div class="mb-6">
        <h1 class="mt-2 text-2xl font-bold text-slate-800">Manajemen Jadwal Pelajaran</h1>
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
                                        <button class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-50 text-amber-600 transition hover:bg-amber-100" title="Edit">
                                            <i class="bi bi-pencil-square text-sm"></i>
                                        </button>
                                        <button class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-50 text-red-600 transition hover:bg-red-100" title="Hapus">
                                            <i class="bi bi-trash3 text-sm"></i>
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
                                        <button class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-50 text-amber-600 transition hover:bg-amber-100" title="Edit">
                                            <i class="bi bi-pencil-square text-sm"></i>
                                        </button>
                                        <button class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-50 text-red-600 transition hover:bg-red-100" title="Hapus">
                                            <i class="bi bi-trash3 text-sm"></i>
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
                                        <button class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-50 text-amber-600 transition hover:bg-amber-100" title="Edit">
                                            <i class="bi bi-pencil-square text-sm"></i>
                                        </button>
                                        <button class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-50 text-red-600 transition hover:bg-red-100" title="Hapus">
                                            <i class="bi bi-trash3 text-sm"></i>
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
                                        <button class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-50 text-amber-600 transition hover:bg-amber-100" title="Edit">
                                            <i class="bi bi-pencil-square text-sm"></i>
                                        </button>
                                        <button class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-50 text-red-600 transition hover:bg-red-100" title="Hapus">
                                            <i class="bi bi-trash3 text-sm"></i>
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
                                        <button class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-50 text-amber-600 transition hover:bg-amber-100" title="Edit">
                                            <i class="bi bi-pencil-square text-sm"></i>
                                        </button>
                                        <button class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-50 text-red-600 transition hover:bg-red-100" title="Hapus">
                                            <i class="bi bi-trash3 text-sm"></i>
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
</div>
@endsection
