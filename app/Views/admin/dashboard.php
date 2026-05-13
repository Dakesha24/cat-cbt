<?= $this->extend('templates/admin/admin_template') ?>

<?= $this->section('content') ?>
<br>

<div class="container-fluid px-4 py-5">

  <div class="mb-5">
    <h1 class="h3 fw-bold">Dashboard Admin</h1>
    <p class="text-muted">Selamat datang kembali! Berikut adalah ringkasan sistem Anda.</p>
  </div>

  <div class="row g-4 mb-5">
    <div class="col-lg-3 col-md-6">
      <div class="card shadow-sm border-0 h-100">
        <div class="card-body d-flex align-items-center">
          <div class="flex-grow-1">
            <h4 class="fw-bold mb-1"><?= $stats['total_guru'] ?? 0 ?></h4>
            <p class="text-muted mb-0">Total Guru</p>
          </div>
          <div class="icon-circle bg-primary-subtle text-primary">
            <i class="bi bi-person-workspace fs-4"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6">
      <div class="card shadow-sm border-0 h-100">
        <div class="card-body d-flex align-items-center">
          <div class="flex-grow-1">
            <h4 class="fw-bold mb-1"><?= $stats['total_siswa'] ?? 0 ?></h4>
            <p class="text-muted mb-0">Total Siswa</p>
          </div>
          <div class="icon-circle bg-success-subtle text-success">
            <i class="bi bi-people fs-4"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6">
      <div class="card shadow-sm border-0 h-100">
        <div class="card-body d-flex align-items-center">
          <div class="flex-grow-1">
            <h4 class="fw-bold mb-1"><?= $stats['total_sekolah'] ?? 0 ?></h4>
            <p class="text-muted mb-0">Total Sekolah</p>
          </div>
          <div class="icon-circle bg-info-subtle text-info">
            <i class="bi bi-building fs-4"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6">
      <div class="card shadow-sm border-0 h-100">
        <div class="card-body d-flex align-items-center">
          <div class="flex-grow-1">
            <h4 class="fw-bold mb-1"><?= $stats['total_kelas'] ?? 0 ?></h4>
            <p class="text-muted mb-0">Total Kelas</p>
          </div>
          <div class="icon-circle bg-warning-subtle text-warning">
            <i class="bi bi-door-open fs-4"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <h2 class="h4 fw-bold mb-4">Menu Utama</h2>
  <div class="row g-4">
    <?php
    $menuItems = [
      ['title' => 'Kelola Guru', 'desc' => 'Kelola data guru dalam sistem.', 'icon' => 'bi-person-workspace', 'color' => 'primary', 'url' => 'admin/guru'],
      ['title' => 'Kelola Siswa', 'desc' => 'Kelola data siswa dalam sistem.', 'icon' => 'bi-people', 'color' => 'success', 'url' => 'admin/siswa'],
      ['title' => 'Sekolah & Kelas', 'desc' => 'Kelola sekolah, kelas, dan siswa.', 'icon' => 'bi-building-gear', 'color' => 'info', 'url' => 'admin/sekolah'],
      ['title' => 'Bank Ujian', 'desc' => 'Kelola bank soal dan koleksi ujian.', 'icon' => 'bi-database', 'color' => 'purple', 'url' => 'admin/bank-soal'],
      ['title' => 'Mata Pelajaran', 'desc' => 'Monitor ujian yang dibuat oleh guru.', 'icon' => 'bi-journal-text', 'color' => 'info', 'url' => 'admin/jenis-ujian'],
      ['title' => 'Kelola Ujian', 'desc' => 'Monitor ujian yang dibuat oleh guru.', 'icon' => 'bi-file-earmark-text', 'color' => 'danger', 'url' => 'admin/ujian'],
      ['title' => 'Jadwal Ujian', 'desc' => 'Monitor jadwal dan peserta ujian.', 'icon' => 'bi-calendar-check', 'color' => 'secondary', 'url' => 'admin/jadwal-ujian'],
      ['title' => 'Hasil Ujian', 'desc' => 'Analisis hasil ujian para siswa.', 'icon' => 'bi-bar-chart-line', 'color' => 'success', 'url' => 'admin/hasil-ujian'],
      ['title' => 'Pengumuman', 'desc' => 'Kelola pengumuman untuk semua user.', 'icon' => 'bi-megaphone', 'color' => 'dark', 'url' => 'admin/pengumuman'],
    ];
    ?>

    <?php foreach ($menuItems as $item) : ?>
      <div class="col-xl-3 col-lg-4 col-md-6">
        <a href="<?= base_url($item['url']) ?>" class="text-decoration-none menu-link">
          <div class="card menu-card h-100">
            <div class="card-body text-center">
              <div class="icon-wrapper bg-<?= $item['color'] ?>-subtle text-<?= $item['color'] ?> mx-auto mb-3">
                <i class="bi <?= $item['icon'] ?> fs-2"></i>
              </div>
              <h5 class="card-title fw-bold text-body"><?= $item['title'] ?></h5>
              <p class="card-text small text-muted"><?= $item['desc'] ?></p>
            </div>
          </div>
        </a>
      </div>
    <?php endforeach; ?>

  </div>

</div>

<style>
  /* Latar belakang halaman yang netral */
  body {
    background-color: #f8f9fa;
  }

  /* Lingkaran Ikon untuk Kartu Statistik */
  .icon-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    /* Mencegah ikon menyusut */
  }

  /* Desain Kartu Menu */
  .menu-card {
    border: 1px solid #e9ecef;
    transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    border-radius: 0.75rem;
    /* Sedikit lebih bulat */
  }

  .menu-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1);
    border-color: var(--bs-primary);
  }

  /* Wrapper Ikon pada Kartu Menu */
  .menu-card .icon-wrapper {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  /* Menghilangkan garis bawah pada tautan kartu */
  .menu-link {
    color: inherit;
  }

  /* Custom Color: Purple (untuk Bank Ujian) */
  .bg-purple-subtle {
    background-color: rgba(102, 16, 242, 0.1);
  }

  .text-purple {
    color: #6f42c1;
  }

  .menu-card:hover .text-purple {
    color: #6610f2 !important;
  }
</style>

<?= $this->endSection() ?>