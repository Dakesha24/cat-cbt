<?= $this->extend('templates/header') ?>

<?= $this->section('content') ?>
<div class="faq-container">
  <div class="hero-section text-center py-5">
    <div class="container">
      <h1 class="hero-title mb-4"><i>Frequently Asked Questions</i></h1>
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <p class="hero-subtitle">Temukan jawaban untuk pertanyaan yang sering diajukan tentang PHY-FA-CAT</p>
        </div>
      </div>
    </div>
  </div>

  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="faq-card">
          <div class="accordion" id="faqAccordion">
            <div class="accordion-item">
              <h2 class="accordion-header" id="heading1">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
                  <i class="bi bi-question-circle me-2"></i>
                  Apa itu&nbsp;<i>Computerized Adaptive Testing (CAT)</i>?
                </button>
              </h2>
              <div id="collapse1" class="accordion-collapse collapse show" aria-labelledby="heading1" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                  <i>CAT</i> adalah sistem ujian modern yang secara cerdas menyesuaikan tingkat kesulitan soal berdasarkan jawaban peserta tes secara <i>real-time</i>. Sistem ini membantu memberikan pengukuran kemampuan yang lebih akurat dan efisien.
                </div>
              </div>
            </div>

            <div class="accordion-item">
              <h2 class="accordion-header" id="heading2">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
                  <i class="bi bi-question-circle me-2"></i>
                  Bagaimana cara kerja asesmen adaptif?
                </button>
              </h2>
              <div id="collapse2" class="accordion-collapse collapse" aria-labelledby="heading2" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                  Jika Anda menjawab benar, sistem akan memberikan soal yang lebih sulit. Jika salah, sistem akan memberikan soal yang lebih mudah untuk menyesuaikan dengan kemampuan Anda.
                </div>
              </div>
            </div>

            <div class="accordion-item">
              <h2 class="accordion-header" id="heading3">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3" aria-expanded="false" aria-controls="collapse3">
                  <i class="bi bi-question-circle me-2"></i>
                  Apakah platform ini cocok untuk semua tingkatan?
                </button>
              </h2>
              <div id="collapse3" class="accordion-collapse collapse" aria-labelledby="heading3" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                  Ya, platform ini dirancang untuk siswa dari semua tingkatan, mulai dari pemula hingga lanjutan.
                </div>
              </div>
            </div>

            <div class="accordion-item">
              <h2 class="accordion-header" id="heading4">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4" aria-expanded="false" aria-controls="collapse4">
                  <i class="bi bi-question-circle me-2"></i>
                  Apakah saya perlu mendaftar untuk menggunakan platform ini?
                </button>
              </h2>
              <div id="collapse4" class="accordion-collapse collapse" aria-labelledby="heading4" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                  Ya, Anda perlu membuat akun untuk mengakses fitur asesmen dan melihat laporan hasil.
                </div>
              </div>
            </div>

            <div class="accordion-item">
              <h2 class="accordion-header" id="heading5">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse5" aria-expanded="false" aria-controls="collapse5">
                  <i class="bi bi-question-circle me-2"></i>
                  Bisakah guru membuat soal sendiri?
                </button>
              </h2>
              <div id="collapse5" class="accordion-collapse collapse" aria-labelledby="heading5" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                  Guru dapat mengunggah atau menambahkan soal baru ke dalam bank soal, yang nantinya akan disesuaikan secara otomatis oleh sistem <i>CAT</i>.
                </div>
              </div>
            </div>

            <div class="accordion-item">
              <h2 class="accordion-header" id="heading6">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse6" aria-expanded="false" aria-controls="collapse6">
                  <i class="bi bi-question-circle me-2"></i>
                  Bagaimana cara saya melihat hasil asesmen?
                </button>
              </h2>
              <div id="collapse6" class="accordion-collapse collapse" aria-labelledby="heading6" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                  Setelah selesai mengerjakan ujian, Anda dapat langsung melihat hasil berupa skor, analisis kemampuan, dan rekomendasi pembelajaran.
                </div>
              </div>
            </div>

            <div class="accordion-item">
              <h2 class="accordion-header" id="heading7">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse7" aria-expanded="false" aria-controls="collapse7">
                  <i class="bi bi-question-circle me-2"></i>
                  Apakah asesmen ini berbatas waktu?
                </button>
              </h2>
              <div id="collapse7" class="accordion-collapse collapse" aria-labelledby="heading7" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                  Ya, durasi ujian diatur sesuai jenis asesmen yang dipilih. Informasi batas waktu akan ditampilkan sebelum ujian dimulai.
                </div>
              </div>
            </div>

            <div class="accordion-item">
              <h2 class="accordion-header" id="heading8">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse8" aria-expanded="false" aria-controls="collapse8">
                  <i class="bi bi-question-circle me-2"></i>
                  Apakah laporan hasil dapat diunduh?
                </button>
              </h2>
              <div id="collapse8" class="accordion-collapse collapse" aria-labelledby="heading8" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                  Ya, laporan hasil ujian dapat diunduh dalam format <i>PDF</i> melalui menu Riwayat Ujian.
                </div>
              </div>
            </div>

            <div class="accordion-item">
              <h2 class="accordion-header" id="heading9">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse9" aria-expanded="false" aria-controls="collapse9">
                  <i class="bi bi-question-circle me-2"></i>
                  Apa yang harus saya lakukan jika mengalami masalah teknis?
                </button>
              </h2>
              <div id="collapse9" class="accordion-collapse collapse" aria-labelledby="heading9" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                  Segera hubungi pusat bantuan kami melalui menu Bantuan atau kirim <i>email</i> ke tim dukungan teknis kami.
                </div>
              </div>
            </div>

            <div class="accordion-item">
              <h2 class="accordion-header" id="heading10">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse10" aria-expanded="false" aria-controls="collapse10">
                  <i class="bi bi-question-circle me-2"></i>
                  Apakah platform ini mendukung ujian selain fisika?
                </button>
              </h2>
              <div id="collapse10" class="accordion-collapse collapse" aria-labelledby="heading10" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                  Saat ini, platform kami berfokus pada materi fisika. Namun, kami berencana untuk menambahkan mata pelajaran lain di masa mendatang.
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  .faq-container {
    background: linear-gradient(276deg, #17376E -2.09%, #481F64 75.22%);
    min-height: 100vh;
    padding-bottom: 4rem;
  }

  .hero-section {
    color: white;
    padding: 4rem 0;
  }

  .hero-title {
    font-size: 2.5rem;
    font-weight: bold;
    margin-bottom: 1.5rem;
  }

  .hero-subtitle {
    font-size: 1.2rem;
    opacity: 0.9;
  }

  .faq-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 2rem;
  }

  .accordion-item {
    background: transparent;
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px !important;
    margin-bottom: 1rem;
    overflow: hidden;
  }

  .accordion-button {
    background: rgba(255, 255, 255, 0.05);
    color: white;
    font-weight: 500;
    padding: 1.25rem;
    transition: all 0.3s ease;
  }

  .accordion-button:not(.collapsed) {
    background: rgba(255, 255, 255, 0.1);
    color: white;
  }

  .accordion-button:hover {
    background: rgba(255, 255, 255, 0.15);
  }

  .accordion-button::after {
    filter: brightness(0) invert(1);
  }

  .accordion-button:focus {
    box-shadow: none;
    border-color: rgba(255, 255, 255, 0.2);
  }

  .accordion-body {
    background: rgba(255, 255, 255, 0.05);
    color: white;
    padding: 1.5rem;
    line-height: 1.6;
  }

  .bi {
    font-size: 1.2rem;
  }

  @media (max-width: 768px) {
    .hero-title {
      font-size: 2rem;
    }

    .faq-card {
      padding: 1rem;
    }

    .accordion-button {
      padding: 1rem;
    }
  }
</style>
<?= $this->endSection() ?>