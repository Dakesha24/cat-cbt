<?php

namespace App\Models;

use CodeIgniter\Model;

class JenisUjianModel extends Model
{
    protected $table = 'jenis_ujian';
    protected $primaryKey = 'jenis_ujian_id';
    protected $allowedFields = ['nama_jenis', 'deskripsi', 'kelas_id', 'created_by'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get Mata Pelajaran berdasarkan kelas yang diajar guru
     * Opsi 1: Menggunakan kelas_id
     */
    public function getByKelasGuru($guruId)
    {
        $db = \Config\Database::connect();

        return $this->select('jenis_ujian.*, kelas.nama_kelas')
            ->join('kelas', 'kelas.kelas_id = jenis_ujian.kelas_id', 'left')
            ->join('kelas_guru', 'kelas_guru.kelas_id = jenis_ujian.kelas_id')
            ->where('kelas_guru.guru_id', $guruId)
            ->orWhere('jenis_ujian.kelas_id IS NULL') // Mata Pelajaran umum
            ->findAll();
    }

    /**
     * Get Mata Pelajaran berdasarkan created_by guru
     * Opsi 3: Menggunakan created_by
     */
    public function getByCreatedBy($userId)
    {
        return $this->where('created_by', $userId)->findAll();
    }

    /**
     * Get Mata Pelajaran dengan relasi kelas (menggunakan pivot table)
     * Opsi 2: Menggunakan tabel pivot
     */
    public function getByKelasGuruPivot($guruId)
    {
        $db = \Config\Database::connect();

        return $db->table('jenis_ujian')
            ->select('jenis_ujian.*, GROUP_CONCAT(kelas.nama_kelas) as kelas_names')
            ->join('jenis_ujian_kelas', 'jenis_ujian_kelas.jenis_ujian_id = jenis_ujian.jenis_ujian_id')
            ->join('kelas', 'kelas.kelas_id = jenis_ujian_kelas.kelas_id')
            ->join('kelas_guru', 'kelas_guru.kelas_id = kelas.kelas_id')
            ->where('kelas_guru.guru_id', $guruId)
            ->groupBy('jenis_ujian.jenis_ujian_id')
            ->get()->getResultArray();
    }

    /**
     * Cek apakah guru memiliki akses ke Mata Pelajaran tertentu
     */
    public function hasAccess($jenisUjianId, $guruId)
    {
        $db = \Config\Database::connect();

        // Opsi 1: Cek berdasarkan kelas
        $access = $db->table('jenis_ujian')
            ->join('kelas_guru', 'kelas_guru.kelas_id = jenis_ujian.kelas_id')
            ->where('jenis_ujian.jenis_ujian_id', $jenisUjianId)
            ->where('kelas_guru.guru_id', $guruId)
            ->get()->getRowArray();

        return !empty($access);
    }

    /**
     * Get kelas yang bisa dipilih untuk Mata Pelajaran berdasarkan guru
     */
    public function getAvailableKelasForGuru($guruId)
    {
        $db = \Config\Database::connect();

        return $db->table('kelas')
            ->select('kelas.*')
            ->join('kelas_guru', 'kelas_guru.kelas_id = kelas.kelas_id')
            ->where('kelas_guru.guru_id', $guruId)
            ->get()->getResultArray();
    }
}
