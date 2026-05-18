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
use App\Models\AttemptSoalModel;
use App\Models\UjianSoalCatModel;


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
  protected $attemptSoalModel;
  protected $ujianSoalCatModel;

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
    $this->attemptSoalModel = new AttemptSoalModel();
    $this->ujianSoalCatModel = new UjianSoalCatModel();
  }

  //dashboard

  public function dashboard()
  {
    return view('siswa/dashboard');
  }

  //pengumuman

  public function pengumuman()
  {
    $pengumumanModel = new \App\Models\PengumumanModel();
    $data['pengumuman'] = $pengumumanModel->getPengumumanWithUser();
    return view('siswa/pengumuman', $data);
  }

  public function profil()
  {
    $userId = session()->get('user_id');

    // Ambil data siswa dengan JOIN untuk mendapatkan sekolah_id dari kelas
    $siswa = $this->siswaModel
      ->select('siswa.*, kelas.sekolah_id')
      ->join('kelas', 'kelas.kelas_id = siswa.kelas_id', 'left')
      ->where('siswa.user_id', $userId)
      ->first();

    $data = [
      'siswa' => $siswa,
      'sekolah' => $this->sekolahModel->findAll(),
      'kelas' => [], // Kosongkan karena akan di-load via AJAX
      'isNewUser' => !$this->siswaModel->checkSiswaExists($userId)
    ];

    return view('siswa/profil', $data);
  }

  //logic simpan profil/ ubah profil
  public function saveProfil()
  {
    $userId = session()->get('user_id');
    $data = [
      'user_id' => $userId,
      'nomor_peserta' => $this->request->getPost('nomor_peserta'),
      'nama_lengkap' => $this->request->getPost('nama_lengkap'),
      'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
      'kelas_id' => $this->request->getPost('kelas_id') // HAPUS sekolah_id
    ];

    $rules = [
      'nomor_peserta' => 'required|min_length[5]',
      'nama_lengkap' => 'required|min_length[3]',
      'jenis_kelamin' => 'required|in_list[Laki-laki,Perempuan]',
      'sekolah_id' => 'required|numeric', // Tetap validasi untuk form
      'kelas_id' => 'required|numeric'
    ];

    if (!$this->validate($rules)) {
      return redirect()->back()
        ->withInput()
        ->with('errors', $this->validator->getErrors());
    }

    // Cek apakah update atau insert
    $existingSiswa = $this->siswaModel->where('user_id', $userId)->first();

    try {
      if ($existingSiswa) {
        $this->siswaModel->update($existingSiswa['siswa_id'], $data);
        session()->setFlashdata('success', 'Profil berhasil diperbarui!');
      } else {
        $this->siswaModel->insert($data);
        session()->setFlashdata('success', 'Profil berhasil disimpan!');
      }
      return redirect()->to(base_url('siswa/profil'));
    } catch (\Exception $e) {
      log_message('error', $e->getMessage());
      return redirect()->back()
        ->withInput()
        ->with('error', 'Terjadi kesalahan saat menyimpan data.');
    }
  }

  // Method API baru untuk get kelas berdasarkan sekolah
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


  //Tampilan awal ujian
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


  //Cek and re check sebelum mulai ujian
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
        ujian.se_awal,
        ujian.se_minimum,
        ujian.delta_se_minimum,
        ujian.maksimal_soal_tampil,
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

    if (($ujianInfo['status'] ?? '') === 'selesai') {
      session()->setFlashdata('error', 'Jadwal ujian sudah tidak tersedia.');
      return redirect()->to(base_url('siswa/ujian'));
    }

    // 5. Cek apakah sudah terdaftar sebagai peserta
    $peserta = $this->pesertaUjianModel
      ->where('jadwal_id', $jadwalId)
      ->where('siswa_id', $siswa['siswa_id'])
      ->first();

    try {
      if (!$peserta) {
        // 6. Daftarkan sebagai peserta baru dengan pengecekan data
        $dataPeserta = [
          'jadwal_id' => $jadwalId,
          'siswa_id' => $siswa['siswa_id'],
          'status' => 'belum_mulai'
        ];

        // Debug data sebelum insert
        log_message('debug', 'Data peserta yang akan diinsert: ' . print_r($dataPeserta, true));

        $this->pesertaUjianModel->insert($dataPeserta);
      } elseif ($peserta['status'] === 'selesai') {
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

  //menu awal untuk ketika masuk ke soal
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
        ujian.se_awal,
        ujian.se_minimum,
        ujian.delta_se_minimum,
        ujian.maksimal_soal_tampil,
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
    $totalSoal = 0;
    if ($attempt) {
      $lastJawaban = $this->attemptJawabanModel
        ->where('attempt_id', $attempt['attempt_id'])
        ->orderBy('nomor_tampil', 'DESC')
        ->first();

      if (($ujianInfo['tipe_ujian'] ?? 'CAT') === 'CAT' && $attempt['status'] !== 'selesai') {
        $attempt['status'] = 'selesai';
        $attempt['waktu_selesai'] = date('Y-m-d H:i:s');
        $attempt['nilai_akhir'] = $attempt['nilai_akhir'] ?? ($lastJawaban['theta_saat_ini'] ?? 0);
        $this->attemptUjianModel->update($attempt['attempt_id'], [
          'status' => 'selesai',
          'waktu_selesai' => $attempt['waktu_selesai'],
          'nilai_akhir' => $attempt['nilai_akhir'],
        ]);
      }

      $nilaiAkhir = $attempt['nilai_akhir'] ?? ($lastJawaban['theta_saat_ini'] ?? 0);
      $totalSoal = $this->attemptJawabanModel->where('attempt_id', $attempt['attempt_id'])->countAllResults();
      session()->remove($this->getCatSessionKey((int) $attempt['attempt_id']));
      session()->remove($this->getCbtSessionKey((int) $attempt['attempt_id']));
    }

    $data = [
      'ujian' => $ujianInfo,
      'peserta' => $peserta,
      'nilai_akhir' => $nilaiAkhir,
      'total_soal' => $totalSoal
    ];

    return view('siswa/selesai_ujian', $data);
  }

  private function getSiswaWithSchoolByUser(int $userId): ?array
  {
    return $this->siswaModel
      ->select('siswa.*, kelas.sekolah_id')
      ->join('kelas', 'kelas.kelas_id = siswa.kelas_id', 'left')
      ->where('siswa.user_id', $userId)
      ->first();
  }

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

    if (($jadwal['tipe_penugasan'] ?? 'kelas') === 'individu') {
      $siswaIds = json_decode($jadwal['siswa_ids'] ?? '[]', true);
      if (!is_array($siswaIds)) {
        return false;
      }

      return in_array((int) ($siswa['siswa_id'] ?? 0), array_map('intval', $siswaIds), true);
    }

    return true;
  }

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

    if (!$paketId) {
      $paket = db_connect()->table('paket_ujian')
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

      if (!$this->buatSnapshotAttemptCbt($attempt, $paketId, $pesertaId)) {
        session()->setFlashdata('error', 'Gagal membuat snapshot soal CBT.');
        return redirect()->to(base_url('siswa/ujian'));
      }

      $this->pesertaUjianModel->update($pesertaId, [
        'status' => 'sedang_mengerjakan',
        'waktu_mulai' => date('Y-m-d H:i:s'),
      ]);
    }

    $sessionKey = $this->getCbtSessionKey((int) $attempt['attempt_id']);
    $cbtParams = session()->get($sessionKey);
    if (!$cbtParams || empty($cbtParams['attempt_soal_ids'])) {
      $snapshotSoal = $this->attemptSoalModel->getByAttempt($attempt['attempt_id']);
      if (empty($snapshotSoal)) {
        if (!$this->buatSnapshotAttemptCbt($attempt, $paketId, $pesertaId)) {
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

    $waktuMulai = $attempt['waktu_mulai'] ?: date('Y-m-d H:i:s');
    $durasi = explode(':', $ujianInfo['durasi']);
    $durasiDetik = ($durasi[0] * 3600) + ($durasi[1] * 60) + (isset($durasi[2]) ? $durasi[2] : 0);
    $sisaWaktu = (strtotime($waktuMulai) + $durasiDetik) - time();

    if ($sisaWaktu <= 0) {
      return redirect()->to(base_url("siswa/ujian/selesai/{$jadwalId}"));
    }

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

    if (!$this->attemptJawabanModel->where(['attempt_id' => $attempt['attempt_id'], 'soal_id' => $soalId])->first()) {
      $this->attemptJawabanModel->insert([
        'attempt_id' => $attempt['attempt_id'],
        'soal_id' => $soalId,
        'nomor_tampil' => $nomorTampil,
        'jawaban_siswa' => $jawaban,
        'is_correct' => $isBenar ? 1 : 0,
        'waktu_menjawab' => date('Y-m-d H:i:s'),
      ]);

      $this->hasilUjianModel->insert([
        'peserta_ujian_id' => $peserta['peserta_ujian_id'],
        'soal_id' => $soalId,
        'jawaban_siswa' => $jawaban,
        'is_correct' => $isBenar ? 1 : 0,
        'theta_saat_ini' => 0,
        'pi_saat_ini' => 0,
        'qi_saat_ini' => 0,
        'ii_saat_ini' => 0,
        'se_saat_ini' => 0,
        'delta_se_saat_ini' => 0,
      ]);
    }

    $cbtParams['current_index']++;
    if ($cbtParams['current_index'] >= count($cbtParams['attempt_soal_ids'])) {
      $benar = $this->attemptJawabanModel->countCorrect($attempt['attempt_id']);
      $nilai = count($cbtParams['attempt_soal_ids']) > 0 ? ($benar / count($cbtParams['attempt_soal_ids'])) * 100 : 0;

      $this->attemptUjianModel->update($attempt['attempt_id'], [
        'status' => 'selesai',
        'waktu_selesai' => date('Y-m-d H:i:s'),
        'nilai_akhir' => $nilai,
      ]);
      $this->pesertaUjianModel->update($peserta['peserta_ujian_id'], [
        'status' => 'selesai',
        'waktu_selesai' => date('Y-m-d H:i:s'),
      ]);
      session()->remove($sessionKey);

      return redirect()->to(base_url("siswa/ujian/selesai/{$ujianInfo['jadwal_id']}"));
    }

    session()->set($sessionKey, $cbtParams);
    return redirect()->to(base_url('siswa/ujian/soal/' . $ujianInfo['jadwal_id']));
  }

  private function buatSnapshotAttemptCbt(array $attempt, $paketId, $pesertaId)
  {
    if ($this->attemptSoalModel->where('attempt_id', $attempt['attempt_id'])->countAllResults() > 0) {
      return true;
    }

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

    $soals = $this->paketUjianModel->getSoalByPaket($paketId, true);
    if (empty($soals)) {
      return false;
    }

    $rows = [];
    foreach ($soals as $index => $soal) {
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
        'pembahasan' => $soal['pembahasan'] ?? null,
        'media' => $soal['media'] ?? ($soal['foto'] ?? null),
        'created_at' => date('Y-m-d H:i:s'),
      ];
    }

    return (bool) $this->attemptSoalModel->insertBatch($rows);
  }

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

  private function soalCat($jadwalId, array $ujianInfo, array $peserta)
  {
    if ($peserta['status'] === 'selesai') {
      session()->setFlashdata('error', 'Anda sudah menyelesaikan attempt ini');
      return redirect()->to(base_url('siswa/ujian'));
    }

    $pesertaId = (int) $peserta['peserta_ujian_id'];
    $attempt = $this->attemptUjianModel->getActiveAttempt($pesertaId);
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

      if (!$this->buatSnapshotAttemptCat($attempt, (int) $ujianInfo['id_ujian'], $pesertaId)) {
        session()->setFlashdata('error', 'Gagal membuat snapshot soal CAT.');
        return redirect()->to(base_url('siswa/ujian'));
      }

      $this->pesertaUjianModel->update($pesertaId, [
        'status' => 'sedang_mengerjakan',
        'waktu_mulai' => $attempt['waktu_mulai'],
      ]);
    }

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
      $b = (float) ($soal['tingkat_kesulitan'] ?? 0);

      $e = 2.71828;
      $Pi = pow($e, ($theta - $b)) / (1 + pow($e, ($theta - $b)));
      $Qi = 1 - $Pi;
      $Ii = $Pi * $Qi;

      $totalIi = 0;
      foreach (($catParams['answered_questions'] ?? []) as $answeredAttemptSoalId) {
        $answeredSoal = $this->attemptSoalModel->find((int) $answeredAttemptSoalId);
        if (!$answeredSoal) {
          continue;
        }

        $bi = (float) ($answeredSoal['tingkat_kesulitan'] ?? 0);
        $PiAnswered = pow($e, ($theta - $bi)) / (1 + pow($e, ($theta - $bi)));
        $QiAnswered = 1 - $PiAnswered;
        $totalIi += ($PiAnswered * $QiAnswered);
      }

      $totalIi += $Ii;
      $SEOld = (float) ($catParams['SE'] ?? 1);
      $SENew = $totalIi > 0 ? (1 / sqrt($totalIi)) : $SEOld;
      $deltaSE = $SEOld - $SENew;

      $theta = $b;
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
      $catParams['current_question'] = $nextQuestion ? $this->formatSnapshotSoalForView($nextQuestion) : null;
      $catParams['total_questions'] = count($answeredQuestions);

      $nomorTampil = count($answeredQuestions);
      if (!$this->attemptJawabanModel->where(['attempt_id' => $attempt['attempt_id'], 'soal_id' => $soalId])->first()) {
        $this->attemptJawabanModel->insert([
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

        $this->hasilUjianModel->insert([
          'peserta_ujian_id' => $peserta['peserta_ujian_id'],
          'soal_id' => $soalId,
          'jawaban_siswa' => $jawaban,
          'is_correct' => $isBenar ? 1 : 0,
          'theta_saat_ini' => $theta,
          'pi_saat_ini' => $Pi,
          'qi_saat_ini' => $Qi,
          'ii_saat_ini' => $Ii,
          'se_saat_ini' => $SENew,
          'delta_se_saat_ini' => $deltaSE,
        ]);
      }

      $shouldStop = false;
      if ($SENew < (float) $ujianInfo['se_minimum']) {
        $shouldStop = true;
      } elseif (abs($deltaSE) < (float) $ujianInfo['delta_se_minimum']) {
        $shouldStop = true;
      } elseif (!$nextQuestion) {
        $shouldStop = true;
      }

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

  private function buatSnapshotAttemptCat(array $attempt, int $ujianId, int $pesertaId): bool
  {
    if ($this->attemptSoalModel->where('attempt_id', $attempt['attempt_id'])->countAllResults() > 0) {
      return true;
    }

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

    $pool = $this->ujianSoalCatModel->getSoalByUjian($ujianId);
    if (empty($pool)) {
      return false;
    }

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

    $jawabanRows = $this->attemptJawabanModel
      ->where('attempt_id', $attempt['attempt_id'])
      ->orderBy('nomor_tampil', 'ASC')
      ->findAll();

    $answeredAttemptSoalIds = [];
    $theta = 0;
    $SE = 1;

    foreach ($jawabanRows as $jawaban) {
      foreach ($snapshotSoal as $snapshot) {
        if ((int) $snapshot['original_soal_id'] === (int) $jawaban['soal_id']) {
          $answeredAttemptSoalIds[] = (int) $snapshot['attempt_soal_id'];
          break;
        }
      }
      $theta = (float) ($jawaban['theta_saat_ini'] ?? $theta);
      $SE = (float) ($jawaban['se_saat_ini'] ?? $SE);
    }

    $currentQuestion = null;
    if (empty($jawabanRows)) {
      $currentQuestion = $this->pilihSoalAwalCat((int) $attempt['attempt_id']);
    } else {
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
      'attempt_id' => $attempt['attempt_id'],
      'theta' => $theta,
      'SE' => $SE,
      'answered_questions' => $answeredAttemptSoalIds,
      'current_question' => $currentQuestion ? $this->formatSnapshotSoalForView($currentQuestion) : null,
      'total_questions' => count($answeredAttemptSoalIds),
    ];
  }

  private function getCatSessionKey(int $attemptId): string
  {
    return 'cat_attempt_' . $attemptId;
  }

  private function getCbtSessionKey(int $attemptId): string
  {
    return 'cbt_attempt_' . $attemptId;
  }

  private function hitungDurasiPerSoal($detailJawaban, $waktuMulaiUjian)
  {
    $hasilDenganDurasi = [];
    $waktuSebelumnya = $waktuMulaiUjian;

    foreach ($detailJawaban as $index => $jawaban) {
      $waktuMenjawab = $jawaban['waktu_menjawab'];

      // Hitung durasi dalam detik
      $durasiDetik = strtotime($waktuMenjawab) - strtotime($waktuSebelumnya);

      // Konversi ke menit dan detik
      $menit = floor($durasiDetik / 60);
      $detik = $durasiDetik % 60;

      $jawaban['durasi_pengerjaan_detik'] = $durasiDetik;
      $jawaban['durasi_pengerjaan_format'] = sprintf('%d menit %d detik', $menit, $detik);
      $jawaban['nomor_soal'] = $index + 1;

      $hasilDenganDurasi[] = $jawaban;
      $waktuSebelumnya = $waktuMenjawab;
    }

    return $hasilDenganDurasi;
  }

  private function pilihSoalAwalCat(int $attemptId): ?array
  {
    $pool = $this->attemptSoalModel->getByAttempt($attemptId);
    if (empty($pool)) {
      return null;
    }

    usort($pool, static function (array $a, array $b): int {
      $distanceA = abs((float) ($a['tingkat_kesulitan'] ?? 0));
      $distanceB = abs((float) ($b['tingkat_kesulitan'] ?? 0));
      if ($distanceA === $distanceB) {
        return strcmp((string) ($b['created_at'] ?? ''), (string) ($a['created_at'] ?? ''));
      }
      return $distanceA <=> $distanceB;
    });

    return $pool[0] ?? null;
  }

  private function pilihSoalBerikutnyaCat(int $attemptId, float $currentDifficulty, array $answeredIds, bool $naik): ?array
  {
    $answeredLookup = array_map('intval', $answeredIds);
    $pool = array_filter(
      $this->attemptSoalModel->getByAttempt($attemptId),
      static function (array $row) use ($answeredLookup, $currentDifficulty, $naik): bool {
        $soalId = (int) ($row['attempt_soal_id'] ?? 0);
        $difficulty = (float) ($row['tingkat_kesulitan'] ?? 0);
        if (in_array($soalId, $answeredLookup, true)) {
          return false;
        }

        return $naik ? $difficulty > $currentDifficulty : $difficulty < $currentDifficulty;
      }
    );

    if (empty($pool)) {
      return null;
    }

    usort($pool, static function (array $a, array $b) use ($naik): int {
      $difficultyA = (float) ($a['tingkat_kesulitan'] ?? 0);
      $difficultyB = (float) ($b['tingkat_kesulitan'] ?? 0);
      return $naik ? ($difficultyA <=> $difficultyB) : ($difficultyB <=> $difficultyA);
    });

    return array_values($pool)[0] ?? null;
  }

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
      $rows = $db->table('attempt_jawaban aj')
        ->select('
          aj.*,
          COALESCE(ats.pertanyaan, su.pertanyaan) as pertanyaan,
          COALESCE(ats.kode_soal, su.kode_soal) as kode_soal,
          COALESCE(ats.jawaban_benar, su.jawaban_benar) as jawaban_benar,
          COALESCE(ats.tingkat_kesulitan, su.tingkat_kesulitan) as tingkat_kesulitan,
          COALESCE(ats.pembahasan, su.pembahasan) as pembahasan,
          COALESCE(ats.media, su.media) as foto,
          DATE_FORMAT(aj.waktu_menjawab, "%H:%i:%s") as waktu_menjawab_format
        ')
        ->join('attempt_soal ats', 'ats.attempt_id = aj.attempt_id AND ats.original_soal_id = aj.soal_id', 'left')
        ->join('soal_ujian su', 'su.soal_id = aj.soal_id', 'left')
        ->where('aj.attempt_id', $attempt['attempt_id'])
        ->orderBy('aj.nomor_tampil', 'ASC')
        ->orderBy('aj.waktu_menjawab', 'ASC')
        ->get()
        ->getResultArray();

      if (!empty($rows)) {
        return $rows;
      }
    }

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

    $groupedRiwayat = [];
    foreach ($rows as $ujian) {
      $jumlahSoal = $this->attemptJawabanModel
        ->where('attempt_id', $ujian['attempt_id'])
        ->countAllResults();
      $ujian['jumlah_soal'] = $jumlahSoal;

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
      $attempt['jumlah_soal'] = $this->attemptJawabanModel
        ->where('attempt_id', $attempt['attempt_id'])
        ->countAllResults();

      if ($attempt['durasi_detik']) {
        $jam = floor($attempt['durasi_detik'] / 3600);
        $menit = floor(($attempt['durasi_detik'] % 3600) / 60);
        $detik = $attempt['durasi_detik'] % 60;
        $attempt['durasi_format'] = sprintf('%02d:%02d:%02d', $jam, $menit, $detik);
      } else {
        $attempt['durasi_format'] = '00:00:00';
      }

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

  private function hitungKemampuanKognitif($theta)
  {
    // Rumus baru: skor akhir siswa (x) = 50 + (16.67 * tetha)
    $skor_akhir = 50 + (16.67 * $theta);

    // Pastikan skor tidak negatif
    $skor_akhir = max(0, $skor_akhir);

    // Mengembalikan skor yang sudah dibulatkan
    return round($skor_akhir, 2);
  }

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

    $data = [
      'hasil' => $hasil,
      'detailJawaban' => $detailJawabanDenganDurasi,
      'totalSoal' => $totalSoal,
      'jawabanBenar' => $jawabanBenar,
      'skor' => $skor_akhir, // Kirim skor ke view
      'kemampuanKognitif' => $kemampuanKognitif, // Data kemampuan kognitif
      'klasifikasiKognitif' => $klasifikasiKognitif, // Klasifikasi kognitif
      'rataRataWaktuFormat' => sprintf('%d menit %d detik', $rataRataMenit, $rataRataDetik),
      'statistikWaktu' => [
        'waktu_tercepat' => $totalSoal > 0 ? min(array_column($detailJawabanDenganDurasi, 'durasi_pengerjaan_detik')) : 0,
        'waktu_terlama' => $totalSoal > 0 ? max(array_column($detailJawabanDenganDurasi, 'durasi_pengerjaan_detik')) : 0,
        'rata_rata' => $rataRataWaktu
      ]
    ];

    return view('siswa/detail_hasil', $data);
  }

  //function unduh hasil ujian

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
