<?= $this->extend('templates/header') ?>
<?= $this->section('content') ?>

<!-- AOS -->
<link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">

<!-- HERO -->
<section class="hp-hero">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6" data-aos="fade-right" data-aos-duration="700">
                <span class="hp-label">Platform Asesmen Digital</span>
                <h1 class="hp-title">Ujian Berbasis Komputer</h1>
                <p class="hp-lead">Platform ini menghadirkan sistem ujian Computer Based Testing (CBT) berbasis Item Response Theory untuk mengukur kemampuan siswa secara presisi.</p>
                <div class="hp-cta">
                    <a href="<?= base_url('login') ?>" class="hp-btn-primary">Masuk ke Sistem</a>
                    <!-- <a href="<?= base_url('register') ?>" class="hp-btn-outline">Daftar Akun</a> -->
                </div>
                <div class="hp-trust">
                    <div class="hp-trust-item"><i class="bi bi-patch-check-fill"></i> Berbasis IRT 3PL</div>
                    <div class="hp-trust-item"><i class="bi bi-shield-check"></i> Estimasi EAP</div>
                    <div class="hp-trust-item"><i class="bi bi-bar-chart-fill"></i> Analitik Hasil</div>
                </div>
            </div>
            <!-- <div class="col-lg-6 d-flex justify-content-center" data-aos="fade-left" data-aos-duration="700" data-aos-delay="100">
                <div class="hp-visual">
                    <div class="hp-visual-card hp-vc-top" data-aos="fade-up" data-aos-delay="200">
                        <div class="hp-vc-icon"><i class="bi bi-cpu"></i></div>
                        <div>
                            <div class="hp-vc-title">Computer Adaptive Testing</div>
                            <div class="hp-vc-sub">Soal menyesuaikan kemampuan siswa secara real-time</div>
                        </div>
                    </div>
                    <div class="hp-visual-card hp-vc-mid" data-aos="fade-up" data-aos-delay="350">
                        <div class="hp-vc-icon"><i class="bi bi-graph-up-arrow"></i></div>
                        <div>
                            <div class="hp-vc-title">Estimasi θ (Theta)</div>
                            <div class="hp-vc-sub">Kemampuan diukur via model IRT dengan akurasi tinggi</div>
                        </div>
                    </div>
                    <div class="hp-visual-card hp-vc-bot" data-aos="fade-up" data-aos-delay="500">
                        <div class="hp-vc-icon"><i class="bi bi-clipboard2-data"></i></div>
                        <div>
                            <div class="hp-vc-title">Rekap & Analitik</div>
                            <div class="hp-vc-sub">Laporan hasil ujian lengkap untuk guru dan admin</div>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->
    </div>
</section>

<!-- FITUR -->
<section class="hp-features">
    <div class="container">
        <div class="hp-section-head" data-aos="fade-up">
            <h2>Fitur Unggulan Platform</h2>
            <p>Dirancang untuk mendukung asesmen formatif yang efektif, efisien, dan berbasis data.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="0">
                <div class="hp-feat-card">
                    <div class="hp-feat-icon"><i class="bi bi-pc-display-horizontal"></i></div>
                    <h5>Item Response Theory (IRT)</h5>
                    <p>Metode pengukuran modern yang menganalisis hubungan antara kemampuan peserta dan karakteristik setiap item untuk menghasilkan penilaian yang lebih akurat dan objektif.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="150">
                <div class="hp-feat-card">
                    <div class="hp-feat-icon"><i class="bi bi-pc-display-horizontal"></i></div>
                    <h5>Item Response Time (IRTime)</h5>
                    <p>Analisis waktu respons pada setiap item untuk memahami kecepatan, pola pengerjaan, dan perilaku peserta selama proses asesmen.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="hp-feat-card">
                    <div class="hp-feat-icon"><i class="bi bi-person-workspace"></i></div>
                    <h5>Multi-Peran</h5>
                    <p>Tiga level akses — Admin, Guru, dan Siswa — dengan dasbor dan fitur yang disesuaikan untuk masing-masing peran.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA BAWAH -->
