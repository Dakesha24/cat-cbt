<?= $this->extend('templates/header') ?>

<?= $this->section('content') ?>
<div class="about-container">
    <div class="container py-5">
        <h1 class="about-title text-center mb-5">Tentang Kami</h1>

        <div class="about-section">
            <div class="intro-card">
                <div class="card-icon">
                    <i class="bi bi-lightbulb-fill"></i>
                </div>
                <h2>Selamat Datang di PHY-FA-CAT</h2>
                <p>PHY-FA-CAT (<i>Physics-Formativec Assessment-Computerized Adaptive Test</i>) adalah platform asesmen diagnostik berbasis <i>Computerized Adaptive Testing</i> (CAT) yang dirancang untuk membantu siswa, guru, dan institusi pendidikan mencapai hasil belajar yang maksimal.</p>
                <p>Platform ini memanfaatkan teknologi komputer untuk memberikan soal-soal yang secara otomatis menyesuaikan dengan kemampuan peserta tes. Dengan pendekatan adaptif ini, setiap pengguna mendapatkan pengalaman asesmen yang inovatif, menantang, dan relevan dengan tingkat pemahamannya.</p>
            </div>
        </div>

        <div class="about-section">
            <h2 class="section-title">Fitur Utama</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-cpu-fill"></i>
                        </div>
                        <h3>Adaptif</h3>
                        <p>Sistem yang menyesuaikan tingkat kesulitan soal dengan kemampuan peserta secara <i>real-time</i>.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <h3>Kognitif</h3>
                        <p>Memberikan hasil analisis mengenai profil kemampuan kognitif siswa.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-lightning-fill"></i>
                        </div>
                        <h3>Inovatif</h3>
                        <p>Menggunakan teknologi terkini untuk memberikan pengalaman pembelajaran yang efektif.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .about-container {
        background: linear-gradient(276deg, #17376E -2.09%, #481F64 75.22%);
        color: white;
        min-height: 100vh;
    }

    .about-title {
        font-weight: bold;
        font-size: 2.5rem;
        margin-bottom: 2rem;
    }

    .about-section {
        margin-bottom: 4rem;
    }

    .intro-card {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 15px;
        padding: 2rem;
        text-align: center;
        backdrop-filter: blur(10px);
        margin-bottom: 2rem;
    }

    .card-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
    }

    .section-title {
        text-align: center;
        margin-bottom: 2rem;
        font-weight: bold;
    }

    .feature-card {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 15px;
        padding: 1.5rem;
        text-align: center;
        height: 100%;
        transition: transform 0.3s ease;
        backdrop-filter: blur(10px);
    }

    .feature-card:hover {
        transform: translateY(-5px);
    }

    .feature-icon {
        font-size: 2.5rem;
        margin-bottom: 1rem;
    }

    .feature-card h3 {
        font-size: 1.5rem;
        margin-bottom: 1rem;
        font-weight: 600;
    }

    .feature-card p {
        margin-bottom: 0;
        line-height: 1.6;
    }

    @media (max-width: 768px) {
        .about-title {
            font-size: 2rem;
        }

        .feature-card {
            margin-bottom: 1rem;
        }

        .intro-card {
            padding: 1.5rem;
        }
    }
</style>
<?= $this->endSection() ?>