<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TambahHasilCbtIrt extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('attempt_ujian')) {
            if ($this->db->fieldExists('nilai_akhir', 'attempt_ujian')) {
                $this->forge->modifyColumn('attempt_ujian', [
                    'nilai_akhir' => [
                        'type' => 'DECIMAL',
                        'constraint' => '6,3',
                        'null' => true,
                    ],
                ]);
            }

            $fields = [];
            if (!$this->db->fieldExists('theta_akhir', 'attempt_ujian')) {
                $fields['theta_akhir'] = [
                    'type' => 'DECIMAL',
                    'constraint' => '6,4',
                    'null' => true,
                    'after' => 'nilai_akhir',
                ];
            }
            if (!$this->db->fieldExists('sem_akhir', 'attempt_ujian')) {
                $fields['sem_akhir'] = [
                    'type' => 'DECIMAL',
                    'constraint' => '6,4',
                    'null' => true,
                    'after' => 'theta_akhir',
                ];
            }
            if (!empty($fields)) {
                $this->forge->addColumn('attempt_ujian', $fields);
            }
        }

        if ($this->db->tableExists('attempt_soal')) {
            $fields = [];
            if (!$this->db->fieldExists('a', 'attempt_soal')) {
                $fields['a'] = [
                    'type' => 'DECIMAL',
                    'constraint' => '5,3',
                    'default' => 1.000,
                    'after' => 'tingkat_kesulitan',
                ];
            }
            if (!$this->db->fieldExists('c', 'attempt_soal')) {
                $fields['c'] = [
                    'type' => 'DECIMAL',
                    'constraint' => '5,3',
                    'default' => 0.000,
                    'after' => 'a',
                ];
            }
            if (!empty($fields)) {
                $this->forge->addColumn('attempt_soal', $fields);
            }
        }

        if (!$this->db->tableExists('attempt_analisis_cbt')) {
            $this->forge->addField([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'auto_increment' => true,
                ],
                'attempt_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                ],
                'soal_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                ],
                'is_correct' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                ],
                'p_residu' => [
                    'type' => 'DECIMAL',
                    'constraint' => '8,6',
                    'null' => true,
                ],
                'q_residu' => [
                    'type' => 'DECIMAL',
                    'constraint' => '8,6',
                    'null' => true,
                ],
                'z_score' => [
                    'type' => 'DECIMAL',
                    'constraint' => '8,4',
                    'null' => true,
                ],
                'kategori_soal' => [
                    'type' => 'VARCHAR',
                    'constraint' => 10,
                    'null' => true,
                ],
                'keterangan' => [
                    'type' => 'VARCHAR',
                    'constraint' => 20,
                    'null' => true,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);

            $this->forge->addPrimaryKey('id');
            $this->forge->addKey('attempt_id');
            $this->forge->addForeignKey('attempt_id', 'attempt_ujian', 'attempt_id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('attempt_analisis_cbt', true);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('attempt_analisis_cbt')) {
            $this->forge->dropTable('attempt_analisis_cbt', true);
        }

        if ($this->db->tableExists('attempt_soal')) {
            if ($this->db->fieldExists('c', 'attempt_soal')) {
                $this->forge->dropColumn('attempt_soal', 'c');
            }
            if ($this->db->fieldExists('a', 'attempt_soal')) {
                $this->forge->dropColumn('attempt_soal', 'a');
            }
        }

        if ($this->db->tableExists('attempt_ujian')) {
            if ($this->db->fieldExists('sem_akhir', 'attempt_ujian')) {
                $this->forge->dropColumn('attempt_ujian', 'sem_akhir');
            }
            if ($this->db->fieldExists('theta_akhir', 'attempt_ujian')) {
                $this->forge->dropColumn('attempt_ujian', 'theta_akhir');
            }
        }
    }
}
