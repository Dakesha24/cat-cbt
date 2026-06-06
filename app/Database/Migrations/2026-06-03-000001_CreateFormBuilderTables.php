<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFormBuilderTables extends Migration
{
    public function up(): void
    {
        // 1. form_templates
        $this->db->query('
            CREATE TABLE IF NOT EXISTS form_templates (
                template_id   INT          AUTO_INCREMENT PRIMARY KEY,
                nama          VARCHAR(150) NOT NULL,
                deskripsi     TEXT         NULL,
                is_active     TINYINT(1)  NOT NULL DEFAULT 0,
                created_by    INT          NOT NULL,
                created_at    DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at    DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ');

        // 2. form_fields
        $this->db->query('
            CREATE TABLE IF NOT EXISTS form_fields (
                field_id      INT          AUTO_INCREMENT PRIMARY KEY,
                template_id   INT          NOT NULL,
                label         VARCHAR(200) NOT NULL,
                tipe          ENUM(\'text\',\'number\',\'select\',\'date\',\'textarea\') NOT NULL DEFAULT \'text\',
                placeholder   VARCHAR(200) NULL,
                is_required   TINYINT(1)  NOT NULL DEFAULT 0,
                urutan        SMALLINT    NOT NULL DEFAULT 0,
                FOREIGN KEY (template_id) REFERENCES form_templates(template_id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ');

        // 3. form_field_options
        $this->db->query('
            CREATE TABLE IF NOT EXISTS form_field_options (
                option_id   INT          AUTO_INCREMENT PRIMARY KEY,
                field_id    INT          NOT NULL,
                label       VARCHAR(200) NOT NULL,
                urutan      SMALLINT    NOT NULL DEFAULT 0,
                FOREIGN KEY (field_id) REFERENCES form_fields(field_id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ');

        // 4. form_responses
        $this->db->query('
            CREATE TABLE IF NOT EXISTS form_responses (
                response_id   INT      AUTO_INCREMENT PRIMARY KEY,
                template_id   INT      NOT NULL,
                siswa_id      INT      NOT NULL,
                created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uq_response (template_id, siswa_id),
                FOREIGN KEY (template_id) REFERENCES form_templates(template_id),
                FOREIGN KEY (siswa_id)    REFERENCES siswa(siswa_id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ');

        // 5. form_response_values
        $this->db->query('
            CREATE TABLE IF NOT EXISTS form_response_values (
                value_id      INT  AUTO_INCREMENT PRIMARY KEY,
                response_id   INT  NOT NULL,
                field_id      INT  NOT NULL,
                nilai         TEXT NULL,
                FOREIGN KEY (response_id) REFERENCES form_responses(response_id) ON DELETE CASCADE,
                FOREIGN KEY (field_id)    REFERENCES form_fields(field_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ');
    }

    public function down(): void
    {
        $this->db->query('DROP TABLE IF EXISTS form_response_values');
        $this->db->query('DROP TABLE IF EXISTS form_responses');
        $this->db->query('DROP TABLE IF EXISTS form_field_options');
        $this->db->query('DROP TABLE IF EXISTS form_fields');
        $this->db->query('DROP TABLE IF EXISTS form_templates');
    }
}
