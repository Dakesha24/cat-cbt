<?= $this->extend('templates/header') ?>

<?= $this->section('content') ?>
<div class="guide-container">
    <div class="container py-5">
        <h1 class="guide-title text-center mb-5">Panduan PHY-FA-CAT</h1>

        <div class="guide-section">
            <div class="guide-card">
                <div class="card-icon">
                    <i class="bi bi-info-circle-fill"></i>
                </div>
                <h2>Apa itu Phy-FA-CAT?</h2>
                <p>Phy-FA-CAT merupakan media asesmen adaptif yang dapat digunakan siswa untuk mengerjakan soal berdasarkan kemampuan <i>real-time</i> dan memberikan pengukuran kemampuan kognitif yang lebih akurat.</p>
            </div>
        </div>

        <div class="guide-section">
            <h2 class="section-title">Langkah Penggunaan</h2>
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="step-card">
                        <div class="step-number">1</div>
                        <h3><i>Login</i></h3>
                        <p>Masuk menggunakan akun yang telah diberikan</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="step-card">
                        <div class="step-number">2</div>
                        <h3><i>Token</i></h3>
                        <p>Masukkan <i>token</i> ujian dari pengawas</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="step-card">
                        <div class="step-number">3</div>
                        <h3>Ujian</h3>
                        <p>Kerjakan soal sesuai waktu yang ditentukan</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="step-card">
                        <div class="step-number">4</div>
                        <h3>Selesai</h3>
                        <p>Hasil akan muncul setelah ujian berakhir</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="guide-section">
            <div class="rules-card">
                <h2 class="mb-4">Peraturan Penting</h2>
                <div class="row">
                    <div class="col-md-6">
                        <div class="rule-item not-allowed">
                            <h4><i class="bi bi-x-circle"></i> Tidak Diperbolehkan</h4>
                            <ul>
                                <li>Membuka <i>tab browser</i> lain</li>
                                <li>Menggunakan perangkat elektronik lain</li>
                                <li>Meninggalkan halaman ujian</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="rule-item allowed">
                            <h4><i class="bi bi-check-circle"></i> Diperbolehkan</h4>
                            <ul>
                                <li>Menggunakan kalkulator <i>scientific</i></li>
                                <li>Menggunakan kertas coret-coretan</li>
                                <li>Bertanya pada pengawas jika ada kendala</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .guide-container {
        background: linear-gradient(276deg, #17376E -2.09%, #481F64 75.22%);
        color: white;
        min-height: 100vh;
    }

    .guide-title {
        font-weight: bold;
        font-size: 2.5rem;
        margin-bottom: 2rem;
    }

    .guide-section {
        margin-bottom: 4rem;
    }

    .guide-card {
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

    .step-card {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 15px;
        padding: 1.5rem;
        text-align: center;
        height: 100%;
        transition: transform 0.3s ease;
    }

    .step-card:hover {
        transform: translateY(-5px);
    }

    .step-number {
        background: #fff;
        color: #481F64;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        font-weight: bold;
        font-size: 1.2rem;
    }

    .rules-card {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 15px;
        padding: 2rem;
        backdrop-filter: blur(10px);
    }

    .rule-item {
        margin-bottom: 1.5rem;
    }

    .rule-item h4 {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    .rule-item.not-allowed i {
        color: #ff4d4d;
    }

    .rule-item.allowed i {
        color: #4dff4d;
    }

    .rule-item ul {
        list-style: none;
        padding-left: 2rem;
    }

    .rule-item ul li {
        margin-bottom: 0.5rem;
        position: relative;
    }

    .rule-item ul li::before {
        content: "•";
        position: absolute;
        left: -1rem;
    }

    @media (max-width: 768px) {
        .guide-title {
            font-size: 2rem;
        }

        .step-card {
            margin-bottom: 1rem;
        }
    }
</style>
<?= $this->endSection() ?>