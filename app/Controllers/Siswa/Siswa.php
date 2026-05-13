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


class Siswa extends Controller
{
  protected $jadwalUjianModel;
  protected $pesertaUjianModel;
  protected $siswaModel;
  protected $kelasModel;
  protected $soalUjianModel;
  protected $hasilUjianModel;
  protected $sekolahModel;

  public function __construct()
  {
    $this->jadwalUjianModel = new JadwalUjianModel();
    $this->pesertaUjianModel = new PesertaUjianModel();
    $this->siswaModel = new SiswaModel();
    $this->kelasModel = new KelasModel();
    $this->soalUjianModel = new SoalUjianModel();
    $this->hasilUjianModel = new HasilUjianModel();
    $this->sekolahModel = new SekolahModel();
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
    $siswa = $this->siswaModel->where('user_id', $userId)->first();

    //kalo belum isi profil, arakan ke profil
    if (!$siswa) {
      session()->setFlashdata('error', 'Silahkan lengkapi profil Anda terlebih dahulu');
      return redirect()->to(base_url('siswa/profil'));
    }

    //gabungkan data jadwal ujian dengan status peserta
    $jadwalUjian = $this->jadwalUjianModel
      ->select('jadwal_ujian.*, ujian.nama_ujian, ujian.kode_ujian, ujian.deskripsi, ujian.durasi, peserta_ujian.status as status_peserta')
      ->join('ujian', 'ujian.id_ujian = jadwal_ujian.ujian_id')
      ->join('peserta_ujian', 'peserta_ujian.jadwal_id = jadwal_ujian.jadwal_id AND peserta_ujian.siswa_id = ' . $siswa['siswa_id'], 'left')
      ->where('jadwal_ujian.kelas_id', $siswa['kelas_id'])
      ->where('jadwal_ujian.tanggal_selesai >=', date('Y-m-d H:i:s'))
      ->where('jadwal_ujian.status !=', 'selesai')
      ->findAll();

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
    $siswa = $this->siswaModel->where('user_id', $userId)->first();

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
    $siswa = $this->siswaModel->where('user_id', $userId)->first();

    if (!$siswa) {
      session()->setFlashdata('error', 'Data siswa tidak ditemukan');
      return redirect()->to(base_url('siswa/profil'));
    }

    // 3. Ambil informasi ujian dan jadwal di awal
    $ujianInfo = $this->jadwalUjianModel
      ->select('jadwal_ujian.*, ujian.*, ujian.kode_ujian, jenis_ujian.nama_jenis')
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

    if ($peserta['status'] === 'selesai') {
      session()->setFlashdata('error', 'Anda sudah menyelesaikan ujian ini');
      return redirect()->to(base_url('siswa/ujian'));
    }

    // 5. Set parameter awal jika baru mulai
    if ($peserta['status'] === 'belum_mulai') {
      // Set waktu mulai
      $waktuMulai = date('Y-m-d H:i:s');

      $this->pesertaUjianModel->update($peserta['peserta_ujian_id'], [
        'status' => 'sedang_mengerjakan',
        'waktu_mulai' => $waktuMulai
      ]);

      $catParams = [
        'theta' => 0,
        'SE' => 1,
        'answered_questions' => [],
        'current_question' => null,
        'total_questions' => 0
      ];
      session()->set('cat_params', $catParams);
    } else {
      $waktuMulai = $peserta['waktu_mulai'];
    }

    // 6. Ambil CAT params dari session dengan validasi
    $catParams = session()->get('cat_params');

    // Jika cat_params belum ada atau null, inisialisasi dengan nilai default
    if (!$catParams) {
      $catParams = [
        'theta' => 0,
        'SE' => 1,
        'answered_questions' => [],
        'current_question' => null,
        'total_questions' => 0
      ];
      session()->set('cat_params', $catParams);
    }

    // 7. Pilih soal berikutnya jika belum ada
    if (!isset($catParams['current_question']) || $catParams['current_question'] === null) {
      // Untuk soal pertama, cari yang paling dekat dengan 0
      $nextQuestion = $this->soalUjianModel
        ->select('*, kode_soal, ABS(tingkat_kesulitan - 0) as distance')  // Hitung jarak dari 0
        ->where('ujian_id', $ujianInfo['id_ujian'])
        ->orderBy('distance', 'ASC')  // Urutkan berdasarkan jarak terdekat dengan 0
        ->first();

      if ($nextQuestion) {
        $catParams['current_question'] = $nextQuestion;
        session()->set('cat_params', $catParams);
      } else {
        session()->setFlashdata('error', 'Tidak ada soal yang tersedia');
        return redirect()->to(base_url('siswa/ujian'));
      }
    }

    // 8. Hitung sisa waktu
    if (!$waktuMulai) {
      // Jika waktu_mulai belum ada, set waktu sekarang
      $waktuMulai = date('Y-m-d H:i:s');
      $this->pesertaUjianModel->update($peserta['peserta_ujian_id'], [
        'waktu_mulai' => $waktuMulai
      ]);
    }

    // Konversi durasi dari format HH:MM:SS ke detik
    $durasi = explode(':', $ujianInfo['durasi']);
    $durasiDetik = ($durasi[0] * 3600) + ($durasi[1] * 60) + (isset($durasi[2]) ? $durasi[2] : 0);

    // Hitung sisa waktu
    $waktuMulaiTimestamp = strtotime($waktuMulai);
    $waktuSelesai = $waktuMulaiTimestamp + $durasiDetik;
    $sisaWaktu = $waktuSelesai - time();

    // Jika waktu sudah habis, arahkan ke halaman selesai
    if ($sisaWaktu <= 0) {
      // Update status peserta
      $this->pesertaUjianModel->update($peserta['peserta_ujian_id'], [
        'status' => 'selesai',
        'waktu_selesai' => date('Y-m-d H:i:s')
      ]);

      // Hapus session CAT
      session()->remove('cat_params');

      return redirect()->to(base_url("siswa/ujian/selesai/{$jadwalId}"));
    }


    // 9. Siapkan data untuk view
    $data = [
      'ujian' => $ujianInfo,
      'soal' => $catParams['current_question'],
      'sisa_waktu' => $sisaWaktu,
      'total_soal' => 'Adaptif',
      'soal_dijawab' => count($catParams['answered_questions'])
    ];

    // 10. Tampilkan view
    return view('siswa/soal', $data);
  }

