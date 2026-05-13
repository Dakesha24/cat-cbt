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
    protected $db;

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
    }

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

    // Method untuk assign kelas ke guru
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

    // Method untuk remove kelas dari guru
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

    public function tambahSiswa()
    {
        $rules = [
            'username' => 'required|min_length[4]|is_unique[users.username]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'nama_lengkap' => 'required|min_length[3]',
            'jenis_kelamin' => 'permit_empty|in_list[Laki-laki,Perempuan]',
            'nomor_peserta' => 'required',
            'sekolah_id' => 'required|numeric',
            'kelas_id' => 'required|numeric'
        ];

        // Sisa kode sama seperti sebelumnya...
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
            'nomor_peserta' => 'required',  // HAPUS |is_unique[siswa.nomor_peserta,siswa_id,{$siswa['siswa_id']}]
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

    // Method untuk batch create siswa
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

    // Method untuk menampilkan kelas berdasarkan sekolah
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

    public function formTambahSekolah()
    {
        return view('admin/sekolah/tambah');
    }

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

    //Method untuk form tambah kelas dalam sekolah

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

    // Form edit kelas via sekolah
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

    // Tambahkan method ini di controller Admin.php (sekitar line 800-an, setelah method formTambahKelasSekolah)

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

    // Hapus kelas via sekolah
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

    // Detail kelas via sekolah (sama seperti detailKelas tapi dengan parameter sekolah)
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

    // Remove guru dari kelas via sekolah
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

    // Transfer siswa via sekolah
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

    // Proses transfer siswa via sekolah
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

    // Method untuk tambah kelas dalam sekolah
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

    public function ujian()
    {
        // Ambil SEMUA ujian dari database
        $data['ujian'] = $this->ujianModel
            ->select('ujian.*, jenis_ujian.nama_jenis, kelas.nama_kelas, sekolah.nama_sekolah, g.nama_lengkap as guru_pembuat')
            ->join('jenis_ujian', 'jenis_ujian.jenis_ujian_id = ujian.jenis_ujian_id', 'left')
            ->join('kelas', 'kelas.kelas_id = ujian.kelas_id', 'left')
            ->join('sekolah', 'sekolah.sekolah_id = kelas.sekolah_id', 'left')
            ->join('users u', 'u.user_id = ujian.created_by', 'left')
            ->join('guru g', 'g.user_id = u.user_id', 'left')
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

        $data = [
            'jenis_ujian_id' => $this->request->getPost('jenis_ujian_id'),
            'nama_ujian' => $this->request->getPost('nama_ujian'),
            'kode_ujian' => $this->request->getPost('kode_ujian'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'se_awal' => $this->request->getPost('se_awal'),
            'se_minimum' => $this->request->getPost('se_minimum'),
            'delta_se_minimum' => $this->request->getPost('delta_se_minimum'),
            'durasi' => $this->request->getPost('durasi'),
            'kelas_id' => $this->request->getPost('kelas_id') ?: null,
            'created_by' => $userId
        ];

        try {
            $this->ujianModel->insert($data);
            return redirect()->to('admin/ujian/')->with('success', 'Ujian berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->to('admin/ujian/')->with('error', 'Gagal menambahkan ujian: ' . $e->getMessage());
        }
    }

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
            'jenis_ujian_id' => 'required|numeric',
            'nama_ujian' => 'required|min_length[3]|max_length[255]',
            'kode_ujian' => "required|alpha_numeric_punct|min_length[3]|max_length[50]|is_unique[ujian.kode_ujian,id_ujian,{$id}]",
            'deskripsi' => 'required|min_length[10]',
            'se_awal' => 'required|decimal',
            'se_minimum' => 'required|decimal',
            'delta_se_minimum' => 'required|decimal',
            'durasi' => 'required',
            'kelas_id' => 'permit_empty|numeric'
        ];

        if (!$this->validate($rules)) {
            // Mengirimkan error validasi ke session flashdata
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // 3. Admin tidak memerlukan validasi akses
        // Semua blok validasi ->hasAccess() dihilangkan

        // 4. Siapkan data baru untuk diupdate
        $data = [
            'jenis_ujian_id' => $this->request->getPost('jenis_ujian_id'),
            'nama_ujian' => $this->request->getPost('nama_ujian'),
            'kode_ujian' => $this->request->getPost('kode_ujian'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'se_awal' => $this->request->getPost('se_awal'),
            'se_minimum' => $this->request->getPost('se_minimum'),
            'delta_se_minimum' => $this->request->getPost('delta_se_minimum'),
            'durasi' => $this->request->getPost('durasi'),
            'kelas_id' => $this->request->getPost('kelas_id') ?: null
        ];

        // 5. Lakukan update dan berikan notifikasi
        try {
            $this->ujianModel->update($id, $data);
            return redirect()->to('admin/ujian/')->with('success', 'Ujian berhasil diperbarui.');
        } catch (\Exception $e) {
            log_message('error', 'Admin gagal mengupdate ujian: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui data ujian.');
        }
    }

    public function hapusUjian($id)
    {
        // 1. Admin tidak memerlukan validasi hak akses untuk menghapus ujian.
        // Blok validasi ->hasAccess() dihilangkan.

        // 2. Tetap lakukan pengecekan penting: Apakah ada soal yang terkait dengan ujian ini?
        // Ini untuk menjaga integritas data agar tidak ada soal yang "yatim".
        $soalTerkait = $this->soalUjianModel->where('ujian_id', $id)->countAllResults();

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
        $data['soal'] = $this->soalUjianModel->where('ujian_id', $ujian_id)->findAll();

        // 4. Arahkan ke view manajemen soal di dalam folder admin
        // Pastikan Anda sudah menyalin view dari `guru/kelola_soal.php` ke `admin/manajemen_ujian/kelola_soal.php`
        return view('admin/ujian/kelola_soal', $data);
    }


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

        // 2. Siapkan data dari form
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
            'created_by' => session()->get('user_id') // Creator adalah Admin yg login
        ];

        // 3. Proses upload foto jika ada
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

                return redirect()->to('admin/soal/' . $data['ujian_id'])->with('success', 'Soal berhasil ditambahkan');
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
            'foto' => 'max_size[foto,2048]|mime_in[foto,image/jpg,image/jpeg,image/png]|ext_in[foto,png,jpg,jpeg]',
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
            'pembahasan' => $this->request->getPost('pembahasan')
        ];

        // 4. Proses upload/hapus foto
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

        // Checkbox untuk menghapus foto
        if ($this->request->getPost('hapus_foto') == '1' && !empty($soal['foto'])) {
            $fotoPath = $uploadPath . '/' . $soal['foto'];
            if (file_exists($fotoPath)) {
                unlink($fotoPath);
            }
            $data['foto'] = null;
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

    public function jadwalUjian()
    {
        // Ambil SEMUA jadwal ujian dari database dengan join ke info terkait
        $data['jadwal'] = $this->db->table('jadwal_ujian')
            ->select('jadwal_ujian.*, ujian.nama_ujian, ujian.kode_ujian, kelas.nama_kelas, guru.nama_lengkap, sekolah.nama_sekolah')
            ->join('ujian', 'ujian.id_ujian = jadwal_ujian.ujian_id')
            ->join('kelas', 'kelas.kelas_id = jadwal_ujian.kelas_id')
            ->join('sekolah', 'sekolah.sekolah_id = kelas.sekolah_id')
            ->join('guru', 'guru.guru_id = jadwal_ujian.guru_id') // guru_id di sini adalah guru pengawas
            ->orderBy('jadwal_ujian.tanggal_mulai', 'DESC')
            ->get()->getResultArray();

        // Ambil SEMUA ujian untuk dropdown di modal tambah & edit
        $data['ujian_tambah'] = $this->ujianModel->select('id_ujian, nama_ujian, kode_ujian')->orderBy('nama_ujian', 'ASC')->findAll();
        $data['ujian_edit'] = $data['ujian_tambah']; // Data yang sama untuk edit

        // Ambil SEMUA kelas untuk dropdown di modal dengan info sekolah
        $data['kelas'] = $this->kelasModel
            ->select('kelas.*, sekolah.nama_sekolah')
            ->join('sekolah', 'sekolah.sekolah_id = kelas.sekolah_id')
            ->orderBy('sekolah.nama_sekolah, kelas.nama_kelas', 'ASC')
            ->findAll();

        // Ambil SEMUA guru untuk dropdown pengawas
        $data['guru'] = $this->guruModel->select('guru_id, nama_lengkap, nip, mata_pelajaran')->orderBy('nama_lengkap', 'ASC')->findAll();

        // Arahkan ke view manajemen jadwal di dalam folder admin
        return view('admin/jadwal/jadwal_ujian', $data);
    }

    public function tambahJadwal()
    {
        $rules = [
            'ujian_id' => 'required|numeric',
            'kelas_id' => 'required|numeric',
            'guru_id' => 'required|numeric',
            'tanggal_mulai' => 'required',
            'tanggal_selesai' => 'required',
            'kode_akses' => 'required|min_length[4]|max_length[50]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $existing = $this->jadwalUjianModel
            ->where('ujian_id', $this->request->getPost('ujian_id'))
            ->where('kelas_id', $this->request->getPost('kelas_id'))
            ->first();

        if ($existing) {
            return redirect()->to('admin/jadwal-ujian') // Redirect ke rute admin
                ->with('error', 'Jadwal untuk ujian dan kelas ini sudah ada.');
        }

        $data = [
            'ujian_id' => $this->request->getPost('ujian_id'),
            'kelas_id' => $this->request->getPost('kelas_id'),
            'guru_id' => $this->request->getPost('guru_id'),
            'tanggal_mulai' => $this->request->getPost('tanggal_mulai'),
            'tanggal_selesai' => $this->request->getPost('tanggal_selesai'),
            'kode_akses' => $this->request->getPost('kode_akses'),
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

    public function editJadwal($id)
    {
        $rules = [
            'ujian_id' => 'required|numeric',
            'kelas_id' => 'required|numeric',
            'guru_id' => 'required|numeric',
            'tanggal_mulai' => 'required',
            'tanggal_selesai' => 'required',
            'kode_akses' => 'required|min_length[4]|max_length[50]',
            'status' => 'required|in_list[belum_mulai,sedang_berlangsung,selesai]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $existing = $this->jadwalUjianModel
            ->where('ujian_id', $this->request->getPost('ujian_id'))
            ->where('kelas_id', $this->request->getPost('kelas_id'))
            ->where('jadwal_id !=', $id)
            ->first();

        if ($existing) {
            return redirect()->to('admin/jadwal-ujian')
                ->with('error', 'Kombinasi ujian dan kelas ini sudah digunakan oleh jadwal lain.');
        }

        $data = [
            'ujian_id' => $this->request->getPost('ujian_id'),
            'kelas_id' => $this->request->getPost('kelas_id'),
            'guru_id' => $this->request->getPost('guru_id'),
            'tanggal_mulai' => $this->request->getPost('tanggal_mulai'),
            'tanggal_selesai' => $this->request->getPost('tanggal_selesai'),
            'kode_akses' => $this->request->getPost('kode_akses'),
            'status' => $this->request->getPost('status')
        ];

        try {
            $this->jadwalUjianModel->update($id, $data);
            return redirect()->to('admin/jadwal-ujian')->with('success', 'Jadwal ujian berhasil diperbarui.');
        } catch (\Exception $e) {
            log_message('error', 'Admin gagal update jadwal: ' . $e->getMessage());
            return redirect()->to('admin/jadwal-ujian')->with('error', 'Gagal memperbarui jadwal ujian.');
        }
    }

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
        // Rumus skor akhir siswa (x) = 50 + (16.67 * tetha)
        $skor_akhir = 50 + (16.67 * (float)$theta);

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


    public function daftarHasilUjian()
    {
        $db = \Config\Database::connect();

        // Query untuk mengambil daftar ujian SEMUA STATUS dengan hasil dan informasi waktu
        $data['daftarUjian'] = $db->table('jadwal_ujian ju')
            ->select('ju.jadwal_id, ju.status as status_ujian, ju.tanggal_mulai, ju.tanggal_selesai, ju.kode_akses,
             u.nama_ujian, u.deskripsi, j.nama_jenis, k.nama_kelas, k.tahun_ajaran, 
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
            ->groupBy('ju.jadwal_id, ju.status, ju.tanggal_mulai, ju.tanggal_selesai, ju.kode_akses, u.nama_ujian, u.deskripsi, j.nama_jenis, k.nama_kelas, k.tahun_ajaran, s.nama_sekolah, g.nama_lengkap')
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

    // Helper method untuk mendapatkan class CSS berdasarkan status
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

    // Helper method untuk mendapatkan teks status
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


    public function hasilUjianSiswa($jadwalId)
    {
        $db = \Config\Database::connect();

        // Ambil info ujian
        $ujian = $db->table('jadwal_ujian ju')
            ->select('ju.*, u.nama_ujian, u.deskripsi, u.kode_ujian, j.nama_jenis, k.nama_kelas, k.tahun_ajaran, 
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
                // 1. Ambil hasil akhir (theta & se)
                $lastResult = $db->table('hasil_ujian')
                    ->select('theta_saat_ini, se_saat_ini')
                    ->where('peserta_ujian_id', $siswa['peserta_ujian_id'])
                    ->orderBy('waktu_menjawab', 'DESC')
                    ->limit(1)
                    ->get()
                    ->getRowArray();

                // 2. Hitung skor akhir berdasarkan theta
                $theta_akhir = $lastResult ? (float)$lastResult['theta_saat_ini'] : 0;
                $skor_akhir = $this->hitungKemampuanKognitif($theta_akhir);
                $siswa['theta_akhir'] = $theta_akhir;
                $siswa['skor'] = $skor_akhir;
                $siswa['nilai'] = min(100, max(0, round($skor_akhir)));
                $siswa['se_akhir'] = $lastResult ? $lastResult['se_saat_ini'] : null;

                // 3. Hitung jawaban benar & total soal
                $jawabanBenar = $db->table('hasil_ujian')->where(['peserta_ujian_id' => $siswa['peserta_ujian_id'], 'is_correct' => 1])->countAllResults();
                $totalSoal = $db->table('hasil_ujian')->where('peserta_ujian_id', $siswa['peserta_ujian_id'])->countAllResults();
                $siswa['jawaban_benar'] = $jawabanBenar;
                $siswa['total_soal'] = $totalSoal;

                // 4. Dapatkan klasifikasi kognitif
                $siswa['klasifikasi_kognitif'] = $this->getKlasifikasiKognitif($skor_akhir);

                // 5. Format durasi pengerjaan
                if (!empty($siswa['durasi_detik'])) {
                    $jam = floor($siswa['durasi_detik'] / 3600);
                    $menit = floor(($siswa['durasi_detik'] % 3600) / 60);
                    $detik = $siswa['durasi_detik'] % 60;
                    $siswa['durasi_format'] = sprintf('%02d:%02d:%02d', $jam, $menit, $detik);
                } else {
                    $siswa['durasi_format'] = '-';
                }
            } else {
                // Set nilai default untuk semua key agar tidak error di view
                $siswa['theta_akhir'] = null;
                $siswa['skor'] = null;
                $siswa['nilai'] = null;
                $siswa['se_akhir'] = null;
                $siswa['jawaban_benar'] = 0;
                $siswa['total_soal'] = 0;
                $siswa['klasifikasi_kognitif'] = $this->getKlasifikasiKognitif(0); // Default klasifikasi
                $siswa['durasi_format'] = '-';
            }
        }

        $data = [
            'ujian' => $ujian,
            'hasilSiswa' => $hasilSiswa
        ];

        return view('admin/hasil/siswa', $data);
    }
    public function detailHasilSiswa($pesertaUjianId)
    {
        $db = \Config\Database::connect();

        $hasil = $db->table('peserta_ujian pu')
            ->select('pu.*, ju.*, u.nama_ujian, u.deskripsi, j.nama_jenis, 
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

        $detailJawaban = $db->table('hasil_ujian')
            ->select('hasil_ujian.*, s.pertanyaan, s.pilihan_a, s.pilihan_b, s.pilihan_c, s.pilihan_d, 
                  s.jawaban_benar, s.tingkat_kesulitan, s.foto, s.pembahasan,
                  DATE_FORMAT(hasil_ujian.waktu_menjawab, "%H:%i:%s") as waktu_menjawab_format')
            ->join('soal_ujian s', 's.soal_id = hasil_ujian.soal_id', 'left')
            ->where('hasil_ujian.peserta_ujian_id', $pesertaUjianId)
            ->orderBy('hasil_ujian.waktu_menjawab', 'ASC')
            ->get()
            ->getResultArray();

        $detailJawabanDenganDurasi = $this->hitungDurasiPerSoal($detailJawaban, $hasil['waktu_mulai']);
        $totalSoal = count($detailJawabanDenganDurasi);
        $jawabanBenar = array_reduce($detailJawabanDenganDurasi, fn($c, $i) => $c + ($i['is_correct'] ? 1 : 0), 0);

        // Perhitungan Skor Kognitif
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
            'kemampuanKognitif' => $kemampuanKognitif,
            'klasifikasiKognitif' => $klasifikasiKognitif,
            'rataRataWaktuFormat' => $rataRataWaktuFormat, // Pastikan variabel ini dikirim ke view
        ];

        return view('admin/hasil/detail', $data);
    }


    public function downloadExcelHTML($pesertaUjianId)
    {
        $db = \Config\Database::connect();
        $hasil = $db->table('peserta_ujian pu')
            ->select('pu.*, ju.*, u.nama_ujian, u.deskripsi, j.nama_jenis, 
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

        $detailJawaban = $db->table('hasil_ujian')
            ->select('hasil_ujian.*, s.pertanyaan, s.kode_soal, s.jawaban_benar, s.tingkat_kesulitan, s.foto, s.pembahasan, DATE_FORMAT(hasil_ujian.waktu_menjawab, "%H:%i:%s") as waktu_menjawab_format')
            ->join('soal_ujian s', 's.soal_id = hasil_ujian.soal_id', 'left')
            ->where('hasil_ujian.peserta_ujian_id', $pesertaUjianId)->orderBy('hasil_ujian.waktu_menjawab', 'ASC')->get()->getResultArray();

        $detailJawabanDenganDurasi = $this->hitungDurasiPerSoal($detailJawaban, $hasil['waktu_mulai']);
        $totalSoal = count($detailJawabanDenganDurasi);
        $jawabanBenar = array_reduce($detailJawabanDenganDurasi, fn($c, $i) => $c + ($i['is_correct'] ? 1 : 0), 0);

        $lastResult = end($detailJawabanDenganDurasi);
        $theta_akhir = $lastResult ? (float)$lastResult['theta_saat_ini'] : 0;
        $skor_akhir = $this->hitungKemampuanKognitif($theta_akhir);
        $klasifikasiKognitif = $this->getKlasifikasiKognitif($skor_akhir);
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

        $data = ['hasil' => $hasil, 'detailJawaban' => $detailJawabanDenganDurasi, 'finalScore' => $skor_akhir, 'lastTheta' => $theta_akhir, 'jawabanBenar' => $jawabanBenar, 'kemampuanKognitif' => $kemampuanKognitif, 'klasifikasiKognitif' => $klasifikasiKognitif, 'rataRataWaktuFormat' => $rataRataWaktuFormat];

        $filename = 'hasil_ujian_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $hasil['nama_lengkap']) . '_' . date('dmY') . '.xls';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo view('admin/hasil/download_excel', $data); // Sesuaikan path jika perlu
        exit;
    }

    public function downloadPDFHTML($pesertaUjianId)
    {
        $db = \Config\Database::connect();

        // Ambil data hasil lengkap
        $hasil = $db->table('peserta_ujian pu')
            ->select('pu.*, ju.*, u.nama_ujian, u.deskripsi, u.kode_ujian, j.nama_jenis, 
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

        // Ambil detail jawaban
        $detailJawaban = $db->table('hasil_ujian')
            ->select('hasil_ujian.*, s.pertanyaan, s.kode_soal, s.jawaban_benar, s.tingkat_kesulitan, s.foto, s.pembahasan, DATE_FORMAT(hasil_ujian.waktu_menjawab, "%H:%i:%s") as waktu_menjawab_format')
            ->join('soal_ujian s', 's.soal_id = hasil_ujian.soal_id', 'left')
            ->where('hasil_ujian.peserta_ujian_id', $pesertaUjianId)
            ->orderBy('hasil_ujian.waktu_menjawab', 'ASC')
            ->get()->getResultArray();

        $detailJawabanDenganDurasi = $this->hitungDurasiPerSoal($detailJawaban, $hasil['waktu_mulai']);
        $totalSoal = count($detailJawabanDenganDurasi);
        $jawabanBenar = array_reduce($detailJawabanDenganDurasi, fn($c, $i) => $c + ($i['is_correct'] ? 1 : 0), 0);

        // Perhitungan Skor Baru
        $lastResult = end($detailJawabanDenganDurasi);
        $theta_akhir = $lastResult ? (float)$lastResult['theta_saat_ini'] : 0;
        $skor_akhir = $this->hitungKemampuanKognitif($theta_akhir);
        $klasifikasiKognitif = $this->getKlasifikasiKognitif($skor_akhir);
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

    public function hapusHasilSiswa($pesertaUjianId)
    {
        try {
            $db = \Config\Database::connect();
            $db->transStart();

            // Ambil info peserta untuk redirect
            $peserta = $db->table('peserta_ujian')->where('peserta_ujian_id', $pesertaUjianId)->get()->getRowArray();

            if (!$peserta) {
                session()->setFlashdata('error', 'Data peserta tidak ditemukan');
                return redirect()->back();
            }

            // Hapus hasil ujian
            $db->table('hasil_ujian')->where('peserta_ujian_id', $pesertaUjianId)->delete();

            // Reset status peserta
            $db->table('peserta_ujian')
                ->where('peserta_ujian_id', $pesertaUjianId)
                ->update([
                    'status' => 'belum_mulai',
                    'waktu_mulai' => null,
                    'waktu_selesai' => null
                ]);

            $db->transComplete();

            if ($db->transStatus() === FALSE) {
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

    public function formTambahPengumuman()
    {
        return view('admin/pengumuman/tambah');
    }

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

    // Method untuk toggle status aktif/nonaktif pengumuman (opsional)
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

    public function bankSoal()
    {
        $db = \Config\Database::connect();

        // Admin bisa akses semua kategori
        $kategoriList = $db->table('bank_ujian')
            ->select('kategori, COUNT(*) as jumlah_bank')
            ->groupBy('kategori')
            ->orderBy('kategori', 'ASC')
            ->get()
            ->getResultArray();

        // Ambil semua Mata Pelajaran untuk dropdown
        $jenisUjianList = $this->jenisUjianModel->findAll();

        $data = [
            'kategoriList' => $kategoriList,
            'jenisUjianList' => $jenisUjianList
        ];

        return view('admin/bank_soal/index', $data);
    }

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

        $data = [
            'kategori' => $kategori,
            'jenisUjianList' => $jenisUjianList
        ];

        return view('admin/bank_soal/kategori', $data);
    }

    public function bankSoalJenisUjian($kategori, $jenisUjianId)
    {
        $db = \Config\Database::connect();

        // Ambil daftar ujian dalam Mata Pelajaran dan kategori ini
        $ujianList = $db->table('bank_ujian')
            ->select('bank_ujian.*, users.username as creator_name, 
                 (SELECT COUNT(*) FROM soal_ujian WHERE soal_ujian.bank_ujian_id = bank_ujian.bank_ujian_id AND soal_ujian.is_bank_soal = 1) as jumlah_soal')
            ->join('users', 'users.user_id = bank_ujian.created_by')
            ->where('bank_ujian.kategori', $kategori)
            ->where('bank_ujian.jenis_ujian_id', $jenisUjianId)
            ->orderBy('bank_ujian.created_at', 'DESC')
            ->get()
            ->getResultArray();

        // Ambil info Mata Pelajaran
        $jenisUjian = $this->jenisUjianModel->find($jenisUjianId);

        $data = [
            'kategori' => $kategori,
            'jenisUjian' => $jenisUjian,
            'ujianList' => $ujianList
        ];

        return view('admin/bank_soal/jenis_ujian', $data);
    }

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
            'canEdit' => true  // Admin selalu bisa edit semua bank soal
        ];

        return view('admin/bank_soal/ujian', $data);
    }

    public function tambahSoalBankUjian()
    {
        $bankUjianId = $this->request->getPost('bank_ujian_id');
        $userId = session()->get('user_id');

        // Admin bisa tambah soal ke bank ujian manapun
        $db = \Config\Database::connect();
        $bankUjian = $db->table('bank_ujian')->where('bank_ujian_id', $bankUjianId)->get()->getRowArray();

        if (!$bankUjian) {
            return redirect()->back()->with('error', 'Bank ujian tidak ditemukan');
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
            'tingkat_kesulitan' => $this->request->getPost('tingkat_kesulitan'),
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
            session()->setFlashdata('success', 'Soal berhasil ditambahkan ke bank ujian!');
            return redirect()->back();
        } catch (\Exception $e) {
            log_message('error', 'Error saat menambahkan soal bank ujian: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan soal: ' . $e->getMessage());
        }
    }

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

        // Handle foto upload/delete (sama seperti di guru)
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
            session()->setFlashdata('success', 'Soal berhasil diupdate!');
            return redirect()->back();
        } catch (\Exception $e) {
            log_message('error', 'Error saat mengupdate soal bank ujian: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui soal: ' . $e->getMessage());
        }
    }

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
                session()->setFlashdata('error', "Tidak dapat menghapus bank ujian karena masih memiliki {$jumlahSoal} soal. Hapus soal terlebih dahulu.");
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
            session()->setFlashdata('error', 'Terjadi kesalahan saat menghapus bank ujian.');
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
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Gagal memuat bank ujian.']);
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
        $kelasId = $this->request->getPost('kelas_id');
        $userId = session()->get('user_id');

        // Validasi input
        $rules = [
            'nama_jenis' => 'required|min_length[3]|max_length[100]',
            'deskripsi' => 'required|min_length[10]',
            'kelas_id' => 'required|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // Validasi kelas exists
        $kelas = $this->kelasModel->find($kelasId);
        if (!$kelas) {
            session()->setFlashdata('error', 'Kelas tidak ditemukan.');
            return redirect()->back()->withInput();
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
            'kelas_id' => 'required|numeric'
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

        $kelasId = $this->request->getPost('kelas_id');

        // Validasi kelas exists
        $kelas = $this->kelasModel->find($kelasId);
        if (!$kelas) {
            session()->setFlashdata('error', 'Kelas tidak ditemukan.');
            return redirect()->back()->withInput();
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
}
