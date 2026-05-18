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
    <!-- jQuery (required by Summernote) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Summernote Editor -->
    <link href="<?= base_url('assets/summernote/summernote-bs5.min.css') ?>" rel="stylesheet">
    <script src="<?= base_url('assets/summernote/summernote-bs5.min.js') ?>"></script>

    <style>
        :root {
            --sidebar-width: 268px;
            --navbar-height: 62px;
            --primary-gradient: linear-gradient(135deg, #1a237e 0%, #0d47a1 100%);
            --sidebar-active: linear-gradient(135deg, #1565c0 0%, #1976d2 100%);
        }

        body {
            min-height: 100vh;
            background-color: #f1f5f9;
        }

        /* ===== NAVBAR ===== */
        .main-navbar {
            background: var(--primary-gradient);
            height: var(--navbar-height);
            padding: 0 1.25rem;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.18);
            z-index: 1030;
        }

        .main-navbar .navbar-brand {
            color: white;
            font-weight: 700;
            font-size: 1.15rem;
            letter-spacing: -0.2px;
        }

        .user-info {
            color: rgba(255, 255, 255, 0.85);
            font-size: 0.875rem;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            width: var(--sidebar-width);
            background: #fff;
            border-right: 1px solid #e2e8f0;
            position: fixed;
            top: var(--navbar-height);
            bottom: 0;
            left: 0;
            z-index: 1025;
            transition: transform 0.3s ease;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* Scrollable Nav Wrapper */
        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: 0.4rem 0 1rem;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }

        .sidebar-nav::-webkit-scrollbar {
            width: 3px;
        }

        .sidebar-nav::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-nav::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        /* Section Labels */
        .sidebar-section {
            padding: 0.9rem 1.25rem 0.3rem;
            font-size: 0.64rem;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            list-style: none;
        }

        /* Nav Links */
        .sidebar .nav-link {
            color: #475569;
            padding: 0.575rem 1rem;
            margin: 0.08rem 0.625rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            transition: background 0.18s ease, color 0.18s ease, box-shadow 0.18s ease;
            text-decoration: none;
        }

        .sidebar .nav-link i {
            font-size: 1rem;
            margin-right: 0.65rem;
            color: #64748b;
            transition: color 0.18s;
            flex-shrink: 0;
            width: 18px;
            text-align: center;
        }

        .sidebar .nav-link span {
            flex: 1;
        }

        .sidebar .nav-link:hover {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .sidebar .nav-link:hover > i {
            color: #1d4ed8;
        }

        .sidebar .nav-link.active {
            background: var(--sidebar-active);
            color: #fff;
            box-shadow: 0 3px 10px rgba(21, 101, 192, 0.28);
        }

        .sidebar .nav-link.active > i {
            color: rgba(255, 255, 255, 0.92);
        }

        /* Collapsible Toggle */
        .sidebar-toggle {
            cursor: pointer;
            display: flex !important;
            align-items: center;
        }

        .sidebar-toggle .toggle-icon {
            font-size: 0.62rem;
            transition: transform 0.25s;
            color: #94a3b8;
            margin-left: auto;
            flex-shrink: 0;
        }

        .sidebar-toggle[aria-expanded="true"] {
            background: #f0f7ff;
            color: #1d4ed8;
        }

        .sidebar-toggle[aria-expanded="true"] > i:first-child {
            color: #1d4ed8;
        }

        .sidebar-toggle[aria-expanded="true"] .toggle-icon {
            transform: rotate(180deg);
            color: #1d4ed8;
        }

        /* Submenu */
        .submenu {
            margin: 0.1rem 0.625rem;
            padding: 0.2rem 0;
            list-style: none;
        }

        .submenu .nav-link {
            padding: 0.45rem 0.75rem 0.45rem 2.35rem !important;
            font-size: 0.84rem;
            color: #64748b;
            margin: 0.04rem 0 !important;
            border-radius: 0.4rem;
            font-weight: 400;
        }

        .submenu .nav-link i {
            font-size: 0.85rem !important;
            margin-right: 0.5rem !important;
            color: #94a3b8 !important;
            width: 15px !important;
        }

        .submenu .nav-link:hover {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .submenu .nav-link:hover i {
            color: #1d4ed8 !important;
        }

        .submenu .nav-link.active {
            background: #dbeafe;
            color: #1d4ed8;
            font-weight: 600;
            box-shadow: none;
        }

        .submenu .nav-link.active i {
            color: #1d4ed8 !important;
        }

        /* Divider */
        .sidebar-divider {
            border: none;
            height: 1px;
            background: #e2e8f0;
            margin: 0.4rem 1.1rem;
        }

        /* ===== CONTENT ===== */
        .content-wrapper {
            margin-left: var(--sidebar-width);
            margin-top: var(--navbar-height);
            padding: 1.75rem 2rem;
            min-height: calc(100vh - var(--navbar-height));
            transition: margin 0.3s ease;
        }

        /* ===== CARDS ===== */
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

        /* ===== RESPONSIVE ===== */
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
                background: rgba(0, 0, 0, 0.45);
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
                <button class="btn text-white me-3 d-lg-none p-1" id="sidebarToggle">
                    <i class="bi bi-list fs-4"></i>
                </button>
                <a class="navbar-brand" href="<?= base_url('admin/dashboard') ?>">
                    <i class="bi bi-mortarboard-fill me-2"></i>
                    Phy-FA-CAT
                </a>
            </div>

            <div class="d-flex align-items-center gap-2">
                <span class="user-info d-none d-md-block">
                    <?= session()->get('username') ?>
                </span>
                <div class="dropdown">
                    <button class="btn text-white d-flex align-items-center gap-1 p-1 px-2" type="button"
                        id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle fs-5"></i>
                        <i class="bi bi-chevron-down" style="font-size:0.6rem"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 py-1" aria-labelledby="profileDropdown"
                        style="min-width:175px; border-radius:0.6rem">
                        <li><a class="dropdown-item d-flex align-items-center gap-2 py-2" href="<?= base_url('admin/profil') ?>">
                                <i class="bi bi-person text-primary"></i> Profil Saya
                            </a></li>
                        <li>
                            <hr class="dropdown-divider my-1">
                        </li>
                        <li><a class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger" href="<?= base_url('logout') ?>">
                                <i class="bi bi-box-arrow-right"></i> Keluar
                            </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="nav-overlay" id="navOverlay"></div>

    <div class="sidebar" id="sidebar">

        <!-- Scrollable Navigation -->
        <div class="sidebar-nav">
            <ul class="nav flex-column" style="list-style:none; padding:0; margin:0">

                <!-- Menu Utama -->
                <li class="sidebar-section">Menu Utama</li>
                <li class="nav-item">
                    <a href="<?= base_url('admin/dashboard') ?>"
                        class="nav-link <?= current_url() == base_url('admin/dashboard') ? 'active' : '' ?>">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <!-- Data -->
                <li class="sidebar-section">Data</li>
                <li class="nav-item">
                    <a class="nav-link sidebar-toggle" data-bs-toggle="collapse" href="#collapseMaster" role="button">
                        <i class="bi bi-gear-wide-connected"></i>
                        <span>Data Master</span>
                        <i class="bi bi-chevron-down toggle-icon"></i>
                    </a>
                    <div class="collapse <?= (strpos(current_url(), 'admin/sekolah') !== false || strpos(current_url(), 'admin/guru') !== false || strpos(current_url(), 'admin/siswa') !== false || strpos(current_url(), 'admin/jenis-ujian') !== false) ? 'show' : '' ?>"
                        id="collapseMaster">
                        <ul class="nav flex-column submenu">
                            <li class="nav-item">
                                <a href="<?= base_url('admin/sekolah') ?>"
                                    class="nav-link <?= (strpos(current_url(), 'admin/sekolah') !== false) ? 'active' : '' ?>">
                                    <i class="bi bi-building"></i>Sekolah & Kelas
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('admin/guru') ?>"
                                    class="nav-link <?= (strpos(current_url(), 'admin/guru') !== false) ? 'active' : '' ?>">
                                    <i class="bi bi-person-workspace"></i>Guru
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('admin/siswa') ?>"
                                    class="nav-link <?= (strpos(current_url(), 'admin/siswa') !== false) ? 'active' : '' ?>">
                                    <i class="bi bi-people"></i>Siswa
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('admin/jenis-ujian') ?>"
                                    class="nav-link <?= (strpos(current_url(), 'admin/jenis-ujian') !== false) ? 'active' : '' ?>">
                                    <i class="bi bi-journal"></i>Mata Pelajaran
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Konten -->
                <li class="sidebar-section">Konten</li>
                <li class="nav-item">
                    <a class="nav-link sidebar-toggle" data-bs-toggle="collapse" href="#collapseBank" role="button">
                        <i class="bi bi-layers"></i>
                        <span>Bank & Metadata</span>
                        <i class="bi bi-chevron-down toggle-icon"></i>
                    </a>
                    <div class="collapse <?= (strpos(current_url(), 'admin/bank-soal') !== false || strpos(current_url(), 'admin/variabel') !== false || strpos(current_url(), 'admin/indikator') !== false || strpos(current_url(), 'admin/materi') !== false) ? 'show' : '' ?>"
                        id="collapseBank">
                        <ul class="nav flex-column submenu">
                            <li class="nav-item">
                                <a href="<?= base_url('admin/bank-soal') ?>"
                                    class="nav-link <?= (strpos(current_url(), 'admin/bank-soal') !== false) ? 'active' : '' ?>">
                                    <i class="bi bi-database"></i>Bank Soal
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('admin/variabel') ?>"
                                    class="nav-link <?= (strpos(current_url(), 'admin/variabel') !== false) ? 'active' : '' ?>">
                                    <i class="bi bi-diagram-3"></i>Variabel
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('admin/indikator') ?>"
                                    class="nav-link <?= (strpos(current_url(), 'admin/indikator') !== false) ? 'active' : '' ?>">
                                    <i class="bi bi-list-check"></i>Indikator
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('admin/materi') ?>"
                                    class="nav-link <?= (strpos(current_url(), 'admin/materi') !== false) ? 'active' : '' ?>">
                                    <i class="bi bi-book"></i>Materi
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Ujian -->
                <li class="sidebar-section">Ujian</li>
                <li class="nav-item">
                    <a class="nav-link sidebar-toggle" data-bs-toggle="collapse" href="#collapseUjian" role="button">
                        <i class="bi bi-pencil-square"></i>
                        <span>Kelola Ujian</span>
                        <i class="bi bi-chevron-down toggle-icon"></i>
                    </a>
                    <div class="collapse <?= (strpos(current_url(), 'admin/ujian') !== false || strpos(current_url(), 'admin/soal') !== false || strpos(current_url(), 'admin/jadwal') !== false || strpos(current_url(), 'admin/hasil') !== false) ? 'show' : '' ?>"
                        id="collapseUjian">
                        <ul class="nav flex-column submenu">
                            <li class="nav-item">
                                <a href="<?= base_url('admin/ujian') ?>"
                                    class="nav-link <?= (strpos(current_url(), 'admin/ujian') !== false) ? 'active' : '' ?>">
                                    <i class="bi bi-file-earmark-text"></i>Daftar Ujian
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('admin/jadwal-ujian') ?>"
                                    class="nav-link <?= (strpos(current_url(), 'admin/jadwal') !== false) ? 'active' : '' ?>">
                                    <i class="bi bi-calendar-check"></i>Jadwal Ujian
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('admin/hasil-ujian') ?>"
                                    class="nav-link <?= (strpos(current_url(), 'admin/hasil-ujian') !== false) ? 'active' : '' ?>">
                                    <i class="bi bi-bar-chart"></i>Hasil Ujian
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Lainnya -->
                <li class="sidebar-section">Lainnya</li>
                <li class="nav-item">
                    <a href="<?= base_url('admin/pengumuman') ?>"
                        class="nav-link <?= (strpos(current_url(), 'admin/pengumuman') !== false) ? 'active' : '' ?>">
                        <i class="bi bi-megaphone"></i>
                        <span>Pengumuman</span>
                    </a>
                </li>

            </ul>
        </div>

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

            if (sidebarToggle) sidebarToggle.addEventListener('click', toggleSidebar);
            if (navOverlay) navOverlay.addEventListener('click', toggleSidebar);
        });
    </script>

</body>

</html>
