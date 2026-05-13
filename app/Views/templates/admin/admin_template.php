<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Phy-FA-CAT</title>

    <link rel="icon" type="image/png" href="<?= base_url('assets/images/icon-cat.png') ?>">
    <link rel="apple-touch-icon" href="<?= base_url('assets/images/icon-cat.png') ?>">
    <link rel="shortcut icon" href="<?= base_url('assets/images/icon-cat.png') ?>">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --sidebar-width: 280px;
            --navbar-height: 70px;
            --primary-gradient: linear-gradient(135deg, #1a237e 0%, #0d47a1 100%);
        }

        body {
            min-height: 100vh;
            background-color: #f8f9fa;
        }

        .main-navbar {
            background: var(--primary-gradient);
            height: var(--navbar-height);
            padding: 0.5rem 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1030;
        }

        .main-navbar .navbar-brand {
            color: white;
            font-weight: 600;
            font-size: 1.3rem;
        }

        .main-navbar .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
        }

        .user-info {
            color: white;
        }

        .sidebar {
            width: var(--sidebar-width);
            background: white;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: var(--navbar-height);
            bottom: 0;
            left: 0;
            z-index: 1025;
            transition: transform 0.3s ease;
            overflow-y: auto;
        }

        .sidebar .nav-link {
            color: #424242;
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            transition: all 0.3s;
            border-radius: 0.5rem;
            margin: 0.2rem 0.8rem;
        }

        .sidebar .nav-link:hover {
            background-color: #e3f2fd;
            color: #1565c0;
        }

        .sidebar .nav-link.active {
            background-color: #1565c0;
            color: white;
        }

        .sidebar .nav-link i {
            font-size: 1.2rem;
            margin-right: 1rem;
        }

        .sidebar-section {
            padding: 0.5rem 1.5rem;
            margin-top: 1rem;
            font-size: 0.85rem;
            font-weight: 600;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .sidebar-divider {
            border: none;
            height: 1px;
            background-color: #e9ecef;
            margin: 0.5rem 1rem;
        }

        .content-wrapper {
            margin-left: var(--sidebar-width);
            padding: 2rem;
            min-height: calc(100vh - var(--navbar-height));
            transition: margin 0.3s ease;
        }

        .menu-card {
            border: none;
            border-radius: 1rem;
            transition: all 0.3s ease;
            background: white;
        }

        .menu-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .menu-card .card-body {
            padding: 2rem;
        }

        .menu-card .icon-wrapper {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .content-wrapper {
                margin-left: 0;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .nav-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1020;
                display: none;
            }

            .nav-overlay.show {
                display: block;
            }
        }
    </style>
</head>

<body>

    <nav class="navbar main-navbar fixed-top">
        <div class="container-fluid">
            <div class="d-flex align-items-center">
                <button class="btn text-white me-3 d-lg-none" id="sidebarToggle">
                    <i class="bi bi-list fs-4"></i>
                </button>
                <a class="navbar-brand" href="<?= base_url('admin/dashboard') ?>">
                    <i class="bi bi-mortarboard-fill me-2"></i>
                    Phy-FA-CAT
                </a>
            </div>

            <div class="d-flex align-items-center">
                <span class="user-info me-3 d-none d-md-block">
                    Selamat datang, <?= session()->get('username') ?>
                </span>
                <div class="dropdown profile-dropdown">
                    <button class="btn text-white dropdown-toggle" type="button" id="profileDropdown" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle fs-5"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                        <li><a class="dropdown-item" href="<?= base_url('admin/profil') ?>">
                                <i class="bi bi-person me-2"></i> Profil
                            </a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item text-danger" href="<?= base_url('logout') ?>">
                                <i class="bi bi-box-arrow-right me-2"></i> Logout
                            </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="nav-overlay" id="navOverlay"></div>

    <div class="sidebar" id="sidebar">
        <ul class="nav flex-column mt-3">
            <!-- Dashboard -->
            <li class="nav-item">
                <a href="<?= base_url('admin/dashboard') ?>" class="nav-link <?= current_url() == base_url('admin/dashboard') ? 'active' : '' ?>">
                    <i class="bi bi-house-door"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <hr class="sidebar-divider">
            <div class="sidebar-section">Kelola Institusi</div>

            <!-- Kelola Sekolah -->
            <li class="nav-item">
                <a href="<?= base_url('admin/sekolah') ?>" class="nav-link <?= (strpos(current_url(), 'admin/sekolah') !== false) ? 'active' : '' ?>">
                    <i class="bi bi-building"></i>
                    <span>Kelola Sekolah & Kelas</span>
                </a>
            </li>

            <hr class="sidebar-divider">
            <div class="sidebar-section">Kelola Pengguna</div>

            <!-- Kelola Guru -->
            <li class="nav-item">
                <a href="<?= base_url('admin/guru') ?>" class="nav-link <?= (strpos(current_url(), 'admin/guru') !== false) ? 'active' : '' ?>">
                    <i class="bi bi-person-workspace"></i>
                    <span>Kelola Guru</span>
                </a>
            </li>

            <!-- Kelola Siswa -->
            <li class="nav-item">
                <a href="<?= base_url('admin/siswa') ?>" class="nav-link <?= (strpos(current_url(), 'admin/siswa') !== false) ? 'active' : '' ?>">
                    <i class="bi bi-people"></i>
                    <span>Kelola Siswa</span>
                </a>
            </li>

            <hr class="sidebar-divider">
            <div class="sidebar-section">Kelola Ujian</div>

            <!-- Bank Ujian -->
            <li class="nav-item">
                <a href="<?= base_url('admin/bank-soal') ?>" class="nav-link <?= (strpos(current_url(), 'admin/bank-soal') !== false) ? 'active' : '' ?>">
                    <i class="bi bi-database"></i>
                    <span>Bank Ujian</span>
                </a>
            </li>
            <!-- kelola Mata Pelajaran -->
            <li class="nav-item">
                <a href="<?= base_url('admin/jenis-ujian') ?>" class="nav-link <?= (strpos(current_url(), 'admin/jenis-ujian') !== false) ? 'active' : '' ?>">
                    <i class="bi bi-journal"></i>
                    <span>Mata Pelajaran</span>
                </a>
            </li>

            <!-- Kelola Ujian -->
            <li class="nav-item">
                <a href="<?= base_url('admin/ujian') ?>" class="nav-link <?= (strpos(current_url(), 'admin/ujian') !== false) ? 'active' : '' ?>">
                    <i class="bi bi-file-earmark-text"></i>
                    <span>Kelola Ujian</span>
                </a>
            </li>

            <!-- Jadwal Ujian -->
            <li class="nav-item">
                <a href="<?= base_url('admin/jadwal-ujian') ?>" class="nav-link <?= (strpos(current_url(), 'admin/jadwal') !== false) ? 'active' : '' ?>">
                    <i class="bi bi-calendar-check"></i>
                    <span>Jadwal Ujian</span>
                </a>
            </li>

            <!-- Hasil Ujian -->
            <li class="nav-item">
                <a href="<?= base_url('admin/hasil-ujian') ?>" class="nav-link <?= (strpos(current_url(), 'admin/hasil-ujian') !== false) ? 'active' : '' ?>">
                    <i class="bi bi-bar-chart"></i>
                    <span>Hasil Ujian</span>
                </a>
            </li>

            <hr class="sidebar-divider">
            <div class="sidebar-section">Komunikasi</div>

            <!-- Kelola Pengumuman -->
            <li class="nav-item">
                <a href="<?= base_url('admin/pengumuman') ?>" class="nav-link <?= (strpos(current_url(), 'admin/pengumuman') !== false) ? 'active' : '' ?>">
                    <i class="bi bi-megaphone"></i>
                    <span>Kelola Pengumuman</span>
                </a>
            </li>
        </ul>
    </div>

    <main class="content-wrapper">
        <?= $this->renderSection('content') ?>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const navOverlay = document.getElementById('navOverlay');

            function toggleSidebar() {
                sidebar.classList.toggle('show');
                navOverlay.classList.toggle('show');
            }

            sidebarToggle.addEventListener('click', toggleSidebar);
            navOverlay.addEventListener('click', toggleSidebar);
        });
    </script>

</body>

</html>