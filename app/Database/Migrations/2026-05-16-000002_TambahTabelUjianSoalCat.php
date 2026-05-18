<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TambahTabelUjianSoalCat extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('ujian_soal_cat')) {
            return;
        }

        $this->forge->addField([
            'ujian_soal_cat_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'ujian_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'soal_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('ujian_soal_cat_id', true);
        $this->forge->addKey('ujian_id');
        $this->forge->addKey('soal_id');
        $this->forge->addUniqueKey(['ujian_id', 'soal_id'], 'uniq_ujian_soal_cat');
        $this->forge->createTable('ujian_soal_cat', true);
    }

    public function down()
    {
        if ($this->db->tableExists('ujian_soal_cat')) {
            $this->forge->dropTable('ujian_soal_cat', true);
        }
    }
}
