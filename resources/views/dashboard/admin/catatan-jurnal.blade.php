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
    
    <!-- Header Halaman -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
      <div>
        <h1 class="text-2xl font-extrabold text-slate-900">Monitoring Jurnal Real-time</h1>
        <p class="text-sm text-slate-500 mt-1">Pantau status laporan kelas dan kehadiran guru hari ini.</p>
      </div>
      <div class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-full text-sm font-bold text-[#155d50] shadow-sm">
        <i class="bi bi-clock"></i>
        <span>07:45 WIB</span>
      </div>
    </div>

    <!-- 4 Kartu Statistik -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      
      <!-- Card 1 -->
      <div class="bg-white border border-slate-100 rounded-xl p-5 shadow-sm flex flex-col justify-between">
        <div class="flex justify-between items-start gap-3">
          <div>
            <div class="text-[11px] uppercase tracking-wider font-bold text-slate-400">TOTAL KELAS</div>
            <div class="text-3xl font-extrabold text-slate-900 mt-2">36</div>
          </div>
          <div class="w-10 h-10 rounded-lg bg-[#155d50]/10 text-[#155d50] flex items-center justify-center text-lg">
            <i class="bi bi-building"></i>
          </div>
        </div>
        <div class="mt-4">
          <div class="text-xs font-semibold text-[#155d50]">36 / 36 Lapor</div>
          <div class="w-full h-2 bg-[#155d50]/10 rounded-full overflow-hidden mt-2">
            <div class="h-full bg-[#155d50] rounded-full w-full"></div>
          </div>
        </div>
      </div>

      <!-- Card 2 -->
      <div class="bg-white border border-slate-100 rounded-xl p-5 shadow-sm flex flex-col justify-between">
        <div class="flex justify-between items-start gap-3">
          <div>
            <div class="text-[11px] uppercase tracking-wider font-bold text-slate-400">GURU HADIR</div>
            <div class="text-3xl font-extrabold text-slate-900 mt-2">34</div>
          </div>
          <div class="w-10 h-10 rounded-lg bg-[#155d50]/10 text-[#155d50] flex items-center justify-center text-lg">
            <i class="bi bi-person-check"></i>
          </div>
        </div>
        <div class="mt-4">
          <span class="text-xs font-bold text-emerald-600">+2 dari jam lalu</span>
        </div>
      </div>

      <!-- Card 3 -->
      <div class="bg-white border border-slate-100 rounded-xl p-5 shadow-sm flex flex-col justify-between">
        <div class="flex justify-between items-start gap-3">
          <div>
            <div class="text-[11px] uppercase tracking-wider font-bold text-slate-400">GURU ABSEN / IZIN</div>
            <div class="text-3xl font-extrabold text-slate-900 mt-2">2</div>
          </div>
          <div class="w-10 h-10 rounded-lg bg-red-50 text-red-600 flex items-center justify-center text-lg">
            <i class="bi bi-person-slash"></i>
          </div>
        </div>
        <div class="mt-4">
          <span class="text-xs font-semibold text-red-600">Membutuhkan Inval</span>
        </div>
      </div>

      <!-- Card 4 -->
      <div class="bg-white border border-slate-100 rounded-xl p-5 shadow-sm flex flex-col justify-between">
        <div class="flex justify-between items-start gap-3">
          <div>
            <div class="text-[11px] uppercase tracking-wider font-bold text-slate-400">MENUNGGU VALIDASI</div>
            <div class="text-3xl font-extrabold text-slate-900 mt-2">8</div>
          </div>
          <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-lg">
            <i class="bi bi-journal-text"></i>
          </div>
        </div>
        <div class="mt-4">
          <span class="text-xs font-semibold text-blue-600">Jurnal Kelas</span>
        </div>
      </div>

    </div>

    <!-- Panel Utama Tabel & Tabs (Sesuai Desain Gambar) -->
    <div class="bg-white rounded-2xl border border-[#DCEBE5] shadow-sm overflow-hidden">
      
      <!-- Baris Tabs & Filter -->
      <!-- Baris Tabs & Filter -->
<div class="flex items-center justify-between px-6 py-3.5 bg-white border-b border-[#E8F2EE]">
  <div class="flex items-center gap-6 text-sm">
    <a href="#" class="px-3.5 py-1.5 rounded-md font-bold bg-[#E3F2ED] !text-[#0D6B5A] !no-underline transition">
      Semua Kelas
    </a>
    <a href="#" class="font-semibold !text-[#0D6B5A]/70 hover:!text-[#0D6B5A] !no-underline transition">
      Belum Validasi
    </a>
    <a href="#" class="font-semibold !text-[#0D6B5A]/70 hover:!text-[#0D6B5A] !no-underline transition">
      Guru Absen
    </a>
  </div>

  <button class="!text-[#0D6B5A] hover:!text-[#08483C] text-sm font-bold flex items-center gap-2 transition">
    <i class="bi bi-sliders text-base"></i>
    <span>Filter Data</span>
  </button>
</div>
      <!-- Tabel Data dengan Efek Zebra -->
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-[#F2F8F5] text-[#4F6F66] text-xs font-medium border-b border-[#E8F2EE]">
              <th class="px-6 py-4 font-semibold">Waktu</th>
              <th class="px-6 py-4 font-semibold">Kelas</th>
              <th class="px-6 py-4 font-semibold">Guru & Mata Pelajaran</th>
              <th class="px-6 py-4 font-semibold">Status Kehadiran</th>
              <th class="px-6 py-4 font-semibold">Validasi Sekretaris</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#E8F2EE] text-sm text-slate-700">
            
            <!-- Baris 1 (Terang) -->
            <tr class="bg-white hover:bg-[#E3F2ED]/20 transition">
              <td class="px-6 py-4 text-[#4F6F66] font-medium">07:45</td>
              <td class="px-6 py-4 font-bold text-slate-800">X MIPA 1</td>
              <td class="px-6 py-4">
                <div class="font-bold text-slate-800">Drs. Budi Santoso</div>
                <div class="text-xs text-[#5C7E74]">Matematika Wajib (Jam 1-2)</div>
              </td>
              <td class="px-6 py-4">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#E3F2ED] text-[#0D6B5A]">
                  <span class="w-1.5 h-1.5 rounded-full bg-[#0D6B5A]"></span>Hadir
                </span>
              </td>
              <td class="px-6 py-4">
                <span class="inline-flex items-center gap-1 text-xs font-semibold text-[#0D6B5A]">
                  <i class="bi bi-check-circle-fill text-emerald-600 text-sm"></i> Selesai
                </span>
              </td>
            </tr>

            <!-- Baris 2 (Gelap / Tinted Zebra) -->
            <tr class="bg-[#F6FAF8] hover:bg-[#E3F2ED]/20 transition">
              <td class="px-6 py-4 text-[#4F6F66] font-medium">07:42</td>
              <td class="px-6 py-4 font-bold text-slate-800">XI IPS 2</td>
              <td class="px-6 py-4">
                <div class="font-bold text-slate-800">Dra. Siti Aminah, M.Pd</div>
                <div class="text-xs text-[#5C7E74]">Sejarah Indonesia (Jam 1-2)</div>
              </td>
              <td class="px-6 py-4">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#E3F2ED] text-[#0D6B5A]">
                  <span class="w-1.5 h-1.5 rounded-full bg-[#0D6B5A]"></span>Hadir
                </span>
              </td>
              <td class="px-6 py-4">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-[#E0F2FE] text-[#0284C7]">
                  <i class="bi bi-clock"></i>Menunggu
                </span>
              </td>
            </tr>

            <!-- Baris 3 (Terang) -->
            <tr class="bg-white hover:bg-[#E3F2ED]/20 transition">
              <td class="px-6 py-4 text-[#4F6F66] font-medium">07:30</td>
              <td class="px-6 py-4 font-bold text-slate-800">XII MIPA 3</td>
              <td class="px-6 py-4">
                <div class="font-bold text-slate-800">Agus Setiawan, S.Si</div>
                <div class="text-xs text-[#5C7E74]">Fisika (Jam 1-3)</div>
              </td>
              <td class="px-6 py-4">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#FDE2E2] text-[#E02424]">
                  <span class="w-1.5 h-1.5 rounded-full bg-[#E02424]"></span>Sakit
                </span>
              </td>
              <td class="px-6 py-4">
                <span class="inline-flex items-center gap-1 text-xs font-semibold text-[#0D6B5A]">
                  <i class="bi bi-check-circle-fill text-emerald-600 text-sm"></i> Selesai
                </span>
              </td>
            </tr>

            <!-- Baris 4 (Gelap / Tinted Zebra) -->
            <tr class="bg-[#F6FAF8] hover:bg-[#E3F2ED]/20 transition">
              <td class="px-6 py-4 text-[#4F6F66] font-medium">07:28</td>
              <td class="px-6 py-4 font-bold text-slate-800">X Bahasa</td>
              <td class="px-6 py-4">
                <div class="font-bold text-slate-800">Rina Melati, S.Pd</div>
                <div class="text-xs text-[#5C7E74]">Bahasa Inggris (Jam 1-2)</div>
              </td>
              <td class="px-6 py-4">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#E3F2ED] text-[#0D6B5A]">
                  <span class="w-1.5 h-1.5 rounded-full bg-[#0D6B5A]"></span>Hadir
                </span>
              </td>
              <td class="px-6 py-4">
                <span class="inline-flex items-center gap-1 text-xs font-semibold text-[#0D6B5A]">
                  <i class="bi bi-check-circle-fill text-emerald-600 text-sm"></i> Selesai
                </span>
              </td>
            </tr>

            <!-- Baris 5 (Terang) -->
            <tr class="bg-white hover:bg-[#E3F2ED]/20 transition">
              <td class="px-6 py-4 text-[#4F6F66] font-medium">07:25</td>
              <td class="px-6 py-4 font-bold text-slate-800">XI MIPA 2</td>
              <td class="px-6 py-4">
                <div class="font-bold text-slate-800">Ir. Wahyu Pratama</div>
                <div class="text-xs text-[#5C7E74]">Biologi (Jam 1-2)</div>
              </td>
              <td class="px-6 py-4">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#E3F2ED] text-[#0D6B5A]">
                  <span class="w-1.5 h-1.5 rounded-full bg-[#0D6B5A]"></span>Hadir
                </span>
              </td>
              <td class="px-6 py-4">
                <span class="inline-flex items-center gap-1 text-xs font-semibold text-[#0D6B5A]">
                  <i class="bi bi-check-circle-fill text-emerald-600 text-sm"></i> Selesai
                </span>
              </td>
            </tr>

          </tbody>
        </table>
      </div>

      <!-- Pagination Bawah -->
      <div class="flex items-center justify-between px-6 py-4 bg-[#F2F8F5] text-xs text-[#4F6F66]">
        <span class="font-medium">Menampilkan 1-5 dari 36 kelas</span>
        <div class="flex items-center gap-3">
          <button class="text-slate-400 cursor-not-allowed hover:text-slate-600 transition" disabled><i class="bi bi-chevron-left"></i></button>
          <button class="text-slate-600 hover:text-[#0D6B5A] transition"><i class="bi bi-chevron-right"></i></button>
        </div>
      </div>

    </div>

</div>
@endsection