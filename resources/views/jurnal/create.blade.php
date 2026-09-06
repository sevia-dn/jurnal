@extends('layouts.app')

@section('content')
<!-- Header Mobile -->
<div class="flex md:hidden justify-between items-center mb-4">
    <div class="flex items-center gap-2">
        <div class="bg-[#3db892] text-white p-2 rounded-xl flex items-center justify-center">
            <i class="bi bi-mortarboard-fill text-xl"></i>
        </div>
        <h1 class="text-xl font-bold text-[#0d6e59]">JurnalKita</h1>
    </div>
    <button class="text-[#0d6e59] text-xl p-1">
        <i class="bi bi-bell"></i>
    </button>
</div>

<!-- Deskripsi Halaman -->
<div class="mb-6">
    <p class="text-xs md:text-sm text-[#5e7e75] font-medium leading-relaxed">
        Catat aktivitas mengajar harian dan observasi kelas Anda.
    </p>
</div>

<!-- Form Container -->
<form action="#" method="POST" enctype="multipart/form-data" class="space-y-5 max-w-2xl mx-auto">
    @csrf

    <!-- Card 1: Detail Kelas -->
    <div class="bg-white p-5 rounded-2xl border-l-4 border-[#0d6e59] shadow-sm">
        <h3 class="font-bold text-base text-[#0f3d32] mb-4">Detail Kelas</h3>

        <div class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Mata Pelajaran</label>
                <select class="w-full text-xs md:text-sm bg-white border border-gray-200 rounded-xl p-3 text-gray-700 focus:ring-[#0d6e59] focus:border-[#0d6e59] outline-none">
                    <option selected disabled>Pilih Mata Pelajaran</option>
                    <option>Matematika</option>
                    <option>Kreativitas, Inovasi, dan Kewirausahaan</option>
                    <option>Bahasa Inggris</option>
                    <option>Konsentrasi RPL</option>
                    <option>PJOK</option>
                    <option>Mapel Pilihan RPL</option>
                    <option>Pendidikan Pancasila</option>
                    <option>Sejarah</option>
                    <option>Bahasa Jepang</option>
                    <option>BK</option>
                    <option>Bahasa Jawa</option>
                    <option>Bahasa Indonesia</option>
                    <option>Pendidikan Agama Islam dan Budi Pekerti</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Kelas</label>
                <!-- Select Kelas dengan Event Onchange -->
                <select id="select-kelas" class="w-full text-xs md:text-sm bg-white border border-gray-200 rounded-xl p-3 text-gray-700 focus:ring-[#0d6e59] focus:border-[#0d6e59] outline-none">
                    <option value="" disabled selected>Pilih Kelas</option>
                    <option value="XI RPL 1">XI RPL 1</option>
                    <option value="XI RPL 2">XI RPL 2</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Kompetensi Dasar / Materi yang Diajarkan</label>
                <input type="text" placeholder="contoh: Pengantar Laravel Framework" class="w-full text-xs md:text-sm bg-white border border-gray-200 rounded-xl p-3 text-gray-700 focus:ring-[#0d6e59] focus:border-[#0d6e59] outline-none">
            </div>
        </div>
    </div>

    <!-- Card 2: Absensi Siswa -->
    <div class="bg-white p-5 rounded-2xl border-l-4 border-[#0d6e59] shadow-sm">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-bold text-base text-[#0f3d32]">Absensi Siswa</h3>
            <div class="flex items-center gap-2">
                <button type="button" onclick="markAllHadir()" class="text-[11px] font-bold text-[#0d6e59] bg-[#e4f3ed] px-2.5 py-1 rounded-lg hover:bg-[#d1e8df] transition">
                    <i class="bi bi-check-all"></i> Semua Hadir
                </button>
                <span id="total-siswa-badge" class="bg-[#e4f3ed] text-[#0d6e59] text-xs font-bold px-3 py-1 rounded-full">0 Siswa</span>
            </div>
        </div>

        <div class="flex justify-between text-xs font-bold text-[#0d6e59] border-b border-gray-100 pb-2 mb-2 pr-2">
            <span>Nama Siswa</span>
            <span>Status</span>
        </div>

        <!-- Container Daftar Siswa dengan Scroll Area -->
        <div id="container-siswa" class="max-h-[380px] overflow-y-auto pr-2 space-y-1 custom-scrollbar">
            <!-- Tampilan awal saat belum pilih kelas -->
            <p class="text-xs text-gray-400 text-center py-8">Silakan pilih kelas terlebih dahulu di atas.</p>
        </div>
    </div>

    <!-- Card 3: Catatan Kelas & Media -->
    <div class="bg-white p-5 rounded-2xl border-l-4 border-[#0d6e59] shadow-sm">
        <h3 class="font-bold text-base text-[#0f3d32] mb-4">Catatan Kelas & Media</h3>

        <div class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Catatan Khusus / Hambatan di Kelas</label>
                <textarea rows="3" placeholder="Catat masalah perilaku, pencapaian khusus, atau kesulitan teknis..." class="w-full text-xs md:text-sm bg-white border border-gray-200 rounded-xl p-3 text-gray-700 focus:ring-[#0d6e59] focus:border-[#0d6e59] outline-none"></textarea>
            </div>

            <!-- Upload Area -->
            <div class="border-2 border-dashed border-gray-300 rounded-2xl p-5 text-center bg-gray-50/40">
                <i class="bi bi-cloud-arrow-up text-3xl text-gray-400"></i>
                <p class="text-xs font-bold text-[#0d6e59] mt-1">Unggah Foto Aktivitas</p>
                <p class="text-[10px] text-gray-400 mb-3">PNG, JPG hingga 10MB</p>
                
                <label class="bg-[#d2ebe2] hover:bg-[#c2e4d9] text-[#0d6e59] text-xs font-semibold px-4 py-2 rounded-xl cursor-pointer inline-flex items-center gap-1.5 transition">
                    <i class="bi bi-image"></i> Jelajahi File
                    <input type="file" class="hidden">
                </label>
            </div>
        </div>
    </div>

    <!-- Submit Button -->
    <button type="submit" class="w-full bg-[#0d6e59] hover:bg-[#095243] text-white py-3.5 rounded-2xl font-bold text-sm shadow-sm transition">
        Kirim Jurnal
    </button>
</form>

<!-- JavaScript Dinamis untuk Mengganti Siswa -->
<!-- JavaScript Dinamis untuk Mengganti Siswa -->
<script>
    // Data Siswa Asli XI RPL 1 dan XI RPL 2
    const dataSiswa = {
        "XI RPL 1": [
            "ABID RIZKY NATANULLOH",
            "AHMAD SANIM SIBTU YAHYA",
            "AHMAD YAZRIL RIDHO FAJRIYA",
            "AISIVA PUJIANSARI",
            "AIZA HAYU PRAMUDYA",
            "ALBERT FACHREZY WIDODO",
            "ALMA LIATUL NURHALIZA",
            "ALVARO ALGOZHALI",
            "ANDRA APRILIAN PRAMUDYA",
            "ARIEL WIJAYA SAPUTRA",
            "AS SYIFA ACINTYA TANITH A.P",
            "ASTITI FEBIANI SAMPURNA",
            "ASYIFA NUR DWI PURWANTI",
            "AWALISHA JUNY PURIPUTRI",
            "AZIZ ARIANSYAH",
            "DANESWARA PUWA HADI GAUTAMA",
            "DEDI PERMANA",
            "DIMAS SAIFUL",
            "DITA PUTRI CAHYANI",
            "ELGA BINTANG CAPUTRA",
            "FACHRIZA ADITYA ALRIFQI",
            "FANDY AHMAD RIYANTO",
            "FARA AZILA TRISNA PUTRI",
            "FELISA PUTRI MAHARANI",
            "ILHAM WICAKSONO",
            "IRFAN FANI SETIAWAN",
            "ISTIQOMAH",
            "KEYLLA PRISCYLIA PUTRI HARIANSYAH",
            "KHANZA HAMIDA KHUMAIROH",
            "KHAYARA MUKHBITA RAMADHINI SYAHPUTRA",
            "MARCHA SUKMA KINANTI",
            "MARDIANSYAH FANI PRATAMA"
        ],
        "XI RPL 2": [
            "MARVEL MAULANA SAPUTRA",
            "MARWA RIZQIANI PUTRI",
            "MAULANA QUBRO ALGHOZALI",
            "MOCHAMAD RAFI NUR ALFAN",
            "MOCHAMMAD WILDAN SEPTIANO PRASETYO",
            "MOHAMMAD REISYA APRILLIAWAN",
            "MUHAMAD BAGUS PRASETIYO",
            "MUHAMMAD ADIP SOFIYULLOH",
            "MUHAMMAD ALBYAN AULIA",
            "MUHAMMAD DUDE FAHREZI",
            "MUHAMMAD FUAD HASAN",
            "MUHAMMAD ILHAM NASHRULLAH",
            "MUHAMMAD RAFA AZRYELLO FARISHUTAMA",
            "MUHAMMAD RAFFI ARKHAN",
            "MUHAMMAD SAIFUDDIN",
            "NANDA AURELIA KHOIRUNNISAA",
            "NASWA PUTRI BINTANG FEBRIANA",
            "NAZWA AFIFAH ANWAR",
            "NITA DWI LARASATI",
            "PRATAMA REZKIANSYAH WIDIANTO",
            "PUTRI LIANASARI",
            "PUTRI ZAHWA RUSDIANA",
            "RAGA SYAHPUTRA ARIFIN",
            "RANIA NURILLAH",
            "RIRIN SRI WAHYUNI",
            "SALMA FIKRIATUL AZIZAH",
            "SEREN KHANZAA AZYLA",
            "SEVIA DWI NOVITASARI",
            "SHALSABILLA PUTRI NURAINI",
            "SKANDINAVIA",
            "SYAFIQI ERDANSYAH RAMADAN",
            "VANESSA FLORIS",
            "VANISSA DEWI PUTRI RIANTO",
            "VARADITA APRILIANDINI",
            "WILDAN RAMADHAN ZULKARNAEN",
            "ZHEFITRA ANANDA WIJAYA"
        ]
    };

    const selectKelas = document.getElementById('select-kelas');
    const containerSiswa = document.getElementById('container-siswa');
    const badgeTotal = document.getElementById('total-siswa-badge');

    // Listener saat pilihan Kelas berubah
    selectKelas.addEventListener('change', function() {
        const kelasDipilih = this.value;
        const listSiswa = dataSiswa[kelasDipilih] || [];

        // Update badge jumlah siswa
        badgeTotal.innerText = `${listSiswa.length} Siswa`;

        // Render Ulang Daftar Siswa
        let html = '';
        listSiswa.forEach((nama, index) => {
            html += `
                <div class="flex justify-between items-center py-2.5 border-b border-gray-50 hover:bg-gray-50/50 px-1 rounded-lg transition">
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-bold text-gray-400 w-5">${index + 1}.</span>
                        <span class="text-xs md:text-sm font-medium text-gray-800">${nama}</span>
                    </div>
                    <div class="flex gap-1 status-radio">
                        <input type="radio" id="h_${index}" name="siswa[${index}]" value="H" checked>
                        <label for="h_${index}">H</label>
                        
                        <input type="radio" id="s_${index}" name="siswa[${index}]" value="S">
                        <label for="s_${index}">S</label>
                        
                        <input type="radio" id="i_${index}" name="siswa[${index}]" value="I">
                        <label for="i_${index}">I</label>
                        
                        <input type="radio" id="d_${index}" name="siswa[${index}]" value="D">
                        <label for="d_${index}">D</label>
                        
                        <input type="radio" id="a_${index}" name="siswa[${index}]" value="A">
                        <label for="a_${index}">A</label>
                    </div>
                </div>
            `;
        });

        containerSiswa.innerHTML = html;
    });

    // Fungsi Tombol "Semua Hadir"
    function markAllHadir() {
        document.querySelectorAll('.status-radio input[value="H"]').forEach(radio => {
            radio.checked = true;
        });
    }
</script>
@endsection