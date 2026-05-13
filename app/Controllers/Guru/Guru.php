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
            ->select('kelas.*')
            ->join('kelas_guru', 'kelas_guru.kelas_id = kelas.kelas_id')
            ->where('kelas_guru.guru_id', $guru['guru_id'])
            ->get()->getResultArray();

        return view('guru/ujian', $data);
    }

    public function tambahUjian()
    {
        $userId = session()->get('user_id');
        $guru = $this->guruModel->where('user_id', $userId)->first();

        // Validasi input form
        $rules = [
            'jenis_ujian_id' => 'required|numeric',
            'nama_ujian' => 'required|min_length[3]|max_length[255]',
            'kode_ujian' => 'required|alpha_numeric_punct|min_length[3]|max_length[50]', // Validasi kode_ujian
            'deskripsi' => 'required|min_length[10]',
            'se_awal' => 'required|decimal',
            'se_minimum' => 'required|decimal',
            'delta_se_minimum' => 'required|decimal',
            'durasi' => 'required',
            'kelas_id' => 'permit_empty|numeric'
        ];

        if (!$this->validate($rules)) {
            // Mengirimkan error ke session flashdata
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors());
        }

        // Validasi Mata Pelajaran (pastikan guru memiliki akses)
        $jenisUjianId = $this->request->getPost('jenis_ujian_id');
        if (!$this->jenisUjianModel->hasAccess($jenisUjianId, $guru['guru_id'])) {
            return redirect()->to('guru/ujian')
                ->with('error', 'Anda tidak memiliki akses untuk menggunakan Mata Pelajaran tersebut.');
        }

        // Validasi kelas (jika dipilih)
        $kelasId = $this->request->getPost('kelas_id');
        if ($kelasId) {
            $kelasAccess = $this->db->table('kelas_guru')
                ->where('guru_id', $guru['guru_id'])
                ->where('kelas_id', $kelasId)
                ->get()->getRowArray();

            if (!$kelasAccess) {
                return redirect()->to('guru/ujian')
                    ->with('error', 'Anda tidak memiliki akses untuk menambahkan ujian pada kelas tersebut.');
            }
        }


        $data = [
            'jenis_ujian_id' => $jenisUjianId,
            'nama_ujian' => $this->request->getPost('nama_ujian'),
            'kode_ujian' => $this->request->getPost('kode_ujian'),
            'deskripsi' => $this->request->getPost('deskripsi'),
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
            'jenis_ujian_id' => 'required|numeric',
            'nama_ujian' => 'required|min_length[3]|max_length[255]',
            'kode_ujian' => 'required|alpha_numeric_punct|min_length[3]|max_length[50]', // Validasi kode_ujian, abaikan ID saat ini
            'deskripsi' => 'required|min_length[10]',
            'se_awal' => 'required|decimal',
            'se_minimum' => 'required|decimal',
            'delta_se_minimum' => 'required|decimal',
            'durasi' => 'required',
            'kelas_id' => 'permit_empty|numeric' // `permit_empty` mengizinkan nilai kosong
        ];

        if (!$this->validate($rules)) {
            // Mengirimkan error ke session flashdata
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

        // Validasi kelas (jika diubah)
        $kelasId = $this->request->getPost('kelas_id');
        // Hanya validasi jika kelasId tidak kosong, karena kelas_id bisa NULL
        if (!empty($kelasId)) {
            $kelasAccess = $this->db->table('kelas_guru')
                ->where('guru_id', $guru['guru_id'])
                ->where('kelas_id', $kelasId)
                ->get()->getRowArray();

            if (!$kelasAccess) {
                return redirect()->to('guru/ujian')
                    ->with('error', 'Anda tidak memiliki akses untuk memindahkan ujian ke kelas tersebut.');
            }
        }

        $data = [
            'jenis_ujian_id' => $jenisUjianId,
            'nama_ujian' => $this->request->getPost('nama_ujian'),
            'kode_ujian' => $this->request->getPost('kode_ujian'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'se_awal' => $this->request->getPost('se_awal'),
            'se_minimum' => $this->request->getPost('se_minimum'),
            'delta_se_minimum' => $this->request->getPost('delta_se_minimum'),
            'durasi' => $this->request->getPost('durasi'),
            'kelas_id' => empty($kelasId) ? null : $kelasId // PERBAIKAN PENTING DI SINI
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

        $soalTerkait = $this->soalUjianModel->where('ujian_id', $id)->countAllResults();

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

        $data['soal'] = $this->soalUjianModel->where('ujian_id', $ujian_id)->findAll();
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
            'foto' => 'max_size[foto,2048]|mime_in[foto,image/jpg,image/jpeg,image/png]|ext_in[foto,png,jpg,jpeg]',
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
        $data = [
            'ujian_id' => $this->request->getPost('ujian_id'),
            'pertanyaan' => $this->request->getPost('pertanyaan'),
            'kode_soal' => $this->request->getPost('kode_soal'),
            'pilihan_a' => $this->request->getPost('pilihan_a'),
            'pilihan_b' => $this->request->getPost('pilihan_b'),
            'pilihan_c' => $this->request->getPost('pilihan_c'),
            'pilihan_d' => $this->request->getPost('pilihan_d'),
            'pilihan_e' => $this->request->getPost('pilihan_e'),
            'jawaban_benar' => $this->request->getPost('jawaban_benar'),
            'tingkat_kesulitan' => $this->request->getPost('tingkat_kesulitan'),
            'pembahasan' => $this->request->getPost('pembahasan'),
            'created_by' => session()->get('user_id')
        ];

        // Upload foto field terpisah (jika ada)
        $fotoFile = $this->request->getFile('foto');
        if ($fotoFile->isValid() && !$fotoFile->hasMoved()) {
            $newName = $fotoFile->getRandomName();
            $uploadPath = FCPATH . 'uploads/soal';

            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $fotoFile->move($uploadPath, $newName);
            $data['foto'] = $newName;
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

                return redirect()->to('guru/soal/' . $data['ujian_id'])->with('success', 'Soal berhasil ditambahkan');
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
            'foto' => 'max_size[foto,2048]|mime_in[foto,image/jpg,image/jpeg,image/png]|ext_in[foto,png,jpg,jpeg]',
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
            'pembahasan' => $this->request->getPost('pembahasan')
        ];

        $uploadPath = FCPATH . 'uploads/soal';

        // Handle upload foto field terpisah (seperti sebelumnya)
        $fotoFile = $this->request->getFile('foto');
        if ($fotoFile->isValid() && !$fotoFile->hasMoved()) {
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

        // Checkbox untuk menghapus foto
        if ($this->request->getPost('hapus_foto') == '1' && !empty($soal['foto'])) {
            $fotoPath = $uploadPath . '/' . $soal['foto'];
            if (file_exists($fotoPath)) {
                unlink($fotoPath);
            }
            $data['foto'] = null;
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
            ->select('jadwal_ujian.*, ujian.nama_ujian, ujian.kode_ujian, kelas.nama_kelas, guru.nama_lengkap')
            ->join('ujian', 'ujian.id_ujian = jadwal_ujian.ujian_id')
            ->join('kelas', 'kelas.kelas_id = jadwal_ujian.kelas_id')
            ->join('guru', 'guru.guru_id = jadwal_ujian.guru_id')
            ->join('kelas_guru', 'kelas_guru.kelas_id = jadwal_ujian.kelas_id')
            ->where('kelas_guru.guru_id', $guru['guru_id'])
            ->orderBy('jadwal_ujian.tanggal_mulai', 'DESC')
            ->get()->getResultArray();

        // Daftar ujian untuk modal tambah: hanya ujian yang bisa diakses guru
        $data['ujian_tambah'] = $this->ujianModel->select('id_ujian, nama_ujian, kode_ujian')->getByKelasGuru($guru['guru_id']);

        // Daftar ujian untuk modal edit: hanya ujian yang bisa diakses guru
        $data['ujian_edit'] = $this->ujianModel->select('id_ujian, nama_ujian, kode_ujian')->getByKelasGuru($guru['guru_id']);

        // Daftar kelas yang diajar guru
        $data['kelas'] = $this->db->table('kelas')
            ->select('kelas.*')
            ->join('kelas_guru', 'kelas_guru.kelas_id = kelas.kelas_id')
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

        // Cek apakah kombinasi ujian_id dan kelas_id sudah ada
        $existing = $this->jadwalUjianModel
            ->where('ujian_id', $ujian_id)
            ->where('kelas_id', $kelas_id)
            ->first();

        if ($existing) {
            return redirect()->to('guru/jadwal-ujian')
                ->with('error', 'Jadwal ujian untuk kelas ini sudah ada. Pilih kelas lain atau ujian lain.');
        }

        $data = [
            'ujian_id' => $ujian_id,
            'kelas_id' => $kelas_id,
            'guru_id' => $guru_pengawas_id,
            'tanggal_mulai' => $this->request->getPost('tanggal_mulai'),
            'tanggal_selesai' => $this->request->getPost('tanggal_selesai'),
            'kode_akses' => $this->request->getPost('kode_akses'),
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

        $data = [
            'ujian_id' => $ujian_id,
            'kelas_id' => $kelas_id,
            'guru_id' => $this->request->getPost('guru_id'),
            'tanggal_mulai' => $this->request->getPost('tanggal_mulai'),
            'tanggal_selesai' => $this->request->getPost('tanggal_selesai'),
            'kode_akses' => $this->request->getPost('kode_akses'),
            'status' => $this->request->getPost('status')
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

    public function hasilUjian()
    {
        $userId = session()->get('user_id');
        $guru = $this->guruModel->where('user_id', $userId)->first();

        // Ambil daftar ujian yang sudah pernah dijadwalkan oleh guru ini dengan informasi waktu
        $daftarUjian = $this->jadwalUjianModel
            ->select('jadwal_ujian.*, ujian.nama_ujian, ujian.deskripsi, ujian.kode_ujian, jenis_ujian.nama_jenis, kelas.nama_kelas,
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
                $lastResult = $db->table('hasil_ujian')
                    ->select('theta_saat_ini, se_saat_ini')
                    ->where('peserta_ujian_id', $siswa['peserta_ujian_id'])
                    ->orderBy('waktu_menjawab', 'DESC')
                    ->limit(1)
                    ->get()
                    ->getRowArray();

                $theta_akhir = $lastResult ? (float)$lastResult['theta_saat_ini'] : 0;
                $skor_akhir = $this->hitungKemampuanKognitif($theta_akhir);
                $klasifikasi_kognitif = $this->getKlasifikasiKognitif($skor_akhir);

                $siswa['theta_akhir'] = $theta_akhir;
                $siswa['skor'] = $skor_akhir;
                $siswa['nilai'] = min(100, max(0, round($skor_akhir)));
                $siswa['se_akhir'] = $lastResult ? $lastResult['se_saat_ini'] : null;

                $jawabanBenar = $db->table('hasil_ujian')
                    ->where('peserta_ujian_id', $siswa['peserta_ujian_id'])
                    ->where('is_correct', 1)
                    ->countAllResults();

                $totalSoal = $db->table('hasil_ujian')
                    ->where('peserta_ujian_id', $siswa['peserta_ujian_id'])
                    ->countAllResults();

                $siswa['jawaban_benar'] = $jawabanBenar;
                $siswa['total_soal'] = $totalSoal;

                $siswa['kemampuan_kognitif'] = [
                    'skor' => $skor_akhir,
                    'total_benar' => $jawabanBenar,
                    'total_salah' => $totalSoal - $jawabanBenar,
                    'rata_rata_pilihan' => 0,
                ];
                $siswa['klasifikasi_kognitif'] = $klasifikasi_kognitif;

                if ($siswa['durasi_detik']) {
                    $jam = floor($siswa['durasi_detik'] / 3600);
                    $menit = floor(($siswa['durasi_detik'] % 3600) / 60);
                    $detik = $siswa['durasi_detik'] % 60;
                    $siswa['durasi_format'] = sprintf('%02d:%02d:%02d', $jam, $menit, $detik);
                } else {
                    $siswa['durasi_format'] = '-';
                }
            } else {
                $siswa['theta_akhir'] = null;
                $siswa['skor'] = null;
                $siswa['nilai'] = null;
                $siswa['se_akhir'] = null;
                $siswa['jawaban_benar'] = 0;
                $siswa['total_soal'] = 0;
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

    public function detailHasil($pesertaUjianId)
    {
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

        // Ambil detail jawaban dengan waktu
        $detailJawaban = $this->hasilUjianModel
            ->select('hasil_ujian.*, soal_ujian.pertanyaan, soal_ujian.kode_soal, soal_ujian.jawaban_benar, soal_ujian.foto,
            soal_ujian.tingkat_kesulitan,
            DATE_FORMAT(hasil_ujian.waktu_menjawab, "%H:%i:%s") as waktu_menjawab_format')
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

        $lastResult = end($detailJawabanDenganDurasi);
        $theta_akhir = $lastResult ? (float)$lastResult['theta_saat_ini'] : 0;
        $skor_akhir = $this->hitungKemampuanKognitif($theta_akhir);
        $klasifikasiKognitif = $this->getKlasifikasiKognitif($skor_akhir);

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
            'kemampuanKognitif' => $kemampuanKognitif,
            'klasifikasiKognitif' => $klasifikasiKognitif,
            'rataRataWaktuFormat' => sprintf('%d menit %d detik', $rataRataMenit, $rataRataDetik),
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

        $kelasId = $this->request->getPost('kelas_id');

        // WAJIB pilih kelas (tidak boleh kosong)
        if (empty($kelasId)) {
            return redirect()->to('guru/jenis-ujian')
                ->with('error', 'Kelas harus dipilih.');
        }

        // Validasi kelas (pastikan guru mengajar kelas yang dipilih)
        $kelasAccess = $this->db->table('kelas_guru')
            ->where('guru_id', $guru['guru_id'])
            ->where('kelas_id', $kelasId)
            ->get()->getRowArray();

        if (!$kelasAccess) {
            return redirect()->to('guru/jenis-ujian')
                ->with('error', 'Anda tidak memiliki akses untuk menambahkan Mata Pelajaran pada kelas tersebut.');
        }

        $data = [
            'nama_jenis' => $this->request->getPost('nama_jenis'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'kelas_id' => $kelasId, // Selalu ada kelas_id
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

        $detailJawaban = $this->hasilUjianModel
            ->select('hasil_ujian.*, soal_ujian.pertanyaan, soal_ujian.kode_soal, soal_ujian.jawaban_benar, soal_ujian.foto,
            soal_ujian.tingkat_kesulitan,
            DATE_FORMAT(hasil_ujian.waktu_menjawab, "%H:%i:%s") as waktu_menjawab_format')
            ->join('soal_ujian', 'soal_ujian.soal_id = hasil_ujian.soal_id')
            ->where('hasil_ujian.peserta_ujian_id', $pesertaUjianId)
            ->orderBy('hasil_ujian.waktu_menjawab', 'ASC')
            ->findAll();

        $detailJawabanDenganDurasi = $this->hitungDurasiPerSoal($detailJawaban, $hasil['waktu_mulai']);
        $totalSoal = count($detailJawabanDenganDurasi);
        $jawabanBenar = array_reduce($detailJawabanDenganDurasi, function ($carry, $item) {
            return $carry + ($item['is_correct'] ? 1 : 0);
        }, 0);

        $lastResult = end($detailJawabanDenganDurasi);
        $theta_akhir = $lastResult ? (float)$lastResult['theta_saat_ini'] : 0;
        $skor_akhir = $this->hitungKemampuanKognitif($theta_akhir);
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
        }

        $rataRataWaktu = $totalSoal > 0 ? ($hasil['durasi_total_detik'] / $totalSoal) : 0;
        $rataRataMenit = floor($rataRataWaktu / 60);
        $rataRataDetik = $rataRataWaktu % 60;

        $data = [
            'hasil' => $hasil,
            'detailJawaban' => $detailJawabanDenganDurasi,
            'finalScore' => $skor_akhir,
            'lastTheta' => $theta_akhir,
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

        $detailJawaban = $this->hasilUjianModel
            ->select('hasil_ujian.*, soal_ujian.pertanyaan, soal_ujian.kode_soal, soal_ujian.jawaban_benar, soal_ujian.foto,
            soal_ujian.tingkat_kesulitan,
            DATE_FORMAT(hasil_ujian.waktu_menjawab, "%H:%i:%s") as waktu_menjawab_format')
            ->join('soal_ujian', 'soal_ujian.soal_id = hasil_ujian.soal_id')
            ->where('hasil_ujian.peserta_ujian_id', $pesertaUjianId)
            ->orderBy('hasil_ujian.waktu_menjawab', 'ASC')
            ->findAll();

        $detailJawabanDenganDurasi = $this->hitungDurasiPerSoal($detailJawaban, $hasil['waktu_mulai']);
        $totalSoal = count($detailJawabanDenganDurasi);
        $jawabanBenar = array_reduce($detailJawabanDenganDurasi, function ($carry, $item) {
            return $carry + ($item['is_correct'] ? 1 : 0);
        }, 0);

        $lastResult = end($detailJawabanDenganDurasi);
        $theta_akhir = $lastResult ? (float)$lastResult['theta_saat_ini'] : 0;
        $skor_akhir = $this->hitungKemampuanKognitif($theta_akhir);
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
        }

        $rataRataWaktu = $totalSoal > 0 ? ($hasil['durasi_total_detik'] / $totalSoal) : 0;
        $rataRataMenit = floor($rataRataWaktu / 60);
        $rataRataDetik = $rataRataWaktu % 60;

        $data = [
            'hasil' => $hasil,
            'detailJawaban' => $detailJawabanDenganDurasi,
            'finalScore' => $skor_akhir,
            'lastTheta' => $theta_akhir,
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

        // Ambil kelas yang diajar oleh guru ini
        $kelasGuru = $this->db->table('kelas_guru')
            ->select('kelas.kelas_id, kelas.nama_kelas')
            ->join('kelas', 'kelas.kelas_id = kelas_guru.kelas_id')
            ->where('kelas_guru.guru_id', $guru['guru_id'])
            ->get()->getResultArray();

        $data = [
            'kelasGuru' => $kelasGuru,
            'jenis_ujian' => $this->jenisUjianModel->findAll()
        ];

        return view('guru/bank_soal/index', $data);
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
        if ($kategori !== 'umum') {
            // Cek apakah guru mengajar kelas ini
            $aksesKelas = $this->db->table('kelas_guru')
                ->join('kelas', 'kelas.kelas_id = kelas_guru.kelas_id')
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

        $data = [
            'kategori' => $kategori,
            'jenisUjianList' => $jenisUjianList
        ];

        return view('guru/bank_soal/kategori', $data);
    }

    public function bankSoalJenisUjian($kategori, $jenisUjianId)
    {
        $userId = session()->get('user_id');

        // Ambil daftar ujian dalam Mata Pelajaran dan kategori ini
        $ujianList = $this->db->table('bank_ujian')
            ->select('bank_ujian.*, users.username as creator_name')
            ->join('users', 'users.user_id = bank_ujian.created_by')
            ->where('bank_ujian.kategori', $kategori)
            ->where('bank_ujian.jenis_ujian_id', $jenisUjianId);

        if ($kategori !== 'umum') {
            $ujianList = $ujianList->where('bank_ujian.created_by', $userId);
        }

        $ujianList = $ujianList->get()->getResultArray();

        // Ambil info Mata Pelajaran
        $jenisUjian = $this->jenisUjianModel->find($jenisUjianId);

        $data = [
            'kategori' => $kategori,
            'jenisUjian' => $jenisUjian,
            'ujianList' => $ujianList
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
            return redirect()->to('guru/bank-soal')->with('error', 'Anda tidak memiliki akses ke bank ujian ini');
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

        // Validasi akses
        $bankUjian = $this->db->table('bank_ujian')->where('bank_ujian_id', $bankUjianId)->get()->getRowArray();
        if (!$bankUjian || ($bankUjian['kategori'] !== 'umum' && $bankUjian['created_by'] != $userId)) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menambah soal ke bank ujian ini');
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
            'foto' => 'max_size[foto,2048]|mime_in[foto,image/jpg,image/jpeg,image/png]|ext_in[foto,png,jpg,jpeg]',
            'pembahasan' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $errorMessage = 'Validasi gagal: ' . implode(', ', $errors);
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
            'pembahasan' => $this->request->getPost('pembahasan')
        ];

        // Upload foto jika ada
        $fotoFile = $this->request->getFile('foto');
        if ($fotoFile->isValid() && !$fotoFile->hasMoved()) {
            $newName = $fotoFile->getRandomName();
            $uploadPath = FCPATH . 'uploads/soal';

            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $fotoFile->move($uploadPath, $newName);
            $data['foto'] = $newName;
        }

        try {
            $this->soalUjianModel->insert($data);
            return redirect()->back()->with('success', 'Soal berhasil ditambahkan ke bank ujian');
        } catch (\Exception $e) {
            log_message('error', 'Error saat menambahkan soal bank ujian: ' . $e->getMessage());
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
            'foto' => 'max_size[foto,2048]|mime_in[foto,image/jpg,image/jpeg,image/png]|ext_in[foto,png,jpg,jpeg]',
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

        if ($fotoFile->isValid() && !$fotoFile->hasMoved()) {
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
                'message' => 'Anda tidak memiliki akses ke bank ujian ini'
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
}
