<?php
namespace App\Models;

use CodeIgniter\Model;

class AttemptJawabanCatModel extends Model
{
    protected $table = 'attempt_jawaban_cat';
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

    public function getByAttempt(int $attemptId): array
    {
        return $this->select('attempt_jawaban_cat.*')
            ->select('COALESCE(attempt_soal_cbt.pertanyaan, soal_ujian.pertanyaan) as pertanyaan')
            ->select('COALESCE(attempt_soal_cbt.jawaban_benar, soal_ujian.jawaban_benar) as jawaban_benar')
            ->select('COALESCE(attempt_soal_cbt.pembahasan, soal_ujian.pembahasan) as pembahasan')
            ->join('attempt_soal_cbt', 'attempt_soal_cbt.attempt_id = attempt_jawaban_cat.attempt_id AND attempt_soal_cbt.original_soal_id = attempt_jawaban_cat.soal_id', 'left')
            ->join('soal_ujian', 'soal_ujian.soal_id = attempt_jawaban_cat.soal_id', 'left')
            ->where('attempt_jawaban_cat.attempt_id', $attemptId)
            ->orderBy('attempt_jawaban_cat.nomor_tampil', 'ASC')
            ->findAll();
    }

    public function countCorrect(int $attemptId): int
    {
        return $this->where(['attempt_id' => $attemptId, 'is_correct' => 1])->countAllResults();
    }

    public function getAnsweredSoalIds(int $attemptId): array
    {
        $rows = $this->select('soal_id')->where('attempt_id', $attemptId)->findAll();
        return array_column($rows, 'soal_id');
    }
}
