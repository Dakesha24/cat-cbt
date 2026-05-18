<?php

namespace App\Models;

use CodeIgniter\Model;

class IndikatorModel extends Model
{
    protected $table = 'indikator';
    protected $primaryKey = 'indikator_id';
    protected $allowedFields = [
        'variabel_id',
        'nama_indikator',
        'deskripsi',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Ambil indikator berdasarkan variabel, beserta jumlah soal
     */
    public function getByVariabel($variabelId)
    {
        return $this->select('indikator.*')
            ->selectCount('soal_ujian.soal_id', 'jumlah_soal')
            ->join('soal_ujian', 'soal_ujian.indikator_id = indikator.indikator_id', 'left')
            ->where('indikator.variabel_id', $variabelId)
            ->groupBy('indikator.indikator_id')
            ->orderBy('nama_indikator', 'ASC')
            ->findAll();
    }

    /**
     * Ambil semua indikator dengan nama variabel
     */
    public function getAllWithVariabel()
    {
        return $this->select('indikator.*, variabel.nama_variabel')
            ->join('variabel', 'variabel.variabel_id = indikator.variabel_id')
            ->orderBy('variabel.nama_variabel', 'ASC')
            ->orderBy('indikator.nama_indikator', 'ASC')
            ->findAll();
    }
}
