<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MakeJadwalUjianKelasNullable extends Migration
{
    public function up()
    {
        $this->dropForeignKeyIfExists('jadwal_ujian', 'kelas_id');

        $this->db->query('ALTER TABLE `jadwal_ujian` MODIFY `kelas_id` INT(11) NULL');

        $this->db->query('
            ALTER TABLE `jadwal_ujian`
            ADD CONSTRAINT `fk_jadwal_ujian_kelas`
            FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`kelas_id`)
            ON DELETE SET NULL ON UPDATE CASCADE
        ');
    }

    public function down()
    {
        $this->dropForeignKeyIfExists('jadwal_ujian', 'kelas_id');

        $fallbackKelas = $this->db->table('kelas')
            ->select('kelas_id')
            ->orderBy('kelas_id', 'ASC')
            ->get(1)
            ->getRowArray();

        if ($fallbackKelas) {
            $this->db->table('jadwal_ujian')
                ->where('kelas_id IS NULL', null, false)
                ->update(['kelas_id' => $fallbackKelas['kelas_id']]);
        }

        $this->db->query('ALTER TABLE `jadwal_ujian` MODIFY `kelas_id` INT(11) NOT NULL');

        $this->db->query('
            ALTER TABLE `jadwal_ujian`
            ADD CONSTRAINT `jadwal_ujian_ibfk_2`
            FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`kelas_id`)
            ON DELETE RESTRICT ON UPDATE RESTRICT
        ');
    }

    private function dropForeignKeyIfExists(string $table, string $column): void
    {
        $database = $this->db->database;
        $rows = $this->db->query(
            'SELECT CONSTRAINT_NAME
             FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = ?
               AND TABLE_NAME = ?
               AND COLUMN_NAME = ?
               AND REFERENCED_TABLE_NAME IS NOT NULL',
            [$database, $table, $column]
        )->getResultArray();

        foreach ($rows as $row) {
            $constraint = str_replace('`', '``', $row['CONSTRAINT_NAME']);
            $this->db->query("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$constraint}`");
        }
    }
}
