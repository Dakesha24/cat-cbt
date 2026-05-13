<?php

namespace App\Models;

use CodeIgniter\Model;

class GuruModel extends Model
{
    protected $table = 'guru';
    protected $primaryKey = 'guru_id';
    protected $allowedFields = ['user_id', 'sekolah_id', 'nip', 'mata_pelajaran', 'nama_lengkap'];

    public function id_saya()
    {
        $guru = $this->where('user_id', session()->get('user_id'))->first();
        return $guru['guru_id'];
    }
}
