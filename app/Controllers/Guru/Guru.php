<?php

namespace App\Controllers\Guru;

use CodeIgniter\Controller;
use App\Models\JenisUjianModel;
use App\Models\UjianModel;
use App\Models\SoalUjianModel;
use App\Models\KelasModel;
use App\Models\JadwalUjianModel;
use App\Models\GuruModel;
use App\Models\PengumumanModel;
use App\Models\HasilUjianModel;
use App\Models\PesertaUjianModel;
use App\Models\VariabelModel;
use App\Models\IndikatorModel;
use App\Models\MateriModel;
use App\Models\UjianBankModel;
use App\Models\PaketUjianModel;
use App\Models\UjianSoalCatModel;

class Guru extends Controller
{
    protected $jenisUjianModel;
    protected $ujianModel;
    protected $soalUjianModel;
    protected $jadwalUjianModel;
    protected $kelasModel;
    protected $guruModel;
    protected $pengumumanModel;
    protected $hasilUjianModel;
    protected $pesertaUjianModel;
    protected $variabelModel;
    protected $indikatorModel;
    protected $materiModel;
    protected $ujianBankModel;
    protected $paketUjianModel;
    protected $ujianSoalCatModel;
    protected $db;

    public function __construct()
    {
        $this->jenisUjianModel = new JenisUjianModel();
        $this->ujianModel = new UjianModel();
        $this->soalUjianModel = new SoalUjianModel();
        $this->jadwalUjianModel = new JadwalUjianModel();
        $this->kelasModel = new KelasModel();
        $this->guruModel = new GuruModel();
        $this->pengumumanModel = new PengumumanModel();
        $this->hasilUjianModel = new HasilUjianModel();
        $this->pesertaUjianModel = new PesertaUjianModel();
        $this->variabelModel = new VariabelModel();
        $this->indikatorModel = new IndikatorModel();
        $this->materiModel = new MateriModel();
        $this->ujianBankModel = new UjianBankModel();
        $this->paketUjianModel = new PaketUjianModel();
        $this->ujianSoalCatModel = new UjianSoalCatModel();
        $this->db = \Config\Database::connect();
    }

    public function dashboard()
    {
        return view('guru/dashboard');
    }

    // ===== KELOLA UJIAN =====

    public function ujian()
    {
        $userId = session()->get('user_id');
        $guru = $this->guruModel->where('user_id', $userId)->first();

        // Ambil ujian berdasarkan kelas yang diajar guru
        $data['ujian'] = $this->ujianModel->getByKelasGuru($guru['guru_id']);

        // Ambil Mata Pelajaran berdasarkan kelas yang diajar guru
        $data['jenis_ujian'] = $this->jenisUjianModel->getByKelasGuru($guru['guru_id']);

        // Ambil kelas yang diajar guru untuk dropdown
        $data['kelas_guru'] = $this->db->table('kelas')
            ->select('kelas.*, sekolah.nama_sekolah')
            ->join('kelas_guru', 'kelas_guru.kelas_id = kelas.kelas_id')
            ->join('sekolah', 'sekolah.sekolah_id = kelas.sekolah_id', 'left')
            ->where('kelas_guru.guru_id', $guru['guru_id'])
            ->get()->getResultArray();

        $data['guru_sekolah'] = $this->db->table('sekolah')
            ->select('sekolah.sekolah_id, sekolah.nama_sekolah')
            ->join('guru', 'guru.sekolah_id = sekolah.sekolah_id')
            ->where('guru.user_id', $userId)
            ->get()->getRowArray();

        return view('guru/ujian', $data);
    }

