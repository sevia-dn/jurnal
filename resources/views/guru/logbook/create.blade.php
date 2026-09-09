@extends('layouts.app')

@section('content')
<!-- Container utama: dibuat full width di layar kecil & terpusat rapi di layar besar -->
<div class="w-full max-w-xl md:max-w-3xl lg:max-w-4xl mx-auto bg-[#f4faf7] min-h-screen p-4 md:p-8 pb-28 font-sans text-gray-800">

    <!-- Deskripsi Sub-header -->
    <p class="text-xs md:text-sm text-[#5e7e75] mb-6 leading-relaxed">
        Catat aktivitas mengajar harian dan observasi kelas Anda.
    </p>

    <form action="#" method="POST" enctype="multipart/form-data" class="space-y-4 md:space-y-6">
        @csrf

        <!-- Layout Grid untuk Layar Besar: Card 1 & Card 2 bersandingan di desktop, tumpuk di mobile -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
            
            <!-- Card 1: Detail Kelas -->
            <div class="bg-white p-4 md:p-6 rounded-2xl border-l-4 border-[#0d6e59] shadow-sm flex flex-col justify-between">
                <div>
                    <h3 class="font-bold text-sm md:text-base text-[#0f3d32] mb-4">Detail Kelas</h3>

                    <div class="space-y-4">
                        <!-- Kelas -->
                        <div>
                            <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-1">Kelas</label>
                            <div class="relative">
                                <select id="select-kelas" class="w-full text-xs md:text-sm bg-white border border-gray-200 rounded-xl p-2.5 md:p-3 pr-8 text-gray-600 appearance-none outline-none focus:border-[#0d6e59]">
                                    <option value="" selected disabled>Pilih Kelas</option>
                                    <option value="XI RPL 1">XI RPL 1</option>
                                    <option value="XI RPL 2">XI RPL 2</option>
                                </select>
                                <i class="bi bi-chevron-down absolute right-3 top-3 text-xs text-gray-400 pointer-events-none"></i>
                            </div>
                        </div>

                        <!-- Mata Pelajaran -->
                        <div>
                            <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-1">Mata Pelajaran</label>
                            <div class="relative">
                                <select class="w-full text-xs md:text-sm bg-white border border-gray-200 rounded-xl p-2.5 md:p-3 pr-8 text-gray-600 appearance-none outline-none focus:border-[#0d6e59]">
                                    <option selected disabled>Pilih Mata Pelajaran</option>
                                    <option>Pemrograman Web</option>
                                    <option>Pemrograman Berorientasi Objek</option>
                                    <option>Basis Data</option>
                                    <option>Matematika</option>
                                </select>
                                <i class="bi bi-chevron-down absolute right-3 top-3 text-xs text-gray-400 pointer-events-none"></i>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-1">Kompetensi Dasar / Materi</label>
                            <input type="text" placeholder="contoh: Pengantar Aljabar" class="w-full text-xs md:text-sm bg-white border border-gray-200 rounded-xl p-2.5 md:p-3 text-gray-700 placeholder-gray-400 border-gray-200 outline-none focus:border-[#0d6e59]">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Absensi Siswa -->
            <div class="bg-white p-4 md:p-6 rounded-2xl border-l-4 border-[#0d6e59] shadow-sm">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="font-bold text-sm md:text-base text-[#0f3d32]">Absensi Siswa</h3>
                    
                    <div class="flex items-center gap-2">
                        <!-- Tombol Hadir Semua -->
                        <button type="button" id="btn-hadir-semua" disabled class="bg-gray-300 text-gray-500 cursor-not-allowed text-[11px] md:text-xs font-semibold px-2.5 py-1.5 rounded-lg transition flex items-center gap-1 shadow-sm">
                            <i class="bi bi-check2-all"></i> Hadir Semua
                        </button>
                        <span id="total-siswa-badge" class="bg-[#e4f3ed] text-[#0d6e59] text-[11px] md:text-xs font-semibold px-2.5 py-1.5 rounded-full">0 Siswa</span>
                    </div>
                </div>

                <div class="flex justify-between text-xs md:text-sm font-bold text-[#0d6e59] border-b border-gray-100 pb-2 mb-2">
                    <span>Nama</span>
                    <span>Status</span>
                </div>

                <!-- Container Daftar Siswa -->
                <div id="container-siswa" class="space-y-1 max-h-[250px] md:max-h-[280px] overflow-y-auto pr-1 custom-scrollbar">
                    <div id="empty-state" class="text-center py-8 text-gray-400 text-xs md:text-sm italic">
                        Silakan pilih kelas terlebih dahulu
                    </div>
                </div>
            </div>

        </div>

        <!-- Card 3: Catatan Kelas & Media (Full width di bawahnya) -->
        <div class="bg-white p-4 md:p-6 rounded-2xl border-l-4 border-[#0d6e59] shadow-sm">
            <h3 class="font-bold text-sm md:text-base text-[#0f3d32] mb-3">Catatan Kelas & Media</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-1">Catatan Khusus / Hambatan di Kelas</label>
                    <textarea rows="4" placeholder="Catat masalah perilaku, pencapaian khusus, atau kesulitan teknis..." class="w-full text-xs md:text-sm bg-white border border-gray-200 rounded-xl p-2.5 md:p-3 text-gray-700 placeholder-gray-400 outline-none focus:border-[#0d6e59] h-[100px] md:h-[120px]"></textarea>
                </div>

                <!-- Dropzone Upload Foto -->
                <div class="border-2 border-dashed border-gray-300 rounded-2xl p-4 text-center bg-gray-50/50 flex flex-col items-center justify-center">
                    <i class="bi bi-cloud-arrow-up text-3xl md:text-4xl text-gray-500 mb-1"></i>
                    <p class="text-xs md:text-sm font-bold text-[#0d6e59]">Unggah Foto Aktivitas</p>
                    <p class="text-[10px] md:text-xs text-gray-400 mt-0.5 mb-2">PNG, JPG hingga 10MB</p>
                    
                    <label class="inline-flex items-center gap-1.5 bg-[#bde8d8] text-[#0d6e59] text-xs md:text-sm font-bold px-3.5 py-1.5 rounded-xl cursor-pointer hover:bg-[#aadbca] transition">
                        <i class="bi bi-image"></i> Jelajahi File
                        <input type="file" class="hidden" accept="image/*">
                    </label>
                </div>
            </div>
        </div>

        <!-- Tombol Kirim -->
        <button type="submit" class="w-full bg-[#0d6e59] hover:bg-[#095243] text-white py-3 rounded-2xl font-bold text-sm md:text-base shadow-sm transition mt-2">
            Kirim Logbook
        </button>
    </form>

