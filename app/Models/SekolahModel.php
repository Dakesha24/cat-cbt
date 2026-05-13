<?php

namespace App\Models;

use CodeIgniter\Model;

class SekolahModel extends Model
{
    protected $table = 'sekolah';
    protected $primaryKey = 'sekolah_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['nama_sekolah', 'alamat', 'telepon', 'email'];
    protected $useTimestamps = false;

    protected $validationRules = [
        'nama_sekolah' => 'required|min_length[3]',
        'alamat' => 'permit_empty',
        'telepon' => 'permit_empty|min_length[10]',
        'email' => 'permit_empty|valid_email'
    ];

    public function getSekolahWithStats()
    {
        return $this->db->table('sekolah s')
            ->select('s.*, COUNT(g.guru_id) as total_guru')
            ->join('guru g', 'g.sekolah_id = s.sekolah_id', 'left')
            ->groupBy('s.sekolah_id')
            ->get()
            ->getResultArray();
    }
}