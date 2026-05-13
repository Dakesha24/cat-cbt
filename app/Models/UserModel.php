<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'user_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['username', 'email', 'password', 'role', 'status'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'username' => 'required|min_length[4]|is_unique[users.username]',
        'email'    => 'required|valid_email|is_unique[users.email]',
        'password' => 'required|min_length[6]',
        'role'     => 'required|in_list[admin,guru,siswa]',
        'status'   => 'in_list[active,inactive]'
    ];

    // Method untuk mendapatkan user aktif saja
    public function getActiveUsers($role = null)
    {
        $builder = $this->where('status', 'active');
        
        if ($role) {
            $builder->where('role', $role);
        }
        
        return $builder->findAll();
    }

    // Method untuk soft delete (mengubah status menjadi inactive)
    public function softDelete($userId)
    {
        return $this->update($userId, ['status' => 'inactive']);
    }

    // Method untuk restore user (mengubah status menjadi active)
    public function restore($userId)
    {
        return $this->update($userId, ['status' => 'active']);
    }

    // Method untuk mendapatkan data guru dengan join
    public function getGuruWithDetails()
    {
        return $this->db->table('users u')
            ->select('u.user_id, u.username, u.email, u.status, u.created_at, g.guru_id, g.nip, g.nama_lengkap, g.mata_pelajaran, s.nama_sekolah')
            ->join('guru g', 'g.user_id = u.user_id', 'left')
            ->join('sekolah s', 's.sekolah_id = g.sekolah_id', 'left')
            ->where('u.role', 'guru')
            ->orderBy('u.created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    // Method untuk mendapatkan data siswa dengan join
    public function getSiswaWithDetails()
    {
        return $this->db->table('users u')
            ->select('u.user_id, u.username, u.email, u.status, u.created_at, s.siswa_id, s.nomor_peserta, s.nama_lengkap, k.nama_kelas, k.tahun_ajaran')
            ->join('siswa s', 's.user_id = u.user_id', 'left')
            ->join('kelas k', 'k.kelas_id = s.kelas_id', 'left')
            ->where('u.role', 'siswa')
            ->orderBy('u.created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    // Method untuk mendapatkan statistik dashboard
    public function getDashboardStats()
    {
        $stats = [];
        
        // Total users by role and status
        $stats['total_admin'] = $this->where(['role' => 'admin', 'status' => 'active'])->countAllResults();
        $stats['total_guru'] = $this->where(['role' => 'guru', 'status' => 'active'])->countAllResults();
        $stats['total_siswa'] = $this->where(['role' => 'siswa', 'status' => 'active'])->countAllResults();
        
        // Inactive users
        $stats['inactive_users'] = $this->where('status', 'inactive')->countAllResults();
        
        // Recent registrations (last 30 days)
        $stats['recent_registrations'] = $this->where('created_at >', date('Y-m-d', strtotime('-30 days')))->countAllResults();
        
        return $stats;
    }
}