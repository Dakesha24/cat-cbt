<?php

namespace App\Models;

use CodeIgniter\Model;

class FormFieldOptionModel extends Model
{
    protected $table            = 'form_field_options';
    protected $primaryKey       = 'option_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = false;

    protected $allowedFields = ['field_id', 'label', 'urutan'];

    public function getByField(int $fieldId): array
    {
        return $this->where('field_id', $fieldId)
            ->orderBy('urutan', 'ASC')
            ->findAll();
    }

    public function getNextUrutan(int $fieldId): int
    {
        $result = $this->selectMax('urutan', 'max_urutan')
            ->where('field_id', $fieldId)
            ->first();

        return (int) ($result['max_urutan'] ?? 0) + 1;
    }
}
