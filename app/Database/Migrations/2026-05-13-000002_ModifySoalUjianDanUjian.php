<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifySoalUjianDanUjian extends Migration
{
    public function up()
    {
        // ===== MODIFIKASI soal_ujian =====

        // 1. Tambah parameter IRT 3PL (cek dulu apakah kolom sudah ada)
        if (!$this->db->fieldExists('a', 'soal_ujian')) {
            $this->forge->addColumn('soal_ujian', [
                'a' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '5,3',
                    'default'    => 1.000,
                    'after'      => 'tingkat_kesulitan',
                    'comment'    => 'parameter diskriminasi IRT 3PL',
                ],
            ]);
        }
        if (!$this->db->fieldExists('c', 'soal_ujian')) {
            $this->forge->addColumn('soal_ujian', [
                'c' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '5,3',
                    'default'    => 0.000,
                    'after'      => 'a',
                    'comment'    => 'parameter pseudo-guessing IRT 3PL',
                ],
            ]);
        }

        // 2. Tambah kolom metadata (variabel, indikator, materi)
        if (!$this->db->fieldExists('variabel_id', 'soal_ujian')) {
            $this->forge->addColumn('soal_ujian', [
                'variabel_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'c',
                ],
            ]);
        }
        if (!$this->db->fieldExists('indikator_id', 'soal_ujian')) {
            $this->forge->addColumn('soal_ujian', [
                'indikator_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'variabel_id',
                ],
            ]);
        }
        if (!$this->db->fieldExists('materi_id', 'soal_ujian')) {
            $this->forge->addColumn('soal_ujian', [
                'materi_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'indikator_id',
                ],
            ]);
        }

        // FK untuk soal_ujian (nullable, jadi tidak wajib) — cek dulu
        $this->addForeignKeyIfNotExists('soal_ujian', 'fk_soal_variabel', 'variabel_id', 'variabel', 'variabel_id', 'SET NULL', 'CASCADE');
        $this->addForeignKeyIfNotExists('soal_ujian', 'fk_soal_indikator', 'indikator_id', 'indikator', 'indikator_id', 'SET NULL', 'CASCADE');
        $this->addForeignKeyIfNotExists('soal_ujian', 'fk_soal_materi', 'materi_id', 'materi', 'materi_id', 'SET NULL', 'CASCADE');

        // 3. Rename foto -> media (jika kolom foto masih ada)
        $colCheck = $this->db->query("SHOW COLUMNS FROM soal_ujian LIKE 'foto'")->getRow();
        if ($colCheck) {
            $this->db->query("ALTER TABLE soal_ujian CHANGE COLUMN foto media VARCHAR(255) NULL COMMENT 'gambar/tabel/formula'");
        }

        // ===== MODIFIKASI ujian =====
        $ujianCols = ['tipe_ujian', 'tampilkan_pembahasan', 'visibilitas', 'pengulangan_aktif', 'maksimal_attempt', 'acak_urutan_soal', 'acak_pilihan_jawaban'];
        $allExist = true;
        foreach ($ujianCols as $col) {
            if (!$this->db->fieldExists($col, 'ujian')) {
                $allExist = false;
                break;
            }
        }

        if (!$allExist) {
            // Drop dulu yg mungkin sudah ada partial
            $fields = [];
            if (!$this->db->fieldExists('tipe_ujian', 'ujian')) {
                $fields['tipe_ujian'] = [
                    'type'       => 'ENUM',
                    'constraint' => ['CAT', 'CBT'],
                    'default'    => 'CAT',
                    'after'      => 'deskripsi',
                ];
            }
            if (!$this->db->fieldExists('tampilkan_pembahasan', 'ujian')) {
                $fields['tampilkan_pembahasan'] = [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 0,
                    'after'      => 'tipe_ujian',
                ];
            }
            if (!$this->db->fieldExists('visibilitas', 'ujian')) {
                $fields['visibilitas'] = [
                    'type'       => 'ENUM',
                    'constraint' => ['terbuka', 'tertutup'],
                    'default'    => 'terbuka',
                    'after'      => 'tampilkan_pembahasan',
                ];
            }
            if (!$this->db->fieldExists('pengulangan_aktif', 'ujian')) {
                $fields['pengulangan_aktif'] = [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 0,
                    'after'      => 'visibilitas',
                ];
            }
            if (!$this->db->fieldExists('maksimal_attempt', 'ujian')) {
                $fields['maksimal_attempt'] = [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 1,
                    'after'      => 'pengulangan_aktif',
                ];
            }
            if (!$this->db->fieldExists('acak_urutan_soal', 'ujian')) {
                $fields['acak_urutan_soal'] = [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 0,
                    'after'      => 'maksimal_attempt',
                ];
            }
            if (!$this->db->fieldExists('acak_pilihan_jawaban', 'ujian')) {
                $fields['acak_pilihan_jawaban'] = [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 0,
                    'after'      => 'acak_urutan_soal',
                ];
            }
            if (!empty($fields)) {
                $this->forge->addColumn('ujian', $fields);
            }
        }

        // ===== MODIFIKASI jadwal_ujian =====
        if (!$this->db->fieldExists('tipe_penugasan', 'jadwal_ujian')) {
            $this->forge->addColumn('jadwal_ujian', [
                'tipe_penugasan' => [
                    'type'       => 'ENUM',
                    'constraint' => ['kelas', 'individu'],
                    'default'    => 'kelas',
                    'after'      => 'status',
                ],
                'siswa_ids' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'after' => 'tipe_penugasan',
                    'comment' => 'JSON array of siswa_id untuk tipe individu',
                ],
            ]);
        }
    }

    public function down()
    {
        // Rollback jadwal_ujian
        if ($this->db->fieldExists('siswa_ids', 'jadwal_ujian')) {
            $this->forge->dropColumn('jadwal_ujian', 'siswa_ids');
        }
        if ($this->db->fieldExists('tipe_penugasan', 'jadwal_ujian')) {
            $this->forge->dropColumn('jadwal_ujian', 'tipe_penugasan');
        }

        // Rollback ujian
        $ujianCols = ['acak_pilihan_jawaban', 'acak_urutan_soal', 'maksimal_attempt', 'pengulangan_aktif', 'visibilitas', 'tampilkan_pembahasan', 'tipe_ujian'];
        foreach ($ujianCols as $col) {
            if ($this->db->fieldExists($col, 'ujian')) {
                $this->forge->dropColumn('ujian', $col);
            }
        }

        // Rollback soal_ujian
        if ($this->db->fieldExists('materi_id', 'soal_ujian')) {
            $this->db->query('ALTER TABLE soal_ujian DROP FOREIGN KEY IF EXISTS fk_soal_materi');
            $this->forge->dropColumn('soal_ujian', 'materi_id');
        }
        if ($this->db->fieldExists('indikator_id', 'soal_ujian')) {
            $this->db->query('ALTER TABLE soal_ujian DROP FOREIGN KEY IF EXISTS fk_soal_indikator');
            $this->forge->dropColumn('soal_ujian', 'indikator_id');
        }
        if ($this->db->fieldExists('variabel_id', 'soal_ujian')) {
            $this->db->query('ALTER TABLE soal_ujian DROP FOREIGN KEY IF EXISTS fk_soal_variabel');
            $this->forge->dropColumn('soal_ujian', 'variabel_id');
        }
        if ($this->db->fieldExists('c', 'soal_ujian')) {
            $this->forge->dropColumn('soal_ujian', 'c');
        }
        if ($this->db->fieldExists('a', 'soal_ujian')) {
            $this->forge->dropColumn('soal_ujian', 'a');
        }

        // Kembalikan media -> foto
        $colCheck = $this->db->query("SHOW COLUMNS FROM soal_ujian LIKE 'media'")->getRow();
        if ($colCheck) {
            $this->db->query("ALTER TABLE soal_ujian CHANGE COLUMN media foto VARCHAR(255) NULL");
        }
    }

    /**
     * Helper: add FK only if it doesn't exist
     */
    private function addForeignKeyIfNotExists(string $table, string $fkName, string $field, string $refTable, string $refField, string $onDelete = 'CASCADE', string $onUpdate = 'CASCADE')
    {
        // Cek apakah FK sudah ada
        $dbName = $this->db->getDatabase();
        $result = $this->db->query(
            "SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS 
             WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND CONSTRAINT_NAME = ? AND CONSTRAINT_TYPE = 'FOREIGN KEY'",
            [$dbName, $table, $fkName]
        )->getRow();

        if (!$result) {
            $this->forge->addForeignKey($field, $refTable, $refField, $onDelete, $onUpdate, $fkName);
        }
    }
}
