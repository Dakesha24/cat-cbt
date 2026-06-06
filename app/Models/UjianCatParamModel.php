<?php
namespace App\Models;

use CodeIgniter\Model;

class UjianCatParamModel extends Model
{
    protected $table = 'ujian_param_cat';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'ujian_id',
        'se_awal',
        'se_minimum',
        'delta_se_minimum',
        'maksimal_soal_tampil',
    ];
    protected $useTimestamps = false;

    public function getByUjian(int $ujianId): ?array
    {
        return $this->where('ujian_id', $ujianId)->first();
    }

    public function saveParam(int $ujianId, array $param): void
    {
        $existing = $this->getByUjian($ujianId);
        $data = [
            'ujian_id'              => $ujianId,
            'se_awal'               => $param['se_awal'] ?? 1.0,
            'se_minimum'            => $param['se_minimum'] ?? 0.25,
            'delta_se_minimum'      => $param['delta_se_minimum'] ?? 0.01,
            'maksimal_soal_tampil'  => $param['maksimal_soal_tampil'] ?? 20,
        ];

        if ($existing) {
            $this->update($existing['id'], $data);
        } else {
            $this->insert($data);
        }
    }
}