</div>

<!-- Script JS -->
<script>
    const dataSiswa = {
        "XI RPL 1": [
            "ABID RIZKY NATANULLOH", "AHMAD SANIM SIBTU YAHYA", "AHMAD YAZRIL RIDHO FAJRIYA", 
            "AISIVA PUJIANSARI", "AIZA HAYU PRAMUDYA", "ALBERT FACHREZY WIDODO", 
            "ALMA LIATUL NURHALIZA", "ALVARO ALGOZHALI", "ANDRA APRILIAN PRAMUDYA", 
            "ARIEL WIJAYA SAPUTRA", "AS SYIFA ACINTYA TANITH A.P", "ASTITI FEBIANI SAMPURNA", 
            "ASYIFA NUR DWI PURWANTI", "AWALISHA JUNY PURIPUTRI", "AZIZ ARIANSYAH", 
            "DANESWARA PUWA HADI GAUTAMA", "DEDI PERMANA", "DIMAS SAIFUL", 
            "DITA PUTRI CAHYANI", "ELGA BINTANG CAPUTRA", "FACHRIZA ADITYA ALRIFQI", 
            "FANDY AHMAD RIYANTO", "FARA AZILA TRISNA PUTRI", "FELISA PUTRI MAHARANI", 
            "ILHAM WICAKSONO", "IRFAN FANI SETIAWAN", "ISTIQOMAH", 
            "KEYLLA PRISCYLIA PUTRI HARIANSYAH", "KHANZA HAMIDA KHUMAIROH", 
            "KHAYARA MUKHBITA RAMADHINI SYAHPUTRA", "MARCHA SUKMA KINANTI", "MARDIANSYAH FANI PRATAMA"
        ],
        "XI RPL 2": [
            "MARVEL MAULANA SAPUTRA", "MARWA RIZQIANI PUTRI", "MAULANA QUBRO ALGHOZALI", 
            "MOCHAMAD RAFI NUR ALFAN", "MOCHAMMAD WILDAN SEPTIANO PRASETYO", "MOHAMMAD REISYA APRILLIAWAN", 
            "MUHAMAD BAGUS PRASETIYO", "MUHAMMAD ADIP SOFIYULLOH", "MUHAMMAD ALBYAN AULIA", 
            "MUHAMMAD DUDE FAHREZI", "MUHAMMAD FUAD HASAN", "MUHAMMAD ILHAM NASHRULLAH", 
            "MUHAMMAD RAFA AZRYELLO FARISHUTAMA", "MUHAMMAD RAFFI ARKHAN", "MUHAMMAD SAIFUDDIN", 
            "NANDA AURELIA KHOIRUNNISAA", "NASWA PUTRI BINTANG FEBRIANA", "NAZWA AFIFAH ANWAR", 
            "NITA DWI LARASATI", "PRATAMA REZKIANSYAH WIDIANTO", "PUTRI LIANASARI", 
            "PUTRI ZAHWA RUSDIANA", "RAGA SYAHPUTRA ARIFIN", "RANIA NURILLAH", 
            "RIRIN SRI WAHYUNI", "SALMA FIKRIATUL AZIZAH", "SEREN KHANZAA AZYLA", 
            "SEVIA DWI NOVITASARI", "SHALSABILLA PUTRI NURAINI", "SKANDINAVIA", 
            "SYAFIQI ERDANSYAH RAMADAN", "VANESSA FLORIS", "VANISSA DEWI PUTRI RIANTO", 
            "VARADITA APRILIANDINI", "WILDAN RAMADHAN ZULKARNAEN", "ZHEFITRA ANANDA WIJAYA"
        ]
    };

    const selectKelas = document.getElementById('select-kelas');
    const containerSiswa = document.getElementById('container-siswa');
    const badgeTotal = document.getElementById('total-siswa-badge');
    const btnHadirSemua = document.getElementById('btn-hadir-semua');

    selectKelas.addEventListener('change', function() {
        const kelasDipilih = this.value;
        const listSiswa = dataSiswa[kelasDipilih] || [];

        badgeTotal.innerText = `${listSiswa.length} Siswa`;

        if (listSiswa.length === 0) {
            containerSiswa.innerHTML = `<div class="text-center py-6 text-gray-400 text-xs italic">Tidak ada data siswa</div>`;
            btnHadirSemua.disabled = true;
            btnHadirSemua.className = "bg-gray-300 text-gray-500 cursor-not-allowed text-[11px] md:text-xs font-semibold px-2.5 py-1.5 rounded-lg transition flex items-center gap-1 shadow-sm";
            return;
        }

        btnHadirSemua.disabled = false;
        btnHadirSemua.className = "bg-[#0d6e59] hover:bg-[#095243] text-white text-[11px] md:text-xs font-semibold px-2.5 py-1.5 rounded-lg transition flex items-center gap-1 shadow-sm cursor-pointer";

        let html = '';
        listSiswa.forEach((nama, index) => {
            html += `
                <div class="flex justify-between items-center py-2 border-b border-gray-50">
                    <span class="text-xs md:text-sm font-medium text-gray-800">${nama}</span>
                    <div class="flex gap-1 status-radio">
                        <input type="radio" id="h_${index}" name="siswa[${index}]" value="H" checked><label for="h_${index}">H</label>
                        <input type="radio" id="s_${index}" name="siswa[${index}]" value="S"><label for="s_${index}">S</label>
                        <input type="radio" id="i_${index}" name="siswa[${index}]" value="I"><label for="i_${index}">I</label>
                        <input type="radio" id="d_${index}" name="siswa[${index}]" value="D"><label for="d_${index}">D</label>
                        <input type="radio" id="a_${index}" name="siswa[${index}]" value="A"><label for="a_${index}">A</label>
                    </div>
                </div>
            `;
        });

        containerSiswa.innerHTML = html;
    });

    btnHadirSemua.addEventListener('click', function() {
        if (this.disabled) return;
        const hadirRadios = containerSiswa.querySelectorAll('input[type="radio"][value="H"]');
        hadirRadios.forEach(radio => radio.checked = true);
    });
</script>
@endsection