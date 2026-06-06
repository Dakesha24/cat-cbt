<?php
namespace App\Models;

use CodeIgniter\Model;

class AttemptSoalModel extends Model
{
    protected $table = 'attempt_soal_cbt';
    protected $primaryKey = 'attempt_soal_id';
    protected $allowedFields = [
        'attempt_id',
        'paket_id',
        'original_soal_id',
        'nomor_urut',
        'kode_soal',
        'pertanyaan',
        'pilihan_a',
        'pilihan_b',
        'pilihan_c',
        'pilihan_d',
        'pilihan_e',
        'jawaban_benar',
        'tingkat_kesulitan',
        'a',
        'c',
        'pembahasan',
        'media',
        'created_at',
    ];
    protected $useTimestamps = false;

    public function getByAttempt($attemptId)
    {
        return $this->where('attempt_id', $attemptId)
            ->orderBy('nomor_urut', 'ASC')
            ->findAll();
    }
}
