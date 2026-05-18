<?php

namespace App\Models;

use CodeIgniter\Model;

class AttemptJawabanModel extends Model
{
    protected $table = 'attempt_jawaban';
    protected $primaryKey = 'jawaban_id';
    protected $allowedFields = [
        'attempt_id',
        'soal_id',
        'nomor_tampil',
        'jawaban_siswa',
        'is_correct',
        'waktu_menjawab',
        'theta_saat_ini',
        'se_saat_ini',
        'delta_se_saat_ini',
        'pi_saat_ini',
        'qi_saat_ini',
        'ii_saat_ini',
    ];
    protected $useTimestamps = false;

    /**
     * Ambil semua jawaban dalam satu attempt
     */
    public function getByAttempt($attemptId)
    {
        return $this->select('attempt_jawaban.*')
            ->select('COALESCE(attempt_soal.pertanyaan, soal_ujian.pertanyaan) as pertanyaan')
            ->select('COALESCE(attempt_soal.jawaban_benar, soal_ujian.jawaban_benar) as jawaban_benar')
            ->select('COALESCE(attempt_soal.pembahasan, soal_ujian.pembahasan) as pembahasan')
            ->join('attempt_soal', 'attempt_soal.attempt_id = attempt_jawaban.attempt_id AND attempt_soal.original_soal_id = attempt_jawaban.soal_id', 'left')
            ->join('soal_ujian', 'soal_ujian.soal_id = attempt_jawaban.soal_id', 'left')
            ->where('attempt_jawaban.attempt_id', $attemptId)
            ->orderBy('attempt_jawaban.waktu_menjawab', 'ASC')
            ->findAll();
    }

    /**
     * Hitung jumlah benar dalam satu attempt
     */
    public function countCorrect($attemptId)
    {
        return $this->where([
            'attempt_id' => $attemptId,
            'is_correct' => 1,
        ])->countAllResults();
    }

    /**
     * Ambil soal yang sudah dijawab dalam attempt (untuk CAT: jangan tampilkan lagi)
     */
    public function getAnsweredSoalIds($attemptId)
    {
        $rows = $this->select('soal_id')
            ->where('attempt_id', $attemptId)
            ->findAll();

        return array_column($rows, 'soal_id');
    }

    /**
     * Simpan batch jawaban (untuk CBT submit sekaligus)
     */
    public function saveBatchJawaban(array $jawabanData)
    {
        return $this->insertBatch($jawabanData);
    }
}