    public function tambahUjian()
    {
        $userId = session()->get('user_id');
        $guru = $this->guruModel->where('user_id', $userId)->first();

        // Validasi input form
        $rules = [
            'sekolah_id' => 'required|numeric',
            'jenis_ujian_id' => 'required|numeric',
            'nama_ujian' => 'required|min_length[3]|max_length[255]',
            'kode_ujian' => 'required|alpha_numeric_punct|min_length[3]|max_length[50]',
            'deskripsi' => 'required|min_length[10]',
            'tipe_ujian' => 'required|in_list[CAT,CBT]',
            'tampilkan_pembahasan' => 'permit_empty',
            'visibilitas' => 'permit_empty',
            'pengulangan_aktif' => 'permit_empty',
            'maksimal_attempt' => 'permit_empty|numeric|less_than_equal_to[3]',
            'acak_urutan_soal' => 'permit_empty',
            'acak_pilihan_jawaban' => 'permit_empty',
            'maksimal_soal_tampil' => 'permit_empty|numeric',
            'se_awal' => 'required|decimal',
            'se_minimum' => 'required|decimal',
            'delta_se_minimum' => 'required|decimal',
            'durasi' => 'required|regex_match[/^([0-1][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/]',
            'kelas_id' => 'permit_empty|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors());
        }

        // Validasi Mata Pelajaran (pastikan guru memiliki akses)
        $jenisUjianId = $this->request->getPost('jenis_ujian_id');
        if (!$this->jenisUjianModel->hasAccess($jenisUjianId, $guru['guru_id'])) {
            return redirect()->to('guru/ujian')
                ->with('error', 'Anda tidak memiliki akses untuk menggunakan Mata Pelajaran tersebut.');
        }

        $sekolahId = $this->normalizeNullableId($this->request->getPost('sekolah_id'));
        if ((int) ($guru['sekolah_id'] ?? 0) !== (int) $sekolahId) {
            return redirect()->to('guru/ujian')
                ->with('error', 'Anda tidak memiliki akses untuk membuat ujian di sekolah tersebut.');
        }

        // Validasi kelas (jika dipilih)
        $kelasId = $this->normalizeNullableId($this->request->getPost('kelas_id'));
        if ($kelasId !== null) {
            $kelasAccess = $this->db->table('kelas_guru')
                ->join('kelas', 'kelas.kelas_id = kelas_guru.kelas_id')
                ->where('guru_id', $guru['guru_id'])
                ->where('kelas_id', $kelasId)
                ->where('kelas.sekolah_id', $sekolahId)
                ->get()->getRowArray();

            if (!$kelasAccess) {
                return redirect()->to('guru/ujian')
                    ->with('error', 'Anda tidak memiliki akses untuk menambahkan ujian pada kelas tersebut.');
            }
        }


        $data = [
            'sekolah_id' => $sekolahId,
            'jenis_ujian_id' => $jenisUjianId,
            'nama_ujian' => $this->request->getPost('nama_ujian'),
            'kode_ujian' => $this->request->getPost('kode_ujian'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'tipe_ujian' => $this->request->getPost('tipe_ujian') ?: 'CAT',
            'tampilkan_pembahasan' => $this->request->getPost('tampilkan_pembahasan') ? 1 : 0,
            'visibilitas' => $this->request->getPost('visibilitas') ?: 'terbuka',
            'pengulangan_aktif' => $this->request->getPost('pengulangan_aktif') ? 1 : 0,
            'maksimal_attempt' => $this->request->getPost('maksimal_attempt') ?: 1,
            'acak_urutan_soal' => $this->request->getPost('acak_urutan_soal') ? 1 : 0,
            'acak_pilihan_jawaban' => $this->request->getPost('acak_pilihan_jawaban') ? 1 : 0,
            'maksimal_soal_tampil' => $this->request->getPost('maksimal_soal_tampil') ?: 20,
            'se_awal' => $this->request->getPost('se_awal'),
            'se_minimum' => $this->request->getPost('se_minimum'),
            'delta_se_minimum' => $this->request->getPost('delta_se_minimum'),
            'durasi' => $this->request->getPost('durasi'),
            'kelas_id' => $kelasId,
            'created_by' => $userId
        ];

        try {
            $this->ujianModel->insert($data);
            return redirect()->to('guru/ujian')->with('success', 'Ujian berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->to('guru/ujian')->with('error', 'Gagal menambahkan ujian: ' . $e->getMessage());
        }
    }

    public function editUjian($id)
    {
        $userId = session()->get('user_id');
        $guru = $this->guruModel->where('user_id', $userId)->first();

        // Ambil data ujian yang akan diedit untuk mendapatkan kode_ujian lama
        $ujian = $this->ujianModel->find($id);
        if (!$ujian) {
            return redirect()->to('guru/ujian')->with('error', 'Ujian tidak ditemukan.');
        }

        // Validasi input form, termasuk validasi untuk kode_ujian
        $rules = [
            'sekolah_id' => 'required|numeric',
            'jenis_ujian_id' => 'required|numeric',
            'nama_ujian' => 'required|min_length[3]|max_length[255]',
            'kode_ujian' => 'required|alpha_numeric_punct|min_length[3]|max_length[50]',
            'deskripsi' => 'required|min_length[10]',
            'tipe_ujian' => 'required|in_list[CAT,CBT]',
            'tampilkan_pembahasan' => 'permit_empty',
            'visibilitas' => 'permit_empty',
            'pengulangan_aktif' => 'permit_empty',
            'maksimal_attempt' => 'permit_empty|numeric|less_than_equal_to[3]',
            'acak_urutan_soal' => 'permit_empty',
            'acak_pilihan_jawaban' => 'permit_empty',
            'maksimal_soal_tampil' => 'permit_empty|numeric',
            'se_awal' => 'required|decimal',
            'se_minimum' => 'required|decimal',
            'delta_se_minimum' => 'required|decimal',
            'durasi' => 'required|regex_match[/^([0-1][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/]',
            'kelas_id' => 'permit_empty|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors());
        }

        // Cek akses ke ujian ini
        if (!$this->ujianModel->hasAccess($id, $guru['guru_id'])) {
            return redirect()->to('guru/ujian')
                ->with('error', 'Anda tidak memiliki akses untuk mengedit ujian ini.');
        }

        // Validasi Mata Pelajaran
        $jenisUjianId = $this->request->getPost('jenis_ujian_id');
        if (!$this->jenisUjianModel->hasAccess($jenisUjianId, $guru['guru_id'])) {
            return redirect()->to('guru/ujian')
                ->with('error', 'Anda tidak memiliki akses untuk menggunakan Mata Pelajaran tersebut.');
        }

        $sekolahId = $this->normalizeNullableId($this->request->getPost('sekolah_id'));
        if ((int) ($guru['sekolah_id'] ?? 0) !== (int) $sekolahId) {
            return redirect()->to('guru/ujian')
                ->with('error', 'Anda tidak memiliki akses untuk mengubah ujian ke sekolah tersebut.');
        }

        // Validasi kelas (jika diubah)
        $kelasId = $this->normalizeNullableId($this->request->getPost('kelas_id'));
        if ($kelasId !== null) {
            $kelasAccess = $this->db->table('kelas_guru')
                ->join('kelas', 'kelas.kelas_id = kelas_guru.kelas_id')
                ->where('guru_id', $guru['guru_id'])
                ->where('kelas_id', $kelasId)
                ->where('kelas.sekolah_id', $sekolahId)
                ->get()->getRowArray();

            if (!$kelasAccess) {
                return redirect()->to('guru/ujian')
                    ->with('error', 'Anda tidak memiliki akses untuk memindahkan ujian ke kelas tersebut.');
            }
        }

        $data = [
            'sekolah_id' => $sekolahId,
            'jenis_ujian_id' => $jenisUjianId,
            'nama_ujian' => $this->request->getPost('nama_ujian'),
            'kode_ujian' => $this->request->getPost('kode_ujian'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'tipe_ujian' => $this->request->getPost('tipe_ujian') ?: 'CAT',
            'tampilkan_pembahasan' => $this->request->getPost('tampilkan_pembahasan') ? 1 : 0,
            'visibilitas' => $this->request->getPost('visibilitas') ?: 'terbuka',
            'pengulangan_aktif' => $this->request->getPost('pengulangan_aktif') ? 1 : 0,
            'maksimal_attempt' => $this->request->getPost('maksimal_attempt') ?: 1,
            'acak_urutan_soal' => $this->request->getPost('acak_urutan_soal') ? 1 : 0,
            'acak_pilihan_jawaban' => $this->request->getPost('acak_pilihan_jawaban') ? 1 : 0,
            'maksimal_soal_tampil' => $this->request->getPost('maksimal_soal_tampil') ?: 20,
            'se_awal' => $this->request->getPost('se_awal'),
            'se_minimum' => $this->request->getPost('se_minimum'),
            'delta_se_minimum' => $this->request->getPost('delta_se_minimum'),
            'durasi' => $this->request->getPost('durasi'),
            'kelas_id' => $kelasId
        ];

        try {
            $this->ujianModel->update($id, $data);
            return redirect()->to('guru/ujian')->with('success', 'Ujian berhasil diupdate');
        } catch (\Exception $e) {
            return redirect()->to('guru/ujian')->with('error', 'Gagal mengupdate ujian: ' . $e->getMessage());
        }
    }

    public function hapusUjian($id)
    {
        $userId = session()->get('user_id');
        $guru = $this->guruModel->where('user_id', $userId)->first();

        // Cek akses ke ujian ini
        if (!$this->ujianModel->hasAccess($id, $guru['guru_id'])) {
            return redirect()->to('guru/ujian')
                ->with('error', 'Anda tidak memiliki akses untuk menghapus ujian ini.');
        }

        $ujian = $this->ujianModel->find($id);
        $soalTerkait = (($ujian['tipe_ujian'] ?? 'CAT') === 'CAT')
            ? $this->ujianSoalCatModel->countSoalByUjian($id)
            : $this->soalUjianModel->where('ujian_id', $id)->countAllResults();

        if ($soalTerkait > 0) {
            return redirect()->to('guru/ujian')
                ->with('error', 'Tidak dapat menghapus ujian ini karena masih ada ' . $soalTerkait . ' soal yang terkait. Harap hapus soal-soal ujian terlebih dahulu.');
        }

        try {
            $this->ujianModel->delete($id);
            return redirect()->to('guru/ujian')
                ->with('success', 'Ujian berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->to('guru/ujian')
                ->with('error', 'Terjadi kesalahan saat menghapus ujian');
        }
    }

    // ===== KELOLA SOAL =====

    public function kelolaSoal($ujian_id)
    {
        $userId = session()->get('user_id');
        $guru = $this->guruModel->where('user_id', $userId)->first();

        // Cek akses ke ujian ini
        if (!$this->ujianModel->hasAccess($ujian_id, $guru['guru_id'])) {
            return redirect()->to('guru/ujian')
                ->with('error', 'Anda tidak memiliki akses untuk mengelola soal ujian ini.');
        }

        $data['ujian'] = $this->ujianModel->find($ujian_id);
        if (!$data['ujian']) {
            return redirect()->to('guru/ujian')
                ->with('error', 'Ujian tidak ditemukan.');
        }

        $data['soal'] = (($data['ujian']['tipe_ujian'] ?? 'CAT') === 'CAT')
            ? $this->ujianSoalCatModel->getSoalByUjian($ujian_id)
            : $this->soalUjianModel->where('ujian_id', $ujian_id)->findAll();
        $data['variabel']  = $this->variabelModel->orderBy('nama_variabel', 'ASC')->findAll();
        $data['indikator'] = $this->indikatorModel->orderBy('nama_indikator', 'ASC')->findAll();
        $data['materi']    = $this->materiModel->orderBy('nama_materi', 'ASC')->findAll();
        $data['sekolah']   = $this->db->table('sekolah')->orderBy('nama_sekolah', 'ASC')->get()->getResultArray();

        // Data bank & paket
        $data['assignedBanks'] = $this->ujianBankModel->getBanksByUjian($ujian_id);
        $data['allBanks'] = $this->db->table('bank_ujian')->orderBy('nama_ujian', 'ASC')->get()->getResultArray();
        foreach ($data['allBanks'] as &$bank) {
            $bank['jumlah_soal'] = $this->soalUjianModel->where(['bank_ujian_id' => $bank['bank_ujian_id'], 'is_bank_soal' => 1])->countAllResults();
        }
        $data['paketList'] = $this->paketUjianModel->getByUjian($ujian_id);
        $totalSoal = 0;
        foreach ($data['assignedBanks'] as $ab) {
            $totalSoal += $this->soalUjianModel->where(['bank_ujian_id' => $ab['bank_ujian_id'], 'is_bank_soal' => 1])->countAllResults();
        }
        $data['totalSoal'] = $totalSoal;
        $data['attemptCount'] = $this->db->table('attempt_ujian au')
            ->join('paket_ujian pu', 'pu.paket_id = au.paket_id')
            ->where('pu.ujian_id', $ujian_id)
            ->countAllResults();
        $data['paketSudahDipakai'] = $data['attemptCount'] > 0;
        $data['draftPaket'] = $this->getDraftPaket($ujian_id);
        $panel = $this->request->getGet('panel');
        $finalPaketSudahAda = !empty($data['paketList']) && empty($data['draftPaket']['packages']);
        $data['panelAktif'] = in_array($panel, ['generate', 'paket'], true)
            ? $panel
            : (!empty($data['draftPaket']['packages']) ? 'paket' : 'generate');
        $requestedStep = (int) ($this->request->getGet('step') ?? 1);
        $maxStep = empty($data['assignedBanks']) ? 1 : ($finalPaketSudahAda ? 3 : 2);
        $data['currentStep'] = $finalPaketSudahAda ? 3 : max(1, min($requestedStep, $maxStep));

        return view('guru/kelola_soal', $data);
    }


    public function tambahSoal()
    {
        // Validasi form input (sama seperti sebelumnya)
        $rules = [
            'ujian_id' => 'required|numeric',
            'pertanyaan' => 'required',
            'kode_soal' => 'required|alpha_numeric_punct|min_length[3]|max_length[50]',
            'pilihan_a' => 'required',
            'pilihan_b' => 'required',
            'pilihan_c' => 'required',
            'pilihan_d' => 'required',
            'jawaban_benar' => 'required|in_list[A,B,C,D,E]',
            'tingkat_kesulitan' => 'required|decimal',
            'a' => 'permit_empty|decimal',
            'c' => 'permit_empty|decimal',
            'variabel_id' => 'permit_empty|numeric',
            'indikator_id' => 'permit_empty|numeric',
            'materi_id' => 'permit_empty|numeric',
            'pembahasan' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            // CLEANUP: Hapus gambar yang diupload sementara jika validasi gagal
            $this->cleanupTempImages();

            $errors = $this->validator->getErrors();
            $errorMessage = 'Validasi gagal: ' . implode(', ', $errors);
            return redirect()->back()->withInput()->with('error', $errorMessage);
        }

        // Ambil data dari form
        $ujianId = (int) $this->request->getPost('ujian_id');
        $ujian = $this->ujianModel->find($ujianId);
        $isCatMode = (($ujian['tipe_ujian'] ?? 'CAT') === 'CAT');

        $data = [
            'ujian_id' => $isCatMode ? null : $ujianId,
            'pertanyaan' => $this->request->getPost('pertanyaan'),
            'kode_soal' => $this->request->getPost('kode_soal'),
            'pilihan_a' => $this->request->getPost('pilihan_a'),
            'pilihan_b' => $this->request->getPost('pilihan_b'),
            'pilihan_c' => $this->request->getPost('pilihan_c'),
            'pilihan_d' => $this->request->getPost('pilihan_d'),
            'pilihan_e' => $this->request->getPost('pilihan_e'),
            'jawaban_benar' => $this->request->getPost('jawaban_benar'),
            'tingkat_kesulitan' => $this->request->getPost('tingkat_kesulitan'),
            'a' => $this->request->getPost('a') ?: 1.000,
            'c' => $this->request->getPost('c') ?: 0.000,
            'variabel_id' => $this->request->getPost('variabel_id') ?: null,
            'indikator_id' => $this->request->getPost('indikator_id') ?: null,
            'materi_id' => $this->request->getPost('materi_id') ?: null,
            'pembahasan' => $this->request->getPost('pembahasan'),
            'created_by' => session()->get('user_id')
        ];

        // Upload media field terpisah (jika ada)
        $mediaFile = $this->request->getFile('media');
        if ($mediaFile && $mediaFile->isValid() && !$mediaFile->hasMoved()) {
            $newName = $mediaFile->getRandomName();
            $uploadPath = FCPATH . 'uploads/soal';

            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $mediaFile->move($uploadPath, $newName);
            $data['media'] = $newName;
        }

        try {
            // Simpan data soal ke database
            $soalId = $this->soalUjianModel->insert($data);

            if ($soalId) {
                // TRACKING: Extract gambar yang digunakan dari semua field HTML
                $allHtmlContent = $data['pertanyaan'] . ' ' . $data['pilihan_a'] . ' ' .
                    $data['pilihan_b'] . ' ' . $data['pilihan_c'] . ' ' .
                    $data['pilihan_d'] . ' ' . ($data['pilihan_e'] ?? '') . ' ' .
                    ($data['pembahasan'] ?? '');

                $usedImages = $this->extractImageFilenames($allHtmlContent);

                // CLEANUP: Hapus gambar yang tidak digunakan
                $tempImages = session()->get('temp_uploaded_images') ?? [];
                $this->cleanupUnusedImages($usedImages, $tempImages);

                // Clear temp session
                session()->remove('temp_uploaded_images');

                if ($isCatMode) {
                    $this->ujianSoalCatModel->linkSoal($ujianId, (int) $soalId);
                }

                return redirect()->to('guru/soal/' . $ujianId)->with('success', 'Soal berhasil ditambahkan');
            } else {
                throw new \Exception('Gagal menyimpan soal');
            }
        } catch (\Exception $e) {
            // CLEANUP: Hapus semua temp images jika ada error
            $this->cleanupTempImages();

            log_message('error', 'Error saat menambahkan soal: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan soal: ' . $e->getMessage());
        }
    }


    public function editSoal($id)
    {
        // Ambil data soal yang akan diedit
        $soal = $this->soalUjianModel->find($id);
        if (!$soal) {
            $this->cleanupTempImages();
            return redirect()->back()->with('error', 'Soal tidak ditemukan');
        }

        // Backup: Extract gambar yang sedang digunakan sebelum edit
        $oldHtmlContent = $soal['pertanyaan'] . ' ' . $soal['pilihan_a'] . ' ' .
            $soal['pilihan_b'] . ' ' . $soal['pilihan_c'] . ' ' .
            $soal['pilihan_d'] . ' ' . ($soal['pilihan_e'] ?? '') . ' ' .
            ($soal['pembahasan'] ?? '');
        $oldUsedImages = $this->extractImageFilenames($oldHtmlContent);

        // Validasi form input (sama seperti sebelumnya)
        $rules = [
            'kode_soal' => 'required|alpha_numeric_punct|min_length[3]|max_length[50]',
            'pertanyaan' => 'required',
            'pilihan_a' => 'required',
            'pilihan_b' => 'required',
            'pilihan_c' => 'required',
            'pilihan_d' => 'required',
            'jawaban_benar' => 'required|in_list[A,B,C,D,E]',
            'tingkat_kesulitan' => 'required|decimal',
            'a' => 'permit_empty|decimal',
            'c' => 'permit_empty|decimal',
            'variabel_id' => 'permit_empty|numeric',
            'indikator_id' => 'permit_empty|numeric',
            'materi_id' => 'permit_empty|numeric',
            'media' => 'permit_empty|max_size[media,2048]|mime_in[media,image/jpg,image/jpeg,image/png]|ext_in[media,png,jpg,jpeg]',
            'pembahasan' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            $this->cleanupTempImages();
            $errors = $this->validator->getErrors();
            $errorMessage = 'Validasi gagal: ' . implode(', ', $errors);
            return redirect()->back()->withInput()->with('error', $errorMessage);
        }

        // Ambil data dari form
        $data = [
            'kode_soal' => $this->request->getPost('kode_soal'),
            'pertanyaan' => $this->request->getPost('pertanyaan'),
            'pilihan_a' => $this->request->getPost('pilihan_a'),
            'pilihan_b' => $this->request->getPost('pilihan_b'),
            'pilihan_c' => $this->request->getPost('pilihan_c'),
            'pilihan_d' => $this->request->getPost('pilihan_d'),
            'pilihan_e' => $this->request->getPost('pilihan_e'),
            'jawaban_benar' => $this->request->getPost('jawaban_benar'),
            'tingkat_kesulitan' => $this->request->getPost('tingkat_kesulitan'),
            'a' => $this->request->getPost('a') ?: 1.000,
            'c' => $this->request->getPost('c') ?: 0.000,
            'variabel_id' => $this->request->getPost('variabel_id') ?: null,
            'indikator_id' => $this->request->getPost('indikator_id') ?: null,
            'materi_id' => $this->request->getPost('materi_id') ?: null,
            'pembahasan' => $this->request->getPost('pembahasan')
        ];

        $uploadPath = FCPATH . 'uploads/soal';
        $oldMediaField = $soal['media'] ?? $soal['foto'] ?? null;

        // Handle upload media field terpisah
        $mediaFile = $this->request->getFile('media');
        if ($mediaFile && $mediaFile->isValid() && !$mediaFile->hasMoved()) {
            if (!empty($oldMediaField)) {
                $oldPath = $uploadPath . '/' . $oldMediaField;
                if (file_exists($oldPath)) { unlink($oldPath); }
            }
            $newName = $mediaFile->getRandomName();
            if (!is_dir($uploadPath)) { mkdir($uploadPath, 0777, true); }
            $mediaFile->move($uploadPath, $newName);
            $data['media'] = $newName;
        }

        // Checkbox untuk menghapus media
        if ($this->request->getPost('hapus_media') == '1' && !empty($oldMediaField)) {
            $oldPath = $uploadPath . '/' . $oldMediaField;
            if (file_exists($oldPath)) { unlink($oldPath); }
            $data['media'] = null;
        }

        try {
            // Update data soal di database
            $this->soalUjianModel->update($id, $data);

            // TRACKING: Extract gambar yang digunakan dari konten baru
            $newHtmlContent = $data['pertanyaan'] . ' ' . $data['pilihan_a'] . ' ' .
                $data['pilihan_b'] . ' ' . $data['pilihan_c'] . ' ' .
                $data['pilihan_d'] . ' ' . ($data['pilihan_e'] ?? '') . ' ' .
                ($data['pembahasan'] ?? '');
            $newUsedImages = $this->extractImageFilenames($newHtmlContent);

            // CLEANUP: Hapus gambar lama yang tidak digunakan lagi
            $tempImages = session()->get('temp_uploaded_images') ?? [];
            $imagesToCleanup = array_diff($oldUsedImages, $newUsedImages);

            foreach ($imagesToCleanup as $filename) {
                $imagePath = FCPATH . 'uploads/editor-images/' . $filename;
                if (file_exists($imagePath)) {
                    // Cek apakah gambar digunakan oleh soal lain
                    $otherUsage = $this->checkImageUsageInOtherQuestions($filename, $id);
                    if (!$otherUsage) {
                        unlink($imagePath);
                    }
                }
            }

            // CLEANUP: Hapus temp images yang tidak digunakan
            $this->cleanupUnusedImages($newUsedImages, $tempImages);

            // Clear temp session
            session()->remove('temp_uploaded_images');

            $ujian_id = $this->request->getPost('ujian_id');
            return redirect()->to('guru/soal/' . $ujian_id)->with('success', 'Soal berhasil diupdate');
        } catch (\Exception $e) {
            $this->cleanupTempImages();
            log_message('error', 'Error saat mengupdate soal: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui soal: ' . $e->getMessage());
        }
    }

    public function hapusSoal($id, $ujian_id)
    {
        // Cek apakah soal sudah dijawab siswa
        $isAnswered = $this->hasilUjianModel->where('soal_id', $id)->countAllResults() > 0;

        if ($isAnswered) {
            return redirect()->to('guru/soal/' . $ujian_id)
                ->with('error', 'Gagal! Soal ini tidak dapat dihapus karena sudah menjadi bagian dari riwayat pengerjaan siswa.');
        }

        try {
            // Ambil data soal yang akan dihapus
            $soal = $this->soalUjianModel->find($id);

            if ($soal) {
                // CLEANUP 1: Handle foto field terpisah (seperti sebelumnya)
                if (!empty($soal['foto'])) {
                    $filename = $soal['foto'];
                    $isImageUsedElsewhere = $this->soalUjianModel
                        ->where('foto', $filename)
                        ->where('soal_id !=', $id)
                        ->countAllResults() > 0;

                    if (!$isImageUsedElsewhere) {
                        $fotoPath = FCPATH . 'uploads/soal/' . $filename;
                        if (file_exists($fotoPath)) {
                            unlink($fotoPath);
                        }
                    }
                }

                // CLEANUP 2: Handle editor images dalam HTML content
                $allHtmlContent = $soal['pertanyaan'] . ' ' . $soal['pilihan_a'] . ' ' .
                    $soal['pilihan_b'] . ' ' . $soal['pilihan_c'] . ' ' .
                    $soal['pilihan_d'] . ' ' . ($soal['pilihan_e'] ?? '') . ' ' .
                    ($soal['pembahasan'] ?? '');

                $usedImages = $this->extractImageFilenames($allHtmlContent);

                foreach ($usedImages as $filename) {
                    // Cek apakah gambar digunakan oleh soal lain
                    $isUsedElsewhere = $this->checkImageUsageInOtherQuestions($filename, $id);

                    if (!$isUsedElsewhere) {
                        $imagePath = FCPATH . 'uploads/editor-images/' . $filename;
                        if (file_exists($imagePath)) {
                            unlink($imagePath);
                        }
                    }
                }

                // Hapus record soal dari database
                $this->ujianSoalCatModel->deleteBySoal($id);
                $this->soalUjianModel->delete($id);
                return redirect()->to('guru/soal/' . $ujian_id)->with('success', 'Soal berhasil dihapus.');
            } else {
                return redirect()->to('guru/soal/' . $ujian_id)->with('error', 'Soal yang akan dihapus tidak ditemukan.');
            }
        } catch (\Exception $e) {
            log_message('error', 'Guru gagal menghapus soal: ' . $e->getMessage());
            return redirect()->to('guru/soal/' . $ujian_id)->with('error', 'Terjadi kesalahan saat menghapus soal.');
        }
    }



    // ===== KELOLA JADWAL UJIAN =====

    public function jadwalUjian()
    {
        $userId = session()->get('user_id');
        $guru = $this->guruModel->where('user_id', $userId)->first();

        $data['jadwal'] = $this->db->table('jadwal_ujian')
            ->select('jadwal_ujian.*, ujian.nama_ujian, ujian.kode_ujian, ujian.tipe_ujian, kelas.nama_kelas, guru.nama_lengkap')
            ->join('ujian', 'ujian.id_ujian = jadwal_ujian.ujian_id')
            ->join('kelas', 'kelas.kelas_id = jadwal_ujian.kelas_id')
            ->join('guru', 'guru.guru_id = jadwal_ujian.guru_id')
            ->join('kelas_guru', 'kelas_guru.kelas_id = jadwal_ujian.kelas_id')
            ->where('kelas_guru.guru_id', $guru['guru_id'])
            ->orderBy('jadwal_ujian.tanggal_mulai', 'DESC')
            ->get()->getResultArray();

        // Daftar ujian untuk modal tambah: hanya ujian yang bisa diakses guru
        $data['ujian_tambah'] = $this->ujianModel->getByKelasGuru($guru['guru_id']);

        // Daftar ujian untuk modal edit: hanya ujian yang bisa diakses guru
        $data['ujian_edit'] = $data['ujian_tambah'];

        // Daftar kelas yang diajar guru
        $data['kelas'] = $this->db->table('kelas')
            ->select('kelas.*, sekolah.nama_sekolah')
            ->join('kelas_guru', 'kelas_guru.kelas_id = kelas.kelas_id')
            ->join('sekolah', 'sekolah.sekolah_id = kelas.sekolah_id', 'left')
            ->where('kelas_guru.guru_id', $guru['guru_id'])
            ->get()->getResultArray();

        // Daftar guru (untuk pengawas) - hanya guru yang mengajar di kelas yang sama
        $data['guru'] = $this->db->table('guru')
            ->select('guru.*')
            ->join('kelas_guru kg1', 'kg1.guru_id = guru.guru_id')
            ->join('kelas_guru kg2', 'kg2.kelas_id = kg1.kelas_id')
            ->where('kg2.guru_id', $guru['guru_id'])
            ->groupBy('guru.guru_id')
            ->get()->getResultArray();

        $data['siswa'] = $this->db->table('siswa')
            ->select('siswa.siswa_id, siswa.nama_lengkap, siswa.nomor_peserta, siswa.kelas_id, kelas.nama_kelas')
            ->join('kelas', 'kelas.kelas_id = siswa.kelas_id', 'left')
            ->join('kelas_guru', 'kelas_guru.kelas_id = siswa.kelas_id')
            ->where('kelas_guru.guru_id', $guru['guru_id'])
            ->orderBy('siswa.nama_lengkap', 'ASC')->get()->getResultArray();

        return view('guru/jadwal_ujian', $data);
    }

    public function tambahJadwal()
    {
        $userId = session()->get('user_id');
        $guru = $this->guruModel->where('user_id', $userId)->first();

        $ujian_id = $this->request->getPost('ujian_id');
        $kelas_id = $this->request->getPost('kelas_id');
        $guru_pengawas_id = $this->request->getPost('guru_id');

        // Validasi akses ujian
        if (!$this->ujianModel->hasAccess($ujian_id, $guru['guru_id'])) {
            return redirect()->to('guru/jadwal-ujian')
                ->with('error', 'Anda tidak memiliki akses untuk menjadwalkan ujian tersebut.');
        }

        // Validasi akses kelas
        $kelasAccess = $this->db->table('kelas_guru')
            ->where('guru_id', $guru['guru_id'])
            ->where('kelas_id', $kelas_id)
            ->get()->getRowArray();

        if (!$kelasAccess) {
            return redirect()->to('guru/jadwal-ujian')
                ->with('error', 'Anda tidak memiliki akses untuk menjadwalkan ujian pada kelas tersebut.');
        }

        $tipePenugasan = $this->request->getPost('tipe_penugasan') ?: 'kelas';
        $siswaIds = $this->request->getPost('siswa_ids') ?? [];
        $siswaIds = array_values(array_unique(array_map('intval', (array) $siswaIds)));

        if (!$this->validateJadwalKelasAgainstUjian((int) $ujian_id, (int) $kelas_id)) {
            return redirect()->to('guru/jadwal-ujian')
                ->with('error', 'Kelas jadwal tidak sesuai dengan pengaturan sekolah/kelas pada ujian.');
        }

        if ($tipePenugasan === 'individu') {
            if (empty($siswaIds)) {
                return redirect()->to('guru/jadwal-ujian')
                    ->with('error', 'Pilih minimal satu siswa untuk penugasan individu.');
            }

            if (!$this->validateGuruSiswaIdsForKelas((int) $guru['guru_id'], (int) $kelas_id, $siswaIds)) {
                return redirect()->to('guru/jadwal-ujian')
                    ->with('error', 'Daftar siswa tidak valid. Hanya siswa dari kelas yang Anda ajar yang boleh ditugaskan.');
            }
        } else {
            $siswaIds = [];
        }

        $data = [
            'ujian_id' => $ujian_id,
            'kelas_id' => $kelas_id,
            'guru_id' => $guru_pengawas_id,
            'tanggal_mulai' => $this->request->getPost('tanggal_mulai'),
            'tanggal_selesai' => $this->request->getPost('tanggal_selesai'),
            'kode_akses' => $this->request->getPost('kode_akses'),
            'tipe_penugasan' => $tipePenugasan,
            'siswa_ids' => $tipePenugasan === 'individu' && !empty($siswaIds) ? json_encode($siswaIds) : null,
            'status' => 'belum_mulai'
        ];

        try {
            $this->jadwalUjianModel->insert($data);
            return redirect()->to('guru/jadwal-ujian')->with('success', 'Jadwal ujian berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->to('guru/jadwal-ujian')->with('error', 'Gagal menambahkan jadwal ujian: ' . $e->getMessage());
        }
    }


    public function editJadwal($id)
    {
        $userId = session()->get('user_id');
        $guru = $this->guruModel->where('user_id', $userId)->first();

        // Cek akses ke jadwal ujian ini
        if (!$this->jadwalUjianModel->hasAccess($id, $guru['guru_id'])) {
            return redirect()->to('guru/jadwal-ujian')
                ->with('error', 'Anda tidak memiliki akses untuk mengedit jadwal ujian ini.');
        }

        $ujian_id = $this->request->getPost('ujian_id');
        $kelas_id = $this->request->getPost('kelas_id');

        // Validasi akses ujian
        if (!$this->ujianModel->hasAccess($ujian_id, $guru['guru_id'])) {
            return redirect()->to('guru/jadwal-ujian')
                ->with('error', 'Anda tidak memiliki akses untuk menggunakan ujian tersebut.');
        }

        // Validasi akses kelas
        $kelasAccess = $this->db->table('kelas_guru')
            ->where('guru_id', $guru['guru_id'])
            ->where('kelas_id', $kelas_id)
            ->get()->getRowArray();

        if (!$kelasAccess) {
            return redirect()->to('guru/jadwal-ujian')
                ->with('error', 'Anda tidak memiliki akses untuk memindahkan jadwal ujian ke kelas tersebut.');
        }

        // Cek apakah kombinasi ujian_id dan kelas_id sudah ada, kecuali untuk jadwal yang sedang diedit
        $existing = $this->jadwalUjianModel
            ->where('ujian_id', $ujian_id)
            ->where('kelas_id', $kelas_id)
            ->where('jadwal_id !=', $id)
            ->first();

        if ($existing) {
            return redirect()->to('guru/jadwal-ujian')
                ->with('error', 'Jadwal ujian untuk kelas ini sudah ada. Pilih kelas lain atau ujian lain.');
        }

        $tipePenugasan = $this->request->getPost('tipe_penugasan') ?: 'kelas';
        $siswaIds = $this->request->getPost('siswa_ids') ?? [];
        $siswaIds = array_values(array_unique(array_map('intval', (array) $siswaIds)));
        $tanggalSelesai = $this->request->getPost('tanggal_selesai');
        $status = $this->request->getPost('status');

        if ($status === 'sedang_berlangsung' && strtotime($tanggalSelesai) < time()) {
            return redirect()->to('guru/jadwal-ujian')
                ->with('error', 'Tidak bisa mengubah status menjadi sedang berlangsung karena waktu selesai ujian sudah terlewat.');
        }

        if (!$this->validateJadwalKelasAgainstUjian((int) $ujian_id, (int) $kelas_id)) {
            return redirect()->to('guru/jadwal-ujian')
                ->with('error', 'Kelas jadwal tidak sesuai dengan pengaturan sekolah/kelas pada ujian.');
        }

        if ($tipePenugasan === 'individu') {
            if (empty($siswaIds)) {
                return redirect()->to('guru/jadwal-ujian')
                    ->with('error', 'Pilih minimal satu siswa untuk penugasan individu.');
            }

            if (!$this->validateGuruSiswaIdsForKelas((int) $guru['guru_id'], (int) $kelas_id, $siswaIds)) {
                return redirect()->to('guru/jadwal-ujian')
                    ->with('error', 'Daftar siswa tidak valid. Hanya siswa dari kelas yang Anda ajar yang boleh ditugaskan.');
            }
        } else {
            $siswaIds = [];
        }

        $data = [
            'ujian_id' => $ujian_id,
            'kelas_id' => $kelas_id,
            'guru_id' => $this->request->getPost('guru_id'),
            'tanggal_mulai' => $this->request->getPost('tanggal_mulai'),
            'tanggal_selesai' => $tanggalSelesai,
            'kode_akses' => $this->request->getPost('kode_akses'),
            'tipe_penugasan' => $tipePenugasan,
            'siswa_ids' => $tipePenugasan === 'individu' && !empty($siswaIds) ? json_encode($siswaIds) : null,
            'status' => $status
        ];

        try {
            $this->jadwalUjianModel->update($id, $data);
            return redirect()->to('guru/jadwal-ujian')->with('success', 'Jadwal ujian berhasil diupdate');
        } catch (\Exception $e) {
            return redirect()->to('guru/jadwal-ujian')->with('error', 'Gagal mengupdate jadwal ujian: ' . $e->getMessage());
        }
    }

    public function hapusJadwal($id)
    {
        $userId = session()->get('user_id');
        $guru = $this->guruModel->where('user_id', $userId)->first();

        // Cek akses ke jadwal ujian ini
        if (!$this->jadwalUjianModel->hasAccess($id, $guru['guru_id'])) {
            return redirect()->to('guru/jadwal-ujian')
                ->with('error', 'Anda tidak memiliki akses untuk menghapus jadwal ujian ini.');
        }

        // Cek apakah ada peserta ujian yang sudah terdaftar
        $pesertaTerkait = $this->db->table('peserta_ujian')
            ->where('jadwal_id', $id)
            ->countAllResults();

        if ($pesertaTerkait > 0) {
            return redirect()->to('guru/jadwal-ujian')
                ->with('error', 'Tidak dapat menghapus jadwal ujian ini karena sudah ada ' . $pesertaTerkait . ' peserta yang terdaftar.');
        }

        try {
            $this->jadwalUjianModel->delete($id);
            return redirect()->to('guru/jadwal-ujian')->with('success', 'Jadwal ujian berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->to('guru/jadwal-ujian')->with('error', 'Terjadi kesalahan saat menghapus jadwal ujian');
        }
    }


    // ===== KELOLA HASIL UJIAN =====

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

    private function hitungKemampuanKognitif($theta)
    {
        // Rumus baru: skor akhir siswa (x) = 50 + (16.67 * tetha)
        $skor_akhir = 50 + (16.67 * (float)$theta);

        // Pastikan skor tidak negatif
        $skor_akhir = max(0, $skor_akhir);

        // Mengembalikan skor yang sudah dibulatkan
        return round($skor_akhir, 2);
    }

    /**
     * Dapatkan klasifikasi kemampuan kognitif berdasarkan skor
     */
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

    private function getLatestAttemptForPeserta(int $pesertaUjianId): ?array
    {
        return $this->db->table('attempt_ujian')
            ->where('peserta_ujian_id', $pesertaUjianId)
            ->orderBy('nomor_attempt', 'DESC')
            ->get()
            ->getRowArray();
    }

    private function getAttemptByIdForPeserta(int $pesertaUjianId, int $attemptId): ?array
    {
        return $this->db->table('attempt_ujian')
            ->where('peserta_ujian_id', $pesertaUjianId)
            ->where('attempt_id', $attemptId)
            ->get()
            ->getRowArray();
    }

    private function getAttemptsForPeserta(int $pesertaUjianId): array
    {
        return $this->db->table('attempt_ujian')
            ->where('peserta_ujian_id', $pesertaUjianId)
            ->orderBy('nomor_attempt', 'ASC')
            ->get()
            ->getResultArray();
    }

    private function buildResultSummary(array $context, array $detailJawaban, ?array $attempt = null): array
    {
        $isCatMode = ($context['tipe_ujian'] ?? 'CAT') === 'CAT';
        $lastResult = !empty($detailJawaban) ? end($detailJawaban) : null;
        $thetaAkhir = $lastResult ? (float) ($lastResult['theta_saat_ini'] ?? 0) : 0.0;
        $seAkhir = $lastResult && isset($lastResult['se_saat_ini']) ? (float) $lastResult['se_saat_ini'] : null;

        if ($isCatMode) {
            $skorAkhir = $this->hitungKemampuanKognitif($thetaAkhir);
            $nilaiAkhir = min(100, max(0, round($skorAkhir)));
        } else {
            $skorAkhir = round((float) ($attempt['nilai_akhir'] ?? $context['nilai_akhir'] ?? 0), 2);
            $nilaiAkhir = $skorAkhir;
        }

        return [
            'is_cat_mode' => $isCatMode,
            'theta_akhir' => $thetaAkhir,
            'se_akhir' => $seAkhir,
            'skor_akhir' => $skorAkhir,
            'nilai_akhir' => $nilaiAkhir,
            'klasifikasi_kognitif' => $this->getKlasifikasiKognitif($skorAkhir),
        ];
    }

    public function hasilUjian()
    {
        $userId = session()->get('user_id');
        $guru = $this->guruModel->where('user_id', $userId)->first();

        // Ambil daftar ujian yang sudah pernah dijadwalkan oleh guru ini dengan informasi waktu
        $daftarUjian = $this->jadwalUjianModel
            ->select('jadwal_ujian.*, ujian.nama_ujian, ujian.deskripsi, ujian.kode_ujian, ujian.tipe_ujian, jenis_ujian.nama_jenis, kelas.nama_kelas,
             (SELECT COUNT(*) FROM peserta_ujian WHERE peserta_ujian.jadwal_id = jadwal_ujian.jadwal_id AND peserta_ujian.status = "selesai") as jumlah_peserta,
             (SELECT AVG(TIME_TO_SEC(TIMEDIFF(peserta_ujian.waktu_selesai, peserta_ujian.waktu_mulai))) 
             FROM peserta_ujian 
             WHERE peserta_ujian.jadwal_id = jadwal_ujian.jadwal_id AND peserta_ujian.status = "selesai") as rata_rata_durasi_detik,
             (SELECT MIN(TIME_TO_SEC(TIMEDIFF(peserta_ujian.waktu_selesai, peserta_ujian.waktu_mulai))) 
             FROM peserta_ujian 
             WHERE peserta_ujian.jadwal_id = jadwal_ujian.jadwal_id AND peserta_ujian.status = "selesai") as durasi_tercepat_detik,
             (SELECT MAX(TIME_TO_SEC(TIMEDIFF(peserta_ujian.waktu_selesai, peserta_ujian.waktu_mulai))) 
             FROM peserta_ujian 
             WHERE peserta_ujian.jadwal_id = jadwal_ujian.jadwal_id AND peserta_ujian.status = "selesai") as durasi_terlama_detik,
             DATE_FORMAT(jadwal_ujian.tanggal_mulai, "%d/%m/%Y %H:%i") as tanggal_mulai_format,
             DATE_FORMAT(jadwal_ujian.tanggal_selesai, "%d/%m/%Y %H:%i") as tanggal_selesai_format')
            ->join('ujian', 'ujian.id_ujian = jadwal_ujian.ujian_id')
            ->join('jenis_ujian', 'jenis_ujian.jenis_ujian_id = ujian.jenis_ujian_id')
            ->join('kelas', 'kelas.kelas_id = jadwal_ujian.kelas_id')
            ->where('jadwal_ujian.guru_id', $guru['guru_id'])
            ->orderBy('jadwal_ujian.tanggal_mulai', 'DESC')
            ->findAll();

        // Format durasi untuk setiap ujian
        foreach ($daftarUjian as &$ujian) {
            // Format rata-rata durasi
            if ($ujian['rata_rata_durasi_detik']) {
                $menit = floor($ujian['rata_rata_durasi_detik'] / 60);
                $detik = $ujian['rata_rata_durasi_detik'] % 60;
                $ujian['rata_rata_durasi_format'] = sprintf('%d menit %d detik', $menit, $detik);
            } else {
                $ujian['rata_rata_durasi_format'] = '-';
            }

            // Format durasi tercepat
            if ($ujian['durasi_tercepat_detik']) {
                $menit = floor($ujian['durasi_tercepat_detik'] / 60);
                $detik = $ujian['durasi_tercepat_detik'] % 60;
                $ujian['durasi_tercepat_format'] = sprintf('%d menit %d detik', $menit, $detik);
            } else {
                $ujian['durasi_tercepat_format'] = '-';
            }

            // Format durasi terlama
            if ($ujian['durasi_terlama_detik']) {
                $menit = floor($ujian['durasi_terlama_detik'] / 60);
                $detik = $ujian['durasi_terlama_detik'] % 60;
                $ujian['durasi_terlama_format'] = sprintf('%d menit %d detik', $menit, $detik);
            } else {
                $ujian['durasi_terlama_format'] = '-';
            }
        }

        return view('guru/hasil_ujian', ['daftarUjian' => $daftarUjian]);
    }

    public function hapusHasilSiswa($pesertaUjianId)
    {
        $userId = session()->get('user_id');
        $guru = $this->guruModel->where('user_id', $userId)->first();

        // Validasi akses
        $peserta = $this->pesertaUjianModel
            ->select('peserta_ujian.*, jadwal_ujian.guru_id, jadwal_ujian.jadwal_id')
            ->join('jadwal_ujian', 'jadwal_ujian.jadwal_id = peserta_ujian.jadwal_id')
            ->where('peserta_ujian.peserta_ujian_id', $pesertaUjianId)
            ->first();

        if (!$peserta) {
            session()->setFlashdata('error', 'Data peserta ujian tidak ditemukan');
            return redirect()->back();
        }

        // Cek akses guru
        if ($peserta['guru_id'] != $guru['guru_id']) {
            session()->setFlashdata('error', 'Anda tidak memiliki akses untuk menghapus hasil ujian ini');
            return redirect()->back();
        }

        try {
            $this->db->transStart();

            // 1. Hapus semua hasil ujian (jawaban) siswa ini
            $this->hasilUjianModel->where('peserta_ujian_id', $pesertaUjianId)->delete();

            // 2. Hapus record peserta ujian
            $this->pesertaUjianModel->delete($pesertaUjianId);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Gagal menghapus data');
            }

            session()->setFlashdata('success', 'Hasil ujian siswa berhasil dihapus');
        } catch (\Exception $e) {
            log_message('error', 'Error hapus hasil ujian: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat menghapus hasil ujian');
        }

        // Redirect kembali ke daftar siswa dengan jadwal_id yang sama
        return redirect()->to('guru/hasil-ujian/siswa/' . $peserta['jadwal_id']);
    }

    /**
     * Reset status ujian siswa (untuk siswa yang belum selesai)
     */
    public function resetStatusSiswa($pesertaUjianId)
    {
        $userId = session()->get('user_id');
        $guru = $this->guruModel->where('user_id', $userId)->first();

        // Validasi akses
        $peserta = $this->pesertaUjianModel
            ->select('peserta_ujian.*, jadwal_ujian.guru_id, jadwal_ujian.jadwal_id')
            ->join('jadwal_ujian', 'jadwal_ujian.jadwal_id = peserta_ujian.jadwal_id')
            ->where('peserta_ujian.peserta_ujian_id', $pesertaUjianId)
            ->first();

        if (!$peserta) {
            session()->setFlashdata('error', 'Data peserta ujian tidak ditemukan');
            return redirect()->back();
        }

        // Cek akses guru
        if ($peserta['guru_id'] != $guru['guru_id']) {
            session()->setFlashdata('error', 'Anda tidak memiliki akses untuk reset ujian ini');
            return redirect()->back();
        }

        try {
            $this->db->transStart();

            // 1. Hapus semua hasil ujian (jawaban) yang sudah ada
            $this->hasilUjianModel->where('peserta_ujian_id', $pesertaUjianId)->delete();

            // 2. Reset status peserta ujian
            $this->pesertaUjianModel->update($pesertaUjianId, [
                'status' => 'belum_mulai',
                'waktu_mulai' => null,
                'waktu_selesai' => null,
                'theta_akhir' => null,
                'se_akhir' => null
            ]);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Gagal reset status ujian');
            }

            session()->setFlashdata('success', 'Status ujian siswa berhasil direset. Siswa dapat mengulang ujian dari awal.');
        } catch (\Exception $e) {
            log_message('error', 'Error reset status ujian: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat reset status ujian');
        }

        // Redirect kembali ke daftar siswa dengan jadwal_id yang sama
        return redirect()->to('guru/hasil-ujian/siswa/' . $peserta['jadwal_id']);
    }

    public function daftarSiswa($jadwalId)
    {
        $db = \Config\Database::connect();

        // Ambil info ujian
        $ujian = $db->table('jadwal_ujian ju')
            ->select('ju.*, u.nama_ujian, u.deskripsi, u.kode_ujian, j.nama_jenis, k.nama_kelas, k.tahun_ajaran, 
                     s.nama_sekolah, g.nama_lengkap as nama_guru, ju.kode_akses,
                     DATE_FORMAT(ju.tanggal_mulai, "%d/%m/%Y %H:%i") as tanggal_mulai_format,
                     DATE_FORMAT(ju.tanggal_selesai, "%d/%m/%Y %H:%i") as tanggal_selesai_format')
            ->join('ujian u', 'u.id_ujian = ju.ujian_id', 'left')
            ->join('jenis_ujian j', 'j.jenis_ujian_id = u.jenis_ujian_id', 'left')
            ->join('kelas k', 'k.kelas_id = ju.kelas_id', 'left')
            ->join('sekolah s', 's.sekolah_id = k.sekolah_id', 'left')
            ->join('guru g', 'g.guru_id = ju.guru_id', 'left')
            ->where('ju.jadwal_id', $jadwalId)
            ->get()
            ->getRowArray();

        if (!$ujian) {
            session()->setFlashdata('error', 'Jadwal ujian tidak ditemukan');
            return redirect()->to(base_url('guru/hasil-ujian'));
        }

        // Validasi akses guru
        $userId = session()->get('user_id');
        $guru = $this->guruModel->where('user_id', $userId)->first();

        if (!$this->jadwalUjianModel->hasAccess($jadwalId, $guru['guru_id'])) {
            session()->setFlashdata('error', 'Anda tidak memiliki akses ke hasil ujian ini');
            return redirect()->to(base_url('guru/hasil-ujian'));
        }

        // Ambil hasil siswa
        $hasilSiswa = $db->table('peserta_ujian pu')
            ->select('pu.peserta_ujian_id, pu.status, pu.waktu_mulai, pu.waktu_selesai,
                     siswa.siswa_id, siswa.nama_lengkap, siswa.nomor_peserta, siswa.jenis_kelamin,
                     u.username,
                     TIMEDIFF(pu.waktu_selesai, pu.waktu_mulai) as durasi_pengerjaan,
                     TIME_TO_SEC(TIMEDIFF(pu.waktu_selesai, pu.waktu_mulai)) as durasi_detik,
                     DATE_FORMAT(pu.waktu_mulai, "%d/%m/%Y %H:%i:%s") as waktu_mulai_format,
                     DATE_FORMAT(pu.waktu_selesai, "%d/%m/%Y %H:%i:%s") as waktu_selesai_format')
            ->join('siswa', 'siswa.siswa_id = pu.siswa_id', 'left')
            ->join('users u', 'u.user_id = siswa.user_id', 'left')
            ->where('pu.jadwal_id', $jadwalId)
            ->orderBy('siswa.nama_lengkap', 'ASC')
            ->get()
            ->getResultArray();

        // Hitung nilai untuk setiap siswa
        foreach ($hasilSiswa as &$siswa) {
            if ($siswa['status'] === 'selesai') {
                $attempts = $this->getAttemptsForPeserta((int) $siswa['peserta_ujian_id']);
                $attempt = !empty($attempts) ? end($attempts) : null;
                $detailJawaban = $this->getAttemptAwareDetailJawaban((int) $siswa['peserta_ujian_id'], $attempt['attempt_id'] ?? null);
                $summary = $this->buildResultSummary($ujian, $detailJawaban, $attempt);

                $siswa['theta_akhir'] = $summary['is_cat_mode'] ? $summary['theta_akhir'] : null;
                $siswa['skor'] = $summary['skor_akhir'];
                $siswa['nilai'] = $summary['nilai_akhir'];
                $siswa['se_akhir'] = $summary['is_cat_mode'] ? $summary['se_akhir'] : null;
                $siswa['is_cat_mode'] = $summary['is_cat_mode'];

                $jawabanBenar = count(array_filter($detailJawaban, static fn($item) => (int) ($item['is_correct'] ?? 0) === 1));
                $totalSoal = count($detailJawaban);

                $siswa['jawaban_benar'] = $jawabanBenar;
                $siswa['total_soal'] = $totalSoal;
                $siswa['jumlah_attempt'] = count($attempts);
                $siswa['attempt_terakhir'] = $attempt['nomor_attempt'] ?? null;

                $siswa['kemampuan_kognitif'] = [
                    'skor' => $summary['skor_akhir'],
                    'total_benar' => $jawabanBenar,
                    'total_salah' => $totalSoal - $jawabanBenar,
                    'rata_rata_pilihan' => 0,
                ];
                $siswa['klasifikasi_kognitif'] = $summary['klasifikasi_kognitif'];

                if (!empty($attempt['waktu_mulai']) && !empty($attempt['waktu_selesai'])) {
                    $durasiDetik = strtotime($attempt['waktu_selesai']) - strtotime($attempt['waktu_mulai']);
                    $jam = floor($durasiDetik / 3600);
                    $menit = floor(($durasiDetik % 3600) / 60);
                    $detik = $durasiDetik % 60;
                    $siswa['durasi_format'] = sprintf('%02d:%02d:%02d', $jam, $menit, $detik);
                    $siswa['waktu_mulai_format'] = date('d/m/Y H:i:s', strtotime($attempt['waktu_mulai']));
                    $siswa['waktu_selesai_format'] = date('d/m/Y H:i:s', strtotime($attempt['waktu_selesai']));
                } else {
                    $siswa['durasi_format'] = '-';
                }
            } else {
                $siswa['theta_akhir'] = null;
                $siswa['skor'] = null;
                $siswa['nilai'] = null;
                $siswa['se_akhir'] = null;
                $siswa['is_cat_mode'] = ($ujian['tipe_ujian'] ?? 'CAT') === 'CAT';
                $siswa['jawaban_benar'] = 0;
                $siswa['total_soal'] = 0;
                $siswa['jumlah_attempt'] = 0;
                $siswa['attempt_terakhir'] = null;
                $siswa['kemampuan_kognitif'] = ['skor' => 0];
                $siswa['klasifikasi_kognitif'] = $this->getKlasifikasiKognitif(0);

                $siswa['durasi_format'] = '-';
            }
        }

        $data = [
            'ujian' => $ujian,
            'hasilSiswa' => $hasilSiswa
        ];

        return view('guru/daftar_siswa', $data);
    }

    public function daftarPercobaan($pesertaUjianId)
    {
        $peserta = $this->pesertaUjianModel
            ->select('peserta_ujian.*, jadwal_ujian.jadwal_id, ujian.nama_ujian, ujian.deskripsi, ujian.kode_ujian, ujian.tipe_ujian,
                jenis_ujian.nama_jenis, sekolah.nama_sekolah, guru.nama_lengkap as nama_guru,
                siswa.nama_lengkap, siswa.nomor_peserta, kelas.nama_kelas')
            ->join('jadwal_ujian', 'jadwal_ujian.jadwal_id = peserta_ujian.jadwal_id', 'left')
            ->join('ujian', 'ujian.id_ujian = jadwal_ujian.ujian_id', 'left')
            ->join('jenis_ujian', 'jenis_ujian.jenis_ujian_id = ujian.jenis_ujian_id', 'left')
            ->join('siswa', 'siswa.siswa_id = peserta_ujian.siswa_id', 'left')
            ->join('kelas', 'kelas.kelas_id = jadwal_ujian.kelas_id', 'left')
            ->join('sekolah', 'sekolah.sekolah_id = kelas.sekolah_id', 'left')
            ->join('guru', 'guru.guru_id = jadwal_ujian.guru_id', 'left')
            ->where('peserta_ujian.peserta_ujian_id', $pesertaUjianId)
            ->first();

        if (!$peserta) {
            session()->setFlashdata('error', 'Data peserta ujian tidak ditemukan');
            return redirect()->to(base_url('guru/hasil-ujian'));
        }

        $attempts = $this->getAttemptsForPeserta((int) $pesertaUjianId);
        foreach ($attempts as &$attempt) {
            $detailJawaban = $this->getAttemptAwareDetailJawaban($pesertaUjianId, (int) $attempt['attempt_id']);
            $summary = $this->buildResultSummary($peserta, $detailJawaban, $attempt);
            $jawabanBenar = count(array_filter($detailJawaban, static fn($item) => (int) ($item['is_correct'] ?? 0) === 1));
            $totalSoal = count($detailJawaban);

            $attempt['is_cat_mode'] = $summary['is_cat_mode'];
            $attempt['theta_akhir'] = $summary['theta_akhir'];
            $attempt['se_akhir'] = $summary['se_akhir'];
            $attempt['skor'] = $summary['skor_akhir'];
            $attempt['nilai'] = $summary['nilai_akhir'];
            $attempt['jawaban_benar'] = $jawabanBenar;
            $attempt['total_soal'] = $totalSoal;
            $attempt['klasifikasi_kognitif'] = $summary['klasifikasi_kognitif'];
            $attempt['waktu_mulai_format'] = !empty($attempt['waktu_mulai']) ? date('d/m/Y H:i:s', strtotime($attempt['waktu_mulai'])) : '-';
            $attempt['waktu_selesai_format'] = !empty($attempt['waktu_selesai']) ? date('d/m/Y H:i:s', strtotime($attempt['waktu_selesai'])) : '-';

            if (!empty($attempt['waktu_mulai']) && !empty($attempt['waktu_selesai'])) {
                $durasiDetik = strtotime($attempt['waktu_selesai']) - strtotime($attempt['waktu_mulai']);
                $attempt['durasi_format'] = sprintf('%02d:%02d:%02d', floor($durasiDetik / 3600), floor(($durasiDetik % 3600) / 60), $durasiDetik % 60);
            } else {
                $attempt['durasi_format'] = '-';
            }
        }
        unset($attempt);

        return view('guru/hasil_percobaan', [
            'peserta' => $peserta,
            'attempts' => $attempts,
        ]);
    }

    public function detailHasil($pesertaUjianId)
    {
        $requestedAttemptId = (int) $this->request->getGet('attempt_id');
        $attempt = $requestedAttemptId > 0
            ? $this->getAttemptByIdForPeserta((int) $pesertaUjianId, $requestedAttemptId)
            : $this->getLatestAttemptForPeserta((int) $pesertaUjianId);

        // Ambil detail hasil ujian dengan informasi waktu
        $hasil = $this->pesertaUjianModel
            ->select('peserta_ujian.*, jadwal_ujian.*, ujian.*, ujian.kode_ujian, jenis_ujian.nama_jenis, sekolah.nama_sekolah, guru.nama_lengkap as nama_guru,
            siswa.nama_lengkap, siswa.nomor_peserta, kelas.nama_kelas,
            TIMEDIFF(peserta_ujian.waktu_selesai, peserta_ujian.waktu_mulai) as durasi_total,
            TIME_TO_SEC(TIMEDIFF(peserta_ujian.waktu_selesai, peserta_ujian.waktu_mulai)) as durasi_total_detik,
            DATE_FORMAT(peserta_ujian.waktu_mulai, "%d/%m/%Y %H:%i:%s") as waktu_mulai_format,
            DATE_FORMAT(peserta_ujian.waktu_selesai, "%d/%m/%Y %H:%i:%s") as waktu_selesai_format')
            ->join('jadwal_ujian', 'jadwal_ujian.jadwal_id = peserta_ujian.jadwal_id')
            ->join('ujian', 'ujian.id_ujian = jadwal_ujian.ujian_id')
            ->join('jenis_ujian', 'jenis_ujian.jenis_ujian_id = ujian.jenis_ujian_id')
            ->join('siswa', 'siswa.siswa_id = peserta_ujian.siswa_id')
            ->join('kelas', 'kelas.kelas_id = jadwal_ujian.kelas_id')
            ->join('sekolah', 'sekolah.sekolah_id = kelas.sekolah_id')
            ->join('guru', 'guru.guru_id = jadwal_ujian.guru_id')
            ->where('peserta_ujian.peserta_ujian_id', $pesertaUjianId)
            ->first();

        if ($attempt) {
            $hasil['attempt_id'] = $attempt['attempt_id'];
            $hasil['nomor_attempt'] = $attempt['nomor_attempt'];
            $hasil['nilai_akhir'] = $attempt['nilai_akhir'];
            if (!empty($attempt['waktu_mulai'])) {
                $hasil['waktu_mulai'] = $attempt['waktu_mulai'];
                $hasil['waktu_mulai_format'] = date('d/m/Y H:i:s', strtotime($attempt['waktu_mulai']));
            }
            if (!empty($attempt['waktu_selesai'])) {
                $hasil['waktu_selesai'] = $attempt['waktu_selesai'];
                $hasil['waktu_selesai_format'] = date('d/m/Y H:i:s', strtotime($attempt['waktu_selesai']));
            }
            if (!empty($attempt['waktu_mulai']) && !empty($attempt['waktu_selesai'])) {
                $hasil['durasi_total_detik'] = strtotime($attempt['waktu_selesai']) - strtotime($attempt['waktu_mulai']);
            }
        }

        $detailJawaban = $this->getAttemptAwareDetailJawaban($pesertaUjianId, $attempt['attempt_id'] ?? null);

        // Hitung durasi per soal
        $detailJawabanDenganDurasi = $this->hitungDurasiPerSoal($detailJawaban, $hasil['waktu_mulai']);

        // Hitung statistik
        $totalSoal = count($detailJawabanDenganDurasi);
        $jawabanBenar = array_reduce($detailJawabanDenganDurasi, function ($carry, $item) {
            return $carry + ($item['is_correct'] ? 1 : 0);
        }, 0);

        $summary = $this->buildResultSummary($hasil, $detailJawabanDenganDurasi, $attempt);
        $skor_akhir = $summary['skor_akhir'];
        $klasifikasiKognitif = $summary['klasifikasi_kognitif'];

        $kemampuanKognitif = [
            'skor' => $skor_akhir,
            'total_benar' => $jawabanBenar,
            'total_salah' => $totalSoal - $jawabanBenar,
            'rata_rata_pilihan' => 0
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

        return view('guru/detail_hasil', [
            'hasil' => $hasil,
            'detailJawaban' => $detailJawabanDenganDurasi,
            'totalSoal' => $totalSoal,
            'jawabanBenar' => $jawabanBenar,
            'isCatMode' => $summary['is_cat_mode'],
            'thetaAkhir' => $summary['theta_akhir'],
            'seAkhir' => $summary['se_akhir'],
            'finalScore' => $summary['skor_akhir'],
            'finalGrade' => $summary['nilai_akhir'],
            'kemampuanKognitif' => $kemampuanKognitif,
            'klasifikasiKognitif' => $klasifikasiKognitif,
            'rataRataWaktuFormat' => sprintf('%d menit %d detik', $rataRataMenit, $rataRataDetik),
            'backUrl' => base_url('guru/hasil-ujian/percobaan/' . $pesertaUjianId),
            'statistikWaktu' => [
                'waktu_tercepat' => $totalSoal > 0 ? min(array_column($detailJawabanDenganDurasi, 'durasi_pengerjaan_detik')) : 0,
                'waktu_terlama' => $totalSoal > 0 ? max(array_column($detailJawabanDenganDurasi, 'durasi_pengerjaan_detik')) : 0,
                'rata_rata' => $rataRataWaktu
            ]
        ]);
    }

    // ===== KELOLA PENGUMUMAN =====

    public function pengumuman()
    {
        $data['pengumuman'] = $this->pengumumanModel->getPengumumanWithUser();
        return view('guru/pengumuman', $data);
    }

    public function tambahPengumuman()
    {
        $data = [
            'judul' => $this->request->getPost('judul'),
            'isi_pengumuman' => $this->request->getPost('isi_pengumuman'),
            'tanggal_publish' => $this->request->getPost('tanggal_publish'),
            'tanggal_berakhir' => $this->request->getPost('tanggal_berakhir'),
            'created_by' => session()->get('user_id')
        ];
        $this->pengumumanModel->insert($data);
        return redirect()->to('guru/pengumuman')->with('success', 'Pengumuman berhasil ditambahkan');
    }

    public function editPengumuman($id)
    {
        $data = [
            'judul' => $this->request->getPost('judul'),
            'isi_pengumuman' => $this->request->getPost('isi_pengumuman'),
            'tanggal_publish' => $this->request->getPost('tanggal_publish'),
            'tanggal_berakhir' => $this->request->getPost('tanggal_berakhir')
        ];
        $this->pengumumanModel->update($id, $data);
        return redirect()->to('guru/pengumuman')->with('success', 'Pengumuman berhasil diupdate');
    }

    public function hapusPengumuman($id)
    {
        $this->pengumumanModel->delete($id);
        return redirect()->to('guru/pengumuman')->with('success', 'Pengumuman berhasil dihapus');
    }

    // ===== KELOLA PROFIL =====

    public function profil()
    {
        $userId = session()->get('user_id');

        // Ambil data guru dengan join ke users dan sekolah
        $guru = $this->guruModel
            ->select('guru.*, users.username, users.email, sekolah.nama_sekolah')
            ->join('users', 'users.user_id = guru.user_id')
            ->join('sekolah', 'sekolah.sekolah_id = guru.sekolah_id')
            ->where('guru.user_id', $userId)
            ->first();

        $data = [
            'guru' => $guru,
            'validation' => \Config\Services::validation()
        ];

        return view('guru/profil', $data);
    }

    public function saveProfil()
    {
        $userId = session()->get('user_id');
        $guru = $this->guruModel->where('user_id', $userId)->first();

        // Validasi input
        $rules = [
            'nama_lengkap' => 'required|min_length[3]',
            'nip' => 'required|min_length[5]',
            'mata_pelajaran' => 'required',
            'email' => 'required|valid_email'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // Update data users
        $this->db->table('users')->where('user_id', $userId)->update([
            'email' => $this->request->getPost('email')
        ]);

        // Update data guru
        $dataGuru = [
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'nip' => $this->request->getPost('nip'),
            'mata_pelajaran' => $this->request->getPost('mata_pelajaran')
        ];

        try {
            $this->guruModel->update($guru['guru_id'], $dataGuru);
            session()->setFlashdata('success', 'Profil berhasil diperbarui!');
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Terjadi kesalahan saat menyimpan data.');
            log_message('error', $e->getMessage());
        }

        return redirect()->to(base_url('guru/profil'));
    }

    // ===== KELOLA JENIS UJIAN/MATA PELAJARAN =====

    public function jenisUjian()
    {
        $userId = session()->get('user_id');
        $guru = $this->guruModel->where('user_id', $userId)->first();

        $data['jenis_ujian'] = $this->jenisUjianModel->getByKelasGuru($guru['guru_id']);

        // Ambil kelas yang diajar guru untuk dropdown - pastikan selalu array
        $kelasGuru = $this->jenisUjianModel->getAvailableKelasForGuru($guru['guru_id']);
        $data['kelas_guru'] = $kelasGuru ?? []; // Pastikan selalu array, bahkan jika null

        return view('guru/jenis_ujian', $data);
    }

    public function tambahJenisUjian()
    {
        $userId = session()->get('user_id');
        $guru = $this->guruModel->where('user_id', $userId)->first();

        // CEK: Pastikan guru memiliki assignment kelas
        $kelasGuru = $this->db->table('kelas_guru')
            ->where('guru_id', $guru['guru_id'])
            ->countAllResults();

        if ($kelasGuru == 0) {
            return redirect()->to('guru/jenis-ujian')
                ->with('error', 'Anda belum di-assign ke kelas manapun. Silakan hubungi admin untuk mendapatkan assignment kelas terlebih dahulu.');
        }

        $kelasId = $this->normalizeNullableId($this->request->getPost('kelas_id'));

        // Validasi kelas (pastikan guru mengajar kelas yang dipilih)
        if ($kelasId !== null) {
            $kelasAccess = $this->db->table('kelas_guru')
                ->where('guru_id', $guru['guru_id'])
                ->where('kelas_id', $kelasId)
                ->get()->getRowArray();

            if (!$kelasAccess) {
                return redirect()->to('guru/jenis-ujian')
                    ->with('error', 'Anda tidak memiliki akses untuk menambahkan Mata Pelajaran pada kelas tersebut.');
            }
        }

        $data = [
            'nama_jenis' => $this->request->getPost('nama_jenis'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'kelas_id' => $kelasId,
            'created_by' => $userId
        ];

        try {
            $this->jenisUjianModel->insert($data);
            return redirect()->to('guru/jenis-ujian')->with('success', 'Mata Pelajaran berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->to('guru/jenis-ujian')->with('error', 'Gagal menambahkan Mata Pelajaran: ' . $e->getMessage());
        }
    }


    public function hapusJenisUjian($id)
    {
        $userId = session()->get('user_id');
        $guru = $this->guruModel->where('user_id', $userId)->first();

        // Cek akses ke Mata Pelajaran ini
        if (!$this->jenisUjianModel->hasAccess($id, $guru['guru_id'])) {
            return redirect()->to('guru/jenis-ujian')
                ->with('error', 'Anda tidak memiliki akses untuk menghapus Mata Pelajaran ini.');
        }

        // Cek apakah ada ujian yang menggunakan Mata Pelajaran ini
        $ujianTerkait = $this->db->table('ujian')
            ->where('jenis_ujian_id', $id)
            ->countAllResults();

        if ($ujianTerkait > 0) {
            return redirect()->to('guru/jenis-ujian')
                ->with('error', 'Tidak dapat menghapus Mata Pelajaran ini karena masih ada ' . $ujianTerkait . ' ujian yang menggunakan Mata Pelajaran ini. Harap hapus ujian terkait terlebih dahulu.');
        }

        try {
            $this->jenisUjianModel->delete($id);
            return redirect()->to('guru/jenis-ujian')
                ->with('success', 'Mata Pelajaran berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->to('guru/jenis-ujian')
                ->with('error', 'Terjadi kesalahan saat menghapus Mata Pelajaran');
        }
    }

    // ===== KELOLA DOWNLOAD HASIL UJIAN =====

    public function downloadExcelHTML($pesertaUjianId)
    {
        $requestedAttemptId = (int) $this->request->getGet('attempt_id');
        $attempt = $requestedAttemptId > 0
            ? $this->getAttemptByIdForPeserta((int) $pesertaUjianId, $requestedAttemptId)
            : $this->getLatestAttemptForPeserta((int) $pesertaUjianId);

        $hasil = $this->pesertaUjianModel
            ->select('peserta_ujian.*, jadwal_ujian.*, ujian.*, ujian.kode_ujian, jenis_ujian.nama_jenis, sekolah.nama_sekolah, guru.nama_lengkap as nama_guru,
            siswa.nama_lengkap, siswa.nomor_peserta, kelas.nama_kelas,
            TIMEDIFF(peserta_ujian.waktu_selesai, peserta_ujian.waktu_mulai) as durasi_total,
            TIME_TO_SEC(TIMEDIFF(peserta_ujian.waktu_selesai, peserta_ujian.waktu_mulai)) as durasi_total_detik,
            DATE_FORMAT(peserta_ujian.waktu_mulai, "%d/%m/%Y %H:%i:%s") as waktu_mulai_format,
            DATE_FORMAT(peserta_ujian.waktu_selesai, "%d/%m/%Y %H:%i:%s") as waktu_selesai_format')
            ->join('jadwal_ujian', 'jadwal_ujian.jadwal_id = peserta_ujian.jadwal_id')
            ->join('ujian', 'ujian.id_ujian = jadwal_ujian.ujian_id')
            ->join('jenis_ujian', 'jenis_ujian.jenis_ujian_id = ujian.jenis_ujian_id')
            ->join('siswa', 'siswa.siswa_id = peserta_ujian.siswa_id')
            ->join('kelas', 'kelas.kelas_id = jadwal_ujian.kelas_id')
            ->join('sekolah', 'sekolah.sekolah_id = kelas.sekolah_id')
            ->join('guru', 'guru.guru_id = jadwal_ujian.guru_id')
            ->where('peserta_ujian.peserta_ujian_id', $pesertaUjianId)
            ->first();

        if ($attempt) {
            $hasil['attempt_id'] = $attempt['attempt_id'];
            $hasil['nomor_attempt'] = $attempt['nomor_attempt'];
            $hasil['nilai_akhir'] = $attempt['nilai_akhir'];
            if (!empty($attempt['waktu_mulai'])) {
                $hasil['waktu_mulai'] = $attempt['waktu_mulai'];
                $hasil['waktu_mulai_format'] = date('d/m/Y H:i:s', strtotime($attempt['waktu_mulai']));
            }
            if (!empty($attempt['waktu_selesai'])) {
                $hasil['waktu_selesai'] = $attempt['waktu_selesai'];
                $hasil['waktu_selesai_format'] = date('d/m/Y H:i:s', strtotime($attempt['waktu_selesai']));
            }
            if (!empty($attempt['waktu_mulai']) && !empty($attempt['waktu_selesai'])) {
                $hasil['durasi_total_detik'] = strtotime($attempt['waktu_selesai']) - strtotime($attempt['waktu_mulai']);
            }
        }

        $detailJawaban = $this->getAttemptAwareDetailJawaban($pesertaUjianId, $attempt['attempt_id'] ?? null);

        $detailJawabanDenganDurasi = $this->hitungDurasiPerSoal($detailJawaban, $hasil['waktu_mulai']);
        $totalSoal = count($detailJawabanDenganDurasi);
        $jawabanBenar = array_reduce($detailJawabanDenganDurasi, function ($carry, $item) {
            return $carry + ($item['is_correct'] ? 1 : 0);
        }, 0);

        $summary = $this->buildResultSummary($hasil, $detailJawabanDenganDurasi, $attempt);
        $theta_akhir = $summary['theta_akhir'];
        $skor_akhir = $summary['skor_akhir'];
        $klasifikasiKognitif = $summary['klasifikasi_kognitif'];

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
        }

        $rataRataWaktu = $totalSoal > 0 ? ($hasil['durasi_total_detik'] / $totalSoal) : 0;
        $rataRataMenit = floor($rataRataWaktu / 60);
        $rataRataDetik = $rataRataWaktu % 60;

        $data = [
            'hasil' => $hasil,
            'detailJawaban' => $detailJawabanDenganDurasi,
            'isCatMode' => $summary['is_cat_mode'],
            'finalScore' => $skor_akhir,
            'lastTheta' => $theta_akhir,
            'finalGrade' => $summary['nilai_akhir'],
            'seAkhir' => $summary['se_akhir'],
            'jawabanBenar' => $jawabanBenar,
            'kemampuanKognitif' => $kemampuanKognitif,
            'klasifikasiKognitif' => $klasifikasiKognitif,
            'rataRataWaktuFormat' => sprintf('%d menit %d detik', $rataRataMenit, $rataRataDetik),
        ];

        $filename = 'hasil_ujian_' . $hasil['nomor_peserta'] . '_' . date('dmY') . '.xls';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        echo view('guru/hasil_ujian_excel', $data); // Pastikan nama view ini benar
        exit;
    }

    public function downloadPDFHTML($pesertaUjianId)
    {
        $requestedAttemptId = (int) $this->request->getGet('attempt_id');
        $attempt = $requestedAttemptId > 0
            ? $this->getAttemptByIdForPeserta((int) $pesertaUjianId, $requestedAttemptId)
            : $this->getLatestAttemptForPeserta((int) $pesertaUjianId);

        $hasil = $this->pesertaUjianModel
            ->select('peserta_ujian.*, jadwal_ujian.*, ujian.*, ujian.kode_ujian, jenis_ujian.nama_jenis, sekolah.nama_sekolah, guru.nama_lengkap as nama_guru,
            siswa.nama_lengkap, siswa.nomor_peserta, kelas.nama_kelas,
            TIMEDIFF(peserta_ujian.waktu_selesai, peserta_ujian.waktu_mulai) as durasi_total,
            TIME_TO_SEC(TIMEDIFF(peserta_ujian.waktu_selesai, peserta_ujian.waktu_mulai)) as durasi_total_detik,
            DATE_FORMAT(peserta_ujian.waktu_mulai, "%d/%m/%Y %H:%i:%s") as waktu_mulai_format,
            DATE_FORMAT(peserta_ujian.waktu_selesai, "%d/%m/%Y %H:%i:%s") as waktu_selesai_format')
            ->join('jadwal_ujian', 'jadwal_ujian.jadwal_id = peserta_ujian.jadwal_id')
            ->join('ujian', 'ujian.id_ujian = jadwal_ujian.ujian_id')
            ->join('jenis_ujian', 'jenis_ujian.jenis_ujian_id = ujian.jenis_ujian_id')
            ->join('siswa', 'siswa.siswa_id = peserta_ujian.siswa_id')
            ->join('kelas', 'kelas.kelas_id = jadwal_ujian.kelas_id')
            ->join('sekolah', 'sekolah.sekolah_id = kelas.sekolah_id')
            ->join('guru', 'guru.guru_id = jadwal_ujian.guru_id')
            ->where('peserta_ujian.peserta_ujian_id', $pesertaUjianId)
            ->first();

        if ($attempt) {
            $hasil['attempt_id'] = $attempt['attempt_id'];
            $hasil['nomor_attempt'] = $attempt['nomor_attempt'];
            $hasil['nilai_akhir'] = $attempt['nilai_akhir'];
            if (!empty($attempt['waktu_mulai'])) {
                $hasil['waktu_mulai'] = $attempt['waktu_mulai'];
                $hasil['waktu_mulai_format'] = date('d/m/Y H:i:s', strtotime($attempt['waktu_mulai']));
            }
            if (!empty($attempt['waktu_selesai'])) {
                $hasil['waktu_selesai'] = $attempt['waktu_selesai'];
                $hasil['waktu_selesai_format'] = date('d/m/Y H:i:s', strtotime($attempt['waktu_selesai']));
            }
            if (!empty($attempt['waktu_mulai']) && !empty($attempt['waktu_selesai'])) {
                $hasil['durasi_total_detik'] = strtotime($attempt['waktu_selesai']) - strtotime($attempt['waktu_mulai']);
            }
        }

        $detailJawaban = $this->getAttemptAwareDetailJawaban($pesertaUjianId, $attempt['attempt_id'] ?? null);

        $detailJawabanDenganDurasi = $this->hitungDurasiPerSoal($detailJawaban, $hasil['waktu_mulai']);
        $totalSoal = count($detailJawabanDenganDurasi);
        $jawabanBenar = array_reduce($detailJawabanDenganDurasi, function ($carry, $item) {
            return $carry + ($item['is_correct'] ? 1 : 0);
        }, 0);

        $summary = $this->buildResultSummary($hasil, $detailJawabanDenganDurasi, $attempt);
        $theta_akhir = $summary['theta_akhir'];
        $skor_akhir = $summary['skor_akhir'];
        $klasifikasiKognitif = $summary['klasifikasi_kognitif'];

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
        }

        $rataRataWaktu = $totalSoal > 0 ? ($hasil['durasi_total_detik'] / $totalSoal) : 0;
        $rataRataMenit = floor($rataRataWaktu / 60);
        $rataRataDetik = $rataRataWaktu % 60;

        $data = [
            'hasil' => $hasil,
            'detailJawaban' => $detailJawabanDenganDurasi,
            'isCatMode' => $summary['is_cat_mode'],
            'finalScore' => $skor_akhir,
            'lastTheta' => $theta_akhir,
            'finalGrade' => $summary['nilai_akhir'],
            'seAkhir' => $summary['se_akhir'],
            'jawabanBenar' => $jawabanBenar,
            'kemampuanKognitif' => $kemampuanKognitif,
            'klasifikasiKognitif' => $klasifikasiKognitif,
            'rataRataWaktuFormat' => sprintf('%d menit %d detik', $rataRataMenit, $rataRataDetik),
        ];

        $html = view('guru/hasil_ujian_pdf', $data);

        // Opsi untuk download atau inline view
        header('Content-Type: text/html');
        header('Content-Disposition: inline; filename="hasil_ujian_' . $hasil['nomor_peserta'] . '.html"');
        echo $html;
        exit;
    }

    // ===== KELOLA BANK SOAL =====

    public function bankSoal()
    {
        $userId = session()->get('user_id');
        $guru = $this->guruModel->where('user_id', $userId)->first();

        $kelasGuru = $this->db->table('kelas_guru')
            ->select('kelas.kelas_id, kelas.nama_kelas, sekolah.nama_sekolah')
            ->join('kelas', 'kelas.kelas_id = kelas_guru.kelas_id')
            ->join('sekolah', 'sekolah.sekolah_id = kelas.sekolah_id', 'left')
            ->where('kelas_guru.guru_id', $guru['guru_id'])
            ->get()->getResultArray();

        $sekolahModel = new \App\Models\SekolahModel();

        $data = [
            'kelasGuru'      => $kelasGuru,
            'jenisUjianList' => $this->jenisUjianModel->findAll(),
            'sekolah'        => $sekolahModel->orderBy('nama_sekolah', 'ASC')->findAll(),
        ];

        return view('guru/bank_soal/index', $data);
    }

    public function getKelasBySekolah($sekolahId)
    {
        $userId = session()->get('user_id');
        $guru = $this->guruModel->where('user_id', $userId)->first();
        if (!$guru) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Guru tidak ditemukan']);
        }

        $kelas = $this->db->table('kelas')
            ->select('kelas_id, nama_kelas, tahun_ajaran')
            ->join('kelas_guru', 'kelas_guru.kelas_id = kelas.kelas_id')
            ->where('sekolah_id', $sekolahId)
            ->where('kelas_guru.guru_id', $guru['guru_id'])
            ->orderBy('tahun_ajaran', 'DESC')
            ->orderBy('nama_kelas', 'ASC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON(['status' => 'success', 'data' => $kelas]);
    }


    public function tambahBankSoal()
    {
        $data = [
            'kategori' => $this->request->getPost('kategori'), // 'umum' atau nama kelas
            'jenis_ujian_id' => $this->request->getPost('jenis_ujian_id'),
            'nama_ujian' => $this->request->getPost('nama_ujian'),
            'deskripsi' => $this->request->getPost('deskripsi')
        ];

        // Cek apakah kombinasi kategori + jenis_ujian + nama_ujian sudah ada
        $existing = $this->db->table('bank_ujian')
            ->where('kategori', $data['kategori'])
            ->where('jenis_ujian_id', $data['jenis_ujian_id'])
            ->where('nama_ujian', $data['nama_ujian'])
            ->where('created_by', session()->get('user_id'))
            ->get()->getRowArray();

        if ($existing) {
            return redirect()->back()->with('error', 'Bank soal dengan kategori, Mata Pelajaran, dan nama ujian yang sama sudah ada.');
        }

        // Insert ke tabel bank_ujian (kita perlu buat tabel baru ini)
        $bankUjianData = [
            'kategori' => $data['kategori'],
            'jenis_ujian_id' => $data['jenis_ujian_id'],
            'nama_ujian' => $data['nama_ujian'],
            'deskripsi' => $data['deskripsi'],
            'created_by' => session()->get('user_id'),
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->db->table('bank_ujian')->insert($bankUjianData);

        return redirect()->to('guru/bank-soal')->with('success', 'Bank soal berhasil ditambahkan');
    }

    public function bankSoalKategori($kategori)
    {
        $userId = session()->get('user_id');
        $guru = $this->guruModel->where('user_id', $userId)->first();

        // Validasi akses kategori
        $aksesKelas = null;
        if ($kategori !== 'umum') {
            // Cek apakah guru mengajar kelas ini
            $aksesKelas = $this->db->table('kelas_guru')
                ->join('kelas', 'kelas.kelas_id = kelas_guru.kelas_id')
                ->join('sekolah', 'sekolah.sekolah_id = kelas.sekolah_id', 'left')
                ->where('kelas_guru.guru_id', $guru['guru_id'])
                ->where('kelas.nama_kelas', $kategori)
                ->get()->getRowArray();

            if (!$aksesKelas) {
                return redirect()->to('guru/bank-soal')->with('error', 'Anda tidak memiliki akses ke kategori ini');
            }
        }

        // Ambil Mata Pelajaran yang ada bank soalnya untuk kategori ini
        $jenisUjianList = $this->db->table('bank_ujian')
            ->select('bank_ujian.jenis_ujian_id, jenis_ujian.nama_jenis, COUNT(*) as jumlah_ujian')
            ->join('jenis_ujian', 'jenis_ujian.jenis_ujian_id = bank_ujian.jenis_ujian_id')
            ->where('bank_ujian.kategori', $kategori);

        if ($kategori === 'umum') {
            // Untuk kategori umum, tampilkan semua
            $jenisUjianList = $jenisUjianList->groupBy('bank_ujian.jenis_ujian_id');
        } else {
            // Untuk kategori kelas, hanya yang dibuat oleh guru ini
            $jenisUjianList = $jenisUjianList
                ->where('bank_ujian.created_by', $userId)
                ->groupBy('bank_ujian.jenis_ujian_id');
        }

        $jenisUjianList = $jenisUjianList->get()->getResultArray();

        $kategoriSekolahList = [];
        if ($kategori !== 'umum') {
            if ($aksesKelas && !empty($aksesKelas['nama_sekolah'])) {
                $kategoriSekolahList[] = ['nama_sekolah' => $aksesKelas['nama_sekolah']];
            } else {
                $kategoriSekolahList = $this->db->table('bank_ujian')
                    ->distinct()
                    ->select('sekolah.nama_sekolah')
                    ->join('jenis_ujian', 'jenis_ujian.jenis_ujian_id = bank_ujian.jenis_ujian_id', 'left')
                    ->join('kelas', 'kelas.kelas_id = jenis_ujian.kelas_id', 'left')
                    ->join('sekolah', 'sekolah.sekolah_id = kelas.sekolah_id', 'left')
                    ->where('bank_ujian.kategori', $kategori)
                    ->where('bank_ujian.created_by', $userId)
                    ->where('sekolah.nama_sekolah IS NOT NULL', null, false)
                    ->orderBy('sekolah.nama_sekolah', 'ASC')
                    ->get()
                    ->getResultArray();
            }
        }

        $data = [
            'kategori' => $kategori,
            'jenisUjianList' => $jenisUjianList,
            'kategoriSekolahList' => $kategoriSekolahList,
        ];

        return view('guru/bank_soal/kategori', $data);
    }

    public function bankSoalJenisUjian($kategori, $jenisUjianId)
    {
        $userId = session()->get('user_id');

        // Ambil daftar ujian dalam Mata Pelajaran dan kategori ini
        $ujianList = $this->db->table('bank_ujian')
            ->select('bank_ujian.*, users.username as creator_name, sekolah.nama_sekolah,
                (SELECT COUNT(*) FROM soal_ujian WHERE soal_ujian.bank_ujian_id = bank_ujian.bank_ujian_id AND soal_ujian.is_bank_soal = 1) as jumlah_soal')
            ->join('users', 'users.user_id = bank_ujian.created_by')
            ->join('jenis_ujian', 'jenis_ujian.jenis_ujian_id = bank_ujian.jenis_ujian_id', 'left')
            ->join('kelas', 'kelas.kelas_id = jenis_ujian.kelas_id', 'left')
            ->join('sekolah', 'sekolah.sekolah_id = kelas.sekolah_id', 'left')
            ->where('bank_ujian.kategori', $kategori)
            ->where('bank_ujian.jenis_ujian_id', $jenisUjianId);

        if ($kategori !== 'umum') {
            $ujianList = $ujianList->where('bank_ujian.created_by', $userId);
        }

        $ujianList = $ujianList->get()->getResultArray();

        $kategoriSekolahList = [];
        if ($kategori !== 'umum') {
            $kategoriSekolahList = $this->db->table('bank_ujian')
                ->distinct()
                ->select('sekolah.nama_sekolah')
                ->join('jenis_ujian', 'jenis_ujian.jenis_ujian_id = bank_ujian.jenis_ujian_id', 'left')
                ->join('kelas', 'kelas.kelas_id = jenis_ujian.kelas_id', 'left')
                ->join('sekolah', 'sekolah.sekolah_id = kelas.sekolah_id', 'left')
                ->where('bank_ujian.kategori', $kategori)
                ->where('bank_ujian.created_by', $userId)
                ->where('sekolah.nama_sekolah IS NOT NULL', null, false)
                ->orderBy('sekolah.nama_sekolah', 'ASC')
                ->get()
                ->getResultArray();
        }

        // Ambil info Mata Pelajaran
        $jenisUjian = $this->jenisUjianModel->find($jenisUjianId);

        $data = [
            'kategori' => $kategori,
            'jenisUjian' => $jenisUjian,
            'ujianList' => $ujianList,
            'kategoriSekolahList' => $kategoriSekolahList,
        ];

        return view('guru/bank_soal/jenis_ujian', $data);
    }

    public function bankSoalUjian($kategori, $jenisUjianId, $bankUjianId)
    {
        $userId = session()->get('user_id');

        // Ambil info bank ujian
        $bankUjian = $this->db->table('bank_ujian')
            ->select('bank_ujian.*, jenis_ujian.nama_jenis')
            ->join('jenis_ujian', 'jenis_ujian.jenis_ujian_id = bank_ujian.jenis_ujian_id')
            ->where('bank_ujian.bank_ujian_id', $bankUjianId)
            ->get()->getRowArray();

        if (!$bankUjian) {
            return redirect()->to('guru/bank-soal')->with('error', 'Bank ujian tidak ditemukan');
        }

        // Cek akses
        if ($kategori !== 'umum' && $bankUjian['created_by'] != $userId) {
            return redirect()->to('guru/bank-soal')->with('error', 'Anda tidak memiliki akses ke bank soal ini');
        }

        // Ambil soal-soal dalam bank ujian ini
        $soalList = $this->soalUjianModel
            ->where('bank_ujian_id', $bankUjianId)
            ->where('is_bank_soal', true)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        $data = [
            'kategori' => $kategori,
            'bankUjian' => $bankUjian,
            'soalList' => $soalList,
            'canEdit' => ($bankUjian['created_by'] == $userId || $kategori === 'umum')
        ];

        return view('guru/bank_soal/ujian', $data);
    }

    public function tambahSoalBankUjian()
    {
        $bankUjianId = $this->request->getPost('bank_ujian_id');
        $userId = session()->get('user_id');
        $isAjax = $this->request->isAJAX();

        // Validasi akses
        $bankUjian = $this->db->table('bank_ujian')->where('bank_ujian_id', $bankUjianId)->get()->getRowArray();
        if (!$bankUjian || ($bankUjian['kategori'] !== 'umum' && $bankUjian['created_by'] != $userId)) {
            if ($isAjax) {
                return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Anda tidak memiliki akses untuk menambah soal ke bank soal ini']);
            }
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menambah soal ke bank soal ini');
        }

        // Validasi form input
        $rules = [
            'kode_soal' => 'required|alpha_numeric_punct|min_length[3]|max_length[50]',
            'pertanyaan' => 'required',
            'pilihan_a' => 'required',
            'pilihan_b' => 'required',
            'pilihan_c' => 'required',
            'pilihan_d' => 'required',
            'jawaban_benar' => 'required|in_list[A,B,C,D,E]',
            'tingkat_kesulitan' => 'required|decimal',
            'variabel_id' => 'permit_empty|numeric',
            'indikator_id' => 'permit_empty|numeric',
            'materi_id' => 'permit_empty|numeric',
            'media' => 'permit_empty|max_size[media,2048]|mime_in[media,image/jpg,image/jpeg,image/png]|ext_in[media,png,jpg,jpeg]',
            'pembahasan' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $errorMessage = 'Validasi gagal: ' . implode(', ', $errors);
            if ($isAjax) {
                return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => $errorMessage, 'errors' => $errors]);
            }
            return redirect()->back()->withInput()->with('error', $errorMessage);
        }

        // Ambil data dari form
        $data = [
            'ujian_id' => null,
            'bank_ujian_id' => $bankUjianId,
            'is_bank_soal' => true,
            'created_by' => $userId,
            'kode_soal' => $this->request->getPost('kode_soal'),
            'pertanyaan' => $this->request->getPost('pertanyaan'),
            'pilihan_a' => $this->request->getPost('pilihan_a'),
            'pilihan_b' => $this->request->getPost('pilihan_b'),
            'pilihan_c' => $this->request->getPost('pilihan_c'),
            'pilihan_d' => $this->request->getPost('pilihan_d'),
            'pilihan_e' => $this->request->getPost('pilihan_e'),
            'jawaban_benar' => $this->request->getPost('jawaban_benar'),
            'tingkat_kesulitan' => $this->request->getPost('tingkat_kesulitan'), // PERBAIKI TYPO
            'a' => $this->request->getPost('a') ?: 1.000,
            'c' => $this->request->getPost('c') ?: 0.000,
            'variabel_id' => $this->request->getPost('variabel_id') ?: null,
            'indikator_id' => $this->request->getPost('indikator_id') ?: null,
            'materi_id' => $this->request->getPost('materi_id') ?: null,
            'pembahasan' => $this->request->getPost('pembahasan')
        ];

        // Upload foto jika ada
        try {
            $this->soalUjianModel->insert($data);
            if ($isAjax) {
                $jumlahSoal = $this->soalUjianModel->where(['bank_ujian_id' => $bankUjianId, 'is_bank_soal' => 1])->countAllResults();
                return $this->response->setJSON(['success' => true, 'message' => 'Soal berhasil ditambahkan ke bank soal', 'jumlah_soal' => $jumlahSoal]);
            }
            return redirect()->back()->with('success', 'Soal berhasil ditambahkan ke bank soal');
        } catch (\Exception $e) {
            log_message('error', 'Error saat menambahkan soal bank ujian: ' . $e->getMessage());
            if ($isAjax) {
                return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Terjadi kesalahan saat menyimpan soal: ' . $e->getMessage()]);
            }
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan soal: ' . $e->getMessage());
        }
    }

    public function editSoalBankUjian($soalId)
    {
        $userId = session()->get('user_id');

        // Ambil data soal
        $soal = $this->soalUjianModel->find($soalId);
        if (!$soal || !$soal['is_bank_soal']) {
            return redirect()->back()->with('error', 'Soal tidak ditemukan');
        }

        // Cek akses
        $bankUjian = $this->db->table('bank_ujian')->where('bank_ujian_id', $soal['bank_ujian_id'])->get()->getRowArray();
        if (!$bankUjian || ($bankUjian['kategori'] !== 'umum' && $soal['created_by'] != $userId)) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengedit soal ini');
        }

        // Validasi form input
        $rules = [
            'kode_soal' => 'required|alpha_numeric_punct|min_length[3]|max_length[50]',
            'pertanyaan' => 'required',
            'pilihan_a' => 'required',
            'pilihan_b' => 'required',
            'pilihan_c' => 'required',
            'pilihan_d' => 'required',
            'jawaban_benar' => 'required|in_list[A,B,C,D,E]',
            'tingkat_kesulitan' => 'required|decimal',
            'foto' => 'permit_empty|max_size[foto,2048]|mime_in[foto,image/jpg,image/jpeg,image/png]|ext_in[foto,png,jpg,jpeg]',
            'pembahasan' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $errorMessage = 'Validasi gagal: ' . implode(', ', $errors);
            return redirect()->back()->withInput()->with('error', $errorMessage);
        }

        $data = [
            'kode_soal' => $this->request->getPost('kode_soal'),
            'pertanyaan' => $this->request->getPost('pertanyaan'),
            'pilihan_a' => $this->request->getPost('pilihan_a'),
            'pilihan_b' => $this->request->getPost('pilihan_b'),
            'pilihan_c' => $this->request->getPost('pilihan_c'),
            'pilihan_d' => $this->request->getPost('pilihan_d'),
            'pilihan_e' => $this->request->getPost('pilihan_e'),
            'jawaban_benar' => $this->request->getPost('jawaban_benar'),
            'tingkat_kesulitan' => $this->request->getPost('tingkat_kesulitan'),
            'pembahasan' => $this->request->getPost('pembahasan')
        ];

        // Handle foto upload/delete
        $uploadPath = FCPATH . 'uploads/soal';
        $fotoFile = $this->request->getFile('foto');

        if ($fotoFile && $fotoFile->isValid() && !$fotoFile->hasMoved()) {
            if (!empty($soal['foto'])) {
                $fotoPath = $uploadPath . '/' . $soal['foto'];
                if (file_exists($fotoPath)) {
                    unlink($fotoPath);
                }
            }

            $newName = $fotoFile->getRandomName();
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $fotoFile->move($uploadPath, $newName);
            $data['foto'] = $newName;
        }

        if ($this->request->getPost('hapus_foto') == '1' && !empty($soal['foto'])) {
            $fotoPath = $uploadPath . '/' . $soal['foto'];
            if (file_exists($fotoPath)) {
                unlink($fotoPath);
            }
            $data['foto'] = null;
        }

        try {
            $this->soalUjianModel->update($soalId, $data);
            return redirect()->back()->with('success', 'Soal berhasil diupdate');
        } catch (\Exception $e) {
            log_message('error', 'Error saat mengupdate soal bank ujian: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui soal: ' . $e->getMessage());
        }
    }

    public function hapusSoalBankUjian($soalId)
    {
        $userId = session()->get('user_id');

        // Ambil data soal
        $soal = $this->soalUjianModel->find($soalId);
        if (!$soal || !$soal['is_bank_soal']) {
            return redirect()->back()->with('error', 'Soal tidak ditemukan');
        }

        // Cek akses
        $bankUjian = $this->db->table('bank_ujian')->where('bank_ujian_id', $soal['bank_ujian_id'])->get()->getRowArray();
        if (!$bankUjian || ($bankUjian['kategori'] !== 'umum' && $soal['created_by'] != $userId)) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menghapus soal ini');
        }

        // Hapus foto jika ada
        if (!empty($soal['foto'])) {
            $fotoPath = 'uploads/soal/' . $soal['foto'];
            if (file_exists($fotoPath)) {
                unlink($fotoPath);
            }
        }

        try {
            $this->ujianSoalCatModel->deleteBySoal($soalId);
            $this->soalUjianModel->delete($soalId);
            return redirect()->back()->with('success', 'Soal berhasil dihapus');
        } catch (\Exception $e) {
            log_message('error', 'Error saat menghapus soal bank ujian: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus soal.');
        }
    }


    // Method untuk mendapatkan bank soal dalam bentuk API (untuk modal import)
    public function getKategoriTersedia()
    {
        $userId = session()->get('user_id');
        $guru = $this->guruModel->where('user_id', $userId)->first();

        // Ambil kelas yang diajar oleh guru ini
        $kelasGuru = $this->db->table('kelas_guru')
            ->select('kelas.nama_kelas')
            ->join('kelas', 'kelas.kelas_id = kelas_guru.kelas_id')
            ->where('kelas_guru.guru_id', $guru['guru_id'])
            ->get()->getResultArray();

        $kategori = ['umum']; // Selalu bisa akses kategori umum
        foreach ($kelasGuru as $kelas) {
            $kategori[] = $kelas['nama_kelas'];
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $kategori
        ]);
    }

    public function getJenisUjianByKategori()
    {
        $kategori = $this->request->getGet('kategori');
        $userId = session()->get('user_id');

        if (!$kategori) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Kategori harus dipilih'
            ]);
        }

        // Validasi akses kategori
        $guru = $this->guruModel->where('user_id', $userId)->first();
        $aksesValid = false;

        if ($kategori === 'umum') {
            $aksesValid = true;
        } else {
            // Cek apakah guru mengajar kelas ini
            $aksesKelas = $this->db->table('kelas_guru')
                ->join('kelas', 'kelas.kelas_id = kelas_guru.kelas_id')
                ->where('kelas_guru.guru_id', $guru['guru_id'])
                ->where('kelas.nama_kelas', $kategori)
                ->get()->getRowArray();
            $aksesValid = !empty($aksesKelas);
        }

        if (!$aksesValid) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses ke kategori ini'
            ]);
        }

        // Ambil Mata Pelajaran yang memiliki bank ujian di kategori ini
        $jenisUjian = $this->db->table('bank_ujian')
            ->select('bank_ujian.jenis_ujian_id, jenis_ujian.nama_jenis, COUNT(*) as jumlah_bank')
            ->join('jenis_ujian', 'jenis_ujian.jenis_ujian_id = bank_ujian.jenis_ujian_id')
            ->where('bank_ujian.kategori', $kategori)
            ->groupBy('bank_ujian.jenis_ujian_id')
            ->get()->getResultArray();

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $jenisUjian
        ]);
    }


    public function getJenisUjianForKelas()
    {
        $kategori = $this->request->getGet('kategori');
        $userId = session()->get('user_id');

        if (!$kategori) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Kategori harus dipilih'
            ]);
        }

        $guru = $this->guruModel->where('user_id', $userId)->first();

        if ($kategori === 'umum') {
            // Untuk kategori umum, ambil semua Mata Pelajaran yang dibuat oleh guru ini
            // atau Mata Pelajaran yang tidak memiliki kelas_id (global)
            $jenisUjian = $this->db->table('jenis_ujian')
                ->select('jenis_ujian.jenis_ujian_id, jenis_ujian.nama_jenis')
                ->where('jenis_ujian.created_by', $userId)
                ->orWhere('jenis_ujian.kelas_id IS NULL')
                ->get()->getResultArray();
        } else {
            // Untuk kategori kelas tertentu, ambil Mata Pelajaran untuk kelas tersebut
            // Pertama, ambil kelas_id berdasarkan nama kelas
            $kelas = $this->db->table('kelas')
                ->select('kelas.kelas_id')
                ->join('kelas_guru', 'kelas_guru.kelas_id = kelas.kelas_id')
                ->where('kelas_guru.guru_id', $guru['guru_id'])
                ->where('kelas.nama_kelas', $kategori)
                ->get()->getRowArray();

            if (!$kelas) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses ke kelas ini'
                ]);
            }

            // Ambil Mata Pelajaran untuk kelas ini yang dibuat oleh guru ini
            $jenisUjian = $this->db->table('jenis_ujian')
                ->select('jenis_ujian.jenis_ujian_id, jenis_ujian.nama_jenis')
                ->where('jenis_ujian.kelas_id', $kelas['kelas_id'])
                ->where('jenis_ujian.created_by', $userId)
                ->get()->getResultArray();
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $jenisUjian
        ]);
    }

    // Method untuk mendapatkan nama ujian berdasarkan kategori dan Mata Pelajaran
    public function getBankUjianByKategoriJenis()
    {
        $kategori = $this->request->getGet('kategori');
        $jenisUjianId = $this->request->getGet('jenis_ujian_id');
        $userId = session()->get('user_id');

        if (!$kategori || !$jenisUjianId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Kategori dan Mata Pelajaran harus dipilih'
            ]);
        }

        // Validasi akses kategori (sama seperti method sebelumnya)
        $guru = $this->guruModel->where('user_id', $userId)->first();
        $aksesValid = false;

        if ($kategori === 'umum') {
            $aksesValid = true;
        } else {
            $aksesKelas = $this->db->table('kelas_guru')
                ->join('kelas', 'kelas.kelas_id = kelas_guru.kelas_id')
                ->where('kelas_guru.guru_id', $guru['guru_id'])
                ->where('kelas.nama_kelas', $kategori)
                ->get()->getRowArray();
            $aksesValid = !empty($aksesKelas);
        }

        if (!$aksesValid) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses ke kategori ini'
            ]);
        }

        // Ambil bank ujian
        $bankUjian = $this->db->table('bank_ujian')
            ->select('bank_ujian.*, users.username as creator_name,
                 (SELECT COUNT(*) FROM soal_ujian WHERE soal_ujian.bank_ujian_id = bank_ujian.bank_ujian_id AND soal_ujian.is_bank_soal = 1) as jumlah_soal')
            ->join('users', 'users.user_id = bank_ujian.created_by')
            ->where('bank_ujian.kategori', $kategori)
            ->where('bank_ujian.jenis_ujian_id', $jenisUjianId)
            ->get()->getResultArray();

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $bankUjian
        ]);
    }

    // Method untuk mendapatkan soal dari bank ujian
    public function getSoalBankUjian()
    {
        $bankUjianId = $this->request->getGet('bank_ujian_id');
        $userId = session()->get('user_id');

        if (!$bankUjianId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Bank ujian harus dipilih'
            ]);
        }

        // Validasi akses bank ujian
        $bankUjian = $this->db->table('bank_ujian')->where('bank_ujian_id', $bankUjianId)->get()->getRowArray();
        if (!$bankUjian) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Bank ujian tidak ditemukan'
            ]);
        }

        // Validasi akses kategori
        $guru = $this->guruModel->where('user_id', $userId)->first();
        $aksesValid = false;

        if ($bankUjian['kategori'] === 'umum') {
            $aksesValid = true;
        } else {
            $aksesKelas = $this->db->table('kelas_guru')
                ->join('kelas', 'kelas.kelas_id = kelas_guru.kelas_id')
                ->where('kelas_guru.guru_id', $guru['guru_id'])
                ->where('kelas.nama_kelas', $bankUjian['kategori'])
                ->get()->getRowArray();
            $aksesValid = !empty($aksesKelas);
        }

        if (!$aksesValid) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses ke bank soal ini'
            ]);
        }

        // Ambil soal-soal dari bank ujian
        $soalList = $this->soalUjianModel
            ->select('soal_ujian.*, soal_ujian.kode_soal') // Tambahkan kode_soal di sini
            ->where('bank_ujian_id', $bankUjianId)
            ->where('is_bank_soal', true)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $soalList,
            'bank_ujian' => $bankUjian
        ]);
    }

    // Method untuk import soal dari bank ujian
    public function importSoalDariBank()
    {
        $ujianId = $this->request->getPost('ujian_id');
        $soalIds = $this->request->getPost('soal_ids'); // Array of soal IDs

        if (!$ujianId || empty($soalIds)) {
            return redirect()->back()->with('error', 'Data tidak lengkap');
        }

        $userId = session()->get('user_id');
        $berhasilImport = 0;
        $gagalImport = 0;

        foreach ($soalIds as $soalId) {
            // Ambil data soal dari bank
            $soalBank = $this->soalUjianModel->find($soalId);

            if ($soalBank && $soalBank['is_bank_soal']) {
                // Validasi akses bank ujian
                $bankUjian = $this->db->table('bank_ujian')->where('bank_ujian_id', $soalBank['bank_ujian_id'])->get()->getRowArray();

                $aksesValid = false;
                if ($bankUjian['kategori'] === 'umum') {
                    $aksesValid = true;
                } else {
                    $guru = $this->guruModel->where('user_id', $userId)->first();
                    $aksesKelas = $this->db->table('kelas_guru')
                        ->join('kelas', 'kelas.kelas_id = kelas_guru.kelas_id')
                        ->where('kelas_guru.guru_id', $guru['guru_id'])
                        ->where('kelas.nama_kelas', $bankUjian['kategori'])
                        ->get()->getRowArray();
                    $aksesValid = !empty($aksesKelas);
                }

                if ($aksesValid) {
                    // Copy soal ke ujian tertentu - LANGSUNG COPY, BOLEH DUPLICATE
                    $dataSoalBaru = $soalBank;
                    unset($dataSoalBaru['soal_id']); // Remove primary key
                    $dataSoalBaru['ujian_id'] = $ujianId;
                    $dataSoalBaru['bank_ujian_id'] = null; // Not linked to bank ujian anymore
                    $dataSoalBaru['is_bank_soal'] = false; // Not a bank question anymore
                    $dataSoalBaru['created_by'] = $userId; // Set current user as creator
                    $dataSoalBaru['created_at'] = date('Y-m-d H:i:s'); // Set current timestamp
                    $dataSoalBaru['updated_at'] = date('Y-m-d H:i:s'); // Set current timestamp
                    // kode_soal akan tetap sama (duplikasi diizinkan)

                    try {
                        $this->soalUjianModel->insert($dataSoalBaru);
                        $berhasilImport++;
                    } catch (\Exception $e) {
                        log_message('error', 'Error import soal: ' . $e->getMessage());
                        $gagalImport++;
                    }
                } else {
                    $gagalImport++;
                }
            } else {
                $gagalImport++;
            }
        }

        $message = "Import selesai: {$berhasilImport} soal berhasil diimport";
        if ($gagalImport > 0) {
            $message .= ", {$gagalImport} soal gagal diimport";
        }

        if ($berhasilImport > 0) {
            return redirect()->to('guru/soal/' . $ujianId)->with('success', $message);
        } else {
            return redirect()->back()->with('error', 'Gagal mengimport soal dari bank');
        }
    }

    /**
     * Tautkan soal dari bank ke ujian (UPDATE ujian_id, tanpa duplikasi)
     */
    public function assignSoalDariBank()
    {
        $ujianId = $this->request->getPost('ujian_id');
        $soalIds = $this->request->getPost('soal_ids');

        if (!$ujianId || empty($soalIds) || !is_array($soalIds)) {
            return redirect()->back()->with('error', 'Pilih minimal satu soal.');
        }

        $ujian = $this->ujianModel->find($ujianId);
        if (!$ujian || (($ujian['tipe_ujian'] ?? 'CAT') !== 'CAT')) {
            return redirect()->back()->with('error', 'Penautan soal dari bank hanya berlaku untuk ujian CAT.');
        }

        $berhasil = 0;
        $sudahAda = 0;
        $gagal = 0;
        foreach ($soalIds as $soalId) {
            $soal = $this->soalUjianModel->find($soalId);
            if ($soal && $soal['is_bank_soal']) {
                if ($this->ujianSoalCatModel->linkSoal((int) $ujianId, (int) $soalId)) {
                    $berhasil++;
                } else {
                    $sudahAda++;
                }

                if ((int) ($soal['ujian_id'] ?? 0) === (int) $ujianId) {
                    $this->db->table('soal_ujian')->where('soal_id', $soalId)->update(['ujian_id' => null]);
                }
            } else {
                $gagal++;
            }
        }

        if ($berhasil > 0 && $sudahAda === 0 && $gagal === 0) {
            session()->setFlashdata('success', "{$berhasil} soal berhasil ditautkan ke ujian.");
        } else {
            $parts = [];
            if ($berhasil > 0) {
                $parts[] = "{$berhasil} soal berhasil ditautkan";
            }
            if ($sudahAda > 0) {
                $parts[] = "{$sudahAda} soal sudah ada di pool ujian";
            }
            if ($gagal > 0) {
                $parts[] = "{$gagal} soal gagal diproses";
            }

            session()->setFlashdata($berhasil > 0 ? 'warning' : 'error', implode('. ', $parts) . '.');
        }
        return redirect()->to('guru/soal/' . $ujianId);
    }

    /**
     * Lepas soal dari ujian (ujian_id = NULL), soal tetap di bank
     */
    public function unassignSoalDariUjian($soalId, $ujianId)
    {
        $this->ujianSoalCatModel->unlinkSoal((int) $ujianId, (int) $soalId);
        $this->db->table('soal_ujian')
            ->where('soal_id', $soalId)
            ->where('ujian_id', $ujianId)
            ->update(['ujian_id' => null]);
        session()->setFlashdata('success', 'Soal dilepas dari ujian. Soal tetap ada di bank.');
        return redirect()->to('guru/soal/' . $ujianId);
    }

    /**
     * Upload image untuk Summernote
     */
    public function uploadSummernoteImage()
    {
        // Cek login
        if (!session()->get('user_id') || session()->get('role') !== 'guru') {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Unauthorized'
            ]);
        }

        try {
            $uploadedFile = $this->request->getFile('upload');

            // Validasi
            if (!$uploadedFile || !$uploadedFile->isValid()) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'No file uploaded'
                ]);
            }

            $ext = strtolower($uploadedFile->getClientExtension());
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Invalid file type'
                ]);
            }

            if ($uploadedFile->getSize() > 2097152) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'File too large'
                ]);
            }

            // Generate nama file dengan timestamp untuk uniqueness
            $fileName = 'editor_' . time() . '_' . uniqid() . '.' . $ext;
            $uploadPath = FCPATH . 'uploads/editor-images';

            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            if ($uploadedFile->move($uploadPath, $fileName)) {
                $imageUrl = base_url('uploads/editor-images/' . $fileName);

                // TRACKING: Simpan info upload sementara di session untuk cleanup later
                $tempImages = session()->get('temp_uploaded_images') ?? [];
                $tempImages[] = [
                    'filename' => $fileName,
                    'path' => $uploadPath . '/' . $fileName,
                    'uploaded_at' => time()
                ];
                session()->set('temp_uploaded_images', $tempImages);

                return $this->response->setJSON([
                    'success' => true,
                    'url' => $imageUrl,
                    'filename' => $fileName,
                    'message' => 'Upload successful'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Failed to save file'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function extractImageFilenames($htmlContent)
    {
        $imageFiles = [];

        // Pattern untuk match URL gambar editor
        $pattern = '/uploads\/editor-images\/([^"\'>\s]+)/';

        if (preg_match_all($pattern, $htmlContent, $matches)) {
            $imageFiles = array_unique($matches[1]); // Ambil filename saja
        }

        return $imageFiles;
    }

    /**
     * Helper function untuk hapus gambar yang tidak digunakan
     */
    private function cleanupUnusedImages($usedImages, $allUploadedImages)
    {
        $deletedCount = 0;

        foreach ($allUploadedImages as $imageInfo) {
            $filename = $imageInfo['filename'];

            // Jika gambar tidak digunakan, hapus
            if (!in_array($filename, $usedImages)) {
                if (file_exists($imageInfo['path'])) {
                    unlink($imageInfo['path']);
                    $deletedCount++;
                }
            }
        }

        return $deletedCount;
    }

    /**
     * Helper function untuk cek penggunaan gambar di soal lain
     */
    private function checkImageUsageInOtherQuestions($filename, $excludeSoalId)
    {
        // Cari di semua field HTML di tabel soal_ujian
        $builder = $this->db->table('soal_ujian');
        $builder->where('soal_id !=', $excludeSoalId);
        $builder->groupStart();
        $builder->like('pertanyaan', $filename);
        $builder->orLike('pilihan_a', $filename);
        $builder->orLike('pilihan_b', $filename);
        $builder->orLike('pilihan_c', $filename);
        $builder->orLike('pilihan_d', $filename);
        $builder->orLike('pilihan_e', $filename);
        $builder->orLike('pembahasan', $filename);
        $builder->groupEnd();

        return $builder->countAllResults() > 0;
    }

    /**
     * Helper function untuk cleanup temp images
     */
    private function cleanupTempImages()
    {
        $tempImages = session()->get('temp_uploaded_images') ?? [];

        foreach ($tempImages as $imageInfo) {
            if (file_exists($imageInfo['path'])) {
                unlink($imageInfo['path']);
            }
        }

        session()->remove('temp_uploaded_images');
    }

    /**
     * Method untuk cleanup gambar orphaned (bisa dijadwalkan via cron job)
     */
    public function cleanupOrphanedImages()
    {
        // Hanya admin yang bisa menjalankan
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/')->with('error', 'Unauthorized');
        }

        $uploadPath = FCPATH . 'uploads/editor-images/';
        $deletedCount = 0;

        if (is_dir($uploadPath)) {
            $files = scandir($uploadPath);

            foreach ($files as $file) {
                if ($file == '.' || $file == '..') continue;

                $filePath = $uploadPath . $file;
                if (is_file($filePath)) {
                    // Cek apakah file digunakan di database
                    $isUsed = $this->checkImageUsageInOtherQuestions($file, 0);

                    if (!$isUsed) {
                        // Cek umur file (hapus jika lebih dari 24 jam dan tidak digunakan)
                        $fileAge = time() - filemtime($filePath);
                        if ($fileAge > 86400) { // 24 jam
                            unlink($filePath);
                            $deletedCount++;
                        }
                    }
                }
            }
        }

        return redirect()->back()->with('success', "Cleanup selesai. {$deletedCount} file orphaned dihapus.");
    }

    // =============================================
    //  METADATA SOAL: VARIABEL, INDIKATOR, MATERI
    // =============================================

    public function variabel()
    {
        $data['variabel'] = $this->variabelModel->getWithCounts();
        return view('guru/variabel', $data);
    }

    public function tambahVariabel()
    {
        return $this->_handleVariabelCrud('tambah');
    }

    public function editVariabel($id)
    {
        return $this->_handleVariabelCrud('edit', $id);
    }

    public function hapusVariabel($id)
    {
        if (!$this->variabelModel->find($id)) {
            session()->setFlashdata('error', 'Variabel tidak ditemukan.');
            return redirect()->to('guru/variabel');
        }
        try {
            $this->variabelModel->delete($id);
            session()->setFlashdata('success', 'Variabel berhasil dihapus!');
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Gagal menghapus. Hapus dulu indikator dan soal terkait.');
        }
        return redirect()->to('guru/variabel');
    }

    private function _handleVariabelCrud($action, $id = null)
    {
        $rules = ['nama_variabel' => 'required|min_length[3]|max_length[100]'];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $data = [
            'nama_variabel' => $this->request->getPost('nama_variabel'),
            'deskripsi'     => $this->request->getPost('deskripsi'),
        ];
        try {
            if ($action === 'tambah') {
                $this->variabelModel->insert($data);
                session()->setFlashdata('success', 'Variabel berhasil ditambahkan!');
            } else {
                $this->variabelModel->update($id, $data);
                session()->setFlashdata('success', 'Variabel berhasil diperbarui!');
            }
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Gagal: ' . $e->getMessage());
        }
        return redirect()->to('guru/variabel');
    }

    // ---------- INDIKATOR ----------

    public function indikator()
    {
        $data['indikator'] = $this->indikatorModel->getAllWithVariabel();
        $data['variabel']  = $this->variabelModel->orderBy('nama_variabel', 'ASC')->findAll();
        return view('guru/indikator', $data);
    }

    public function tambahIndikator()
    {
        return $this->_handleIndikatorCrud('tambah');
    }

    public function editIndikator($id)
    {
        return $this->_handleIndikatorCrud('edit', $id);
    }

    public function hapusIndikator($id)
    {
        if (!$this->indikatorModel->find($id)) {
            session()->setFlashdata('error', 'Indikator tidak ditemukan.');
            return redirect()->to('guru/indikator');
        }
        try {
            $this->indikatorModel->delete($id);
            session()->setFlashdata('success', 'Indikator berhasil dihapus!');
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Gagal menghapus. Hapus dulu soal yang terkait.');
        }
        return redirect()->to('guru/indikator');
    }

    private function _handleIndikatorCrud($action, $id = null)
    {
        $rules = [
            'variabel_id'     => 'required|numeric',
            'nama_indikator'  => 'required|min_length[3]|max_length[200]',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $data = [
            'variabel_id'    => $this->request->getPost('variabel_id'),
            'nama_indikator' => $this->request->getPost('nama_indikator'),
            'deskripsi'      => $this->request->getPost('deskripsi'),
        ];
        try {
            if ($action === 'tambah') {
                $this->indikatorModel->insert($data);
                session()->setFlashdata('success', 'Indikator berhasil ditambahkan!');
            } else {
                $this->indikatorModel->update($id, $data);
                session()->setFlashdata('success', 'Indikator berhasil diperbarui!');
            }
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Gagal: ' . $e->getMessage());
        }
        return redirect()->to('guru/indikator');
    }

    // ---------- MATERI ----------

    public function materi()
    {
        $data['materi'] = $this->materiModel->getWithCount();
        return view('guru/materi', $data);
    }

    public function tambahMateri()
    {
        return $this->_handleMateriCrud('tambah');
    }

    public function editMateri($id)
    {
        return $this->_handleMateriCrud('edit', $id);
    }

    public function hapusMateri($id)
    {
        if (!$this->materiModel->find($id)) {
            session()->setFlashdata('error', 'Materi tidak ditemukan.');
            return redirect()->to('guru/materi');
        }
        try {
            $this->materiModel->delete($id);
            session()->setFlashdata('success', 'Materi berhasil dihapus!');
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Gagal menghapus. Hapus dulu soal yang terkait.');
        }
        return redirect()->to('guru/materi');
    }

    private function _handleMateriCrud($action, $id = null)
    {
        $rules = ['nama_materi' => 'required|min_length[2]|max_length[200]'];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $data = [
            'nama_materi' => $this->request->getPost('nama_materi'),
            'deskripsi'   => $this->request->getPost('deskripsi'),
        ];
        try {
            if ($action === 'tambah') {
                $this->materiModel->insert($data);
                session()->setFlashdata('success', 'Materi berhasil ditambahkan!');
            } else {
                $this->materiModel->update($id, $data);
                session()->setFlashdata('success', 'Materi berhasil diperbarui!');
            }
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Gagal: ' . $e->getMessage());
        }
        return redirect()->to('guru/materi');
    }

    public function getIndikatorByVariabel($variabelId)
    {
        $indikator = $this->indikatorModel->where('variabel_id', $variabelId)
            ->orderBy('nama_indikator', 'ASC')
            ->findAll();
        return $this->response->setJSON($indikator);
    }

    // =============================================
    //  MULTI-BANK & GENERATE PAKET
    // =============================================

    public function assignBank($ujianId)
    {
        // Redirect ke halaman kelola soal (tab Bank & Paket)
        return redirect()->to('guru/soal/' . $ujianId);
    }

    public function syncBanks($ujianId)
    {
        $existingPaket = $this->paketUjianModel->where('ujian_id', $ujianId)->countAllResults();
        if ($existingPaket > 0) {
            session()->setFlashdata('error', 'Sumber bank soal tidak bisa diubah karena paket sudah terbentuk. Hapus semua paket terlebih dahulu jika ingin mengganti sumber bank.');
            return redirect()->back();
        }

        $bankIds = $this->request->getPost('bank_ids') ?? [];
        if (!empty($bankIds) && !is_array($bankIds)) {
            $bankIds = [$bankIds];
        }
        try {
            $this->ujianBankModel->syncBanks($ujianId, $bankIds);
            session()->setFlashdata('success', empty($bankIds) ? 'Assignment bank dikosongkan.' : '1 bank berhasil di-assign sebagai sumber tunggal CBT.');
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Gagal: ' . $e->getMessage());
        }
        return redirect()->back();
    }

    public function generatePaket($ujianId)
    {
        // Redirect ke halaman kelola soal (tab Bank & Paket)
        return redirect()->to('guru/soal/' . $ujianId);
    }

    public function prosesGeneratePaket($ujianId)
    {
        $jumlahPaket = (int) $this->request->getPost('jumlah_paket') ?: 3;
        $soalPerPaket = (int) $this->request->getPost('soal_per_paket') ?: 25;
        $attemptCount = $this->db->table('attempt_ujian au')
            ->join('paket_ujian pu', 'pu.paket_id = au.paket_id')
            ->where('pu.ujian_id', $ujianId)
            ->countAllResults();

        if ($attemptCount > 0) {
            return redirect()->back()->with('error', 'Paket tidak dapat diacak ulang karena sudah pernah dipakai siswa. Membuat ulang paket saat data attempt sudah ada berisiko merusak konsistensi hasil ujian.');
        }

        $banks = $this->ujianBankModel->getBanksByUjian($ujianId);
        if (empty($banks)) {
            return redirect()->back()->with('error', 'Assign bank dulu.');
        }
        if (count($banks) !== 1) {
            return redirect()->back()->with('error', 'CBT hanya boleh memakai satu bank soal sebagai sumber tunggal.');
        }

        $bankId = $banks[0]['bank_ujian_id'];
        $totalSoal = $this->soalUjianModel->where(['bank_ujian_id' => $bankId, 'is_bank_soal' => 1])->countAllResults();
        if ($soalPerPaket >= $totalSoal) {
            return redirect()->back()->with('error', "Soal per paket harus lebih kecil dari stok bank (Y < N). Tersedia: {$totalSoal}, diminta: {$soalPerPaket}.");
        }

        try {
            $draft = $this->buildDraftPaket($ujianId, $bankId, $jumlahPaket, $soalPerPaket);
            $this->setDraftPaket($ujianId, $draft);
            session()->setFlashdata('success', "{$jumlahPaket} draft paket berhasil dibuat. Review dulu, lalu klik Simpan Paket untuk mengunci paket ke database.");
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Gagal generate draft: ' . $e->getMessage());
        }
        return redirect()->to('guru/soal/' . $ujianId . '?step=2&panel=paket');
    }

    public function simpanDraftPaket($ujianId)
    {
        $draft = $this->getDraftPaket($ujianId);
        if (empty($draft['packages'])) {
            return redirect()->back()->with('error', 'Draft paket belum tersedia.');
        }

        $attemptCount = $this->db->table('attempt_ujian au')
            ->join('paket_ujian pu', 'pu.paket_id = au.paket_id')
            ->where('pu.ujian_id', $ujianId)
            ->countAllResults();
        if ($attemptCount > 0) {
            return redirect()->back()->with('error', 'Draft paket tidak dapat disimpan karena ujian sudah pernah dikerjakan siswa.');
        }

        $this->paketUjianModel->db->transStart();
        try {
            $this->paketUjianModel->deleteByUjian($ujianId);
            foreach ($draft['packages'] as $package) {
                $this->paketUjianModel->db->table('paket_ujian')->insert([
                    'ujian_id' => $ujianId,
                    'nama_paket' => $package['nama_paket'],
                    'nomor_paket' => $package['nomor_paket'],
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
                $paketId = $this->paketUjianModel->db->insertID();
                $urut = 1;
                foreach ($package['soal_ids'] as $soalId) {
                    $r = $this->paketUjianModel->db->table('paket_ujian_item')->insert([
                        'paket_id' => $paketId,
                        'soal_id' => $soalId,
                        'nomor_urut' => $urut++,
                    ]);
                    if (!$r) throw new \Exception('Gagal menyimpan item paket.');
                }
            }
            $this->paketUjianModel->db->transComplete();

            if ($this->paketUjianModel->db->transStatus() === false) {
                throw new \Exception('Gagal menyimpan paket final.');
            }

            $this->clearDraftPaket($ujianId);
            session()->setFlashdata('success', 'Paket berhasil disimpan dan dikunci sebagai paket final.');
        } catch (\Exception $e) {
            $this->paketUjianModel->db->transRollback();
            session()->setFlashdata('error', 'Gagal menyimpan paket: ' . $e->getMessage());
        }

        return redirect()->to('guru/soal/' . $ujianId . '?step=3&panel=paket');
    }

    public function batalDraftPaket($ujianId)
    {
        $this->clearDraftPaket($ujianId);
        session()->setFlashdata('success', 'Draft paket dibatalkan.');
        return redirect()->to('guru/soal/' . $ujianId . '?step=2&panel=generate');
    }

    public function hapusPaket($ujianId, $paketId)
    {
        $attemptCount = $this->db->table('attempt_ujian au')
            ->join('paket_ujian pu', 'pu.paket_id = au.paket_id')
            ->where('pu.ujian_id', $ujianId)
            ->countAllResults();
        if ($attemptCount > 0) {
            session()->setFlashdata('error', 'Paket tidak dapat dihapus karena sudah pernah dipakai siswa.');
            return redirect()->back();
        }

        $this->db->table('paket_ujian')->where('paket_id', $paketId)->delete();
        session()->setFlashdata('success', 'Paket dihapus.');
        return redirect()->back();
    }

    public function hapusSemuaPaket($ujianId)
    {
        $attemptCount = $this->db->table('attempt_ujian au')
            ->join('paket_ujian pu', 'pu.paket_id = au.paket_id')
            ->where('pu.ujian_id', $ujianId)
            ->countAllResults();
        if ($attemptCount > 0) {
            session()->setFlashdata('error', 'Semua paket tidak dapat dihapus karena sudah pernah dipakai siswa.');
            return redirect()->back();
        }

        $this->paketUjianModel->deleteByUjian($ujianId);
        $this->clearDraftPaket($ujianId);
        $this->ujianBankModel->where('ujian_id', $ujianId)->delete();
        session()->setFlashdata('success', 'Semua paket dihapus. Sumber bank soal juga sudah direset.');
        return redirect()->to('guru/soal/' . $ujianId . '?step=1');
    }

    public function getSoalByPaket($paketId)
    {
        return $this->response->setJSON($this->paketUjianModel->getSoalByPaket($paketId));
    }

    public function getSoalByDraftPaket($ujianId, $index)
    {
        $draft = $this->getDraftPaket($ujianId);
        $packages = $draft['packages'] ?? [];
        $package = $packages[$index - 1] ?? null;
        if (!$package) {
            return $this->response->setJSON([]);
        }

        return $this->response->setJSON($this->getOrderedSoalByIds($package['soal_ids'] ?? []));
    }

    private function _getGuruData()
    {
        $userId = session()->get('user_id');
        return $this->guruModel->where('user_id', $userId)->first();
    }

    private function normalizeNullableId($value)
    {
        return ($value === null || $value === '' || $value === '0' || $value === 0) ? null : (int) $value;
    }

    private function validateGuruSiswaIdsForKelas(int $guruId, int $kelasId, array $siswaIds): bool
    {
        if ($guruId <= 0 || $kelasId <= 0 || empty($siswaIds)) {
            return false;
        }

        $validCount = $this->db->table('siswa')
            ->join('kelas_guru', 'kelas_guru.kelas_id = siswa.kelas_id')
            ->where('siswa.kelas_id', $kelasId)
            ->where('kelas_guru.guru_id', $guruId)
            ->whereIn('siswa.siswa_id', $siswaIds)
            ->countAllResults();

        return $validCount === count($siswaIds);
    }

    private function validateJadwalKelasAgainstUjian(int $ujianId, int $kelasId): bool
    {
        if ($ujianId <= 0 || $kelasId <= 0) {
            return false;
        }

        $ujian = $this->ujianModel->find($ujianId);
        $kelas = $this->kelasModel->find($kelasId);
        if (!$ujian || !$kelas) {
            return false;
        }

        if (!empty($ujian['kelas_id'])) {
            return (int) $ujian['kelas_id'] === $kelasId;
        }

        if (!empty($ujian['sekolah_id'])) {
            return (int) $kelas['sekolah_id'] === (int) $ujian['sekolah_id'];
        }

        return true;
    }

    private function getDraftPaketKey($ujianId)
    {
        return 'guru_draft_paket_' . $ujianId;
    }

    private function getDraftPaket($ujianId)
    {
        return session()->get($this->getDraftPaketKey($ujianId));
    }

    private function setDraftPaket($ujianId, array $draft)
    {
        session()->set($this->getDraftPaketKey($ujianId), $draft);
    }

    private function clearDraftPaket($ujianId)
    {
        session()->remove($this->getDraftPaketKey($ujianId));
    }

    private function getAttemptAwareDetailJawaban($pesertaUjianId, ?int $attemptId = null): array
    {
        $attemptQuery = $this->db->table('attempt_ujian')
            ->where('peserta_ujian_id', $pesertaUjianId);

        if ($attemptId !== null) {
            $attemptQuery->where('attempt_id', $attemptId);
        } else {
            $attemptQuery->orderBy('nomor_attempt', 'DESC');
        }

        $attempt = $attemptQuery->get()->getRowArray();

        if ($attempt) {
            $rows = $this->db->table('attempt_jawaban aj')
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
            ->select('hasil_ujian.*, soal_ujian.pertanyaan, soal_ujian.kode_soal, soal_ujian.jawaban_benar, soal_ujian.media as foto,
                soal_ujian.tingkat_kesulitan, soal_ujian.pembahasan,
                DATE_FORMAT(hasil_ujian.waktu_menjawab, "%H:%i:%s") as waktu_menjawab_format')
            ->join('soal_ujian', 'soal_ujian.soal_id = hasil_ujian.soal_id')
            ->where('hasil_ujian.peserta_ujian_id', $pesertaUjianId)
            ->orderBy('hasil_ujian.waktu_menjawab', 'ASC')
            ->findAll();
    }

    private function buildDraftPaket($ujianId, $bankId, $jumlahPaket, $soalPerPaket)
    {
        $packages = [];
        for ($i = 1; $i <= $jumlahPaket; $i++) {
            $soals = $this->soalUjianModel
                ->where(['bank_ujian_id' => $bankId, 'is_bank_soal' => 1])
                ->orderBy('RAND()')
                ->findAll($soalPerPaket);
            if (count($soals) !== $soalPerPaket) {
                throw new \Exception('Jumlah soal draft tidak sesuai stok yang diminta.');
            }
            $packages[] = [
                'nama_paket' => 'Paket ' . $i,
                'nomor_paket' => $i,
                'jumlah_soal' => count($soals),
                'soal_ids' => array_column($soals, 'soal_id'),
            ];
        }

        return [
            'ujian_id' => $ujianId,
            'jumlah_paket' => $jumlahPaket,
            'soal_per_paket' => $soalPerPaket,
            'created_at' => date('Y-m-d H:i:s'),
            'packages' => $packages,
        ];
    }

    private function getOrderedSoalByIds(array $soalIds)
    {
        if (empty($soalIds)) {
            return [];
        }

        $soalMap = [];
        foreach ($this->soalUjianModel->whereIn('soal_id', $soalIds)->findAll() as $soal) {
            $soalMap[$soal['soal_id']] = $soal;
        }

        $ordered = [];
        foreach ($soalIds as $idx => $soalId) {
            if (isset($soalMap[$soalId])) {
                $soalMap[$soalId]['nomor_urut'] = $idx + 1;
                $ordered[] = $soalMap[$soalId];
            }
        }

        return $ordered;
    }
}
