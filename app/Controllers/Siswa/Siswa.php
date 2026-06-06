<?php

namespace App\Controllers\Siswa;

use CodeIgniter\Controller;
use App\Models\JadwalUjianModel;
use App\Models\PesertaUjianModel;
use App\Models\SiswaModel;
use App\Models\KelasModel;
use App\Models\SoalUjianModel;
use App\Models\HasilUjianModel;
use App\Models\SekolahModel;
use App\Models\PaketUjianModel;
use App\Models\AttemptUjianModel;
use App\Models\AttemptJawabanModel;
use App\Models\AttemptJawabanCatModel;
use App\Models\AttemptJawabanCbtModel;
use App\Models\AttemptAnalisisCbtModel;
use App\Models\AttemptSoalModel;
use App\Models\UjianSoalCatModel;
use App\Models\UjianCatParamModel;
use App\Libraries\CatEngine;
use App\Libraries\CbtEngine;


class Siswa extends Controller
{
  protected $jadwalUjianModel;
  protected $pesertaUjianModel;
  protected $siswaModel;
  protected $kelasModel;
  protected $soalUjianModel;
  protected $hasilUjianModel;
  protected $sekolahModel;
  protected $paketUjianModel;
  protected $attemptUjianModel;
  protected $attemptJawabanModel;
  protected $attemptJawabanCatModel;
  protected $attemptJawabanCbtModel;
  protected $attemptAnalisisCbtModel;
  protected $attemptSoalModel;
  protected $ujianSoalCatModel;
  protected $ujianCatParamModel;

  // Inisialisasi semua model yang dibutuhkan controller ini
  public function __construct()
  {
    $this->jadwalUjianModel = new JadwalUjianModel();
    $this->pesertaUjianModel = new PesertaUjianModel();
    $this->siswaModel = new SiswaModel();
    $this->kelasModel = new KelasModel();
    $this->soalUjianModel = new SoalUjianModel();
    $this->hasilUjianModel = new HasilUjianModel();
    $this->sekolahModel = new SekolahModel();
    $this->paketUjianModel = new PaketUjianModel();
    $this->attemptUjianModel = new AttemptUjianModel();
    $this->attemptJawabanModel = new AttemptJawabanModel();
    $this->attemptJawabanCatModel = new AttemptJawabanCatModel();
    $this->attemptJawabanCbtModel = new AttemptJawabanCbtModel();
    $this->attemptAnalisisCbtModel = new AttemptAnalisisCbtModel();
    $this->attemptSoalModel = new AttemptSoalModel();
    $this->ujianSoalCatModel = new UjianSoalCatModel();
    $this->ujianCatParamModel = new UjianCatParamModel();
  }

  //dashboard

  // Halaman utama siswa setelah login
  public function dashboard()
  {
    $userId = session()->get('user_id');

    if (!$this->isProfilLengkap((int) $userId)) {
      session()->setFlashdata('profil_incomplete',
        'Lengkapi profil Anda terlebih dahulu sebelum mengakses fitur ujian.'
      );
      return redirect()->to(base_url('siswa/profil'));
    }

    return view('siswa/dashboard');
  }

  // Cek apakah profil siswa sudah lengkap (data dasar + field wajib form builder)
  private function isProfilLengkap(int $userId): bool
  {
    $siswa = $this->siswaModel->where('user_id', $userId)->first();

    // Data dasar belum diisi
    if (!$siswa || empty($siswa['nama_lengkap']) || empty($siswa['nomor_peserta']) || empty($siswa['kelas_id'])) {
      return false;
    }

    // Cek field wajib dari form builder
    $template   = (new \App\Models\FormTemplateModel())->getSingle();
    $fieldModel = new \App\Models\FormFieldModel();
    $required   = array_filter(
      $fieldModel->getByTemplate((int) $template['template_id']),
      fn($f) => (int) $f['is_required'] === 1
    );

    if (empty($required)) {
      return true;
    }

    $responseModel = new \App\Models\FormResponseModel();
    $valueModel    = new \App\Models\FormResponseValueModel();
    $response      = $responseModel->getBySiswaAndTemplate(
      (int) $siswa['siswa_id'],
      (int) $template['template_id']
    );

    if (!$response) {
      return false;
    }

    $values = $valueModel->getByResponse((int) $response['response_id']);

    foreach ($required as $field) {
      $val = $values[$field['field_id']] ?? null;
      if ($val === null || trim((string) $val) === '') {
        return false;
      }
    }

    return true;
  }

  //pengumuman

  // Tampilkan daftar pengumuman dari semua pengguna
  public function pengumuman()
  {
    $pengumumanModel = new \App\Models\PengumumanModel();
    $data['pengumuman'] = $pengumumanModel->getPengumumanWithUser();
    return view('siswa/pengumuman', $data);
  }

  // Tampilkan halaman profil siswa; kelas di-load via AJAX bukan sekaligus
  public function profil()
  {
    $userId = session()->get('user_id');

    $siswa = $this->siswaModel
      ->select('siswa.*, kelas.sekolah_id')
      ->join('kelas', 'kelas.kelas_id = siswa.kelas_id', 'left')
      ->where('siswa.user_id', $userId)
      ->first();

    // Load formulir aktif beserta field dan nilai siswa yang sudah tersimpan
    $formTemplateModel = new \App\Models\FormTemplateModel();
    $formFieldModel    = new \App\Models\FormFieldModel();
    $formResponseModel = new \App\Models\FormResponseModel();
    $formValueModel    = new \App\Models\FormResponseValueModel();

    $template   = $formTemplateModel->getSingle();
    $formFields = $formFieldModel->getWithOptions((int) $template['template_id']);
    $formValues = [];

    if ($siswa) {
      $existing = $formResponseModel->getBySiswaAndTemplate(
        (int) $siswa['siswa_id'],
        (int) $template['template_id']
      );
      if ($existing) {
        $formValues = $formValueModel->getByResponse((int) $existing['response_id']);
      }
    }

    return view('siswa/profil', [
      'siswa'        => $siswa,
      'sekolah'      => $this->sekolahModel->findAll(),
      'isNewUser'    => !$this->siswaModel->checkSiswaExists($userId),
      'formTemplate' => $template,
      'formFields'   => $formFields,
      'formValues'   => $formValues,
    ]);
  }

  // Simpan atau update data profil siswa; bedakan insert vs update berdasarkan keberadaan data sebelumnya
  public function saveProfil()
  {
    $userId = session()->get('user_id');

    $rules = [
      'nomor_peserta' => 'required|min_length[5]',
      'nama_lengkap'  => 'required|min_length[3]',
      'jenis_kelamin' => 'required|in_list[Laki-laki,Perempuan]',
      'sekolah_id'    => 'required|numeric',
      'kelas_id'      => 'required|numeric',
    ];

    $formTemplateModel = new \App\Models\FormTemplateModel();
    $formFieldModel    = new \App\Models\FormFieldModel();
    $template          = $formTemplateModel->getSingle();
    $formFields = $formFieldModel->getByTemplate((int) $template['template_id']);
    foreach ($formFields as $field) {
      if ($field['is_required']) {
        $rules['form_field.' . $field['field_id']] = 'required';
      }
    }

    if (!$this->validate($rules)) {
      // Normalise error keys: form_field.123 → form_field_123
      $rawErrors   = $this->validator->getErrors();
      $cleanErrors = [];
      foreach ($rawErrors as $key => $msg) {
        $cleanKey = str_replace('.', '_', $key);
        $cleanErrors[$cleanKey] = $msg;
      }
      return redirect()->back()->withInput()->with('errors', $cleanErrors);
    }

    $profileData = [
      'user_id'       => $userId,
      'nomor_peserta' => $this->request->getPost('nomor_peserta'),
      'nama_lengkap'  => $this->request->getPost('nama_lengkap'),
      'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
      'kelas_id'      => $this->request->getPost('kelas_id'),
    ];

    $existingSiswa = $this->siswaModel->where('user_id', $userId)->first();

    try {
      if ($existingSiswa) {
        $this->siswaModel->update($existingSiswa['siswa_id'], $profileData);
        $siswaId = (int) $existingSiswa['siswa_id'];
      } else {
        $siswaId = (int) $this->siswaModel->insert($profileData, true);
      }

      // Simpan nilai field dinamis
      if ($siswaId) {
        $formResponseModel = new \App\Models\FormResponseModel();
        $formValueModel    = new \App\Models\FormResponseValueModel();
        $responseId        = $formResponseModel->getOrCreate($siswaId, (int) $template['template_id']);
        $formInput         = $this->request->getPost('form_field') ?? [];
        $formValueModel->saveAll($responseId, $formInput);
      }

      session()->setFlashdata('success', 'Profil berhasil disimpan!');
      return redirect()->to(base_url('siswa/profil'));
    } catch (\Exception $e) {
      log_message('error', $e->getMessage());
      return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data.');
    }
  }

