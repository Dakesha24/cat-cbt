<?php

namespace App\Models;

use CodeIgniter\Model;

class FormTemplateModel extends Model
{
    protected $table            = 'form_templates';
    protected $primaryKey       = 'template_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $allowedFields = [
        'nama', 'deskripsi', 'is_active', 'created_by',
    ];

    // Ambil template tunggal — buat otomatis jika belum ada
    public function getSingle(): array
    {
        $template = $this->first();

        if (!$template) {
            $id = (int) $this->insert([
                'nama'       => 'Biodata Tambahan',
                'deskripsi'  => null,
                'is_active'  => 1,
                'created_by' => 0,
            ], true);
            $template = $this->find($id);
        }

        return $template;
    }
}
