@extends('layouts.app')

@section('content')
<!-- Container utama: dibuat full width di layar kecil & terpusat rapi di layar besar -->
<div class="w-full max-w-xl md:max-w-3xl lg:max-w-4xl mx-auto bg-[#f4faf7] min-h-screen p-4 md:p-8 pb-28 font-sans text-gray-800">

    <!-- Flash Notifications -->
    @if(session('success'))
        <div class="mb-5 flex items-center justify-between rounded-xl bg-emerald-50 border border-emerald-200 p-4 text-xs md:text-sm font-semibold text-emerald-800 shadow-xs">
            <div class="flex items-center gap-2">
                <i class="bi bi-check-circle-fill text-emerald-600 text-base"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-5 rounded-xl bg-rose-50 border border-rose-200 p-4 text-xs md:text-sm text-rose-800 shadow-xs">
            <div class="font-bold mb-1">Periksa kembali formulir:</div>
            <ul class="list-disc list-inside space-y-0.5 text-rose-700">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Header Info -->
    <div class="mb-5 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <div class="flex items-center gap-2">
                <span class="bg-[#0d6e59] text-white text-[10px] font-bold px-2 py-0.5 rounded-md uppercase">Jurnal Guru</span>
                <span class="text-xs font-bold text-slate-500">{{ $namaHari ?? 'Hari Ini' }}, {{ now()->translatedFormat('d M Y') }}</span>
            </div>
            <h1 class="text-xl md:text-2xl font-extrabold text-[#0f3d32] mt-1">Logbook Pembelajaran Harian</h1>
            <p class="text-xs md:text-sm text-[#5e7e75] mt-0.5">Catat aktivitas mengajar harian dan absensi siswa sesuai jadwal.</p>
        </div>
        <div class="inline-flex items-center gap-2 px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs font-bold text-[#0d6e59] shadow-xs self-start md:self-auto">
            <i class="bi bi-clock-history"></i>
            <span>Pukul {{ now()->format('H:i') }} WIB</span>
        </div>
    </div>

    <!-- Jadwal Mengajar Hari Ini & Indikator Keterlambatan -->
    <div class="mb-6 bg-white border border-[#DCEBE5] rounded-2xl p-4 sm:p-5 shadow-xs">
        <div class="flex items-center justify-between mb-3 border-b border-slate-100 pb-2.5">
            <h3 class="text-xs md:text-sm font-bold text-[#0f3d32] flex items-center gap-2">
                <i class="bi bi-calendar-check text-[#0d6e59]"></i>
                <span>Jadwal Mengajar Anda Hari Ini ({{ $namaHari ?? 'Hari Ini' }})</span>
            </h3>
            <span class="text-[11px] text-slate-400">Tenggat toleransi: 15 menit awal sesi</span>
        </div>

        @if(isset($jadwalsWithStatus) && $jadwalsWithStatus->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($jadwalsWithStatus as $js)
                    @php $j = $js['jadwal']; @endphp
                    <div class="p-3.5 rounded-xl border {{ $js['menitKeterlambatan'] > 0 ? 'bg-amber-50/50 border-amber-200' : 'bg-slate-50 border-slate-200' }} flex flex-col justify-between text-xs">
                        <div>
                            <div class="flex items-center justify-between font-bold text-slate-800">
                                <span>{{ optional($j->kelas)->nama_kelas ?? 'Kelas' }}</span>
                                <span class="font-mono text-[11px] px-2 py-0.5 rounded-md bg-white border border-slate-200 text-slate-600">
                                    {{ $js['mulai'] }} - {{ $js['selesai'] }}
                                </span>
                            </div>
                            <div class="text-[11px] text-[#0d6e59] font-semibold mt-1">
                                {{ $j->mapel }} (Jam ke-{{ $j->jam_ke }})
                            </div>
                        </div>
                        <div class="mt-3 pt-2 border-t border-slate-200/60 flex items-center justify-between">
                            <span class="text-[10px] text-slate-400">Status Waktu:</span>
                            @if($js['menitKeterlambatan'] > 0)
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                    <i class="bi bi-exclamation-triangle-fill"></i> Telat {{ $js['menitKeterlambatan'] }}m
                                </span>
                            @elseif($js['statusWaktu'] === 'Belum Dimulai')
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-200 text-slate-700">
                                    Belum Dimulai
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                    <i class="bi bi-check-circle-fill"></i> Tepat Waktu
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-4 text-xs text-slate-500 bg-slate-50 rounded-xl">
                <i class="bi bi-info-circle text-slate-400 mr-1"></i> Tidak ada jadwal mengajar tetap yang terdaftar untuk hari {{ $namaHari ?? 'ini' }}. Anda tetap dapat mengisi logbook mandiri di bawah ini.
            </div>
        @endif
    </div>

    <form action="{{ route('guru.logbook.store') }}" method="POST" enctype="multipart/form-data" autocomplete="off" class="space-y-4 md:space-y-6">
        @csrf

        <!-- Layout Grid: Card 1 & Card 2 -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
            
            <!-- Card 1: Detail Kelas -->
            <div class="bg-white p-4 md:p-6 rounded-2xl border-l-4 border-[#0d6e59] shadow-sm flex flex-col justify-between">
                <div>
                    <h3 class="font-bold text-sm md:text-base text-[#0f3d32] mb-4">Detail Sesi Mengajar</h3>

                    <div class="space-y-4">
                        <!-- Kelas -->
                        <div>
                            <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-1">Kelas <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <select id="select-kelas" name="id_kelas" required class="w-full text-xs md:text-sm bg-white border border-gray-200 rounded-xl p-2.5 md:p-3 pr-8 text-gray-700 appearance-none outline-none focus:border-[#0d6e59]">
                                    <option value="" disabled {{ empty($selectedKelasId) ? 'selected' : '' }}>-- Pilih Kelas --</option>
                                    @if(isset($kelases))
                                        @foreach($kelases as $k)
                                            <option value="{{ $k->id_kelas }}" {{ ($selectedKelasId == $k->id_kelas) ? 'selected' : '' }}>Kelas {{ $k->nama_kelas }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <i class="bi bi-chevron-down absolute right-3 top-3.5 text-xs text-gray-400 pointer-events-none"></i>
                            </div>
                        </div>

                        <!-- Mata Pelajaran -->
                        <div>
                            <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-1">Mata Pelajaran <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <select name="id_mapel" class="w-full text-xs md:text-sm bg-white border border-gray-200 rounded-xl p-2.5 md:p-3 pr-8 text-gray-700 appearance-none outline-none focus:border-[#0d6e59]">
                                    <option value="" disabled selected>-- Pilih Mata Pelajaran --</option>
                                    @if(isset($mapels))
                                        @foreach($mapels as $m)
                                            <option value="{{ $m->id }}">{{ $m->nama_mapel }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <i class="bi bi-chevron-down absolute right-3 top-3.5 text-xs text-gray-400 pointer-events-none"></i>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-1">Materi / Topik Pembelajaran <span class="text-rose-500">*</span></label>
                            <input type="text" name="materi" required autocomplete="off" placeholder="Contoh: Pengenalan Object-Oriented Programming" class="w-full text-xs md:text-sm bg-white border border-gray-200 rounded-xl p-2.5 md:p-3 text-gray-700 placeholder-gray-400 outline-none focus:border-[#0d6e59]">
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