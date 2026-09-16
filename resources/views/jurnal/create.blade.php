<!-- Form Container -->
<form action="{{ route('pengurus-kelas.jurnal.store') }}" method="POST" class="space-y-5 max-w-2xl">
    @csrf

    <!-- Card 1: Informasi Sesi Pelajaran -->
    <div class="bg-white p-5 rounded-2xl border-l-4 border-[#0d6e59] shadow-sm">
        <h3 class="font-bold text-base text-[#0f3d32] mb-4">Informasi Sesi</h3>

        <div class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Kelas</label>
                <select name="id_kelas" class="w-full text-xs md:text-sm bg-white border border-gray-200 rounded-xl p-3 text-gray-700 focus:ring-[#0d6e59] focus:border-[#0d6e59] outline-none" required>
                    <option value="" disabled selected>Pilih Kelas</option>
                    <!-- Contoh data dinamis dari database -->
                    @foreach($kelases ?? [] as $kelas)
                        <option value="{{ $kelas->id_kelas }}">{{ $kelas->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Mata Pelajaran</label>
                <select name="id_mapel" class="w-full text-xs md:text-sm bg-white border border-gray-200 rounded-xl p-3 text-gray-700 focus:ring-[#0d6e59] focus:border-[#0d6e59] outline-none" required>
                    <option selected disabled>Pilih Mata Pelajaran</option>
                    @foreach($mapels ?? [] as $mapel)
                        <option value="{{ $mapel->id }}">{{ $mapel->nama_mapel }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Jam Ke-</label>
                <input type="number" name="jam_ke" placeholder="Contoh: 3" class="w-full text-xs md:text-sm bg-white border border-gray-200 rounded-xl p-3 text-gray-700 focus:ring-[#0d6e59] focus:border-[#0d6e59] outline-none" required>
            </div>
        </div>
    </div>

    <!-- Card 2: Status Kehadiran Guru -->
    <div class="bg-white p-5 rounded-2xl border-l-4 border-[#0d6e59] shadow-sm">
        <h3 class="font-bold text-base text-[#0f3d32] mb-4">Status Kehadiran Guru</h3>

        <div class="grid grid-cols-2 gap-3">
            <label class="flex items-center justify-center p-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-[#0d6e59] transition text-xs font-bold text-gray-700 has-[:checked]:border-[#0d6e59] has-[:checked]:bg-[#e4f3ed] has-[:checked]:text-[#0d6e59]">
                <input type="radio" name="status_kehadiran_guru" value="Hadir" class="hidden" checked>
                <i class="bi bi-check-circle-fill mr-2 text-base"></i> Guru Hadir
            </label>

            <label class="flex items-center justify-center p-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-red-500 transition text-xs font-bold text-gray-700 has-[:checked]:border-red-500 has-[:checked]:bg-red-50 has-[:checked]:text-red-600">
                <input type="radio" name="status_kehadiran_guru" value="Tanpa Keterangan" class="hidden">
                <i class="bi bi-x-circle-fill mr-2 text-base"></i> Tidak Hadir / Inal
            </label>
        </div>

        <div class="mt-4">
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Catatan / Keterangan Tambahan</label>
            <textarea name="catatan" rows="3" placeholder="Contoh: Guru memberi tugas via WhatsApp / Guru terlambat 15 menit" class="w-full text-xs md:text-sm bg-white border border-gray-200 rounded-xl p-3 text-gray-700 focus:ring-[#0d6e59] focus:border-[#0d6e59] outline-none"></textarea>
        </div>
    </div>

    <!-- Submit Button -->
    <button type="submit" class="w-full bg-[#0d6e59] hover:bg-[#095243] text-white py-3.5 rounded-2xl font-bold text-sm shadow-sm transition">
        Simpan Verifikasi
    </button>
</form>