<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TambahTabelPaketUjian extends Migration
{
    public function up()
    {
        // 1. Tabel paket_ujian
        if (!$this->db->tableExists('paket_ujian')) {
            $this->forge->addField([
                'paket_id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'auto_increment' => true,
                ],
                'ujian_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                ],
                'nama_paket' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                ],
                'nomor_paket' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                ],
                'created_at' => [
                    'type' => 'TIMESTAMP',
                    'null' => true,
                ],
            ]);
            $this->forge->addPrimaryKey('paket_id');
            $this->forge->addKey('ujian_id');
            $this->forge->addForeignKey('ujian_id', 'ujian', 'id_ujian', 'CASCADE', 'CASCADE');
            $this->forge->createTable('paket_ujian', true);
        }

        // 2. Tabel paket_ujian_item
        if (!$this->db->tableExists('paket_ujian_item')) {
            $this->forge->addField([
                'paket_item_id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'auto_increment' => true,
                ],
                'paket_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                ],
                'soal_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                ],
                'nomor_urut' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'default'    => 1,
                ],
                'created_at' => [
                    'type' => 'TIMESTAMP',
                    'null' => true,
                ],
            ]);
            $this->forge->addPrimaryKey('paket_item_id');
            $this->forge->addUniqueKey(['paket_id', 'soal_id']);
            $this->forge->addForeignKey('paket_id', 'paket_ujian', 'paket_id', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('soal_id', 'soal_ujian', 'soal_id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('paket_ujian_item', true);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('paket_ujian_item')) {
            $this->forge->dropTable('paket_ujian_item', true);
        }
        if ($this->db->tableExists('paket_ujian')) {
            $this->forge->dropTable('paket_ujian', true);
        }
    }
}
