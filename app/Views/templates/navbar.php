<style>
    .navbar {
        background: linear-gradient(90deg, #17376E 0%, #481F64 100%);
        padding: 15px 0;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .navbar-brand {
        color: white !important;
        font-weight: 700;
        font-size: 1.5rem;
        letter-spacing: 1px;
    }

    .navbar-nav {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
    }

    .navbar-nav .nav-item {
        margin: 0 15px;
        /* Tambah jarak antar menu */
    }

    .navbar-nav .nav-link {
        color: rgba(255, 255, 255, 0.7) !important;
        font-weight: 500;
        transition: all 0.3s ease;
        position: relative;
        padding: 10px 0;
    }

    .navbar-nav .nav-link:hover,
    .navbar-nav .nav-link.active {
        color: white !important;
    }

    .navbar-nav .nav-link::after {
        content: '';
        position: absolute;
        width: 0;
        height: 2px;
        bottom: -5px;
        left: 50%;
        background-color: white;
        transition: all 0.3s ease;
        transform: translateX(-50%);
    }

    .navbar-nav .nav-link:hover::after,
    .navbar-nav .nav-link.active::after {
        width: 100%;
    }



    .navbar-toggler-icon {
        background-image: url("data:image/svg+xml;charset=utf8,%3Csvg viewBox='0 0 30 30' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath stroke='rgba(255,255,255,1)' stroke-width='2' stroke-linecap='round' stroke-miterlimit='10' d='M4 7h22M4 15h22M4 23h22'/%3E%3C/svg%3E");
    }

    @media (max-width: 991px) {
        .navbar-nav {
            flex-direction: column;
            align-items: center;
        }

        .navbar-nav .nav-item {
            margin: 10px 0;
        }

        .navbar .btn-group {
            margin-left: 0;
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }
    }
</style>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="<?= base_url() ?>">PHY-FA-CAT</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link <?= uri_string() == '' ? 'active' : '' ?>" href="<?= base_url() ?>">Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= uri_string() == 'about' ? 'active' : '' ?>" href="<?= base_url('about') ?>">Tentang Kami</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= uri_string() == 'faq' ? 'active' : '' ?>" href="<?= base_url('faq') ?>">FAQ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= uri_string() == 'guide' ? 'active' : '' ?>" href="<?= base_url('guide') ?>">Petunjuk Penggunaan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= uri_string() == 'bantuan' ? 'active' : '' ?>" href="<?= base_url('bantuan') ?>">Bantuan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= uri_string() == 'profile' ? 'active' : '' ?>" href="<?= base_url('profile') ?>">Profil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= uri_string() == 'contact' ? 'active' : '' ?>" href="<?= base_url('contact') ?>">Saran</a>
                </li>
            </ul>

        </div>
    </div>
</nav>