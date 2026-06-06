<?php
namespace App\Controllers\Admin;

use CodeIgniter\Controller;
use App\Models\UserModel;
use App\Models\GuruModel;
use App\Models\SiswaModel;
use App\Models\KelasModel;
use App\Models\UjianModel;
use App\Models\SoalUjianModel;
use App\Models\JenisUjianModel;
use App\Models\JadwalUjianModel;
use App\Models\HasilUjianModel;
use App\Models\PesertaUjianModel;
use App\Models\PengumumanModel;
use App\Models\SekolahModel;
use App\Models\VariabelModel;
use App\Models\IndikatorModel;
use App\Models\MateriModel;
use App\Models\UjianBankModel;
use App\Models\PaketUjianModel;
use App\Models\UjianSoalCatModel;
use App\Models\UjianCatParamModel;
use Config\Database;

class Admin extends Controller
{
    protected $userModel;
    protected $guruModel;
    protected $siswaModel;
    protected $kelasModel;
    protected $ujianModel;
    protected $soalUjianModel;
    protected $jenisUjianModel;
    protected $jadwalUjianModel;
    protected $hasilUjianModel;
    protected $pesertaUjianModel;
    protected $pengumumanModel;
    protected $sekolahModel;
    protected $variabelModel;
    protected $indikatorModel;
    protected $materiModel;
    protected $ujianBankModel;
    protected $paketUjianModel;
    protected $ujianSoalCatModel;
    protected $ujianCatParamModel;
    protected $db;

    // Inisialisasi semua model yang dipakai di controller ini — dipanggil otomatis sebelum action manapun
    public function __construct()
    {
        $this->db = Database::connect();
        $this->userModel = new UserModel();
        $this->guruModel = new GuruModel();
        $this->siswaModel = new SiswaModel();
        $this->kelasModel = new KelasModel();
        $this->ujianModel = new UjianModel();
        $this->soalUjianModel = new SoalUjianModel();
        $this->jenisUjianModel = new JenisUjianModel();
        $this->jadwalUjianModel = new JadwalUjianModel();
        $this->hasilUjianModel = new HasilUjianModel();
        $this->pesertaUjianModel = new PesertaUjianModel();
        $this->pengumumanModel = new PengumumanModel();
        $this->sekolahModel = new SekolahModel();
        $this->variabelModel = new VariabelModel();
        $this->indikatorModel = new IndikatorModel();
        $this->materiModel = new MateriModel();
        $this->ujianBankModel = new UjianBankModel();
        $this->paketUjianModel = new PaketUjianModel();
        $this->ujianSoalCatModel = new UjianSoalCatModel();
        $this->ujianCatParamModel = new UjianCatParamModel();
    }

    // Halaman utama admin — tampilkan statistik ringkas: total guru, siswa, sekolah, kelas
    public function dashboard()
    {
        $db = \Config\Database::connect();

        $data['stats'] = [
            'total_guru' => $db->table('guru')->countAllResults(),
            'total_siswa' => $db->table('siswa')->countAllResults(),
            'total_sekolah' => $db->table('sekolah')->countAllResults(),
            'total_kelas' => $db->table('kelas')->countAllResults()
        ];

        return view('admin/dashboard', $data);
    }

    // ===== KELOLA GURU =====

