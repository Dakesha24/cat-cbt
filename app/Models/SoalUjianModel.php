<?php

namespace App\Models;

use CodeIgniter\Model;

class SoalUjianModel extends Model
{
    protected $table = 'soal_ujian';
    protected $primaryKey = 'soal_id';
    protected $allowedFields = [
        'ujian_id',
        'bank_ujian_id',
        'kode_soal',
        'is_bank_soal',
        'created_by',
        'pertanyaan',
        'pilihan_a',
        'pilihan_b',
        'pilihan_c',
        'pilihan_d',
        'pilihan_e',
        'jawaban_benar',
        'tingkat_kesulitan',
        'pembahasan',
        'foto'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}
