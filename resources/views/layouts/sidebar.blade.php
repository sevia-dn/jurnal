<aside class="sidebar">
  <div class="sidebar-inner d-flex flex-column h-100">
    <div class="logo-wrap p-3">
      <div class="d-flex align-items-center">
        <div class="logo-icon me-3"><i class="bi bi-mortarboard-fill"></i></div>
        <div class="logo-text">
          <div class="brand">JurnalKita</div>
          <div class="subtitle">Management System</div>
        </div>
      </div>
    </div>

    <nav class="nav flex-column px-3">
      <a href="{{ route('dashboard') }}" class="nav-link active"><i class="bi bi-grid"></i><span>Halaman Utama</span></a>
      <a href="#" class="nav-link"><i class="bi bi-person"></i><span>Data Guru</span></a>
      <a href="#" class="nav-link"><i class="bi bi-book"></i><span>Data Kelas</span></a>
      <a href="{{ route('dashboard') }}" class="nav-link"><i class="bi bi-journal-text"></i><span>Catatan Jurnal</span></a>
    </nav>

    <div class="mt-auto p-3">
      <a href="#" class="btn btn-add w-100 mb-3">+ Tambah Jurnal</a>
      <hr style="border-color: rgba(255,255,255,0.1);">
      <a href="#" class="d-block mb-3 setting-link"><i class="bi bi-gear me-2"></i>Pengaturan</a>
      <a href="{{ route('login') }}" class="d-block logout-link"><i class="bi bi-box-arrow-right me-2"></i>Keluar</a>
    </div>
  </div>
</aside>