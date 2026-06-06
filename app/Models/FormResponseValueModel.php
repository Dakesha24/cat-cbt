<?php

namespace App\Models;

use CodeIgniter\Model;

class FormResponseValueModel extends Model
{
    protected $table            = 'form_response_values';
    protected $primaryKey       = 'value_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = false;

    protected $allowedFields = ['response_id', 'field_id', 'nilai'];

    public function getByResponse(int $responseId): array
    {
        $rows = $this->where('response_id', $responseId)->findAll();

        // Kembalikan sebagai map [field_id => nilai]
        $map = [];
        foreach ($rows as $row) {
            $map[(int) $row['field_id']] = $row['nilai'];
        }

        return $map;
    }

    public function upsert(int $responseId, int $fieldId, ?string $nilai): void
    {
        $existing = $this->where('response_id', $responseId)
            ->where('field_id', $fieldId)
            ->first();

        if ($existing) {
            $this->update($existing['value_id'], ['nilai' => $nilai]);
        } else {
            $this->insert([
                'response_id' => $responseId,
                'field_id'    => $fieldId,
                'nilai'       => $nilai,
            ]);
        }
    }

    public function saveAll(int $responseId, array $values): void
    {
        foreach ($values as $fieldId => $nilai) {
            $this->upsert($responseId, (int) $fieldId, $nilai !== '' ? $nilai : null);
        }
    }
}
