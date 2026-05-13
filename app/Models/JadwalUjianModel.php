<?php

namespace App\Models;

use CodeIgniter\Model;

class JadwalUjianModel extends Model
{
  protected $table = 'jadwal_ujian';
  protected $primaryKey = 'jadwal_id';
  protected $allowedFields = ['ujian_id', 'kelas_id', 'guru_id', 'tanggal_mulai', 'tanggal_selesai', 'kode_akses', 'status'];
  protected $useTimestamps = false;

  public function getJadwalWithRelations()
  {
    return $this->select('jadwal_ujian.*, ujian.nama_ujian, kelas.nama_kelas, guru.nama_lengkap')
      ->join('ujian', 'ujian.id_ujian = jadwal_ujian.ujian_id')
      ->join('kelas', 'kelas.kelas_id = jadwal_ujian.kelas_id')
      ->join('guru', 'guru.guru_id = jadwal_ujian.guru_id')
      ->findAll();
  }

  /**
   * Get jadwal ujian berdasarkan kelas yang diajar guru
   */
  public function getJadwalByKelasGuru($guruId)
  {
    return $this->select('jadwal_ujian.*, ujian.nama_ujian, kelas.nama_kelas, guru.nama_lengkap')
      ->join('ujian', 'ujian.id_ujian = jadwal_ujian.ujian_id')
      ->join('kelas', 'kelas.kelas_id = jadwal_ujian.kelas_id')
      ->join('guru', 'guru.guru_id = jadwal_ujian.guru_id')
      ->join('kelas_guru', 'kelas_guru.kelas_id = jadwal_ujian.kelas_id')
      ->where('kelas_guru.guru_id', $guruId)
      ->groupBy('jadwal_ujian.jadwal_id')
      ->orderBy('jadwal_ujian.tanggal_mulai', 'DESC')
      ->findAll();
  }

  /**
   * Cek apakah guru memiliki akses ke jadwal ujian tertentu
   */
  public function hasAccess($jadwalId, $guruId)
  {
    $db = \Config\Database::connect();

    $access = $db->table('jadwal_ujian')
      ->select('jadwal_ujian.jadwal_id')
      ->join('kelas_guru', 'kelas_guru.kelas_id = jadwal_ujian.kelas_id')
      ->where('jadwal_ujian.jadwal_id', $jadwalId)
      ->where('kelas_guru.guru_id', $guruId)
      ->get()->getRowArray();

    return !empty($access);
  }

  public function getJadwalUjianSiswa($kelasId)
  {
    return $this->db->table('jadwal_ujian ju')
      ->select('ju.*, u.nama_ujian, u.deskripsi, u.durasi, k.nama_kelas')
      ->join('ujian u', 'u.id_ujian = ju.ujian_id')
      ->join('kelas k', 'k.kelas_id = ju.kelas_id')
      ->where('ju.kelas_id', $kelasId)
      ->where('ju.tanggal_selesai >=', date('Y-m-d H:i:s'))
      ->orderBy('ju.tanggal_mulai', 'ASC')
      ->get()
      ->getResultArray();
  }
}
