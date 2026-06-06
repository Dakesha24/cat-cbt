<?php
namespace App\Models;

use CodeIgniter\Model;

class PaketUjianItemModel extends Model
{
    protected $table = 'paket_ujian_item_cbt';
    protected $primaryKey = 'paket_item_id';
    protected $allowedFields = [
        'paket_id',
        'soal_id',
        'nomor_urut',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = null;

    /**
     * Ambil item berdasarkan paket, urut nomor
     */
    public function getByPaket($paketId)
    {
        return $this->select('paket_ujian_item_cbt.*, soal_ujian.pertanyaan, soal_ujian.tingkat_kesulitan')
            ->join('soal_ujian', 'soal_ujian.soal_id = paket_ujian_item_cbt.soal_id')
            ->where('paket_ujian_item_cbt.paket_id', $paketId)
            ->orderBy('nomor_urut', 'ASC')
            ->findAll();
    }
}
