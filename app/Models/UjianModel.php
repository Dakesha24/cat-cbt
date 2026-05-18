<?php

namespace App\Models;

use CodeIgniter\Model;

class UjianModel extends Model
{
  protected $table = 'ujian';
  protected $primaryKey = 'id_ujian';
  protected $allowedFields = [
    'jenis_ujian_id', 'nama_ujian', 'kode_ujian', 'deskripsi',
    'tipe_ujian', 'tampilkan_pembahasan', 'visibilitas',
    'pengulangan_aktif', 'maksimal_attempt',
    'acak_urutan_soal', 'acak_pilihan_jawaban',
    'se_awal', 'se_minimum', 'delta_se_minimum',
    'maksimal_soal_tampil', 'durasi', 'sekolah_id', 'kelas_id', 'created_by'
  ];
  protected $useTimestamps = true;
  protected $createdField = 'created_at';
  protected $updatedField = 'updated_at';

  /**
   * Get ujian berdasarkan kelas yang diajar guru
   */
  public function getByKelasGuru($guruId)
  {
    $guruId = (int) $guruId;

    return $this->select('ujian.*, kelas.nama_kelas, jenis_ujian.nama_jenis, sekolah.nama_sekolah')
      ->join('kelas', 'kelas.kelas_id = ujian.kelas_id', 'left')
      ->join('jenis_ujian', 'jenis_ujian.jenis_ujian_id = ujian.jenis_ujian_id', 'left')
      ->join('sekolah', 'sekolah.sekolah_id = COALESCE(kelas.sekolah_id, ujian.sekolah_id)', 'left', false)
      ->join('kelas_guru', 'kelas_guru.kelas_id = ujian.kelas_id', 'left')
      ->join('guru guru_access', 'guru_access.guru_id = ' . $guruId, 'inner', false)
      ->groupStart()
        ->where('kelas_guru.guru_id', $guruId)
        ->orGroupStart()
          ->where('ujian.kelas_id IS NULL', null, false)
          ->groupStart()
            ->where('ujian.sekolah_id IS NULL', null, false)
            ->orWhere('ujian.sekolah_id = guru_access.sekolah_id', null, false)
          ->groupEnd()
        ->groupEnd()
      ->groupEnd()
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
    $guruId = (int) $guruId;

    $access = $db->table('ujian')
      ->select('ujian.id_ujian')
      ->join('kelas_guru', 'kelas_guru.kelas_id = ujian.kelas_id', 'left')
      ->join('guru guru_access', 'guru_access.guru_id = ' . $guruId, 'inner', false)
      ->where('ujian.id_ujian', $ujianId)
      ->groupStart()
        ->where('kelas_guru.guru_id', $guruId)
        ->orGroupStart()
          ->where('ujian.kelas_id IS NULL', null, false)
          ->groupStart()
            ->where('ujian.sekolah_id IS NULL', null, false)
            ->orWhere('ujian.sekolah_id = guru_access.sekolah_id', null, false)
          ->groupEnd()
        ->groupEnd()
      ->groupEnd()
      ->get()->getRowArray();

    return !empty($access);
  }

  /**
   * Get ujian dengan filter Mata Pelajaran berdasarkan kelas guru
   */
  public function getWithJenisUjianByKelasGuru($guruId)
  {
    $guruId = (int) $guruId;

    return $this->select('ujian.*, kelas.nama_kelas, jenis_ujian.nama_jenis, sekolah.nama_sekolah')
      ->join('kelas', 'kelas.kelas_id = ujian.kelas_id', 'left')
      ->join('jenis_ujian', 'jenis_ujian.jenis_ujian_id = ujian.jenis_ujian_id')
      ->join('sekolah', 'sekolah.sekolah_id = COALESCE(kelas.sekolah_id, ujian.sekolah_id)', 'left', false)
      ->join('kelas_guru', 'kelas_guru.kelas_id = ujian.kelas_id', 'left')
      ->join('guru guru_access', 'guru_access.guru_id = ' . $guruId, 'inner', false)
      ->groupStart()
        ->where('kelas_guru.guru_id', $guruId)
        ->orGroupStart()
          ->where('ujian.kelas_id IS NULL', null, false)
          ->groupStart()
            ->where('ujian.sekolah_id IS NULL', null, false)
            ->orWhere('ujian.sekolah_id = guru_access.sekolah_id', null, false)
          ->groupEnd()
        ->groupEnd()
      ->groupEnd()
      ->groupBy('ujian.id_ujian')
      ->orderBy('ujian.created_at', 'DESC')
      ->findAll();
  }
}
