<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TambahTabelAttemptUjian extends Migration
{
    public function up()
    {
        // 1. Tabel attempt_ujian
        if (!$this->db->tableExists('attempt_ujian')) {
            $this->forge->addField([
                'attempt_id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'auto_increment' => true,
                ],
                'peserta_ujian_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                ],
                'nomor_attempt' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'comment'    => '1, 2, atau 3',
                ],
                'paket_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'null'       => true,
                    'comment'    => 'NULL untuk CAT, terisi untuk CBT',
                ],
                'status' => [
                    'type'       => 'ENUM',
                    'constraint' => ['belum_mulai', 'sedang_mengerjakan', 'selesai'],
                    'default'    => 'belum_mulai',
                ],
                'waktu_mulai' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'waktu_selesai' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'nilai_akhir' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '5,3',
                    'null'       => true,
                ],
                'created_at' => [
                    'type' => 'TIMESTAMP',
                    'null' => true,
                ],
            ]);
            $this->forge->addPrimaryKey('attempt_id');
            $this->forge->addUniqueKey(['peserta_ujian_id', 'nomor_attempt']);
            $this->forge->addKey('peserta_ujian_id');
            $this->forge->addKey('paket_id');
            $this->forge->addForeignKey('peserta_ujian_id', 'peserta_ujian', 'peserta_ujian_id', 'CASCADE', 'CASCADE');
            // FK ke paket_ujian hanya jika tabelnya SUDAH ada
            if ($this->db->tableExists('paket_ujian')) {
                $this->forge->addForeignKey('paket_id', 'paket_ujian', 'paket_id', 'SET NULL', 'CASCADE');
            }
            $this->forge->createTable('attempt_ujian', true);
        }

        // 2. Tabel attempt_jawaban
        if (!$this->db->tableExists('attempt_jawaban')) {
            $this->forge->addField([
                'jawaban_id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'auto_increment' => true,
                ],
                'attempt_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                ],
                'soal_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                ],
                'nomor_tampil' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'null'       => true,
                    'comment'    => 'nomor urut yang ditampilkan ke siswa (hasil shuffle)',
                ],
                'jawaban_siswa' => [
                    'type'       => 'ENUM',
                    'constraint' => ['A', 'B', 'C', 'D', 'E'],
                ],
                'is_correct' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'null'       => true,
                ],
                'waktu_menjawab' => [
                    'type'    => 'TIMESTAMP',
                    'null'    => false,
                ],
                'theta_saat_ini' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '5,3',
                    'null'       => true,
                ],
                'se_saat_ini' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '5,3',
                    'null'       => true,
                ],
                'delta_se_saat_ini' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '5,3',
                    'null'       => true,
                ],
                'pi_saat_ini' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '5,3',
                    'null'       => true,
                ],
                'qi_saat_ini' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '5,3',
                    'null'       => true,
                ],
                'ii_saat_ini' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '5,3',
                    'null'       => true,
                ],
            ]);
            $this->forge->addPrimaryKey('jawaban_id');
            $this->forge->addKey('attempt_id');
            $this->forge->addKey('soal_id');
            $this->forge->addForeignKey('attempt_id', 'attempt_ujian', 'attempt_id', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('soal_id', 'soal_ujian', 'soal_id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('attempt_jawaban', true);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('attempt_jawaban')) {
            $this->forge->dropTable('attempt_jawaban', true);
        }
        if ($this->db->tableExists('attempt_ujian')) {
            $this->forge->dropTable('attempt_ujian', true);
        }
    }
}
