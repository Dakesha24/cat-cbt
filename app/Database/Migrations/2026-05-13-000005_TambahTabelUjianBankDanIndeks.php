<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TambahTabelUjianBankDanIndeks extends Migration
{
    public function up()
    {
        // 1. Tabel pivot ujian_bank
        if (!$this->db->tableExists('ujian_bank')) {
            $this->forge->addField([
                'ujian_bank_id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'auto_increment' => true,
                ],
                'ujian_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                ],
                'bank_ujian_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                ],
                'created_at' => [
                    'type' => 'TIMESTAMP',
                    'null' => true,
                ],
            ]);
            $this->forge->addPrimaryKey('ujian_bank_id');
            $this->forge->addUniqueKey('ujian_id', 'uk_ujian_satu_bank');
            $this->forge->addKey('bank_ujian_id');
            $this->forge->addForeignKey('ujian_id', 'ujian', 'id_ujian', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('bank_ujian_id', 'bank_ujian', 'bank_ujian_id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('ujian_bank', true);
        }

        // 2. Index tambahan — cek dulu sebelum buat
        try {
            $this->db->query('CREATE INDEX idx_hasil_peserta_soal ON hasil_ujian (peserta_ujian_id, soal_id)');
        } catch (\Exception $e) {
            // index already exists, ignore
        }
        try {
            $this->db->query('CREATE INDEX idx_soal_ujian_b ON soal_ujian (ujian_id, tingkat_kesulitan)');
        } catch (\Exception $e) {
            // index already exists, ignore
        }
        try {
            $this->db->query('CREATE INDEX idx_jadwal_tanggal ON jadwal_ujian (tanggal_mulai, tanggal_selesai)');
        } catch (\Exception $e) {
            // index already exists, ignore
        }
        if ($this->db->tableExists('peserta_ujian') && !$this->db->fieldExists('id_paket', 'peserta_ujian')) {
            $this->forge->addColumn('peserta_ujian', [
                'id_paket' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'null'       => true,
                    'after'      => 'status',
                ],
            ]);
        }
        foreach ([
            'idx_peserta_id_paket' => 'CREATE INDEX idx_peserta_id_paket ON peserta_ujian (id_paket)',
            'idx_paket_item_soal' => 'CREATE INDEX idx_paket_item_soal ON paket_ujian_item (soal_id)',
            'idx_soal_metadata' => 'CREATE INDEX idx_soal_metadata ON soal_ujian (variabel_id, indikator_id, materi_id)',
            'idx_soal_bank_meta' => 'CREATE INDEX idx_soal_bank_meta ON soal_ujian (bank_ujian_id, is_bank_soal, variabel_id, indikator_id, materi_id)',
        ] as $sql) {
            try { $this->db->query($sql); } catch (\Exception $e) {}
        }
    }

    public function down()
    {
        try { $this->db->query('DROP INDEX idx_soal_bank_meta ON soal_ujian'); } catch (\Exception $e) {}
        try { $this->db->query('DROP INDEX idx_soal_metadata ON soal_ujian'); } catch (\Exception $e) {}
        try { $this->db->query('DROP INDEX idx_paket_item_soal ON paket_ujian_item'); } catch (\Exception $e) {}
        try { $this->db->query('DROP INDEX idx_peserta_id_paket ON peserta_ujian'); } catch (\Exception $e) {}
        if ($this->db->tableExists('peserta_ujian') && $this->db->fieldExists('id_paket', 'peserta_ujian')) {
            $this->forge->dropColumn('peserta_ujian', 'id_paket');
        }
        try { $this->db->query('DROP INDEX idx_jadwal_tanggal ON jadwal_ujian'); } catch (\Exception $e) {}
        try { $this->db->query('DROP INDEX idx_soal_ujian_b ON soal_ujian'); } catch (\Exception $e) {}
        try { $this->db->query('DROP INDEX idx_hasil_peserta_soal ON hasil_ujian'); } catch (\Exception $e) {}
        if ($this->db->tableExists('ujian_bank')) {
            $this->forge->dropTable('ujian_bank', true);
        }
    }
}
