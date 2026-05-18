<?php

namespace App\Models;

use CodeIgniter\Model;

class MateriModel extends Model
{
    protected $table = 'materi';
    protected $primaryKey = 'materi_id';
    protected $allowedFields = [
        'nama_materi',
        'deskripsi',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Ambil semua materi beserta jumlah soal terkait
     */
    public function getWithCount()
    {
        return $this->select('materi.*')
            ->selectCount('soal_ujian.soal_id', 'jumlah_soal')
            ->join('soal_ujian', 'soal_ujian.materi_id = materi.materi_id', 'left')
            ->groupBy('materi.materi_id')
            ->orderBy('nama_materi', 'ASC')
            ->findAll();
    }
}