<section class="hp-bottom-cta">
    <div class="container">
        <div class="hp-cta-box" data-aos="fade-up" data-aos-duration="600">
            <div>
                <h3>Siap memulai ujian?</h3>
                <p>Masuk dengan akun yang sudah terdaftar atau hubungi administrator untuk mendapatkan akses.</p>
            </div>
            <a href="<?= base_url('login') ?>" class="hp-btn-primary">Masuk Sekarang</a>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer class="footer">
    <div class="container">
        <div class="row gy-4">

            <!-- Tentang -->
            <div class="col-lg-5">
                <!-- <h5 class="footer-title">CBT-IRT Platform</h5> -->

                <p class="footer-description">
                    Platform ini dikembangkan sebagai hasil kerjasama dari Learning Analytics and Digital Assessment Reserch (LADAR) bekerjasama dengan Pusat Unggulan Universitas (PUU) STEM-EC.
                </p>

                <!-- <div class="social-links">
                    <a href="https://ladar.id/" class="social-link" target="_blank">
                        <i class="bi bi-globe"></i>
                    </a>

                    <a href="https://www.instagram.com/rzakwandi/" class="social-link" target="_blank">
                        <i class="bi bi-instagram"></i>
                    </a>

                    <a href="https://www.linkedin.com/in/rizki-zakwandi-197302124/" class="social-link" target="_blank">
                        <i class="bi bi-linkedin"></i>
                    </a>
                </div> -->

                <div class="footer-logos">
               <a href="https://ladar.id/" target="_blank">
                    <img src="<?= base_url('assets/images/logo-full.jpg') ?>"
                        alt="LADAR"
                        style="width: 200px; height: auto; object-fit: contain;">
                </a>
                <a href="https://www.instagram.com/stemcenterupi/" target="_blank">
                    <img src="<?= base_url('assets/images/stem-upi.png') ?>"
                        alt="LADAR"
                        style="width: auto; height: 70px; object-fit: contain;">
                </a>
            </div>

            </div>

            <!-- Mitra -->
            <div class="col-lg-3 col-md-6">
                <h5 class="footer-subtitle">Mitra Pengembang</h5>

                <ul class="footer-links">
                    <li>
                        <a href="https://ladar.id/" target="_blank">
                            LADAR Research Group
                        </a>
                    </li>

                    <li>
                        <a href="https://www.instagram.com/stemcenterupi/" target="_blank">
                            STEM Education Center (STEM-EC)
                        </a>
                    </li>

                    <li>
                        <a href="https://fisika.upi.edu/akademik/pendidikan-fisika/" target="_blank">
                            Pendidikan Fisika UPI
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Kontak -->
            <div class="col-lg-4 col-md-6">
                <h5 class="footer-subtitle">Kontak</h5>

                <ul class="footer-contact">
                    <li>
                        <i class="bi bi-geo-alt-fill"></i>
                        Universitas Pendidikan Indonesia<br>
                        Jl. Dr. Setiabudi No.229, Isola,
                        Kec. Sukasari, Kota Bandung,
                        Jawa Barat 40154
                    </li>

                    <li>
                        <a href="mailto:r.zakwandi@upi.edu">
                            <i class="bi bi-envelope-fill"></i>
                            r.zakwandi@upi.edu
                        </a>
                    </li>

                    <li>
                        <a href="https://fisika.upi.edu/akademik/pendidikan-fisika/" target="_blank">
                            <i class="bi bi-mortarboard-fill"></i>
                            Pendidikan Fisika UPI
                        </a>
                    </li>
                </ul>
            </div>

        </div>

        <hr class="footer-divider">

        <div class="row footer-bottom align-items-center">
            <div class="col-md-6">
                <div class="footer-copyright">
                    © <?= date('Y') ?> CBT-IRT Platform.
                    All Rights Reserved.
                </div>
            </div>

            <div class="col-md-6 text-md-end">
                <a href="#" class="footer-bottom-link">
                    Kebijakan Privasi
                </a>

                <a href="#" class="footer-bottom-link">
                    Syarat & Ketentuan
                </a>
            </div>
        </div>
    </div>