    // Daftar semua guru dengan info sekolah dan total kelas yang diajar
    public function daftarGuru()
    {
        $db = \Config\Database::connect();

        // Query untuk mengambil data guru dengan detail sekolah dan jumlah kelas yang diajar
        $data['guru'] = $db->table('users u')
            ->select('u.user_id, u.username, u.email, u.status, u.created_at,
                 g.guru_id, g.nip, g.nama_lengkap, g.mata_pelajaran, g.sekolah_id,
                 s.nama_sekolah,
                 COUNT(DISTINCT kg.kelas_id) as total_kelas')
            ->join('guru g', 'g.user_id = u.user_id', 'left')
            ->join('sekolah s', 's.sekolah_id = g.sekolah_id', 'left')
            ->join('kelas_guru kg', 'kg.guru_id = g.guru_id', 'left')
            ->where('u.role', 'guru')
            ->groupBy('u.user_id, u.username, u.email, u.status, u.created_at,
                  g.guru_id, g.nip, g.nama_lengkap, g.mata_pelajaran, g.sekolah_id,
                  s.nama_sekolah')
            ->orderBy('g.nama_lengkap', 'ASC')
            ->get()
            ->getResultArray();

        return view('admin/guru/daftar', $data);
    }

    // Form tambah guru — siapkan data sekolah dan semua kelas (termasuk sekolah-nya) untuk cascade dropdown di JS
    public function formTambahGuru()
    {
        $sekolahModel = new \App\Models\SekolahModel();
        $data['sekolah'] = $sekolahModel->findAll();

        // Ambil semua kelas dengan info sekolah untuk JavaScript
        $db = \Config\Database::connect();
        $data['kelas'] = $db->table('kelas k')
            ->select('k.kelas_id, k.nama_kelas, k.tahun_ajaran, k.sekolah_id, s.nama_sekolah')
            ->join('sekolah s', 's.sekolah_id = k.sekolah_id')
            ->orderBy('s.nama_sekolah', 'ASC')
            ->orderBy('k.nama_kelas', 'ASC')
            ->get()
            ->getResultArray();

        return view('admin/guru/tambah', $data);
    }

    // Proses simpan guru baru — insert ke tabel users + guru dalam satu transaksi, lalu assign kelas jika ada
    public function tambahGuru()
    {
        $rules = [
            'username' => 'required|min_length[4]|is_unique[users.username]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'nama_lengkap' => 'required|min_length[3]',
            'nip' => 'permit_empty|is_unique[guru.nip]',
            'mata_pelajaran' => 'required',
            'sekolah_id' => 'required|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        try {
            $db = \Config\Database::connect();
            $db->transStart();

            // Insert ke tabel users
            $userData = [
                'username' => $this->request->getPost('username'),
                'email' => $this->request->getPost('email'),
                'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                'role' => 'guru',
                'status' => 'active'
            ];

            $userId = $this->userModel->insert($userData);

            if ($userId) {
                // Insert ke tabel guru
                $guruData = [
                    'user_id' => $userId,
                    'sekolah_id' => $this->request->getPost('sekolah_id'),
                    'nip' => $this->request->getPost('nip') ?: null,
                    'nama_lengkap' => $this->request->getPost('nama_lengkap'),
                    'mata_pelajaran' => $this->request->getPost('mata_pelajaran')
                ];

                $guruId = $this->guruModel->insert($guruData);

                // Handle assignment kelas jika ada
                $kelasIds = $this->request->getPost('kelas_ids');
                if (!empty($kelasIds) && is_array($kelasIds)) {
                    $sekolahId = $this->request->getPost('sekolah_id');

                    foreach ($kelasIds as $kelasId) {
                        // Validasi kelas berada di sekolah yang sama
                        $kelas = $db->table('kelas')
                            ->where('kelas_id', $kelasId)
                            ->where('sekolah_id', $sekolahId)
                            ->get()
                            ->getRowArray();

                        if ($kelas) {
                            // Insert ke tabel kelas_guru
                            $db->table('kelas_guru')->insert([
                                'kelas_id' => $kelasId,
                                'guru_id' => $guruId,
                                'created_at' => date('Y-m-d H:i:s')
                            ]);
                        }
                    }
                }

                $db->transComplete();

                if ($db->transStatus() === FALSE) {
                    throw new \Exception('Transaction failed');
                }

                session()->setFlashdata('success', 'Guru berhasil ditambahkan!');
                return redirect()->to(base_url('admin/guru'));
            }
        } catch (\Exception $e) {
            log_message('error', 'Error adding guru: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat menambah guru.');
            return redirect()->back()->withInput();
        }
    }

    // Form edit guru — ambil data guru existing, kelas yang sudah diajar, dan semua kelas untuk assignment baru
    public function formEditGuru($userId)
    {
        $db = \Config\Database::connect();

        // Ambil data guru
        $guru = $db->table('users u')
            ->select('u.user_id, u.username, u.email, u.status, u.created_at, 
                 g.guru_id, g.nip, g.nama_lengkap, g.mata_pelajaran, g.sekolah_id,
                 s.nama_sekolah')
            ->join('guru g', 'g.user_id = u.user_id', 'left')
            ->join('sekolah s', 's.sekolah_id = g.sekolah_id', 'left')
            ->where('u.user_id', $userId)
            ->where('u.role', 'guru')
            ->get()
            ->getRowArray();

        if (!$guru) {
            session()->setFlashdata('error', 'Data guru tidak ditemukan');
            return redirect()->to(base_url('admin/guru'));
        }

        // Set default values
        $defaultFields = [
            'user_id' => '',
            'username' => '',
            'email' => '',
            'status' => 'active',
            'guru_id' => '',
            'sekolah_id' => '',
            'nip' => '',
            'nama_lengkap' => '',
            'mata_pelajaran' => '',
            'nama_sekolah' => ''
        ];

        $guru = array_merge($defaultFields, $guru ?: []);

        // Ambil data sekolah
        $sekolahModel = new \App\Models\SekolahModel();
        $data['sekolah'] = $sekolahModel->findAll();

        // Ambil kelas yang sudah diajar oleh guru ini
        $data['kelasGuru'] = $db->table('kelas_guru kg')
            ->select('kg.*, k.nama_kelas, k.tahun_ajaran, k.kelas_id')
            ->join('kelas k', 'k.kelas_id = kg.kelas_id')
            ->where('kg.guru_id', $guru['guru_id'])
            ->orderBy('k.nama_kelas', 'ASC')
            ->get()
            ->getResultArray();

        // Ambil semua kelas untuk JavaScript (untuk assignment baru)
        $data['allKelas'] = $db->table('kelas k')
            ->select('k.kelas_id, k.nama_kelas, k.tahun_ajaran, k.sekolah_id')
            ->orderBy('k.nama_kelas', 'ASC')
            ->get()
            ->getResultArray();

        $data['guru'] = $guru;

        return view('admin/guru/edit', $data);
    }


    // Proses update data guru — password hanya diupdate kalau diisi, pakai raw query untuk update partial
    public function editGuru($userId)
    {
        // Validasi input
        $rules = [
            'username' => "required|min_length[4]",
            'email'    => "required|valid_email",
            'nama_lengkap' => 'required|min_length[3]',
            'mata_pelajaran' => 'required',
            'sekolah_id' => 'required|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        try {
            $db = \Config\Database::connect();

            // Ambil data input
            $username = $this->request->getPost('username');
            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');
            $namaLengkap = $this->request->getPost('nama_lengkap');
            $nip = $this->request->getPost('nip');
            $mataPelajaran = $this->request->getPost('mata_pelajaran');
            $sekolahId = $this->request->getPost('sekolah_id');

            // Update tabel users dengan raw query
            $sqlUser = "UPDATE users SET username = ?, email = ?";
            $paramsUser = [$username, $email];

            if (!empty($password)) {
                $sqlUser .= ", password = ?";
                $paramsUser[] = password_hash($password, PASSWORD_DEFAULT);
            }

            $sqlUser .= " WHERE user_id = ?";
            $paramsUser[] = $userId;

            $db->query($sqlUser, $paramsUser);

            // Update tabel guru dengan raw query
            $sqlGuru = "UPDATE guru SET nama_lengkap = ?, nip = ?, mata_pelajaran = ?, sekolah_id = ? WHERE user_id = ?";
            $paramsGuru = [$namaLengkap, $nip, $mataPelajaran, $sekolahId, $userId];

            $db->query($sqlGuru, $paramsGuru);

            session()->setFlashdata('success', 'Data guru berhasil diperbarui!');
            return redirect()->to(base_url('admin/guru'));
        } catch (\Exception $e) {
            log_message('error', 'Error updating guru: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    // Assign satu kelas ke guru — validasi kelas harus dari sekolah yang sama dan belum terdaftar
    public function assignKelas()
    {
        $guruId = $this->request->getPost('guru_id');
        $kelasId = $this->request->getPost('kelas_id');

        if (!$guruId || !$kelasId) {
            session()->setFlashdata('error', 'Data tidak lengkap');
            return redirect()->back();
        }

        try {
            $db = \Config\Database::connect();

            // Ambil info guru untuk validasi sekolah
            $guru = $db->table('guru')->where('guru_id', $guruId)->get()->getRowArray();
            if (!$guru) {
                session()->setFlashdata('error', 'Guru tidak ditemukan');
                return redirect()->back();
            }

            // Validasi kelas dari sekolah yang sama
            $kelas = $db->table('kelas')
                ->where('kelas_id', $kelasId)
                ->where('sekolah_id', $guru['sekolah_id'])
                ->get()
                ->getRowArray();

            if (!$kelas) {
                session()->setFlashdata('error', 'Kelas tidak valid atau tidak berada di sekolah yang sama');
                return redirect()->back();
            }

            // Cek apakah guru sudah mengajar di kelas ini
            $existing = $db->table('kelas_guru')
                ->where('kelas_id', $kelasId)
                ->where('guru_id', $guruId)
                ->countAllResults();

            if ($existing > 0) {
                session()->setFlashdata('error', 'Guru sudah mengajar di kelas ini');
                return redirect()->back();
            }

            // Insert ke tabel kelas_guru
            $db->table('kelas_guru')->insert([
                'kelas_id' => $kelasId,
                'guru_id' => $guruId,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            session()->setFlashdata('success', 'Kelas berhasil ditambahkan ke guru!');
        } catch (\Exception $e) {
            log_message('error', 'Error assigning kelas: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat menambahkan kelas.');
        }

        return redirect()->back();
    }

    // Lepas guru dari kelas tertentu — hapus record di kelas_guru
    public function removeKelas($guruId, $kelasId)
    {
        try {
            $db = \Config\Database::connect();

            // Hapus dari tabel kelas_guru
            $affected = $db->table('kelas_guru')
                ->where('kelas_id', $kelasId)
                ->where('guru_id', $guruId)
                ->delete();

            if ($affected > 0) {
                session()->setFlashdata('success', 'Guru berhasil dikeluarkan dari kelas!');
            } else {
                session()->setFlashdata('warning', 'Tidak ada data yang dihapus');
            }
        } catch (\Exception $e) {
            log_message('error', 'Error removing kelas: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat mengeluarkan guru dari kelas.');
        }

        return redirect()->back();
    }


    // Nonaktifkan guru (soft delete) — tidak benar-benar dihapus dari database
    public function hapusGuru($userId)
    {
        try {
            $this->userModel->softDelete($userId);
            session()->setFlashdata('success', 'Guru berhasil dinonaktifkan!');
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Terjadi kesalahan saat menonaktifkan guru.');
        }

        return redirect()->to(base_url('admin/guru'));
    }

    // Aktifkan kembali guru yang sudah dinonaktifkan (restore dari soft delete)
    public function restoreGuru($userId)
    {
        try {
            $this->userModel->restore($userId);
            session()->setFlashdata('success', 'Guru berhasil diaktifkan kembali!');
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Terjadi kesalahan saat mengaktifkan guru.');
        }

        return redirect()->to(base_url('admin/guru'));
    }

    // ===== KELOLA SISWA =====

    // Daftar semua siswa — kelas dan sekolah diambil secara terpisah per-loop (perhatikan N+1 query jika data besar)
    public function daftarSiswa()
    {
        $db = \Config\Database::connect();

        // Query SANGAT SEDERHANA - ambil semua siswa
        $allSiswa = $db->table('users u')
            ->select('u.user_id, u.username, u.email, u.status, u.created_at')
            ->join('siswa s', 's.user_id = u.user_id', 'inner')  // INNER JOIN agar pasti ada data siswa
            ->select('s.siswa_id, s.nomor_peserta, s.nama_lengkap, s.jenis_kelamin, s.kelas_id')
            ->where('u.role', 'siswa')
            ->orderBy('s.nama_lengkap', 'ASC')
            ->get()
            ->getResultArray();

        // Loop untuk ambil data kelas dan sekolah secara terpisah
        foreach ($allSiswa as &$siswa) {
            if (!empty($siswa['kelas_id'])) {
                $kelas = $db->table('kelas')->where('kelas_id', $siswa['kelas_id'])->get()->getRowArray();
                if ($kelas) {
                    $siswa['nama_kelas'] = $kelas['nama_kelas'];
                    $siswa['tahun_ajaran'] = $kelas['tahun_ajaran'];

                    $sekolah = $db->table('sekolah')->where('sekolah_id', $kelas['sekolah_id'])->get()->getRowArray();
                    if ($sekolah) {
                        $siswa['nama_sekolah'] = $sekolah['nama_sekolah'];
                    }
                }
            }

            // Set default jika kosong
            $siswa['nama_kelas'] = $siswa['nama_kelas'] ?? 'Belum Ditentukan';
            $siswa['tahun_ajaran'] = $siswa['tahun_ajaran'] ?? '-';
            $siswa['nama_sekolah'] = $siswa['nama_sekolah'] ?? 'Belum Ditentukan';
        }

        $data['siswa'] = $allSiswa;
        return view('admin/siswa/daftar', $data);
    }

    // Form tambah siswa — siapkan dropdown sekolah dan kelas untuk cascade di JS
    public function formTambahSiswa()
    {
        $sekolahModel = new \App\Models\SekolahModel();
        $data['sekolah'] = $sekolahModel->findAll();

        // Ambil semua kelas dengan info sekolah untuk JavaScript
        $db = \Config\Database::connect();
        $data['kelas'] = $db->table('kelas k')
            ->select('k.kelas_id, k.nama_kelas, k.tahun_ajaran, k.sekolah_id, s.nama_sekolah')
            ->join('sekolah s', 's.sekolah_id = k.sekolah_id')
            ->orderBy('s.nama_sekolah', 'ASC')
            ->orderBy('k.nama_kelas', 'ASC')
            ->get()
            ->getResultArray();

        return view('admin/siswa/tambah', $data);
    }

    // Proses simpan siswa baru — validasi kelas harus berada di sekolah yang dipilih, lalu insert ke users + siswa
    public function tambahSiswa()
    {
        $rules = [
            'username' => 'required|min_length[4]|is_unique[users.username]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'nama_lengkap' => 'required|min_length[3]',
            'jenis_kelamin' => 'permit_empty|in_list[Laki-laki,Perempuan]',
            'nomor_peserta' => 'required|is_unique[siswa.nomor_peserta]',
            'sekolah_id' => 'required|numeric',
            'kelas_id' => 'required|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        try {
            $db = \Config\Database::connect();

            // Validasi kelas berada di sekolah yang dipilih
            $sekolahId = $this->request->getPost('sekolah_id');
            $kelasId = $this->request->getPost('kelas_id');

            $kelas = $db->table('kelas')
                ->where('kelas_id', $kelasId)
                ->where('sekolah_id', $sekolahId)
                ->get()
                ->getRowArray();

            if (!$kelas) {
                session()->setFlashdata('error', 'Kelas yang dipilih tidak valid untuk sekolah tersebut.');
                return redirect()->back()->withInput();
            }

            // Insert ke tabel users
            $userData = [
                'username' => $this->request->getPost('username'),
                'email' => $this->request->getPost('email'),
                'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                'role' => 'siswa',
                'status' => 'active'
            ];

            $userId = $this->userModel->insert($userData);

            if ($userId) {
                // Insert ke tabel siswa
                $siswaData = [
                    'user_id' => $userId,
                    'kelas_id' => $kelasId,
                    'nomor_peserta' => $this->request->getPost('nomor_peserta'),
                    'nama_lengkap' => $this->request->getPost('nama_lengkap'),
                    'jenis_kelamin' => $this->request->getPost('jenis_kelamin') ?: null
                ];

                $this->siswaModel->insert($siswaData);

                session()->setFlashdata('success', 'Siswa berhasil ditambahkan!');
                return redirect()->to(base_url('admin/siswa'));
            }
        } catch (\Exception $e) {
            log_message('error', 'Error adding siswa: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat menambah siswa.');
            return redirect()->back()->withInput();
        }
    }

    // Form edit siswa — ambil data user dan siswa secara terpisah lalu merge, sekolah didapat dari kelas
    public function formEditSiswa($userId)
    {
        $db = \Config\Database::connect();

        // STEP 1: Cek user dulu
        $user = $db->table('users')->where('user_id', $userId)->where('role', 'siswa')->get()->getRowArray();
        if (!$user) {
            session()->setFlashdata('error', 'User tidak ditemukan');
            return redirect()->to(base_url('admin/siswa'));
        }

        // STEP 2: Cek siswa
        $siswaData = $db->table('siswa')->where('user_id', $userId)->get()->getRowArray();
        if (!$siswaData) {
            session()->setFlashdata('error', 'Data siswa tidak ditemukan di tabel siswa');
            return redirect()->to(base_url('admin/siswa'));
        }

        // STEP 3: Gabungkan data user + siswa
        $siswa = array_merge($user, $siswaData);

        // STEP 4: Ambil data kelas dan sekolah jika ada (OPSIONAL)
        if (!empty($siswa['kelas_id'])) {
            $kelas = $db->table('kelas')->where('kelas_id', $siswa['kelas_id'])->get()->getRowArray();
            if ($kelas) {
                $siswa['nama_kelas'] = $kelas['nama_kelas'];
                $siswa['tahun_ajaran'] = $kelas['tahun_ajaran'];

                // Ambil sekolah dari kelas
                $sekolah = $db->table('sekolah')->where('sekolah_id', $kelas['sekolah_id'])->get()->getRowArray();
                if ($sekolah) {
                    $siswa['sekolah_id'] = $sekolah['sekolah_id'];
                    $siswa['nama_sekolah'] = $sekolah['nama_sekolah'];
                }
            }
        }

        // STEP 5: Set default untuk field yang mungkin kosong
        $siswa['nama_kelas'] = $siswa['nama_kelas'] ?? 'Belum Ditentukan';
        $siswa['tahun_ajaran'] = $siswa['tahun_ajaran'] ?? '-';
        $siswa['nama_sekolah'] = $siswa['nama_sekolah'] ?? 'Belum Ditentukan';
        $siswa['sekolah_id'] = $siswa['sekolah_id'] ?? '';
        $siswa['jenis_kelamin'] = $siswa['jenis_kelamin'] ?? '';

        // Data untuk dropdown
        $sekolahModel = new \App\Models\SekolahModel();
        $data['sekolah'] = $sekolahModel->findAll();

        $data['kelas'] = $db->table('kelas k')
            ->select('k.kelas_id, k.nama_kelas, k.tahun_ajaran, k.sekolah_id, s.nama_sekolah')
            ->join('sekolah s', 's.sekolah_id = k.sekolah_id')
            ->orderBy('s.nama_sekolah', 'ASC')
            ->orderBy('k.nama_kelas', 'ASC')
            ->get()
            ->getResultArray();

        $data['siswa'] = $siswa;

        return view('admin/siswa/edit', $data);
    }


    // Proses update data siswa — validasi kelas-sekolah, update users dan siswa dengan raw query
    public function editSiswa($userId)
    {
        $siswa = $this->siswaModel->where('user_id', $userId)->first();
        if (!$siswa) {
            session()->setFlashdata('error', 'Data siswa tidak ditemukan');
            return redirect()->to(base_url('admin/siswa'));
        }

        $rules = [
            'username' => "required|min_length[4]|is_unique[users.username,user_id,{$userId}]",
            'email'    => "required|valid_email|is_unique[users.email,user_id,{$userId}]",
            'nama_lengkap' => 'required|min_length[3]',
            'jenis_kelamin' => 'permit_empty|in_list[Laki-laki,Perempuan]',
            'nomor_peserta' => "required|is_unique[siswa.nomor_peserta,siswa_id,{$siswa['siswa_id']}]",
            'sekolah_id' => 'required|numeric',
            'kelas_id' => 'required|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        try {
            $db = \Config\Database::connect();

            // Validasi kelas berada di sekolah yang dipilih
            $sekolahId = $this->request->getPost('sekolah_id');
            $kelasId = $this->request->getPost('kelas_id');

            $kelas = $db->table('kelas')
                ->where('kelas_id', $kelasId)
                ->where('sekolah_id', $sekolahId)
                ->get()
                ->getRowArray();

            if (!$kelas) {
                session()->setFlashdata('error', 'Kelas yang dipilih tidak valid untuk sekolah tersebut.');
                return redirect()->back()->withInput();
            }

            // Ambil data input
            $username = $this->request->getPost('username');
            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');
            $namaLengkap = $this->request->getPost('nama_lengkap');
            $jenisKelamin = $this->request->getPost('jenis_kelamin');
            $nomorPeserta = $this->request->getPost('nomor_peserta');

            // Update tabel users
            $sqlUser = "UPDATE users SET username = ?, email = ?";
            $paramsUser = [$username, $email];

            if (!empty($password)) {
                $sqlUser .= ", password = ?";
                $paramsUser[] = password_hash($password, PASSWORD_DEFAULT);
            }

            $sqlUser .= " WHERE user_id = ?";
            $paramsUser[] = $userId;

            $result = $db->query($sqlUser, $paramsUser);

            if (!$result) {
                throw new \Exception('User update failed: ' . $db->error()['message']);
            }

            // Update tabel siswa
            $sqlSiswa = "UPDATE siswa SET nama_lengkap = ?, jenis_kelamin = ?, nomor_peserta = ?, kelas_id = ? WHERE user_id = ?";
            $paramsSiswa = [$namaLengkap, $jenisKelamin, $nomorPeserta, $kelasId, $userId];

            $result = $db->query($sqlSiswa, $paramsSiswa);

            if (!$result) {
                throw new \Exception('Siswa update failed: ' . $db->error()['message']);
            }

            session()->setFlashdata('success', 'Data siswa berhasil diperbarui!');
            return redirect()->to(base_url('admin/siswa'));
        } catch (\Exception $e) {
            log_message('error', 'Error updating siswa: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }



    // Nonaktifkan siswa (soft delete) — tidak benar-benar dihapus dari database
    public function hapusSiswa($userId)
    {
        try {
            $this->userModel->softDelete($userId);
            session()->setFlashdata('success', 'Siswa berhasil dinonaktifkan!');
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Terjadi kesalahan saat menonaktifkan siswa.');
        }

        return redirect()->to(base_url('admin/siswa'));
    }

    // Aktifkan kembali siswa yang sudah dinonaktifkan
    public function restoreSiswa($userId)
    {
        try {
            $this->userModel->restore($userId);
            session()->setFlashdata('success', 'Siswa berhasil diaktifkan kembali!');
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Terjadi kesalahan saat mengaktifkan siswa.');
        }

        return redirect()->to(base_url('admin/siswa'));
    }

    // Buat banyak siswa sekaligus dari URL parameter — maksimal 50 siswa per request, password default 'password123'
    public function batchCreateSiswa()
    {
        $kelasId = $this->request->getGet('kelas');
        $jumlah = (int)$this->request->getGet('jumlah');
        $prefix = $this->request->getGet('prefix');
        $jenisKelamin = $this->request->getGet('jenis_kelamin');

        if (!$kelasId || !$jumlah || !$prefix || $jumlah > 50) {
            session()->setFlashdata('error', 'Parameter tidak valid');
            return redirect()->to(base_url('admin/siswa/tambah'));
        }

        try {
            $berhasil = 0;
            $gagal = 0;
            $errors = [];

            for ($i = 1; $i <= $jumlah; $i++) {
                $num = str_pad($i, 3, '0', STR_PAD_LEFT);
                $username = strtolower($prefix) . $num;
                $email = $username . '@sekolah.com';
                $nama = $prefix . ' ' . $num;
                $noPeserta = $prefix . $num;
                $password = 'password123';

                // Tentukan jenis kelamin
                $gender = null;
                if ($jenisKelamin) {
                    $gender = $jenisKelamin;
                } else {
                    $gender = ($i % 2 === 1) ? 'Laki-laki' : 'Perempuan';
                }

                // Cek apakah username sudah ada
                if ($this->userModel->where('username', $username)->first()) {
                    $gagal++;
                    $errors[] = "Username {$username} sudah digunakan";
                    continue;
                }

                // HAPUS CEK NOMOR PESERTA UNIQUE
                // if ($this->siswaModel->where('nomor_peserta', $noPeserta)->first()) {
                //     $gagal++;
                //     $errors[] = "Nomor peserta {$noPeserta} sudah digunakan";
                //     continue;
                // }

                // Insert user
                $userData = [
                    'username' => $username,
                    'email' => $email,
                    'password' => password_hash($password, PASSWORD_DEFAULT),
                    'role' => 'siswa',
                    'status' => 'active'
                ];

                $userId = $this->userModel->insert($userData);

                if ($userId) {
                    // Insert siswa
                    $siswaData = [
                        'user_id' => $userId,
                        'kelas_id' => $kelasId,
                        'nomor_peserta' => $noPeserta,
                        'nama_lengkap' => $nama,
                        'jenis_kelamin' => $gender
                    ];

                    if ($this->siswaModel->insert($siswaData)) {
                        $berhasil++;
                    } else {
                        $gagal++;
                        $this->userModel->delete($userId);
                    }
                } else {
                    $gagal++;
                }
            }

            $message = "Batch create selesai. Berhasil: {$berhasil}, Gagal: {$gagal}";

            if ($gagal > 0) {
                $message .= "\nError: " . implode(', ', array_slice($errors, 0, 5));
                if (count($errors) > 5) {
                    $message .= " (dan " . (count($errors) - 5) . " error lainnya)";
                }
                session()->setFlashdata('warning', $message);
            } else {
                session()->setFlashdata('success', $message);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error batch create siswa: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat batch create siswa');
        }

        return redirect()->to(base_url('admin/siswa'));
    }

    // ===== KELOLA SEKOLAH =====

    // Daftar semua sekolah dengan jumlah guru dan kelas masing-masing
    public function daftarSekolah()
    {
        // Ambil data sekolah dengan semua field, jumlah guru, dan jumlah kelas
        $db = \Config\Database::connect();
        $data['sekolah'] = $db->table('sekolah s')
            ->select('s.sekolah_id, s.nama_sekolah, s.alamat, s.telepon, s.email, 
                 COUNT(DISTINCT g.guru_id) as total_guru,
                 COUNT(DISTINCT k.kelas_id) as total_kelas')
            ->join('guru g', 'g.sekolah_id = s.sekolah_id', 'left')
            ->join('kelas k', 'k.sekolah_id = s.sekolah_id', 'left')
            ->groupBy('s.sekolah_id, s.nama_sekolah, s.alamat, s.telepon, s.email')
            ->orderBy('s.nama_sekolah', 'ASC')
            ->get()
            ->getResultArray();

        return view('admin/sekolah/daftar', $data);
    }

    // Tampilkan daftar kelas milik sekolah tertentu beserta jumlah siswa dan guru per kelas
    public function daftarKelasBySekolah($sekolahId)
    {
        $sekolahModel = new \App\Models\SekolahModel();
        $sekolah = $sekolahModel->find($sekolahId);

        if (!$sekolah) {
            session()->setFlashdata('error', 'Sekolah tidak ditemukan');
            return redirect()->to(base_url('admin/sekolah'));
        }

        $db = \Config\Database::connect();

        // Ambil data kelas dengan jumlah siswa dan guru
        $kelas = $db->table('kelas k')
            ->select('k.*, 
                 COUNT(DISTINCT s.siswa_id) as total_siswa,
                 COUNT(DISTINCT kg.guru_id) as total_guru')
            ->join('siswa s', 's.kelas_id = k.kelas_id', 'left')
            ->join('kelas_guru kg', 'kg.kelas_id = k.kelas_id', 'left')
            ->where('k.sekolah_id', $sekolahId)
            ->groupBy('k.kelas_id')
            ->orderBy('k.tahun_ajaran', 'DESC')
            ->orderBy('k.nama_kelas', 'ASC')
            ->get()
            ->getResultArray();

        // Hitung total guru sekolah
        $sekolah['total_guru'] = $db->table('guru')
            ->where('sekolah_id', $sekolahId)
            ->countAllResults();

        $data = [
            'sekolah' => $sekolah,
            'kelas' => $kelas
        ];

        return view('admin/sekolah/kelas', $data);
    }

    // Form tambah sekolah baru (tidak ada data yang perlu diambil terlebih dahulu)
    public function formTambahSekolah()
    {
        return view('admin/sekolah/tambah');
    }

    // Proses simpan sekolah baru
    public function tambahSekolah()
    {
        $rules = [
            'nama_sekolah' => 'required|min_length[3]',
            'alamat' => 'permit_empty',
            'telepon' => 'permit_empty|min_length[10]',
            'email' => 'permit_empty|valid_email'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        try {
            $sekolahModel = new \App\Models\SekolahModel();
            $data = [
                'nama_sekolah' => $this->request->getPost('nama_sekolah'),
                'alamat' => $this->request->getPost('alamat'),
                'telepon' => $this->request->getPost('telepon'),
                'email' => $this->request->getPost('email')
            ];

            $sekolahModel->insert($data);
            session()->setFlashdata('success', 'Sekolah berhasil ditambahkan!');
            return redirect()->to(base_url('admin/sekolah'));
        } catch (\Exception $e) {
            log_message('error', 'Error adding sekolah: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat menambah sekolah.');
            return redirect()->back()->withInput();
        }
    }

    // Form edit sekolah — ambil data sekolah yang akan diedit
    public function formEditSekolah($sekolahId)
    {
        $sekolahModel = new \App\Models\SekolahModel();
        $sekolah = $sekolahModel->find($sekolahId);

        if (!$sekolah) {
            session()->setFlashdata('error', 'Data sekolah tidak ditemukan');
            return redirect()->to(base_url('admin/sekolah'));
        }

        $data['sekolah'] = $sekolah;
        return view('admin/sekolah/edit', $data);
    }

    // Proses update data sekolah
    public function editSekolah($sekolahId)
    {
        $sekolahModel = new \App\Models\SekolahModel();
        $sekolah = $sekolahModel->find($sekolahId);

        if (!$sekolah) {
            session()->setFlashdata('error', 'Data sekolah tidak ditemukan');
            return redirect()->to(base_url('admin/sekolah'));
        }

        $rules = [
            'nama_sekolah' => 'required|min_length[3]',
            'alamat' => 'permit_empty',
            'telepon' => 'permit_empty|min_length[10]',
            'email' => 'permit_empty|valid_email'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        try {
            $data = [
                'nama_sekolah' => $this->request->getPost('nama_sekolah'),
                'alamat' => $this->request->getPost('alamat'),
                'telepon' => $this->request->getPost('telepon'),
                'email' => $this->request->getPost('email')
            ];

            $sekolahModel->update($sekolahId, $data);
            session()->setFlashdata('success', 'Data sekolah berhasil diperbarui!');
            return redirect()->to(base_url('admin/sekolah'));
        } catch (\Exception $e) {
            log_message('error', 'Error updating sekolah: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat memperbarui sekolah.');
            return redirect()->back()->withInput();
        }
    }

    // Hapus sekolah — tidak bisa dihapus jika masih punya guru terdaftar
    public function hapusSekolah($sekolahId)
    {
        try {
            $sekolahModel = new \App\Models\SekolahModel();

            // Cek apakah sekolah masih memiliki guru
            $totalGuru = $this->guruModel->where('sekolah_id', $sekolahId)->countAllResults();

            if ($totalGuru > 0) {
                session()->setFlashdata('error', "Tidak dapat menghapus sekolah karena masih memiliki {$totalGuru} guru.");
                return redirect()->to(base_url('admin/sekolah'));
            }

            $sekolahModel->delete($sekolahId);
            session()->setFlashdata('success', 'Sekolah berhasil dihapus!');
        } catch (\Exception $e) {
            log_message('error', 'Error deleting sekolah: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat menghapus sekolah.');
        }

        return redirect()->to(base_url('admin/sekolah'));
    }

    // ===== KELOLA KELAS =====

    // Form tambah kelas dalam konteks sekolah tertentu
    public function formTambahKelasSekolah($sekolahId)
    {
        $sekolahModel = new \App\Models\SekolahModel();
        $sekolah = $sekolahModel->find($sekolahId);

        if (!$sekolah) {
            session()->setFlashdata('error', 'Sekolah tidak ditemukan');
            return redirect()->to(base_url('admin/sekolah'));
        }

        $data = [
            'sekolah' => $sekolah,
            'sekolah_id' => $sekolahId
        ];

        return view('admin/sekolah/tambah_kelas', $data);
    }

    // Proses update kelas via sekolah — validasi kelas harus benar-benar milik sekolah yang bersangkutan
    public function editKelasSekolah($sekolahId, $kelasId)
    {
        $sekolahModel = new \App\Models\SekolahModel();
        $sekolah = $sekolahModel->find($sekolahId);

        if (!$sekolah) {
            session()->setFlashdata('error', 'Sekolah tidak ditemukan');
            return redirect()->to(base_url('admin/sekolah'));
        }

        $kelas = $this->kelasModel->find($kelasId);

        if (!$kelas || $kelas['sekolah_id'] != $sekolahId) {
            session()->setFlashdata('error', 'Kelas tidak ditemukan atau tidak berada di sekolah ini');
            return redirect()->to(base_url('admin/sekolah/' . $sekolahId . '/kelas'));
        }

        $rules = [
            'nama_kelas' => 'required|min_length[2]',
            'tahun_ajaran' => 'required|regex_match[/^\d{4}\/\d{4}$/]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        try {
            $data = [
                'nama_kelas' => $this->request->getPost('nama_kelas'),
                'tahun_ajaran' => $this->request->getPost('tahun_ajaran')
                // sekolah_id tetap sama, tidak berubah
            ];

            $this->kelasModel->update($kelasId, $data);
            session()->setFlashdata('success', 'Data kelas berhasil diperbarui!');
            return redirect()->to(base_url('admin/sekolah/' . $sekolahId . '/kelas'));
        } catch (\Exception $e) {
            log_message('error', 'Error updating kelas: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat memperbarui kelas.');
            return redirect()->back()->withInput();
        }
    }

    // Form edit kelas via sekolah — tampilkan form dengan data kelas yang sudah ada
    public function formEditKelasSekolah($sekolahId, $kelasId)
    {
        $sekolahModel = new \App\Models\SekolahModel();
        $sekolah = $sekolahModel->find($sekolahId);

        if (!$sekolah) {
            session()->setFlashdata('error', 'Sekolah tidak ditemukan');
            return redirect()->to(base_url('admin/sekolah'));
        }

        $kelas = $this->kelasModel->find($kelasId);

        if (!$kelas || $kelas['sekolah_id'] != $sekolahId) {
            session()->setFlashdata('error', 'Kelas tidak ditemukan atau tidak berada di sekolah ini');
            return redirect()->to(base_url('admin/sekolah/' . $sekolahId . '/kelas'));
        }

        $data = [
            'sekolah' => $sekolah,
            'kelas' => $kelas
        ];

        return view('admin/sekolah/edit_kelas', $data);
    }

    // Hapus kelas — tidak bisa dihapus jika masih ada siswa atau guru yang terkait
    public function hapusKelasSekolah($sekolahId, $kelasId)
    {
        try {
            $sekolahModel = new \App\Models\SekolahModel();
            $sekolah = $sekolahModel->find($sekolahId);

            if (!$sekolah) {
                session()->setFlashdata('error', 'Sekolah tidak ditemukan');
                return redirect()->to(base_url('admin/sekolah'));
            }

            $kelas = $this->kelasModel->find($kelasId);

            if (!$kelas || $kelas['sekolah_id'] != $sekolahId) {
                session()->setFlashdata('error', 'Kelas tidak ditemukan atau tidak berada di sekolah ini');
                return redirect()->to(base_url('admin/sekolah/' . $sekolahId . '/kelas'));
            }

            // Cek apakah kelas masih memiliki siswa
            $totalSiswa = $this->siswaModel->where('kelas_id', $kelasId)->countAllResults();

            // Cek apakah kelas masih memiliki guru
            $db = \Config\Database::connect();
            $totalGuru = $db->table('kelas_guru')->where('kelas_id', $kelasId)->countAllResults();

            if ($totalSiswa > 0) {
                session()->setFlashdata('error', "Tidak dapat menghapus kelas karena masih memiliki {$totalSiswa} siswa.");
                return redirect()->to(base_url('admin/sekolah/' . $sekolahId . '/kelas'));
            }

            if ($totalGuru > 0) {
                session()->setFlashdata('error', "Tidak dapat menghapus kelas karena masih memiliki {$totalGuru} guru pengajar.");
                return redirect()->to(base_url('admin/sekolah/' . $sekolahId . '/kelas'));
            }

            $this->kelasModel->delete($kelasId);
            session()->setFlashdata('success', 'Kelas berhasil dihapus!');
        } catch (\Exception $e) {
            log_message('error', 'Error deleting kelas: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat menghapus kelas.');
        }

        return redirect()->to(base_url('admin/sekolah/' . $sekolahId . '/kelas'));
    }

    // Detail kelas: tampilkan daftar guru (termasuk kelas lain yang diajar via GROUP_CONCAT), siswa, dan guru tersedia untuk di-assign
    public function detailKelasSekolah($sekolahId, $kelasId)
    {
        $db = \Config\Database::connect();

        // Validasi sekolah
        $sekolahModel = new \App\Models\SekolahModel();
        $sekolah = $sekolahModel->find($sekolahId);

        if (!$sekolah) {
            session()->setFlashdata('error', 'Sekolah tidak ditemukan');
            return redirect()->to(base_url('admin/sekolah'));
        }

        // Ambil detail kelas
        $kelas = $db->table('kelas k')
            ->select('k.*, s.nama_sekolah, s.sekolah_id')
            ->join('sekolah s', 's.sekolah_id = k.sekolah_id')
            ->where('k.kelas_id', $kelasId)
            ->where('k.sekolah_id', $sekolahId)
            ->get()
            ->getRowArray();

        if (!$kelas) {
            session()->setFlashdata('error', 'Kelas tidak ditemukan atau tidak berada di sekolah ini');
            return redirect()->to(base_url('admin/sekolah/' . $sekolahId . '/kelas'));
        }

        // Ambil daftar guru yang mengajar di kelas ini (dengan info kelas lain yang diajar)
        $daftarGuru = $db->table('kelas_guru kg')
            ->select('kg.*, g.guru_id, g.nama_lengkap, g.nip, g.mata_pelajaran, 
                 u.user_id, u.username, u.status,
                 GROUP_CONCAT(DISTINCT CASE 
                    WHEN k2.kelas_id != kg.kelas_id THEN k2.nama_kelas 
                    END ORDER BY k2.nama_kelas SEPARATOR ", ") as kelas_lain')
            ->join('guru g', 'g.guru_id = kg.guru_id')
            ->join('users u', 'u.user_id = g.user_id')
            ->join('kelas_guru kg2', 'kg2.guru_id = g.guru_id', 'left')
            ->join('kelas k2', 'k2.kelas_id = kg2.kelas_id', 'left')
            ->where('kg.kelas_id', $kelasId)
            ->groupBy('kg.kelas_guru_id, kg.kelas_id, kg.guru_id, kg.created_at, kg.updated_at, 
                  g.guru_id, g.nama_lengkap, g.nip, g.mata_pelajaran, 
                  u.user_id, u.username, u.status')
            ->orderBy('g.nama_lengkap', 'ASC')
            ->get()
            ->getResultArray();

        // Ambil daftar siswa di kelas ini
        $daftarSiswa = $db->table('siswa s')
            ->select('s.*, u.user_id, u.username, u.status')
            ->join('users u', 'u.user_id = s.user_id')
            ->where('s.kelas_id', $kelasId)
            ->orderBy('s.nama_lengkap', 'ASC')
            ->get()
            ->getResultArray();

        // Ambil daftar guru yang tersedia untuk di-assign (HANYA dari sekolah yang sama)
        $assignedGuruIds = array_column($daftarGuru, 'guru_id');
        $whereNotIn = !empty($assignedGuruIds) ? $assignedGuruIds : [0];

        $availableGuru = $db->table('guru g')
            ->select('g.guru_id, g.nama_lengkap, g.mata_pelajaran,
                 GROUP_CONCAT(DISTINCT k.nama_kelas ORDER BY k.nama_kelas SEPARATOR ", ") as kelas_diajar')
            ->join('users u', 'u.user_id = g.user_id')
            ->join('kelas_guru kg', 'kg.guru_id = g.guru_id', 'left')
            ->join('kelas k', 'k.kelas_id = kg.kelas_id', 'left')
            ->where('g.sekolah_id', $sekolahId) // Filter sekolah
            ->where('u.status', 'active')
            ->whereNotIn('g.guru_id', $whereNotIn)
            ->groupBy('g.guru_id, g.nama_lengkap, g.mata_pelajaran')
            ->orderBy('g.nama_lengkap', 'ASC')
            ->get()
            ->getResultArray();

        $data = [
            'sekolah' => $sekolah,
            'kelas' => $kelas,
            'daftarGuru' => $daftarGuru,
            'daftarSiswa' => $daftarSiswa,
            'availableGuru' => $availableGuru
        ];

        return view('admin/sekolah/detail_kelas', $data);
    }

    // Assign guru ke kelas — guru harus dari sekolah yang sama dan belum mengajar di kelas ini
    public function assignGuruKelasSekolah($sekolahId, $kelasId)
    {
        $guruId = $this->request->getPost('guru_id');

        if (!$guruId) {
            session()->setFlashdata('error', 'Guru harus dipilih');
            return redirect()->back();
        }

        try {
            $db = \Config\Database::connect();

            // Validasi guru dari sekolah yang sama
            $guru = $db->table('guru')->where('guru_id', $guruId)->where('sekolah_id', $sekolahId)->get()->getRowArray();

            if (!$guru) {
                session()->setFlashdata('error', 'Guru tidak ditemukan atau tidak berada di sekolah ini');
                return redirect()->back();
            }

            // Cek apakah guru sudah mengajar di kelas ini
            $existing = $db->table('kelas_guru')
                ->where('kelas_id', $kelasId)
                ->where('guru_id', $guruId)
                ->countAllResults();

            if ($existing > 0) {
                session()->setFlashdata('error', 'Guru sudah mengajar di kelas ini');
                return redirect()->back();
            }

            // Insert ke tabel kelas_guru
            $db->table('kelas_guru')->insert([
                'kelas_id' => $kelasId,
                'guru_id' => $guruId,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            session()->setFlashdata('success', 'Guru berhasil di-assign ke kelas!');
        } catch (\Exception $e) {
            log_message('error', 'Error assigning guru: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat assign guru.');
        }

        return redirect()->to(base_url('admin/sekolah/' . $sekolahId . '/kelas/' . $kelasId . '/detail'));
    }

    // Lepas guru dari kelas via konteks sekolah
    public function removeGuruKelasSekolah($sekolahId, $kelasId, $guruId)
    {
        try {
            $db = \Config\Database::connect();

            $db->table('kelas_guru')
                ->where('kelas_id', $kelasId)
                ->where('guru_id', $guruId)
                ->delete();

            session()->setFlashdata('success', 'Guru berhasil dikeluarkan dari kelas!');
        } catch (\Exception $e) {
            log_message('error', 'Error removing guru: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat mengeluarkan guru.');
        }

        return redirect()->to(base_url('admin/sekolah/' . $sekolahId . '/kelas/' . $kelasId . '/detail'));
    }

    // Tampilkan form transfer siswa ke kelas lain — hanya kelas dalam sekolah yang sama yang ditampilkan
    public function transferSiswaSekolah($sekolahId, $kelasId, $siswaId)
    {
        $db = \Config\Database::connect();

        // Validasi sekolah
        $sekolahModel = new \App\Models\SekolahModel();
        $sekolah = $sekolahModel->find($sekolahId);

        if (!$sekolah) {
            session()->setFlashdata('error', 'Sekolah tidak ditemukan');
            return redirect()->to(base_url('admin/sekolah'));
        }

        // Ambil info siswa dan kelas
        $siswa = $db->table('siswa s')
            ->select('s.*, u.username, k.nama_kelas, k.sekolah_id, sk.nama_sekolah')
            ->join('users u', 'u.user_id = s.user_id')
            ->join('kelas k', 'k.kelas_id = s.kelas_id')
            ->join('sekolah sk', 'sk.sekolah_id = k.sekolah_id')
            ->where('s.siswa_id', $siswaId)
            ->where('s.kelas_id', $kelasId)
            ->where('k.sekolah_id', $sekolahId)
            ->get()
            ->getRowArray();

        if (!$siswa) {
            session()->setFlashdata('error', 'Siswa tidak ditemukan atau tidak berada di kelas/sekolah ini');
            return redirect()->to(base_url('admin/sekolah/' . $sekolahId . '/kelas/' . $kelasId . '/detail'));
        }

        // Ambil daftar kelas lain di sekolah yang sama
        $kelasLain = $db->table('kelas k')
            ->select('k.kelas_id, k.nama_kelas, k.tahun_ajaran, COUNT(s.siswa_id) as jumlah_siswa')
            ->join('siswa s', 's.kelas_id = k.kelas_id', 'left')
            ->where('k.sekolah_id', $sekolahId)
            ->where('k.kelas_id !=', $kelasId)
            ->groupBy('k.kelas_id, k.nama_kelas, k.tahun_ajaran')
            ->orderBy('k.tahun_ajaran', 'DESC')
            ->orderBy('k.nama_kelas', 'ASC')
            ->get()
            ->getResultArray();

        $data = [
            'sekolah' => $sekolah,
            'siswa' => $siswa,
            'kelasAsal' => $kelasId,
            'kelasLain' => $kelasLain
        ];

        return view('admin/sekolah/transfer_siswa', $data);
    }

    // Proses pindah siswa ke kelas tujuan — validasi kelas tujuan harus di sekolah yang sama
    public function prosesTransferSiswaSekolah()
    {
        $siswaId = $this->request->getPost('siswa_id');
        $sekolahId = $this->request->getPost('sekolah_id');
        $kelasAsalId = $this->request->getPost('kelas_asal_id');
        $kelasTujuanId = $this->request->getPost('kelas_tujuan_id');

        if (!$siswaId || !$sekolahId || !$kelasAsalId || !$kelasTujuanId) {
            session()->setFlashdata('error', 'Data tidak lengkap');
            return redirect()->back();
        }

        try {
            $db = \Config\Database::connect();

            // Validasi kelas tujuan di sekolah yang sama
            $kelasTujuan = $db->table('kelas')->where('kelas_id', $kelasTujuanId)->where('sekolah_id', $sekolahId)->get()->getRowArray();

            if (!$kelasTujuan) {
                session()->setFlashdata('error', 'Kelas tujuan tidak valid');
                return redirect()->back();
            }

            // Ambil info untuk log
            $siswa = $db->table('siswa')->select('nama_lengkap')->where('siswa_id', $siswaId)->get()->getRowArray();
            $kelasAsal = $db->table('kelas')->select('nama_kelas')->where('kelas_id', $kelasAsalId)->get()->getRowArray();

            // Update kelas siswa
            $affected = $db->table('siswa')
                ->where('siswa_id', $siswaId)
                ->update(['kelas_id' => $kelasTujuanId]);

            if ($affected > 0) {
                session()->setFlashdata(
                    'success',
                    "Siswa <strong>{$siswa['nama_lengkap']}</strong> berhasil dipindahkan dari " .
                        "<strong>{$kelasAsal['nama_kelas']}</strong> ke <strong>{$kelasTujuan['nama_kelas']}</strong>."
                );
            } else {
                session()->setFlashdata('warning', 'Tidak ada perubahan yang dilakukan');
            }
        } catch (\Exception $e) {
            log_message('error', 'Error transferring siswa: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat memindahkan siswa: ' . $e->getMessage());
        }

        return redirect()->to(base_url('admin/sekolah/' . $sekolahId . '/kelas/' . $kelasAsalId . '/detail'));
    }

    // Proses simpan kelas baru dalam sekolah — format tahun ajaran harus YYYY/YYYY
    public function tambahKelasSekolah($sekolahId)
    {
        $sekolahModel = new \App\Models\SekolahModel();
        $sekolah = $sekolahModel->find($sekolahId);

        if (!$sekolah) {
            session()->setFlashdata('error', 'Sekolah tidak ditemukan');
            return redirect()->to(base_url('admin/sekolah'));
        }

        $rules = [
            'nama_kelas' => 'required|min_length[2]',
            'tahun_ajaran' => 'required|regex_match[/^\d{4}\/\d{4}$/]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        try {
            $data = [
                'sekolah_id' => $sekolahId,
                'nama_kelas' => $this->request->getPost('nama_kelas'),
                'tahun_ajaran' => $this->request->getPost('tahun_ajaran')
            ];

            $this->kelasModel->insert($data);
            session()->setFlashdata('success', 'Kelas berhasil ditambahkan!');
            return redirect()->to(base_url('admin/sekolah/' . $sekolahId . '/kelas'));
        } catch (\Exception $e) {
            log_message('error', 'Error adding kelas: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat menambah kelas.');
            return redirect()->back()->withInput();
        }
    }


    // ===== KELOLA UJIAN =====

    // Halaman daftar ujian admin — tampilkan semua ujian tanpa filter, lengkap dengan info sekolah, kelas, dan guru pembuat
    public function ujian()
    {
        // Ambil SEMUA ujian dari database, sertakan parameter CAT via LEFT JOIN
        $data['ujian'] = $this->ujianModel
            ->select('ujian.*, jenis_ujian.nama_jenis, kelas.nama_kelas, sekolah.nama_sekolah, g.nama_lengkap as guru_pembuat')
            ->select('ucp.se_awal, ucp.se_minimum, ucp.delta_se_minimum, ucp.maksimal_soal_tampil')
            ->join('jenis_ujian', 'jenis_ujian.jenis_ujian_id = ujian.jenis_ujian_id', 'left')
            ->join('kelas', 'kelas.kelas_id = ujian.kelas_id', 'left')
            ->join('sekolah', 'sekolah.sekolah_id = COALESCE(kelas.sekolah_id, ujian.sekolah_id)', 'left', false)
            ->join('users u', 'u.user_id = ujian.created_by', 'left')
            ->join('guru g', 'g.user_id = u.user_id', 'left')
            ->join('ujian_param_cat ucp', 'ucp.ujian_id = ujian.id_ujian', 'left')
            ->orderBy('ujian.created_at', 'DESC')
            ->findAll();

        // Ambil SEMUA mata pelajaran untuk dropdown
        $data['jenis_ujian'] = $this->jenisUjianModel
            ->select('jenis_ujian.*, kelas.nama_kelas, sekolah.nama_sekolah')
            ->join('kelas', 'kelas.kelas_id = jenis_ujian.kelas_id', 'left')
            ->join('sekolah', 'sekolah.sekolah_id = kelas.sekolah_id', 'left')
            ->findAll();

        // Ambil SEMUA kelas untuk dropdown
        $data['kelas_guru'] = $this->kelasModel
            ->select('kelas.*, sekolah.nama_sekolah')
            ->join('sekolah', 'sekolah.sekolah_id = kelas.sekolah_id', 'left')
            ->orderBy('sekolah.nama_sekolah', 'ASC')
            ->orderBy('kelas.nama_kelas', 'ASC')
            ->findAll();

        // Menggunakan view yang sama dengan guru, tapi dari folder admin
        $data['sekolah'] = $this->sekolahModel->orderBy('nama_sekolah', 'ASC')->findAll();

        return view('admin/ujian/daftar', $data);
    }

    // Proses tambah ujian baru — support AJAX dan non-AJAX, validasi sekolah/kelas harus konsisten, tipe CAT/CBT
    public function tambahUjian()
    {
        $isAjax = $this->request->isAJAX();
        $userId = session()->get('user_id');

        // Validasi input form
        $rules = [
            'sekolah_id' => 'required|numeric',
            'jenis_ujian_id' => 'required|numeric',
            'nama_ujian' => 'required|min_length[3]|max_length[255]',
            'kode_ujian' => 'required|alpha_numeric_punct|min_length[3]|max_length[50]|is_unique[ujian.kode_ujian]',
            'deskripsi' => 'required|min_length[10]',
            'tipe_ujian' => 'required|in_list[CAT,CBT]',
            'tampilkan_pembahasan' => 'permit_empty',
            'visibilitas' => 'permit_empty',
            'pengulangan_aktif' => 'permit_empty',
            'maksimal_attempt' => 'permit_empty|numeric|less_than_equal_to[3]',
            'acak_urutan_soal' => 'permit_empty',
            'acak_pilihan_jawaban' => 'permit_empty',
            'maksimal_soal_tampil' => 'permit_empty|numeric',
            'se_awal' => 'permit_empty|decimal',
            'se_minimum' => 'permit_empty|decimal',
            'delta_se_minimum' => 'permit_empty|decimal',
            'durasi' => 'required|regex_match[/^([0-1][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/]',
            'kelas_id' => 'permit_empty|numeric'
        ];

        $tipeUjian = $this->request->getPost('tipe_ujian') ?: 'CAT';
        if ($tipeUjian === 'CAT') {
            $rules['se_awal'] = 'required|decimal';
            $rules['se_minimum'] = 'required|decimal';
            $rules['delta_se_minimum'] = 'required|decimal';
        }

        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            if ($isAjax) {
                return $this->response->setJSON(['success' => false, 'errors' => $errors]);
            }
            return redirect()->back()->withInput()->with('error', $errors);
        }

        $sekolahId = $this->normalizeNullableId($this->request->getPost('sekolah_id'));
        $kelasId = $this->normalizeNullableId($this->request->getPost('kelas_id'));

        if ($sekolahId === null && $kelasId !== null) {
            $msg = 'Ujian sekolah umum tidak boleh langsung dibatasi ke kelas tertentu.';
            if ($isAjax) return $this->response->setJSON(['success' => false, 'errors' => ['sekolah_id' => $msg]]);
            return redirect()->back()->withInput()->with('error', $msg);
        }

        if ($sekolahId !== null && !$this->sekolahModel->find($sekolahId)) {
            $msg = 'Sekolah yang dipilih tidak ditemukan.';
            if ($isAjax) return $this->response->setJSON(['success' => false, 'errors' => ['sekolah_id' => $msg]]);
            return redirect()->back()->withInput()->with('error', $msg);
        }

        if ($kelasId !== null) {
            $kelas = $this->kelasModel
                ->where('kelas_id', $kelasId)
                ->where('sekolah_id', $sekolahId)
                ->first();

            if (!$kelas) {
                $msg = 'Kelas tidak valid untuk sekolah yang dipilih.';
                if ($isAjax) return $this->response->setJSON(['success' => false, 'errors' => ['kelas_id' => $msg]]);
                return redirect()->back()->withInput()->with('error', $msg);
            }
        }

        $data = [
            'sekolah_id' => $sekolahId,
            'jenis_ujian_id' => $this->request->getPost('jenis_ujian_id'),
            'nama_ujian' => $this->request->getPost('nama_ujian'),
            'kode_ujian' => $this->request->getPost('kode_ujian'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'tipe_ujian' => $tipeUjian,
            'tampilkan_pembahasan' => $this->request->getPost('tampilkan_pembahasan') ? 1 : 0,
            'visibilitas' => $this->request->getPost('visibilitas') ?: 'terbuka',
            'pengulangan_aktif' => $this->request->getPost('pengulangan_aktif') ? 1 : 0,
            'maksimal_attempt' => $this->request->getPost('maksimal_attempt') ?: 1,
            'acak_urutan_soal' => $this->request->getPost('acak_urutan_soal') ? 1 : 0,
            'acak_pilihan_jawaban' => $this->request->getPost('acak_pilihan_jawaban') ? 1 : 0,
            'durasi' => $this->request->getPost('durasi'),
            'kelas_id' => $kelasId,
            'created_by' => $userId
        ];

        try {
            $ujianId = $this->ujianModel->insert($data, true);
            if ($tipeUjian === 'CAT') {
                $this->ujianCatParamModel->saveParam((int) $ujianId, [
                    'se_awal'              => $this->request->getPost('se_awal'),
                    'se_minimum'           => $this->request->getPost('se_minimum'),
                    'delta_se_minimum'     => $this->request->getPost('delta_se_minimum'),
                    'maksimal_soal_tampil' => $this->request->getPost('maksimal_soal_tampil') ?: 20,
                ]);
            }
            if ($isAjax) {
                return $this->response->setJSON(['success' => true, 'redirect' => base_url('admin/ujian/')]);
            }
            return redirect()->to('admin/ujian/')->with('success', 'Ujian berhasil ditambahkan');
        } catch (\Exception $e) {
            $msg = 'Gagal menambahkan ujian: ' . $e->getMessage();
            if ($isAjax) return $this->response->setJSON(['success' => false, 'errors' => ['general' => $msg]]);
            return redirect()->to('admin/ujian/')->with('error', $msg);
        }
    }

    // Proses update ujian — kode_ujian unique diabaikan untuk ID ujian saat ini, validasi kelas-sekolah tetap dijalankan
    public function editUjian($id)
    {
        // 1. Cek apakah ujian yang akan diedit memang ada
        $ujian = $this->ujianModel->find($id);
        if (!$ujian) {
            return redirect()->to('admin/ujian/')->with('error', 'Ujian tidak ditemukan.');
        }

        // 2. Validasi input form
        // Aturan 'is_unique' untuk kode_ujian harus mengabaikan ID ujian saat ini
        $rules = [
            'sekolah_id' => 'required|numeric',
            'jenis_ujian_id' => 'required|numeric',
            'nama_ujian' => 'required|min_length[3]|max_length[255]',
            'kode_ujian' => "required|alpha_numeric_punct|min_length[3]|max_length[50]|is_unique[ujian.kode_ujian,id_ujian,{$id}]",
            'deskripsi' => 'required|min_length[10]',
            'tipe_ujian' => 'required|in_list[CAT,CBT]',
            'tampilkan_pembahasan' => 'permit_empty',
            'visibilitas' => 'permit_empty',
            'pengulangan_aktif' => 'permit_empty',
            'maksimal_attempt' => 'permit_empty|numeric|less_than_equal_to[3]',
            'acak_urutan_soal' => 'permit_empty',
            'acak_pilihan_jawaban' => 'permit_empty',
            'maksimal_soal_tampil' => 'permit_empty|numeric',
            'se_awal' => 'permit_empty|decimal',
            'se_minimum' => 'permit_empty|decimal',
            'delta_se_minimum' => 'permit_empty|decimal',
            'durasi' => 'required|regex_match[/^([0-1][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/]',
            'kelas_id' => 'permit_empty|numeric'
        ];

        $tipeUjian = $this->request->getPost('tipe_ujian') ?: 'CAT';
        if ($tipeUjian === 'CAT') {
            $rules['se_awal'] = 'required|decimal';
            $rules['se_minimum'] = 'required|decimal';
            $rules['delta_se_minimum'] = 'required|decimal';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $sekolahId = $this->normalizeNullableId($this->request->getPost('sekolah_id'));
        $kelasId = $this->normalizeNullableId($this->request->getPost('kelas_id'));

        if ($sekolahId === null && $kelasId !== null) {
            return redirect()->back()->withInput()->with('error', 'Ujian sekolah umum tidak boleh langsung dibatasi ke kelas tertentu.');
        }

        if ($sekolahId !== null && !$this->sekolahModel->find($sekolahId)) {
            return redirect()->back()->withInput()->with('error', 'Sekolah yang dipilih tidak ditemukan.');
        }

        if ($kelasId !== null) {
            $kelas = $this->kelasModel
                ->where('kelas_id', $kelasId)
                ->where('sekolah_id', $sekolahId)
                ->first();

            if (!$kelas) {
                return redirect()->back()->withInput()->with('error', 'Kelas tidak valid untuk sekolah yang dipilih.');
            }
        }

        // 4. Siapkan data baru untuk diupdate
        $data = [
            'sekolah_id' => $sekolahId,
            'jenis_ujian_id' => $this->request->getPost('jenis_ujian_id'),
            'nama_ujian' => $this->request->getPost('nama_ujian'),
            'kode_ujian' => $this->request->getPost('kode_ujian'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'tipe_ujian' => $tipeUjian,
            'tampilkan_pembahasan' => $this->request->getPost('tampilkan_pembahasan') ? 1 : 0,
            'visibilitas' => $this->request->getPost('visibilitas') ?: 'terbuka',
            'pengulangan_aktif' => $this->request->getPost('pengulangan_aktif') ? 1 : 0,
            'maksimal_attempt' => $this->request->getPost('maksimal_attempt') ?: 1,
            'acak_urutan_soal' => $this->request->getPost('acak_urutan_soal') ? 1 : 0,
            'acak_pilihan_jawaban' => $this->request->getPost('acak_pilihan_jawaban') ? 1 : 0,
            'durasi' => $this->request->getPost('durasi'),
            'kelas_id' => $kelasId
        ];

        // 5. Lakukan update dan berikan notifikasi
        try {
            $this->ujianModel->update($id, $data);
            if ($tipeUjian === 'CAT') {
                $this->ujianCatParamModel->saveParam((int) $id, [
                    'se_awal'              => $this->request->getPost('se_awal'),
                    'se_minimum'           => $this->request->getPost('se_minimum'),
                    'delta_se_minimum'     => $this->request->getPost('delta_se_minimum'),
                    'maksimal_soal_tampil' => $this->request->getPost('maksimal_soal_tampil') ?: 20,
                ]);
            }
            return redirect()->to('admin/ujian/')->with('success', 'Ujian berhasil diperbarui.');
        } catch (\Exception $e) {
            log_message('error', 'Admin gagal mengupdate ujian: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui data ujian.');
        }
    }

    // Hapus ujian — tidak bisa dihapus jika masih ada soal terkait (CAT cek di ujian_soal_cat, CBT di soal_ujian)
    public function hapusUjian($id)
    {
        // 1. Admin tidak memerlukan validasi hak akses untuk menghapus ujian.
        // Blok validasi ->hasAccess() dihilangkan.

        // 2. Tetap lakukan pengecekan penting: Apakah ada soal yang terkait dengan ujian ini?
        // Ini untuk menjaga integritas data agar tidak ada soal yang "yatim".
        $ujian = $this->ujianModel->find($id);
        $soalTerkait = (($ujian['tipe_ujian'] ?? 'CAT') === 'CAT')
            ? $this->ujianSoalCatModel->countSoalByUjian($id)
            : $this->soalUjianModel->where('ujian_id', $id)->countAllResults();

        if ($soalTerkait > 0) {
            return redirect()->to('admin/ujian/')
                ->with('error', 'Gagal! Tidak dapat menghapus ujian ini karena masih ada ' . $soalTerkait . ' soal yang terkait. Harap hapus atau pindahkan soal-soal tersebut terlebih dahulu.');
        }

        // 3. Lakukan proses hapus jika tidak ada soal terkait
        try {
            $this->ujianModel->delete($id);
            return redirect()->to('admin/ujian/')
                ->with('success', 'Ujian berhasil dihapus secara permanen.');
        } catch (\Exception $e) {
            log_message('error', 'Admin gagal menghapus ujian: ' . $e->getMessage());
            return redirect()->to('admin/ujian/')
                ->with('error', 'Terjadi kesalahan saat menghapus ujian.');
        }
    }

    // ===== KELOLA SOAL =====

    // Halaman kelola soal — CAT pakai ujian_soal_cat, CBT pakai soal_ujian langsung; juga tampilkan panel Bank & Paket untuk CBT
    public function kelolaSoal($ujian_id)
    {
        // 1. Admin tidak memerlukan validasi hak akses (`hasAccess`).

        // 2. Ambil data ujian berdasarkan ID
        $data['ujian'] = $this->ujianModel->find($ujian_id);
        if (!$data['ujian']) {
            // Jika ujian tidak ditemukan, kembalikan ke daftar ujian admin
            return redirect()->to('admin/ujian/')
                ->with('error', 'Ujian tidak ditemukan.');
        }

        // 3. Ambil semua soal yang terkait dengan ujian ini
        $data['soal'] = (($data['ujian']['tipe_ujian'] ?? 'CAT') === 'CAT')
            ? $this->ujianSoalCatModel->getSoalByUjian($ujian_id)
            : $this->soalUjianModel->where('ujian_id', $ujian_id)->findAll();

        // 4. Ambil data metadata untuk dropdown
        $data['variabel']  = $this->variabelModel->orderBy('nama_variabel', 'ASC')->findAll();
        $data['indikator'] = $this->indikatorModel->orderBy('nama_indikator', 'ASC')->findAll();
        $data['materi']    = $this->materiModel->orderBy('nama_materi', 'ASC')->findAll();
        $data['sekolah']   = $this->sekolahModel->orderBy('nama_sekolah', 'ASC')->findAll();

        // 5. Ambil data bank & paket (untuk tab Bank & Paket)
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
            ->join('paket_ujian_cbt pu', 'pu.paket_id = au.paket_id')
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

        // 6. Arahkan ke view manajemen soal
        return view('admin/ujian/kelola_soal', $data);
    }


    // Tambah soal baru — untuk CAT: soal disimpan tanpa ujian_id lalu ditautkan via ujian_soal_cat; gambar unused di-cleanup dari session
    public function tambahSoal()
    {
        // 1. Validasi form input (aturan sama seperti Guru)
        $rules = [
            'ujian_id' => 'required|numeric',
            'pertanyaan' => 'required',
            'kode_soal' => 'required|alpha_numeric_punct|min_length[3]|max_length[50]|is_unique[soal_ujian.kode_soal]',
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

        // 2. Siapkan data dari form
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
            'created_by' => session()->get('user_id') // Creator adalah Admin yg login
        ];

        // 3. Proses upload media jika ada
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

        // 4. Simpan ke database
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

                return redirect()->to('admin/soal/' . $ujianId)->with('success', 'Soal berhasil ditambahkan');
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

    // Update soal — gambar lama yang tidak dipakai lagi dihapus, tapi hanya jika tidak dipakai di soal lain
    public function editSoal($id)
    {
        // 1. Ambil data soal yang akan diedit
        $soal = $this->soalUjianModel->find($id);
        if (!$soal) {
            return redirect()->back()->with('error', 'Soal tidak ditemukan.');
        }

        // Backup: Extract gambar yang sedang digunakan sebelum edit
        $oldHtmlContent = $soal['pertanyaan'] . ' ' . $soal['pilihan_a'] . ' ' .
            $soal['pilihan_b'] . ' ' . $soal['pilihan_c'] . ' ' .
            $soal['pilihan_d'] . ' ' . ($soal['pilihan_e'] ?? '') . ' ' .
            ($soal['pembahasan'] ?? '');
        $oldUsedImages = $this->extractImageFilenames($oldHtmlContent);

        // 2. Validasi form input
        $rules = [
            'kode_soal' => "required|alpha_numeric_punct|min_length[3]|max_length[50]|is_unique[soal_ujian.kode_soal,soal_id,{$id}]",
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
            'pembahasan' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            $this->cleanupTempImages();
            $errors = $this->validator->getErrors();
            $errorMessage = 'Validasi gagal: ' . implode(', ', $errors);
            return redirect()->back()->withInput()->with('error', $errorMessage);
        }

        // 3. Siapkan data dari form
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

        // 4. Proses upload/hapus media
        $uploadPath = FCPATH . 'uploads/soal';
        $mediaFile = $this->request->getFile('media');
        $oldMediaField = $soal['media'] ?? $soal['foto'] ?? null;
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

        // 5. Update ke database
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
            return redirect()->to('admin/soal/' . $ujian_id)->with('success', 'Soal berhasil diupdate');
        } catch (\Exception $e) {
            $this->cleanupTempImages();
            log_message('error', 'Error saat mengupdate soal: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui soal: ' . $e->getMessage());
        }
    }

    // Hapus soal — tidak bisa dihapus jika sudah ada di riwayat jawaban siswa; juga hapus gambar editor yang tidak dipakai soal lain
    public function hapusSoal($id, $ujian_id)
    {
        // Cek apakah soal sudah dijawab siswa
        $isAnswered = $this->hasilUjianModel->where('soal_id', $id)->countAllResults() > 0;

        if ($isAnswered) {
            return redirect()->to('admin/soal/' . $ujian_id)
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
                return redirect()->to('admin/soal/' . $ujian_id)->with('success', 'Soal berhasil dihapus.');
            } else {
                return redirect()->to('admin/soal/' . $ujian_id)->with('error', 'Soal yang akan dihapus tidak ditemukan.');
            }
        } catch (\Exception $e) {
            log_message('error', 'Guru gagal menghapus soal: ' . $e->getMessage());
            return redirect()->to('admin/soal/' . $ujian_id)->with('error', 'Terjadi kesalahan saat menghapus soal.');
        }
    }



    // ===== KELOLA JADWAL UJIAN =====

    // Halaman jadwal ujian — tampilkan semua jadwal tanpa filter, siapkan data dropdown untuk modal tambah dan edit
    public function jadwalUjian()
    {
        // Ambil SEMUA jadwal ujian dari database dengan join ke info terkait
        $data['jadwal'] = $this->db->table('jadwal_ujian')
            ->select('jadwal_ujian.*, ujian.nama_ujian, ujian.kode_ujian, ujian.tipe_ujian, kelas.nama_kelas, guru.nama_lengkap, sekolah.sekolah_id, sekolah.nama_sekolah')
            ->join('ujian', 'ujian.id_ujian = jadwal_ujian.ujian_id')
            ->join('kelas', 'kelas.kelas_id = jadwal_ujian.kelas_id', 'left')
            ->join('sekolah', 'sekolah.sekolah_id = kelas.sekolah_id', 'left')
            ->join('guru', 'guru.guru_id = jadwal_ujian.guru_id') // guru_id di sini adalah guru pengawas
            ->orderBy('jadwal_ujian.tanggal_mulai', 'DESC')
            ->get()->getResultArray();

        // Ambil SEMUA ujian untuk dropdown di modal tambah & edit
        $data['ujian_tambah'] = $this->ujianModel
            ->select('ujian.id_ujian, ujian.nama_ujian, ujian.kode_ujian, ujian.tipe_ujian, ujian.sekolah_id, ujian.kelas_id, sekolah.nama_sekolah, kelas.nama_kelas')
            ->join('kelas', 'kelas.kelas_id = ujian.kelas_id', 'left')
            ->join('sekolah', 'sekolah.sekolah_id = COALESCE(kelas.sekolah_id, ujian.sekolah_id)', 'left', false)
            ->orderBy('ujian.nama_ujian', 'ASC')
            ->findAll();
        $data['ujian_edit'] = $data['ujian_tambah']; // Data yang sama untuk edit

        // Ambil SEMUA kelas untuk dropdown di modal dengan info sekolah
        $data['kelas'] = $this->kelasModel
            ->select('kelas.*, sekolah.nama_sekolah')
            ->join('sekolah', 'sekolah.sekolah_id = kelas.sekolah_id')
            ->orderBy('sekolah.nama_sekolah, kelas.nama_kelas', 'ASC')
            ->findAll();

        // Ambil SEMUA guru untuk dropdown pengawas
        $data['guru'] = $this->guruModel->select('guru_id, nama_lengkap, nip, mata_pelajaran')->orderBy('nama_lengkap', 'ASC')->findAll();

        // Ambil SEMUA siswa untuk multi-select penugasan individu
        $data['siswa'] = $this->siswaModel
            ->select('siswa.siswa_id, siswa.nama_lengkap, siswa.nomor_peserta, siswa.kelas_id, kelas.nama_kelas, sekolah.nama_sekolah')
            ->join('kelas', 'kelas.kelas_id = siswa.kelas_id', 'left')
            ->join('sekolah', 'sekolah.sekolah_id = kelas.sekolah_id', 'left')
            ->orderBy('siswa.nama_lengkap', 'ASC')->findAll();

        // Data sekolah untuk cascade dropdown
        $data['sekolah'] = $this->sekolahModel->orderBy('nama_sekolah', 'ASC')->findAll();

        return view('admin/jadwal/jadwal_ujian', $data);
    }

    // Proses tambah jadwal — validasi kelas harus sesuai ujian, siswa_ids di-encode JSON untuk penugasan individu
    public function tambahJadwal()
    {
        $rules = [
            'ujian_id' => 'required|numeric',
            'kelas_id' => 'permit_empty|numeric',
            'guru_id' => 'required|numeric',
            'tanggal_mulai' => 'required',
            'tanggal_selesai' => 'required',
            'kode_akses' => 'required|min_length[4]|max_length[50]',
            'tipe_penugasan' => 'required|in_list[kelas,individu]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $tglMulai   = $this->request->getPost('tanggal_mulai');
        $tglSelesai = $this->request->getPost('tanggal_selesai');
        if (strtotime($tglMulai) >= strtotime($tglSelesai)) {
            return redirect()->back()->withInput()
                ->with('error', 'Tanggal mulai harus lebih awal dari tanggal selesai.');
        }

        $tipePenugasan = $this->request->getPost('tipe_penugasan') ?: 'kelas';
        $siswaIds = $this->request->getPost('siswa_ids') ?? [];
        $kelasId = $this->normalizeNullableId($this->request->getPost('kelas_id'));
        $ujianId = (int) $this->request->getPost('ujian_id');

        if (!$this->validateJadwalKelasAgainstUjian($ujianId, $kelasId)) {
            return redirect()->back()->withInput()->with('error', 'Kelas jadwal tidak sesuai dengan pengaturan sekolah/kelas pada ujian.');
        }

        if ($tipePenugasan === 'individu') {
            $siswaIds = array_values(array_unique(array_map('intval', (array) $siswaIds)));
            if (empty($siswaIds)) {
                return redirect()->back()->withInput()->with('error', 'Pilih minimal satu siswa untuk penugasan individu.');
            }

            $siswaValid = empty($kelasId)
                ? $this->validateSiswaIds($siswaIds)
                : $this->validateSiswaIdsForKelas((int) $kelasId, $siswaIds);

            if (!$siswaValid) {
                return redirect()->back()->withInput()->with('error', 'Daftar siswa tidak valid. Hanya siswa dari kelas yang dipilih yang boleh ditugaskan.');
            }
        } else {
            $siswaIds = [];
        }

        $data = [
            'ujian_id' => $ujianId,
            'kelas_id' => $kelasId,
            'guru_id' => $this->request->getPost('guru_id'),
            'tanggal_mulai' => $this->request->getPost('tanggal_mulai'),
            'tanggal_selesai' => $this->request->getPost('tanggal_selesai'),
            'kode_akses' => $this->request->getPost('kode_akses'),
            'tipe_penugasan' => $tipePenugasan,
            'siswa_ids' => $tipePenugasan === 'individu' && !empty($siswaIds) ? json_encode($siswaIds) : null,
            'status' => 'belum_mulai'
        ];

        try {
            $this->jadwalUjianModel->insert($data);
            return redirect()->to('admin/jadwal-ujian')->with('success', 'Jadwal ujian berhasil ditambahkan.');
        } catch (\Exception $e) {
            log_message('error', 'Admin gagal tambah jadwal: ' . $e->getMessage());
            return redirect()->to('admin/jadwal-ujian')->with('error', 'Gagal menambahkan jadwal ujian.');
        }
    }

    // Proses update jadwal — cek duplikat kombinasi ujian+kelas di jadwal lain, validasi status tidak bisa kembali ke sedang_berlangsung jika waktu sudah lewat
    public function editJadwal($id)
    {
        $rules = [
            'ujian_id' => 'required|numeric',
            'kelas_id' => 'permit_empty|numeric',
            'guru_id' => 'required|numeric',
            'tanggal_mulai' => 'required',
            'tanggal_selesai' => 'required',
            'kode_akses' => 'required|min_length[4]|max_length[50]',
            'tipe_penugasan' => 'required|in_list[kelas,individu]',
            'status' => 'required|in_list[belum_mulai,sedang_berlangsung,selesai]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $postedKelasId = $this->normalizeNullableId($this->request->getPost('kelas_id'));
        $existingBuilder = $this->jadwalUjianModel
            ->where('ujian_id', $this->request->getPost('ujian_id'))
            ->where('jadwal_id !=', $id);
        if ($postedKelasId === null) {
            $existingBuilder->where('kelas_id IS NULL', null, false);
        } else {
            $existingBuilder->where('kelas_id', $postedKelasId);
        }
        $existing = $existingBuilder->first();

        if ($existing) {
            return redirect()->to('admin/jadwal-ujian')
                ->with('error', 'Kombinasi ujian dan kelas ini sudah digunakan oleh jadwal lain.');
        }

        $tipePenugasan = $this->request->getPost('tipe_penugasan') ?: 'kelas';
        $siswaIds = $this->request->getPost('siswa_ids') ?? [];
        $kelasId = $postedKelasId;
        $ujianId = (int) $this->request->getPost('ujian_id');
        $tanggalSelesai = $this->request->getPost('tanggal_selesai');
        $status = $this->request->getPost('status');

        if ($status === 'sedang_berlangsung' && strtotime($tanggalSelesai) < time()) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Tidak bisa mengubah status menjadi sedang berlangsung karena waktu selesai ujian sudah terlewat.');
        }

        if (!$this->validateJadwalKelasAgainstUjian($ujianId, $kelasId)) {
            return redirect()->back()->withInput()->with('error', 'Kelas jadwal tidak sesuai dengan pengaturan sekolah/kelas pada ujian.');
        }

        if ($tipePenugasan === 'individu') {
            $siswaIds = array_values(array_unique(array_map('intval', (array) $siswaIds)));
            if (empty($siswaIds)) {
                return redirect()->back()->withInput()->with('error', 'Pilih minimal satu siswa untuk penugasan individu.');
            }

            $siswaValid = empty($kelasId)
                ? $this->validateSiswaIds($siswaIds)
                : $this->validateSiswaIdsForKelas((int) $kelasId, $siswaIds);

            if (!$siswaValid) {
                return redirect()->back()->withInput()->with('error', 'Daftar siswa tidak valid. Hanya siswa dari kelas yang dipilih yang boleh ditugaskan.');
            }
        } else {
            $siswaIds = [];
        }

        $data = [
            'ujian_id' => $ujianId,
            'kelas_id' => $kelasId,
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
            return redirect()->to('admin/jadwal-ujian')->with('success', 'Jadwal ujian berhasil diperbarui.');
        } catch (\Exception $e) {
            log_message('error', 'Admin gagal update jadwal: ' . $e->getMessage());
            return redirect()->to('admin/jadwal-ujian')->with('error', 'Gagal memperbarui jadwal ujian.');
        }
    }

    // Hapus jadwal — tidak bisa dihapus jika sudah ada peserta terdaftar
    public function hapusJadwal($id)
    {
        $pesertaTerkait = $this->pesertaUjianModel->where('jadwal_id', $id)->countAllResults();

        if ($pesertaTerkait > 0) {
            return redirect()->to('admin/jadwal-ujian')
                ->with('error', 'Gagal! Jadwal ini tidak dapat dihapus karena sudah memiliki ' . $pesertaTerkait . ' peserta terdaftar.');
        }

        try {
            $this->jadwalUjianModel->delete($id);
            return redirect()->to('admin/jadwal-ujian')->with('success', 'Jadwal ujian berhasil dihapus.');
        } catch (\Exception $e) {
            log_message('error', 'Admin gagal hapus jadwal: ' . $e->getMessage());
            return redirect()->to('admin/jadwal-ujian')->with('error', 'Terjadi kesalahan saat menghapus jadwal ujian.');
        }
    }

    // ===== KELOLA HASIL UJIAN =====

    // Hitung durasi pengerjaan setiap soal berdasarkan selisih waktu_menjawab antar soal secara berurutan
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

    // Konversi nilai theta IRT ke skor 0-100 — rumus: 50 + (16.67 * theta), dibulatkan dan di-clamp ke [0, 100]
    private function hitungKemampuanKognitif($theta)
    {
        // Rumus skor akhir siswa (x) = 50 + (16.67 * tetha)
        $skor_akhir = 50 + (16.67 * (float)$theta);

        $skor_akhir = max(0, min(100, $skor_akhir));

        return round($skor_akhir, 2);
    }

    // Konversi skor numerik ke label dan class CSS untuk tampilan — 5 level dari Sangat Rendah sampai Sangat Baik
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

    // Ambil attempt terakhir (nomor tertinggi) dari peserta tertentu
    private function getLatestAttemptForPeserta(int $pesertaUjianId): ?array
    {
        return $this->db->table('attempt_ujian')
            ->where('peserta_ujian_id', $pesertaUjianId)
            ->orderBy('nomor_attempt', 'DESC')
            ->get()
            ->getRowArray();
    }

    // Ambil attempt spesifik berdasarkan attempt_id dan peserta_ujian_id (untuk navigasi antar percobaan)
    private function getAttemptByIdForPeserta(int $pesertaUjianId, int $attemptId): ?array
    {
        return $this->db->table('attempt_ujian')
            ->where('peserta_ujian_id', $pesertaUjianId)
            ->where('attempt_id', $attemptId)
            ->get()
            ->getRowArray();
    }

    // Ambil semua attempt peserta urut dari nomor terkecil — dipakai untuk halaman daftar percobaan
    private function getAttemptsForPeserta(int $pesertaUjianId): array
    {
        return $this->db->table('attempt_ujian')
            ->where('peserta_ujian_id', $pesertaUjianId)
            ->orderBy('nomor_attempt', 'ASC')
            ->get()
            ->getResultArray();
    }

    // Bangun ringkasan hasil satu attempt — CAT ambil theta dari jawaban terakhir, CBT ambil nilai_akhir dari tabel attempt
    private function buildResultSummary(array $context, array $detailJawaban, ?array $attempt = null): array
    {
        $isCatMode = ($context['tipe_ujian'] ?? 'CAT') === 'CAT';
        $lastResult = !empty($detailJawaban) ? end($detailJawaban) : null;
        // Theta dan SE diambil dari baris terakhir jawaban CAT — ini adalah estimasi kemampuan akhir
        if ($isCatMode) {
            $thetaAkhir = $lastResult ? (float) ($lastResult['theta_saat_ini'] ?? 0) : 0.0;
            $seAkhir = $lastResult && isset($lastResult['se_saat_ini']) ? (float) $lastResult['se_saat_ini'] : null;
            // CAT: konversi theta ke skor 0-100 menggunakan rumus IRT — lihat hitungKemampuanKognitif()
            $skorAkhir = $this->hitungKemampuanKognitif($thetaAkhir);
            $nilaiAkhir = min(100, max(0, round($skorAkhir)));
        } else {
            $thetaAkhir = isset($attempt['theta_akhir']) ? (float) $attempt['theta_akhir'] : null;
            $seAkhir = isset($attempt['sem_akhir']) ? (float) $attempt['sem_akhir'] : null;
            // CBT: nilai sudah dihitung oleh sistem saat ujian berlangsung, langsung ambil dari attempt
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


    // Daftar semua jadwal ujian beserta statistik peserta (selesai, sedang, belum) dan durasi min/maks/rata-rata
    public function daftarHasilUjian()
    {
        $db = \Config\Database::connect();

        // Query untuk mengambil daftar ujian SEMUA STATUS dengan hasil dan informasi waktu
        $data['daftarUjian'] = $db->table('jadwal_ujian ju')
            ->select('ju.jadwal_id, ju.status as status_ujian, ju.tanggal_mulai, ju.tanggal_selesai, ju.kode_akses,
             u.nama_ujian, u.deskripsi, u.tipe_ujian, j.nama_jenis, k.nama_kelas, k.tahun_ajaran, 
             s.nama_sekolah, g.nama_lengkap as nama_guru,
             COUNT(DISTINCT pu.peserta_ujian_id) as jumlah_peserta,
             COUNT(DISTINCT CASE WHEN pu.status = "selesai" THEN pu.peserta_ujian_id END) as peserta_selesai,
             COUNT(DISTINCT CASE WHEN pu.status = "sedang_mengerjakan" THEN pu.peserta_ujian_id END) as peserta_sedang_mengerjakan,
             COUNT(DISTINCT CASE WHEN pu.status = "belum_mulai" THEN pu.peserta_ujian_id END) as peserta_belum_mulai,
             AVG(CASE WHEN pu.status = "selesai" THEN TIME_TO_SEC(TIMEDIFF(pu.waktu_selesai, pu.waktu_mulai)) END) as rata_rata_durasi_detik,
             MIN(CASE WHEN pu.status = "selesai" THEN TIME_TO_SEC(TIMEDIFF(pu.waktu_selesai, pu.waktu_mulai)) END) as durasi_tercepat_detik,
             MAX(CASE WHEN pu.status = "selesai" THEN TIME_TO_SEC(TIMEDIFF(pu.waktu_selesai, pu.waktu_mulai)) END) as durasi_terlama_detik,
             DATE_FORMAT(ju.tanggal_mulai, "%d/%m/%Y %H:%i") as tanggal_mulai_format,
             DATE_FORMAT(ju.tanggal_selesai, "%d/%m/%Y %H:%i") as tanggal_selesai_format')
            ->join('ujian u', 'u.id_ujian = ju.ujian_id', 'left')
            ->join('jenis_ujian j', 'j.jenis_ujian_id = u.jenis_ujian_id', 'left')
            ->join('kelas k', 'k.kelas_id = ju.kelas_id', 'left')
            ->join('sekolah s', 's.sekolah_id = k.sekolah_id', 'left')
            ->join('guru g', 'g.guru_id = ju.guru_id', 'left')
            ->join('peserta_ujian pu', 'pu.jadwal_id = ju.jadwal_id', 'left')
            ->groupBy('ju.jadwal_id, ju.status, ju.tanggal_mulai, ju.tanggal_selesai, ju.kode_akses, u.nama_ujian, u.deskripsi, u.tipe_ujian, j.nama_jenis, k.nama_kelas, k.tahun_ajaran, s.nama_sekolah, g.nama_lengkap')
            ->orderBy('ju.tanggal_mulai', 'DESC')
            ->get()
            ->getResultArray();

        // Format durasi untuk setiap ujian
        foreach ($data['daftarUjian'] as &$ujian) {
            // Format rata-rata durasi
            if ($ujian['rata_rata_durasi_detik']) {
                $jam = floor($ujian['rata_rata_durasi_detik'] / 3600);
                $menit = floor(($ujian['rata_rata_durasi_detik'] % 3600) / 60);
                $detik = $ujian['rata_rata_durasi_detik'] % 60;
                $ujian['rata_rata_durasi_format'] = sprintf('%02d:%02d:%02d', $jam, $menit, $detik);
            } else {
                $ujian['rata_rata_durasi_format'] = '-';
            }

            // Format durasi tercepat
            if ($ujian['durasi_tercepat_detik']) {
                $jam = floor($ujian['durasi_tercepat_detik'] / 3600);
                $menit = floor(($ujian['durasi_tercepat_detik'] % 3600) / 60);
                $detik = $ujian['durasi_tercepat_detik'] % 60;
                $ujian['durasi_tercepat_format'] = sprintf('%02d:%02d:%02d', $jam, $menit, $detik);
            } else {
                $ujian['durasi_tercepat_format'] = '-';
            }

            // Format durasi terlama
            if ($ujian['durasi_terlama_detik']) {
                $jam = floor($ujian['durasi_terlama_detik'] / 3600);
                $menit = floor(($ujian['durasi_terlama_detik'] % 3600) / 60);
                $detik = $ujian['durasi_terlama_detik'] % 60;
                $ujian['durasi_terlama_format'] = sprintf('%02d:%02d:%02d', $jam, $menit, $detik);
            } else {
                $ujian['durasi_terlama_format'] = '-';
            }

            // Tambahkan informasi status untuk styling
            $ujian['status_class'] = $this->getStatusClass($ujian['status_ujian']);
            $ujian['status_text'] = $this->getStatusText($ujian['status_ujian']);
        }

        return view('admin/hasil/daftar', $data);
    }

    // Halaman analitik hasil ujian — filter multi-dimensi (sekolah, kelas, tipe, ujian, percobaan, variabel, indikator, materi)
    public function analitikHasilUjian()
    {
        $filters = $this->getAnalitikFiltersFromRequest();
        $biodataFilters = $this->getBiodataFiltersFromRequest();
        $filters['biodata'] = $biodataFilters;

        $pesertaRows = $this->getAnalitikPesertaRows($filters);
        $overallStats = $this->getAnalitikOverallJawaban($filters);
        $studentRows = $this->getAnalitikStudentRows($filters);

        $formTemplateModel = new \App\Models\FormTemplateModel();
        $formFieldModel    = new \App\Models\FormFieldModel();
        $template    = $formTemplateModel->getSingle();
        $allFields   = $formFieldModel->getWithOptions((int)($template['template_id'] ?? 0));
        $selectFields = array_values(array_filter($allFields, fn($f) => $f['tipe'] === 'select'));

        $data = [
            'pageRole' => 'admin',
            'basePath' => 'admin/hasil-ujian',
            'filters' => $filters,
            'biodataFilters' => $biodataFilters,
            'selectFields' => $selectFields,
            'filterOptions' => [
                'sekolah' => $this->sekolahModel->orderBy('nama_sekolah', 'ASC')->findAll(),
                'kelas' => $this->db->table('kelas k')
                    ->select('k.kelas_id, k.sekolah_id, k.nama_kelas, k.tahun_ajaran, s.nama_sekolah')
                    ->join('sekolah s', 's.sekolah_id = k.sekolah_id', 'left')
                    ->orderBy('s.nama_sekolah', 'ASC')
                    ->orderBy('k.nama_kelas', 'ASC')
                    ->get()
                    ->getResultArray(),
                'jenis_ujian' => [
                    ['value' => 'CAT', 'label' => 'CAT'],
                    ['value' => 'CBT', 'label' => 'CBT'],
                ],
                'ujian' => $this->db->table('jadwal_ujian ju')
                    ->select('ju.jadwal_id, ju.kelas_id, u.tipe_ujian, u.nama_ujian, u.kode_ujian, k.sekolah_id, k.nama_kelas, ju.tanggal_mulai')
                    ->join('ujian u', 'u.id_ujian = ju.ujian_id')
                    ->join('kelas k', 'k.kelas_id = ju.kelas_id', 'left')
                    ->orderBy('ju.tanggal_mulai', 'DESC')
                    ->get()
                    ->getResultArray(),
                'percobaan' => $this->db->table('attempt_ujian au')
                    ->distinct()
                    ->select('au.nomor_attempt')
                    ->join('peserta_ujian pu', 'pu.peserta_ujian_id = au.peserta_ujian_id')
                    ->join('jadwal_ujian ju', 'ju.jadwal_id = pu.jadwal_id')
                    ->join('ujian u', 'u.id_ujian = ju.ujian_id')
                    ->where('au.status', 'selesai')
                    ->where('u.tipe_ujian', 'CBT')
                    ->orderBy('au.nomor_attempt', 'ASC')
                    ->get()
                    ->getResultArray(),
                'variabel' => $this->variabelModel->orderBy('nama_variabel', 'ASC')->findAll(),
                'indikator' => $this->indikatorModel
                    ->select('indikator.*, variabel.nama_variabel')
                    ->join('variabel', 'variabel.variabel_id = indikator.variabel_id', 'left')
                    ->orderBy('variabel.nama_variabel', 'ASC')
                    ->orderBy('indikator.nama_indikator', 'ASC')
                    ->findAll(),
                'materi' => $this->materiModel->orderBy('nama_materi', 'ASC')->findAll(),
            ],
            'summary' => [
                'total_peserta' => count($pesertaRows),
                'rata_rata_skor' => !empty($pesertaRows) ? array_sum(array_column($pesertaRows, 'skor_akhir')) / count($pesertaRows) : 0,
                'rata_rata_benar' => (float) ($overallStats['persentase_benar'] ?? 0),
                'rata_rata_durasi_detik' => (int) round($overallStats['rata_rata_durasi_detik'] ?? 0),
                'total_soal_muncul' => (int) ($overallStats['total_soal'] ?? 0),
            ],
            'durationBars' => $this->getAnalitikDurationBars($filters),
            'studentRows' => $studentRows,
        ];

        return view('admin/hasil/analitik', $data);
    }

    // Mapping status jadwal ke class Bootstrap badge
    private function getStatusClass($status)
    {
        switch ($status) {
            case 'belum_mulai':
                return 'secondary';
            case 'sedang_berlangsung':
                return 'warning';
            case 'selesai':
                return 'success';
            default:
                return 'secondary';
        }
    }

    // Mapping status jadwal ke teks label yang ditampilkan ke pengguna
    private function getStatusText($status)
    {
        switch ($status) {
            case 'belum_mulai':
                return 'Belum Mulai';
            case 'sedang_berlangsung':
                return 'Sedang Berlangsung';
            case 'selesai':
                return 'Selesai';
            default:
                return 'Tidak Diketahui';
        }
    }

    // Parse dan normalisasi semua parameter filter analitik dari GET request
    private function getAnalitikFiltersFromRequest(): array
    {
        return [
            'sekolah_id' => $this->normalizeNullableInt($this->request->getGet('sekolah_id')),
            'kelas_id' => $this->normalizeNullableInt($this->request->getGet('kelas_id')),
            'tipe_ujian' => $this->normalizeNullableExamType($this->request->getGet('tipe_ujian')),
            'jadwal_id' => $this->normalizeNullableInt($this->request->getGet('jadwal_id')),
            'nomor_attempt' => $this->normalizeNullableInt($this->request->getGet('nomor_attempt')),
            'variabel_id' => $this->normalizeNullableInt($this->request->getGet('variabel_id')),
            'indikator_id' => $this->normalizeNullableInt($this->request->getGet('indikator_id')),
            'materi_id' => $this->normalizeNullableInt($this->request->getGet('materi_id')),
            'jenis_kelamin' => $this->request->getGet('jenis_kelamin') ?: null,
        ];
    }

    // Konversi string/int ke int nullable — string kosong atau bukan angka jadi null
    private function normalizeNullableInt($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return is_numeric($value) ? (int) $value : null;
    }

    // Normalisasi tipe ujian dari GET — hanya 'CAT' dan 'CBT' yang valid, selain itu null
    private function normalizeNullableExamType($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = strtoupper((string) $value);

        return in_array($value, ['CAT', 'CBT'], true) ? $value : null;
    }

    // Pre-filter peserta berdasarkan jenis_kelamin dan biodata dinamis — kembalikan array siswa_id yang lolos, atau null jika filter tidak aktif
    private function getFilteredSiswaIds(array $filters): ?array
    {
        if (empty($filters['jenis_kelamin']) && empty($filters['biodata'])) {
            return null;
        }
        $builder = $this->db->table('siswa s')->select('s.siswa_id');
        if (!empty($filters['jenis_kelamin'])) {
            $builder->where('s.jenis_kelamin', $filters['jenis_kelamin']);
        }
        if (!empty($filters['biodata'])) {
            $builder->join('form_responses fr', 'fr.siswa_id = s.siswa_id', 'inner');
            foreach ($filters['biodata'] as $fieldId => $nilai) {
                $alias = 'frv_' . $fieldId;
                $builder->join("form_response_values {$alias}", "{$alias}.response_id = fr.response_id AND {$alias}.field_id = {$fieldId}", 'inner', false)
                        ->where("{$alias}.nilai", $nilai);
            }
        }
        return array_column($builder->get()->getResultArray(), 'siswa_id');
    }

    // Bangun subquery untuk join attempt — kalau filter nomor_attempt ada, ambil attempt spesifik; kalau tidak, ambil attempt terbaru per peserta
    private function getAnalitikAttemptSubquery(array $filters): string
    {
        if (!empty($filters['nomor_attempt'])) {
            return '(SELECT peserta_ujian_id, nomor_attempt AS max_attempt
                FROM attempt_ujian
                WHERE status = "selesai" AND nomor_attempt = ' . (int) $filters['nomor_attempt'] . ') la';
        }

        return '(SELECT peserta_ujian_id, MAX(nomor_attempt) AS max_attempt
            FROM attempt_ujian
            WHERE status = "selesai"
            GROUP BY peserta_ujian_id) la';
    }

    // Terapkan semua filter scope ke query builder — sekolah, kelas, tipe ujian, jadwal, variabel, indikator, materi
    private function applyAnalitikScopeFilters($builder, array $filters): void
    {
        if (!empty($filters['sekolah_id'])) {
            $builder->where('k.sekolah_id', $filters['sekolah_id']);
        }

        if (!empty($filters['kelas_id'])) {
            $builder->where('ju.kelas_id', $filters['kelas_id']);
        }

        if (!empty($filters['tipe_ujian'])) {
            $builder->where('u.tipe_ujian', $filters['tipe_ujian']);
        }

        if (!empty($filters['jadwal_id'])) {
            $builder->where('ju.jadwal_id', $filters['jadwal_id']);
        }

        if (!empty($filters['variabel_id'])) {
            $builder->where('sq.variabel_id', $filters['variabel_id']);
        }

        if (!empty($filters['indikator_id'])) {
            $builder->where('sq.indikator_id', $filters['indikator_id']);
        }

        if (!empty($filters['materi_id'])) {
            $builder->where('sq.materi_id', $filters['materi_id']);
        }
    }

    // Ambil baris peserta untuk analitik — join ke metadata soal hanya kalau filter variabel/indikator/materi aktif (efisiensi query)
    private function getAnalitikPesertaRows(array $filters): array
    {
        $builder = $this->db->table('peserta_ujian pu')
            ->select('pu.peserta_ujian_id, u.tipe_ujian, au.nilai_akhir, au.waktu_mulai, au.waktu_selesai')
            ->join('jadwal_ujian ju', 'ju.jadwal_id = pu.jadwal_id')
            ->join('ujian u', 'u.id_ujian = ju.ujian_id')
            ->join('kelas k', 'k.kelas_id = ju.kelas_id', 'left')
            ->join($this->getAnalitikAttemptSubquery($filters), 'la.peserta_ujian_id = pu.peserta_ujian_id', 'inner', false)
            ->join('attempt_ujian au', 'au.peserta_ujian_id = la.peserta_ujian_id AND au.nomor_attempt = la.max_attempt', 'inner', false)
            ->where('pu.status', 'selesai');

        // Join ke tabel soal hanya jika filter metadata soal aktif — menghindari join besar yang tidak perlu
        if (!empty($filters['variabel_id']) || !empty($filters['indikator_id']) || !empty($filters['materi_id'])) {
            $builder
                ->join('attempt_soal_cbt ats', 'ats.attempt_id = au.attempt_id', 'inner')
                ->join('soal_ujian sq', 'sq.soal_id = ats.original_soal_id', 'left');
        }

        $this->applyAnalitikScopeFilters($builder, $filters);

        $siswaIds = $this->getFilteredSiswaIds($filters);
        if ($siswaIds !== null) {
            if (empty($siswaIds)) return [];
            $builder->whereIn('pu.siswa_id', $siswaIds);
        }

        $rows = $builder
            ->groupBy('pu.peserta_ujian_id, u.tipe_ujian, au.nilai_akhir, au.waktu_mulai, au.waktu_selesai')
            ->get()
            ->getResultArray();

        foreach ($rows as &$row) {
            $rawScore = (float) ($row['nilai_akhir'] ?? 0);
            // CAT: nilai_akhir berisi theta, perlu dikonversi; CBT: nilai_akhir sudah berupa skor 0-100
            $row['skor_akhir'] = ($row['tipe_ujian'] ?? 'CAT') === 'CAT'
                ? $this->hitungKemampuanKognitif($rawScore)
                : round($rawScore, 2);
            $row['level_kemampuan'] = $this->getAnalitikLevelKemampuan($row['skor_akhir']);
        }
        unset($row);

        return $rows;
    }

    // Hitung jumlah peserta per level kemampuan (Mahir/Cakap/Layak/Berkembang) dari baris peserta yang sudah ada skornya
    private function buildAnalitikLevelSummary(array $pesertaRows): array
    {
        $summary = [
            'Mahir' => ['label' => 'Mahir', 'count' => 0, 'color' => '#198754'],
            'Cakap' => ['label' => 'Cakap', 'count' => 0, 'color' => '#0d6efd'],
            'Layak' => ['label' => 'Layak', 'count' => 0, 'color' => '#fd7e14'],
            'Berkembang' => ['label' => 'Berkembang', 'count' => 0, 'color' => '#dc3545'],
        ];

        foreach ($pesertaRows as $row) {
            $level = $row['level_kemampuan'] ?? 'Berkembang';
            if (isset($summary[$level])) {
                $summary[$level]['count']++;
            }
        }

        return array_values($summary);
    }

    // Konversi skor numerik ke label level kemampuan — 4 level: Berkembang < 42 < Layak < 58 < Cakap < 75 < Mahir
    private function getAnalitikLevelKemampuan(float $skor): string
    {
        if ($skor >= 75) {
            return 'Mahir';
        }

        if ($skor >= 58) {
            return 'Cakap';
        }

        if ($skor >= 42) {
            return 'Layak';
        }

        return 'Berkembang';
    }

    // Hitung statistik keseluruhan jawaban (total, benar, salah, tidak dijawab, rata-rata durasi) sesuai filter aktif
    private function getAnalitikOverallJawaban(array $filters): array
    {
        $builder = $this->db->table('peserta_ujian pu')
            ->select('
                COUNT(ats.attempt_soal_id) as total_soal,
                SUM(CASE WHEN aj.jawaban_id IS NOT NULL AND aj.is_correct = 1 THEN 1 ELSE 0 END) as total_benar,
                SUM(CASE WHEN aj.jawaban_id IS NOT NULL AND (aj.is_correct = 0 OR aj.is_correct IS NULL) THEN 1 ELSE 0 END) as total_salah,
                SUM(CASE WHEN aj.jawaban_id IS NULL THEN 1 ELSE 0 END) as total_tidak_dijawab,
                AVG(CASE WHEN au.waktu_mulai IS NOT NULL AND au.waktu_selesai IS NOT NULL THEN TIME_TO_SEC(TIMEDIFF(au.waktu_selesai, au.waktu_mulai)) END) as rata_rata_durasi_detik
            ', false)
            ->join('jadwal_ujian ju', 'ju.jadwal_id = pu.jadwal_id')
            ->join('ujian u', 'u.id_ujian = ju.ujian_id')
            ->join('kelas k', 'k.kelas_id = ju.kelas_id', 'left')
            ->join($this->getAnalitikAttemptSubquery($filters), 'la.peserta_ujian_id = pu.peserta_ujian_id', 'inner', false)
            ->join('attempt_ujian au', 'au.peserta_ujian_id = la.peserta_ujian_id AND au.nomor_attempt = la.max_attempt', 'inner', false)
            ->join('attempt_soal_cbt ats', 'ats.attempt_id = au.attempt_id', 'inner')
            ->join('soal_ujian sq', 'sq.soal_id = ats.original_soal_id', 'left')
            ->join('attempt_jawaban aj', 'aj.attempt_id = ats.attempt_id AND aj.soal_id = ats.original_soal_id', 'left', false)
            ->where('pu.status', 'selesai');

        $this->applyAnalitikScopeFilters($builder, $filters);

        $siswaIds = $this->getFilteredSiswaIds($filters);
        if ($siswaIds !== null) {
            if (empty($siswaIds)) return [];
            $builder->whereIn('pu.siswa_id', $siswaIds);
        }

        $row = $builder->get()->getRowArray() ?? [];
        $totalSoal = (int) ($row['total_soal'] ?? 0);
        $row['persentase_benar'] = $totalSoal > 0 ? round(((int) ($row['total_benar'] ?? 0) / $totalSoal) * 100, 2) : 0;

        return $row;
    }

    // Ambil statistik jawaban dikelompokkan berdasarkan variabel/indikator/materi — mode ditentukan parameter $mode
    private function getAnalitikMetadataRows(array $filters, string $mode): array
    {
        $selectMap = [
            'variabel' => '
                sq.variabel_id as group_id,
                COALESCE(v.nama_variabel, "Tanpa Variabel") as group_label,
                "" as parent_label
            ',
            'indikator' => '
                sq.indikator_id as group_id,
                COALESCE(i.nama_indikator, "Tanpa Indikator") as group_label,
                COALESCE(v.nama_variabel, "-") as parent_label
            ',
            'materi' => '
                sq.materi_id as group_id,
                COALESCE(m.nama_materi, "Tanpa Materi") as group_label,
                COALESCE(i.nama_indikator, "-") as parent_label
            ',
        ];

        $groupMap = [
            'variabel' => 'sq.variabel_id, v.nama_variabel',
            'indikator' => 'sq.indikator_id, i.nama_indikator, v.nama_variabel',
            'materi' => 'sq.materi_id, m.nama_materi, i.nama_indikator',
        ];

        if (!isset($selectMap[$mode])) {
            return [];
        }

        $builder = $this->db->table('peserta_ujian pu')
            ->select($selectMap[$mode] . ',
                COUNT(ats.attempt_soal_id) as total_soal,
                SUM(CASE WHEN aj.jawaban_id IS NOT NULL AND aj.is_correct = 1 THEN 1 ELSE 0 END) as total_benar,
                SUM(CASE WHEN aj.jawaban_id IS NOT NULL AND (aj.is_correct = 0 OR aj.is_correct IS NULL) THEN 1 ELSE 0 END) as total_salah,
                SUM(CASE WHEN aj.jawaban_id IS NULL THEN 1 ELSE 0 END) as total_tidak_dijawab
            ', false)
            ->join('jadwal_ujian ju', 'ju.jadwal_id = pu.jadwal_id')
            ->join('ujian u', 'u.id_ujian = ju.ujian_id')
            ->join('kelas k', 'k.kelas_id = ju.kelas_id', 'left')
            ->join($this->getAnalitikAttemptSubquery($filters), 'la.peserta_ujian_id = pu.peserta_ujian_id', 'inner', false)
            ->join('attempt_ujian au', 'au.peserta_ujian_id = la.peserta_ujian_id AND au.nomor_attempt = la.max_attempt', 'inner', false)
            ->join('attempt_soal_cbt ats', 'ats.attempt_id = au.attempt_id', 'inner')
            ->join('soal_ujian sq', 'sq.soal_id = ats.original_soal_id', 'left')
            ->join('variabel v', 'v.variabel_id = sq.variabel_id', 'left')
            ->join('indikator i', 'i.indikator_id = sq.indikator_id', 'left')
            ->join('materi m', 'm.materi_id = sq.materi_id', 'left')
            ->join('attempt_jawaban aj', 'aj.attempt_id = ats.attempt_id AND aj.soal_id = ats.original_soal_id', 'left', false)
            ->where('pu.status', 'selesai');

        $this->applyAnalitikScopeFilters($builder, $filters);

        $rows = $builder
            ->groupBy($groupMap[$mode])
            ->orderBy('group_label', 'ASC')
            ->get()
            ->getResultArray();

        foreach ($rows as &$row) {
            $totalSoal = (int) ($row['total_soal'] ?? 0);
            $row['persentase_benar'] = $totalSoal > 0 ? round(((int) ($row['total_benar'] ?? 0) / $totalSoal) * 100, 2) : 0;
            $row['persentase_salah'] = $totalSoal > 0 ? round(((int) ($row['total_salah'] ?? 0) / $totalSoal) * 100, 2) : 0;
            $row['persentase_tidak_dijawab'] = $totalSoal > 0 ? round(((int) ($row['total_tidak_dijawab'] ?? 0) / $totalSoal) * 100, 2) : 0;
        }
        unset($row);

        usort($rows, static fn($a, $b) => ($a['persentase_benar'] <=> $b['persentase_benar']) ?: strcmp($a['group_label'], $b['group_label']));

        return $rows;
    }

    // Hitung rata-rata durasi pengerjaan (detik) untuk peserta yang sesuai filter — dipakai untuk bar chart durasi
    private function getAnalitikAverageDurationSeconds(array $filters): int
    {
        $builder = $this->db->table('peserta_ujian pu')
            ->select('AVG(TIME_TO_SEC(TIMEDIFF(au.waktu_selesai, au.waktu_mulai))) as rata_rata_durasi_detik', false)
            ->join('jadwal_ujian ju', 'ju.jadwal_id = pu.jadwal_id')
            ->join('ujian u', 'u.id_ujian = ju.ujian_id')
            ->join('kelas k', 'k.kelas_id = ju.kelas_id', 'left')
            ->join($this->getAnalitikAttemptSubquery($filters), 'la.peserta_ujian_id = pu.peserta_ujian_id', 'inner', false)
            ->join('attempt_ujian au', 'au.peserta_ujian_id = la.peserta_ujian_id AND au.nomor_attempt = la.max_attempt', 'inner', false)
            ->where('pu.status', 'selesai')
            ->where('au.waktu_mulai IS NOT NULL', null, false)
            ->where('au.waktu_selesai IS NOT NULL', null, false);

        if (!empty($filters['variabel_id']) || !empty($filters['indikator_id']) || !empty($filters['materi_id'])) {
            $builder
                ->join('attempt_soal_cbt ats', 'ats.attempt_id = au.attempt_id', 'inner')
                ->join('soal_ujian sq', 'sq.soal_id = ats.original_soal_id', 'left');
        }

        $this->applyAnalitikScopeFilters($builder, $filters);

        $siswaIds = $this->getFilteredSiswaIds($filters);
        if ($siswaIds !== null) {
            if (empty($siswaIds)) return 0;
            $builder->whereIn('pu.siswa_id', $siswaIds);
        }

        $row = $builder->get()->getRowArray() ?? [];

        return (int) round((float) ($row['rata_rata_durasi_detik'] ?? 0));
    }

    // Bangun data 3 bar durasi (Variabel/Indikator/Materi) — filter materi/indikator dipersempit per level agar konsisten
    private function getAnalitikDurationBars(array $filters): array
    {
        $variabelFilters = $filters;
        $variabelFilters['indikator_id'] = null;
        $variabelFilters['materi_id'] = null;

        $indikatorFilters = $filters;
        $indikatorFilters['materi_id'] = null;

        return [
            ['label' => 'Variabel', 'seconds' => $this->getAnalitikAverageDurationSeconds($variabelFilters), 'color' => '#2563eb'],
            ['label' => 'Indikator', 'seconds' => $this->getAnalitikAverageDurationSeconds($indikatorFilters), 'color' => '#0f766e'],
            ['label' => 'Materi', 'seconds' => $this->getAnalitikAverageDurationSeconds($filters), 'color' => '#d97706'],
        ];
    }

    // Ambil baris per siswa untuk tabel analitik — termasuk variabel/indikator/materi yang dikerjakan dan interpretasi kecepatan
    private function getAnalitikStudentRows(array $filters): array
    {
        $builder = $this->db->table('peserta_ujian pu')
            ->select('
                pu.peserta_ujian_id,
                siswa.nama_lengkap,
                sekolah.nama_sekolah,
                kelas.nama_kelas,
                u.tipe_ujian,
                u.nama_ujian,
                COALESCE(GROUP_CONCAT(DISTINCT v.nama_variabel ORDER BY v.nama_variabel SEPARATOR ", "), "-") as daftar_variabel,
                COALESCE(GROUP_CONCAT(DISTINCT i.nama_indikator ORDER BY i.nama_indikator SEPARATOR ", "), "-") as daftar_indikator,
                COALESCE(GROUP_CONCAT(DISTINCT m.nama_materi ORDER BY m.nama_materi SEPARATOR ", "), "-") as daftar_materi,
                TIME_TO_SEC(TIMEDIFF(au.waktu_selesai, au.waktu_mulai)) as durasi_detik
            ', false)
            ->join('jadwal_ujian ju', 'ju.jadwal_id = pu.jadwal_id')
            ->join('ujian u', 'u.id_ujian = ju.ujian_id')
            ->join('kelas', 'kelas.kelas_id = ju.kelas_id', 'left')
            ->join('sekolah', 'sekolah.sekolah_id = kelas.sekolah_id', 'left')
            ->join('siswa', 'siswa.siswa_id = pu.siswa_id', 'left')
            ->join($this->getAnalitikAttemptSubquery($filters), 'la.peserta_ujian_id = pu.peserta_ujian_id', 'inner', false)
            ->join('attempt_ujian au', 'au.peserta_ujian_id = la.peserta_ujian_id AND au.nomor_attempt = la.max_attempt', 'inner', false)
            ->join('attempt_soal_cbt ats', 'ats.attempt_id = au.attempt_id', 'inner')
            ->join('soal_ujian sq', 'sq.soal_id = ats.original_soal_id', 'left')
            ->join('variabel v', 'v.variabel_id = sq.variabel_id', 'left')
            ->join('indikator i', 'i.indikator_id = sq.indikator_id', 'left')
            ->join('materi m', 'm.materi_id = sq.materi_id', 'left')
            ->where('pu.status', 'selesai')
            ->where('au.waktu_mulai IS NOT NULL', null, false)
            ->where('au.waktu_selesai IS NOT NULL', null, false);

        $this->applyAnalitikScopeFilters($builder, $filters);

        $siswaIds = $this->getFilteredSiswaIds($filters);
        if ($siswaIds !== null) {
            if (empty($siswaIds)) return [];
            $builder->whereIn('siswa.siswa_id', $siswaIds);
        }

        $rows = $builder
            ->groupBy('pu.peserta_ujian_id, siswa.nama_lengkap, sekolah.nama_sekolah, kelas.nama_kelas, u.tipe_ujian, u.nama_ujian, au.waktu_mulai, au.waktu_selesai')
            ->orderBy('siswa.nama_lengkap', 'ASC')
            ->get()
            ->getResultArray();

        $durations = array_values(array_filter(array_map(static fn($row) => (int) ($row['durasi_detik'] ?? 0), $rows)));
        $averageDuration = !empty($durations) ? (int) round(array_sum($durations) / count($durations)) : 0;

        foreach ($rows as &$row) {
            $durationSeconds = (int) ($row['durasi_detik'] ?? 0);
            $row['interpretasi'] = $this->getAnalitikInterpretasiDurasi($durationSeconds, $averageDuration);
        }
        unset($row);

        return $rows;
    }

    // Klasifikasikan durasi siswa relatif terhadap rata-rata: ±10% = Rata-rata, < 90% = Cepat, > 110% = Lambat
    private function getAnalitikInterpretasiDurasi(int $durationSeconds, int $averageDuration): string
    {
        if ($durationSeconds <= 0 || $averageDuration <= 0) {
            return 'Rata-rata';
        }

        if ($durationSeconds <= (int) floor($averageDuration * 0.9)) {
            return 'Cepat';
        }

        if ($durationSeconds >= (int) ceil($averageDuration * 1.1)) {
            return 'Lambat';
        }

        return 'Rata-rata';
    }


    // Daftar hasil semua siswa dalam satu jadwal — setiap siswa dihitung skornya, CAT pakai theta, CBT pakai nilai_akhir
    public function hasilUjianSiswa($jadwalId)
    {
        $db = \Config\Database::connect();

        // Ambil info ujian
        $ujian = $db->table('jadwal_ujian ju')
            ->select('ju.*, u.nama_ujian, u.deskripsi, u.kode_ujian, u.tipe_ujian, j.nama_jenis, k.nama_kelas, k.tahun_ajaran,
                     s.nama_sekolah, g.nama_lengkap as nama_guru,
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
            return redirect()->to(base_url('admin/hasil-ujian'));
        }

        // Ambil data semua peserta untuk jadwal ini
        $hasilSiswa = $db->table('peserta_ujian pu')
            ->select('pu.peserta_ujian_id, pu.status, pu.waktu_mulai, pu.waktu_selesai,
                     siswa.siswa_id, siswa.nama_lengkap, siswa.nomor_peserta, siswa.jenis_kelamin,
                     u.username,
                     TIME_TO_SEC(TIMEDIFF(pu.waktu_selesai, pu.waktu_mulai)) as durasi_detik,
                     DATE_FORMAT(pu.waktu_mulai, "%d/%m/%Y %H:%i:%s") as waktu_mulai_format,
                     DATE_FORMAT(pu.waktu_selesai, "%d/%m/%Y %H:%i:%s") as waktu_selesai_format')
            ->join('siswa', 'siswa.siswa_id = pu.siswa_id', 'left')
            ->join('users u', 'u.user_id = siswa.user_id', 'left')
            ->where('pu.jadwal_id', $jadwalId)
            ->orderBy('siswa.nama_lengkap', 'ASC')
            ->get()
            ->getResultArray();

        // Proses setiap siswa untuk melengkapi data yang dibutuhkan view
        foreach ($hasilSiswa as &$siswa) {
            if ($siswa['status'] === 'selesai') {
                $attempts = $this->getAttemptsForPeserta((int) $siswa['peserta_ujian_id']);
                $attempt = !empty($attempts) ? end($attempts) : null;
                $detailJawaban = $this->getAttemptAwareDetailJawaban((int) $siswa['peserta_ujian_id'], $attempt['attempt_id'] ?? null);
                $summary = $this->buildResultSummary($ujian, $detailJawaban, $attempt);

                $siswa['theta_akhir'] = $summary['theta_akhir'];
                $siswa['skor'] = $summary['skor_akhir'];
                $siswa['nilai'] = $summary['nilai_akhir'];
                $siswa['se_akhir'] = $summary['se_akhir'];
                $siswa['is_cat_mode'] = $summary['is_cat_mode'];

                $jawabanBenar = count(array_filter($detailJawaban, static fn($item) => (int) ($item['is_correct'] ?? 0) === 1));
                $totalSoal = count($detailJawaban);
                $siswa['jawaban_benar'] = $jawabanBenar;
                $siswa['total_soal'] = $totalSoal;
                $siswa['jumlah_attempt'] = count($attempts);
                $siswa['attempt_terakhir'] = $attempt['nomor_attempt'] ?? null;

                // Bangun array durasi per percobaan — dipakai untuk tooltip di tabel hasil siswa
                $attemptsDurasi = [];
                foreach ($attempts as $att) {
                    $d = (!empty($att['waktu_mulai']) && !empty($att['waktu_selesai']))
                        ? strtotime($att['waktu_selesai']) - strtotime($att['waktu_mulai'])
                        : 0;
                    $attemptsDurasi[] = [
                        'nomor'  => (int) $att['nomor_attempt'],
                        'durasi' => $d > 0 ? sprintf('%02d:%02d:%02d', floor($d / 3600), floor(($d % 3600) / 60), $d % 60) : '-',
                    ];
                }
                $siswa['attempts_durasi'] = $attemptsDurasi;

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
                // Set nilai default untuk semua key agar tidak error di view
                $siswa['theta_akhir'] = null;
                $siswa['skor'] = null;
                $siswa['nilai'] = null;
                $siswa['se_akhir'] = null;
                $siswa['is_cat_mode'] = ($ujian['tipe_ujian'] ?? 'CAT') === 'CAT';
                $siswa['jawaban_benar'] = 0;
                $siswa['total_soal'] = 0;
                $siswa['jumlah_attempt'] = 0;
                $siswa['attempt_terakhir'] = null;
                $siswa['attempts_durasi'] = [];
                $siswa['klasifikasi_kognitif'] = $this->getKlasifikasiKognitif(0);
                $siswa['durasi_format'] = '-';
            }
        }

        $data = [
            'ujian' => $ujian,
            'hasilSiswa' => $hasilSiswa
        ];

        return view('admin/hasil/siswa', $data);
    }

    // Daftar semua percobaan (attempt) satu peserta — setiap attempt dihitung ulang skornya untuk ditampilkan
    public function daftarPercobaanSiswa($pesertaUjianId)
    {
        $peserta = $this->pesertaUjianModel
            ->select('peserta_ujian.*, jadwal_ujian.jadwal_id, ujian.nama_ujian, ujian.deskripsi, ujian.kode_ujian, ujian.tipe_ujian,
                jenis_ujian.nama_jenis, sekolah.nama_sekolah, guru.nama_lengkap as nama_guru,
                siswa.nama_lengkap, siswa.nomor_peserta, kelas.nama_kelas, kelas.tahun_ajaran')
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
            return redirect()->to(base_url('admin/hasil-ujian'));
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

        return view('admin/hasil/percobaan', [
            'peserta' => $peserta,
            'attempts' => $attempts,
        ]);
    }
    // Detail hasil siswa — bisa lihat attempt tertentu via ?attempt_id=X, default ke attempt terakhir; backUrl beda antara CAT dan CBT
    public function detailHasilSiswa($pesertaUjianId)
    {
        $db = \Config\Database::connect();
        // Kalau ada query param attempt_id, tampilkan attempt itu; kalau tidak, pakai yang terbaru
        $requestedAttemptId = (int) $this->request->getGet('attempt_id');
        $attempt = $requestedAttemptId > 0
            ? $this->getAttemptByIdForPeserta((int) $pesertaUjianId, $requestedAttemptId)
            : $this->getLatestAttemptForPeserta((int) $pesertaUjianId);

        $hasil = $db->table('peserta_ujian pu')
            ->select('pu.*, ju.*, u.nama_ujian, u.deskripsi, u.tipe_ujian, j.nama_jenis,
                  siswa.nama_lengkap, siswa.nomor_peserta,
                  k.nama_kelas, k.tahun_ajaran, s.nama_sekolah,
                  g.nama_lengkap as nama_guru,
                  TIMEDIFF(pu.waktu_selesai, pu.waktu_mulai) as durasi_total,
                  TIME_TO_SEC(TIMEDIFF(pu.waktu_selesai, pu.waktu_mulai)) as durasi_total_detik,
                  DATE_FORMAT(pu.waktu_mulai, "%d/%m/%Y %H:%i:%s") as waktu_mulai_format,
                  DATE_FORMAT(pu.waktu_selesai, "%d/%m/%Y %H:%i:%s") as waktu_selesai_format')
            ->join('jadwal_ujian ju', 'ju.jadwal_id = pu.jadwal_id', 'left')
            ->join('ujian u', 'u.id_ujian = ju.ujian_id', 'left')
            ->join('jenis_ujian j', 'j.jenis_ujian_id = u.jenis_ujian_id', 'left')
            ->join('siswa', 'siswa.siswa_id = pu.siswa_id', 'left')
            ->join('kelas k', 'k.kelas_id = ju.kelas_id', 'left')
            ->join('sekolah s', 's.sekolah_id = k.sekolah_id', 'left')
            ->join('guru g', 'g.guru_id = ju.guru_id', 'left')
            ->where('pu.peserta_ujian_id', $pesertaUjianId)
            ->get()
            ->getRowArray();

        if (!$hasil) {
            session()->setFlashdata('error', 'Data hasil ujian tidak ditemukan');
            return redirect()->to(base_url('admin/hasil-ujian'));
        }

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

        $detailJawabanDenganDurasi = $this->hitungDurasiPerSoal($detailJawaban, $hasil['waktu_mulai'] ?? null);
        $totalSoal = count($detailJawabanDenganDurasi);
        $jawabanBenar = array_reduce($detailJawabanDenganDurasi, fn($c, $i) => $c + ($i['is_correct'] ? 1 : 0), 0);

        // Perhitungan Skor Kognitif
        $summary = $this->buildResultSummary($hasil, $detailJawabanDenganDurasi, $attempt);
        $skor_akhir = $summary['skor_akhir'];
        $klasifikasiKognitif = $summary['klasifikasi_kognitif'];

        $kemampuanKognitif = [
            'skor' => $skor_akhir,
            'total_benar' => $jawabanBenar,
            'total_salah' => $totalSoal - $jawabanBenar,
            'rata_rata_pilihan' => 0
        ];

        // ====================================================================
        // KODE YANG DITAMBAHKAN KEMBALI UNTUK MEMPERBAIKI ERROR
        // ====================================================================
        if (!empty($hasil['durasi_total_detik'])) {
            $jam = floor($hasil['durasi_total_detik'] / 3600);
            $menit = floor(($hasil['durasi_total_detik'] % 3600) / 60);
            $detik = $hasil['durasi_total_detik'] % 60;
            $hasil['durasi_total_format'] = sprintf('%02d:%02d:%02d', $jam, $menit, $detik);
        } else {
            $hasil['durasi_total_format'] = '-';
        }

        if ($totalSoal > 0 && !empty($hasil['durasi_total_detik'])) {
            $rataRataWaktu = $hasil['durasi_total_detik'] / $totalSoal;
            $rataRataMenit = floor($rataRataWaktu / 60);
            $rataRataDetik = (int) $rataRataWaktu % 60;
            $rataRataWaktuFormat = sprintf('%d menit %d detik', $rataRataMenit, $rataRataDetik);
        } else {
            $rataRataWaktuFormat = '-';
        }
        // ====================================================================

        $data = [
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
            'rataRataWaktuFormat' => $rataRataWaktuFormat, // Pastikan variabel ini dikirim ke view
            // CAT tidak ada halaman percobaan karena hanya 1 attempt — langsung balik ke daftar siswa; CBT bisa multi-attempt
            'backUrl' => $summary['is_cat_mode']
                ? base_url('admin/hasil-ujian/siswa/' . $hasil['jadwal_id'])
                : base_url('admin/hasil-ujian/percobaan/' . $pesertaUjianId),
        ];

        return view('admin/hasil/detail', $data);
    }


    // Download hasil ujian sebagai file XLS — dirender dari view HTML dengan header Content-Type ms-excel
    public function downloadExcelHTML($pesertaUjianId)
    {
        $db = \Config\Database::connect();
        $requestedAttemptId = (int) $this->request->getGet('attempt_id');
        $attempt = $requestedAttemptId > 0
            ? $this->getAttemptByIdForPeserta((int) $pesertaUjianId, $requestedAttemptId)
            : $this->getLatestAttemptForPeserta((int) $pesertaUjianId);
        $hasil = $db->table('peserta_ujian pu')
            ->select('pu.*, ju.*, u.nama_ujian, u.deskripsi, u.tipe_ujian, j.nama_jenis,
                  siswa.nama_lengkap, siswa.nomor_peserta,
                  k.nama_kelas, k.tahun_ajaran, s.nama_sekolah,
                  g.nama_lengkap as nama_guru,
                  TIMEDIFF(pu.waktu_selesai, pu.waktu_mulai) as durasi_total,
                  TIME_TO_SEC(TIMEDIFF(pu.waktu_selesai, pu.waktu_mulai)) as durasi_total_detik,
                  DATE_FORMAT(pu.waktu_mulai, "%d/%m/%Y %H:%i:%s") as waktu_mulai_format,
                  DATE_FORMAT(pu.waktu_selesai, "%d/%m/%Y %H:%i:%s") as waktu_selesai_format')
            ->join('jadwal_ujian ju', 'ju.jadwal_id = pu.jadwal_id', 'left')
            ->join('ujian u', 'u.id_ujian = ju.ujian_id', 'left')
            ->join('jenis_ujian j', 'j.jenis_ujian_id = u.jenis_ujian_id', 'left')
            ->join('siswa', 'siswa.siswa_id = pu.siswa_id', 'left')
            ->join('kelas k', 'k.kelas_id = ju.kelas_id', 'left')
            ->join('sekolah s', 's.sekolah_id = k.sekolah_id', 'left')
            ->join('guru g', 'g.guru_id = ju.guru_id', 'left')
            ->where('pu.peserta_ujian_id', $pesertaUjianId)->get()->getRowArray();

        if (!$hasil) {
            return redirect()->to(base_url('admin/hasil-ujian'));
        }

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
        $jawabanBenar = array_reduce($detailJawabanDenganDurasi, fn($c, $i) => $c + ($i['is_correct'] ? 1 : 0), 0);

        $summary = $this->buildResultSummary($hasil, $detailJawabanDenganDurasi, $attempt);
        $theta_akhir = $summary['theta_akhir'];
        $skor_akhir = $summary['skor_akhir'];
        $klasifikasiKognitif = $summary['klasifikasi_kognitif'];
        $kemampuanKognitif = ['skor' => $skor_akhir, 'total_benar' => $jawabanBenar, 'total_salah' => $totalSoal - $jawabanBenar, 'rata_rata_pilihan' => 0];

        // Tambahkan blok format durasi
        if (!empty($hasil['durasi_total_detik'])) {
            $jam = floor($hasil['durasi_total_detik'] / 3600);
            $menit = floor(($hasil['durasi_total_detik'] % 3600) / 60);
            $detik = $hasil['durasi_total_detik'] % 60;
            $hasil['durasi_total_format'] = sprintf('%02d:%02d:%02d', $jam, $menit, $detik);
        } else {
            $hasil['durasi_total_format'] = '-';
        }

        if ($totalSoal > 0 && !empty($hasil['durasi_total_detik'])) {
            $rataRataWaktu = $hasil['durasi_total_detik'] / $totalSoal;
            $rataRataMenit = floor($rataRataWaktu / 60);
            $rataRataDetik = (int) $rataRataWaktu % 60;
            $rataRataWaktuFormat = sprintf('%d menit %d detik', $rataRataMenit, $rataRataDetik);
        } else {
            $rataRataWaktuFormat = '-';
        }

        $data = ['hasil' => $hasil, 'detailJawaban' => $detailJawabanDenganDurasi, 'isCatMode' => $summary['is_cat_mode'], 'finalScore' => $skor_akhir, 'lastTheta' => $theta_akhir, 'finalGrade' => $summary['nilai_akhir'], 'seAkhir' => $summary['se_akhir'], 'jawabanBenar' => $jawabanBenar, 'kemampuanKognitif' => $kemampuanKognitif, 'klasifikasiKognitif' => $klasifikasiKognitif, 'rataRataWaktuFormat' => $rataRataWaktuFormat];

        $filename = 'hasil_ujian_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $hasil['nama_lengkap']) . '_' . date('dmY') . '.xls';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo view('admin/hasil/download_excel', $data); // Sesuaikan path jika perlu
        exit;
    }

    // Download hasil ujian sebagai HTML inline siap print — juga kirim data chart theta/SE untuk grafik di view
    public function downloadPDFHTML($pesertaUjianId)
    {
        $db = \Config\Database::connect();
        $requestedAttemptId = (int) $this->request->getGet('attempt_id');
        $attempt = $requestedAttemptId > 0
            ? $this->getAttemptByIdForPeserta((int) $pesertaUjianId, $requestedAttemptId)
            : $this->getLatestAttemptForPeserta((int) $pesertaUjianId);

        // Ambil data hasil lengkap
        $hasil = $db->table('peserta_ujian pu')
            ->select('pu.*, ju.*, u.nama_ujian, u.deskripsi, u.kode_ujian, u.tipe_ujian, j.nama_jenis,
                  siswa.nama_lengkap, siswa.nomor_peserta,
                  k.nama_kelas, k.tahun_ajaran, s.nama_sekolah,
                  g.nama_lengkap as nama_guru,
                  TIMEDIFF(pu.waktu_selesai, pu.waktu_mulai) as durasi_total,
                  TIME_TO_SEC(TIMEDIFF(pu.waktu_selesai, pu.waktu_mulai)) as durasi_total_detik,
                  DATE_FORMAT(pu.waktu_mulai, "%d/%m/%Y %H:%i:%s") as waktu_mulai_format,
                  DATE_FORMAT(pu.waktu_selesai, "%d/%m/%Y %H:%i:%s") as waktu_selesai_format')
            ->join('jadwal_ujian ju', 'ju.jadwal_id = pu.jadwal_id', 'left')
            ->join('ujian u', 'u.id_ujian = ju.ujian_id', 'left')
            ->join('jenis_ujian j', 'j.jenis_ujian_id = u.jenis_ujian_id', 'left')
            ->join('siswa', 'siswa.siswa_id = pu.siswa_id', 'left')
            ->join('kelas k', 'k.kelas_id = ju.kelas_id', 'left')
            ->join('sekolah s', 's.sekolah_id = k.sekolah_id', 'left')
            ->join('guru g', 'g.guru_id = ju.guru_id', 'left')
            ->where('pu.peserta_ujian_id', $pesertaUjianId)
            ->get()->getRowArray();

        if (!$hasil) {
            session()->setFlashdata('error', 'Data hasil ujian tidak ditemukan.');
            return redirect()->to(base_url('admin/hasil-ujian'));
        }

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

        // Ambil detail jawaban
        $detailJawaban = $this->getAttemptAwareDetailJawaban($pesertaUjianId, $attempt['attempt_id'] ?? null);

        $detailJawabanDenganDurasi = $this->hitungDurasiPerSoal($detailJawaban, $hasil['waktu_mulai']);
        $totalSoal = count($detailJawabanDenganDurasi);
        $jawabanBenar = array_reduce($detailJawabanDenganDurasi, fn($c, $i) => $c + ($i['is_correct'] ? 1 : 0), 0);

        // Perhitungan Skor Baru
        $summary = $this->buildResultSummary($hasil, $detailJawabanDenganDurasi, $attempt);
        $theta_akhir = $summary['theta_akhir'];
        $skor_akhir = $summary['skor_akhir'];
        $klasifikasiKognitif = $summary['klasifikasi_kognitif'];
        $kemampuanKognitif = [
            'skor' => $skor_akhir,
            'total_benar' => $jawabanBenar,
            'total_salah' => $totalSoal - $jawabanBenar,
            'rata_rata_pilihan' => 0 // Tidak relevan lagi
        ];

        // Pemformatan Durasi
        if (!empty($hasil['durasi_total_detik'])) {
            $jam = floor($hasil['durasi_total_detik'] / 3600);
            $menit = floor(($hasil['durasi_total_detik'] % 3600) / 60);
            $detik = $hasil['durasi_total_detik'] % 60;
            $hasil['durasi_total_format'] = sprintf('%02d:%02d:%02d', $jam, $menit, $detik);
        } else {
            $hasil['durasi_total_format'] = '-';
        }

        if ($totalSoal > 0 && !empty($hasil['durasi_total_detik'])) {
            $rataRataWaktu = $hasil['durasi_total_detik'] / $totalSoal;
            $rataRataMenit = floor($rataRataWaktu / 60);
            $rataRataDetik = (int) $rataRataWaktu % 60;
            $rataRataWaktuFormat = sprintf('%d menit %d detik', $rataRataMenit, $rataRataDetik);
        } else {
            $rataRataWaktuFormat = '-';
        }

        // Persiapan data untuk view PDF
        $data = [
            'hasil' => $hasil,
            'detailJawaban' => $detailJawabanDenganDurasi,
            'isCatMode' => $summary['is_cat_mode'],
            'finalScore' => $summary['skor_akhir'],
            'lastTheta' => $theta_akhir,
            'thetaAkhir' => $theta_akhir,
            'finalGrade' => $summary['nilai_akhir'],
            'seAkhir' => $summary['se_akhir'],
            'jawabanBenar' => $jawabanBenar,
            'totalSoal' => $totalSoal,
            'kemampuanKognitif' => $kemampuanKognitif,
            'klasifikasiKognitif' => $klasifikasiKognitif,
            'rataRataWaktuFormat' => $rataRataWaktuFormat,
            'thetaData' => json_encode(array_column($detailJawabanDenganDurasi, 'theta_saat_ini')),
            'seData' => json_encode(array_column($detailJawabanDenganDurasi, 'se_saat_ini')),
            'labels' => json_encode(array_column($detailJawabanDenganDurasi, 'nomor_soal')),
        ];

        $html = view('admin/hasil/download_pdf', $data);

        header('Content-Type: text/html');
        header('Content-Disposition: inline; filename="laporan_hasil_ujian.html"');
        echo $html;
        exit;
    }

    // Hapus hasil ujian siswa dan reset status peserta ke belum_mulai — berguna untuk memulai ulang ujian siswa
    public function hapusHasilSiswa($pesertaUjianId)
    {
        try {
            $db = \Config\Database::connect();
            $db->transStart();

            $peserta = $db->table('peserta_ujian')->where('peserta_ujian_id', $pesertaUjianId)->get()->getRowArray();
            if (!$peserta) {
                session()->setFlashdata('error', 'Data peserta tidak ditemukan');
                return redirect()->back();
            }

            // Hapus semua data attempt beserta jawaban dan analisis
            $attempts = $db->table('attempt_ujian')
                ->where('peserta_ujian_id', $pesertaUjianId)
                ->get()->getResultArray();

            foreach ($attempts as $attempt) {
                $aid = (int) $attempt['attempt_id'];
                $db->table('attempt_jawaban_cat')->where('attempt_id', $aid)->delete();
                $db->table('attempt_jawaban_cbt')->where('attempt_id', $aid)->delete();
                $db->table('attempt_soal_cbt')->where('attempt_id', $aid)->delete();
                $db->table('attempt_analisis_cbt')->where('attempt_id', $aid)->delete();
                $db->table('attempt_soal')->where('attempt_id', $aid)->delete();
            }
            $db->table('attempt_ujian')->where('peserta_ujian_id', $pesertaUjianId)->delete();

            $db->table('hasil_ujian')->where('peserta_ujian_id', $pesertaUjianId)->delete();

            // Reset status peserta ke belum_mulai
            $db->table('peserta_ujian')
                ->where('peserta_ujian_id', $pesertaUjianId)
                ->update(['status' => 'belum_mulai', 'waktu_mulai' => null, 'waktu_selesai' => null]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }

            session()->setFlashdata('success', 'Hasil ujian siswa berhasil dihapus dan direset!');
        } catch (\Exception $e) {
            log_message('error', 'Error deleting hasil siswa: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat menghapus hasil ujian: ' . $e->getMessage());
        }

        return redirect()->to(base_url('admin/hasil-ujian/siswa/' . $peserta['jadwal_id']));
    }

    // ===== KELOLA PENGUMUMAN =====

    // Daftar semua pengumuman urut terbaru, beserta username pembuat
    public function daftarPengumuman()
    {
        $db = \Config\Database::connect();

        // Ambil semua pengumuman dengan info pembuat
        $data['pengumuman'] = $db->table('pengumuman p')
            ->select('p.*, u.username as pembuat')
            ->join('users u', 'u.user_id = p.created_by', 'left')
            ->orderBy('p.tanggal_publish', 'DESC')
            ->get()
            ->getResultArray();

        return view('admin/pengumuman/daftar', $data);
    }

    // Form tambah pengumuman (tidak ada data yang perlu diambil)
    public function formTambahPengumuman()
    {
        return view('admin/pengumuman/tambah');
    }

    // Proses simpan pengumuman — tanggal_berakhir opsional dan divalidasi manual karena format datetime-local
    public function tambahPengumuman()
    {
        $rules = [
            'judul' => 'required|min_length[5]|max_length[200]',
            'isi_pengumuman' => 'required|min_length[10]',
            // HAPUS validasi tanggal_berakhir dari rules
            // 'tanggal_berakhir' => 'permit_empty|valid_date[Y-m-d H:i]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        try {
            $pengumumanModel = new \App\Models\PengumumanModel();

            // Handle tanggal_berakhir secara manual
            $tanggalBerakhir = $this->request->getPost('tanggal_berakhir');

            // Validasi manual untuk tanggal berakhir jika diisi
            if (!empty($tanggalBerakhir)) {
                // Konversi dari format datetime-local ke format database
                $tanggalBerakhir = date('Y-m-d H:i:s', strtotime($tanggalBerakhir));

                // Validasi apakah tanggal valid
                if ($tanggalBerakhir === false || $tanggalBerakhir === '1970-01-01 00:00:00') {
                    session()->setFlashdata('error', 'Format tanggal berakhir tidak valid.');
                    return redirect()->back()->withInput();
                }

                // Validasi apakah tanggal berakhir tidak lebih awal dari sekarang
                if (strtotime($tanggalBerakhir) <= time()) {
                    session()->setFlashdata('error', 'Tanggal berakhir harus lebih dari waktu sekarang.');
                    return redirect()->back()->withInput();
                }
            } else {
                $tanggalBerakhir = null;
            }

            $data = [
                'judul' => $this->request->getPost('judul'),
                'isi_pengumuman' => $this->request->getPost('isi_pengumuman'),
                'tanggal_publish' => date('Y-m-d H:i:s'),
                'tanggal_berakhir' => $tanggalBerakhir,
                'created_by' => session()->get('user_id')
            ];

            $pengumumanModel->insert($data);
            session()->setFlashdata('success', 'Pengumuman berhasil ditambahkan!');
            return redirect()->to(base_url('admin/pengumuman'));
        } catch (\Exception $e) {
            log_message('error', 'Error adding pengumuman: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat menambah pengumuman: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    // Form edit pengumuman — ambil data yang sudah ada untuk diisi ke form
    public function formEditPengumuman($pengumumanId)
    {
        $pengumumanModel = new \App\Models\PengumumanModel();
        $pengumuman = $pengumumanModel->find($pengumumanId);

        if (!$pengumuman) {
            session()->setFlashdata('error', 'Pengumuman tidak ditemukan');
            return redirect()->to(base_url('admin/pengumuman'));
        }

        $data['pengumuman'] = $pengumuman;
        return view('admin/pengumuman/edit', $data);
    }

    // Proses update pengumuman — tanggal_berakhir null berarti pengumuman tidak punya batas waktu
    public function editPengumuman($pengumumanId)
    {
        $pengumumanModel = new \App\Models\PengumumanModel();
        $pengumuman = $pengumumanModel->find($pengumumanId);

        if (!$pengumuman) {
            session()->setFlashdata('error', 'Pengumuman tidak ditemukan');
            return redirect()->to(base_url('admin/pengumuman'));
        }

        $rules = [
            'judul' => 'required|min_length[5]|max_length[200]',
            'isi_pengumuman' => 'required|min_length[10]',
            // HAPUS validasi tanggal_berakhir dari rules
            // 'tanggal_berakhir' => 'permit_empty|valid_date[Y-m-d H:i]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        try {
            // Handle tanggal_berakhir secara manual
            $tanggalBerakhir = $this->request->getPost('tanggal_berakhir');

            // Validasi manual untuk tanggal berakhir jika diisi
            if (!empty($tanggalBerakhir)) {
                // Konversi dari format datetime-local ke format database
                $tanggalBerakhir = date('Y-m-d H:i:s', strtotime($tanggalBerakhir));

                // Validasi apakah tanggal valid
                if ($tanggalBerakhir === false || $tanggalBerakhir === '1970-01-01 00:00:00') {
                    session()->setFlashdata('error', 'Format tanggal berakhir tidak valid.');
                    return redirect()->back()->withInput();
                }
            } else {
                $tanggalBerakhir = null;
            }

            $data = [
                'judul' => $this->request->getPost('judul'),
                'isi_pengumuman' => $this->request->getPost('isi_pengumuman'),
                'tanggal_berakhir' => $tanggalBerakhir
            ];

            $pengumumanModel->update($pengumumanId, $data);
            session()->setFlashdata('success', 'Pengumuman berhasil diperbarui!');
            return redirect()->to(base_url('admin/pengumuman'));
        } catch (\Exception $e) {
            log_message('error', 'Error updating pengumuman: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat memperbarui pengumuman: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    // Hapus pengumuman secara permanen
    public function hapusPengumuman($pengumumanId)
    {
        try {
            $pengumumanModel = new \App\Models\PengumumanModel();
            $pengumuman = $pengumumanModel->find($pengumumanId);

            if (!$pengumuman) {
                session()->setFlashdata('error', 'Pengumuman tidak ditemukan');
                return redirect()->to(base_url('admin/pengumuman'));
            }

            $pengumumanModel->delete($pengumumanId);
            session()->setFlashdata('success', 'Pengumuman berhasil dihapus!');
        } catch (\Exception $e) {
            log_message('error', 'Error deleting pengumuman: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat menghapus pengumuman.');
        }

        return redirect()->to(base_url('admin/pengumuman'));
    }

    // Detail pengumuman dengan info pembuat
    public function detailPengumuman($pengumumanId)
    {
        $db = \Config\Database::connect();

        $pengumuman = $db->table('pengumuman p')
            ->select('p.*, u.username as pembuat')
            ->join('users u', 'u.user_id = p.created_by', 'left')
            ->where('p.pengumuman_id', $pengumumanId)
            ->get()
            ->getRowArray();

        if (!$pengumuman) {
            session()->setFlashdata('error', 'Pengumuman tidak ditemukan');
            return redirect()->to(base_url('admin/pengumuman'));
        }

        $data['pengumuman'] = $pengumuman;
        return view('admin/pengumuman/detail', $data);
    }

    // Toggle aktif/nonaktif pengumuman — nonaktif dilakukan dengan mengisi tanggal_berakhir ke sekarang, aktif dengan mengosongkannya
    public function toggleStatusPengumuman($pengumumanId)
    {
        try {
            $pengumumanModel = new \App\Models\PengumumanModel();
            $pengumuman = $pengumumanModel->find($pengumumanId);

            if (!$pengumuman) {
                session()->setFlashdata('error', 'Pengumuman tidak ditemukan');
                return redirect()->to(base_url('admin/pengumuman'));
            }

            // Toggle berdasarkan tanggal berakhir
            $newStatus = $pengumuman['tanggal_berakhir'] ? null : date('Y-m-d H:i:s');

            $pengumumanModel->update($pengumumanId, ['tanggal_berakhir' => $newStatus]);

            $statusText = $newStatus ? 'dinonaktifkan' : 'diaktifkan';
            session()->setFlashdata('success', "Pengumuman berhasil {$statusText}!");
        } catch (\Exception $e) {
            log_message('error', 'Error toggling pengumuman status: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat mengubah status pengumuman.');
        }

        return redirect()->to(base_url('admin/pengumuman'));
    }

    // ===== KELOLA BANK SOAL =====

    // Halaman indeks bank soal — tampilkan daftar kategori beserta jumlah bank dan sekolah terkait
    public function bankSoal()
    {
        $db = \Config\Database::connect();

        $kategoriList = $db->table('bank_ujian')
            ->select("bank_ujian.kategori, COUNT(*) as jumlah_bank, GROUP_CONCAT(DISTINCT sekolah.nama_sekolah ORDER BY sekolah.nama_sekolah SEPARATOR '||') as sekolah_list")
            ->join('jenis_ujian', 'jenis_ujian.jenis_ujian_id = bank_ujian.jenis_ujian_id', 'left')
            ->join('kelas', 'kelas.kelas_id = jenis_ujian.kelas_id', 'left')
            ->join('sekolah', 'sekolah.sekolah_id = kelas.sekolah_id', 'left')
            ->groupBy('kategori')
            ->orderBy('kategori', 'ASC')
            ->get()
            ->getResultArray();

        $data = [
            'kategoriList'   => $kategoriList,
            'jenisUjianList' => $this->jenisUjianModel->findAll(),
            'sekolah'        => $this->sekolahModel->orderBy('nama_sekolah', 'ASC')->findAll(),
        ];

        return view('admin/bank_soal/index', $data);
    }

    // Proses tambah bank soal baru — cek duplikat kombinasi kategori+jenis_ujian+nama sebelum insert
    public function tambahBankSoal()
    {
        // Debug: Log semua input
        log_message('debug', 'Input data: ' . json_encode($this->request->getPost()));

        $rules = [
            'kategori' => 'required',
            'jenis_ujian_id' => 'required|numeric',
            'nama_ujian' => 'required|min_length[3]',
            'deskripsi' => 'required|min_length[10]'
        ];

        if (!$this->validate($rules)) {
            log_message('debug', 'Validation errors: ' . json_encode($this->validator->getErrors()));
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // Cek apakah kombinasi kategori + jenis_ujian + nama_ujian sudah ada
        $db = \Config\Database::connect();
        $existing = $db->table('bank_ujian')
            ->where('kategori', $this->request->getPost('kategori'))
            ->where('jenis_ujian_id', $this->request->getPost('jenis_ujian_id'))
            ->where('nama_ujian', $this->request->getPost('nama_ujian'))
            ->get()->getRowArray();

        if ($existing) {
            log_message('debug', 'Bank soal already exists');
            session()->setFlashdata('error', 'Bank soal dengan kategori, Mata Pelajaran, dan nama ujian yang sama sudah ada.');
            return redirect()->back()->withInput();
        }

        try {
            $userId = session()->get('user_id');
            log_message('debug', 'Current user ID: ' . $userId);

            if (!$userId) {
                session()->setFlashdata('error', 'Session expired. Please login again.');
                return redirect()->to(base_url('admin/login'));
            }

            $bankUjianData = [
                'kategori' => $this->request->getPost('kategori'),
                'jenis_ujian_id' => $this->request->getPost('jenis_ujian_id'),
                'nama_ujian' => $this->request->getPost('nama_ujian'),
                'deskripsi' => $this->request->getPost('deskripsi'),
                'created_by' => $userId,
                'created_at' => date('Y-m-d H:i:s')
            ];

            log_message('debug', 'Data to insert: ' . json_encode($bankUjianData));

            $result = $db->table('bank_ujian')->insert($bankUjianData);

            if ($result) {
                log_message('debug', 'Bank soal inserted successfully');
                session()->setFlashdata('success', 'Bank soal berhasil ditambahkan!');
            } else {
                log_message('error', 'Failed to insert bank soal');
                session()->setFlashdata('error', 'Gagal menyimpan bank soal.');
            }

            return redirect()->to(base_url('admin/bank-soal'));
        } catch (\Exception $e) {
            log_message('error', 'Error adding bank soal: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat menambah bank soal: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    // Tampilkan bank soal per kategori — kategori 'umum' tidak perlu info sekolah
    public function bankSoalKategori($kategori)
    {
        $db = \Config\Database::connect();

        // Admin bisa akses semua kategori tanpa validasi
        $jenisUjianList = $db->table('bank_ujian')
            ->select('bank_ujian.jenis_ujian_id, jenis_ujian.nama_jenis, COUNT(*) as jumlah_ujian')
            ->join('jenis_ujian', 'jenis_ujian.jenis_ujian_id = bank_ujian.jenis_ujian_id')
            ->where('bank_ujian.kategori', $kategori)
            ->groupBy('bank_ujian.jenis_ujian_id, jenis_ujian.nama_jenis')
            ->orderBy('jenis_ujian.nama_jenis', 'ASC')
            ->get()
            ->getResultArray();

        $kategoriSekolahList = [];
        if ($kategori !== 'umum') {
            $kategoriSekolahList = $db->table('bank_ujian')
                ->distinct()
                ->select('sekolah.nama_sekolah')
                ->join('jenis_ujian', 'jenis_ujian.jenis_ujian_id = bank_ujian.jenis_ujian_id', 'left')
                ->join('kelas', 'kelas.kelas_id = jenis_ujian.kelas_id', 'left')
                ->join('sekolah', 'sekolah.sekolah_id = kelas.sekolah_id', 'left')
                ->where('bank_ujian.kategori', $kategori)
                ->where('sekolah.nama_sekolah IS NOT NULL', null, false)
                ->orderBy('sekolah.nama_sekolah', 'ASC')
                ->get()
                ->getResultArray();
        }

        $data = [
            'kategori' => $kategori,
            'jenisUjianList' => $jenisUjianList,
            'kategoriSekolahList' => $kategoriSekolahList,
        ];

        return view('admin/bank_soal/kategori', $data);
    }

    // Tampilkan daftar bank ujian dalam kategori dan mata pelajaran tertentu — jumlah soal dihitung via subquery
    public function bankSoalJenisUjian($kategori, $jenisUjianId)
    {
        $db = \Config\Database::connect();

        // Ambil daftar ujian dalam Mata Pelajaran dan kategori ini
        $ujianList = $db->table('bank_ujian')
            ->select('bank_ujian.*, users.username as creator_name, 
                 sekolah.nama_sekolah,
                 (SELECT COUNT(*) FROM soal_ujian WHERE soal_ujian.bank_ujian_id = bank_ujian.bank_ujian_id AND soal_ujian.is_bank_soal = 1) as jumlah_soal')
            ->join('users', 'users.user_id = bank_ujian.created_by')
            ->join('jenis_ujian', 'jenis_ujian.jenis_ujian_id = bank_ujian.jenis_ujian_id', 'left')
            ->join('kelas', 'kelas.kelas_id = jenis_ujian.kelas_id', 'left')
            ->join('sekolah', 'sekolah.sekolah_id = kelas.sekolah_id', 'left')
            ->where('bank_ujian.kategori', $kategori)
            ->where('bank_ujian.jenis_ujian_id', $jenisUjianId)
            ->orderBy('bank_ujian.created_at', 'DESC')
            ->get()
            ->getResultArray();

        $kategoriSekolahList = [];
        if ($kategori !== 'umum') {
            $kategoriSekolahList = $db->table('bank_ujian')
                ->distinct()
                ->select('sekolah.nama_sekolah')
                ->join('jenis_ujian', 'jenis_ujian.jenis_ujian_id = bank_ujian.jenis_ujian_id', 'left')
                ->join('kelas', 'kelas.kelas_id = jenis_ujian.kelas_id', 'left')
                ->join('sekolah', 'sekolah.sekolah_id = kelas.sekolah_id', 'left')
                ->where('bank_ujian.kategori', $kategori)
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

        return view('admin/bank_soal/jenis_ujian', $data);
    }

    // Tampilkan detail satu bank ujian beserta daftar soal di dalamnya — admin selalu punya akses edit
    public function bankSoalUjian($kategori, $jenisUjianId, $bankUjianId)
    {
        $db = \Config\Database::connect();

        // Ambil info bank ujian
        $bankUjian = $db->table('bank_ujian')
            ->select('bank_ujian.*, jenis_ujian.nama_jenis, users.username as creator_name')
            ->join('jenis_ujian', 'jenis_ujian.jenis_ujian_id = bank_ujian.jenis_ujian_id')
            ->join('users', 'users.user_id = bank_ujian.created_by')
            ->where('bank_ujian.bank_ujian_id', $bankUjianId)
            ->get()
            ->getRowArray();

        if (!$bankUjian) {
            session()->setFlashdata('error', 'Bank ujian tidak ditemukan');
            return redirect()->to(base_url('admin/bank-soal'));
        }

        // Ambil soal-soal dalam bank ujian ini
        $soalList = $db->table('soal_ujian')
            ->select('soal_ujian.*, users.username as creator_name')
            ->join('users', 'users.user_id = soal_ujian.created_by', 'left')
            ->where('bank_ujian_id', $bankUjianId)
            ->where('is_bank_soal', true)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();

        $data = [
            'kategori' => $kategori,
            'bankUjian' => $bankUjian,
            'soalList' => $soalList,
            'canEdit' => true,  // Admin selalu bisa edit semua bank soal
            'variabel' => $this->variabelModel->orderBy('nama_variabel', 'ASC')->findAll(),
            'indikator' => $this->indikatorModel->orderBy('nama_indikator', 'ASC')->findAll(),
            'materi' => $this->materiModel->orderBy('nama_materi', 'ASC')->findAll(),
        ];

        return view('admin/bank_soal/ujian', $data);
    }

    // Tambah soal ke bank ujian — support AJAX untuk modal form, is_bank_soal harus true dan ujian_id null
    public function tambahSoalBankUjian()
    {
        $bankUjianId = $this->request->getPost('bank_ujian_id');
        $userId = session()->get('user_id');
        $isAjax = $this->request->isAJAX();

        // Admin bisa tambah soal ke bank ujian manapun
        $db = \Config\Database::connect();
        $bankUjian = $db->table('bank_ujian')->where('bank_ujian_id', $bankUjianId)->get()->getRowArray();

        if (!$bankUjian) {
            if ($isAjax) {
                return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Bank ujian tidak ditemukan']);
            }
            return redirect()->back()->with('error', 'Bank soal tidak ditemukan');
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
            'a' => 'permit_empty|decimal',
            'c' => 'permit_empty|decimal',
            'variabel_id' => 'permit_empty|numeric',
            'indikator_id' => 'permit_empty|numeric',
            'materi_id' => 'permit_empty|numeric',
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
            'tingkat_kesulitan' => $this->request->getPost('tingkat_kesulitan'),
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
                return $this->response->setJSON(['success' => true, 'message' => 'Soal berhasil ditambahkan ke bank soal!', 'jumlah_soal' => $jumlahSoal]);
            }
            session()->setFlashdata('success', 'Soal berhasil ditambahkan ke bank soal!');
            return redirect()->back();
        } catch (\Exception $e) {
            log_message('error', 'Error saat menambahkan soal bank ujian: ' . $e->getMessage());
            if ($isAjax) {
                return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Terjadi kesalahan saat menyimpan soal: ' . $e->getMessage()]);
            }
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan soal: ' . $e->getMessage());
        }
    }

    // Update soal di bank ujian — admin tidak perlu validasi kepemilikan; foto lama dihapus jika ada yang baru diupload
    public function editSoalBankUjian($soalId)
    {
        // Admin bisa edit soal bank ujian siapa saja
        $soal = $this->soalUjianModel->find($soalId);
        if (!$soal || !$soal['is_bank_soal']) {
            return redirect()->back()->with('error', 'Soal tidak ditemukan');
        }

        // Validasi form input (sama seperti di guru)
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
            'a' => $this->request->getPost('a') ?: 1.000,
            'c' => $this->request->getPost('c') ?: 0.000,
            'pembahasan' => $this->request->getPost('pembahasan')
        ];

        // Handle foto upload/delete (sama seperti di guru)
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
            session()->setFlashdata('success', 'Soal berhasil diupdate!');
            return redirect()->back();
        } catch (\Exception $e) {
            log_message('error', 'Error saat mengupdate soal bank ujian: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui soal: ' . $e->getMessage());
        }
    }

    // Hapus soal dari bank ujian — hapus juga file foto jika ada, lalu hapus link di ujian_soal_cat
    public function hapusSoalBankUjian($soalId)
    {
        // Admin bisa hapus soal bank ujian siapa saja
        $soal = $this->soalUjianModel->find($soalId);
        if (!$soal || !$soal['is_bank_soal']) {
            return redirect()->back()->with('error', 'Soal tidak ditemukan');
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
            session()->setFlashdata('success', 'Soal berhasil dihapus!');
            return redirect()->back();
        } catch (\Exception $e) {
            log_message('error', 'Error saat menghapus soal bank ujian: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus soal.');
        }
    }

    public function hapusBankUjian($bankUjianId)
    {
        $db = \Config\Database::connect();

        try {
            $db->transStart();

            // Cek apakah ada soal di bank ujian ini
            $jumlahSoal = $db->table('soal_ujian')
                ->where('bank_ujian_id', $bankUjianId)
                ->where('is_bank_soal', true)
                ->countAllResults();

            if ($jumlahSoal > 0) {
                session()->setFlashdata('error', "Tidak dapat menghapus bank soal karena masih memiliki {$jumlahSoal} soal. Hapus soal terlebih dahulu.");
                return redirect()->back();
            }

            // Hapus bank ujian
            $db->table('bank_ujian')->where('bank_ujian_id', $bankUjianId)->delete();

            $db->transComplete();

            if ($db->transStatus() === FALSE) {
                throw new \Exception('Transaction failed');
            }

            session()->setFlashdata('success', 'Bank ujian berhasil dihapus!');
        } catch (\Exception $e) {
            log_message('error', 'Error deleting bank ujian: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat menghapus bank soal.');
        }

        return redirect()->to(base_url('admin/bank-soal'));
    }

    // API Methods untuk AJAX (bisa digunakan untuk modal atau select dinamis)
    public function getKategoriTersedia()
    {
        try {
            // Mengambil daftar kategori unik LANGSUNG dari tabel bank_ujian
            $kategoriData = $this->db->table('bank_ujian')
                ->select('kategori')
                ->distinct()
                ->orderBy('kategori', 'ASC')
                ->get()
                ->getResultArray();

            // Mengubah array of array menjadi array of string
            // Contoh: dari [['kategori' => 'UMUM'], ['kategori' => 'OLIMPIADE']] menjadi ['UMUM', 'OLIMPIADE']
            $kategoriList = array_column($kategoriData, 'kategori');

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $kategoriList
            ]);
        } catch (\Exception $e) {
            log_message('error', '[Admin::getKategoriTersedia] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Gagal memuat kategori.']);
        }
    }

    public function getJenisUjianByKategori()
    {
        $kategori = $this->request->getGet('kategori');
        if (!$kategori) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Kategori harus dipilih']);
        }

        try {
            $query = $this->db->table('bank_ujian')
                ->select('bank_ujian.jenis_ujian_id, jenis_ujian.nama_jenis, COUNT(*) as jumlah_bank')
                ->join('jenis_ujian', 'jenis_ujian.jenis_ujian_id = bank_ujian.jenis_ujian_id')
                ->where('bank_ujian.kategori', $kategori)
                ->groupBy('bank_ujian.jenis_ujian_id, jenis_ujian.nama_jenis')
                ->orderBy('jenis_ujian.nama_jenis', 'ASC');

            $jenisUjian = $query->get()->getResultArray();

            return $this->response->setJSON(['status' => 'success', 'data' => $jenisUjian]);
        } catch (\Exception $e) {
            log_message('error', '[Admin::getJenisUjianByKategori] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Gagal memuat mata pelajaran.']);
        }
    }

    public function getBankUjianByKategoriJenis()
    {
        $kategori = $this->request->getGet('kategori');
        $jenisUjianId = $this->request->getGet('jenis_ujian_id');

        if (!$kategori || !$jenisUjianId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Kategori dan Mata Pelajaran harus dipilih']);
        }

        try {
            $bankUjian = $this->db->table('bank_ujian')
                ->select('bank_ujian.*, users.username as creator_name, (SELECT COUNT(*) FROM soal_ujian WHERE soal_ujian.bank_ujian_id = bank_ujian.bank_ujian_id AND soal_ujian.is_bank_soal = 1) as jumlah_soal')
                ->join('users', 'users.user_id = bank_ujian.created_by')
                ->where('bank_ujian.kategori', $kategori)
                ->where('bank_ujian.jenis_ujian_id', $jenisUjianId)
                ->orderBy('bank_ujian.created_at', 'DESC')
                ->get()->getResultArray();

            return $this->response->setJSON(['status' => 'success', 'data' => $bankUjian]);
        } catch (\Exception $e) {
            log_message('error', '[Admin::getBankUjianByKategoriJenis] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Gagal memuat bank soal.']);
        }
    }


    public function getSoalBankUjian()
    {
        $bankUjianId = $this->request->getGet('bank_ujian_id');

        if (!$bankUjianId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Bank ujian harus dipilih']);
        }

        try {
            $bankUjian = $this->db->table('bank_ujian')->where('bank_ujian_id', $bankUjianId)->get()->getRowArray();
            if (!$bankUjian) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Bank ujian tidak ditemukan']);
            }

            $soalList = $this->soalUjianModel
                ->select('soal_ujian.*')
                ->where('bank_ujian_id', $bankUjianId)
                ->where('is_bank_soal', true)
                ->orderBy('created_at', 'DESC')
                ->findAll();

            return $this->response->setJSON(['status' => 'success', 'data' => $soalList, 'bank_ujian' => $bankUjian]);
        } catch (\Exception $e) {
            log_message('error', '[Admin::getSoalBankUjian] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Gagal memuat soal.']);
        }
    }

    public function importSoalDariBank()
    {
        // 1. Ambil data dari form POST
        $ujianId = $this->request->getPost('ujian_id');
        $soalIds = $this->request->getPost('soal_ids'); // Ini adalah array ID soal yang dicentang

        // 2. Validasi dasar
        if (!$ujianId || empty($soalIds) || !is_array($soalIds)) {
            return redirect()->back()->with('error', 'Data tidak lengkap. Pilih minimal satu soal untuk diimpor.');
        }

        // 3. Siapkan variabel
        $userId = session()->get('user_id'); // ID Admin yang sedang login
        $berhasilImport = 0;
        $gagalImport = 0;
        $errorMessages = [];

        // 4. Looping untuk setiap soal yang dipilih
        foreach ($soalIds as $soalId) {
            // Ambil data asli soal dari bank
            $soalBank = $this->soalUjianModel->find($soalId);

            // Pastikan soal ada dan merupakan soal dari bank soal
            if ($soalBank && $soalBank['is_bank_soal']) {

                // ADMIN TIDAK PERLU VALIDASI HAK AKSES, LANGSUNG PROSES

                // Siapkan data soal baru dengan menyalin data dari bank soal
                $dataSoalBaru = $soalBank;

                // Hapus primary key lama agar bisa di-insert sebagai record baru
                unset($dataSoalBaru['soal_id']);

                // Atur ulang beberapa field penting
                $dataSoalBaru['ujian_id'] = $ujianId;          // Set ID ujian tujuan
                $dataSoalBaru['bank_ujian_id'] = null;       // Hapus referensi ke bank ujian
                $dataSoalBaru['is_bank_soal'] = false;       // Tandai sebagai soal ujian biasa
                $dataSoalBaru['created_by'] = $userId;       // Creator adalah admin yang mengimpor
                $dataSoalBaru['created_at'] = date('Y-m-d H:i:s');
                $dataSoalBaru['updated_at'] = date('Y-m-d H:i:s');

                try {
                    $this->soalUjianModel->insert($dataSoalBaru);
                    $berhasilImport++;
                } catch (\Exception $e) {
                    // Tangani jika ada error saat insert (misal, kode soal duplikat)
                    log_message('error', 'Admin gagal import soal: ' . $e->getMessage());
                    $gagalImport++;
                    // Simpan pesan error jika ada, untuk ditampilkan
                    if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                        $errorMessages[] = "Soal dengan kode '{$dataSoalBaru['kode_soal']}' sudah ada di ujian ini.";
                    }
                }
            } else {
                // Jika soal tidak ditemukan atau bukan soal dari bank, hitung sebagai gagal
                $gagalImport++;
            }
        }

        // 5. Siapkan notifikasi dan redirect
        $message = "Proses import selesai: {$berhasilImport} soal berhasil diimpor.";
        if ($gagalImport > 0) {
            $message .= " {$gagalImport} soal gagal diimpor.";
            if (!empty($errorMessages)) {
                $message .= " Alasan: " . implode(', ', $errorMessages);
            }
            session()->setFlashdata('warning', $message);
        } else {
            session()->setFlashdata('success', $message);
        }

        // Arahkan kembali ke halaman kelola soal untuk Admin
        return redirect()->to('admin/soal/' . $ujianId);
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
        return redirect()->to('admin/soal/' . $ujianId);
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
        return redirect()->to('admin/soal/' . $ujianId);
    }

    // ===== KELOLA Mata Pelajaran =====

    public function daftarJenisUjian()
    {
        $db = \Config\Database::connect();

        // Query untuk mengambil semua Mata Pelajaran dengan informasi lengkap
        $data['jenis_ujian'] = $db->table('jenis_ujian ju')
            ->select('ju.*, k.nama_kelas, k.tahun_ajaran, s.nama_sekolah, s.sekolah_id,
                 g.nama_lengkap as guru_pembuat, u.username as user_pembuat,
                 COUNT(DISTINCT uj.id_ujian) as total_ujian')
            ->join('kelas k', 'k.kelas_id = ju.kelas_id', 'left')
            ->join('sekolah s', 's.sekolah_id = k.sekolah_id', 'left')
            ->join('users u', 'u.user_id = ju.created_by', 'left')
            ->join('guru g', 'g.user_id = ju.created_by', 'left')
            ->join('ujian uj', 'uj.jenis_ujian_id = ju.jenis_ujian_id', 'left')
            ->groupBy('ju.jenis_ujian_id, ju.nama_jenis, ju.deskripsi, ju.kelas_id, ju.created_by, ju.created_at, ju.updated_at,
                  k.nama_kelas, k.tahun_ajaran, s.nama_sekolah, s.sekolah_id, g.nama_lengkap, u.username')
            ->orderBy('ju.created_at', 'DESC')
            ->get()
            ->getResultArray();

        // Ambil semua sekolah untuk filter/dropdown
        $sekolahModel = new \App\Models\SekolahModel();
        $data['sekolah'] = $sekolahModel->findAll();

        // Ambil semua kelas untuk dropdown tambah/edit
        $data['kelas'] = $db->table('kelas k')
            ->select('k.kelas_id, k.nama_kelas, k.tahun_ajaran, s.nama_sekolah, s.sekolah_id')
            ->join('sekolah s', 's.sekolah_id = k.sekolah_id')
            ->orderBy('s.nama_sekolah', 'ASC')
            ->orderBy('k.nama_kelas', 'ASC')
            ->get()
            ->getResultArray();

        return view('admin/jenis_ujian/daftar', $data);
    }

    public function jenisUjian()
    {
        $db = \Config\Database::connect();

        // Admin bisa melihat semua Mata Pelajaran dari semua guru
        $data['jenis_ujian'] = $db->table('jenis_ujian ju')
            ->select('ju.*, k.nama_kelas, k.tahun_ajaran, s.nama_sekolah, u.username as creator_name, g.nama_lengkap as guru_nama')
            ->join('kelas k', 'k.kelas_id = ju.kelas_id', 'left')
            ->join('sekolah s', 's.sekolah_id = k.sekolah_id', 'left')
            ->join('users u', 'u.user_id = ju.created_by', 'left')
            ->join('guru g', 'g.user_id = u.user_id', 'left')
            ->orderBy('ju.created_at', 'DESC')
            ->get()
            ->getResultArray();

        // Ambil semua kelas untuk dropdown
        $data['semua_kelas'] = $db->table('kelas k')
            ->select('k.kelas_id, k.nama_kelas, k.tahun_ajaran, s.nama_sekolah')
            ->join('sekolah s', 's.sekolah_id = k.sekolah_id')
            ->orderBy('s.nama_sekolah', 'ASC')
            ->orderBy('k.nama_kelas', 'ASC')
            ->get()
            ->getResultArray();

        return view('admin/jenis_ujian', $data);
    }

    public function tambahJenisUjian()
    {
        $kelasId = $this->normalizeNullableId($this->request->getPost('kelas_id'));
        $userId = session()->get('user_id');

        // Validasi input
        $rules = [
            'nama_jenis' => 'required|min_length[3]|max_length[100]',
            'deskripsi' => 'required|min_length[10]',
            'kelas_id' => 'permit_empty|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // Validasi kelas exists (skip jika umum)
        if (!empty($kelasId)) {
            $kelas = $this->kelasModel->find($kelasId);
            if (!$kelas) {
                session()->setFlashdata('error', 'Kelas tidak ditemukan.');
                return redirect()->back()->withInput();
            }
        }

        try {
            $data = [
                'nama_jenis' => $this->request->getPost('nama_jenis'),
                'deskripsi' => $this->request->getPost('deskripsi'),
                'kelas_id' => $kelasId,
                'created_by' => $userId
            ];

            $this->jenisUjianModel->insert($data);
            session()->setFlashdata('success', 'Mata Pelajaran berhasil ditambahkan!');
            return redirect()->to(base_url('admin/jenis-ujian'));
        } catch (\Exception $e) {
            log_message('error', 'Error adding Mata Pelajaran: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat menambah Mata Pelajaran: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }


    public function editJenisUjian($jenisUjianId)
    {
        $userId = session()->get('user_id');

        // Validasi input
        $rules = [
            'nama_jenis' => 'required|min_length[3]|max_length[100]',
            'deskripsi' => 'required|min_length[10]',
            'kelas_id' => 'permit_empty|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // Cek Mata Pelajaran exists
        $jenisUjian = $this->jenisUjianModel->find($jenisUjianId);
        if (!$jenisUjian) {
            session()->setFlashdata('error', 'Mata Pelajaran tidak ditemukan.');
            return redirect()->to(base_url('admin/jenis-ujian'));
        }

        $kelasId = $this->normalizeNullableId($this->request->getPost('kelas_id'));

        // Validasi kelas exists (skip jika umum)
        if (!empty($kelasId)) {
            $kelas = $this->kelasModel->find($kelasId);
            if (!$kelas) {
                session()->setFlashdata('error', 'Kelas tidak ditemukan.');
                return redirect()->back()->withInput();
            }
        }

        try {
            $data = [
                'nama_jenis' => $this->request->getPost('nama_jenis'),
                'deskripsi' => $this->request->getPost('deskripsi'),
                'kelas_id' => $kelasId
            ];

            $this->jenisUjianModel->update($jenisUjianId, $data);
            session()->setFlashdata('success', 'Mata Pelajaran berhasil diperbarui!');
            return redirect()->to(base_url('admin/jenis-ujian'));
        } catch (\Exception $e) {
            log_message('error', 'Error updating Mata Pelajaran: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat memperbarui Mata Pelajaran: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function hapusJenisUjian($jenisUjianId)
    {
        try {
            // Cek Mata Pelajaran exists
            $jenisUjian = $this->jenisUjianModel->find($jenisUjianId);
            if (!$jenisUjian) {
                session()->setFlashdata('error', 'Mata Pelajaran tidak ditemukan.');
                return redirect()->to(base_url('admin/jenis-ujian'));
            }

            // Cek apakah ada ujian yang menggunakan Mata Pelajaran ini
            $db = \Config\Database::connect();
            $ujianTerkait = $db->table('ujian')
                ->where('jenis_ujian_id', $jenisUjianId)
                ->countAllResults();

            if ($ujianTerkait > 0) {
                session()->setFlashdata('error', "Tidak dapat menghapus Mata Pelajaran ini karena masih ada {$ujianTerkait} ujian yang menggunakan Mata Pelajaran ini. Harap hapus ujian terkait terlebih dahulu.");
                return redirect()->to(base_url('admin/jenis-ujian'));
            }

            $this->jenisUjianModel->delete($jenisUjianId);
            session()->setFlashdata('success', 'Mata Pelajaran berhasil dihapus!');
        } catch (\Exception $e) {
            log_message('error', 'Error deleting Mata Pelajaran: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat menghapus Mata Pelajaran.');
        }

        return redirect()->to(base_url('admin/jenis-ujian'));
    }

    // API method untuk mendapatkan jenis ujian berdasarkan sekolah/kelas (untuk AJAX)
    public function getJenisUjian()
    {
        $db = \Config\Database::connect();
        $sekolahId = (int)($this->request->getGet('sekolah_id') ?? 0);
        $kelasId   = (int)($this->request->getGet('kelas_id')   ?? 0);

        $builder = $db->table('jenis_ujian ju')
            ->select('ju.jenis_ujian_id, ju.nama_jenis, ju.kelas_id, k.nama_kelas, k.tahun_ajaran')
            ->join('kelas k', 'k.kelas_id = ju.kelas_id', 'left')
            ->orderBy('ju.nama_jenis', 'ASC');

        if ($kelasId > 0) {
            $builder->groupStart()
                ->where('ju.kelas_id', $kelasId)
                ->orWhere('ju.kelas_id IS NULL')
                ->groupEnd();
        } elseif ($sekolahId > 0) {
            $builder->groupStart()
                ->where('k.sekolah_id', $sekolahId)
                ->orWhere('ju.kelas_id IS NULL')
                ->groupEnd();
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $builder->get()->getResultArray()
        ]);
    }

    // API method untuk mendapatkan kelas berdasarkan sekolah (untuk AJAX)
    public function getKelasBySekolah($sekolahId)
    {
        $db = \Config\Database::connect();

        $kelas = $db->table('kelas')
            ->select('kelas_id, nama_kelas, tahun_ajaran')
            ->where('sekolah_id', $sekolahId)
            ->orderBy('tahun_ajaran', 'DESC')
            ->orderBy('nama_kelas', 'ASC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $kelas
        ]);
    }

    private function normalizeNullableId($value)
    {
        return ($value === null || $value === '' || $value === '0' || $value === 0) ? null : (int) $value;
    }

    private function validateSiswaIdsForKelas(int $kelasId, array $siswaIds): bool
    {
        if ($kelasId <= 0 || empty($siswaIds)) {
            return false;
        }

        $validCount = $this->db->table('siswa')
            ->where('kelas_id', $kelasId)
            ->whereIn('siswa_id', $siswaIds)
            ->countAllResults();

        return $validCount === count($siswaIds);
    }

    private function validateSiswaIds(array $siswaIds): bool
    {
        if (empty($siswaIds)) {
            return false;
        }

        $validCount = $this->db->table('siswa')
            ->whereIn('siswa_id', $siswaIds)
            ->countAllResults();

        return $validCount === count($siswaIds);
    }

    private function validateJadwalKelasAgainstUjian(int $ujianId, ?int $kelasId): bool
    {
        if ($ujianId <= 0) {
            return false;
        }

        $ujian = $this->ujianModel->find($ujianId);
        if (!$ujian) {
            return false;
        }

        if (empty($kelasId)) {
            return empty($ujian['sekolah_id']) && empty($ujian['kelas_id']);
        }

        $kelas = $this->kelasModel->find($kelasId);
        if (!$kelas) {
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

    /**
     * Upload image untuk Summernote
     */
    public function uploadSummernoteImage()
    {
        // Cek login
        $userRole = session()->get('role');
        if (!session()->get('user_id') || !in_array($userRole, ['admin', 'guru'])) {
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

    // ---------- VARIABEL ----------

    public function variabel()
    {
        $data['variabel'] = $this->variabelModel->getWithCounts();
        return view('admin/variabel', $data);
    }

    public function tambahVariabel()
    {
        $rules = [
            'nama_variabel' => 'required|min_length[3]|max_length[100]',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        try {
            $this->variabelModel->insert([
                'nama_variabel' => $this->request->getPost('nama_variabel'),
                'deskripsi'     => $this->request->getPost('deskripsi'),
            ]);
            session()->setFlashdata('success', 'Variabel berhasil ditambahkan!');
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Gagal: ' . $e->getMessage());
        }
        return redirect()->to('admin/variabel');
    }

    public function editVariabel($id)
    {
        $rules = [
            'nama_variabel' => 'required|min_length[3]|max_length[100]',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        if (!$this->variabelModel->find($id)) {
            session()->setFlashdata('error', 'Variabel tidak ditemukan.');
            return redirect()->to('admin/variabel');
        }
        try {
            $this->variabelModel->update($id, [
                'nama_variabel' => $this->request->getPost('nama_variabel'),
                'deskripsi'     => $this->request->getPost('deskripsi'),
            ]);
            session()->setFlashdata('success', 'Variabel berhasil diperbarui!');
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Gagal: ' . $e->getMessage());
        }
        return redirect()->to('admin/variabel');
    }

    public function hapusVariabel($id)
    {
        if (!$this->variabelModel->find($id)) {
            session()->setFlashdata('error', 'Variabel tidak ditemukan.');
            return redirect()->to('admin/variabel');
        }
        try {
            $this->variabelModel->delete($id);
            session()->setFlashdata('success', 'Variabel berhasil dihapus!');
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Gagal menghapus. Hapus dulu indikator dan soal terkait.');
        }
        return redirect()->to('admin/variabel');
    }

    // ---------- INDIKATOR ----------

    public function indikator()
    {
        $data['indikator'] = $this->indikatorModel->getAllWithVariabel();
        $data['variabel']  = $this->variabelModel->orderBy('nama_variabel', 'ASC')->findAll();
        return view('admin/indikator', $data);
    }

    public function tambahIndikator()
    {
        $rules = [
            'variabel_id'     => 'required|numeric',
            'nama_indikator'  => 'required|min_length[3]|max_length[200]',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        try {
            $this->indikatorModel->insert([
                'variabel_id'    => $this->request->getPost('variabel_id'),
                'nama_indikator' => $this->request->getPost('nama_indikator'),
                'deskripsi'      => $this->request->getPost('deskripsi'),
            ]);
            session()->setFlashdata('success', 'Indikator berhasil ditambahkan!');
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Gagal: ' . $e->getMessage());
        }
        return redirect()->to('admin/indikator');
    }

    public function editIndikator($id)
    {
        $rules = [
            'variabel_id'     => 'required|numeric',
            'nama_indikator'  => 'required|min_length[3]|max_length[200]',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        if (!$this->indikatorModel->find($id)) {
            session()->setFlashdata('error', 'Indikator tidak ditemukan.');
            return redirect()->to('admin/indikator');
        }
        try {
            $this->indikatorModel->update($id, [
                'variabel_id'    => $this->request->getPost('variabel_id'),
                'nama_indikator' => $this->request->getPost('nama_indikator'),
                'deskripsi'      => $this->request->getPost('deskripsi'),
            ]);
            session()->setFlashdata('success', 'Indikator berhasil diperbarui!');
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Gagal: ' . $e->getMessage());
        }
        return redirect()->to('admin/indikator');
    }

    public function hapusIndikator($id)
    {
        if (!$this->indikatorModel->find($id)) {
            session()->setFlashdata('error', 'Indikator tidak ditemukan.');
            return redirect()->to('admin/indikator');
        }
        try {
            $this->indikatorModel->delete($id);
            session()->setFlashdata('success', 'Indikator berhasil dihapus!');
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Gagal menghapus. Hapus dulu soal yang terkait.');
        }
        return redirect()->to('admin/indikator');
    }

    // ---------- MATERI ----------

    public function materi()
    {
        $data['materi'] = $this->materiModel->getWithCount();
        return view('admin/materi', $data);
    }

    public function tambahMateri()
    {
        $rules = [
            'nama_materi' => 'required|min_length[2]|max_length[200]',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        try {
            $this->materiModel->insert([
                'nama_materi' => $this->request->getPost('nama_materi'),
                'deskripsi'   => $this->request->getPost('deskripsi'),
            ]);
            session()->setFlashdata('success', 'Materi berhasil ditambahkan!');
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Gagal: ' . $e->getMessage());
        }
        return redirect()->to('admin/materi');
    }

    public function editMateri($id)
    {
        $rules = [
            'nama_materi' => 'required|min_length[2]|max_length[200]',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        if (!$this->materiModel->find($id)) {
            session()->setFlashdata('error', 'Materi tidak ditemukan.');
            return redirect()->to('admin/materi');
        }
        try {
            $this->materiModel->update($id, [
                'nama_materi' => $this->request->getPost('nama_materi'),
                'deskripsi'   => $this->request->getPost('deskripsi'),
            ]);
            session()->setFlashdata('success', 'Materi berhasil diperbarui!');
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Gagal: ' . $e->getMessage());
        }
        return redirect()->to('admin/materi');
    }

    public function hapusMateri($id)
    {
        if (!$this->materiModel->find($id)) {
            session()->setFlashdata('error', 'Materi tidak ditemukan.');
            return redirect()->to('admin/materi');
        }
        try {
            $this->materiModel->delete($id);
            session()->setFlashdata('success', 'Materi berhasil dihapus!');
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Gagal menghapus. Hapus dulu soal yang terkait.');
        }
        return redirect()->to('admin/materi');
    }

    // ---------- API: Indikator by Variabel (untuk AJAX cascade) ----------

    public function getIndikatorByVariabel($variabelId)
    {
        $indikator = $this->indikatorModel->where('variabel_id', $variabelId)
            ->orderBy('nama_indikator', 'ASC')
            ->findAll();
        return $this->response->setJSON($indikator);
    }

    public function tambahVariabelInline()
    {
        $rules = [
            'nama_variabel' => 'required|min_length[3]|max_length[200]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => implode(' ', $this->validator->getErrors()),
            ]);
        }

        try {
            $variabelId = $this->variabelModel->insert([
                'nama_variabel' => trim((string) $this->request->getPost('nama_variabel')),
                'deskripsi' => $this->request->getPost('deskripsi'),
            ], true);

            $variabel = $this->variabelModel->find($variabelId);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Variabel berhasil ditambahkan.',
                'item' => $variabel,
            ]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan variabel.',
            ]);
        }
    }

    public function tambahIndikatorInline()
    {
        $rules = [
            'variabel_id' => 'required|numeric',
            'nama_indikator' => 'required|min_length[3]|max_length[200]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => implode(' ', $this->validator->getErrors()),
            ]);
        }

        try {
            $indikatorId = $this->indikatorModel->insert([
                'variabel_id' => $this->request->getPost('variabel_id'),
                'nama_indikator' => trim((string) $this->request->getPost('nama_indikator')),
                'deskripsi' => $this->request->getPost('deskripsi'),
            ], true);

            $indikator = $this->indikatorModel->find($indikatorId);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Indikator berhasil ditambahkan.',
                'item' => $indikator,
            ]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan indikator.',
            ]);
        }
    }

    public function tambahMateriInline()
    {
        $rules = [
            'nama_materi' => 'required|min_length[2]|max_length[200]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => implode(' ', $this->validator->getErrors()),
            ]);
        }

        try {
            $materiId = $this->materiModel->insert([
                'nama_materi' => trim((string) $this->request->getPost('nama_materi')),
                'deskripsi' => $this->request->getPost('deskripsi'),
            ], true);

            $materi = $this->materiModel->find($materiId);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Materi berhasil ditambahkan.',
                'item' => $materi,
            ]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan materi.',
            ]);
        }
    }

    // =============================================
    //  MULTI-BANK & GENERATE PAKET
    // =============================================

    /**
     * Halaman assign bank soal ke ujian + daftar paket
     */
    public function assignBank($ujianId)
    {
        // Redirect ke halaman kelola soal (tab Bank & Paket)
        return redirect()->to('admin/soal/' . $ujianId);
    }

    /**
     * Sync bank assignment (POST)
     */
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

    /**
     * Halaman generate paket (GET)
     */
    public function generatePaket($ujianId)
    {
        // Redirect ke halaman kelola soal (tab Bank & Paket)
        return redirect()->to('admin/soal/' . $ujianId);
    }

    /**
     * Proses generate paket (POST)
     */
    public function prosesGeneratePaket($ujianId)
    {
        $jumlahPaket = (int) $this->request->getPost('jumlah_paket') ?: 3;
        $soalPerPaket = (int) $this->request->getPost('soal_per_paket') ?: 25;
        $attemptCount = $this->db->table('attempt_ujian au')
            ->join('paket_ujian_cbt pu', 'pu.paket_id = au.paket_id')
            ->where('pu.ujian_id', $ujianId)
            ->countAllResults();

        if ($attemptCount > 0) {
            return redirect()->back()->with('error', 'Paket tidak dapat diacak ulang karena sudah pernah dipakai siswa. Membuat ulang paket saat data attempt sudah ada berisiko merusak konsistensi hasil ujian.');
        }

        // Ambil bank yang di-assign
        $banks = $this->ujianBankModel->getBanksByUjian($ujianId);
        if (empty($banks)) {
            return redirect()->back()->with('error', 'Belum ada bank yang di-assign. Assign bank dulu.');
        }

        if (count($banks) !== 1) {
            return redirect()->back()->with('error', 'CBT hanya boleh memakai satu bank soal sebagai sumber tunggal.');
        }

        $bankId = $banks[0]['bank_ujian_id'];
        $totalSoal = $this->soalUjianModel->where(['bank_ujian_id' => $bankId, 'is_bank_soal' => 1])->countAllResults();

        if ($soalPerPaket > $totalSoal) {
            return redirect()->back()->with('error', "Soal per paket tidak boleh melebihi stok bank (Y ≤ N). Tersedia: {$totalSoal}, diminta: {$soalPerPaket}.");
        }

        try {
            $draft = $this->buildDraftPaket($ujianId, $bankId, $jumlahPaket, $soalPerPaket);
            $this->setDraftPaket($ujianId, $draft);
            session()->setFlashdata('success', "{$jumlahPaket} draft paket berhasil dibuat. Review dulu, lalu klik Simpan Paket untuk mengunci paket ke database.");
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Gagal generate draft: ' . $e->getMessage());
        }

        return redirect()->to('admin/soal/' . $ujianId . '?step=2&panel=paket');
    }

    public function simpanDraftPaket($ujianId)
    {
        $draft = $this->getDraftPaket($ujianId);
        if (empty($draft['packages'])) {
            return redirect()->back()->with('error', 'Draft paket belum tersedia.');
        }

        $attemptCount = $this->db->table('attempt_ujian au')
            ->join('paket_ujian_cbt pu', 'pu.paket_id = au.paket_id')
            ->where('pu.ujian_id', $ujianId)
            ->countAllResults();
        if ($attemptCount > 0) {
            return redirect()->back()->with('error', 'Draft paket tidak dapat disimpan karena ujian sudah pernah dikerjakan siswa.');
        }

        $this->paketUjianModel->db->transStart();
        try {
            $this->paketUjianModel->deleteByUjian($ujianId);
            foreach ($draft['packages'] as $package) {
                $this->paketUjianModel->db->table('paket_ujian_cbt')->insert([
                    'ujian_id' => $ujianId,
                    'nama_paket' => $package['nama_paket'],
                    'nomor_paket' => $package['nomor_paket'],
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
                $paketId = $this->paketUjianModel->db->insertID();
                $urut = 1;
                foreach ($package['soal_ids'] as $soalId) {
                    $result = $this->paketUjianModel->db->table('paket_ujian_item_cbt')->insert([
                        'paket_id' => $paketId,
                        'soal_id' => $soalId,
                        'nomor_urut' => $urut++,
                    ]);
                    if (!$result) {
                        throw new \Exception('Gagal menyimpan item paket.');
                    }
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

        return redirect()->to('admin/soal/' . $ujianId . '?step=3&panel=paket');
    }

    public function batalDraftPaket($ujianId)
    {
        $this->clearDraftPaket($ujianId);
        session()->setFlashdata('success', 'Draft paket dibatalkan.');
        return redirect()->to('admin/soal/' . $ujianId . '?step=2&panel=generate');
    }

    /**
     * Hapus satu paket
     */
    public function hapusPaket($ujianId, $paketId)
    {
        $attemptCount = $this->db->table('attempt_ujian au')
            ->join('paket_ujian_cbt pu', 'pu.paket_id = au.paket_id')
            ->where('pu.ujian_id', $ujianId)
            ->countAllResults();
        if ($attemptCount > 0) {
            session()->setFlashdata('error', 'Paket tidak dapat dihapus karena sudah pernah dipakai siswa.');
            return redirect()->back();
        }

        $this->db->table('paket_ujian_cbt')->where('paket_id', $paketId)->delete();
        session()->setFlashdata('success', 'Paket berhasil dihapus.');
        return redirect()->back();
    }

    /**
     * Hapus semua paket
     */
    public function hapusSemuaPaket($ujianId)
    {
        $attemptCount = $this->db->table('attempt_ujian au')
            ->join('paket_ujian_cbt pu', 'pu.paket_id = au.paket_id')
            ->where('pu.ujian_id', $ujianId)
            ->countAllResults();
        if ($attemptCount > 0) {
            session()->setFlashdata('error', 'Semua paket tidak dapat dihapus karena sudah pernah dipakai siswa.');
            return redirect()->back();
        }

        $this->paketUjianModel->deleteByUjian($ujianId);
        $this->clearDraftPaket($ujianId);
        $this->ujianBankModel->where('ujian_id', $ujianId)->delete();
        session()->setFlashdata('success', 'Semua paket berhasil dihapus. Sumber bank soal juga sudah direset.');
        return redirect()->to('admin/soal/' . $ujianId . '?step=1');
    }

    /**
     * API: Ambil soal dalam suatu paket (JSON)
     */
    public function getSoalByPaket($paketId)
    {
        $soal = $this->paketUjianModel->getSoalByPaket($paketId);
        return $this->response->setJSON($soal);
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

    private function getDraftPaketKey($ujianId)
    {
        return 'admin_draft_paket_' . $ujianId;
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
            $jawabanTable = empty($attempt['paket_id']) ? 'attempt_jawaban_cat' : 'attempt_jawaban_cbt';
            $rows = $this->db->table($jawabanTable . ' aj')
                ->select('
                    aj.*,
                    COALESCE(ats.pertanyaan, s.pertanyaan) as pertanyaan,
                    COALESCE(ats.kode_soal, s.kode_soal) as kode_soal,
                    COALESCE(ats.pilihan_a, s.pilihan_a) as pilihan_a,
                    COALESCE(ats.pilihan_b, s.pilihan_b) as pilihan_b,
                    COALESCE(ats.pilihan_c, s.pilihan_c) as pilihan_c,
                    COALESCE(ats.pilihan_d, s.pilihan_d) as pilihan_d,
                    COALESCE(ats.pilihan_e, s.pilihan_e) as pilihan_e,
                    COALESCE(ats.jawaban_benar, s.jawaban_benar) as jawaban_benar,
                    COALESCE(ats.tingkat_kesulitan, s.tingkat_kesulitan) as tingkat_kesulitan,
                    COALESCE(ats.pembahasan, s.pembahasan) as pembahasan,
                    COALESCE(ats.media, s.media) as foto,
                    aac.p_residu,
                    aac.q_residu,
                    aac.z_score,
                    aac.kategori_soal,
                    aac.keterangan as keterangan_residu,
                    DATE_FORMAT(aj.waktu_menjawab, "%H:%i:%s") as waktu_menjawab_format
                ')
                ->join('attempt_soal_cbt ats', 'ats.attempt_id = aj.attempt_id AND ats.original_soal_id = aj.soal_id', 'left')
                ->join('attempt_analisis_cbt aac', 'aac.attempt_id = aj.attempt_id AND aac.soal_id = aj.soal_id', 'left')
                ->join('soal_ujian s', 's.soal_id = aj.soal_id', 'left')
                ->where('aj.attempt_id', $attempt['attempt_id'])
                ->orderBy('aj.nomor_tampil', 'ASC')
                ->orderBy('aj.waktu_menjawab', 'ASC')
                ->get()
                ->getResultArray();

            if (!empty($rows)) {
                return $rows;
            }
        }

        return $this->db->table('hasil_ujian')
            ->select('hasil_ujian.*, s.pertanyaan, s.kode_soal, s.pilihan_a, s.pilihan_b, s.pilihan_c, s.pilihan_d, s.pilihan_e,
                s.jawaban_benar, s.tingkat_kesulitan, s.media as foto, s.pembahasan,
                DATE_FORMAT(hasil_ujian.waktu_menjawab, "%H:%i:%s") as waktu_menjawab_format')
            ->join('soal_ujian s', 's.soal_id = hasil_ujian.soal_id', 'left')
            ->where('hasil_ujian.peserta_ujian_id', $pesertaUjianId)
            ->orderBy('hasil_ujian.waktu_menjawab', 'ASC')
            ->get()
            ->getResultArray();
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

    // ── Analisis Hasil Ujian (grafik) ────────────────────────────────────────
    public function analisisUjian()
    {
        $filters = $this->getAnalitikFiltersFromRequest();
        $filters['nilai_min']         = $this->request->getGet('nilai_min') !== null ? (int)$this->request->getGet('nilai_min') : null;
        $filters['nilai_max']         = $this->request->getGet('nilai_max') !== null ? (int)$this->request->getGet('nilai_max') : null;
        $filters['kategori']          = $this->request->getGet('kategori') ?: null;
        $filters['theta_min']         = $this->request->getGet('theta_min') !== null ? (float)$this->request->getGet('theta_min') : null;
        $filters['theta_max']         = $this->request->getGet('theta_max') !== null ? (float)$this->request->getGet('theta_max') : null;
        $filters['keterangan_residu'] = $this->request->getGet('keterangan_residu') ?: null;
        $filters['jenis_kelamin']     = $this->request->getGet('jenis_kelamin') ?: null;
        // Biodata tambahan: field_id => nilai
        $biodataFilters = $this->getBiodataFiltersFromRequest();
        $filters['biodata'] = $biodataFilters;

        $pesertaRows = $this->getAnalisisUjianPesertaRows($filters);

        // Post-filter: rentang nilai
        if ($filters['nilai_min'] !== null || $filters['nilai_max'] !== null) {
            $pesertaRows = array_filter($pesertaRows, function($r) use ($filters) {
                $s = $r['skor_akhir'];
                if ($filters['nilai_min'] !== null && $s < $filters['nilai_min']) return false;
                if ($filters['nilai_max'] !== null && $s > $filters['nilai_max']) return false;
                return true;
            });
            $pesertaRows = array_values($pesertaRows);
        }

        // Post-filter: kategori
        if (!empty($filters['kategori'])) {
            $pesertaRows = array_values(array_filter($pesertaRows, fn($r) =>
                $this->getKlasifikasiKognitif($r['skor_akhir'])['kategori'] === $filters['kategori']
            ));
        }

        // Post-filter: theta range
        if ($filters['theta_min'] !== null || $filters['theta_max'] !== null) {
            $pesertaRows = array_values(array_filter($pesertaRows, function($r) use ($filters) {
                $t = (float)($r['theta_akhir'] ?? 0);
                if ($filters['theta_min'] !== null && $t < $filters['theta_min']) return false;
                if ($filters['theta_max'] !== null && $t >= $filters['theta_max']) return false;
                return true;
            }));
        }

        // Post-filter: keterangan residu (Lucky Guess / Ceroboh / Normal)
        if (!empty($filters['keterangan_residu'])) {
            $db = \Config\Database::connect();
            $targetKet = $filters['keterangan_residu'];
            $pesertaIds = array_column($pesertaRows, 'peserta_ujian_id');
            if (!empty($pesertaIds)) {
                $matchIds = $db->table('attempt_analisis_cbt aac')
                    ->select('DISTINCT pu.peserta_ujian_id')
                    ->join('attempt_ujian au', 'au.attempt_id = aac.attempt_id')
                    ->join('peserta_ujian pu', 'pu.peserta_ujian_id = au.peserta_ujian_id')
                    ->where('aac.keterangan', $targetKet)
                    ->whereIn('pu.peserta_ujian_id', $pesertaIds)
                    ->get()->getResultArray();
                $matchSet = array_flip(array_column($matchIds, 'peserta_ujian_id'));
                $pesertaRows = array_values(array_filter($pesertaRows, fn($r) => isset($matchSet[$r['peserta_ujian_id']])));
            }
        }

        // Tampilkan grafik CBT jika ada data CBT — baik filter eksplisit maupun campuran
        $isCbt = !empty(array_filter($pesertaRows, fn($r) => ($r['tipe_ujian'] ?? '') === 'CBT'));

        // Biodata form fields (hanya tipe select)
        $formTemplateModel = new \App\Models\FormTemplateModel();
        $formFieldModel    = new \App\Models\FormFieldModel();
        $template    = $formTemplateModel->getSingle();
        $allFields   = $formFieldModel->getWithOptions((int) $template['template_id']);
        $selectFields = array_filter($allFields, fn($f) => $f['tipe'] === 'select');

        $data = [
            'pageRole'     => 'admin',
            'basePath'     => 'admin/hasil-ujian',
            'filters'      => $filters,
            'biodataFilters' => $biodataFilters,
            'selectFields' => array_values($selectFields),
            'filterOptions' => [
                'sekolah'    => $this->sekolahModel->orderBy('nama_sekolah', 'ASC')->findAll(),
                'kelas'      => $this->db->table('kelas k')->select('k.kelas_id,k.sekolah_id,k.nama_kelas,s.nama_sekolah')->join('sekolah s','s.sekolah_id=k.sekolah_id','left')->orderBy('s.nama_sekolah','ASC')->orderBy('k.nama_kelas','ASC')->get()->getResultArray(),
                'jenis_ujian' => [['value'=>'CAT','label'=>'CAT'],['value'=>'CBT','label'=>'CBT']],
                'ujian'      => $this->db->table('jadwal_ujian ju')->select('ju.jadwal_id,ju.kelas_id,u.tipe_ujian,u.nama_ujian,u.kode_ujian,k.sekolah_id,k.nama_kelas')->join('ujian u','u.id_ujian=ju.ujian_id')->join('kelas k','k.kelas_id=ju.kelas_id','left')->orderBy('ju.tanggal_mulai','DESC')->get()->getResultArray(),
                'variabel'   => $this->variabelModel->orderBy('nama_variabel','ASC')->findAll(),
                'indikator'  => $this->indikatorModel->select('indikator.*,variabel.nama_variabel')->join('variabel','variabel.variabel_id=indikator.variabel_id','left')->orderBy('variabel.nama_variabel','ASC')->findAll(),
                'materi'     => $this->materiModel->orderBy('nama_materi','ASC')->findAll(),
            ],
            'chartData'    => $this->buildAnalisisChartData($pesertaRows, $filters, $isCbt),
            'totalPeserta' => count($pesertaRows),
            'isCbt'        => $isCbt,
            'studentRows'  => $this->mergeResiduCounts($pesertaRows),
        ];

        return view('admin/hasil/analisis_ujian', $data);
    }

    // Tambahkan hitungan Lucky Guess & Ceroboh per peserta ke student rows
    private function mergeResiduCounts(array $rows): array
    {
        if (empty($rows)) return $rows;

        $pesertaIds = array_column($rows, 'peserta_ujian_id');

        // Batch query — hitung per keterangan per peserta dalam satu query
        $residuRaw = $this->db->table('attempt_analisis_cbt aac')
            ->select('pu.peserta_ujian_id, aac.keterangan, COUNT(*) as jumlah')
            ->join('attempt_ujian au', 'au.attempt_id = aac.attempt_id')
            ->join('peserta_ujian pu', 'pu.peserta_ujian_id = au.peserta_ujian_id')
            ->whereIn('pu.peserta_ujian_id', $pesertaIds)
            ->whereIn('aac.keterangan', ['Lucky Guess', 'Ceroboh'])
            ->groupBy('pu.peserta_ujian_id, aac.keterangan')
            ->get()->getResultArray();

        // Bentuk map [peserta_ujian_id => ['Lucky Guess' => n, 'Ceroboh' => n]]
        $residuMap = [];
        foreach ($residuRaw as $r) {
            $pid = (int)$r['peserta_ujian_id'];
            $residuMap[$pid][$r['keterangan']] = (int)$r['jumlah'];
        }

        foreach ($rows as &$row) {
            $pid = (int)$row['peserta_ujian_id'];
            $row['lucky_guess_count'] = $residuMap[$pid]['Lucky Guess'] ?? 0;
            $row['ceroboh_count']     = $residuMap[$pid]['Ceroboh']     ?? 0;
        }
        unset($row);

        return $rows;
    }

    private function getBiodataFiltersFromRequest(): array
    {
        $result = [];
        foreach ($this->request->getGet() as $key => $val) {
            if (str_starts_with($key, 'biodata_') && $val !== '') {
                $fieldId = (int) str_replace('biodata_', '', $key);
                if ($fieldId > 0) {
                    $result[$fieldId] = $val;
                }
            }
        }
        return $result;
    }

    private function getAnalisisUjianPesertaRows(array $filters): array
    {
        $builder = $this->db->table('peserta_ujian pu')
            ->select('pu.peserta_ujian_id, siswa.siswa_id, siswa.nama_lengkap, siswa.nomor_peserta, u.tipe_ujian, u.nama_ujian, au.nilai_akhir, au.theta_akhir, au.sem_akhir, au.waktu_mulai, au.waktu_selesai, sekolah.nama_sekolah, kelas.nama_kelas')
            ->join('jadwal_ujian ju', 'ju.jadwal_id = pu.jadwal_id')
            ->join('ujian u', 'u.id_ujian = ju.ujian_id')
            ->join('kelas', 'kelas.kelas_id = ju.kelas_id', 'left')
            ->join('sekolah', 'sekolah.sekolah_id = kelas.sekolah_id', 'left')
            ->join('siswa', 'siswa.siswa_id = pu.siswa_id', 'left')
            ->join($this->getAnalitikAttemptSubquery($filters), 'la.peserta_ujian_id = pu.peserta_ujian_id', 'inner', false)
            ->join('attempt_ujian au', 'au.peserta_ujian_id = la.peserta_ujian_id AND au.nomor_attempt = la.max_attempt', 'inner', false)
            ->where('pu.status', 'selesai');

        if (!empty($filters['variabel_id']) || !empty($filters['indikator_id']) || !empty($filters['materi_id'])) {
            $builder->join('attempt_soal_cbt ats', 'ats.attempt_id = au.attempt_id', 'inner')
                    ->join('soal_ujian sq', 'sq.soal_id = ats.original_soal_id', 'left');
        }

        $this->applyAnalitikScopeFilters($builder, $filters);

        // Filter jenis kelamin
        if (!empty($filters['jenis_kelamin'])) {
            $builder->where('siswa.jenis_kelamin', $filters['jenis_kelamin']);
        }

        // Filter biodata
        if (!empty($filters['biodata'])) {
            $builder->join('form_responses fr', 'fr.siswa_id = siswa.siswa_id', 'inner');
            foreach ($filters['biodata'] as $fieldId => $nilai) {
                $alias = 'frv_' . $fieldId;
                $builder->join("form_response_values {$alias}", "{$alias}.response_id = fr.response_id AND {$alias}.field_id = {$fieldId}", 'inner', false)
                        ->where("{$alias}.nilai", $nilai);
            }
        }

        $rows = $builder->groupBy('pu.peserta_ujian_id, siswa.siswa_id, siswa.nama_lengkap, siswa.nomor_peserta, u.tipe_ujian, u.nama_ujian, au.nilai_akhir, au.theta_akhir, au.sem_akhir, au.waktu_mulai, au.waktu_selesai, sekolah.nama_sekolah, kelas.nama_kelas')
            ->orderBy('siswa.nama_lengkap', 'ASC')
            ->get()->getResultArray();

        foreach ($rows as &$row) {
            $raw = (float)($row['nilai_akhir'] ?? 0);
            $row['skor_akhir'] = ($row['tipe_ujian'] ?? 'CAT') === 'CAT'
                ? $this->hitungKemampuanKognitif($raw)
                : round($raw, 2);
            $durasi = (strtotime($row['waktu_selesai'] ?? '') - strtotime($row['waktu_mulai'] ?? ''));
            $row['durasi_menit'] = $durasi > 0 ? round($durasi / 60, 1) : 0;
        }
        unset($row);

        return $rows;
    }

    private function buildAnalisisChartData(array $rows, array $filters, bool $isCbt): array
    {
        // 1. Distribusi nilai (histogram)
        $buckets = ['0–20'=>0,'21–40'=>0,'41–60'=>0,'61–80'=>0,'81–100'=>0];
        foreach ($rows as $r) {
            $s = $r['skor_akhir'];
            if ($s <= 20)      $buckets['0–20']++;
            elseif ($s <= 40)  $buckets['21–40']++;
            elseif ($s <= 60)  $buckets['41–60']++;
            elseif ($s <= 80)  $buckets['61–80']++;
            else               $buckets['81–100']++;
        }
        $chart1 = ['labels' => array_keys($buckets), 'data' => array_values($buckets)];

        // 2. Distribusi kategori
        $kat = ['Sangat Rendah'=>0,'Rendah'=>0,'Cukup'=>0,'Baik'=>0,'Sangat Baik'=>0];
        foreach ($rows as $r) {
            $k = $this->getKlasifikasiKognitif($r['skor_akhir'])['kategori'];
            if (isset($kat[$k])) $kat[$k]++;
        }
        $chart2 = ['labels' => array_keys($kat), 'data' => array_values($kat)];

        // 3. Rata-rata per kelompok (sekolah jika tidak ada filter kelas)
        $byKelas   = !empty($filters['kelas_id']);
        $groups    = [];
        $groupIds  = [];
        foreach ($rows as $r) {
            $key = $byKelas ? ($r['nama_kelas'] ?? '-') : ($r['nama_sekolah'] ?? '-');
            $id  = $byKelas ? ($r['kelas_id'] ?? null) : null; // sekolah_id tidak di-select, skip
            if (!isset($groups[$key])) { $groups[$key] = ['total'=>0,'count'=>0]; $groupIds[$key] = $id; }
            $groups[$key]['total'] += $r['skor_akhir'];
            $groups[$key]['count']++;
        }
        $chart3 = ['labels'=>[],'data'=>[],'counts'=>[],'ids'=>[],'groupBy'=>$byKelas?'kelas':'sekolah'];
        arsort($groups);
        foreach (array_slice($groups, 0, 15, true) as $label => $g) {
            $chart3['labels'][] = $label;
            $chart3['data'][]   = $g['count'] > 0 ? round($g['total']/$g['count'],2) : 0;
            $chart3['counts'][] = $g['count'];
            $chart3['ids'][]    = $groupIds[$label];
        }

        $chart4 = $chart5 = $chart6 = $chart7 = null;

        if ($isCbt) {
            // 4. Distribusi theta
            $thetaBuckets = ['< -2'=>0,'-2..-1'=>0,'-1..0'=>0,'0..1'=>0,'1..2'=>0,'> 2'=>0];
            foreach ($rows as $r) {
                $t = (float)($r['theta_akhir'] ?? 0);
                if ($t < -2)      $thetaBuckets['< -2']++;
                elseif ($t < -1)  $thetaBuckets['-2..-1']++;
                elseif ($t < 0)   $thetaBuckets['-1..0']++;
                elseif ($t < 1)   $thetaBuckets['0..1']++;
                elseif ($t <= 2)  $thetaBuckets['1..2']++;
                else              $thetaBuckets['> 2']++;
            }
            $chart4 = ['labels' => array_keys($thetaBuckets), 'data' => array_values($thetaBuckets)];

            // 5. Scatter: durasi vs nilai
            $scatter = [];
            foreach ($rows as $r) {
                if ($r['durasi_menit'] > 0) {
                    $scatter[] = ['x' => $r['durasi_menit'], 'y' => $r['skor_akhir']];
                }
            }
            $chart5 = $scatter;

            // 6. Residu: Lucky Guess & Ceroboh per rentang nilai
            $residuBuckets = ['0–20'=>['Lucky Guess'=>0,'Ceroboh'=>0,'Normal'=>0],'21–40'=>['Lucky Guess'=>0,'Ceroboh'=>0,'Normal'=>0],'41–60'=>['Lucky Guess'=>0,'Ceroboh'=>0,'Normal'=>0],'61–80'=>['Lucky Guess'=>0,'Ceroboh'=>0,'Normal'=>0],'81–100'=>['Lucky Guess'=>0,'Ceroboh'=>0,'Normal'=>0]];
            $attemptIds = array_column($rows, 'peserta_ujian_id');
            if (!empty($attemptIds)) {
                $residuRows = $this->db->table('attempt_analisis_cbt aac')
                    ->select('aac.keterangan, au.nilai_akhir')
                    ->join('attempt_ujian au', 'au.attempt_id = aac.attempt_id')
                    ->join('peserta_ujian pu', 'pu.peserta_ujian_id = au.peserta_ujian_id')
                    ->whereIn('pu.peserta_ujian_id', $attemptIds)
                    ->get()->getResultArray();
                foreach ($residuRows as $rr) {
                    $s = round((float)($rr['nilai_akhir'] ?? 0), 2);
                    $bucket = $s <= 20 ? '0–20' : ($s <= 40 ? '21–40' : ($s <= 60 ? '41–60' : ($s <= 80 ? '61–80' : '81–100')));
                    $ket = $rr['keterangan'] ?? 'Normal';
                    if (isset($residuBuckets[$bucket][$ket])) $residuBuckets[$bucket][$ket]++;
                }
            }
            $chart6 = ['labels' => array_keys($residuBuckets), 'luckyGuess' => array_column(array_values($residuBuckets), 'Lucky Guess'), 'ceroboh' => array_column(array_values($residuBuckets), 'Ceroboh'), 'normal' => array_column(array_values($residuBuckets), 'Normal')];

            // 7. SEM vs nilai
            $semGroups = ['0–20'=>['total'=>0,'count'=>0],'21–40'=>['total'=>0,'count'=>0],'41–60'=>['total'=>0,'count'=>0],'61–80'=>['total'=>0,'count'=>0],'81–100'=>['total'=>0,'count'=>0]];
            foreach ($rows as $r) {
                $s = $r['skor_akhir'];
                $bucket = $s <= 20 ? '0–20' : ($s <= 40 ? '21–40' : ($s <= 60 ? '41–60' : ($s <= 80 ? '61–80' : '81–100')));
                $sem = (float)($r['sem_akhir'] ?? 0);
                if ($sem > 0) { $semGroups[$bucket]['total'] += $sem; $semGroups[$bucket]['count']++; }
            }
            $chart7 = ['labels' => array_keys($semGroups), 'data' => array_map(fn($g) => $g['count'] > 0 ? round($g['total']/$g['count'],3) : 0, $semGroups)];
        }

        // 8. Distribusi durasi pengerjaan
        $durBuckets = ['< 10 mnt'=>0,'10–20 mnt'=>0,'20–30 mnt'=>0,'30–45 mnt'=>0,'45–60 mnt'=>0,'> 60 mnt'=>0];
        foreach ($rows as $r) {
            $d = $r['durasi_menit'] ?? 0;
            if ($d <= 0)     continue;
            if ($d < 10)     $durBuckets['< 10 mnt']++;
            elseif ($d < 20) $durBuckets['10–20 mnt']++;
            elseif ($d < 30) $durBuckets['20–30 mnt']++;
            elseif ($d < 45) $durBuckets['30–45 mnt']++;
            elseif ($d < 60) $durBuckets['45–60 mnt']++;
            else             $durBuckets['> 60 mnt']++;
        }
        $avgMenit = count($rows) > 0 ? round(array_sum(array_column($rows, 'durasi_menit')) / count($rows), 1) : 0;
        $chart8 = ['labels' => array_keys($durBuckets), 'data' => array_values($durBuckets), 'avg' => $avgMenit];

        return compact('chart1','chart2','chart3','chart4','chart5','chart6','chart7','chart8');
    }
}
