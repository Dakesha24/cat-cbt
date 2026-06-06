<?php

namespace App\Models;

use CodeIgniter\Model;

class FormResponseModel extends Model
{
    protected $table            = 'form_responses';
    protected $primaryKey       = 'response_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $allowedFields = ['template_id', 'siswa_id'];

    public function getBySiswaAndTemplate(int $siswaId, int $templateId): ?array
    {
        return $this->where('siswa_id', $siswaId)
            ->where('template_id', $templateId)
            ->first();
    }

    public function getOrCreate(int $siswaId, int $templateId): int
    {
        $existing = $this->getBySiswaAndTemplate($siswaId, $templateId);

        if ($existing) {
            return (int) $existing['response_id'];
        }

        return (int) $this->insert([
            'siswa_id'    => $siswaId,
            'template_id' => $templateId,
        ], true);
    }
}
