<?php

namespace App\Models;

use CodeIgniter\Model;

class AttemptUjianModel extends Model
{
    protected $table = 'attempt_ujian';
    protected $primaryKey = 'attempt_id';
    protected $allowedFields = [
        'peserta_ujian_id',
        'nomor_attempt',
        'paket_id',
        'status',
        'waktu_mulai',
        'waktu_selesai',
        'nilai_akhir',
        'theta_akhir',
        'sem_akhir',
        'created_at',
    ];
    protected $useTimestamps = false;
    protected $createdField = 'created_at';
    protected $updatedField = null;

    /**
     * Ambil semua attempt seorang peserta
     */
    public function getByPeserta($pesertaUjianId)
    {
        return $this->where('peserta_ujian_id', $pesertaUjianId)
            ->orderBy('nomor_attempt', 'ASC')
            ->findAll();
    }

    /**
     * Cek apakah attempt tertentu sudah ada
     */
    public function attemptExists($pesertaUjianId, $nomorAttempt)
    {
        return $this->where([
            'peserta_ujian_id' => $pesertaUjianId,
            'nomor_attempt'    => $nomorAttempt,
        ])->first();
    }

    /**
     * Ambil attempt yang sedang berlangsung
     */
    public function getActiveAttempt($pesertaUjianId)
    {
        return $this->where([
            'peserta_ujian_id' => $pesertaUjianId,
            'status'           => 'sedang_mengerjakan',
        ])->first();
    }
}
