<?php
namespace App\Models;

use CodeIgniter\Model;

class AttemptJawabanCbtModel extends Model
{
    protected $table = 'attempt_jawaban_cbt';
    protected $primaryKey = 'jawaban_id';
    protected $allowedFields = [
        'attempt_id',
        'soal_id',
        'nomor_tampil',
        'jawaban_siswa',
        'is_correct',
        'waktu_menjawab',
    ];
    protected $useTimestamps = false;

    public function getByAttempt(int $attemptId): array
    {
        return $this->select('attempt_jawaban_cbt.*')
            ->select('COALESCE(attempt_soal_cbt.pertanyaan, soal_ujian.pertanyaan) as pertanyaan')
            ->select('COALESCE(attempt_soal_cbt.jawaban_benar, soal_ujian.jawaban_benar) as jawaban_benar')
            ->select('COALESCE(attempt_soal_cbt.pembahasan, soal_ujian.pembahasan) as pembahasan')
            ->join('attempt_soal_cbt', 'attempt_soal_cbt.attempt_id = attempt_jawaban_cbt.attempt_id AND attempt_soal_cbt.original_soal_id = attempt_jawaban_cbt.soal_id', 'left')
            ->join('soal_ujian', 'soal_ujian.soal_id = attempt_jawaban_cbt.soal_id', 'left')
            ->where('attempt_jawaban_cbt.attempt_id', $attemptId)
            ->orderBy('attempt_jawaban_cbt.nomor_tampil', 'ASC')
            ->findAll();
    }

    public function countCorrect(int $attemptId): int
    {
        return $this->where(['attempt_id' => $attemptId, 'is_correct' => 1])->countAllResults();
    }

    public function saveBatchJawaban(array $jawabanData): bool
    {
        return (bool) $this->insertBatch($jawabanData);
    }
}
