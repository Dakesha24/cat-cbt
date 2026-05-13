<?php

namespace App\Models;

use CodeIgniter\Model;

class UjianModel extends Model
{
  protected $table = 'ujian';
  protected $primaryKey = 'id_ujian';
  protected $allowedFields = ['jenis_ujian_id', 'nama_ujian', 'kode_ujian', 'deskripsi', 'se_awal', 'se_minimum', 'delta_se_minimum', 'durasi', 'kelas_id', 'created_by'];
  protected $useTimestamps = true;
  protected $createdField = 'created_at';
  protected $updatedField = 'updated_at';

  /**
   * Get ujian berdasarkan kelas yang diajar guru
   */
  public function getByKelasGuru($guruId)
  {
    return $this->select('ujian.*, kelas.nama_kelas, jenis_ujian.nama_jenis')
      ->join('kelas', 'kelas.kelas_id = ujian.kelas_id', 'left')
      ->join('jenis_ujian', 'jenis_ujian.jenis_ujian_id = ujian.jenis_ujian_id', 'left')
      ->join('kelas_guru', 'kelas_guru.kelas_id = ujian.kelas_id', 'left')
      ->where('(kelas_guru.guru_id = ' . $guruId . ' OR ujian.kelas_id IS NULL)')
      ->groupBy('ujian.id_ujian')
      ->orderBy('ujian.created_at', 'DESC')
      ->findAll();
  }

  /**
   * Cek apakah guru memiliki akses ke ujian tertentu
   */
  public function hasAccess($ujianId, $guruId)
  {
    $db = \Config\Database::connect();

    $access = $db->table('ujian')
      ->select('ujian.id_ujian')
      ->join('kelas_guru', 'kelas_guru.kelas_id = ujian.kelas_id', 'left')
      ->where('ujian.id_ujian', $ujianId)
      ->where('(kelas_guru.guru_id = ' . $guruId . ' OR ujian.kelas_id IS NULL)')
      ->get()->getRowArray();

    return !empty($access);
  }

  /**
   * Get ujian dengan filter Mata Pelajaran berdasarkan kelas guru
   */
  public function getWithJenisUjianByKelasGuru($guruId)
  {
    return $this->select('ujian.*, kelas.nama_kelas, jenis_ujian.nama_jenis')
      ->join('kelas', 'kelas.kelas_id = ujian.kelas_id', 'left')
      ->join('jenis_ujian', 'jenis_ujian.jenis_ujian_id = ujian.jenis_ujian_id')
      ->join('kelas_guru', 'kelas_guru.kelas_id = ujian.kelas_id', 'left')
      ->where('(kelas_guru.guru_id = ' . $guruId . ' OR ujian.kelas_id IS NULL)')
      ->groupBy('ujian.id_ujian')
      ->orderBy('ujian.created_at', 'DESC')
      ->findAll();
  }
}
