<?php

namespace App\Models;

use CodeIgniter\Model;

class UjianBankModel extends Model
{
    protected $table = 'ujian_bank';
    protected $primaryKey = 'ujian_bank_id';
    protected $allowedFields = [
        'ujian_id',
        'bank_ujian_id',
        'created_at',
    ];
    protected $useTimestamps = false;

    /**
     * Ambil daftar bank yang di-assign ke suatu ujian
     */
    public function getBanksByUjian($ujianId)
    {
        return $this->select('ujian_bank.*, bank_ujian.nama_ujian, bank_ujian.kategori')
            ->join('bank_ujian', 'bank_ujian.bank_ujian_id = ujian_bank.bank_ujian_id')
            ->where('ujian_bank.ujian_id', $ujianId)
            ->findAll();
    }

    /**
     * Assign beberapa bank ke ujian (hapus dulu lalu insert baru)
     */
    public function syncBanks($ujianId, array $bankIds)
    {
        $this->where('ujian_id', $ujianId)->delete();
        $bankIds = array_slice(array_values(array_unique(array_filter($bankIds))), 0, 1);
        $data = [];
        $now = date('Y-m-d H:i:s');
        foreach ($bankIds as $bankId) {
            $data[] = [
                'ujian_id' => $ujianId,
                'bank_ujian_id' => $bankId,
                'created_at' => $now,
            ];
        }
        if (!empty($data)) {
            $this->insertBatch($data);
        }
    }

    /**
     * Ambil semua soal dari bank-bank yang di-assign ke suatu ujian
     */
    public function getSoalFromBanks($ujianId)
    {
        return $this->select('soal_ujian.*')
            ->join('bank_ujian', 'bank_ujian.bank_ujian_id = ujian_bank.bank_ujian_id')
            ->join('soal_ujian', 'soal_ujian.bank_ujian_id = ujian_bank.bank_ujian_id')
            ->where('ujian_bank.ujian_id', $ujianId)
            ->where('soal_ujian.is_bank_soal', 1)
            ->findAll();
    }
}