  // API: ambil daftar kelas berdasarkan sekolah_id, dipanggil via AJAX dari form profil
  public function getKelasBySekolah($sekolahId)
  {
    try {
      $kelas = $this->kelasModel
        ->where('sekolah_id', $sekolahId)
        ->orderBy('nama_kelas', 'ASC')
        ->findAll();

      return $this->response->setJSON([
        'success' => true,
        'kelas' => $kelas
      ]);
    } catch (\Exception $e) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Error memuat data kelas'
      ]);
    }
  }


  // Tampilan daftar jadwal ujian yang tersedia; hitung sisa attempt untuk tiap jadwal
  public function ujian()
  {
    if (!session()->get('user_id')) {
      return redirect()->to(base_url('login'));
    }

    $userId = session()->get('user_id');
    $siswa = $this->getSiswaWithSchoolByUser((int) $userId);

    //kalo belum isi profil, arakan ke profil
    if (!$siswa) {
      session()->setFlashdata('error', 'Silahkan lengkapi profil Anda terlebih dahulu');
      return redirect()->to(base_url('siswa/profil'));
    }

    $jadwalUjian = $this->getAvailableJadwalForSiswa($siswa);

    foreach ($jadwalUjian as &$jadwal) {
      $jadwal['jumlah_attempt'] = !empty($jadwal['peserta_ujian_id'])
        ? $this->attemptUjianModel->where('peserta_ujian_id', $jadwal['peserta_ujian_id'])->countAllResults()
        : 0;
      $maksAttempt = (int)($jadwal['maksimal_attempt'] ?? 1);
      $jadwal['bisa_mengulang'] = ($jadwal['status_peserta'] === 'selesai'
        && (int)($jadwal['pengulangan_aktif'] ?? 0) === 1
        && $jadwal['jumlah_attempt'] < $maksAttempt);
    }

    $data = [
      'jadwalUjian' => $jadwalUjian,
      'siswa' => $siswa
    ];

    return view('siswa/ujian', $data);
  }


  // Validasi kode akses + daftarkan siswa sebagai peserta ujian + redirect ke halaman soal
  // Juga tangani pengulangan attempt jika fitur aktif dan batas belum habis
  public function mulaiUjian()
  {
    // 1. Debug untuk melihat session user_id
    if (!session()->get('user_id')) {
      session()->setFlashdata('error', 'Silahkan login terlebih dahulu');
      return redirect()->to(base_url('login'));
    }

    // 2. Ambil siswa_id dengan pengecekan
    $userId = session()->get('user_id');
    $siswa = $this->getSiswaWithSchoolByUser((int) $userId);

    if (!$siswa) {
      session()->setFlashdata('error', 'Data siswa tidak ditemukan. Silahkan lengkapi profil terlebih dahulu');
      return redirect()->to(base_url('siswa/profil'));
    }

    // 3. Ambil data dari form dengan validasi
    $jadwalId = $this->request->getPost('jadwal_id');
    $kodeAkses = $this->request->getPost('kode_akses');

    if (!$jadwalId || !$kodeAkses) {
      session()->setFlashdata('error', 'Data tidak lengkap');
      return redirect()->back();
    }

    // 4. Validasi kode akses
    $jadwal = $this->jadwalUjianModel->find($jadwalId);
    if (!$jadwal || $jadwal['kode_akses'] != $kodeAkses) {
      session()->setFlashdata('error', 'Kode akses ujian tidak valid!');
      return redirect()->back();
    }

    $ujianInfo = $this->jadwalUjianModel
      ->select('
        jadwal_ujian.*,
        ujian.id_ujian,
        ujian.jenis_ujian_id,
        ujian.nama_ujian,
        ujian.kode_ujian,
        ujian.deskripsi,
        ujian.tipe_ujian,
        ujian.tampilkan_pembahasan,
        ujian.visibilitas,
        ujian.pengulangan_aktif,
        ujian.maksimal_attempt,
        ujian.acak_urutan_soal,
        ujian.acak_pilihan_jawaban,
        ujian.durasi,
        ujian.created_by,
        ujian.sekolah_id as ujian_sekolah_id,
        ujian.kelas_id as ujian_kelas_id
      ')
      ->join('ujian', 'ujian.id_ujian = jadwal_ujian.ujian_id')
      ->where('jadwal_ujian.jadwal_id', $jadwalId)
      ->first();

    if (!$ujianInfo || !$this->siswaCanAccessJadwal($siswa, $ujianInfo)) {
      session()->setFlashdata('error', 'Anda tidak memiliki akses ke jadwal ujian ini.');
      return redirect()->to(base_url('siswa/ujian'));
    }

    $jadwalStatus = $ujianInfo['status'] ?? '';
    if ($jadwalStatus === 'selesai') {
      session()->setFlashdata('error', 'Jadwal ujian sudah selesai.');
      return redirect()->to(base_url('siswa/ujian'));
    }
    if ($jadwalStatus === 'belum_mulai') {
      session()->setFlashdata('error', 'Jadwal ujian belum dibuka. Silakan tunggu hingga ujian dimulai.');
      return redirect()->to(base_url('siswa/ujian'));
    }

    // 5. Cek apakah sudah terdaftar sebagai peserta
    $peserta = $this->pesertaUjianModel
      ->where('jadwal_id', $jadwalId)
      ->where('siswa_id', $siswa['siswa_id'])
      ->first();

    try {
      if (!$peserta) {
        // Siswa belum terdaftar sama sekali, insert dulu sebagai peserta baru
        $dataPeserta = [
          'jadwal_id' => $jadwalId,
          'siswa_id' => $siswa['siswa_id'],
          'status' => 'belum_mulai'
        ];

        // Debug data sebelum insert
        log_message('debug', 'Data peserta yang akan diinsert: ' . print_r($dataPeserta, true));

        $this->pesertaUjianModel->insert($dataPeserta);
      } elseif ($peserta['status'] === 'selesai') {
        // Siswa sudah pernah ujian — cek apakah boleh mengulang
        $jumlahAttempt = $this->attemptUjianModel->where('peserta_ujian_id', $peserta['peserta_ujian_id'])->countAllResults();
        $maksAttempt = (int)($ujianInfo['maksimal_attempt'] ?? 1);

        if ((int)($ujianInfo['pengulangan_aktif'] ?? 0) !== 1 || $jumlahAttempt >= $maksAttempt) {
          session()->setFlashdata('error', 'Batas attempt ujian sudah habis.');
          return redirect()->back();
        }

        $this->pesertaUjianModel->update($peserta['peserta_ujian_id'], [
          'status' => 'belum_mulai',
          'waktu_mulai' => null,
          'waktu_selesai' => null,
        ]);
      }

      // 7. Redirect ke halaman soal
      return redirect()->to(base_url("siswa/ujian/soal/$jadwalId"));
    } catch (\Exception $e) {
      // 8. Tangkap error jika terjadi masalah
      log_message('error', 'Error saat mendaftarkan peserta: ' . $e->getMessage());
      session()->setFlashdata('error', 'Terjadi kesalahan saat memulai ujian. Silahkan coba lagi.');
      return redirect()->back();
    }
  }

  // Entry point soal ujian: validasi akses lalu dispatch ke soalCbt atau soalCat sesuai tipe ujian
  public function soal($jadwalId)
  {
    // Simpan jadwal_id ke session untuk digunakan saat menyimpan jawaban
    session()->set('current_jadwal_id', $jadwalId);


    // 1. Validasi akses dan session
    if (!session()->get('user_id')) {
      return redirect()->to(base_url('login'));
    }

    // 2. Ambil data siswa
    $userId = session()->get('user_id');
    $siswa = $this->getSiswaWithSchoolByUser((int) $userId);

    if (!$siswa) {
      session()->setFlashdata('error', 'Data siswa tidak ditemukan');
      return redirect()->to(base_url('siswa/profil'));
    }

    // 3. Ambil informasi ujian dan jadwal di awal
    $ujianInfo = $this->jadwalUjianModel
      ->select('
        jadwal_ujian.*,
        ujian.id_ujian,
        ujian.jenis_ujian_id,
        ujian.nama_ujian,
        ujian.kode_ujian,
        ujian.deskripsi,
        ujian.tipe_ujian,
        ujian.tampilkan_pembahasan,
        ujian.visibilitas,
        ujian.pengulangan_aktif,
        ujian.maksimal_attempt,
        ujian.acak_urutan_soal,
        ujian.acak_pilihan_jawaban,
        ujian.durasi,
        ujian.created_by,
        ujian.sekolah_id as ujian_sekolah_id,
        ujian.kelas_id as ujian_kelas_id,
        jenis_ujian.nama_jenis
      ')
      ->join('ujian', 'ujian.id_ujian = jadwal_ujian.ujian_id')
      ->join('jenis_ujian', 'jenis_ujian.jenis_ujian_id = ujian.jenis_ujian_id')
      ->where('jadwal_ujian.jadwal_id', $jadwalId)
      ->first();

    if (!$ujianInfo) {
      session()->setFlashdata('error', 'Data ujian tidak ditemukan');
      return redirect()->to(base_url('siswa/ujian'));
    }

    // 4. Cek status peserta
    $peserta = $this->pesertaUjianModel
      ->where('jadwal_id', $jadwalId)
      ->where('siswa_id', $siswa['siswa_id'])
      ->first();

    if (!$peserta) {
      session()->setFlashdata('error', 'Anda belum terdaftar sebagai peserta ujian');
      return redirect()->to(base_url('siswa/ujian'));
    }

    if (!$this->siswaCanAccessJadwal($siswa, $ujianInfo)) {
      session()->setFlashdata('error', 'Anda tidak memiliki akses ke jadwal ujian ini.');
      return redirect()->to(base_url('siswa/ujian'));
    }

    if (($ujianInfo['tipe_ujian'] ?? 'CAT') === 'CBT') {
      return $this->soalCbt($jadwalId, $ujianInfo, $peserta);
    }

    return $this->soalCat($jadwalId, $ujianInfo, $peserta);
  }

  // Terima jawaban dari form, lalu dispatch ke simpanJawabanCbt atau simpanJawabanCat
  public function simpanJawaban()
  {
    // Debug untuk melihat input
    log_message('debug', 'POST Data: ' . print_r($this->request->getPost(), true));

    // 1. Validasi input
    $soalId = $this->request->getPost('soal_id');
    $attemptSoalId = $this->request->getPost('attempt_soal_id');
    $jawaban = $this->request->getPost('jawaban');

    if ((!$soalId && !$attemptSoalId) || !$jawaban) {
      session()->setFlashdata('error', 'Data jawaban tidak lengkap');
      return redirect()->back();
    }

    // Ambil jadwal_id dari URL (yang disimpan dalam session saat akses soal)
    $current_jadwal_id = session()->get('current_jadwal_id');
    if (!$current_jadwal_id) {
      session()->setFlashdata('error', 'Data jadwal ujian tidak ditemukan');
      return redirect()->to(base_url('siswa/ujian'));
    }

    // 2.1 Ambil info ujian dengan jadwal_id yang benar
    $ujianInfo = $this->jadwalUjianModel
      ->select('jadwal_ujian.*, ujian.*')
      ->join('ujian', 'ujian.id_ujian = jadwal_ujian.ujian_id')
      ->where('jadwal_ujian.jadwal_id', $current_jadwal_id)
      ->first();

    if (!$ujianInfo) {
      session()->setFlashdata('error', 'Data ujian tidak ditemukan');
      return redirect()->to(base_url('siswa/ujian'));
    }

    if (($ujianInfo['tipe_ujian'] ?? 'CAT') === 'CBT') {
      return $this->simpanJawabanCbt($ujianInfo, $attemptSoalId, $jawaban);
    }

    return $this->simpanJawabanCat($ujianInfo, $attemptSoalId, $jawaban);
  }

  // Tandai ujian selesai, update status peserta & attempt, bersihkan session CAT/CBT
  public function selesaiUjian($jadwalId)
  {
    if (!session()->get('user_id')) {
      return redirect()->to(base_url('login'));
    }

    // 1. Ambil data peserta
    $siswaId = $this->siswaModel->where('user_id', session()->get('user_id'))->first()['siswa_id'];
    $peserta = $this->pesertaUjianModel
      ->where('jadwal_id', $jadwalId)
      ->where('siswa_id', $siswaId)
      ->first();

    if (!$peserta) {
      session()->setFlashdata('error', 'Data peserta tidak ditemukan');
      return redirect()->to(base_url('siswa/ujian'));
    }

    // 2. Update status peserta menjadi selesai
    $this->pesertaUjianModel->update($peserta['peserta_ujian_id'], [
      'status' => 'selesai',
      'waktu_selesai' => date('Y-m-d H:i:s')
    ]);

    // 3. Ambil informasi ujian untuk ditampilkan
    $ujianInfo = $this->jadwalUjianModel
      ->select('jadwal_ujian.*, ujian.nama_ujian, ujian.deskripsi, ujian.tipe_ujian')
      ->join('ujian', 'ujian.id_ujian = jadwal_ujian.ujian_id')
      ->where('jadwal_ujian.jadwal_id', $jadwalId)
      ->first();

    // 4. Hitung nilai akhir dari attempt terbaru
    $attempt = $this->attemptUjianModel
      ->where('peserta_ujian_id', $peserta['peserta_ujian_id'])
      ->orderBy('nomor_attempt', 'DESC')
      ->first();

    $nilaiAkhir = 0;
    $thetaAkhir = null;
    $semAkhir = null;
    $totalSoal = 0;
    if ($attempt) {
      $isCat = ($ujianInfo['tipe_ujian'] ?? 'CAT') === 'CAT';

      if ($isCat) {
        $lastJawaban = $this->attemptJawabanCatModel
          ->where('attempt_id', $attempt['attempt_id'])
          ->orderBy('nomor_tampil', 'DESC')
          ->first();
        // CAT: pastikan attempt ditutup walau siswa keluar sebelum stopping rule terpenuhi
        if ($attempt['status'] !== 'selesai') {
          $attempt['nilai_akhir'] = $attempt['nilai_akhir'] ?? ($lastJawaban['theta_saat_ini'] ?? 0);
          $this->attemptUjianModel->update($attempt['attempt_id'], [
            'status' => 'selesai',
            'waktu_selesai' => date('Y-m-d H:i:s'),
            'nilai_akhir' => $attempt['nilai_akhir'],
          ]);
        }
        $nilaiAkhir = $attempt['nilai_akhir'] ?? ($lastJawaban['theta_saat_ini'] ?? 0);
        $thetaAkhir = $lastJawaban['theta_saat_ini'] ?? $attempt['nilai_akhir'] ?? null;
        $semAkhir = $lastJawaban['se_saat_ini'] ?? null;
        $totalSoal = $this->attemptJawabanCatModel->where('attempt_id', $attempt['attempt_id'])->countAllResults();
      } else {
        // CBT: jika waktu habis sebelum soal terakhir dijawab, jalankan EAP dari soal yang sudah terjawab
        if ($attempt['status'] !== 'selesai') {
          $responses = $this->ambilResponsesUntukEAP((int) $attempt['attempt_id']);
          if (!empty($responses)) {
            $eap = CbtEngine::estimasiEAP($responses);
            $now = date('Y-m-d H:i:s');
            $this->attemptUjianModel->update($attempt['attempt_id'], [
              'status'       => 'selesai',
              'waktu_selesai' => $now,
              'nilai_akhir'  => $eap['NA'],
              'theta_akhir'  => $eap['theta_final'],
              'sem_akhir'    => $eap['SEM'],
            ]);
            $attempt['nilai_akhir']  = $eap['NA'];
            $attempt['theta_akhir']  = $eap['theta_final'];
            $attempt['sem_akhir']    = $eap['SEM'];
          } else {
            $this->attemptUjianModel->update($attempt['attempt_id'], [
              'status'        => 'selesai',
              'waktu_selesai' => date('Y-m-d H:i:s'),
              'nilai_akhir'   => 50,
              'theta_akhir'   => 0,
              'sem_akhir'     => 0,
            ]);
            $attempt['nilai_akhir']  = 50;
            $attempt['theta_akhir']  = 0;
            $attempt['sem_akhir']    = 0;
          }
        }
        $nilaiAkhir = $attempt['nilai_akhir'] ?? 0;
        $thetaAkhir = $attempt['theta_akhir'] ?? null;
        $semAkhir   = $attempt['sem_akhir'] ?? null;
        $totalSoal  = $this->attemptJawabanCbtModel->where('attempt_id', $attempt['attempt_id'])->countAllResults();
      }

      session()->remove($this->getCatSessionKey((int) $attempt['attempt_id']));
      session()->remove($this->getCbtSessionKey((int) $attempt['attempt_id']));
    }

    $data = [
      'ujian' => $ujianInfo,
      'peserta' => $peserta,
      'nilai_akhir' => $nilaiAkhir,
      'theta_akhir' => $thetaAkhir,
      'sem_akhir' => $semAkhir,
      'total_soal' => $totalSoal
    ];

    return view('siswa/selesai_ujian', $data);
  }

  // Ambil data siswa beserta sekolah_id lewat JOIN ke tabel kelas
  private function getSiswaWithSchoolByUser(int $userId): ?array
  {
    return $this->siswaModel
      ->select('siswa.*, kelas.sekolah_id')
      ->join('kelas', 'kelas.kelas_id = siswa.kelas_id', 'left')
      ->where('siswa.user_id', $userId)
      ->first();
  }

  // Ambil jadwal yang masih aktif dan bisa diakses siswa ini (filter kelas/sekolah/individu)
  private function getAvailableJadwalForSiswa(array $siswa): array
  {
    $siswaId = (int) ($siswa['siswa_id'] ?? 0);
    $jadwalUjian = $this->jadwalUjianModel
      ->select('
        jadwal_ujian.*,
        ujian.nama_ujian,
        ujian.kode_ujian,
        ujian.deskripsi,
        ujian.durasi,
        ujian.tipe_ujian,
        ujian.pengulangan_aktif,
        ujian.maksimal_attempt,
        ujian.sekolah_id as ujian_sekolah_id,
        ujian.kelas_id as ujian_kelas_id,
        peserta_ujian.status as status_peserta,
        peserta_ujian.peserta_ujian_id
      ')
      ->join('ujian', 'ujian.id_ujian = jadwal_ujian.ujian_id')
      ->join('peserta_ujian', 'peserta_ujian.jadwal_id = jadwal_ujian.jadwal_id AND peserta_ujian.siswa_id = ' . $siswaId, 'left')
      ->where('jadwal_ujian.status !=', 'selesai')
      ->orderBy('jadwal_ujian.tanggal_mulai', 'ASC')
      ->findAll();

    return array_values(array_filter($jadwalUjian, function (array $jadwal) use ($siswa): bool {
      return $this->siswaCanAccessJadwal($siswa, $jadwal);
    }));
  }

  // Cek apakah siswa berhak mengikuti jadwal ini: cek kelas, sekolah, atau mode individu
  private function siswaCanAccessJadwal(array $siswa, array $jadwal): bool
  {
    $kelasId = (int) ($siswa['kelas_id'] ?? 0);
    $sekolahId = (int) ($siswa['sekolah_id'] ?? 0);
    $jadwalKelasId = (int) ($jadwal['kelas_id'] ?? 0);
    $ujianKelasId = (int) ($jadwal['ujian_kelas_id'] ?? 0);
    $ujianSekolahId = (int) ($jadwal['ujian_sekolah_id'] ?? 0);

    if ($jadwalKelasId > 0 && $jadwalKelasId !== $kelasId) {
      return false;
    }

    if ($jadwalKelasId <= 0) {
      if ($ujianKelasId > 0 && $ujianKelasId !== $kelasId) {
        return false;
      }

      if ($ujianKelasId <= 0 && $ujianSekolahId > 0 && $ujianSekolahId !== $sekolahId) {
        return false;
      }
    }

    // Mode individu: siswa hanya bisa akses jika namanya ada di daftar siswa_ids JSON
    if (($jadwal['tipe_penugasan'] ?? 'kelas') === 'individu') {
      $siswaIds = json_decode($jadwal['siswa_ids'] ?? '[]', true);
      if (!is_array($siswaIds)) {
        return false;
      }

      return in_array((int) ($siswa['siswa_id'] ?? 0), array_map('intval', $siswaIds), true);
    }

    return true;
  }

  // Render soal CBT: ambil/buat attempt, load snapshot soal dari session, hitung sisa waktu
  private function soalCbt($jadwalId, array $ujianInfo, array $peserta)
  {
    if ($peserta['status'] === 'selesai') {
      session()->setFlashdata('error', 'Anda sudah menyelesaikan attempt ini');
      return redirect()->to(base_url('siswa/ujian'));
    }

    $pesertaId = $peserta['peserta_ujian_id'];
    $paketId = null;
    $attempt = $this->attemptUjianModel->getActiveAttempt($pesertaId);
    if (!empty($attempt['paket_id'])) {
      $paketId = (int) $attempt['paket_id'];
    }

    // Kalau belum ada paket yang terkunci, pilih satu secara acak dari paket yang tersedia
    if (!$paketId) {
      $paket = db_connect()->table('paket_ujian_cbt')
        ->where('ujian_id', $ujianInfo['id_ujian'])
        ->orderBy('RAND()')
        ->get(1)
        ->getRowArray();

      if (!$paket) {
        session()->setFlashdata('error', 'Paket CBT belum tersedia. Hubungi guru untuk generate paket.');
        return redirect()->to(base_url('siswa/ujian'));
      }

      $paketId = $paket['paket_id'];
    }

    // Attempt baru: buat record attempt lalu generate snapshot soal CBT
    if (!$attempt) {
      $nomorAttempt = $this->attemptUjianModel->where('peserta_ujian_id', $pesertaId)->countAllResults() + 1;
      $attemptId = $this->attemptUjianModel->insert([
        'peserta_ujian_id' => $pesertaId,
        'nomor_attempt' => $nomorAttempt,
        'paket_id' => $paketId,
        'status' => 'sedang_mengerjakan',
        'waktu_mulai' => date('Y-m-d H:i:s'),
        'created_at' => date('Y-m-d H:i:s'),
      ]);
      $attempt = $this->attemptUjianModel->find($attemptId);

      if (!$this->buatSnapshotAttemptCbt($attempt, $paketId, $pesertaId,
          (bool)($ujianInfo['acak_urutan_soal'] ?? false),
          (bool)($ujianInfo['acak_pilihan_jawaban'] ?? false)
      )) {
        session()->setFlashdata('error', 'Gagal membuat snapshot soal CBT.');
        return redirect()->to(base_url('siswa/ujian'));
      }

      $this->pesertaUjianModel->update($pesertaId, [
        'status' => 'sedang_mengerjakan',
        'waktu_mulai' => date('Y-m-d H:i:s'),
      ]);
    }

    // Load urutan soal dari session; kalau session hilang, rebuild dari tabel attempt_soal_cbt
    $sessionKey = $this->getCbtSessionKey((int) $attempt['attempt_id']);
    $cbtParams = session()->get($sessionKey);
    if (!$cbtParams || empty($cbtParams['attempt_soal_ids'])) {
      $snapshotSoal = $this->attemptSoalModel->getByAttempt($attempt['attempt_id']);
      if (empty($snapshotSoal)) {
        if (!$this->buatSnapshotAttemptCbt($attempt, $paketId, $pesertaId,
            (bool)($ujianInfo['acak_urutan_soal'] ?? false),
            (bool)($ujianInfo['acak_pilihan_jawaban'] ?? false)
        )) {
          session()->setFlashdata('error', 'Snapshot soal CBT tidak tersedia.');
          return redirect()->to(base_url('siswa/ujian'));
        }
        $snapshotSoal = $this->attemptSoalModel->getByAttempt($attempt['attempt_id']);
      }

      if (empty($snapshotSoal)) {
        session()->setFlashdata('error', 'Snapshot soal CBT kosong.');
        return redirect()->to(base_url('siswa/ujian'));
      }

      $cbtParams = [
        'attempt_id' => $attempt['attempt_id'],
        'attempt_soal_ids' => array_column($snapshotSoal, 'attempt_soal_id'),
        'current_index' => 0,
      ];
      session()->set($sessionKey, $cbtParams);
    }

    // Hitung sisa waktu; kalau sudah habis otomatis selesaikan ujian
    $waktuMulai = $attempt['waktu_mulai'] ?: date('Y-m-d H:i:s');
    $durasi = explode(':', $ujianInfo['durasi']);
    $durasiDetik = ($durasi[0] * 3600) + ($durasi[1] * 60) + (isset($durasi[2]) ? $durasi[2] : 0);
    $sisaWaktu = (strtotime($waktuMulai) + $durasiDetik) - time();

    if ($sisaWaktu <= 0) {
      return redirect()->to(base_url("siswa/ujian/selesai/{$jadwalId}"));
    }

    // Ambil soal sesuai current_index dari urutan snapshot CBT
    $attemptSoalId = $cbtParams['attempt_soal_ids'][$cbtParams['current_index']] ?? null;
    $soal = $attemptSoalId ? $this->formatSnapshotSoalForView($this->attemptSoalModel->find($attemptSoalId)) : null;
    if (!$soal) {
      session()->setFlashdata('error', 'Soal CBT tidak ditemukan.');
      return redirect()->to(base_url('siswa/ujian'));
    }

    return view('siswa/soal', [
      'ujian' => $ujianInfo,
      'soal' => $soal,
      'sisa_waktu' => $sisaWaktu,
      'total_soal' => count($cbtParams['attempt_soal_ids']),
      'soal_dijawab' => $cbtParams['current_index'],
      'nomor_attempt' => $attempt['nomor_attempt'],
    ]);
  }

  // Simpan jawaban CBT: validasi urutan soal, catat ke attempt_jawaban + hasil_ujian, lalu maju ke soal berikutnya
  // Kalau soal terakhir sudah dijawab, hitung nilai akhir dan selesaikan attempt
  private function simpanJawabanCbt(array $ujianInfo, $attemptSoalId, $jawaban)
  {
    $siswa = $this->siswaModel->where('user_id', session()->get('user_id'))->first();
    $peserta = $this->pesertaUjianModel
      ->where('jadwal_id', $ujianInfo['jadwal_id'])
      ->where('siswa_id', $siswa['siswa_id'])
      ->first();

    $attempt = $this->attemptUjianModel->getActiveAttempt($peserta['peserta_ujian_id']);
    if (!$attempt) {
      session()->setFlashdata('error', 'Attempt CBT aktif tidak ditemukan.');
      return redirect()->to(base_url('siswa/ujian'));
    }

    if (empty($attempt['paket_id'])) {
      session()->setFlashdata('error', 'Paket CBT belum terkunci untuk attempt ini.');
      return redirect()->to(base_url('siswa/ujian'));
    }

    $sessionKey = $this->getCbtSessionKey((int) $attempt['attempt_id']);
    $cbtParams = session()->get($sessionKey);
    if (!$cbtParams) {
      session()->setFlashdata('error', 'Urutan soal CBT tidak ditemukan.');
      return redirect()->to(base_url('siswa/ujian/soal/' . $ujianInfo['jadwal_id']));
    }

    // Pastikan soal yang dikirim sesuai urutan yang diharapkan (anti-skip)
    $expectedAttemptSoalId = $cbtParams['attempt_soal_ids'][$cbtParams['current_index']] ?? null;
    if ((int)$expectedAttemptSoalId !== (int)$attemptSoalId) {
      session()->setFlashdata('error', 'Urutan soal tidak valid.');
      return redirect()->to(base_url('siswa/ujian/soal/' . $ujianInfo['jadwal_id']));
    }

    $soal = $this->attemptSoalModel->find($attemptSoalId);
    if (!$soal) {
      session()->setFlashdata('error', 'Snapshot soal tidak ditemukan.');
      return redirect()->to(base_url('siswa/ujian/soal/' . $ujianInfo['jadwal_id']));
    }

    $soalId = $soal['original_soal_id'];
    $isBenar = $soal && $jawaban === $soal['jawaban_benar'];
    $nomorTampil = $cbtParams['current_index'] + 1;

    // Insert jawaban hanya sekali; hindari duplikat kalau siswa reload halaman
    if (!$this->attemptJawabanCbtModel->where(['attempt_id' => $attempt['attempt_id'], 'soal_id' => $soalId])->first()) {
      $this->attemptJawabanCbtModel->insert([
        'attempt_id' => $attempt['attempt_id'],
        'soal_id' => $soalId,
        'nomor_tampil' => $nomorTampil,
        'jawaban_siswa' => $jawaban,
        'is_correct' => $isBenar ? 1 : 0,
        'waktu_menjawab' => date('Y-m-d H:i:s'),
      ]);
    }

    // Maju ke soal berikutnya; kalau sudah habis, hitung EAP-IRT dan selesaikan.
    $cbtParams['current_index']++;
    if ($cbtParams['current_index'] >= count($cbtParams['attempt_soal_ids'])) {
      $attemptId = (int) $attempt['attempt_id'];
      $responses = $this->ambilResponsesUntukEAP($attemptId);
      $eap = CbtEngine::estimasiEAP($responses);
      $residu = CbtEngine::analisisResidu($eap['theta_final'], $responses);
      $now = date('Y-m-d H:i:s');

      $this->attemptUjianModel->update($attemptId, [
        'status' => 'selesai',
        'waktu_selesai' => $now,
        'nilai_akhir' => $eap['NA'],
        'theta_akhir' => $eap['theta_final'],
        'sem_akhir' => $eap['SEM'],
      ]);

      $this->attemptAnalisisCbtModel->where('attempt_id', $attemptId)->delete();
      $batch = [];
      foreach ($residu as $row) {
        $batch[] = [
          'attempt_id' => $attemptId,
          'soal_id' => $row['soal_id'],
          'is_correct' => $row['jawab_id'],
          'p_residu' => $row['p'],
          'q_residu' => $row['q'],
          'z_score' => $row['z'],
          'kategori_soal' => $row['kategori_soal'],
          'keterangan' => $row['keterangan'],
          'created_at' => $now,
        ];
      }
      if (!empty($batch)) {
        $this->attemptAnalisisCbtModel->insertBatch($batch);
      }

      $this->pesertaUjianModel->update($peserta['peserta_ujian_id'], [
        'status' => 'selesai',
        'waktu_selesai' => $now,
      ]);
      session()->remove($sessionKey);

      return redirect()->to(base_url("siswa/ujian/selesai/{$ujianInfo['jadwal_id']}"));
    }

    session()->set($sessionKey, $cbtParams);
    return redirect()->to(base_url('siswa/ujian/soal/' . $ujianInfo['jadwal_id']));
  }

  /**
   * Ambil semua jawaban CBT satu attempt beserta parameter soal snapshot.
   */
  private function ambilResponsesUntukEAP(int $attemptId): array
  {
    $rows = $this->attemptJawabanCbtModel
      ->select('attempt_jawaban_cbt.soal_id')
      ->select('attempt_jawaban_cbt.is_correct')
      ->select('COALESCE(attempt_soal_cbt.a, soal_ujian.a, 1.000) AS a', false)
      ->select('COALESCE(attempt_soal_cbt.tingkat_kesulitan, soal_ujian.tingkat_kesulitan, 0.000) AS b', false)
      ->select('COALESCE(attempt_soal_cbt.c, soal_ujian.c, 0.000) AS c', false)
      ->join('attempt_soal_cbt', 'attempt_soal_cbt.attempt_id = attempt_jawaban_cbt.attempt_id AND attempt_soal_cbt.original_soal_id = attempt_jawaban_cbt.soal_id', 'left')
      ->join('soal_ujian', 'soal_ujian.soal_id = attempt_jawaban_cbt.soal_id', 'left')
      ->where('attempt_jawaban_cbt.attempt_id', $attemptId)
      ->orderBy('attempt_jawaban_cbt.nomor_tampil', 'ASC')
      ->findAll();

    $responses = [];
    foreach ($rows as $row) {
      $responses[] = [
        'soal_id' => (int) $row['soal_id'],
        'a' => (float) $row['a'],
        'b' => (float) $row['b'],
        'c' => (float) $row['c'],
        'u' => (int) $row['is_correct'],
      ];
    }

    return $responses;
  }

  // Buat snapshot soal CBT ke tabel attempt_soal_cbt; reuse dari attempt sebelumnya jika ada (paket sama)
  // Kalau attempt pertama, ambil dari paket_ujian_cbt lalu shuffle jika diaktifkan
  private function buatSnapshotAttemptCbt(array $attempt, $paketId, $pesertaId, bool $acakUrutan = false, bool $acakPilihan = false)
  {
    // Snapshot sudah ada, tidak perlu dibuat ulang
    if ($this->attemptSoalModel->where('attempt_id', $attempt['attempt_id'])->countAllResults() > 0) {
      return true;
    }

    // Coba copy snapshot dari attempt sebelumnya dengan paket yang sama (urutan tetap konsisten)
    $sourceAttempt = $this->attemptUjianModel
      ->where('peserta_ujian_id', $pesertaId)
      ->where('paket_id', $paketId)
      ->where('attempt_id !=', $attempt['attempt_id'])
      ->orderBy('nomor_attempt', 'ASC')
      ->first();

    if ($sourceAttempt) {
      $sourceRows = $this->attemptSoalModel->getByAttempt($sourceAttempt['attempt_id']);
      if (!empty($sourceRows)) {
        $rows = [];
        foreach ($sourceRows as $row) {
          unset($row['attempt_soal_id']);
          $row['attempt_id'] = $attempt['attempt_id'];
          $row['created_at'] = date('Y-m-d H:i:s');
          $rows[] = $row;
        }
        return (bool) $this->attemptSoalModel->insertBatch($rows);
      }
    }

    // Tidak ada attempt sebelumnya: ambil soal dari paket dengan urutan nomor_urut (false = tidak acak di DB)
    $soals = $this->paketUjianModel->getSoalByPaket($paketId, false);
    if (empty($soals)) {
      return false;
    }

    // Acak urutan soal di PHP hanya jika fitur diaktifkan
    if ($acakUrutan) {
      shuffle($soals);
    }

    $rows = [];
    foreach ($soals as $index => $soal) {
      if ($acakPilihan) {
        $soal = $this->acakPilihanJawaban($soal);
      }
      $rows[] = [
        'attempt_id' => $attempt['attempt_id'],
        'paket_id' => $paketId,
        'original_soal_id' => $soal['soal_id'] ?? null,
        'nomor_urut' => $index + 1,
        'kode_soal' => $soal['kode_soal'] ?? null,
        'pertanyaan' => $soal['pertanyaan'] ?? '',
        'pilihan_a' => $soal['pilihan_a'] ?? null,
        'pilihan_b' => $soal['pilihan_b'] ?? null,
        'pilihan_c' => $soal['pilihan_c'] ?? null,
        'pilihan_d' => $soal['pilihan_d'] ?? null,
        'pilihan_e' => $soal['pilihan_e'] ?? null,
        'jawaban_benar' => $soal['jawaban_benar'] ?? 'A',
        'tingkat_kesulitan' => $soal['tingkat_kesulitan'] ?? null,
        'a' => $soal['a'] ?? 1.000,
        'c' => $soal['c'] ?? 0.000,
        'pembahasan' => $soal['pembahasan'] ?? null,
        'media' => $soal['media'] ?? ($soal['foto'] ?? null),
        'created_at' => date('Y-m-d H:i:s'),
      ];
    }

    return (bool) $this->attemptSoalModel->insertBatch($rows);
  }

  // Acak urutan pilihan jawaban (A-E) tapi tetap update kunci jawaban sesuai nilai yang benar
  private function acakPilihanJawaban(array $soal): array
  {
    $kunci = ['A', 'B', 'C', 'D', 'E'];
    $pilihan = [];
    foreach ($kunci as $k) {
      $val = $soal['pilihan_' . strtolower($k)] ?? null;
      if ($val !== null && $val !== '') {
        $pilihan[$k] = $val;
      }
    }
    if (count($pilihan) <= 1) return $soal;

    $jawabanBenarNilai = $pilihan[$soal['jawaban_benar'] ?? 'A'] ?? null;
    $keys   = array_keys($pilihan);
    $values = array_values($pilihan);
    shuffle($values);
    $pilihanAcak = array_combine($keys, $values);

    foreach ($pilihanAcak as $k => $v) {
      $soal['pilihan_' . strtolower($k)] = $v;
    }
    if ($jawabanBenarNilai !== null) {
      foreach ($pilihanAcak as $k => $v) {
        if ($v === $jawabanBenarNilai) {
          $soal['jawaban_benar'] = $k;
          break;
        }
      }
    }
    return $soal;
  }

  // Normalkan field snapshot soal agar kompatibel dengan view (soal_id, foto, dll)
  private function formatSnapshotSoalForView(?array $soal)
  {
    if (!$soal) {
      return null;
    }

    $soal['soal_id'] = $soal['original_soal_id'];
    $soal['attempt_soal_id'] = $soal['attempt_soal_id'];
    $soal['foto'] = $soal['media'] ?? null;

    return $soal;
  }

  // Render soal CAT: buat/lanjutkan attempt, load parameter theta/SE dari session, kirim soal berikutnya ke view
  private function soalCat($jadwalId, array $ujianInfo, array $peserta)
  {
    if ($peserta['status'] === 'selesai') {
      session()->setFlashdata('error', 'Anda sudah menyelesaikan attempt ini');
      return redirect()->to(base_url('siswa/ujian'));
    }

    $pesertaId = (int) $peserta['peserta_ujian_id'];
    $attempt = $this->attemptUjianModel->getActiveAttempt($pesertaId);
    // CAT tidak pakai paket; buat attempt baru dan generate snapshot pool soal
    if (!$attempt) {
      $nomorAttempt = $this->attemptUjianModel->where('peserta_ujian_id', $pesertaId)->countAllResults() + 1;
      $attemptId = $this->attemptUjianModel->insert([
        'peserta_ujian_id' => $pesertaId,
        'nomor_attempt' => $nomorAttempt,
        'paket_id' => null,
        'status' => 'sedang_mengerjakan',
        'waktu_mulai' => date('Y-m-d H:i:s'),
        'created_at' => date('Y-m-d H:i:s'),
      ]);
      $attempt = $this->attemptUjianModel->find($attemptId);

      if (!$this->buatSnapshotAttemptCat($attempt, (int) $ujianInfo['id_ujian'], $pesertaId,
          (bool)($ujianInfo['acak_pilihan_jawaban'] ?? false)
      )) {
        session()->setFlashdata('error', 'Gagal membuat snapshot soal CAT.');
        return redirect()->to(base_url('siswa/ujian'));
      }

      $this->pesertaUjianModel->update($pesertaId, [
        'status' => 'sedang_mengerjakan',
        'waktu_mulai' => $attempt['waktu_mulai'],
      ]);
    }

    // Load state CAT dari session; kalau hilang (e.g. timeout), rebuild dari database
    $sessionKey = $this->getCatSessionKey((int) $attempt['attempt_id']);
    $catParams = session()->get($sessionKey);
    if (!$catParams) {
      $catParams = $this->buildCatSessionParams($attempt, (int) $ujianInfo['id_ujian']);
      if (!$catParams || empty($catParams['current_question'])) {
        session()->setFlashdata('error', 'Snapshot soal CAT kosong.');
        return redirect()->to(base_url('siswa/ujian'));
      }
      session()->set($sessionKey, $catParams);
    }

    // Hitung sisa waktu; kalau habis selesaikan dengan theta terakhir sebagai nilai akhir
    $waktuMulai = $attempt['waktu_mulai'] ?: date('Y-m-d H:i:s');
    $durasi = explode(':', $ujianInfo['durasi']);
    $durasiDetik = ($durasi[0] * 3600) + ($durasi[1] * 60) + (isset($durasi[2]) ? $durasi[2] : 0);
    $sisaWaktu = (strtotime($waktuMulai) + $durasiDetik) - time();

    if ($sisaWaktu <= 0) {
      $nilaiAkhir = (float) ($catParams['theta'] ?? 0);
      $this->attemptUjianModel->update($attempt['attempt_id'], [
        'status' => 'selesai',
        'waktu_selesai' => date('Y-m-d H:i:s'),
        'nilai_akhir' => $nilaiAkhir,
      ]);
      $this->pesertaUjianModel->update($pesertaId, [
        'status' => 'selesai',
        'waktu_selesai' => date('Y-m-d H:i:s')
      ]);
      session()->remove($sessionKey);

      return redirect()->to(base_url("siswa/ujian/selesai/{$jadwalId}"));
    }

    return view('siswa/soal', [
      'ujian' => $ujianInfo,
      'soal' => $catParams['current_question'],
      'sisa_waktu' => $sisaWaktu,
      'total_soal' => 'Adaptif',
      'soal_dijawab' => count($catParams['answered_questions']),
      'nomor_attempt' => $attempt['nomor_attempt'],
    ]);
  }

  // Inti algoritma CAT: update theta (=b soal terakhir), hitung Fisher Information & SE baru,
  // pilih soal berikutnya, lalu cek stopping rule sebelum lanjut
  private function simpanJawabanCat(array $ujianInfo, $attemptSoalId, $jawaban)
  {
    $siswa = $this->siswaModel->where('user_id', session()->get('user_id'))->first();
    $peserta = $this->pesertaUjianModel
      ->where('jadwal_id', $ujianInfo['jadwal_id'])
      ->where('siswa_id', $siswa['siswa_id'])
      ->first();

    if (!$peserta) {
      session()->setFlashdata('error', 'Data peserta ujian tidak ditemukan.');
      return redirect()->to(base_url('siswa/ujian'));
    }

    $attempt = $this->attemptUjianModel->getActiveAttempt($peserta['peserta_ujian_id']);
    if (!$attempt) {
      session()->setFlashdata('error', 'Attempt CAT aktif tidak ditemukan.');
      return redirect()->to(base_url('siswa/ujian/soal/' . $ujianInfo['jadwal_id']));
    }

    $sessionKey = $this->getCatSessionKey((int) $attempt['attempt_id']);
    $catParams = session()->get($sessionKey);
    if (!$catParams) {
      $catParams = $this->buildCatSessionParams($attempt, (int) $ujianInfo['id_ujian']);
      if (!$catParams) {
        session()->setFlashdata('error', 'Parameter CAT tidak ditemukan.');
        return redirect()->to(base_url('siswa/ujian/soal/' . $ujianInfo['jadwal_id']));
      }
    }

    $expectedAttemptSoalId = $catParams['current_question']['attempt_soal_id'] ?? null;
    if ((int) $expectedAttemptSoalId !== (int) $attemptSoalId) {
      session()->setFlashdata('error', 'Urutan soal CAT tidak valid.');
      return redirect()->to(base_url('siswa/ujian/soal/' . $ujianInfo['jadwal_id']));
    }

    $soal = $this->attemptSoalModel->find($attemptSoalId);
    if (!$soal) {
      session()->setFlashdata('error', 'Snapshot soal CAT tidak ditemukan.');
      return redirect()->to(base_url('siswa/ujian/soal/' . $ujianInfo['jadwal_id']));
    }

    $soal = $this->formatSnapshotSoalForView($soal);
    $soalId = (int) ($soal['soal_id'] ?? 0);
    $isBenar = $jawaban === ($soal['jawaban_benar'] ?? null);

    try {
      $theta = (float) ($catParams['theta'] ?? 0);
      $b     = (float) ($soal['tingkat_kesulitan'] ?? 0);

      // Hitung Pi, Qi, Ii soal ini menggunakan CatEngine (Rasch 1PL)
      $Pi = CatEngine::hitungPi($theta, $b);
      $Qi = CatEngine::hitungQi($Pi);
      $Ii = CatEngine::hitungIi($Pi, $Qi);

      // Kumpulkan snapshot soal-soal yang sudah dijawab untuk akumulasi Fisher Information
      $soalYangDijawab = [];
      foreach (($catParams['answered_questions'] ?? []) as $answeredAttemptSoalId) {
        $answeredSoal = $this->attemptSoalModel->find((int) $answeredAttemptSoalId);
        if ($answeredSoal) {
          $soalYangDijawab[] = $answeredSoal;
        }
      }

      // Akumulasi total Fisher Information lalu hitung SE baru
      $totalIi = CatEngine::hitungTotalIi($soalYangDijawab, $theta, $Ii);
      $SEOld   = (float) ($catParams['SE'] ?? 1);
      $SENew   = CatEngine::hitungSE($totalIi, $SEOld);
      $deltaSE = $SEOld - $SENew;

      // Update theta menggunakan MLE Newton-Raphson atas seluruh respons
      $responses   = $catParams['responses'] ?? [];
      $responses[] = ['b' => $b, 'correct' => $isBenar ? 1 : 0];
      $theta       = CatEngine::estimateThetaMLE($responses, $theta);
      $answeredQuestions = array_map('intval', $catParams['answered_questions'] ?? []);
      if (!in_array((int) $attemptSoalId, $answeredQuestions, true)) {
        $answeredQuestions[] = (int) $attemptSoalId;
      }

      $nextQuestion = $this->pilihSoalBerikutnyaCat(
        (int) $attempt['attempt_id'],
        $b,
        $answeredQuestions,
        $isBenar
      );

      $catParams['theta'] = $theta;
      $catParams['SE'] = $SENew;
      $catParams['answered_questions'] = $answeredQuestions;
      $catParams['responses'] = $responses;
      $catParams['current_question'] = $nextQuestion ? $this->formatSnapshotSoalForView($nextQuestion) : null;
      $catParams['total_questions'] = count($answeredQuestions);

      $nomorTampil = count($answeredQuestions);
      if (!$this->attemptJawabanCatModel->where(['attempt_id' => $attempt['attempt_id'], 'soal_id' => $soalId])->first()) {
        $this->attemptJawabanCatModel->insert([
          'attempt_id' => $attempt['attempt_id'],
          'soal_id' => $soalId,
          'nomor_tampil' => $nomorTampil,
          'jawaban_siswa' => $jawaban,
          'is_correct' => $isBenar ? 1 : 0,
          'waktu_menjawab' => date('Y-m-d H:i:s'),
          'theta_saat_ini' => $theta,
          'se_saat_ini' => $SENew,
          'delta_se_saat_ini' => $deltaSE,
          'pi_saat_ini' => $Pi,
          'qi_saat_ini' => $Qi,
          'ii_saat_ini' => $Ii,
        ]);
      }

      // Load CAT params dari ujian_param_cat untuk stopping rule
      $catParam = $this->ujianCatParamModel->getByUjian((int) $ujianInfo['id_ujian']);
      $seMinimum = (float) ($catParam['se_minimum'] ?? 0.25);
      $deltaSeMinimum = (float) ($catParam['delta_se_minimum'] ?? 0.01);

      // Stopping rule CAT via CatEngine
      $shouldStop = CatEngine::shouldStop($SENew, $deltaSE, (bool) $nextQuestion, $seMinimum, $deltaSeMinimum);

      session()->set($sessionKey, $catParams);

      if ($shouldStop) {
        $this->attemptUjianModel->update($attempt['attempt_id'], [
          'status' => 'selesai',
          'waktu_selesai' => date('Y-m-d H:i:s'),
          'nilai_akhir' => $theta,
        ]);
        $this->pesertaUjianModel->update($peserta['peserta_ujian_id'], [
          'status' => 'selesai',
          'waktu_selesai' => date('Y-m-d H:i:s')
        ]);
        session()->remove($sessionKey);

        return redirect()->to(base_url("siswa/ujian/selesai/{$ujianInfo['jadwal_id']}"));
      }

      return redirect()->to(base_url('siswa/ujian/soal/' . $ujianInfo['jadwal_id']));
    } catch (\Exception $e) {
      log_message('error', 'Error saat memproses jawaban CAT: ' . $e->getMessage());
      session()->setFlashdata('error', 'Terjadi kesalahan saat memproses jawaban CAT.');
      return redirect()->to(base_url('siswa/ujian/soal/' . $ujianInfo['jadwal_id']));
    }
  }

  // Buat snapshot pool soal CAT diurutkan dari b terkecil ke terbesar (mudah ke sulit)
  // Reuse dari attempt sebelumnya jika ada; kalau tidak, ambil dari ujian_soal_cat
  private function buatSnapshotAttemptCat(array $attempt, int $ujianId, int $pesertaId, bool $acakPilihan = false): bool
  {
    // Snapshot sudah ada, skip
    if ($this->attemptSoalModel->where('attempt_id', $attempt['attempt_id'])->countAllResults() > 0) {
      return true;
    }

    // Coba reuse snapshot dari attempt CAT sebelumnya (pool soal harus konsisten antar attempt)
    $sourceAttempts = $this->attemptUjianModel
      ->where('peserta_ujian_id', $pesertaId)
      ->where('attempt_id !=', $attempt['attempt_id'])
      ->orderBy('nomor_attempt', 'ASC')
      ->findAll();

    foreach ($sourceAttempts as $sourceAttempt) {
      $sourceRows = $this->attemptSoalModel->getByAttempt($sourceAttempt['attempt_id']);
      if (empty($sourceRows)) {
        continue;
      }

      $rows = [];
      foreach ($sourceRows as $row) {
        unset($row['attempt_soal_id']);
        $row['attempt_id'] = $attempt['attempt_id'];
        $row['created_at'] = date('Y-m-d H:i:s');
        $rows[] = $row;
      }

      return (bool) $this->attemptSoalModel->insertBatch($rows);
    }

    // Attempt pertama: ambil semua soal CAT lalu urutkan ascending berdasarkan tingkat kesulitan (b)
    $pool = $this->ujianSoalCatModel->getSoalByUjian($ujianId);
    if (empty($pool)) {
      return false;
    }

    // Urutan b ascending: soal paling mudah di index 0, jadi pilihSoalAwal dapat soal tengah (b~0)
    usort($pool, static function (array $a, array $b): int {
      $difficultyA = (float) ($a['tingkat_kesulitan'] ?? 0);
      $difficultyB = (float) ($b['tingkat_kesulitan'] ?? 0);
      if ($difficultyA === $difficultyB) {
        return ((int) ($a['soal_id'] ?? 0)) <=> ((int) ($b['soal_id'] ?? 0));
      }
      return $difficultyA <=> $difficultyB;
    });

    $rows = [];
    foreach (array_values($pool) as $index => $soal) {
      if ($acakPilihan) {
        $soal = $this->acakPilihanJawaban($soal);
      }
      $rows[] = [
        'attempt_id' => $attempt['attempt_id'],
        'paket_id' => null,
        'original_soal_id' => $soal['soal_id'] ?? null,
        'nomor_urut' => $index + 1,
        'kode_soal' => $soal['kode_soal'] ?? null,
        'pertanyaan' => $soal['pertanyaan'] ?? '',
        'pilihan_a' => $soal['pilihan_a'] ?? null,
        'pilihan_b' => $soal['pilihan_b'] ?? null,
        'pilihan_c' => $soal['pilihan_c'] ?? null,
        'pilihan_d' => $soal['pilihan_d'] ?? null,
        'pilihan_e' => $soal['pilihan_e'] ?? null,
        'jawaban_benar' => $soal['jawaban_benar'] ?? 'A',
        'tingkat_kesulitan' => $soal['tingkat_kesulitan'] ?? null,
        'pembahasan' => $soal['pembahasan'] ?? null,
        'media' => $soal['foto'] ?? ($soal['media'] ?? null),
        'created_at' => date('Y-m-d H:i:s'),
      ];
    }

    return (bool) $this->attemptSoalModel->insertBatch($rows);
  }

  // Rebuild state CAT dari database (theta, SE, soal sudah dijawab, soal berikutnya)
  // Dipanggil saat session habis tapi attempt masih aktif
  private function buildCatSessionParams(array $attempt, int $ujianId): ?array
  {
    $snapshotSoal = $this->attemptSoalModel->getByAttempt($attempt['attempt_id']);
    if (empty($snapshotSoal)) {
      if (!$this->buatSnapshotAttemptCat($attempt, $ujianId, (int) $attempt['peserta_ujian_id'])) {
        return null;
      }
      $snapshotSoal = $this->attemptSoalModel->getByAttempt($attempt['attempt_id']);
    }

    if (empty($snapshotSoal)) {
      return null;
    }

    $jawabanRows = $this->attemptJawabanCatModel
      ->where('attempt_id', $attempt['attempt_id'])
      ->orderBy('nomor_tampil', 'ASC')
      ->findAll();

    // Ambil se_awal dari konfigurasi ujian untuk inisialisasi SE yang benar
    $catParam = $this->ujianCatParamModel->getByUjian($ujianId);
    $seAwal   = (float) ($catParam['se_awal'] ?? 1.0);

    $answeredAttemptSoalIds = [];
    $responses = [];
    $theta = 0;
    $SE    = $seAwal;

    // Rekonstruksi daftar soal yang sudah dijawab, ambil theta/SE terakhir, dan rebuild responses untuk MLE
    foreach ($jawabanRows as $jawaban) {
      foreach ($snapshotSoal as $snapshot) {
        if ((int) $snapshot['original_soal_id'] === (int) $jawaban['soal_id']) {
          $answeredAttemptSoalIds[] = (int) $snapshot['attempt_soal_id'];
          $responses[] = [
            'b'       => (float) ($snapshot['tingkat_kesulitan'] ?? 0),
            'correct' => (int) ($jawaban['is_correct'] ?? 0),
          ];
          break;
        }
      }
      $theta = (float) ($jawaban['theta_saat_ini'] ?? $theta);
      $SE    = (float) ($jawaban['se_saat_ini']    ?? $SE);
    }

    // Re-estimasi theta dari seluruh respons yang sudah dijawab
    if (!empty($responses)) {
      $theta = CatEngine::estimateThetaMLE($responses, $theta);
    }

    $currentQuestion = null;
    // Belum menjawab soal apapun: pilih soal awal (kesulitan paling dekat ke 0)
    if (empty($jawabanRows)) {
      $currentQuestion = $this->pilihSoalAwalCat((int) $attempt['attempt_id']);
    } else {
      // Sudah ada riwayat jawaban: lanjutkan dari soal berikutnya berdasarkan jawaban terakhir
      $lastJawaban = end($jawabanRows);
      $lastSnapshot = null;
      foreach ($snapshotSoal as $snapshot) {
        if ((int) $snapshot['original_soal_id'] === (int) $lastJawaban['soal_id']) {
          $lastSnapshot = $snapshot;
          break;
        }
      }

      if ($lastSnapshot) {
        $currentQuestion = $this->pilihSoalBerikutnyaCat(
          (int) $attempt['attempt_id'],
          (float) ($lastSnapshot['tingkat_kesulitan'] ?? 0),
          $answeredAttemptSoalIds,
          ((int) ($lastJawaban['is_correct'] ?? 0) === 1)
        );
      }
    }

    return [
      'attempt_id'         => $attempt['attempt_id'],
      'theta'              => $theta,
      'SE'                 => $SE,
      'answered_questions' => $answeredAttemptSoalIds,
      'responses'          => $responses,
      'current_question'   => $currentQuestion ? $this->formatSnapshotSoalForView($currentQuestion) : null,
      'total_questions'    => count($answeredAttemptSoalIds),
    ];
  }

  // Generate key session CAT unik per attempt_id
  private function getCatSessionKey(int $attemptId): string
  {
    return 'cat_attempt_' . $attemptId;
  }

  // Generate key session CBT unik per attempt_id
  private function getCbtSessionKey(int $attemptId): string
  {
    return 'cbt_attempt_' . $attemptId;
  }

  // Hitung durasi pengerjaan tiap soal berdasarkan selisih waktu menjawab antar soal
  private function hitungDurasiPerSoal($detailJawaban, $waktuMulaiUjian)
  {
    $hasilDenganDurasi = [];
    $tsSebelumnya = $waktuMulaiUjian ? strtotime($waktuMulaiUjian) : null;

    foreach ($detailJawaban as $index => $jawaban) {
      $waktuMenjawab = $jawaban['waktu_menjawab'] ?? null;
      $tsMenjawab    = $waktuMenjawab ? strtotime($waktuMenjawab) : null;

      if ($tsSebelumnya !== null && $tsMenjawab !== null && $tsMenjawab > $tsSebelumnya) {
        $durasiDetik = $tsMenjawab - $tsSebelumnya;
        $menit = (int) floor($durasiDetik / 60);
        $detik = $durasiDetik % 60;
        $jawaban['durasi_pengerjaan_detik']  = $durasiDetik;
        $jawaban['durasi_pengerjaan_format'] = sprintf('%d menit %d detik', $menit, $detik);
      } else {
        $jawaban['durasi_pengerjaan_detik']  = 0;
        $jawaban['durasi_pengerjaan_format'] = '-';
      }

      $jawaban['nomor_soal'] = $index + 1;
      $hasilDenganDurasi[]  = $jawaban;
      $tsSebelumnya = $tsMenjawab ?? $tsSebelumnya;
    }

    return $hasilDenganDurasi;
  }

  // Pilih soal awal CAT via CatEngine
  private function pilihSoalAwalCat(int $attemptId): ?array
  {
    return CatEngine::pilihSoalAwal($this->attemptSoalModel->getByAttempt($attemptId));
  }

  // Pilih soal berikutnya CAT via CatEngine
  private function pilihSoalBerikutnyaCat(int $attemptId, float $currentDifficulty, array $answeredIds, bool $naik): ?array
  {
    return CatEngine::pilihSoalBerikutnya(
      $this->attemptSoalModel->getByAttempt($attemptId),
      $answeredIds,
      $currentDifficulty,
      $naik
    );
  }

  // Ambil detail jawaban per soal: prioritaskan dari attempt_jawaban (sistem baru),
  // fallback ke hasil_ujian (data lama sebelum sistem attempt).
  // CBT: di-JOIN dengan attempt_analisis_cbt untuk data residual (p_residu, z_score, keterangan).
  private function getAttemptAwareDetailJawaban($pesertaUjianId, ?int $attemptId = null): array
  {
    $db = db_connect();
    $attemptQuery = $db->table('attempt_ujian')
      ->where('peserta_ujian_id', $pesertaUjianId);

    if ($attemptId !== null) {
      $attemptQuery->where('attempt_id', $attemptId);
    } else {
      $attemptQuery->orderBy('nomor_attempt', 'DESC');
    }

    $attempt = $attemptQuery
      ->get()
      ->getRowArray();

    if ($attempt) {
      $isCbt        = $attempt['paket_id'] !== null;
      $jawabanTable = $isCbt ? 'attempt_jawaban_cbt' : 'attempt_jawaban_cat';

      $builder = $db->table($jawabanTable . ' aj')
        ->select('
          aj.*,
          COALESCE(ats.pertanyaan, su.pertanyaan) as pertanyaan,
          COALESCE(ats.kode_soal, su.kode_soal) as kode_soal,
          COALESCE(ats.pilihan_a, su.pilihan_a) as pilihan_a,
          COALESCE(ats.pilihan_b, su.pilihan_b) as pilihan_b,
          COALESCE(ats.pilihan_c, su.pilihan_c) as pilihan_c,
          COALESCE(ats.pilihan_d, su.pilihan_d) as pilihan_d,
          COALESCE(ats.pilihan_e, su.pilihan_e) as pilihan_e,
          COALESCE(ats.jawaban_benar, su.jawaban_benar) as jawaban_benar,
          COALESCE(ats.tingkat_kesulitan, su.tingkat_kesulitan) as tingkat_kesulitan,
          COALESCE(ats.pembahasan, su.pembahasan) as pembahasan,
          COALESCE(ats.media, su.media) as foto,
          DATE_FORMAT(aj.waktu_menjawab, "%H:%i:%s") as waktu_menjawab_format
        ')
        ->join('attempt_soal_cbt ats', 'ats.attempt_id = aj.attempt_id AND ats.original_soal_id = aj.soal_id', 'left')
        ->join('soal_ujian su', 'su.soal_id = aj.soal_id', 'left');

      if ($isCbt) {
        $builder
          ->select('aac.p_residu, aac.q_residu, aac.z_score, aac.kategori_soal, aac.keterangan as keterangan_residu', false)
          ->join('attempt_analisis_cbt aac', 'aac.attempt_id = aj.attempt_id AND aac.soal_id = aj.soal_id', 'left');
      }

      $rows = $builder
        ->where('aj.attempt_id', $attempt['attempt_id'])
        ->orderBy('aj.nomor_tampil', 'ASC')
        ->orderBy('aj.waktu_menjawab', 'ASC')
        ->get()
        ->getResultArray();

      if (!empty($rows)) {
        return $rows;
      }
    }

    // Fallback: attempt_jawaban kosong, ambil dari hasil_ujian (data lama)
    return $this->hasilUjianModel
      ->select('
        hasil_ujian.*,
        soal_ujian.pertanyaan,
        soal_ujian.kode_soal,
        soal_ujian.jawaban_benar,
        soal_ujian.tingkat_kesulitan,
        soal_ujian.pembahasan,
        soal_ujian.media as foto,
        DATE_FORMAT(hasil_ujian.waktu_menjawab, "%H:%i:%s") as waktu_menjawab_format
      ')
      ->join('soal_ujian', 'soal_ujian.soal_id = hasil_ujian.soal_id')
      ->where('hasil_ujian.peserta_ujian_id', $pesertaUjianId)
      ->orderBy('hasil_ujian.waktu_menjawab', 'ASC')
      ->findAll();
  }

  // Ambil data hasil attempt beserta info ujian dan durasi, hanya untuk siswa yang bersangkutan
  private function getAttemptResultForSiswa(int $attemptId, int $siswaId): ?array
  {
    return db_connect()->table('attempt_ujian au')
      ->select('
        au.*,
        pu.peserta_ujian_id,
        pu.siswa_id,
        jadwal_ujian.*,
        ujian.*,
        ujian.kode_ujian,
        jenis_ujian.nama_jenis,
        TIMEDIFF(au.waktu_selesai, au.waktu_mulai) as durasi_total,
        TIME_TO_SEC(TIMEDIFF(au.waktu_selesai, au.waktu_mulai)) as durasi_total_detik,
        DATE_FORMAT(au.waktu_mulai, "%d/%m/%Y %H:%i:%s") as waktu_mulai_format,
        DATE_FORMAT(au.waktu_selesai, "%d/%m/%Y %H:%i:%s") as waktu_selesai_format
      ')
      ->join('peserta_ujian pu', 'pu.peserta_ujian_id = au.peserta_ujian_id')
      ->join('jadwal_ujian', 'jadwal_ujian.jadwal_id = pu.jadwal_id')
      ->join('ujian', 'ujian.id_ujian = jadwal_ujian.ujian_id')
      ->join('jenis_ujian', 'jenis_ujian.jenis_ujian_id = ujian.jenis_ujian_id')
      ->where('au.attempt_id', $attemptId)
      ->where('pu.siswa_id', $siswaId)
      ->get()
      ->getRowArray();
  }


  // Tampilkan riwayat semua ujian siswa, dikelompokkan per peserta_ujian_id
  public function hasil()
  {
    if (!session()->get('user_id')) {
      return redirect()->to(base_url('login'));
    }

    $userId = session()->get('user_id');
    $siswa = $this->siswaModel->where('user_id', $userId)->first();

    $rows = db_connect()->table('attempt_ujian au')
      ->select('
            au.*,
            peserta_ujian.peserta_ujian_id,
            peserta_ujian.jadwal_id,
            ujian.nama_ujian,
            ujian.kode_ujian,
            ujian.deskripsi,
            ujian.durasi,
            ujian.tipe_ujian,
            jenis_ujian.nama_jenis,
            TIMEDIFF(au.waktu_selesai, au.waktu_mulai) as durasi_pengerjaan,
            TIME_TO_SEC(TIMEDIFF(au.waktu_selesai, au.waktu_mulai)) as durasi_detik,
            DATE_FORMAT(au.waktu_mulai, "%d/%m/%Y %H:%i:%s") as waktu_mulai_format,
            DATE_FORMAT(au.waktu_selesai, "%d/%m/%Y %H:%i:%s") as waktu_selesai_format
        ')
      ->join('peserta_ujian', 'peserta_ujian.peserta_ujian_id = au.peserta_ujian_id')
      ->join('jadwal_ujian', 'jadwal_ujian.jadwal_id = peserta_ujian.jadwal_id')
      ->join('ujian', 'ujian.id_ujian = jadwal_ujian.ujian_id')
      ->join('jenis_ujian', 'jenis_ujian.jenis_ujian_id = ujian.jenis_ujian_id')
      ->where('peserta_ujian.siswa_id', $siswa['siswa_id'])
      ->where('au.status', 'selesai')
      ->orderBy('peserta_ujian.peserta_ujian_id', 'DESC')
      ->orderBy('au.nomor_attempt', 'ASC')
      ->get()
      ->getResultArray();

    // Kelompokkan semua attempt per peserta_ujian_id agar bisa ditampilkan per ujian
    $groupedRiwayat = [];
    foreach ($rows as $ujian) {
      $jawabanModel = ($ujian['tipe_ujian'] ?? 'CAT') === 'CAT'
        ? $this->attemptJawabanCatModel
        : $this->attemptJawabanCbtModel;
      $ujian['jumlah_soal'] = $jawabanModel->where('attempt_id', $ujian['attempt_id'])->countAllResults();

      if ($ujian['durasi_detik']) {
        $jam = floor($ujian['durasi_detik'] / 3600);
        $menit = floor(($ujian['durasi_detik'] % 3600) / 60);
        $detik = $ujian['durasi_detik'] % 60;
        $ujian['durasi_format'] = sprintf('%02d:%02d:%02d', $jam, $menit, $detik);
      } else {
        $ujian['durasi_format'] = '00:00:00';
      }

      $groupKey = (int) $ujian['peserta_ujian_id'];
      if (!isset($groupedRiwayat[$groupKey])) {
        $groupedRiwayat[$groupKey] = [
          'peserta_ujian_id' => $ujian['peserta_ujian_id'],
          'jadwal_id' => $ujian['jadwal_id'],
          'nama_ujian' => $ujian['nama_ujian'],
          'kode_ujian' => $ujian['kode_ujian'],
          'deskripsi' => $ujian['deskripsi'],
          'durasi' => $ujian['durasi'],
          'tipe_ujian' => $ujian['tipe_ujian'],
          'nama_jenis' => $ujian['nama_jenis'],
          'attempt_terakhir' => null,
          'jumlah_attempt' => 0,
          'attempts' => [],
        ];
      }

      $groupedRiwayat[$groupKey]['attempts'][] = $ujian;
      $groupedRiwayat[$groupKey]['jumlah_attempt']++;
      $groupedRiwayat[$groupKey]['attempt_terakhir'] = $ujian;
    }

    $data = [
      'riwayatUjian' => array_values($groupedRiwayat)
    ];

    return view('siswa/hasil', $data);
  }

  // Tampilkan semua attempt untuk satu peserta_ujian_id; skor ditampilkan berbeda untuk CAT vs CBT
  public function hasilUjian($pesertaUjianId)
  {
    if (!session()->get('user_id')) {
      return redirect()->to(base_url('login'));
    }

    $userId = session()->get('user_id');
    $siswa = $this->siswaModel->where('user_id', $userId)->first();

    $rows = db_connect()->table('attempt_ujian au')
      ->select('
            au.*,
            peserta_ujian.peserta_ujian_id,
            peserta_ujian.jadwal_id,
            ujian.nama_ujian,
            ujian.kode_ujian,
            ujian.deskripsi,
            ujian.durasi,
            ujian.tipe_ujian,
            jenis_ujian.nama_jenis,
            TIMEDIFF(au.waktu_selesai, au.waktu_mulai) as durasi_pengerjaan,
            TIME_TO_SEC(TIMEDIFF(au.waktu_selesai, au.waktu_mulai)) as durasi_detik,
            DATE_FORMAT(au.waktu_mulai, "%d/%m/%Y %H:%i:%s") as waktu_mulai_format,
            DATE_FORMAT(au.waktu_selesai, "%d/%m/%Y %H:%i:%s") as waktu_selesai_format
        ')
      ->join('peserta_ujian', 'peserta_ujian.peserta_ujian_id = au.peserta_ujian_id')
      ->join('jadwal_ujian', 'jadwal_ujian.jadwal_id = peserta_ujian.jadwal_id')
      ->join('ujian', 'ujian.id_ujian = jadwal_ujian.ujian_id')
      ->join('jenis_ujian', 'jenis_ujian.jenis_ujian_id = ujian.jenis_ujian_id')
      ->where('peserta_ujian.siswa_id', $siswa['siswa_id'])
      ->where('peserta_ujian.peserta_ujian_id', $pesertaUjianId)
      ->where('au.status', 'selesai')
      ->orderBy('au.nomor_attempt', 'ASC')
      ->get()
      ->getResultArray();

    if (empty($rows)) {
      session()->setFlashdata('error', 'Riwayat ujian tidak ditemukan.');
      return redirect()->to(base_url('siswa/hasil'));
    }

    foreach ($rows as &$attempt) {
      $jawabanModelHasil = ($attempt['tipe_ujian'] ?? 'CAT') === 'CAT'
        ? $this->attemptJawabanCatModel
        : $this->attemptJawabanCbtModel;
      $attempt['jumlah_soal'] = $jawabanModelHasil->where('attempt_id', $attempt['attempt_id'])->countAllResults();

      if ($attempt['durasi_detik']) {
        $jam = floor($attempt['durasi_detik'] / 3600);
        $menit = floor(($attempt['durasi_detik'] % 3600) / 60);
        $detik = $attempt['durasi_detik'] % 60;
        $attempt['durasi_format'] = sprintf('%02d:%02d:%02d', $jam, $menit, $detik);
      } else {
        $attempt['durasi_format'] = '00:00:00';
      }

      // CBT: nilai_akhir = NA (sudah skala 0-100 dari EAP); CAT: konversi theta → skor kognitif
      $attempt['nilai_tampil'] = ($attempt['tipe_ujian'] ?? 'CAT') === 'CBT'
        ? round((float) ($attempt['nilai_akhir'] ?? 0), 2)
        : $this->hitungKemampuanKognitif((float) ($attempt['nilai_akhir'] ?? 0));
    }
    unset($attempt);

    $data = [
      'ujian' => [
        'peserta_ujian_id' => $rows[0]['peserta_ujian_id'],
        'nama_ujian' => $rows[0]['nama_ujian'],
        'kode_ujian' => $rows[0]['kode_ujian'],
        'deskripsi' => $rows[0]['deskripsi'],
        'durasi' => $rows[0]['durasi'],
        'tipe_ujian' => $rows[0]['tipe_ujian'],
        'nama_jenis' => $rows[0]['nama_jenis'],
      ],
      'attempts' => $rows,
    ];

    return view('siswa/hasil_ujian', $data);
  }

  //fungsi kemampuan kognitif

  // Konversi theta IRT ke skor skala 0-100 menggunakan rumus: skor = 50 + (16.67 × θ)
  private function hitungKemampuanKognitif($theta): float
  {
    return round(CatEngine::hitungKemampuanKognitif((float) $theta), 2);
  }

  // Klasifikasikan skor ke kategori: Sangat Rendah / Rendah / Cukup / Baik / Sangat Baik
  private function getKlasifikasiKognitif($skor)
  {
    if ($skor < 25) {
      return [
        'kategori' => 'Sangat Rendah',
        'class' => 'text-danger',
        'bg_class' => 'bg-danger'
      ];
    } elseif ($skor >= 25 && $skor < 42) {
      return [
        'kategori' => 'Rendah',
        'class' => 'text-orange',
        'bg_class' => 'bg-orange'
      ];
    } elseif ($skor >= 42 && $skor < 58) {
      return [
        'kategori' => 'Cukup',
        'class' => 'text-warning',
        'bg_class' => 'bg-warning'
      ];
    } elseif ($skor >= 58 && $skor < 75) {
      return [
        'kategori' => 'Baik',
        'class' => 'text-info',
        'bg_class' => 'bg-info'
      ];
    } else { // $skor >= 75
      return [
        'kategori' => 'Sangat Baik',
        'class' => 'text-success',
        'bg_class' => 'bg-success'
      ];
    }
  }


  // Detail hasil satu attempt: tampilkan jawaban per soal, durasi per soal, skor dan klasifikasi
  // Mode CAT: gunakan theta/IRT; Mode CBT: gunakan % benar/salah
  public function detailHasil($attemptId)
  {
    if (!session()->get('user_id')) {
      return redirect()->to(base_url('login'));
    }

    $userId = session()->get('user_id');
    $siswa = $this->siswaModel->where('user_id', $userId)->first();
    $hasil = $this->getAttemptResultForSiswa((int) $attemptId, (int) $siswa['siswa_id']);

    if (!$hasil) {
      session()->setFlashdata('error', 'Hasil attempt tidak ditemukan.');
      return redirect()->to(base_url('siswa/hasil'));
    }

    $detailJawaban = $this->getAttemptAwareDetailJawaban($hasil['peserta_ujian_id'], (int) $hasil['attempt_id']);

    $detailJawabanDenganDurasi = $this->hitungDurasiPerSoal($detailJawaban, $hasil['waktu_mulai']);

    $totalSoal = count($detailJawabanDenganDurasi);
    $jawabanBenar = array_reduce($detailJawabanDenganDurasi, function ($carry, $item) {
      return $carry + ($item['is_correct'] ? 1 : 0);
    }, 0);

    // Ambil theta dari jawaban terakhir (bukan dari nilai_akhir) agar lebih akurat
    $lastResult = !empty($detailJawabanDenganDurasi) ? end($detailJawabanDenganDurasi) : null;
    $theta_akhir = $lastResult ? (float) ($lastResult['theta_saat_ini'] ?? 0) : (float) ($hasil['nilai_akhir'] ?? 0);
    $skor_akhir = ($hasil['tipe_ujian'] ?? 'CAT') === 'CBT'
      ? round((float) ($hasil['nilai_akhir'] ?? 0), 2)
      : $this->hitungKemampuanKognitif($theta_akhir);
    $klasifikasiKognitif = $this->getKlasifikasiKognitif($skor_akhir);

    $kemampuanKognitif = [
      'skor' => $skor_akhir,
      'total_benar' => $jawabanBenar,
      'total_salah' => $totalSoal - $jawabanBenar,
      'rata_rata_pilihan' => 0
    ];

    $rataRataWaktu = $totalSoal > 0 ? ($hasil['durasi_total_detik'] / $totalSoal) : 0;
    $rataRataMenit = floor($rataRataWaktu / 60);
    $rataRataDetik = $rataRataWaktu % 60;

    if ($hasil['durasi_total_detik']) {
      $jam = floor($hasil['durasi_total_detik'] / 3600);
      $menit = floor(($hasil['durasi_total_detik'] % 3600) / 60);
      $detik = $hasil['durasi_total_detik'] % 60;
      $hasil['durasi_total_format'] = sprintf('%02d:%02d:%02d', $jam, $menit, $detik);
    } else {
      $hasil['durasi_total_format'] = '00:00:00';
    }

    // Tentukan mode ujian: CAT menggunakan IRT/theta, CBT menggunakan skor benar/salah
    $isCatMode = ($hasil['tipe_ujian'] ?? 'CAT') === 'CAT';

    $data = [
      'detailRole'         => 'siswa',
      'hasil'              => $hasil,
      'detailJawaban'      => $detailJawabanDenganDurasi,
      'totalSoal'          => $totalSoal,
      'jawabanBenar'       => $jawabanBenar,
      'isCatMode'          => $isCatMode,
      'skor'               => $skor_akhir,
      'kemampuanKognitif'  => $kemampuanKognitif,
      'klasifikasiKognitif' => $klasifikasiKognitif,
      'rataRataWaktuFormat' => sprintf('%d menit %d detik', $rataRataMenit, $rataRataDetik),
      'statistikWaktu' => [
        'waktu_tercepat' => $totalSoal > 0 ? min(array_column($detailJawabanDenganDurasi, 'durasi_pengerjaan_detik')) : 0,
        'waktu_terlama'  => $totalSoal > 0 ? max(array_column($detailJawabanDenganDurasi, 'durasi_pengerjaan_detik')) : 0,
        'rata_rata'      => $rataRataWaktu,
      ],
    ];

    return view('siswa/detail_hasil', $data);
  }

  //function unduh hasil ujian

  // Generate halaman cetak hasil ujian untuk diunduh/print; logika sama dengan detailHasil
  public function unduh($attemptId)
  {
    if (!session()->get('user_id')) {
      return redirect()->to(base_url('login'));
    }

    $userId = session()->get('user_id');
    $siswa = $this->siswaModel->where('user_id', $userId)->first();

    $hasil = $this->getAttemptResultForSiswa((int) $attemptId, (int) $siswa['siswa_id']);

    if (!$hasil) {
      session()->setFlashdata('error', 'Anda tidak memiliki akses ke laporan ini');
      return redirect()->to(base_url('siswa/hasil'));
    }

    $detailJawaban = $this->getAttemptAwareDetailJawaban($hasil['peserta_ujian_id'], (int) $hasil['attempt_id']);

    $detailJawabanDenganDurasi = $this->hitungDurasiPerSoal($detailJawaban, $hasil['waktu_mulai']);

    $totalSoal = count($detailJawabanDenganDurasi);
    $jawabanBenar = array_reduce($detailJawabanDenganDurasi, function ($carry, $item) {
      return $carry + ($item['is_correct'] ? 1 : 0);
    }, 0);

    $lastResult = !empty($detailJawabanDenganDurasi) ? end($detailJawabanDenganDurasi) : null;
    $theta_akhir = $lastResult ? (float) ($lastResult['theta_saat_ini'] ?? 0) : (float) ($hasil['nilai_akhir'] ?? 0);
    $skor_akhir = ($hasil['tipe_ujian'] ?? 'CAT') === 'CBT'
      ? round((float) ($hasil['nilai_akhir'] ?? 0), 2)
      : $this->hitungKemampuanKognitif($theta_akhir);
    $klasifikasiKognitif = $this->getKlasifikasiKognitif($skor_akhir);

    $kemampuanKognitif = [
      'skor' => $skor_akhir,
      'total_benar' => $jawabanBenar,
      'total_salah' => $totalSoal - $jawabanBenar,
      'rata_rata_pilihan' => 0
    ];

    if ($hasil['durasi_total_detik']) {
      $jam = floor($hasil['durasi_total_detik'] / 3600);
      $menit = floor(($hasil['durasi_total_detik'] % 3600) / 60);
      $detik = $hasil['durasi_total_detik'] % 60;
      $hasil['durasi_total_format'] = sprintf('%02d:%02d:%02d', $jam, $menit, $detik);
    } else {
      $hasil['durasi_total_format'] = '00:00:00';
    }

    $rataRataWaktu = $totalSoal > 0 ? ($hasil['durasi_total_detik'] / $totalSoal) : 0;
    $rataRataMenit = floor($rataRataWaktu / 60);
    $rataRataDetik = $rataRataWaktu % 60;

    $data = [
      'hasil' => $hasil,
      'detailJawaban' => $detailJawabanDenganDurasi,
      'totalSoal' => $totalSoal,
      'jawabanBenar' => $jawabanBenar,
      'siswa' => $siswa,
      'skor' => $skor_akhir, // Gunakan skor baru yang konsisten
      'kemampuanKognitif' => $kemampuanKognitif,
      'klasifikasiKognitif' => $klasifikasiKognitif,
      'rataRataWaktuFormat' => sprintf('%d menit %d detik', $rataRataMenit, $rataRataDetik)
    ];

    return view('siswa/cetak_hasil_ujian', $data);
  }
}
