<?php

namespace App\Models;

use CodeIgniter\Model;

class PengumumanModel extends Model
{
    protected $table = 'pengumuman';
    protected $primaryKey = 'pengumuman_id';
    protected $allowedFields = ['judul', 'isi_pengumuman', 'tanggal_publish', 'tanggal_berakhir', 'created_by'];
    protected $useTimestamps = false;

    public function getPengumumanWithUser()
    {
        return $this->select('pengumuman.*, users.username')
                    ->join('users', 'users.user_id = pengumuman.created_by')
                    ->orderBy('tanggal_publish', 'DESC')
                    ->findAll();
    }
}