<?php

namespace App\Models;

use CodeIgniter\Model;

class VariabelModel extends Model
{
    protected $table = 'variabel';
    protected $primaryKey = 'variabel_id';
    protected $allowedFields = [
        'nama_variabel',
        'deskripsi',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Ambil semua variabel beserta jumlah indikator dan soal terkait
     */
    public function getWithCounts()
    {
        return $this->select('variabel.*, COUNT(DISTINCT indikator.indikator_id) as jumlah_indikator, COUNT(DISTINCT soal_ujian.soal_id) as jumlah_soal')
            ->join('indikator', 'indikator.variabel_id = variabel.variabel_id', 'left')
            ->join('soal_ujian', 'soal_ujian.variabel_id = variabel.variabel_id', 'left')
            ->groupBy('variabel.variabel_id')
            ->orderBy('nama_variabel', 'ASC')
            ->findAll();
    }
}
