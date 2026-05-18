<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSekolahIdToUjian extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('sekolah_id', 'ujian')) {
            $this->forge->addColumn('ujian', [
                'sekolah_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'durasi',
                ],
            ]);
        }

        $this->db->query(
            'UPDATE ujian
             LEFT JOIN kelas ON kelas.kelas_id = ujian.kelas_id
             SET ujian.sekolah_id = kelas.sekolah_id
             WHERE ujian.sekolah_id IS NULL AND kelas.sekolah_id IS NOT NULL'
        );
    }

    public function down()
    {
        if ($this->db->fieldExists('sekolah_id', 'ujian')) {
            $this->forge->dropColumn('ujian', 'sekolah_id');
        }
    }
}
