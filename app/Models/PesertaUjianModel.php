<?php
namespace App\Models;

use CodeIgniter\Model;

class PesertaUjianModel extends Model
{
    protected $table = 'peserta_ujian';
    protected $primaryKey = 'peserta_ujian_id';
    protected $allowedFields = ['jadwal_id', 'siswa_id', 'status', 'waktu_mulai', 
                              'waktu_selesai'];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}