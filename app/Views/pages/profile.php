<?= $this->extend('templates/header') ?>

<?= $this->section('content') ?>
<div class="profile-container">
    <div class="container py-5">
        <h1 class="profile-title text-center mb-5">Tim Pengembang PHY-FA-CAT</h1>

        <div class="row justify-content-center g-4">
            <!-- Pengembang -->
            <div class="col-md-4">
                <div class="team-card">
                    <div class="card-image">
                        <img src="<?= base_url('assets/images/profil/albert.webp') ?>" alt="Foto Pengembang">
                    </div>
                    <div class="card-content">
                        <h3>Jauza Amalia</h3>
                        <p class="role">Web Developer & Researcher</p>
                        <p class="institution">Universitas Pendidikan Indonesia</p>
                        <button class="view-btn" data-bs-toggle="modal" data-bs-target="#developerModal">
                            <i class="bi bi-person-lines-fill"></i> Lihat Profil
                        </button>
                    </div>
                </div>
            </div>

            <!-- Pembimbing 1 -->
            <div class="col-md-4">
                <div class="team-card">
                    <div class="card-image">
                        <img src="<?= base_url('assets/images/profil/albert.webp') ?>" alt="Foto Pembimbing 1">
                    </div>
                    <div class="card-content">
                        <h3>Prof. Dr. Pembimbing Satu</h3>
                        <p class="role">Dosen Pembimbing 1</p>
                        <p class="institution">Universitas Negeri Yogyakarta</p>
                        <button class="view-btn" data-bs-toggle="modal" data-bs-target="#supervisor1Modal">
                            <i class="bi bi-person-lines-fill"></i> Lihat Profil
                        </button>
                    </div>
                </div>
            </div>

            <!-- Pembimbing 2 -->
            <div class="col-md-4">
                <div class="team-card">
                    <div class="card-image">
                        <img src="<?= base_url('assets/images/profil/albert.webp') ?>" alt="Foto Pembimbing 2">
                    </div>
                    <div class="card-content">
                        <h3>Dr. Pembimbing Dua</h3>
                        <p class="role">Dosen Pembimbing 2</p>
                        <p class="institution">Universitas Negeri Yogyakarta</p>
                        <button class="view-btn" data-bs-toggle="modal" data-bs-target="#supervisor2Modal">
                            <i class="bi bi-person-lines-fill"></i> Lihat Profil
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pengembang -->
<div class="modal fade" id="developerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content custom-modal">
            <div class="modal-header">
                <h5 class="modal-title">Profil Pengembang</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="profile-image-container">
                            <img src="<?= base_url('assets/images/profil/albert.webp') ?>" class="img-fluid rounded" alt="Foto Pengembang">
                        </div>
                    </div>
                    <div class="col-md-8">
                        <h4>Jauza Amalia</h4>
                        <p class="role-title">Web Developer & Researcher</p>

                        <div class="info-section">
                            <h5><i class="bi bi-person-vcard"></i> Tentang</h5>
                            <p class="description">
                                Seorang peneliti yang fokus pada pengembangan asesmen diagnostik yang adaptif untuk pembelajaran Fisika. Memiliki passion untuk mengintegrasikan teknologi dengan pendidikan agar dapat menciptakan pengalaman pembelajaran yang lebih terpersonalisasi.
                            </p>
                        </div>

                        <div class="info-section">
                            <h5><i class="bi bi-mortarboard"></i> Pendidikan</h5>
                            <ul class="custom-list">
                                <li>S1 Pendidikan Fisika - Universitas Pendidikan Indonesia</li>
                            </ul>
                        </div>

                        <div class="info-section">
                            <h5><i class="bi bi-award"></i> Prestasi</h5>
                            <ul class="custom-list">
                                <li>Juara 3 Video Animasi PHYFEST 8.0</li>
                            </ul>
                        </div>

                        <div class="info-section">
                            <h5><i class="bi bi-envelope"></i> Kontak</h5>
                            <div class="contact-links">
                                <a href="mailto:jauzaamalia@upi.edu" target="_blank" class="contact-item">
                                    <i class="bi bi-envelope-fill"></i> jauzaamalia@upi.edu
                                </a>
                                <a href="https://www.linkedin.com/in/jauza-amalia-906070328/?originalSubdomain=id" target="_blank" class="contact-item">
                                    <i class="bi bi-linkedin"></i> linkedin.com/in/Jauza Amalia
                                </a>
                                <a href="https://wa.me/6285794124143" target="_blank" class="contact-item">
                                    <i class="bi bi-whatsapp"></i> +62 857 9412 4143
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pembimbing 1 -->
<div class="modal fade" id="supervisor1Modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content custom-modal">
            <div class="modal-header">
                <h5 class="modal-title">Profil Pembimbing 1</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="profile-image-container">
                            <img src="<?= base_url('assets/images/profil/albert.webp') ?>" class="img-fluid rounded" alt="Foto Pembimbing 1">
                        </div>
                    </div>
                    <div class="col-md-8">
                        <h4>Prof. Dr. Pembimbing Satu</h4>
                        <p class="role-title">Professor di Jurusan Pendidikan Fisika</p>

                        <div class="info-section">
                            <h5><i class="bi bi-person-vcard"></i> Tentang</h5>
                            <p class="description">
                                Profesor senior dengan pengalaman lebih dari 25 tahun dalam pendidikan fisika. Fokus penelitian pada pengembangan metode asesmen inovatif dan media pembelajaran berbasis teknologi. Telah membimbing lebih dari 50 mahasiswa S2 dan S3.
                            </p>
                        </div>

                        <div class="info-section">
                            <h5><i class="bi bi-book"></i> Bidang Keahlian</h5>
                            <ul class="custom-list">
                                <li>Pengembangan Media Pembelajaran Fisika</li>
                                <li>Asesmen dan Evaluasi Pendidikan</li>
                                <li>Teknologi Pendidikan Fisika</li>
                            </ul>
                        </div>

                        <div class="info-section">
                            <h5><i class="bi bi-journal-text"></i> Publikasi Terpilih</h5>
                            <ul class="custom-list">
                                <li>Assessment Methods in Physics Education (2023)</li>
                                <li>Technology Integration in Science Education (2022)</li>
                                <li>Adaptive Testing Development (2021)</li>
                            </ul>
                        </div>

                        <div class="info-section">
                            <h5><i class="bi bi-envelope"></i> Kontak</h5>
                            <div class="contact-links">
                                <a href="mailto:pembimbing1@uny.ac.id" class="contact-item">
                                    <i class="bi bi-envelope-fill"></i> pembimbing1@uny.ac.id
                                </a>
                                <a href="#" class="contact-item">
                                    <i class="bi bi-google"></i> Google Scholar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pembimbing 2 -->