</footer>

<style>
/* =========================
   FOOTER
========================= */

.footer{
    background: linear-gradient(135deg,#0f172a,#1e293b);
    color:#fff;
    padding:70px 0 25px;
}

.footer-title{
    font-size:1.3rem;
    font-weight:800;
    margin-bottom:15px;
}

.footer-description{
    color:rgba(255,255,255,.75);
    line-height:1.8;
    font-size:.95rem;
    max-width:450px;
}

.footer-subtitle{
    font-size:1rem;
    font-weight:700;
    margin-bottom:18px;
    position:relative;
    padding-bottom:10px;
}

.footer-subtitle::after{
    content:"";
    position:absolute;
    left:0;
    bottom:0;
    width:45px;
    height:3px;
    background:#3b82f6;
    border-radius:20px;
}

.footer-links,
.footer-contact{
    list-style:none;
    padding:0;
    margin:0;
}

.footer-links li,
.footer-contact li{
    margin-bottom:14px;
    color:rgba(255,255,255,.75);
    line-height:1.7;
}

.footer-links a,
.footer-contact a{
    color:rgba(255,255,255,.75);
    text-decoration:none;
    transition:.3s;
}

.footer-links a:hover,
.footer-contact a:hover{
    color:#60a5fa;
    padding-left:5px;
}

.footer-contact i{
    margin-right:8px;
    color:#60a5fa;
}

.social-links{
    display:flex;
    gap:12px;
    margin-top:20px;
}

.social-link{
    width:42px;
    height:42px;
    border-radius:50%;
    background:rgba(255,255,255,.08);
    display:flex;
    align-items:center;
    justify-content:center;
    color:#fff;
    text-decoration:none;
    transition:.3s;
}

.social-link:hover{
    background:#2563eb;
    color:#fff;
    transform:translateY(-3px);
}

.footer-divider{
    margin:35px 0 20px;
    border-color:rgba(255,255,255,.1);
}

.footer-copyright{
    color:rgba(255,255,255,.6);
    font-size:.9rem;
}

.footer-bottom-link{
    color:rgba(255,255,255,.6);
    text-decoration:none;
    margin-left:20px;
    font-size:.9rem;
    transition:.3s;
}

.footer-bottom-link:hover{
    color:#60a5fa;
}

@media (max-width:768px){

    .footer{
        text-align:center;
    }

    .footer-subtitle::after{
        left:50%;
        transform:translateX(-50%);
    }

    .social-links{
        justify-content:center;
    }

    .footer-bottom{
        text-align:center;
    }

    .footer-bottom-link{
        display:block;
        margin:10px 0;
    }

    .footer-copyright{
        margin-bottom:15px;
    }
}
</style>

<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>AOS.init({ once: true, offset: 60 });</script>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
/* ── Reset navbar override dari halaman lain ── */
.navbar { background: linear-gradient(90deg,#17376E,#481F64) !important; backdrop-filter: none !important; border-bottom: none !important; }

.hp-hero, .hp-features, .hp-bottom-cta, .hp-hero *, .hp-features *, .hp-bottom-cta * {
    font-family: 'Inter', sans-serif;
}

/* ── Hero ── */
.hp-hero { background: #fff; padding: 80px 0 60px; border-bottom: 1px solid #e9eef6; }

.hp-label {
    display: inline-block;
    font-size: .75rem;
    font-weight: 700;
    letter-spacing: .1em;
    text-transform: uppercase;
    color: #2563eb;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    padding: .3rem .85rem;
    border-radius: 4px;
    margin-bottom: 1.25rem;
}

.hp-title {
    font-size: clamp(1.75rem, 3.5vw, 2.5rem);
    font-weight: 800;
    color: #0f172a;
    line-height: 1.2;
    letter-spacing: -.02em;
    margin-bottom: 1.25rem;
}

.hp-lead { font-size: 1.05rem; color: #475569; line-height: 1.75; margin-bottom: 2rem; max-width: 520px; }

.hp-cta { display: flex; gap: .75rem; flex-wrap: wrap; margin-bottom: 2rem; }

.hp-btn-primary {
    padding: .75rem 1.75rem;
    background: #1d4ed8;
    color: #fff;
    font-weight: 700;
    font-size: .95rem;
    border-radius: 6px;
    text-decoration: none;
    transition: background .2s, transform .15s;
    display: inline-block;
}
.hp-btn-primary:hover { background: #1e40af; color: #fff; transform: translateY(-1px); }

.hp-btn-outline {
    padding: .75rem 1.75rem;
    background: #fff;
    color: #1d4ed8;
    font-weight: 700;
    font-size: .95rem;
    border-radius: 6px;
    border: 1.5px solid #bfdbfe;
    text-decoration: none;
    transition: border-color .2s, background .2s;
    display: inline-block;
}
.hp-btn-outline:hover { border-color: #93c5fd; background: #eff6ff; color: #1d4ed8; }

.hp-trust { display: flex; gap: 1.25rem; flex-wrap: wrap; }
.hp-trust-item { font-size: .82rem; font-weight: 600; color: #475569; display: flex; align-items: center; gap: .4rem; }
.hp-trust-item i { color: #2563eb; }

/* Visual cards */
.hp-visual { display: flex; flex-direction: column; gap: 1rem; width: 100%; max-width: 380px; }

.hp-visual-card {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 1.1rem 1.25rem;
    box-shadow: 0 2px 12px rgba(15,23,42,.07);
    transition: box-shadow .2s;
}
.hp-visual-card:hover { box-shadow: 0 6px 24px rgba(15,23,42,.1); }
.hp-vc-mid { margin-left: 1.5rem; }

.hp-vc-icon {
    width: 40px; height: 40px;
    background: #eff6ff;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem;
    color: #2563eb;
    flex-shrink: 0;
}

.hp-vc-title { font-size: .88rem; font-weight: 700; color: #0f172a; margin-bottom: .2rem; }
.hp-vc-sub   { font-size: .78rem; color: #64748b; line-height: 1.45; }

/* ── Features ── */
.hp-features { background: #f8fafc; padding: 72px 0; border-bottom: 1px solid #e9eef6; }

.hp-section-head { text-align: center; margin-bottom: 3rem; }
.hp-section-head h2 { font-size: 1.85rem; font-weight: 800; color: #0f172a; margin-bottom: .5rem; }
.hp-section-head p  { color: #64748b; font-size: 1rem; }

.hp-feat-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 1.75rem;
    height: 100%;
    transition: box-shadow .2s, transform .2s;
}
.hp-feat-card:hover { box-shadow: 0 8px 28px rgba(15,23,42,.09); transform: translateY(-3px); }

.hp-feat-icon {
    width: 48px; height: 48px;
    background: #eff6ff;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.3rem;
    color: #2563eb;
    margin-bottom: 1rem;
}

.hp-feat-card h5 { font-size: 1rem; font-weight: 700; color: #0f172a; margin-bottom: .6rem; }
.hp-feat-card p  { font-size: .88rem; color: #64748b; line-height: 1.65; margin: 0; }

/* ── Bottom CTA ── */
.hp-bottom-cta { background: #fff; padding: 56px 0; }

.hp-cta-box {
    background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 100%);
    border-radius: 14px;
    padding: 2.5rem 2.75rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 2rem;
    flex-wrap: wrap;
}
.hp-cta-box h3 { color: #fff; font-weight: 800; font-size: 1.45rem; margin-bottom: .35rem; }
.hp-cta-box p  { color: rgba(255,255,255,.75); font-size: .92rem; margin: 0; }
.hp-cta-box .hp-btn-primary { background: #fff; color: #1d4ed8; white-space: nowrap; flex-shrink: 0; }
.hp-cta-box .hp-btn-primary:hover { background: #eff6ff; color: #1e40af; }

@media (max-width: 767px) {
    .hp-hero { padding: 50px 0 40px; }
    .hp-visual { max-width: 100%; }
    .hp-vc-mid { margin-left: 0; }
    .hp-cta-box { flex-direction: column; text-align: center; padding: 2rem 1.5rem; }
}
</style>

<?= $this->endSection() ?>
