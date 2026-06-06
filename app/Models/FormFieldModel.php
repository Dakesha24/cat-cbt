<?php

namespace App\Models;

use CodeIgniter\Model;

class FormFieldModel extends Model
{
    protected $table            = 'form_fields';
    protected $primaryKey       = 'field_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'template_id', 'label', 'tipe', 'placeholder', 'is_required', 'urutan',
    ];

    public function getByTemplate(int $templateId): array
    {
        return $this->where('template_id', $templateId)
            ->orderBy('urutan', 'ASC')
            ->findAll();
    }

    public function getWithOptions(int $templateId): array
    {
        $fields = $this->getByTemplate($templateId);
        $optionModel = new FormFieldOptionModel();

        foreach ($fields as &$field) {
            $field['options'] = $field['tipe'] === 'select'
                ? $optionModel->getByField((int) $field['field_id'])
                : [];
        }

        return $fields;
    }

    public function getNextUrutan(int $templateId): int
    {
        $result = $this->selectMax('urutan', 'max_urutan')
            ->where('template_id', $templateId)
            ->first();

        return (int) ($result['max_urutan'] ?? 0) + 1;
    }
}
