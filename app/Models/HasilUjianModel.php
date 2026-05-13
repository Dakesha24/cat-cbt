<?php

namespace App\Models;

use CodeIgniter\Model;

class HasilUjianModel extends Model
{
    protected $table = 'hasil_ujian';
    protected $primaryKey = 'jawaban_id';
    protected $allowedFields = [
        'peserta_ujian_id',
        'soal_id',
        'jawaban_siswa',
        'is_correct',
        'theta_saat_ini',
        'se_saat_ini',
        'delta_se_saat_ini',
        'pi_saat_ini',
        'qi_saat_ini',
        'ii_saat_ini'
    ];
    
    protected $useTimestamps = false;
}