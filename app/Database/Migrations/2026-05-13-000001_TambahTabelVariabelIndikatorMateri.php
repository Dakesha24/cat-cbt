<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TambahTabelVariabelIndikatorMateri extends Migration
{
    public function up()
    {
        // 1. Tabel variabel
        $this->forge->addField([
            'variabel_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_variabel' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);
        $this->forge->addPrimaryKey('variabel_id');
        $this->forge->createTable('variabel', true);

        // 2. Tabel indikator (dependen ke variabel)
        $this->forge->addField([
            'indikator_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'variabel_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'nama_indikator' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
            ],
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);
        $this->forge->addPrimaryKey('indikator_id');
        $this->forge->addForeignKey('variabel_id', 'variabel', 'variabel_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('indikator', true);

        // 3. Tabel materi
        $this->forge->addField([
            'materi_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_materi' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
            ],
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);
        $this->forge->addPrimaryKey('materi_id');
        $this->forge->createTable('materi', true);
    }

    public function down()
    {
        // Hapus dalam urutan terbalik (dependen dulu)
        $this->forge->dropTable('materi', true);
        $this->forge->dropTable('indikator', true);
        $this->forge->dropTable('variabel', true);
    }
}
