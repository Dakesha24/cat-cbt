<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TambahTabelAttemptSoal extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('attempt_soal')) {
            return;
        }

        $this->forge->addField([
            'attempt_soal_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
            ],
            'attempt_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'paket_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'original_soal_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'nomor_urut' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'kode_soal' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'pertanyaan' => [
                'type' => 'LONGTEXT',
            ],
            'pilihan_a' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'pilihan_b' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'pilihan_c' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'pilihan_d' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'pilihan_e' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'jawaban_benar' => [
                'type'       => 'ENUM',
                'constraint' => ['A', 'B', 'C', 'D', 'E'],
            ],
            'tingkat_kesulitan' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,3',
                'null'       => true,
            ],
            'pembahasan' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'media' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('attempt_soal_id');
        $this->forge->addUniqueKey(['attempt_id', 'nomor_urut']);
        $this->forge->addKey('attempt_id');
        $this->forge->addKey('paket_id');
        $this->forge->addKey('original_soal_id');
        $this->forge->addForeignKey('attempt_id', 'attempt_ujian', 'attempt_id', 'CASCADE', 'CASCADE');
        if ($this->db->tableExists('paket_ujian')) {
            $this->forge->addForeignKey('paket_id', 'paket_ujian', 'paket_id', 'SET NULL', 'CASCADE');
        }
        $this->forge->createTable('attempt_soal', true);
    }

    public function down()
    {
        if ($this->db->tableExists('attempt_soal')) {
            $this->forge->dropTable('attempt_soal', true);
        }
    }
}