<div class="modal fade" id="supervisor2Modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content custom-modal">
            <div class="modal-header">
                <h5 class="modal-title">Profil Pembimbing 2</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="profile-image-container">
                            <img src="<?= base_url('assets/images/profil/albert.jpg') ?>" class="img-fluid rounded" alt="Foto Pembimbing 2">
                        </div>
                    </div>
                    <div class="col-md-8">
                        <h4>Dr. Pembimbing Dua</h4>
                        <p class="role-title">Dosen Jurusan Pendidikan Fisika</p>

                        <div class="info-section">
                            <h5><i class="bi bi-person-vcard"></i> Tentang</h5>
                            <p class="description">
                                Peneliti aktif dalam bidang teknologi pendidikan dan computer adaptive testing. Berpengalaman dalam pengembangan sistem asesmen berbasis komputer dan implementasi pembelajaran digital. Aktif dalam berbagai proyek penelitian internasional.
                            </p>
                        </div>

                        <div class="info-section">
                            <h5><i class="bi bi-book"></i> Bidang Keahlian</h5>
                            <ul class="custom-list">
                                <li>Computer Adaptive Testing</li>
                                <li>Educational Data Mining</li>
                                <li>E-Learning Systems Development</li>
                            </ul>
                        </div>

                        <div class="info-section">
                            <h5><i class="bi bi-graph-up"></i> Riset Terkini</h5>
                            <ul class="custom-list">
                                <li>Pengembangan Algoritma CAT untuk Pembelajaran Sains</li>
                                <li>Implementasi AI dalam Asesmen Pendidikan</li>
                                <li>Learning Analytics untuk Personalisasi Pembelajaran</li>
                            </ul>
                        </div>

                        <div class="info-section">
                            <h5><i class="bi bi-envelope"></i> Kontak</h5>
                            <div class="contact-links">
                                <a href="mailto:pembimbing2@uny.ac.id" class="contact-item">
                                    <i class="bi bi-envelope-fill"></i> pembimbing2@uny.ac.id
                                </a>
                                <a href="#" class="contact-item">
                                    <i class="bi bi-diagram-3"></i> ResearchGate
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .profile-container {
        background: linear-gradient(276deg, #17376E -2.09%, #481F64 75.22%);
        min-height: 100vh;
        color: white;
    }

    .profile-title {
        font-size: 2.5rem;
        font-weight: bold;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
    }

    .team-card {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 20px;
        overflow: hidden;
        backdrop-filter: blur(10px);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .team-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }

    .card-image {
        position: relative;
        overflow: hidden;
    }

    .card-image img {
        width: 100%;
        height: 300px;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .team-card:hover .card-image img {
        transform: scale(1.05);
    }

    .card-content {
        padding: 1.5rem;
        text-align: center;
    }

    .card-content h3 {
        font-size: 1.4rem;
        margin-bottom: 0.5rem;
        font-weight: bold;
    }

    .role {
        color: rgba(255, 255, 255, 0.9);
        margin-bottom: 0.5rem;
    }

    .institution {
        color: rgba(255, 255, 255, 0.7);
        font-size: 0.9rem;
        margin-bottom: 1rem;
    }

    .view-btn {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        padding: 0.5rem 1.5rem;
        border-radius: 25px;
        color: white;
        transition: all 0.3s ease;
    }

    .view-btn:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateY(-2px);
    }

    .custom-modal .modal-content {
        background: linear-gradient(145deg, #ffffff, #f8f9fa);
        color: #333;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }

    .custom-modal .modal-header {
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        padding: 1.5rem;
    }

    .modal-title {
        color: #17376E;
        font-weight: bold;
    }

    .custom-modal .modal-footer {
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .role-title {
        color: #333 !important;
        font-size: 1.1rem;
        margin-bottom: 1.5rem;
        font-weight: 500;
    }

    .info-section {
        margin-bottom: 1.5rem;
    }

    .info-section p {
        color: #333 !important;
    }

    .info-section h5 {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: rgba(255, 255, 255, 0.9);
        margin-bottom: 1rem;
    }

    .info-section ul {
        list-style: none;
        padding-left: 1.5rem;
    }

    .info-section ul li {
        margin-bottom: 0.5rem;
        position: relative;
    }

    .info-section ul li::before {
        content: "•";
        position: absolute;
        left: -1rem;
        color: rgba(255, 255, 255, 0.6);
    }

    .contact-info {
        padding-left: 1.5rem;
        color: rgba(255, 255, 255, 0.8);
    }

    /* Modal Styles */
    .custom-modal .modal-content {
        background: linear-gradient(145deg, #17376E, #481F64);
        color: white;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }

    .custom-modal .modal-header {
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding: 1.5rem;
    }

    .custom-modal .modal-body {
        padding: 2rem;
    }

    .modal-body {
        color: #333 !important;
    }

    .profile-image-container {
        position: relative;
        overflow: hidden;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    .profile-image-container img {
        width: 100%;
        height: auto;
        transform: scale(1);
        transition: transform 0.3s ease;
    }

    .profile-image-container:hover img {
        transform: scale(1.05);
    }

    .role-title {
        color: rgba(255, 255, 255, 0.9);
        font-size: 1.1rem;
        margin-bottom: 1.5rem;
    }

    .description {
        color: #333;
        line-height: 1.6;
        text-align: justify;
    }

    .info-section {
        margin-bottom: 1.5rem;
    }

    .info-section * {
        color: #333 !important;
    }

    .info-section h5 {
        color: #17376E;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 600;
    }

    .info-section h5 i {
        color: #481F64;
    }

    .custom-list {
        list-style: none;
        padding-left: 1rem;
    }

    .custom-list li {
        color: #333 !important;
        margin-bottom: 0.5rem;
        position: relative;
        padding-left: 1.5rem;
    }

    .custom-list li::before {
        content: "•";
        position: absolute;
        left: 0;
        color: #481F64;
    }

    .contact-links {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .contact-item {
        color: #17376E;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
    }

    .contact-item:hover {
        color: #481F64;
        transform: translateX(5px);
    }

    .btn-close {
        opacity: 0.8;
        filter: none !important;
    }

    .btn-close:hover {
        opacity: 1;
    }

    @media (max-width: 768px) {
        .modal-body {
            padding: 1rem;
        }

        .profile-image-container {
            margin-bottom: 1.5rem;
        }
    }

    @media (max-width: 768px) {
        .profile-title {
            font-size: 2rem;
        }

        .team-card {
            margin-bottom: 2rem;
        }
    }
</style>
<?= $this->endSection() ?>