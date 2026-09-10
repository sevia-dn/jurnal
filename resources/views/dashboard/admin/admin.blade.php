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
    
    <!-- Header Section -->
    <div class="mb-8">
      <h1 class="text-xl font-bold text-gray-900 mb-1">Overview</h1>
      <p class="text-sm text-gray-500">Ringkasan aktivitas hari ini, 24 Oct 2026</p>
    </div>

    <!-- Stat Cards Section -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      
      <!-- Card 1 -->
      <a href="{{ route('dashboard.guru') }}" class="block group !no-underline">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center gap-5 transition-all duration-300 group-hover:shadow-md group-hover:border-emerald-300 group-hover:-translate-y-1">
          <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-2xl">
            <i class="bi bi-people"></i>
          </div>
          <div>
            <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">JUMLAH GURU</div>
            <div class="text-3xl font-bold !text-gray-800">42</div>
          </div>
        </div>
      </a>

      <!-- Card 2 -->
      <a href="{{ route('dashboard.kelas') }}" class="block group !no-underline">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center gap-5 transition-all duration-300 group-hover:shadow-md group-hover:border-emerald-300 group-hover:-translate-y-1">
          <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-2xl">
            <i class="bi bi-building"></i>
          </div>
          <div>
            <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">JUMLAH KELAS</div>
            <div class="text-3xl font-bold !text-gray-800">12</div>
          </div>
        </div>
      </a>

      <!-- Card 3 -->
      <a href="{{ route('catatan-jurnal') }}" class="block group !no-underline">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center gap-5 transition-all duration-300 group-hover:shadow-md group-hover:border-emerald-300 group-hover:-translate-y-1">
          <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-2xl">
            <i class="bi bi-journal-bookmark"></i>
          </div>
          <div>
            <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">JURNAL HARI INI</div>
            <div class="text-3xl font-bold !text-gray-800">8</div>
          </div>
        </div>
      </a>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mt-8">
      
      <!-- Table Header -->
      <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-white">
        <h2 class="text-lg font-bold text-gray-800">Aktivitas Jurnal Terbaru</h2>
        <!-- Class !no-underline dan !text-emerald-600 memaksa link agar tidak menjadi biru -->
        <a href="#" class="text-sm font-semibold !text-emerald-600 hover:!text-emerald-700 !no-underline transition-colors flex items-center gap-1">
          Lihat Semua 
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </a>
      </div>

      <!-- Table Body -->
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-gray-50/50">
              <th class="py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider border-b border-gray-100">Tanggal</th>
              <th class="py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider border-b border-gray-100">Nama Guru</th>
              <th class="py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider border-b border-gray-100">Kelas</th>
              <th class="py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider border-b border-gray-100">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr class="hover:bg-emerald-50/30 transition-colors duration-200">
              <td class="py-4 px-6 text-sm text-gray-600">24 Oct 2026, 08:30</td>
              <td class="py-4 px-6 text-sm font-bold text-gray-800">Budi Santoso, S.Pd</td>
              <td class="py-4 px-6 text-sm text-gray-600">XII IPA 1</td>
              <td class="py-4 px-6">
                <span class="px-3 py-1 bg-emerald-100/80 text-emerald-700 rounded-full text-xs font-bold tracking-wide">Selesai</span>
              </td>
            </tr>
            <tr class="hover:bg-emerald-50/30 transition-colors duration-200">
              <td class="py-4 px-6 text-sm text-gray-600">24 Oct 2026, 09:15</td>
              <td class="py-4 px-6 text-sm font-bold text-gray-800">Siti Aminah, M.Pd</td>
              <td class="py-4 px-6 text-sm text-gray-600">X IPS 2</td>
              <td class="py-4 px-6">
                <span class="px-3 py-1 bg-amber-100/80 text-amber-700 rounded-full text-xs font-bold tracking-wide">Menunggu</span>
              </td>
            </tr>
            <tr class="hover:bg-emerald-50/30 transition-colors duration-200">
              <td class="py-4 px-6 text-sm text-gray-600">24 Oct 2026, 10:00</td>
              <td class="py-4 px-6 text-sm font-bold text-gray-800">Ahmad Yani, S.Kom</td>
              <td class="py-4 px-6 text-sm text-gray-600">XI RPL</td>
              <td class="py-4 px-6">
                <span class="px-3 py-1 bg-emerald-100/80 text-emerald-700 rounded-full text-xs font-bold tracking-wide">Selesai</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
    </div>
  </div>
@endsection