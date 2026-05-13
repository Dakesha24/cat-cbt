<?php
namespace App\Models;

use CodeIgniter\Model;

class KelasModel extends Model
{
    protected $table = 'kelas';
    protected $primaryKey = 'kelas_id';
    protected $allowedFields = ['sekolah_id', 'nama_kelas', 'tahun_ajaran'];

    public function getKelas()
    {
        return $this->findAll();
    }
}