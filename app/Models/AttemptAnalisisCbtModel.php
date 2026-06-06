<?php

namespace App\Models;

use CodeIgniter\Model;

class AttemptAnalisisCbtModel extends Model
{
    protected $table = 'attempt_analisis_cbt';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'attempt_id',
        'soal_id',
        'is_correct',
        'p_residu',
        'q_residu',
        'z_score',
        'kategori_soal',
        'keterangan',
        'created_at',
    ];
    protected $useTimestamps = false;
}