  public function simpanJawaban()
  {
    // Debug untuk melihat input
    log_message('debug', 'POST Data: ' . print_r($this->request->getPost(), true));

    // 1. Validasi input
    $soalId = $this->request->getPost('soal_id');
    $jawaban = $this->request->getPost('jawaban');

    if (!$soalId || !$jawaban) {
      session()->setFlashdata('error', 'Data jawaban tidak lengkap');
      return redirect()->back();
    }

    // 2. Ambil data soal dengan validasi
    $soal = $this->soalUjianModel->find($soalId);
    if (!$soal) {
      session()->setFlashdata('error', 'Soal tidak ditemukan');
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

    // 3. Ambil CAT params dari session dengan validasi
    $catParams = session()->get('cat_params');
    if (!$catParams) {
      session()->setFlashdata('error', 'Parameter ujian tidak ditemukan');
      return redirect()->to(base_url('siswa/ujian'));
    }

    // 4. Cek jawaban
    $isBenar = ($jawaban === $soal['jawaban_benar']);

    try {
      // 5. Hitung parameter CAT
      $theta = $catParams['theta'];
      $b = $soal['tingkat_kesulitan'];

      // 6. Hitung probabilitas
      $e = 2.71828;
      $Pi = pow($e, ($theta - $b)) / (1 + pow($e, ($theta - $b)));
      $Qi = 1 - $Pi;
      $Ii = $Pi * $Qi; // Fungsi informasi untuk soal saat ini

      // 7. Hitung fungsi informasi
      $totalIi = 0;
      foreach ($catParams['answered_questions'] as $answeredSoalId) {
        $answeredSoal = $this->soalUjianModel->find($answeredSoalId);
        $bi = $answeredSoal['tingkat_kesulitan'];

        // Hitung Pi dan Qi untuk setiap soal yang sudah dijawab
        $Pi_answered = pow($e, ($theta - $bi)) / (1 + pow($e, ($theta - $bi)));
        $Qi_answered = 1 - $Pi_answered;

        // Tambahkan ke total informasi
        $totalIi += ($Pi_answered * $Qi_answered);
      }

      // Tambahkan informasi soal saat ini
      $totalIi += ($Pi * $Qi);

      // 8. Hitung SE baru
      $SE_old = $catParams['SE'];
      $SE_new = 1 / sqrt($totalIi);
      $delta_SE = $SE_old - $SE_new;

      // Debug info
      log_message('debug', 'Total Information: ' . $totalIi);
      log_message('debug', 'SE_new: ' . $SE_new);
      log_message('debug', 'Delta SE: ' . $delta_SE);

      // 9. Pilih soal berikutnya berdasarkan jawaban
      if ($isBenar) {
        $theta = $b;
        //didapat dari tetha = bi + 1/D.alpha ln(0,5(1 + akar (1 + 8c)))
        //karena logistic 1 PL, maka aplha = 1, D=1.7, c=0
        //ln(1) = 0
        //maka tetha = bi

        // Jika benar, cari soal lebih sulit
        $nextQuestion = $this->soalUjianModel
          ->select('*, kode_soal, ABS(tingkat_kesulitan - ' . ($b + 0.01) . ') as distance')
          ->where('ujian_id', $soal['ujian_id'])
          ->where('tingkat_kesulitan >', $b);

        if (!empty($catParams['answered_questions'])) {
          $nextQuestion->whereNotIn('soal_id', $catParams['answered_questions']);
        }

        $nextQuestion = $nextQuestion->orderBy('tingkat_kesulitan', 'ASC')
          ->first();
      } else {
        // Jika salah, update theta dan cari soal lebih mudah
        $theta = $b;
        $nextQuestion = $this->soalUjianModel
          ->select('*, kode_soal, ABS(tingkat_kesulitan - ' . ($b - 0.01) . ') as distance')
          ->where('ujian_id', $soal['ujian_id'])
          ->where('tingkat_kesulitan <', $b);

        if (!empty($catParams['answered_questions'])) {
          $nextQuestion->whereNotIn('soal_id', $catParams['answered_questions']);
        }

        $nextQuestion = $nextQuestion->orderBy('tingkat_kesulitan', 'DESC')
          ->first();
      }

      // Debug info sebelum update
      log_message('debug', 'Total Questions before: ' . $catParams['total_questions']);
      // log_message('debug', 'Maksimal Soal: ' . $ujianInfo['maksimal_soal_tampil']);

      // Update CAT parameters
      $catParams['theta'] = $theta;
      $catParams['SE'] = $SE_new;
      if (!in_array($soalId, $catParams['answered_questions'])) {
        $catParams['answered_questions'][] = $soalId;
      }
      $catParams['current_question'] = $nextQuestion;
      $catParams['total_questions'] = count($catParams['answered_questions']);

      // Debug info setelah update
      log_message('debug', 'Total Questions after: ' . $catParams['total_questions']);

      // Cek kondisi berhenti dengan lebih ketat
      $shouldStop = false;

      // 1. Cek maksimal soal
      // if ($catParams['total_questions'] >= (int)$ujianInfo['maksimal_soal_tampil']) {
      //   log_message('debug', 'Stopping: Reached max questions');
      //   $shouldStop = true;
      // }


      // 2. Cek SE target
      if ($SE_new < (float)$ujianInfo['se_minimum']) {
        log_message('debug', 'Stopping: SE below minimum');
        $shouldStop = true;
      }

      // 3. Cek Delta SE
      else if (abs($delta_SE) < (float)$ujianInfo['delta_se_minimum']) {
        log_message('debug', 'Stopping: Delta SE below minimum');
        $shouldStop = true;
      }

      // 4. Cek waktu
      else if (!$nextQuestion) {
        log_message('debug', 'Stopping: No more questions');
        $shouldStop = true;
      }

      // Update session dengan parameter terbaru
      session()->set('cat_params', $catParams);

      //simpan jawaban ke database
      try {
        // Ambil peserta_ujian_id
        $siswaId = $this->siswaModel->where('user_id', session()->get('user_id'))->first()['siswa_id'];
        $peserta = $this->pesertaUjianModel
          ->where('jadwal_id', $ujianInfo['jadwal_id'])
          ->where('siswa_id', $siswaId)
          ->first();

        // Simpan hasil jawaban ke tabel hasil_ujian
        $dataHasil = [
          'peserta_ujian_id' => $peserta['peserta_ujian_id'],
          'soal_id' => $soalId,
          'jawaban_siswa' => $jawaban,
          'is_correct' => $isBenar,
          'theta_saat_ini' => $theta,
          'pi_saat_ini' => $Pi,
          'qi_saat_ini' => $Qi,
          'ii_saat_ini' => $Ii,
          'se_saat_ini' => $SE_new,
          'delta_se_saat_ini' => $delta_SE
        ];

        // Debug info
        log_message('debug', 'Saving hasil ujian: ' . print_r($dataHasil, true));

        $this->hasilUjianModel->insert($dataHasil);
      } catch (\Exception $e) {
        log_message('error', 'Error saving hasil ujian: ' . $e->getMessage());
      }

      if ($shouldStop) {
        // Update status peserta menjadi selesai
        $siswaId = $this->siswaModel->where('user_id', session()->get('user_id'))->first()['siswa_id'];

        $peserta = $this->pesertaUjianModel
          ->where('jadwal_id', $ujianInfo['jadwal_id'])
          ->where('siswa_id', $siswaId)
          ->first();

        if ($peserta) {
          $this->pesertaUjianModel->update($peserta['peserta_ujian_id'], [
            'status' => 'selesai',
            'waktu_selesai' => date('Y-m-d H:i:s')
          ]);
        }

        return redirect()->to(base_url("siswa/ujian/selesai/{$ujianInfo['jadwal_id']}"));
      }

      // Lanjut ke soal berikutnya
      return redirect()->back();
    } catch (\Exception $e) {
      log_message('error', 'Error saat memproses jawaban: ' . $e->getMessage());
      session()->setFlashdata('error', 'Terjadi kesalahan saat memproses jawaban');
      return redirect()->back();
    }
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
      ->select('jadwal_ujian.*, ujian.nama_ujian, ujian.deskripsi')
      ->join('ujian', 'ujian.id_ujian = jadwal_ujian.ujian_id')
      ->where('jadwal_ujian.jadwal_id', $jadwalId)
      ->first();

    // 4. Hitung nilai akhir dari CAT params
    $catParams = session()->get('cat_params');
    $nilaiAkhir = $catParams ? $catParams['theta'] : 0;

    // 5. Clear session CAT
    session()->remove('cat_params');

    $data = [
      'ujian' => $ujianInfo,
      'peserta' => $peserta,
      'nilai_akhir' => $nilaiAkhir,
      'total_soal' => count($catParams['answered_questions'])
    ];

    return view('siswa/selesai_ujian', $data);
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


  public function hasil()
  {
    if (!session()->get('user_id')) {
      return redirect()->to(base_url('login'));
    }

    $userId = session()->get('user_id');
    $siswa = $this->siswaModel->where('user_id', $userId)->first();

    // Tambahkan lebih banyak informasi waktu
    $riwayatUjian = $this->pesertaUjianModel
      ->select('
            peserta_ujian.*, 
            jadwal_ujian.*, 
            ujian.nama_ujian, 
            ujian.kode_ujian,
            ujian.deskripsi, 
            ujian.durasi,
            jenis_ujian.nama_jenis,
            TIMEDIFF(peserta_ujian.waktu_selesai, peserta_ujian.waktu_mulai) as durasi_pengerjaan,
            TIME_TO_SEC(TIMEDIFF(peserta_ujian.waktu_selesai, peserta_ujian.waktu_mulai)) as durasi_detik,
            DATE_FORMAT(peserta_ujian.waktu_mulai, "%d/%m/%Y %H:%i:%s") as waktu_mulai_format,
            DATE_FORMAT(peserta_ujian.waktu_selesai, "%d/%m/%Y %H:%i:%s") as waktu_selesai_format
        ')
      ->join('jadwal_ujian', 'jadwal_ujian.jadwal_id = peserta_ujian.jadwal_id')
      ->join('ujian', 'ujian.id_ujian = jadwal_ujian.ujian_id')
      ->join('jenis_ujian', 'jenis_ujian.jenis_ujian_id = ujian.jenis_ujian_id')
      ->where('peserta_ujian.siswa_id', $siswa['siswa_id'])
      ->where('peserta_ujian.status', 'selesai')
      ->orderBy('peserta_ujian.waktu_selesai', 'DESC')
      ->findAll();


    // Tambahkan informasi jumlah soal untuk setiap ujian
    foreach ($riwayatUjian as &$ujian) {
      $jumlahSoal = $this->hasilUjianModel
        ->where('peserta_ujian_id', $ujian['peserta_ujian_id'])
        ->countAllResults();
      $ujian['jumlah_soal'] = $jumlahSoal;

      // Format durasi menjadi jam:menit:detik
      if ($ujian['durasi_detik']) {
        $jam = floor($ujian['durasi_detik'] / 3600);
        $menit = floor(($ujian['durasi_detik'] % 3600) / 60);
        $detik = $ujian['durasi_detik'] % 60;
        $ujian['durasi_format'] = sprintf('%02d:%02d:%02d', $jam, $menit, $detik);
      }
    }

    $data = [
      'riwayatUjian' => $riwayatUjian
    ];

    return view('siswa/hasil', $data);
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


  public function detailHasil($pesertaUjianId)
  {
    if (!session()->get('user_id')) {
      return redirect()->to(base_url('login'));
    }

    // Ambil detail hasil ujian dengan informasi waktu
    $hasil = $this->pesertaUjianModel
      ->select('
            peserta_ujian.*, 
            jadwal_ujian.*, 
            ujian.*, 
            ujian.kode_ujian,
            jenis_ujian.nama_jenis,
            TIMEDIFF(peserta_ujian.waktu_selesai, peserta_ujian.waktu_mulai) as durasi_total,
            TIME_TO_SEC(TIMEDIFF(peserta_ujian.waktu_selesai, peserta_ujian.waktu_mulai)) as durasi_total_detik,
            DATE_FORMAT(peserta_ujian.waktu_mulai, "%d/%m/%Y %H:%i:%s") as waktu_mulai_format,
            DATE_FORMAT(peserta_ujian.waktu_selesai, "%d/%m/%Y %H:%i:%s") as waktu_selesai_format
        ')
      ->join('jadwal_ujian', 'jadwal_ujian.jadwal_id = peserta_ujian.jadwal_id')
      ->join('ujian', 'ujian.id_ujian = jadwal_ujian.ujian_id')
      ->join('jenis_ujian', 'jenis_ujian.jenis_ujian_id = ujian.jenis_ujian_id')
      ->where('peserta_ujian.peserta_ujian_id', $pesertaUjianId)
      ->first();

    // Ambil detail jawaban dengan waktu
    $detailJawaban = $this->hasilUjianModel
      ->select('
            hasil_ujian.*, 
            soal_ujian.pertanyaan, 
            soal_ujian.kode_soal,
            soal_ujian.jawaban_benar, 
            soal_ujian.tingkat_kesulitan, 
            soal_ujian.pembahasan,
            DATE_FORMAT(hasil_ujian.waktu_menjawab, "%H:%i:%s") as waktu_menjawab_format
        ')
      ->join('soal_ujian', 'soal_ujian.soal_id = hasil_ujian.soal_id')
      ->where('hasil_ujian.peserta_ujian_id', $pesertaUjianId)
      ->orderBy('hasil_ujian.waktu_menjawab', 'ASC')
      ->findAll();

    // Hitung durasi per soal
    $detailJawabanDenganDurasi = $this->hitungDurasiPerSoal($detailJawaban, $hasil['waktu_mulai']);

    // Hitung statistik tambahan
    $totalSoal = count($detailJawabanDenganDurasi);
    $jawabanBenar = array_reduce($detailJawabanDenganDurasi, function ($carry, $item) {
      return $carry + ($item['is_correct'] ? 1 : 0);
    }, 0);

    $lastResult = $this->hasilUjianModel
      ->select('theta_saat_ini, se_saat_ini')
      ->where('peserta_ujian_id', $pesertaUjianId)
      ->orderBy('waktu_menjawab', 'DESC')
      ->limit(1)
      ->first();

    $theta_akhir = $lastResult ? (float)$lastResult['theta_saat_ini'] : 0;

    // 1. Hitung skor akhir menggunakan fungsi yang sudah diubah
    $skor_akhir = $this->hitungKemampuanKognitif($theta_akhir);

    // 2. Dapatkan klasifikasi berdasarkan skor akhir
    $klasifikasiKognitif = $this->getKlasifikasiKognitif($skor_akhir);

    // 3. Siapkan array data kognitif untuk dikirim ke view.
    //    Ini menggantikan array lama yang dihasilkan oleh fungsi hitungKemampuanKognitif yang lama.
    $kemampuanKognitif = [
      'skor' => $skor_akhir,
      'total_benar' => $jawabanBenar,
      'total_salah' => $totalSoal - $jawabanBenar,
      // Rata-rata pilihan tidak lagi relevan untuk rumus baru, namun bisa tetap dikirim agar tidak error di view.
      'rata_rata_pilihan' => 0
    ];

    // Hitung rata-rata waktu per soal
    $rataRataWaktu = $hasil['durasi_total_detik'] / $totalSoal;
    $rataRataMenit = floor($rataRataWaktu / 60);
    $rataRataDetik = $rataRataWaktu % 60;

    // Format durasi total
    if ($hasil['durasi_total_detik']) {
      $jam = floor($hasil['durasi_total_detik'] / 3600);
      $menit = floor(($hasil['durasi_total_detik'] % 3600) / 60);
      $detik = $hasil['durasi_total_detik'] % 60;
      $hasil['durasi_total_format'] = sprintf('%02d:%02d:%02d', $jam, $menit, $detik);
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
        'waktu_tercepat' => min(array_column($detailJawabanDenganDurasi, 'durasi_pengerjaan_detik')),
        'waktu_terlama' => max(array_column($detailJawabanDenganDurasi, 'durasi_pengerjaan_detik')),
        'rata_rata' => $rataRataWaktu
      ]
    ];

    return view('siswa/detail_hasil', $data);
  }

  //function unduh hasil ujian

  public function unduh($pesertaUjianId)
  {
    if (!session()->get('user_id')) {
      return redirect()->to(base_url('login'));
    }

    // Verifikasi akses
    $userId = session()->get('user_id');
    $siswa = $this->siswaModel->where('user_id', $userId)->first();

    // Ambil detail hasil ujian dengan informasi waktu lengkap
    $hasil = $this->pesertaUjianModel
      ->select('
            peserta_ujian.*, 
            jadwal_ujian.*, 
            ujian.*, 
            ujian.kode_ujian,
            jenis_ujian.nama_jenis,
            TIMEDIFF(peserta_ujian.waktu_selesai, peserta_ujian.waktu_mulai) as durasi_total,
            TIME_TO_SEC(TIMEDIFF(peserta_ujian.waktu_selesai, peserta_ujian.waktu_mulai)) as durasi_total_detik,
            DATE_FORMAT(peserta_ujian.waktu_mulai, "%d/%m/%Y %H:%i:%s") as waktu_mulai_format,
            DATE_FORMAT(peserta_ujian.waktu_selesai, "%d/%m/%Y %H:%i:%s") as waktu_selesai_format
        ')
      ->join('jadwal_ujian', 'jadwal_ujian.jadwal_id = peserta_ujian.jadwal_id')
      ->join('ujian', 'ujian.id_ujian = jadwal_ujian.ujian_id')
      ->join('jenis_ujian', 'jenis_ujian.jenis_ujian_id = ujian.jenis_ujian_id')
      ->where('peserta_ujian.peserta_ujian_id', $pesertaUjianId)
      ->first();

    // Verifikasi hak akses
    if (!$hasil || $hasil['siswa_id'] != $siswa['siswa_id']) {
      session()->setFlashdata('error', 'Anda tidak memiliki akses ke laporan ini');
      return redirect()->to(base_url('siswa/hasil'));
    }

    // Ambil detail jawaban
    $detailJawaban = $this->hasilUjianModel
      ->select('
            hasil_ujian.*, 
            soal_ujian.pertanyaan, 
            soal_ujian.kode_soal,
            soal_ujian.jawaban_benar, 
            soal_ujian.tingkat_kesulitan, 
            soal_ujian.pembahasan,
            DATE_FORMAT(hasil_ujian.waktu_menjawab, "%H:%i:%s") as waktu_menjawab_format
        ')
      ->join('soal_ujian', 'soal_ujian.soal_id = hasil_ujian.soal_id')
      ->where('hasil_ujian.peserta_ujian_id', $pesertaUjianId)
      ->orderBy('hasil_ujian.waktu_menjawab', 'ASC')
      ->findAll();

    // Hitung durasi per soal
    $detailJawabanDenganDurasi = $this->hitungDurasiPerSoal($detailJawaban, $hasil['waktu_mulai']);

    // Hitung statistik
    $totalSoal = count($detailJawabanDenganDurasi);
    $jawabanBenar = array_reduce($detailJawabanDenganDurasi, function ($carry, $item) {
      return $carry + ($item['is_correct'] ? 1 : 0);
    }, 0);

    // Ambil theta terakhir dari database
    $lastResult = $this->hasilUjianModel
      ->select('theta_saat_ini')
      ->where('peserta_ujian_id', $pesertaUjianId)
      ->orderBy('waktu_menjawab', 'DESC')
      ->limit(1)
      ->first();

    // Ekstrak nilai numerik dari array $lastResult.
    $theta_akhir = $lastResult ? (float)$lastResult['theta_saat_ini'] : 0;

    // Hitung skor akhir menggunakan fungsi yang sudah diubah
    $skor_akhir = $this->hitungKemampuanKognitif($theta_akhir);

    // Dapatkan klasifikasi berdasarkan skor akhir
    $klasifikasiKognitif = $this->getKlasifikasiKognitif($skor_akhir);

    // Siapkan array data kognitif untuk dikirim ke view.
    $kemampuanKognitif = [
      'skor' => $skor_akhir,
      'total_benar' => $jawabanBenar,
      'total_salah' => $totalSoal - $jawabanBenar,
      'rata_rata_pilihan' => 0 // Tidak lagi relevan
    ];

    // Format durasi total
    if ($hasil['durasi_total_detik']) {
      $jam = floor($hasil['durasi_total_detik'] / 3600);
      $menit = floor(($hasil['durasi_total_detik'] % 3600) / 60);
      $detik = $hasil['durasi_total_detik'] % 60;
      $hasil['durasi_total_format'] = sprintf('%02d:%02d:%02d', $jam, $menit, $detik);
    }

    // Hitung rata-rata waktu per soal
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
