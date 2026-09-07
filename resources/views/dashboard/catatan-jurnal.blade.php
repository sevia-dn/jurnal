@extends('layouts.app')

@section('title', 'Catatan Jurnal')

@section('content')
  <div class="journal-page">
    
    <!-- Header Halaman -->
    <div class="journal-head">
      <div class="journal-title-wrap">
        <h1>Monitoring Jurnal Real-time</h1>
        <p>Pantau status laporan kelas dan kehadiran guru hari ini.</p>
      </div>
      <div class="time-pill">
        <i class="bi bi-clock"></i>
        <span>07:45 WIB</span>
      </div>
    </div>

    <!-- 4 Kartu Statistik -->
    <div class="journal-stats">
      <!-- Card 1 -->
      <div class="journal-card top-card">
        <div class="journal-card-header">
          <div>
            <div class="journal-card-label">TOTAL KELAS</div>
            <div class="journal-card-value">36</div>
          </div>
          <div class="journal-card-icon green">
            <i class="bi bi-building"></i>
          </div>
        </div>
        <div>
          <div class="journal-card-note text-green">36 / 36 Lapor</div>
          <div class="progress-wrap">
            <div class="progress-bar"><span></span></div>
          </div>
        </div>
      </div>

      <!-- Card 2 -->
      <div class="journal-card top-card">
        <div class="journal-card-header">
          <div>
            <div class="journal-card-label">GURU HADIR</div>
            <div class="journal-card-value">34</div>
          </div>
          <div class="journal-card-icon green">
            <i class="bi bi-person-check"></i>
          </div>
        </div>
        <div class="meta-inline">
          <span class="delta">+2 dari jam lalu</span>
        </div>
      </div>

      <!-- Card 3 -->
      <div class="journal-card top-card">
        <div class="journal-card-header">
          <div>
            <div class="journal-card-label">GURU ABSEN / IZIN</div>
            <div class="journal-card-value">2</div>
          </div>
          <div class="journal-card-icon red">
            <i class="bi bi-person-slash"></i>
          </div>
        </div>
        <div class="journal-card-note text-red">Membutuhkan Inval</div>
      </div>

      <!-- Card 4 -->
      <div class="journal-card top-card">
        <div class="journal-card-header">
          <div>
            <div class="journal-card-label">MENUNGGU VALIDASI</div>
            <div class="journal-card-value">8</div>
          </div>
          <div class="journal-card-icon blue">
            <i class="bi bi-journal-text"></i>
          </div>
        </div>
        <div class="journal-card-note text-blue">Jurnal Kelas</div>
      </div>
    </div>

    <!-- Panel Utama Tabel & Tabs -->
    <div class="journal-table-panel">
      <!-- Baris Tabs & Filter yang menyatu di dalam panel -->
      <div class="journal-tabs-bar">
        <div class="journal-tabs">
          <a href="#" class="tab-pill active">Semua Kelas</a>
          <a href="#" class="tab-pill">Belum Validasi</a>
          <a href="#" class="tab-pill">Guru Absen</a>
        </div>
        <button class="filter-button">
          <i class="bi bi-filter"></i> Filter Data
        </button>
      </div>

      <!-- Tabel Data -->
      <div class="table-responsive">
        <table class="journal-table">
          <thead>
            <tr>
              <th>Waktu</th>
              <th>Kelas</th>
              <th>Guru & Mata Pelajaran</th>
              <th>Status Kehadiran</th>
              <th>Validasi Sekretaris</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>07:45</td>
              <td><strong>X MIPA 1</strong></td>
              <td>
                <div style="font-weight: 600; color: #0f172a;">Drs. Budi Santoso</div>
                <div style="font-size: 0.82rem; color: #64748b;">Matematika Wajib (Jam 1-2)</div>
              </td>
              <td><span class="journal-status status-hadir"><span class="status-dot"></span>Hadir</span></td>
              <td><span class="journal-status status-selesai"><span class="status-check"><i class="bi bi-check"></i></span>Selesai</span></td>
            </tr>
            <tr>
              <td>07:42</td>
              <td><strong>XI IPS 2</strong></td>
              <td>
                <div style="font-weight: 600; color: #0f172a;">Dra. Siti Aminah, M.Pd</div>
                <div style="font-size: 0.82rem; color: #64748b;">Sejarah Indonesia (Jam 1-2)</div>
              </td>
              <td><span class="journal-status status-hadir"><span class="status-dot"></span>Hadir</span></td>
              <td><span class="journal-status status-menunggu"><i class="bi bi-clock me-1"></i>Menunggu</span></td>
            </tr>
            <tr>
              <td>07:30</td>
              <td><strong>XII MIPA 3</strong></td>
              <td>
                <div style="font-weight: 600; color: #0f172a;">Agus Setiawan, S.Si</div>
                <div style="font-size: 0.82rem; color: #64748b;">Fisika (Jam 1-3)</div>
              </td>
              <td><span class="journal-status status-sakit"><span class="status-dot"></span>Sakit</span></td>
              <td><span class="journal-status status-selesai"><span class="status-check"><i class="bi bi-check"></i></span>Selesai</span></td>
            </tr>
            <tr>
              <td>07:28</td>
              <td><strong>X Bahasa</strong></td>
              <td>
                <div style="font-weight: 600; color: #0f172a;">Rina Melati, S.Pd</div>
                <div style="font-size: 0.82rem; color: #64748b;">Bahasa Inggris (Jam 1-2)</div>
              </td>
              <td><span class="journal-status status-hadir"><span class="status-dot"></span>Hadir</span></td>
              <td><span class="journal-status status-selesai"><span class="status-check"><i class="bi bi-check"></i></span>Selesai</span></td>
            </tr>
            <tr>
              <td>07:25</td>
              <td><strong>XI MIPA 2</strong></td>
              <td>
                <div style="font-weight: 600; color: #0f172a;">Ir. Wahyu Pratama</div>
                <div style="font-size: 0.82rem; color: #64748b;">Biologi (Jam 1-2)</div>
              </td>
              <td><span class="journal-status status-hadir"><span class="status-dot"></span>Hadir</span></td>
              <td><span class="journal-status status-selesai"><span class="status-check"><i class="bi bi-check"></i></span>Selesai</span></td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination Bawah -->
      <div class="pagination-bar">
        <span class="meta">Menampilkan 1-5 dari 36 kelas</span>
        <div class="pagination-controls">
          <button class="page-btn" disabled><i class="bi bi-chevron-left"></i></button>
          <button class="page-btn"><i class="bi bi-chevron-right"></i></button>
        </div>
      </div>
    </div>

  </div>
@endsection